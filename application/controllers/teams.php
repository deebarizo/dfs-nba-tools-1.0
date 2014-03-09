<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Teams extends CI_Controller 
{
	public function __construct()
	{
		parent::__construct();

		$this->load->library('Mod_Team_Abbr');
	}

	public function overview($team)
	{
		$data['page_type'] = 'Teams';
		$data['page_title'] = $team.' - Teams - DFS NBA Tools';
		$data['h2_tag'] = 'Team Overview';

		$this->load->model('teams_model');
		list($data['team_opp_stats'], $data['overview']) = $this->teams_model->get_overview($team);

		# echo '<pre>';
		# var_dump($data['team_opp_stats']);
		# echo '</pre>'; exit();

		$this->load->view('templates/header', $data);
		$this->load->view('overview', $data);
		$this->load->view('templates/footer_teams', $data);
	}

}