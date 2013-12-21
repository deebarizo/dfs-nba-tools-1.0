<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Irlstats extends CI_Controller 
{

	public function index()
	{
		$date_range = $this->create_date_range_array('2013-10-29', '2013-10-29'); 
		// when ready to insert, change last date to 2013-12-19

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
					$data_to_insert[$date_segment]['games'][$n]['url'] = 'http://scores.espn.go.com'.$url_segment;
				}
			}
			else
			{
				$data_to_insert[$date_segment]['num_of_games'] = $num_urls;
			}
		}

		foreach ($data_to_insert as $key => &$date) 
		{
			if ($date['num_of_games'] > 0)
			{
				foreach ($date as $key2 => &$games) 
				{
					if ($key2 === 'games')
					{
						foreach ($games as $key3 => &$game) 
						{
							$html = phpQuery::newDocumentFileHTML($game['url']);

							$game['team1'] = $html->find('tr[class=periods]')->next()->find('td[class=team]')->text();
							$game['team2'] = $html->find('tr[class=periods]')->next()->next()->find('td[class=team]')->text();

							$game['score1'] = $html->find('td[class=ts]:eq(0)')->text();
							$game['score2'] = $html->find('td[class=ts]:eq(1)')->text();

							$count = $html->find('table[class=mod-data] td');
							$num_td = count($count);

							for ($n = 0; $n < $num_td; $n++)
							{
								$stats[$n] = $html->find('table[class=mod-data] td:eq('.$n.')')->text();
							}

							foreach ($stats as $key => &$stat) 
							{
								// get rid of weird "Â" character
								// http://stackoverflow.com/questions/14881286/ignore-if-there-is-url
								// http://www.stemkoski.com/php-remove-non-ascii-characters-from-a-string/
								$stat = preg_replace('/[^(\x20-\x7F)]*/', "", $stat);
							}

							unset($stat);

							foreach ($stats as $key => $stat) 
							{
								if (substr($stat, 0, 17) === 'Fast break points')
								{
									$key_to_halve_array = $key;
									break;
								}
							}

							for ($n = 0; $n < 5; $n++)
							{
								$starters_stats['team1'][] = array_slice($stats, $n*15, 15);
							}

							foreach ($starters_stats as &$starter) 
							{
								foreach ($starter as &$row) 
								{
									$row['name'] = preg_replace('/(.*),(.*)/', '$1', $row[0]);
									$row['position'] = preg_replace('/(.*),(.*)/', '$2', $row[0]);

									unset($row[0]);	
								}
							}

							unset($row);

							echo '<pre>'; var_dump($starters_stats); echo '</pre>'; exit();
						}
					}
				}
			}
		}

		unset($date);
		unset($games);
		unset($game);

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