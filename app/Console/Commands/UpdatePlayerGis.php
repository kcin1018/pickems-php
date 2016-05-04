<?php

namespace Pickems\Console\Commands;

use Pickems\NflPlayer;
use Illuminate\Console\Command;

class UpdatePlayerGis extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pickems:update-players';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update the players with no GIS ID.';

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
        if (!file_exists(storage_path().'/app/players.json')) {
            $this->call('pickems:dump-players');
        }

        $cachedPlayers = json_decode(file_get_contents(storage_path().'/app/players.json'), true);
        $cachedPlayerKeys = array_keys($cachedPlayers);

        foreach (NflPlayer::all() as $player) {
            if ($player->gsis_id === null) {
                if (isset($cachedPlayers[$player->profile_id])) {
                    $this->info('Updating: '.$player->displayName());
                    $player->gsis_id = $cachedPlayers[$player->profile_id]['gsis_id'];
                    $player->save();
                } else {
                    $this->error('Not Found: '.$player->displayName());
                }
            }
        }
    }
}
