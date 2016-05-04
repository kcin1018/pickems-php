<?php

namespace Pickems\Console\Commands;

use Illuminate\Console\Command;

class DumpPlayers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pickems:dump-players';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Dumps the player data from nflgame';

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
        $output = [];
        $data = json_decode(file_get_contents('https://raw.githubusercontent.com/BurntSushi/nflgame/master/nflgame/players.json'));

        foreach ($data as $gsisId => $playerData) {
            $output[$playerData->profile_id] = [
                'gsis_id' => $playerData->gsis_id,
                'profile_id' => $playerData->profile_id,
                'name' => $playerData->full_name,
            ];
        }

        file_put_contents(storage_path().'/app/players.json', json_encode($output));
    }
}
