<?php
class notes_model extends CI_Model 
{

	public function get_notes($teams_today)
	{
		$file = base_url().'files/notes.txt';
		$contents = file_get_contents($file);

		$notes_for_today = '';

		foreach ($teams_today as $key => $team) 
		{
			$notes_for_today .= preg_replace("/(.*)(\[".$team."\])([^\[]+)(.*)/s", "$2$3", $contents);
		}

		echo '<pre>';
		echo $notes_for_today;
		echo '</pre>';
	}

}