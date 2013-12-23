<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Today extends CI_Controller 
{
	
	public function __construct()
	{
		parent::__construct();

		date_default_timezone_set('America/Chicago');

		$today = date('Y-m-d');

		if (time() < strtotime($today.'6:00PM'))
		{
			$this->date = $today;
		}

		if (time() > strtotime($today.'6:00PM') AND time() < strtotime($today.'11:59PM'))
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

		$this->load->model('team_model');

		$data['teams'] = $this->team_model->get_all_teams();

		echo '<pre>';
		# var_dump($data['games']);
		var_dump($data['teams']);
		echo '</pre>'; exit();

		$this->load->view('templates/header', $data);
		$this->load->view('today', $data);
		$this->load->view('templates/footer', $data);
	}

}