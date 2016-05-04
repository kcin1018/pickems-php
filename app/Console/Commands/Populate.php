<?php

namespace Pickems\Console\Commands;

use Pickems\Team;
use Pickems\User;
use Pickems\NflGame;
use Pickems\NflStat;
use Pickems\NflTeam;
use Pickems\TeamPick;
use Pickems\NflPlayer;
use Pickems\TeamPlayoffPick;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

class Populate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pickems:populate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Populate the database with test data';

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->clearDatabase();

        $numberOfUsers = 25;
        $numberOfTeams = 3;

        $this->info('Creating users and teams');
        $bar = $this->output->createProgressBar(count($numberOfUsers * $numberOfTeams));
        factory(User::class, $numberOfUsers)
            ->create()
            ->each(function ($user) use ($numberOfTeams, $bar) {
                factory(Team::class, $numberOfTeams)->create(['user_id' => $user->id]);
                $bar->advance();
            });
        $bar->finish();
        $this->info('');

        $this->info('Creating random picks for all teams');
        $bar = $this->output->createProgressBar(count($numberOfUsers * $numberOfTeams * 17 * 2));
        $currentWeek = NflGame::fetchCurrentWeek();
        $counts = Config::get('pickems.counts');
        foreach (Team::all() as $userTeam) {
            $currentCounts = $counts;
            foreach (range(1, 17) as $week) {
                foreach (range(1, 2) as $pickNumber) {
                    /* select which type of pick your picking*/
                    if ($currentCounts['qb'] == 0 && $currentCounts['wrte'] == 0 && $currentCounts['rb'] == 0 && $currentCounts['k'] == 0) {
                        $type = 'team';
                    } elseif ($currentCounts['afc'] == 0 && $currentCounts['nfc'] == 0) {
                        $type = 'player';
                    } else {
                        $type = (rand(1, 100) > 80) ? 'team' : 'player';
                    }

                    /* if conferences are available random 80% between players/teams pick */
                    $playmaker = 0;
                    if ($type == 'player' && $currentCounts['playmakers'] > 0) {
                        /* if playmakers are available and is player pick random 50 on playmaker */
                        $playmaker = (rand(1, 100) > 50) ? true : false;
                    }

                    /* make pick #1 and #2 */
                    $this->makePick($currentCounts, $userTeam->id, $week, $pickNumber, $type, $playmaker);
                    $bar->advance();
                }
            }

            $userTeam->validatePicks(17, false);
            if ($userTeam->paid && $currentWeek > 17) {
                $this->makePlayoffPicks($userTeam->id);
            }
        }
        $bar->finish();

        $this->info('');
    }

    private function clearDatabase()
    {
        DB::statement('TRUNCATE TABLE team_picks CASCADE');
        DB::statement('ALTER SEQUENCE team_picks_id_seq RESTART WITH 1');
        DB::statement('TRUNCATE TABLE teams CASCADE');
        DB::statement('ALTER SEQUENCE teams_id_seq RESTART WITH 1');
        DB::statement('DELETE FROM users WHERE id > 1');
        DB::statement('ALTER SEQUENCE teams_id_seq RESTART WITH 2');
    }

    private function makePick(&$currentCounts, $teamId, $week, $pickNumber, $type, $playmaker)
    {
        if ($type == 'player') {
            $position = null;
            $positions = ['qb', 'rb', 'wrte', 'k'];
            while ($position === null || $currentCounts[$position] == 0) {
                $position = $positions[array_rand($positions)];
            }

            $base = NflPlayer::join('nfl_teams', 'nfl_teams.id', '=', 'nfl_players.team_id')
                ->whereNotIn('nfl_teams.abbr', $currentCounts['teams'])
                ->orderByRaw('random()')
                ->select('nfl_players.*');

            /* fetch the nfl player based on position */
            switch ($position) {
                case 'qb':
                case 'k':
                    /* fetch by strtoupper */
                    $nflPick = $base->where('position', '=', strtoupper($position))->first();
                    break;
                case 'rb':
                    /* fetch by strtoupper rb and fb */
                    $nflPick = $base->whereIn('position', ['RB', 'FB'])->first();
                    break;
                case 'wrte':
                    /* fetch by strtoupper wr and te */
                    $nflPick = $base->whereIn('position', ['WR', 'TE'])->first();
                    break;
            }

            if ($nflPick === null) {
                $log = DB::getQueryLog();
                $log = end($log);
                $this->error($log);
            }

            --$currentCounts[$position];
            if ($playmaker === 1) {
                --$currentCounts['playmakers'];
            }

            $currentCounts['teams'][] = $nflPick->team->abbr;
        } else {
            $conference = null;
            while ($conference === null || $currentCounts[$conference] == 0) {
                $conference = (rand(1, 100) > 50) ? 'afc' : 'nfc';
            }

            $nflPick = NflTeam::where('conference', '=', strtoupper($conference))->orderByRaw('random()')->first();
            --$currentCounts[$conference];
        }

        if ($nflPick) {
            $nflStat = NflStat::fetchOrCreateStat($week, $type, $nflPick->id);
            if ($nflStat) {
                $teamPick = new TeamPick();
                $teamPick->team_id = $teamId;
                $teamPick->week = $week;
                $teamPick->number = $pickNumber;
                $teamPick->stat_id = $nflStat->id;
                $teamPick->playmaker = $playmaker;
                $teamPick->save();
            } else {
                $this->error($nflPick);
                throw new \Exception('Could not make the pick.');
            }
        } else {
            $this->error($nflStat);
            throw new \Exception('Could not create/find the stat.');
        }

        return $teamPick;
    }

    private function makePlayoffPicks($teamId)
    {
        try {
            $picks = new TeamPlayoffPick();
            $picks->team_id = $teamId;
            $pickData = [];
            $teamCounts = [];
            $playoffTeams = NflGame::fetchPlayoffTeams();
            $playoffPicks = Config::get('pickems.playoffsSelections');
            foreach (array_keys($playoffPicks) as $key) {
                $pKey = preg_replace('/[0-9]/', '', $key);

                $base = NflPlayer::join('nfl_teams', 'nfl_teams.id', '=', 'nfl_players.team_id')
                    ->whereNotIn('nfl_teams.abbr', $teamCounts)
                    ->whereIn('nfl_teams.id', $playoffTeams)
                    ->orderByRaw('random()')
                    ->select('nfl_players.*');

                switch ($pKey) {
                    case 'wrte':
                        $position = (rand(1, 100) > 30) ? 'WR' : 'TE';
                        break;
                    case 'rb':
                        $position = (rand(1, 100) > 10) ? 'RB' : 'FB';
                        break;
                    default:
                        $position = strtoupper($pKey);
                        break;
                }

                $nflPlayer = $base->where('position', '=', $position)->first();
                if ($nflPlayer === null) {
                    $log = DB::getQueryLog();
                    $log = end($log);
                    $this->error($log['query']);
                    exit();
                }

                $pickData[$key] = $nflPlayer->id;
                $teamCounts[] = $nflPlayer->team->abbr;
            }

            $picks->picks = json_encode($pickData);
            $picks->save();
        } catch (\Exception $e) {
            dd($e->getMessage(), $e->getTraceAsString());
        }
    }
}
