<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Evaluation extends CI_Controller 
{

	public function __construct()
	{
		parent::__construct();

		$this->load->database();
		$this->load->model('evaluation_model');

		date_default_timezone_set('America/Chicago');
		
		$this->today_date = date('Y-m-d');
	}

	public function stats($stat)
	{
		$data['page_type'] = 'Evaluation';
		$data['page_title'] = 'Evaluation - DFS NBA Tools';
		$data['h2_tag'] = 'Evaluation';
		$data['subhead'] = 'DFS NBA Tools';

		if ($stat == 'teams')
		{
			$this->load->library('Calculations');
			$this->evaluation_model->get_team_stats($this->today_date);
		}
	}

}