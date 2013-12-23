<?php
class scraping_model extends CI_Model 
{

	public function scrape_odds()
	{
		ini_set('max_execution_time', 10800); // 10800 seconds = 3 hours

		date_default_timezone_set('America/Chicago');

		$today = date('Y-m-d');

		if (time() < strtotime($today.'6:00PM'))
		{
			$date = $today;
		}

		if (time() > strtotime($today.'6:00PM') AND time() < strtotime($today.'11:59PM'))
		{
			$date = date('Y-m-d',strtotime("1 days"));
		}

		$url_segment = preg_replace('/(\d\d\d\d)-(\d\d)-(\d\d)/', '$1$2$3', $date);

		$this->load->helper('phpquery');

		$html = phpQuery::newDocumentFileHTML('http://www.scoresandodds.com/grid_'.$url_segment.'.html');

		$raw_data = $html->find('div[id=nba]')->next()->find('table[class=data]')->find('tr[class=team odd]');
		$num_of_games = count($raw_data);

		for ($n=0; $n < $num_of_games; $n++)
		{
			$raw_data2['odd']['team'][] = $html->find('div[id=nba]')->next()->
									find('table[class=data]')->find('tr[class=team odd]')->
									find('td[class=name]:eq('.$n.')')->text();

			$raw_data2['odd']['line_ou'][] = $html->find('div[id=nba]')->next()->
									find('table[class=data]')->find('tr[class=team odd]')->
									find('td[class=currentline ]:eq('.$n.')')->text();

			$raw_data2['even']['team'][] = $html->find('div[id=nba]')->next()->
									find('table[class=data]')->find('tr[class=team even]')->
									find('td[class=name]:eq('.$n.')')->text();


			$raw_data2['even']['line_ou'][] = $html->find('div[id=nba]')->next()->
									find('table[class=data]')->find('tr[class=team even]')->
									find('td[class=currentline ]:eq('.$n.')')->text();
		}

		// clean up results

		foreach ($raw_data2 as $key => &$odd_even) 
		{
			foreach ($odd_even as $key2 => &$row) 
			{
				foreach ($row as &$value) 
				{
					if ($key2 == 'team')
					{
						$value = preg_replace('/\d+ /', '', $value);
					}

					if ($key2 == 'line_ou')
					{
						$value = trim($value);
						$value = preg_replace('/PK/', '0', $value);
						$value = preg_replace('/[a-zA-Z].*/', '', $value);
					}
				}

				unset($value);
			}

			unset($row);
		}

		unset($odd_even);	

		// generate relevant data

		foreach ($raw_data2 as $key => &$odd_even) 
		{
			foreach ($odd_even as $key2 => &$row) 
			{
				foreach ($row as $key3 => &$value) 
				{
					if ($key == 'odd' AND $key2 == 'team')
					{
						$odds[$key3]['team1'] = $value;
					}

					if ($key == 'even' AND $key2 == 'team')
					{
						$odds[$key3]['team2'] = $value;
					}

					if ($key == 'odd' AND $key2 == 'line_ou')
					{
						$odds[$key3]['line_ou1'] = $value;
					}

					if ($key == 'even' AND $key2 == 'line_ou')
					{
						$odds[$key3]['line_ou2'] = $value;
					}
				}
			}
		}

		foreach ($odds as $key => $game) 
		{
			if ($game['line_ou1'] == '' AND $game['line_ou2'] == '')
			{
				$games['no_lines'][$key]['team1'] = $game['team1']; 
				$games['no_lines'][$key]['team2'] = $game['team2']; 

				continue;
			}

			$games['has_lines'][$key]['team1'] = $game['team1']; 
			$games['has_lines'][$key]['team2'] = $game['team2']; 

			if ($game['line_ou1'] > 100)
			{
				$games['has_lines'][$key]['score1'] = ($game['line_ou1'] + $game['line_ou2']) / 2;
				$games['has_lines'][$key]['score2'] = $games['has_lines'][$key]['score1'] - $game['line_ou2'];

				continue;
			}

			if ($game['line_ou1'] < 100)
			{
				$games['has_lines'][$key]['score1'] = (($game['line_ou2'] + $game['line_ou1']) / 2) - $game['line_ou1'];
				$games['has_lines'][$key]['score2'] = ($game['line_ou1'] + $game['line_ou2']) / 2;
			}			
		}

		# echo '<pre>';
		# var_dump($raw_data);
		# var_dump($num_of_games);
		# var_dump($raw_data2);
		# var_dump($odds);
		# var_dump($games);
		# echo '</pre>'; exit();	

		return $games;
	}

}