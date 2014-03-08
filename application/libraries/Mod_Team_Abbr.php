<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class Mod_Team_Abbr
{

	public function mod_daily_abbr($team)
	{
		switch ($team) 
		{
		    case 'PHO':
		        return 'PHX';
		    case 'UTA':
		        return 'UTAH';
		    case 'WAS':
		        return 'WSH';
		}

		return $team;
	}

}