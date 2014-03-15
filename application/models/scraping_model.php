<?php
class scraping_model extends CI_Model 
{

	public function __construct()
	{
		parent::__construct();

		$this->load->database();
	}

	public function add_fpts_of_new_site()
	{
		ini_set('max_execution_time', 10800); // 10800 seconds = 3 hours

		$sql = 'SELECT * FROM `irlstats`';
		$s = $this->db->conn_id->prepare($sql);
		$s->execute(); 	

		$irlstats = $s->fetchAll(PDO::FETCH_ASSOC);	

		foreach ($irlstats as &$value) 
		{
			$fpts_fd = $value['pts'] +
								($value['reb'] * 1.2) +
								($value['ast'] * 1.5) +
								($value['blk'] * 2) +
								($value['stl'] * 2) +
								($value['turnovers'] * -1);

			$sql = 'UPDATE `irlstats` SET `fpts_fd` = :fpts_fd WHERE `id` = :id';
			$s = $this->db->conn_id->prepare($sql);
			$s->bindValue(':fpts_fd', $fpts_fd);
			$s->bindValue(':id', $value['id']);
			$s->execute();
		}

		unset($value);

		echo '<pre>';
		var_dump($irlstats);
		echo '</pre>'; exit();		
	}

	public function scrape_fd_salaries($form_data, $today_year)
	{
		$url = $form_data['url'];

		$this->load->helper('phpquery');

		$html = phpQuery::newDocumentFileHTML($url);

		$h1_tag_with_date = $html->find('span[class=sport-icon]')->parent()->text();

		$month_and_day = preg_replace("/(.+)(\w\w\w\s\d+)(\w\w$)/", "$2", $h1_tag_with_date);

		$date = date('Y-m-d', strtotime($month_and_day.', '.$today_year));

		$sql = 'SELECT `date` FROM `fstats_fd` WHERE `date` = :date';
		$s = $this->db->conn_id->prepare($sql);
		$s->bindValue(':date', $date);
		$s->execute(); 	

		$result = $s->fetchAll(PDO::FETCH_COLUMN);	

		if (empty($result))
		{
			$result = $html->find('tr[data-role=player]');
			$num_players = count($result);

			for ($i = 0; $i < $num_players; $i++) 
			{ 
				$fstats_fd[$i]['name'] = $html->find('tr[data-role=player]:eq('.$i.')')->find('td:eq(1)')->text();
				$fstats_fd[$i]['team'] = $html->find('tr[data-role=player]:eq('.$i.')')->find('td:eq(4)')->find('b')->text();
				$fstats_fd[$i]['position'] = $html->find('tr[data-role=player]:eq('.$i.')')->find('td:eq(0)')->text();
				
				$salary = $html->find('tr[data-role=player]:eq('.$i.')')->find('td:eq(5)')->text();
				$salary = preg_replace("/\\$/", "", $salary);
				$salary = preg_replace("/,/", "", $salary);
				$fstats_fd[$i]['salary'] = $salary; 

				$opponent = $html->find('tr[data-role=player]:eq('.$i.')')->find('td:eq(4)')->text();
				$opponent = preg_replace("/@/", "", $opponent);
				$opponent = preg_replace("/".$fstats_fd[$i]['team']."/", "", $opponent);
				$fstats_fd[$i]['opponent'] = $opponent; 

				$fstats_fd[$i]['num_games'] = $html->find('tr[data-role=player]:eq('.$i.')')->find('td:eq(3)')->text();
				$fstats_fd[$i]['fppg'] = $html->find('tr[data-role=player]:eq('.$i.')')->find('td:eq(2)')->text();
			}

			# echo '<pre>';
			# var_dump($fstats_fd);
			# echo '</pre>'; exit();	

			foreach ($fstats_fd as $key => $value) 
			{
				$sql = 'INSERT INTO `fstats_fd`(`name`, 
												`team`, 
												`position`,
												`salary`,
												`opponent`,
												`num_games`,
												`fppg`,
												`date`) 
						VALUES (:name, 
								:team, 
								:position,
								:salary,
								:opponent,
								:num_games,
								:fppg,
								:date)'; 
				$s = $this->db->conn_id->prepare($sql);
				$s->bindValue(':name', $value['name']);
				$s->bindValue(':team', $value['team']);
				$s->bindValue(':position', $value['position']);
				$s->bindValue(':salary', $value['salary']);
				$s->bindValue(':opponent', $value['opponent']);
				$s->bindValue(':num_games', $value['num_games']);
				$s->bindValue(':fppg', $value['fppg']);
				$s->bindValue(':date', $date);
				$s->execute(); 
			}				

			return 'Success: The FD salaries for this date were scraped.';
		}
		else
		{
			return 'Error: The FD salaries for this date are already in the database.';
		}	
	}

	public function scrape_team_opp_stats($form_data)
	{
		$date = $form_data['date'];

		$sql = 'SELECT `date` FROM `team_opp_stats` WHERE `date` = :date';
		$s = $this->db->conn_id->prepare($sql);
		$s->bindValue(':date', $date);
		$s->execute(); 	

		$result = $s->fetchAll(PDO::FETCH_COLUMN);	

		if (empty($result))
		{
			$this->load->helper('phpquery');

			$html = phpQuery::newDocumentFileHTML('http://espn.go.com/nba/statistics/team/_/stat/defense-per-game');

			$n = 1;
			$i = 0;

			do
			{
				$check = $html->find('div[id=my-teams-table]')->find('div[class=mod-content]')->find('table[class=tablehead]')->find('tr:eq('.$n.')')->find('td:eq(1)')->text();

				if ($check != 'TEAM' AND $check != '')
				{
					$team_opp_pts_breakdown[$i]['team'] = $html->find('div[id=my-teams-table]')->find('div[class=mod-content]')->find('table[class=tablehead]')->find('tr:eq('.$n.')')->find('td:eq(1)')->text();
					$team_opp_pts_breakdown[$i]['threepm_per_game'] = $html->find('div[id=my-teams-table]')->find('div[class=mod-content]')->find('table[class=tablehead]')->find('tr:eq('.$n.')')->find('td:eq(6)')->text();
					$team_opp_pts_breakdown[$i]['threepa_per_game'] = $html->find('div[id=my-teams-table]')->find('div[class=mod-content]')->find('table[class=tablehead]')->find('tr:eq('.$n.')')->find('td:eq(7)')->text();
					$team_opp_pts_breakdown[$i]['threep_percentage'] = $html->find('div[id=my-teams-table]')->find('div[class=mod-content]')->find('table[class=tablehead]')->find('tr:eq('.$n.')')->find('td:eq(8)')->text();
					$team_opp_pts_breakdown[$i]['fta_per_game'] = $html->find('div[id=my-teams-table]')->find('div[class=mod-content]')->find('table[class=tablehead]')->find('tr:eq('.$n.')')->find('td:eq(10)')->text();

					$i++;

					if ($i == 30) { break; }
				}

				$n++;
			} while ($n < 100);

			$html = phpQuery::newDocumentFileHTML('http://espn.go.com/nba/statistics/team/_/stat/rebounds-per-game');

			$n = 1;
			$i = 0;

			do
			{
				$check = $html->find('div[id=my-teams-table]')->find('div[class=mod-content]')->find('table[class=tablehead]')->find('tr:eq('.$n.')')->find('td:eq(1)')->text();

				if ($check != 'TEAM' AND $check != '' AND $check != 'REBOUND PCT')
				{
					$team_reb_percentage[$i]['team'] = $html->find('div[id=my-teams-table]')->find('div[class=mod-content]')->find('table[class=tablehead]')->find('tr:eq('.$n.')')->find('td:eq(1)')->text();
					$team_reb_percentage[$i]['oreb_percentage'] = $html->find('div[id=my-teams-table]')->find('div[class=mod-content]')->find('table[class=tablehead]')->find('tr:eq('.$n.')')->find('td:eq(2)')->text();
					$team_reb_percentage[$i]['dreb_percentage'] = $html->find('div[id=my-teams-table]')->find('div[class=mod-content]')->find('table[class=tablehead]')->find('tr:eq('.$n.')')->find('td:eq(3)')->text();
					$team_reb_percentage[$i]['treb_percentage'] = $html->find('div[id=my-teams-table]')->find('div[class=mod-content]')->find('table[class=tablehead]')->find('tr:eq('.$n.')')->find('td:eq(4)')->text();

					$i++;

					if ($i == 30) { break; }
				}

				$n++;
			} while ($n < 100);

			$html = phpQuery::newDocumentFileHTML('http://espn.go.com/nba/statistics/team/_/stat/miscellaneous-per-game');

			$n = 1;
			$i = 0;

			do
			{
				$check = $html->find('div[id=my-teams-table]')->find('div[class=mod-content]')->find('table[class=tablehead]')->find('tr:eq('.$n.')')->find('td:eq(1)')->text();

				if ($check != 'TEAM' AND $check != '' AND $check != 'ASSISTS')
				{
					$team_opp_misc[$i]['team'] = $html->find('div[id=my-teams-table]')->find('div[class=mod-content]')->find('table[class=tablehead]')->find('tr:eq('.$n.')')->find('td:eq(1)')->text();
					$team_opp_misc[$i]['ast_per_game'] = $html->find('div[id=my-teams-table]')->find('div[class=mod-content]')->find('table[class=tablehead]')->find('tr:eq('.$n.')')->find('td:eq(3)')->text();
					$team_opp_misc[$i]['to_per_game'] = $html->find('div[id=my-teams-table]')->find('div[class=mod-content]')->find('table[class=tablehead]')->find('tr:eq('.$n.')')->find('td:eq(9)')->text();
					$team_opp_misc[$i]['stl_per_game'] = $html->find('div[id=my-teams-table]')->find('div[class=mod-content]')->find('table[class=tablehead]')->find('tr:eq('.$n.')')->find('td:eq(5)')->text();
					$team_opp_misc[$i]['blk_per_game'] = $html->find('div[id=my-teams-table]')->find('div[class=mod-content]')->find('table[class=tablehead]')->find('tr:eq('.$n.')')->find('td:eq(7)')->text();

					$i++;

					if ($i == 30) { break; }
				}

				$n++;
			} while ($n < 100);

			$team_opp_stats = $team_opp_pts_breakdown;

			foreach ($team_opp_stats as $key => &$stat) 
			{
				foreach ($team_reb_percentage as $value) 
				{
					if ($stat['team'] == $value['team'])
					{
						$stat['oreb_percentage'] = $value['oreb_percentage'];
						$stat['dreb_percentage'] = $value['dreb_percentage'];
						$stat['treb_percentage'] = $value['treb_percentage'];

						break;
					}
				}
			}

			unset($stat);

			foreach ($team_opp_stats as $key => &$stat) 
			{
				foreach ($team_opp_misc as $value) 
				{
					if ($stat['team'] == $value['team'])
					{
						$stat['ast_per_game'] = $value['ast_per_game'];
						$stat['to_per_game'] = $value['to_per_game'];
						$stat['stl_per_game'] = $value['stl_per_game'];
						$stat['blk_per_game'] = $value['blk_per_game'];

						break;
					}
				}
			}

			unset($stat);

			foreach ($team_opp_stats as $key => $value) 
			{
				$sql = 'INSERT INTO `team_opp_stats`(`team`, 
													 `threepm_per_game`, 
													 `threepa_per_game`,
													 `threep_percentage`,
													 `fta_per_game`,
													 `oreb_percentage`,
													 `dreb_percentage`,
													 `treb_percentage`,
													 `ast_per_game`,
													 `to_per_game`,
													 `stl_per_game`,
													 `blk_per_game`,
													 `date`) 
						VALUES (:team, 
								:threepm_per_game, 
								:threepa_per_game,
								:threep_percentage,
								:fta_per_game,
								:oreb_percentage,
								:dreb_percentage,
								:treb_percentage,
								:ast_per_game,
								:to_per_game,
								:stl_per_game,
								:blk_per_game,
								:date)'; 
				$s = $this->db->conn_id->prepare($sql);
				$s->bindValue(':team', $value['team']);
				$s->bindValue(':threepm_per_game', $value['threepm_per_game']);
				$s->bindValue(':threepa_per_game', $value['threepa_per_game']);
				$s->bindValue(':threep_percentage', $value['threep_percentage']);
				$s->bindValue(':fta_per_game', $value['fta_per_game']);
				$s->bindValue(':oreb_percentage', $value['oreb_percentage']);
				$s->bindValue(':dreb_percentage', $value['dreb_percentage']);
				$s->bindValue(':treb_percentage', $value['treb_percentage']);
				$s->bindValue(':ast_per_game', $value['ast_per_game']);
				$s->bindValue(':to_per_game', $value['to_per_game']);
				$s->bindValue(':stl_per_game', $value['stl_per_game']);
				$s->bindValue(':blk_per_game', $value['blk_per_game']);
				$s->bindValue(':date', $date);
				$s->execute(); 
			}			

			return 'Success: The team opp stats for this date were scraped.';
		}
		else
		{
			return 'Error: The team opp stats for this date are already in the database.';
		}	
	}

	public function scrape_pace($form_data)
	{
		$date = $form_data['date'];

		$sql = 'SELECT `date` FROM `pace` WHERE `date` = :date';
		$s = $this->db->conn_id->prepare($sql);
		$s->bindValue(':date', $date);
		$s->execute(); 	

		$result = $s->fetchAll(PDO::FETCH_COLUMN);	

		if (empty($result))
		{	
			$this->load->helper('phpquery');

			$html = phpQuery::newDocumentFileHTML('http://www.basketball-reference.com/leagues/NBA_2014.html');

			for ($n = 0; $n < 30; $n++)
			{
				$pace[$n]['team'] = $html->find('div[id=all_misc_stats]')->find('table[id=misc]')->find('tbody')->find('tr:eq('.$n.')')->find('td:eq(1)')->text();
				$pace[$n]['poss_per_48'] = $html->find('div[id=all_misc_stats]')->find('table[id=misc]')->find('tbody')->find('tr:eq('.$n.')')->find('td:eq(10)')->text();
			}

			$pace[30]['team'] = $html->find('div[id=all_misc_stats]')->find('table[id=misc]')->find('tbody')->find('tr:eq(30)')->find('td:eq(1)')->text();
			$pace[30]['poss_per_48'] = $html->find('div[id=all_misc_stats]')->find('table[id=misc]')->find('tbody')->find('tr:eq(30)')->find('td:eq(10)')->text();

			foreach ($pace as $key => $value) 
			{
				$sql = 'INSERT INTO `pace`(`team`, `poss_per_48`, `date`) VALUES (:team, :poss_per_48, :date)';
				$s = $this->db->conn_id->prepare($sql);
				$s->bindValue(':team', $value['team']);
				$s->bindValue(':poss_per_48', $value['poss_per_48']);
				$s->bindValue(':date', $date);
				$s->execute(); 
			}			

			return 'Success: The pace stats for this date were scraped.';
		}
		else
		{
			return 'Error: The pace stats for this date are already in the database.';
		}
	}

	public function scrape_irlstats($form_data)
	{
		$date = $form_data['date'];

		$sql = 'SELECT `date` FROM `irlstats` WHERE `date` = :date';
		$s = $this->db->conn_id->prepare($sql);
		$s->bindValue(':date', $date);
		$s->execute(); 	

		$result = $s->fetchAll(PDO::FETCH_COLUMN);

		if (empty($result))
		{
			ini_set('max_execution_time', 10800); // 10800 seconds = 3 hours

			$date_range = $this->create_date_range_array($date, $date); 

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
													$row['pts'] +
													($row['reb'] * 1.25) +
													($row['ast'] * 1.5) -
													$row['turnovers'] +
													(($row['fga'] - $row['fgm']) * -0.5) +
													(($row['fta'] - $row['ftm']) * -0.5) +
													($row['stl'] * 2) +
													($row['blk'] * 2);
												$row['fpts_fd'] = 
													$row['pts'] +
										    		($row['reb'] * 1.2) +
													($row['ast'] * 1.5) +
													($row['blk'] * 2) +
													($row['stl'] * 2) +
													($row['turnovers'] * -1);
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
												$row['fpts_fd'] = NULL;
												$row['date'] = $key;

												unset($row[0]);
												unset($row[1]);											
											}

											$sql = 'INSERT INTO `irlstats`(`name`, `position`, `team`, `opponent`, `starter`, `played`, 
																			`minutes`, `fgm`, `fga`, `threepm`, `threepa`, `ftm`, `fta`, `oreb`, `dreb`, `reb`, 
																			`ast`, `stl`, `blk`, `turnovers`, `pfouls`, `plus_minus`, `pts`, `fpts_ds`, `fpts_fd`, `date`) 
													VALUES (:name, :position, :team, :opponent, :starter, :played,
															:minutes, :fgm, :fga, :threepm, :threepa, :ftm, :fta, :oreb, :dreb, :reb,
															:ast, :stl, :blk, :turnovers, :pfouls, :plus_minus, :pts, :fpts_ds, :fpts_fd, :date)';
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
											$s->bindValue(':fpts_fd', $row['fpts_fd']);
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

			return 'Success: The box score stats for this date were scraped.';
		}
		else
		{
			return 'Error: The box score stats for this date are already in the database.';
		}
	}

	public function scrape_dvp($form_data)
	{
		$date = $form_data['date'];

		$sql = 'SELECT `date` FROM `dvp` WHERE `date` = :date';
		$s = $this->db->conn_id->prepare($sql);
		$s->bindValue(':date', $date);
		$s->execute(); 	

		$result = $s->fetchAll(PDO::FETCH_COLUMN);

		if (empty($result))
		{
			ini_set('max_execution_time', 10800); // 10800 seconds = 3 hours

			$this->load->helper('phpquery');

			$html = phpQuery::newDocumentFileHTML('http://rotogrinders.com/pages/NBA_Defense_vs_Position_Stats_All_Positions_Season-176473');

			$array_to_match_db = array('team', 'pg_fpa', 'pg_rank', 
				'sg_fpa', 'sg_rank', 'sf_fpa', 'sf_rank', 
				'pf_fpa', 'pf_rank', 'c_fpa', 'c_rank');

			for ($n = 1; $n <= 30; $n++) // This skips the table header row
			{
				for ($i = 0; $i < 11; $i++) 
				{ 
					$result = $html->find('table[class=sortable] tr:eq('.$n.') td:eq('.$i.')')->text();
					$dvp[$n][$array_to_match_db[$i]] = trim($result);
				}
			}

			foreach ($dvp as $key => &$value) 
			{
				$avg_rank = ($value['pg_rank'] + 
							$value['sg_rank'] + 
							$value['sf_rank'] + 
							$value['pf_rank'] + 
							$value['c_rank']) / 5;

				$value['pg_rank_mod'] = $value['pg_rank'] - $avg_rank;
				$value['sg_rank_mod'] = $value['sg_rank'] - $avg_rank;
				$value['sf_rank_mod'] = $value['sf_rank'] - $avg_rank;
				$value['pf_rank_mod'] = $value['pf_rank'] - $avg_rank;
				$value['c_rank_mod'] = $value['c_rank'] - $avg_rank;
			}

			unset($value);

			foreach ($dvp as $key => $value) 
			{
				$sql = 'INSERT INTO `dvp`(`team`, `pg_fpa`, `pg_rank`, `pg_rank_mod`, 
									`sg_fpa`, `sg_rank`, `sg_rank_mod`, 
									`sf_fpa`, `sf_rank`, `sf_rank_mod`, 
									`pf_fpa`, `pf_rank`, `pf_rank_mod`, 
									`c_fpa`, `c_rank`, `c_rank_mod`, `date`) 
						VALUES (:team, :pg_fpa, :pg_rank, :pg_rank_mod,
								:sg_fpa, :sg_rank, :sg_rank_mod,
								:sf_fpa, :sf_rank, :sf_rank_mod,
								:pf_fpa, :pf_rank, :pf_rank_mod,
								:c_fpa, :c_rank, :c_rank_mod, :date)';
				$s = $this->db->conn_id->prepare($sql);
				$s->bindValue(':team', $value['team']);
				$s->bindValue(':pg_fpa', $value['pg_fpa']);
				$s->bindValue(':pg_rank', $value['pg_rank']);
				$s->bindValue(':pg_rank_mod', $value['pg_rank_mod']);
				$s->bindValue(':sg_fpa', $value['sg_fpa']);
				$s->bindValue(':sg_rank', $value['sg_rank']);
				$s->bindValue(':sg_rank_mod', $value['sg_rank_mod']);
				$s->bindValue(':sf_fpa', $value['sf_fpa']);
				$s->bindValue(':sf_rank', $value['sf_rank']);
				$s->bindValue(':sf_rank_mod', $value['sf_rank_mod']);
				$s->bindValue(':pf_fpa', $value['pf_fpa']);
				$s->bindValue(':pf_rank', $value['pf_rank']);
				$s->bindValue(':pf_rank_mod', $value['pf_rank_mod']);
				$s->bindValue(':c_fpa', $value['c_fpa']);
				$s->bindValue(':c_rank', $value['c_rank']);
				$s->bindValue(':c_rank_mod', $value['c_rank_mod']);
				$s->bindValue(':date', $date);
				$s->execute(); 
			}

			return 'Success: The DvP stats for this date were scraped.';
		}
		else
		{
			return 'Error: The DvP stats for this date are already in the database.';
		}
	}

	public function scrape_odds($date)
	{
		ini_set('max_execution_time', 10800); // 10800 seconds = 3 hours

		$url_segment = preg_replace('/(\d\d\d\d)-(\d\d)-(\d\d)/', '$1$2$3', $date);

		$this->load->helper('phpquery');

		$html = phpQuery::newDocumentFileHTML('http://www.scoresandodds.com/grid_'.$url_segment.'.html');

		$raw_data = $html->find('div[id=nba]')->next()->find('table[class=data]')->find('tr[class=team odd]');
		$num_of_games = count($raw_data);

		for ($n=0; $n < $num_of_games; $n++)
		{
			$raw_data2['odd']['team'][] = $html->find('div[id=nba]')->next()->
									find('table[class=data]')->find('tr[class=team odd]')->
									find('td[class=name]:eq('.$n.')')->text();

			$raw_data2['odd']['line_ou'][] = $html->find('div[id=nba]')->next()->
									find('table[class=data]')->find('tr[class=team odd]')->
									find('td[class*=currentline]:eq('.$n.')')->text();

			$raw_data2['even']['team'][] = $html->find('div[id=nba]')->next()->
									find('table[class=data]')->find('tr[class=team even]')->
									find('td[class=name]:eq('.$n.')')->text();


			$raw_data2['even']['line_ou'][] = $html->find('div[id=nba]')->next()->
									find('table[class=data]')->find('tr[class=team even]')->
									find('td[class*=currentline]:eq('.$n.')')->text();
		}

		// clean up results

		foreach ($raw_data2 as $key => &$odd_even) 
		{
			foreach ($odd_even as $key2 => &$row) 
			{
				foreach ($row as &$value) 
				{
					if ($key2 == 'team')
					{
						$value = trim($value);
						$value = preg_replace('/\d+ /', '', $value);
					}

					if ($key2 == 'line_ou')
					{
						$value = trim($value);
						$value = preg_replace('/PK/', '0', $value);
						$value = preg_replace('/[a-zA-Z].*/', '', $value);
						$value = preg_replace('/ -\d+/', '', $value);
					}
				}

				unset($value);
			}

			unset($row);
		}

		unset($odd_even);	

		// generate relevant data

		foreach ($raw_data2 as $key => &$odd_even) 
		{
			foreach ($odd_even as $key2 => &$row) 
			{
				foreach ($row as $key3 => &$value) 
				{
					if ($key == 'odd' AND $key2 == 'team')
					{
						$odds[$key3]['team1'] = $value;
					}

					if ($key == 'even' AND $key2 == 'team')
					{
						$odds[$key3]['team2'] = $value;
					}

					if ($key == 'odd' AND $key2 == 'line_ou')
					{
						$odds[$key3]['line_ou1'] = $value;
					}

					if ($key == 'even' AND $key2 == 'line_ou')
					{
						$odds[$key3]['line_ou2'] = $value;
					}
				}
			}
		}

		foreach ($odds as $key => $game) 
		{
			if ($game['line_ou1'] == '' AND $game['line_ou2'] == '')
			{
				$games['no_lines'][$key]['team1'] = $game['team1']; 
				$games['no_lines'][$key]['team2'] = $game['team2']; 

				continue;
			}

			$games['has_lines'][$key]['team1'] = $game['team1']; 
			$games['has_lines'][$key]['team2'] = $game['team2']; 

			if ($game['line_ou1'] > 100)
			{
				$games['has_lines'][$key]['score1'] = ($game['line_ou1'] + $game['line_ou2']) / 2;
				$games['has_lines'][$key]['score2'] = $games['has_lines'][$key]['score1'] - $game['line_ou2'];

				$games['has_lines'][$key]['ps1'] = $game['line_ou2'] * -1;
				$games['has_lines'][$key]['ps2'] = $game['line_ou2']; 

				continue;
			}

			if ($game['line_ou1'] < 100)
			{
				$games['has_lines'][$key]['score1'] = (($game['line_ou2'] + $game['line_ou1']) / 2) - $game['line_ou1'];
				$games['has_lines'][$key]['score2'] = ($game['line_ou1'] + $game['line_ou2']) / 2;

				$games['has_lines'][$key]['ps1'] = $game['line_ou1'];
				$games['has_lines'][$key]['ps2'] = $game['line_ou1'] * -1; 
			}			
		}

		# echo '<pre>';
		# var_dump($raw_data);
		# var_dump($num_of_games);
		# var_dump($raw_data2);
		# var_dump($odds);
		# var_dump($games);
		# echo '</pre>'; exit();	

		return $games;
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