<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Update extends CI_Controller 
{

	public function __construct()
	{
		parent::__construct();

		date_default_timezone_set('America/Chicago');

		$this->yesterday_date = date('Y-m-d',strtotime("1 days ago"));
	}

	public function index()
	{
		$data['page_type'] = 'Update';
		$data['page_title'] = 'Update - DFS NBA Tools';
		$data['h2_tag'] = 'Update';	

		$data['yesterday_date'] = $this->yesterday_date;

		$this->load->helper('form');
		$this->load->library('form_validation');

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
			$data['message'] .= '<br>'.$this->scraping_model->scrape_dvp($form_data);
			$data['message'] .= '<br>'.$this->scraping_model->scrape_pace($form_data);
		}

		$this->load->view('templates/header', $data);
		$this->load->view('update', $data);
		$this->load->view('templates/footer');
	}

}