<?php
class notes_model extends CI_Model 
{

	public function get_notes()
	{
		$file = base_url().'files/notes.txt';
		$contents = file_get_contents($file);

		echo $contents;
	}

}