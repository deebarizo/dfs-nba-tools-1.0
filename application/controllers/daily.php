<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Daily extends CI_Controller 
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->database();

		$sql = 'SELECT DISTINCT `date` FROM `irlstats` ORDER BY `date` DESC LIMIT 1';
		$s = $this->db->conn_id->prepare($sql);
		$s->execute(); 

		$result = $s->fetchAll(PDO::FETCH_COLUMN);
		$latest_date_in_db = $result[0];

		$date = new DateTime($latest_date_in_db);
		$date->modify('+1 day');

		$this->date = $date->format('Y-m-d');
	}

	public function index()
	{
		$this->get_stats($this->date);
	}

	public function get_team_rotation($team, $date)
	{
		$this->load->model('teams_model');
		$this->teams_model->get_team_rotation($team, $date);		
	}

	public function get_team_dvp($team, $date)
	{
		$this->load->model('teams_model');
		$this->teams_model->get_team_dvp($team, $date);		
	}

	public function get_stats($date)
	{
		$data['page_type'] = 'Daily';
		$data['page_title'] = 'Daily - DFS NBA Tools';
		$data['h2_tag'] = 'Daily';
		$data['subhead'] = 'DFS NBA Tools';

		$this->load->model('scraping_model');
		$data['games'] = $this->scraping_model->scrape_odds($date);

		$this->load->model('teams_model');
		$data['teams'] = $this->teams_model->get_all_teams();

		$this->load->model('matchups_model');
		$data['matchups'] = $this->matchups_model->get_todays_matchups($data['games'], $data['teams']);

		$this->load->model('players_model');
		$data['players'] = $this->players_model->get_todays_players($date);

		foreach ($data['players'] as &$player) 
		{
			foreach ($data['matchups'] as $key => $type) 
			{
				foreach ($type as $row) 
				{
					for ($i = 1; $i <= 2; $i++) 
					{ 
						$modified_team = $this->modify_team_abbr($player['team']);

						if ($modified_team == $row['team_abbr'.$i]) 
						{
							$player['fpts_plus_minus'] = $row['fpts_plus_minus'.$i];

							if ($key == 'has_lines') { $player['line'] = 'Y'; }
							if ($key == 'no_lines') { $player['line'] = 'N'; }

							$player['fppg_2013_la'] = 
								number_format(($player['fppg_2013'] * $row['line_adj'.$i]) + $player['fppg_2013'], 2);

							$player['vr_2013_la'] =
								number_format(($player['vr_2013'] * $row['line_adj'.$i]) + $player['vr_2013'], 2);

							$player['vr_last_15_days_la'] =
								number_format(($player['vr_last_15_days'] * $row['line_adj'.$i]) + $player['vr_last_15_days'], 2);

							$player['fppg_2012_la'] = 
								number_format(($player['fppg_2012'] * $row['line_adj'.$i]) + $player['fppg_2012'], 2);

							$player['vr_2012_la'] =
								number_format(($player['vr_2012'] * $row['line_adj'.$i]) + $player['vr_2012'], 2);

							$player['fppm_2013_la'] =
								number_format($player['fppg_2013_la'] / $player['mpg_2013'], 2);

							if ($player['mpg_last_15_days'] == 0)
							{
								$player['fppg_last_15_days_la'] = 0;
								$player['fppm_last_15_days_la'] = 0;								
							}
							else
							{
								$player['fppg_last_15_days_la'] = 
									number_format(($player['fppg_last_15_days'] * $row['line_adj'.$i]) + $player['fppg_last_15_days'], 2);

								$player['fppm_last_15_days_la'] =
									number_format(($player['fppg_last_15_days'] / $player['mpg_last_15_days'] * $row['line_adj'.$i]) + ($player['fppg_last_15_days'] / $player['mpg_last_15_days']), 2);
							}

							$player['ps'] = $row['ps'.$i] > 0 ? '+'.$row['ps'.$i] : $row['ps'.$i];
					 	
							break;
						}
					}

					if (isset($player['fpts_plus_minus'])) { break; }
				}

				if (isset($player['fpts_plus_minus'])) { break; }
			}
		}

		unset($player);

		foreach ($data['matchups']['has_lines'] as $key => &$game) 
		{ 
			for ($i=1; $i<=2; $i++) 
			{ 
				$game['team_abbr'.$i] = $this->modify_team_abbr_match_ds($game['team_abbr'.$i]);
				$data['teams_today'][] = $game['team_abbr'.$i];
			}
		}

		unset($game);

		sort($data['teams_today']);

		$data['chosen_date'] = $date;

		for ($i = 0; $i <= 4; $i++) 
		{ 
			$date = new DateTime($data['chosen_date']);
			$date->modify('-'.$i.' day');

			$data['dates'][] = $date->format('Y-m-d');
		}

		# echo '<pre>';
		# var_dump($data['games']);
		# var_dump($data['teams']);
		# var_dump($data['matchups']);
		# var_dump($data['players']);
		# var_dump($data['teams_today']);
		# echo '</pre>'; exit();

		$this->load->view('templates/header', $data);
		$this->load->view('daily', $data);
		$this->load->view('templates/footer', $data);
	}

	public function modify_team_abbr($team)
	{
		switch ($team) 
		{
		    case 'PHO':
		        return 'PHX';
		    case 'UTA':
		        return 'UTAH';
		    case 'WAS':
		        return 'WSH';
		}

		return $team;
	}

	public function modify_team_abbr_match_ds($team)
	{
		switch ($team) 
		{
		    case 'PHX':
		        return 'PHO';
		    case 'UTAH':
		        return 'UTA';
		    case 'WSH':
		        return 'WAS';
		}

		return $team;
	}

}