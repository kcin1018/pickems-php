<?php

namespace Pickems;

use Exception;
use Symfony\Component\DomCrawler\Crawler;

class NflScrape
{
    protected $_teams;
    private $_year;

    protected $baseUrl = 'http://www.nfl.com';
    protected $rosterUrl = 'http://www.nfl.com/teams/roster?team=';
    protected $gsisProfileUrl = 'http://www.nfl.com/players/profile?id=';
    protected $gameDataUrl = 'http://www.nfl.com/liveupdate/game-center/%s/%s_gtd.json';

    public function __construct($year = null)
    {
        // initialize the teams lookup table
        $this->_teams = [];
        $this->_year = (empty($year)) ? date('Y') : $year;
    }

    public function curlFetchUrl($url)
    {
        $user_agent = 'Mozilla/5.0 (Windows NT 6.1; rv:8.0) Gecko/20100101 Firefox/8.0';

        $options = [
            CURLOPT_CUSTOMREQUEST => 'GET',        //set request type post or get
            CURLOPT_POST => false,        //set to GET
            CURLOPT_USERAGENT => $user_agent, //set user agent
            CURLOPT_COOKIEFILE => 'cookie.txt', //set cookie file
            CURLOPT_COOKIEJAR => 'cookie.txt', //set cookie jar
            CURLOPT_RETURNTRANSFER => true,     // return web page
            CURLOPT_HEADER => false,    // don't return headers
            CURLOPT_FOLLOWLOCATION => true,     // follow redirects
            CURLOPT_ENCODING => '',       // handle all encodings
            CURLOPT_AUTOREFERER => true,     // set referer on redirect
            CURLOPT_CONNECTTIMEOUT => 120,      // timeout on connect
            CURLOPT_TIMEOUT => 120,      // timeout on response
            CURLOPT_MAXREDIRS => 10,       // stop after 10 redirects
        ];

        $ch = curl_init($url);
        curl_setopt_array($ch, $options);
        $content = curl_exec($ch);
        $err = curl_errno($ch);
        $errmsg = curl_error($ch);
        $header = curl_getinfo($ch);
        curl_close($ch);

        if ($err !== 0 || $header['http_code'] !== 200) {
            echo "    Could not load $url ($errmsg)\n";
        }

        return $content;
    }

    public function getPlayers()
    {
        $cachedPlayers = json_decode(file_get_contents(storage_path().'/app/players.json'), true);
        $cachedPlayerKeys = array_keys($cachedPlayers);
        $players = [];

        // build the teams lookup table if it doesn't exist
        foreach (NflTeam::all() as $team) {
            $teams[$team->abbr] = $team->id;
        }

        // for each team get rosters
        foreach ($teams as $abbr => $teamId) {
            $url = $this->rosterUrl.$abbr;
            $start = microtime(true);
            echo "    Fetching players from {$abbr}...";
            $crawler = new Crawler();
            $crawler->addContent($this->curlFetchUrl($url));

            foreach ($crawler->filter('table[id=result] tbody tr') as $row) {
                $tmp = [
                    'team_id' => $teamId,
                    'active' => 1,
                ];

                if ($row->getAttribute('class') === 'even' || $row->getAttribute('class') === 'odd') {
                    $playerUri = null;
                    foreach ($row->childNodes as $num => $node) {
                        if (get_class($node) == 'DOMElement') {
                            $children = $node->childNodes;
                            if ($children->length > 1) {
                                $matches = [];
                                $playerUri = $children->item(1)->getAttribute('href');
                                if (!preg_match('/(\d+)/', $playerUri, $matches)) {
                                    throw new Exception('ERROR: Cannot find profile ID');
                                }
                                $tmp['profile_id'] = $matches[1];
                                $nameParts = explode(',', $children->item(1)->childNodes->item(0)->wholeText);
                                $tmp['name'] = trim($nameParts[1]).' '.trim($nameParts[0]);
                            } elseif ($num == 4) {
                                $tmp['position'] = $children->item(0)->wholeText;
                            }
                        }
                    }

                    $result = NflPlayer::where('profile_id', '=', $tmp['profile_id'])->orderBy('created_at', 'desc')->first();
                    if ($result) {
                        if ($tmp['position'] != $result->position || $tmp['team_id'] != $result->team_id) {
                            // deactivate old
                            $result->active = 0;
                            $result->save();
                        } else {
                            $result->active = 1;
                            $result->save();
                            continue;
                        }
                    } else {
                        // check to see if it is in the cached copy
                        if (!in_array($tmp['profile_id'], $cachedPlayerKeys)) {
                            // fetch gsis id and name
                            $profile = $this->curlFetchUrl($this->baseUrl.$playerUri);
                            $matches = [];
                            $tmp['gsis_id'] = (preg_match('/GSIS ID: (\d\d-\d\d\d\d\d\d\d)/', $profile, $matches)) ? $matches[1] : null;
                            $cachedPlayers[$tmp['profile_id']] = [
                                'gsis_id' => $tmp['gsis_id'],
                                'profile_id' => $tmp['profile_id'],
                                'name' => $tmp['name'],
                            ];
                        } else {
                            $tmp['gsis_id'] = $cachedPlayers[$tmp['profile_id']]['gsis_id'];
                        }
                    }

                    // create new player
                    $players[] = $tmp;
                }
            }

            echo 'DONE. ('.number_format(microtime(true) - $start, 2)."s)\n";
        }

        echo '    Saving player data...';
        // write cached players back for any updates
        file_put_contents(storage_path().'/app/players.json', json_encode($cachedPlayers));
        echo "DONE.\n";

        // return players
        return $players;
    }

    public function getTeams()
    {
        $this->_teams = [
            ['abbr' => 'ARI', 'city' => 'Arizona', 'name' => 'Cardinals', 'conference' => 'NFC'],
            ['abbr' => 'ATL', 'city' => 'Atlanta', 'name' => 'Falcons', 'conference' => 'NFC'],
            ['abbr' => 'BAL', 'city' => 'Baltimore', 'name' => 'Ravens', 'conference' => 'AFC'],
            ['abbr' => 'BUF', 'city' => 'Buffalo', 'name' => 'Bills', 'conference' => 'AFC'],
            ['abbr' => 'CAR', 'city' => 'Carolina', 'name' => 'Panthers', 'conference' => 'NFC'],
            ['abbr' => 'CHI', 'city' => 'Chicago', 'name' => 'Bears', 'conference' => 'NFC'],
            ['abbr' => 'CIN', 'city' => 'Cincinnati', 'name' => 'Bengals', 'conference' => 'AFC'],
            ['abbr' => 'CLE', 'city' => 'Cleveland', 'name' => 'Browns', 'conference' => 'AFC'],
            ['abbr' => 'DAL', 'city' => 'Dallas', 'name' => 'Cowboys', 'conference' => 'NFC'],
            ['abbr' => 'DEN', 'city' => 'Denver', 'name' => 'Broncos', 'conference' => 'AFC'],
            ['abbr' => 'DET', 'city' => 'Detroit', 'name' => 'Lions', 'conference' => 'NFC'],
            ['abbr' => 'GB', 'city' => 'Green Bay', 'name' => 'Packers', 'conference' => 'NFC'],
            ['abbr' => 'HOU', 'city' => 'Houston', 'name' => 'Texans', 'conference' => 'AFC'],
            ['abbr' => 'IND', 'city' => 'Indianapolis', 'name' => 'Colts', 'conference' => 'AFC'],
            ['abbr' => 'JAC', 'city' => 'Jacksonville', 'name' => 'Jaguars', 'conference' => 'AFC'],
            ['abbr' => 'KC', 'city' => 'Kansas City', 'name' => 'Chiefs', 'conference' => 'AFC'],
            ['abbr' => 'MIA', 'city' => 'Miami', 'name' => 'Dolphins', 'conference' => 'AFC'],
            ['abbr' => 'MIN', 'city' => 'Minnesota', 'name' => 'Vikings', 'conference' => 'NFC'],
            ['abbr' => 'NE', 'city' => 'New England', 'name' => 'Patriots', 'conference' => 'AFC'],
            ['abbr' => 'NO', 'city' => 'New Orleans', 'name' => 'Saints', 'conference' => 'NFC'],
            ['abbr' => 'NYG', 'city' => 'New York', 'name' => 'Giants', 'conference' => 'NFC'],
            ['abbr' => 'NYJ', 'city' => 'New York', 'name' => 'Jets', 'conference' => 'AFC'],
            ['abbr' => 'OAK', 'city' => 'Oakland', 'name' => 'Raiders', 'conference' => 'AFC'],
            ['abbr' => 'PHI', 'city' => 'Philadelphia', 'name' => 'Eagles', 'conference' => 'NFC'],
            ['abbr' => 'PIT', 'city' => 'Pittsburgh', 'name' => 'Steelers', 'conference' => 'AFC'],
            ['abbr' => 'SD', 'city' => 'San Diego', 'name' => 'Chargers', 'conference' => 'AFC'],
            ['abbr' => 'SEA', 'city' => 'Seattle', 'name' => 'Seahawks', 'conference' => 'NFC'],
            ['abbr' => 'SF', 'city' => 'San Francisco', 'name' => '49ers', 'conference' => 'NFC'],
            ['abbr' => 'STL', 'city' => 'St. Louis', 'name' => 'Rams', 'conference' => 'NFC'],
            ['abbr' => 'TB', 'city' => 'Tampa Bay', 'name' => 'Buccaneers', 'conference' => 'NFC'],
            ['abbr' => 'TEN', 'city' => 'Tennessee', 'name' => 'Titans', 'conference' => 'AFC'],
            ['abbr' => 'WAS', 'city' => 'Washington', 'name' => 'Redskins', 'conference' => 'NFC'],
        ];

        // return the teams array
        return $this->_teams;
    }

    public function getGames()
    {
        // build the teams lookup table if it doesn't exist
        echo '    Building team lookup table...';
        $teams = array();
        foreach (NflTeam::all() as $team) {
            $teams[$team->abbr] = $team->id;
        }
        echo "DONE.\n";

        // for each week
        echo '    Fetching regular season games...';
        $games = array();
        foreach (range(1, 17) as $week) {
            $this->fetchGameData($games, $teams, $week, 'REG');
        }
        echo "DONE.\n";

        echo '    Fetching post season games...';
        foreach (array(18, 19, 20, 22) as $week) {
            $this->fetchGameData($games, $teams, $week, 'POST');
        }
        echo "DONE.\n";

        // return the games array
        return $games;
    }

    private function fetchGameData(&$games, $teams, $week, $type)
    {
        // get the html data for that week
        $url = 'http://www.nfl.com/ajax/scorestrip?season='.$this->_year.'&seasonType='.$type.'&week='.$week;
        $xml = simplexml_load_file($url);

        // parse the xml
        foreach ($xml->xpath('//g') as $game) {
            $attributes = $game->attributes();

            // get the nfl game id
            $game_id = (string) $attributes->eid;
            $game_key = (string) $attributes->gsis;

            // get the date parts and time
            $y = substr($game_id, 0, 4);
            $m = substr($game_id, 4, 2);
            $d = substr($game_id, 6, 2);
            list($h, $i) = explode(':', (string) $attributes->t);
            if ($h != 12) {
                $h += 12;
            }

            // add data to the games array
            $games[] = array(
                'week' => $week,
                'starts_at' => "$y-$m-$d $h:$i:00",
                'away_team_id' => $teams[(string) $attributes->v],
                'home_team_id' => $teams[(string) $attributes->h],
                'game_id' => $attributes->eid,
                'game_key' => $attributes->gsis,
                'type' => $attributes->gt,
            );
        }

        unset($game);
        unset($xml);

        return $games;
    }

    public function getStats($week)
    {
        $results = array();
        foreach (NflGame::with(['homeTeam', 'awayTeam'])->where('week', '=', $week)->get() as $game) {
            $gameId = $game->game_id;
            $url = 'http://www.nfl.com/liveupdate/game-center/'.$gameId.'/'.$gameId.'_gtd.json?random='.microtime(true);
            $page = json_decode($this->curlFetchUrl($url));
            $data = array();
            if (!empty($page->$gameId)) {
                $data['hometeam'] = $page->$gameId->home->abbr;
                $data['awayteam'] = $page->$gameId->away->abbr;
                $data['homescore'] = $page->$gameId->home->score->T;
                $data['awayscore'] = $page->$gameId->away->score->T;

                foreach (array('hometeam' => $page->$gameId->home->stats, 'awayteam' => $page->$gameId->away->stats) as $team => $stats) {
                    foreach (array('passing', 'rushing', 'receiving', 'kicking', 'kickret', 'puntret') as $type) {
                        if (isset($stats->$type)) {
                            foreach ($stats->$type as $id => $player) {
                                if (isset($data[$data[$team]][$id])) {
                                    $data[$data[$team]][$id]['td'] += (isset($player->tds)) ? $player->tds : 0;
                                    $data[$data[$team]][$id]['two'] += (isset($player->twoptm)) ? $player->twoptm : 0;
                                    $data[$data[$team]][$id]['fg'] += (isset($player->fgm)) ? $player->fgm : 0;
                                    $data[$data[$team]][$id]['xp'] += (isset($player->xpmade)) ? $player->xpmade : 0;
                                } else {
                                    $data[$data[$team]][$id] = array(
                                        'td' => (isset($player->tds)) ? $player->tds : 0,
                                        'two' => (isset($player->twoptm)) ? $player->twoptm : 0,
                                        'fg' => (isset($player->fgm)) ? $player->fgm : 0,
                                        'xp' => (isset($player->xpmade)) ? $player->xpmade : 0,
                                    );
                                }
                            }
                        }
                    }
                }
                $results[$game->game_id] = $data;
            }
        }

        unset($page);
        unset($data);
        unset($stats);

        return $results;
    }
}
