<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Today extends CI_Controller 
{

	public function index()
	{
		$data['page_type'] = 'Today';
		$data['page_title'] = 'Today - DFS NBA Tools';
		$data['h2_tag'] = 'Today';
		$data['subhead'] = 'DFS NBA Tools';

		$this->load->model('scraping_model');

		$data['games'] = $this->scraping_model->scrape_odds();

		echo '<pre>';
		var_dump($data['games']);
		echo '</pre>'; exit();

		$this->load->view('templates/header', $data);
		$this->load->view('today', $data);
		$this->load->view('templates/footer', $data);
	}

}