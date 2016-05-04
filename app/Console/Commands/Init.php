<?php

namespace Pickems\Console\Commands;

use Pickems\User;
use Pickems\NflGame;
use Pickems\NflTeam;
use Pickems\NflPlayer;
use Pickems\NflScrape;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class Init extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pickems:init
        {year : The year of the league}
        {--test= : Populate the database with test data}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Initializes the database for the given year';

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
        \Eloquent::unguard();

        // clear the db tables
        $this->call('migrate:reset');

        // re-create the tables
        $this->call('migrate');

        // download the player data
        $this->call('pickems:dump-players');

        // get year
        $year = ($this->argument('year')) ? $this->argument('year') : date('Y');
        $this->info('Initializing for year '.$year);

        // get input for email and password
        $email = $this->ask('Admin email address');
        $password = $this->secret('Admin password');

        // create the user and add them to the admin group
        User::create([
            'email' => trim(strtolower($email)),
            'name' => 'System Admin',
            'password' => Hash::make($password),
            'admin' => true,
        ]);
        $this->info('Created the admin user.');

        $nflScrape = new NflScrape($year);

        // get all the NFL teams
        $this->info('Fetching teams...');
        $start = microtime(true);
        $teams = $nflScrape->getTeams();
        $this->info('Creating teams...');
        foreach ($teams as $team) {
            NflTeam::create($team);
        }
        $time = microtime(true) - $start;
        $this->info('Created '.count($teams).' NFL teams. ('.number_format($time, 3).'s)');

        // get all the NFL games
        $this->info('Fetching games...');
        $start = microtime(true);
        $games = $nflScrape->getGames();
        $this->info('Creating games...');
        foreach ($games as $game) {
            NflGame::create($game);
        }
        $time = microtime(true) - $start;
        $this->info('Created '.count($games).' NFL games. ('.number_format($time, 3).'s)');

        // get all the NFL players
        $this->info('Fetching players...');
        $start = microtime(true);
        $players = $nflScrape->getPlayers();
        $this->info('Creating players...');
        foreach ($players as $player) {
            NflPlayer::create($player);
        }
        $time = microtime(true) - $start;
        $this->info('Created '.count($players).' NFL players. ('.number_format($time, 3).'s)');

        if ($this->option('test')) {
            echo "Populating with test data\n";
            $this->call('pickems:populate');
        }

        $this->info('pickems:init: COMPLETED');
    }
}
