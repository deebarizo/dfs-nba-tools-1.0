<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Today extends CI_Controller 
{
	
	public function __construct()
	{
		parent::__construct();
/*
		date_default_timezone_set('America/Chicago');

		$today = date('Y-m-d');

		if (time() < strtotime($today.'11:58PM'))
		{
			$this->date = $today;
		}

		if (time() >= strtotime($today.'11:58PM') AND time() < strtotime($today.'11:59PM'))
		{
			$this->date = date('Y-m-d',strtotime("1 days"));
		}
		*/

		$this->date = '2013-12-23';
	}

	public function index()
	{
		$data['page_type'] = 'Today';
		$data['page_title'] = 'Today - DFS NBA Tools';
		$data['h2_tag'] = 'Today';
		$data['subhead'] = 'DFS NBA Tools';

		$this->load->model('scraping_model');
		$data['games'] = $this->scraping_model->scrape_odds($this->date);

		$this->load->model('teams_model');
		$data['teams'] = $this->teams_model->get_all_teams();

		$this->load->model('matchups_model');
		$data['matchups'] = $this->matchups_model->get_todays_matchups($data['games'], $data['teams']);

		$this->load->model('players_model');
		$data['players'] = $this->players_model->get_todays_players($this->date);

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
						
							break;
						}
					}

					if (isset($player['fpts_plus_minus'])) { break; }
				}

				if (isset($player['fpts_plus_minus'])) { break; }
			}
		}

		unset($player);

		# echo '<pre>';
		# var_dump($data['games']);
		# var_dump($data['teams']);
		# var_dump($data['matchups']);
		# var_dump($data['players']);
		# echo '</pre>'; exit();

		$this->load->view('templates/header', $data);
		$this->load->view('today', $data);
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
		}

		return $team;
	}

}