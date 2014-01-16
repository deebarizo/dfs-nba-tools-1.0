<div class="main">
	
	<div class="main-inner">

	    <div class="container">

      	  <div class="row">

      	  	<div class="span12">
	      	  	<h3><?php echo $h2_tag; ?></h3>
				<div style='display:inline-block'><strong>Date:</strong></div>
				<div style='display:inline-block'>
					<form action="">
						<select class="date-drop-down" name="date-drop-down">
			      			<?php foreach ($dates as $key => $date) { ?>
			      				<option value="<?php echo base_url().$date; ?>"<?php echo $chosen_date == $date ? ' selected' : ''; ?>><?php echo $date; ?></option>
			      			<?php } ?>
						</select>
					</form>
				</div>
	      	</div>

      	  </div>

      	  <div class="row">
	
		  	<div class="span12">

				<div class="widget">

					<div class="widget-header">
						<h3><i class="fa fa-th-list"></i> Options</h3>
					</div> <!-- /widget-header -->
					
					<div class="widget-content">

			      		<div class='game-buttons'>

			      			<h4>Games</h4>

			      			<?php foreach ($matchups['has_lines'] as $key => $game) { ?>
			      				<button type='button' class="btn game-button show-game" data-toggle="button"><?php echo $game['team_abbr1'].' vs '.$game['team_abbr2'];?></button>
			      			<?php } ?>
			      			
			      		</div>

			      		<div class='options-inline'>

			      			<h4>Position</h4>

							<form action="">
								<select class="position-drop-down" name="position-drop-down">
									<option value="all" selected>All</option>
									<option value="forward">F</option>
									<option value="guard">G</option>
									<option value="center">C</option>
								</select>
							</form>

			      		</div>

						<div class='options-inline'>

							<h4>Salary</h4>

							<form action="">
								<input class="salary-input" type="number" placeholder="0">
								<label class="radio inline">
									<input type="radio" name="salary-toggle" id="greater-than" value="greater-than" checked>
									&gt;=
								</label>
								<label class="radio inline">
									<input type="radio" name="salary-toggle" id="less-than" value="less-than">
									&lt;=
								</label>
								<input type="button" name="salary-reset" class="salary-reset" value="Salary Reset">
							</form>

						</div>

						<div>

							<h4>Team</h4>

							<form action="">
								<select class="team-drop-down" name="team-drop-down">
									<option value="all" selected>All</option>

									<?php foreach ($teams_today as $key => $team) { ?>
									<option value="<?php echo $team; ?>"><?php echo $team; ?></option>
									<?php } ?>
								</select>
							</form>

						</div>

						<div class="team-info">

							<div class="team-links">

								<div class="dvp-team-drop-down">
								</div>

								<a target="_blank" class="rotoworld-team-link" href="http://www.rotoworld.com/teams/clubhouse/nba/DEN">Rotoworld</a>
								 | 
								<a target="_blank" class="espn-team-schedule-link" href="http://espn.go.com/nba/teams/schedule?team=DEN">ESPN Schedule</a>

								<br>
								<a class="rotations-toggle" href="#">Show Rotations</a>

							</div>

							<div class="chosen-team-rotation">

								<form action="">
									<label class="radio inline">
										<input type="radio" name="starters-toggle" id="only-starters" value="only-starters" checked>
										Only Starters
									</label>
									<label class="radio inline">
										<input type="radio" name="starters-toggle" id="only-bench" value="only-bench">
										Only Bench
									</label>
									<label class="radio inline">
										<input type="radio" name="starters-toggle" id="starters-and-bench" value="starters-and-bench">
										Starters and Bench
									</label>
								</form>

					  			<div class="team-rotation-line-chart">
					  			</div>

					  		</div>

					  	</div>

					</div>

				</div>

			</div>

		  </div>

      	  <div class="row">
	
		  	<div class="span12">

				<div class="widget widget-table">

					<div class="widget-header">
						<h3><i class="fa fa-users"></i> Stats</h3>
					</div> <!-- /widget-header -->
					
					<div class="widget-content">

						<table id='daily-stats' class="table table-bordered daily-stats">
							
							<thead>
								<tr>
									<th data-sort='string'>Name</th>
									<th data-sort='string'>Pos</th>
									<th data-sort='int'>Salary</th>
									<th data-sort='float'>LA VR</th>
									<th data-sort='float'>CV</th>
									<th data-sort='string'>Opp</th>
									<th data-sort='float'>PS</th>
									<th data-sort='string'>Line</th>
									
									<?php if (isset($players[1]['actual_min'])) { ?>

									<th data-sort='int'>A MIN</th>
									<th data-sort='float'>A FPTS</th>
									<th data-sort='float'>A VR</th>

									<?php } ?>
									
									<th data-sort='float'>FPTS +/-</th>

									<th data-sort='int'>2013 GP</th>
									<th data-sort='float'>MPG</th>
									<th data-sort='float'>LA FPPG</th>
									<th data-sort='float'>LA FPPM</th>
									<th data-sort='float'>CV</th>
									<th data-sort='float'>LA VR</th>

									<th data-sort='float'>MPG CH</th>
									<th data-sort='int'>L15 GP</th>
									<th data-sort='float'>MPG</th>
									<th data-sort='float'>LA FPPG</th>
									<th data-sort='float'>LA FPPM</th>
									<th data-sort='float'>CV</th>
									<th data-sort='float'>LA VR</th>
									
									<th data-sort='int'>2012 GP</th>
									<th data-sort='float'>LA FPPG</th>
									<th data-sort='float'>LA VR</th>
								</tr>
							</thead>

							<tbody>
								
									<?php 

									foreach ($players as $player) 
									{
										echo '<tr class="'.$player['team'].' position-'.$player['position'].' show-row row-info" data-salary="'.$player['salary'].'">';
											echo '<td><a target="_blank" href="'.base_url().'players/game_log/'.$player['url_segment'].'">'.$player['name'].'</a> (<a target="_blank" href="http://espn.go.com/nba/teams/schedule?team='.$player['team'].'">'.$player['team'].'</a>)</td>';
											echo '<td>'.$player['position'].'</td>';
											echo '<td>'.$player['salary'].'</td>';
											echo '<td>'.$player['vr_2013_la'].'</td>';
											echo '<td>'.$player['cv_2013'].'</td>';
											echo '<td><a class="dvp-link" href="'.base_url().'daily/get_team_dvp/'.str_replace('@', '', $player['opponent']).'/'.$chosen_date.'/dvp-link'.'">'.$player['opponent'].'</a></td>';
											echo '<td>'.$player['ps'].'</td>';
											echo '<td>'.$player['line'].'</td>';

											if (isset($player['actual_min'])) 
											{ 

											echo '<td>'.$player['actual_min'].'</td>';
											echo '<td>'.$player['actual_fpts'].'</td>';
											echo '<td class="actual-stats-column">'.$player['actual_vr'].'</td>';

											}

											echo '<td>'.$player['fpts_plus_minus'].'</td>';
				
											echo '<td>'.$player['gp_2013'].'</td>';
											echo '<td>'.$player['mpg_2013'].'</td>';
											echo '<td>'.$player['fppg_2013_la'].'</td>';
											echo '<td>'.$player['fppm_2013_la'].'</td>';
											echo '<td>'.$player['cv_2013'].'</td>';
											echo '<td class="vr-column">'.$player['vr_2013_la'].'</td>';

											echo '<td>'.$player['mpg_ch'].'</td>';
											echo '<td>'.$player['gp_last_15_days'].'</td>';
											echo '<td>'.$player['mpg_last_15_days'].'</td>';
											echo '<td>'.$player['fppg_last_15_days_la'].'</td>';
											echo '<td>'.$player['fppm_last_15_days_la'].'</td>';
											echo '<td>'.$player['cv_last_15_days'].'</td>';
											echo '<td class="vr-column">'.$player['vr_last_15_days_la'].'</td>';

											echo '<td>'.$player['gp_2012'].'</td>';
											echo '<td>'.$player['fppg_2012_la'].'</td>';
											echo '<td class="vr-column">'.$player['vr_2012_la'].'</td>';
										echo '</tr>';
									} 

									?> 
								
							</tbody>
						
						</table>

					</div>

				</div>

			</div>

		  </div>

	    </div> <!-- /container -->
	    
	</div> <!-- /main-inner -->
    
</div> <!-- /main -->

