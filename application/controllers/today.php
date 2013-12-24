<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Today extends CI_Controller 
{
	
	public function __construct()
	{
		parent::__construct();

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

		# echo '<pre>';
		# var_dump($data['games']);
		# var_dump($data['teams']);
		# var_dump($data['matchups']);
		# echo '</pre>'; exit();

		$this->load->view('templates/header', $data);
		$this->load->view('today', $data);
		$this->load->view('templates/footer', $data);
	}

}