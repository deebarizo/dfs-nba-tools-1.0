<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Teams extends CI_Controller 
{

	public function overview($team)
	{
		$data['page_type'] = 'Teams';
		$data['page_title'] = $team.' - Teams - DFS NBA Tools';
		$data['h2_tag'] = 'Team Overview';

		$this->load->model('teams_model');
		$data['overview'] = $this->teams_model->get_overview($team);

		# echo '<pre>';
		# var_dump($data['game_log']);
		# echo '</pre>'; exit();

		$this->load->view('templates/header', $data);
		$this->load->view('overview', $data);
		$this->load->view('templates/footer_teams', $data);
	}

}