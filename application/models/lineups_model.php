<?php
class lineups_model extends CI_Model 
{

	public function save_lineup()
	{
		$test = $this->input->post();

		header('Content-Type: application/json');

		echo json_encode($test['forward1']['name']);
	}

}