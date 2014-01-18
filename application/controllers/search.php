<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Search extends CI_Controller 
{

	public function index()
	{
		$data['page_type'] = 'Search';
		$data['page_title'] = 'Search - DFS NBA Tools';
		$data['h2_tag'] = 'Search';

		$this->load->helper('form');
		$this->load->library('form_validation');

		$this->form_validation->set_rules('player', 'Player', 'required|trim');
		$this->form_validation->set_rules('date', 'Date', 'required|trim');	
			
		$this->form_validation->set_error_delimiters('<br /><span style="color:red" class="error">', '</span>');

		if ($this->form_validation->run() == FALSE) // validation hasn't been passed
		{
			$data['message'] = 'Form validation error.';
		}
		else
		{
			$form_data = array(
							'date' => set_value('date')
						);

			$this->load->model('scraping_model');
			$data['message'] = $this->scraping_model->scrape_irlstats($form_data);
			$data['message'] = $data['message'].'<br>'.$this->scraping_model->scrape_dvp($form_data);
		}

		$this->load->view('templates/header', $data);
		$this->load->view('search', $data);
		$this->load->view('templates/footer', $data);
	}

}