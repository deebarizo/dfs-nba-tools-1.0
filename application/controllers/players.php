<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Players extends CI_Controller 
{

	public function game_log($player)
	{
		$data['page_type'] = 'Players';
		$data['page_title'] = 'Players - DFS NBA Tools';
		$data['h2_tag'] = 'Game Log';

		$this->load->model('players_model');
		$data['game_log'] = $this->players_model->get_game_log($player);

		# echo '<pre>';
		# var_dump($data['game_log']);
		# echo '</pre>'; exit();

		$this->load->view('templates/header', $data);
		$this->load->view('game_log', $data);
		$this->load->view('templates/footer', $data);
	}

}