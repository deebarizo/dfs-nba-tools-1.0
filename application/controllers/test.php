<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Test extends CI_Controller 
{

	public function index()
	{
		$this->load->database();

		ini_set('max_execution_time', 10800); // 10800 seconds = 3 hours

		$date_range = $this->create_date_range_array('2013-10-29', '2013-12-21'); 

		foreach ($date_range as $key => $date) 
		{
			$sql = 'SELECT `team1`, `score1`, `team2`, `score2`, `date` FROM `games` WHERE `date` = :date';
			$s = $this->db->conn_id->prepare($sql);
			$s->bindValue(':date', $date);
			$s->execute(); 

			$result = $s->fetchAll(PDO::FETCH_ASSOC);
			if (empty($result) === false)
			{
				$schedule[$date] = $result;
			}
		}

		foreach ($schedule as &$games) 
		{
			foreach ($games as &$game) 
			{
				for ($i = 1; $i <= 2; $i++) 
				{ 
					$sql = 'SELECT SUM(`fpts_ds`) FROM `irlstats` 
							WHERE `team` = :team AND `date` = :date GROUP BY team';
					$s = $this->db->conn_id->prepare($sql);
					$s->bindValue(':team', $game['team'.$i]);
					$s->bindValue(':date', $game['date']);
					$s->execute(); 

					$result = $s->fetchAll(PDO::FETCH_COLUMN, 0);
					$game['fpts'.$i] = $result[0];	

					$game['ratio'.$i] = $game['fpts'.$i] / $game['score'.$i];
				}
			}

			unset($game);				
		}

		unset($games);

		// calculate standard deviation and coefficient of variation

		$stats['count'] = 0;

		$stats['ratios']['sum'] = 0;

		foreach ($schedule as $games) 
		{
			foreach ($games as $game) 
			{
				$stats['count'] += 2;

				$stats['ratios']['sum'] += $game['ratio1'];
				$stats['ratios']['sum'] += $game['ratio2'];
			}
		}

		$stats['ratios']['mean'] = $stats['ratios']['sum'] / $stats['count'];

		$diff_squared = 0;

		foreach ($schedule as &$games) 
		{
			foreach ($games as &$game) 
			{
				$diff_squared += pow($game['ratio1'] - $stats['ratios']['mean'], 2); 
				$diff_squared += pow($game['ratio2'] - $stats['ratios']['mean'], 2); 
			}

			unset($game);				
		}

		unset($games);

		$variance = $diff_squared / ($stats['count'] - 1);
		$stats['stdev'] = sqrt($variance);

		$stats['cv'] = $stats['stdev'] / $stats['ratios']['mean'];

		// calculate correlation

		$stats['pts']['sum'] = 0;
		$stats['fpts']['sum'] = 0;

		foreach ($schedule as $games) 
		{
			foreach ($games as $game) 
			{
				$stats['pts']['sum'] += $game['score1'];
				$stats['pts']['sum'] += $game['score2'];

				$stats['fpts']['sum'] += $game['fpts1'];
				$stats['fpts']['sum'] += $game['fpts2'];
			}
		}

		$stats['pts']['mean'] = $stats['pts']['sum'] / $stats['count'];	

		$stats['fpts']['mean'] = $stats['fpts']['sum'] / $stats['count'];	

		echo '<pre>'; var_dump($stats); echo '</pre>'; exit();
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