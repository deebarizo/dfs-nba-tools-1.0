<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Irlstats extends CI_Controller 
{

	public function index()
	{
		$date_range = $this->create_date_range_array('2013-10-29', '2013-12-19');

		// echo '<pre>'; var_dump($date_range); echo '</pre>'; exit();

		$this->load->helper('phpquery');

		foreach ($date_range as $key => $date) 
		{
			$date_segment = preg_replace("/\-/", "", $date);

			$html = phpQuery::newDocumentFileHTML('http://scores.espn.go.com/nba/scoreboard?date='.$date_segment);

			$count = $html->find('.expand-gameLinks');
			$num_urls = count($count);

			if ($num_urls > 0)
			{
				$data_to_insert[$date_segment]['num_of_games'] = $num_urls;

				for ($n=0; $n < $num_urls; $n++)
				{
					$url_segment = $html->find('div[class=expand-gameLinks]:eq('.$n.') a:first')->attr('href');
					$data_to_insert[$date_segment]['games'][$n] = 'http://scores.espn.go.com'.$url_segment;
				}
			}
			else
			{
				$data_to_insert[$date_segment]['num_of_games'] = $num_urls;
			}
		}

		foreach ($data_to_insert as $key => $date) 
		{
			if ($date['num_of_games'] > 0)
			{
				echo $key.'<br>';

				foreach ($date as $key2 => $games) 
				{
					if ($key2 === 'games')
					{
						foreach ($games as $game_url) 
						{
							echo $game_url.'<br>';
						}
					}
				}
			}
		}

		echo '<pre>'; var_dump($data_to_insert); echo '</pre>'; exit();
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