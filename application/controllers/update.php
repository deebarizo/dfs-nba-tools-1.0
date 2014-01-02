<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Update extends CI_Controller 
{

	public function __construct()
	{
		parent::__construct();

		date_default_timezone_set('America/Chicago');

		$this->today_date = date('Y-m-d');
	}

	public function index()
	{
		$data['page_type'] = 'Update';
		$data['page_title'] = 'Update - DFS NBA Tools';
		$data['h2_tag'] = 'Update - DFS NBA Tools';	

		$data['today_date'] = $this->today_date;

		$this->load->helper('form');
		$this->load->library('form_validation');

		$this->form_validation->set_rules('date', 'Date', 'required|trim');	
			
		$this->form_validation->set_error_delimiters('<br /><span style="color:red" class="error">', '</span>');

		if ($this->form_validation->run() == FALSE) // validation hasn't been passed
		{
			$data['success'] = false;
		}
		else
		{
			echo 'scraping...'; exit();

			$form_data = array(
							'date' => set_value('date')
						);

			$data['success'] = $this->scraping_model->scrape_irlstats($form_data);
		}

		$this->load->view('templates/header', $data);
		$this->load->view('update', $data);
		$this->load->view('templates/footer');
	}

	function create_date_range_array($strDateFrom,$strDateTo)
	{
	    // takes two dates formatted as YYYY-MM-DD and creates an
	    // inclusive array of the dates between the from and to dates.

	    // could test validity of dates here but I'm already doing
	    // that in the main script

	    $aryRange=array();

	    $iDateFrom=mktime(1,0,0,substr($strDateFrom,5,2),     substr($strDateFrom,8,2),substr($strDateFrom,0,4));
	    $iDateTo=mktime(1,0,0,substr($strDateTo,5,2),     substr($strDateTo,8,2),substr($strDateTo,0,4));

	    if ($iDateTo>=$iDateFrom)
	    {
	        array_push($aryRange,date('Y-m-d',$iDateFrom)); // first entry
	        while ($iDateFrom<$iDateTo)
	        {
	            $iDateFrom+=86400; // add 24 hours
	            array_push($aryRange,date('Y-m-d',$iDateFrom));
	        }
	    }

	    return $aryRange;
	}

}