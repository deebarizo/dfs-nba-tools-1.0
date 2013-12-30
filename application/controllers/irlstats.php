<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Irlstats extends CI_Controller 
{

	public function index()
	{
		$this->load->database();

		ini_set('max_execution_time', 10800); // 10800 seconds = 3 hours

		$date_range = $this->create_date_range_array('2013-12-29', '2013-12-29'); 

		$this->load->helper('phpquery');

		foreach ($date_range as $key => $date) 
		{
			$date_segment = preg_replace("/\-/", "", $date);

			$html = phpQuery::newDocumentFileHTML('http://scores.espn.go.com/nba/scoreboard?date='.$date_segment);

			$count = $html->find('.expand-gameLinks');
			$num_urls = count($count);

			$data_to_insert[$date]['num_of_games'] = $num_urls;

			if ($num_urls > 0)
			{
				for ($n=0; $n < $num_urls; $n++)
				{
					$url_segment = $html->find('div[class=expand-gameLinks]:eq('.$n.') a:first')->attr('href');
					$data_to_insert[$date]['games'][$n]['url'] = 'http://scores.espn.go.com'.$url_segment;
				}
			}
		}

		foreach ($data_to_insert as $key => &$date) 
		{
			$sql = 'INSERT INTO `date`(`date`, `num_of_games`) VALUES (:date, :num_of_games)';
			$s = $this->db->conn_id->prepare($sql);
			$s->bindValue(':date', $key);
			$s->bindValue(':num_of_games', $date['num_of_games']);
			$s->execute(); 	

			if ($date['num_of_games'] > 0)
			{
				foreach ($date as $key2 => &$games) 
				{
					if ($key2 === 'games')
					{
						foreach ($games as $key3 => &$game) 
						{
							$html = phpQuery::newDocumentFileHTML($game['url']);

							$game['team1'] = $html->find('tr[class=periods]')->next()->find('td[class=team]')->text();
							$game['team2'] = $html->find('tr[class=periods]')->next()->next()->find('td[class=team]')->text();

							$game['score1'] = $html->find('td[class=ts]:eq(0)')->text();
							$game['score2'] = $html->find('td[class=ts]:eq(1)')->text();

							$sql = 'INSERT INTO `games`(`url_espn`, `team1`, `team2`, `score1`, `score2`, `date`) 
									VALUES (:url_espn, :team1, :team2, :score1, :score2, :date)';
							$s = $this->db->conn_id->prepare($sql);
							$s->bindValue(':url_espn', $game['url']);
							$s->bindValue(':team1', $game['team1']);
							$s->bindValue(':team2', $game['team2']);
							$s->bindValue(':score1', $game['score1']);
							$s->bindValue(':score2', $game['score2']);
							$s->bindValue(':date', $key);
							$s->execute(); 	

							$count = $html->find('table[class=mod-data] td');
							$num_td = count($count);

							for ($n = 0; $n < $num_td; $n++)
							{
								$stats[$n] = $html->find('table[class=mod-data] td:eq('.$n.')')->text();
							}

							foreach ($stats as &$stat) 
							{
								// get rid of weird "Â" character
								// http://stackoverflow.com/questions/14881286/ignore-if-there-is-url
								// http://www.stemkoski.com/php-remove-non-ascii-characters-from-a-string/
								$stat = preg_replace('/[^(\x20-\x7F)]*/', "", $stat);
							}

							unset($stat);

							foreach ($stats as $key4 => $stat) 
							{
								if (substr($stat, 0, 17) === 'Fast break points')
								{
									$key_to_halve_array = $key4+1;
									break;
								}
							}

							for ($n = 0; $n < 5; $n++)
							{
								$raw_player_data['team1']['starter'][] = array_slice($stats, $n*15, 15);
								$raw_player_data['team2']['starter'][] = array_slice($stats, $key_to_halve_array+($n*15), 15);
							}

							$bench_raw_data[1] = array_slice($stats, 75, $key_to_halve_array-75);
							$bench_raw_data[2] = array_slice($stats, $key_to_halve_array+75);

							for ($n = 1; $n <= 2; $n++) 
							{ 
								for ($i = 0; $i <= 200; $i = $i + 15) 
								{ 
									if (isset($bench_raw_data[$n][$i+1]) AND is_numeric($bench_raw_data[$n][$i+1]))
									{
										$raw_player_data['team'.$n]['bench'][] = array_slice($bench_raw_data[$n], $i, 15);
									}
									else
									{
										break;
									}
								}

								foreach ($bench_raw_data[$n] as $key10 => $row) 
								{
									if (substr($row, 0, 3) === 'DNP')
									{
										$raw_player_data['team'.$n]['bench'][] = array_slice($bench_raw_data[$n], $key10-1, 2);
									}
								}
							}

							foreach ($raw_player_data as $key5 => &$team) 
							{
								foreach ($team as $key6 => &$player_type) 
								{
									foreach ($player_type as &$row) 
									{
										$row['name'] = preg_replace('/(.*),(.*)/', '$1', $row[0]);
										$row['position'] = preg_replace('/(.*),(.*)/', '$2', $row[0]);

										if ($key5 === 'team1') 
										{ 
											$row['team'] = $game['team1']; 
											$row['opponent'] = $game['team2'];
										}

										if ($key5 === 'team2') 
										{ 
											$row['team'] = $game['team2']; 
											$row['opponent'] = $game['team1'];
										}

										if ($key6 === 'starter') { $row['starter'] = 'yes'; }
										if ($key6 === 'bench') { $row['starter'] = 'no'; }

										if (is_numeric($row[1]) === true)
										{
											$row['played'] = 'yes';

											$row['minutes'] = $row[1];
											$row['fgm'] = preg_replace('/(.*)-(.*)/', '$1', $row[2]);
											$row['fga'] = preg_replace('/(.*)-(.*)/', '$2', $row[2]);
											$row['threepm'] = preg_replace('/(.*)-(.*)/', '$1', $row[3]);
											$row['threepa'] = preg_replace('/(.*)-(.*)/', '$2', $row[3]);
											$row['ftm'] = preg_replace('/(.*)-(.*)/', '$1', $row[4]);
											$row['fta'] = preg_replace('/(.*)-(.*)/', '$2', $row[4]);
											$row['oreb'] = $row[5];
											$row['dreb'] = $row[6];
											$row['reb'] = $row[7];
											$row['ast'] = $row[8];
											$row['stl'] = $row[9];
											$row['blk'] = $row[10];
											$row['turnovers'] = $row[11];
											$row['pfouls'] = $row[12];
											$row['plus_minus'] = $row[13];
											$row['pts'] = $row[14];
											$row['fpts_ds'] = 
												$row['pts']+
												($row['reb']*1.25)+
												($row['ast']*1.5)-
												$row['turnovers']+
												(($row['fga']-$row['fgm'])*-0.5)+
												(($row['fta']-$row['ftm'])*-0.5)+
												($row['stl']*2)+
												($row['blk']*2);
											$row['date'] = $key;

											for ($i=0; $i < 15; $i++) 
											{ 
												unset($row[$i]);
											}	
										}
										else
										{
											$row['played'] = $row[1];

											$row['minutes'] = NULL;
											$row['fgm'] = NULL;
											$row['fga'] = NULL;
											$row['threepm'] = NULL;
											$row['threepa'] = NULL;
											$row['ftm'] = NULL;
											$row['fta'] = NULL;
											$row['oreb'] = NULL;
											$row['dreb'] = NULL;
											$row['reb'] = NULL;
											$row['ast'] = NULL;
											$row['stl'] = NULL;
											$row['blk'] = NULL;
											$row['turnovers'] = NULL;
											$row['pfouls'] = NULL;
											$row['plus_minus'] = NULL;
											$row['pts'] = NULL;
											$row['fpts_ds'] = NULL;
											$row['date'] = $key;

											unset($row[0]);
											unset($row[1]);											
										}

										$sql = 'INSERT INTO `irlstats`(`name`, `position`, `team`, `opponent`, `starter`, `played`, 
																		`minutes`, `fgm`, `fga`, `threepm`, `threepa`, `ftm`, `fta`, `oreb`, `dreb`, `reb`, 
																		`ast`, `stl`, `blk`, `turnovers`, `pfouls`, `plus_minus`, `pts`, `fpts_ds`, `date`) 
												VALUES (:name, :position, :team, :opponent, :starter, :played,
														:minutes, :fgm, :fga, :threepm, :threepa, :ftm, :fta, :oreb, :dreb, :reb,
														:ast, :stl, :blk, :turnovers, :pfouls, :plus_minus, :pts, :fpts_ds, :date)';
										$s = $this->db->conn_id->prepare($sql);
										$s->bindValue(':name', $row['name']);
										$s->bindValue(':position', $row['position']);
										$s->bindValue(':team', $row['team']);
										$s->bindValue(':opponent', $row['opponent']);
										$s->bindValue(':starter', $row['starter']);
										$s->bindValue(':played', $row['played']);
										$s->bindValue(':minutes', $row['minutes']);
										$s->bindValue(':fgm', $row['fgm']);
										$s->bindValue(':fga', $row['fga']);
										$s->bindValue(':threepm', $row['threepm']);
										$s->bindValue(':threepa', $row['threepa']);
										$s->bindValue(':ftm', $row['ftm']);
										$s->bindValue(':fta', $row['fta']);
										$s->bindValue(':oreb', $row['oreb']);
										$s->bindValue(':dreb', $row['dreb']);
										$s->bindValue(':reb', $row['reb']);
										$s->bindValue(':ast', $row['ast']);
										$s->bindValue(':stl', $row['stl']);
										$s->bindValue(':blk', $row['blk']);
										$s->bindValue(':turnovers', $row['turnovers']);
										$s->bindValue(':pfouls', $row['pfouls']);
										$s->bindValue(':plus_minus', $row['plus_minus']);
										$s->bindValue(':pts', $row['pts']);
										$s->bindValue(':fpts_ds', $row['fpts_ds']);
										$s->bindValue(':date', $row['date']);
										$s->execute(); 	
									}

									unset($row);
								}

								unset($player_type);
							}

							unset($team);

							array_push($game, $raw_player_data);

							unset($raw_player_data);
						}

						unset($game);
					}
				}

				unset($games);
			}
		}

		unset($date);
	}

	function create_date_range_array($strDateFrom,$strDateTo)
	{
	    // takes two dates formatted as YYYY-MM-DD and creates an
	    // inclusive array of the dates between the from and to dates.

	    // could test validity of dates here but I'm already doing
	    // that in the main script

	    $aryRange=array();

	    $iDateFrom=mktime(1,0,0,substr($strDateFrom,5,2),     substr($strDateFrom,8,2),substr($strDateFrom,0,4));
	    $iDateTo=mktime(1,0,0,substr($strDateTo,5,2),     substr($strDateTo,8,2),substr($strDateTo,0,4));

	    if ($iDateTo>=$iDateFrom)
	    {
	        array_push($aryRange,date('Y-m-d',$iDateFrom)); // first entry
	        while ($iDateFrom<$iDateTo)
	        {
	            $iDateFrom+=86400; // add 24 hours
	            array_push($aryRange,date('Y-m-d',$iDateFrom));
	        }
	    }

	    return $aryRange;
	}

}