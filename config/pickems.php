<?php

return [
    'cost' => env('PICKEMS_COST', 10),
    'points' => [
        'td' => 6,
        'fg' => 3,
        'two' => 2,
        'xp' => 1,
    ],
    'counts' => [
        'qb' => 8,
        'rb' => 8,
        'wrte' => 8,
        'k' => 8,
        'afc' => 1,
        'nfc' => 1,
        'playmakers' => 2,
        'teams' => [],
    ],
    'playoffsSelections' => [
        'qb1' => 'QB #1',
        'qb2' => 'QB #2',
        'rb1' => 'RB #1',
        'rb2' => 'RB #2',
        'rb3' => 'RB #3',
        'wrte1' => 'WR/TE #1',
        'wrte2' => 'WR/TE #2',
        'wrte3' => 'WR/TE #3',
        'wrte4' => 'WR/TE #4',
        'wrte5' => 'WR/TE #5',
        'k1' => 'K #1',
        'k2' => 'K #2',
    ],
    'playoffsCounts' => [
        'qb' => 2,
        'rb' => 3,
        'wrte' => 5,
        'k' => 2,
        'teams' => [],
    ],
];
