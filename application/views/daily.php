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
			      				<option value="<?php echo base_url().'daily/ds/'.$date; ?>"<?php echo $chosen_date == $date ? ' selected' : ''; ?>><?php echo $date; ?></option>
			      			<?php } ?>
						</select>
					</form>
				</div>
				<div style='display:inline-block'>
					<a class="games-toggle" href="#">Show Games</a>
				</div>
				<div style='display:inline-block'>
					<a class="lineup-toggle" href="#">Show Lineup</a>
				</div>
				<div style='display:inline-block'>
					<a style="margin-left: 20px" target="_blank" href="<?php echo base_url().'daily/notes'; ?>">Go to Notes</a>
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

			      			<?php if (isset($matchups['no_lines'])) { ?>
				      			<?php foreach ($matchups['no_lines'] as $key => $game) { ?>
				      				<button type='button' class="btn game-button show-game" data-toggle="button"><?php echo $game['team_abbr1'].' vs '.$game['team_abbr2'];?></button>
				      			<?php } ?>
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

						<div class='options-inline'>

							<h4>CV and CV FPPM</h4>

			      			<button type='button' class="btn cv2-button show-cv2" data-toggle="button">Hide 40%+</button>

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

								<a target="_blank" class="rotoworld-team-link" href="">Rotoworld</a>
								 | 
								<a target="_blank" class="espn-team-schedule-link" href="">ESPN Schedule</a>

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

      	  <div class="row lineup-section">
	
		  	<div class="span12">

				<div class="widget">

					<div class="widget-header">
						<h3><i class="fa fa-th-list"></i> Lineup</h3>
					</div> <!-- /widget-header -->
					
					<div class="widget-content">

						<div style='display:inline-block'>

							<table class='inside-box lineup'>
								<thead>
								    <tr>
								        <th colspan="4">Forwards</th>
								        <th colspan="4">Guards</th>
								        <th colspan="4">Center and Utility</th>
								    </tr>
								</thead>
								<tbody>
								    <tr>
								    	<td class="lineup-position">F</td>
								        <td class="lineup-player lineup-forward"></td>
								        <td class="lineup-salary"></td>
								        <td class="lineup-fpts"></td>
								        <td class="lineup-position">G</td>
								        <td class="lineup-player lineup-guard"></td>
								        <td class="lineup-salary"></td>
								        <td class="lineup-fpts"></td>
								        <td class="lineup-position">C</td>
								        <td class="lineup-player lineup-center"></td>
								        <td class="lineup-salary"></td>
								        <td class="lineup-fpts"></td>
								    </tr>
								    <tr>
								    	<td class="lineup-position">F</td>
								        <td class="lineup-player lineup-forward"></td>
								        <td class="lineup-salary"></td>
								        <td class="lineup-fpts"></td>
								        <td class="lineup-position">G</td>
								        <td class="lineup-player lineup-guard"></td>
								        <td class="lineup-salary"></td>
								        <td class="lineup-fpts"></td>
								        <td class="lineup-position">U</td>
								        <td class="lineup-player lineup-utility"></td>
								        <td class="lineup-salary"></td>
								        <td class="lineup-fpts"></td>
								    </tr>
								    <tr>
								    	<td class="lineup-position">F</td>
								        <td class="lineup-player lineup-forward"></td>
								        <td class="lineup-salary"></td>
								        <td class="lineup-fpts"></td>
								        <td class="lineup-position">G</td>
								        <td class="lineup-player lineup-guard"></td>
								        <td class="lineup-salary"></td>
								        <td class="lineup-fpts"></td>
								        <td colspan="4"></td>
								    </tr>
								</tbody>
							</table>

						</div>

						<div style='display:inline-block'>

							<table class="inside-box lineup-tracker">
							    <tr>
							        <td>Total Salary</td>
							        <td class="lineup-tracker-data lineup-total-salary">0</td>
							    </tr>
							    <tr>
							        <td>Over/Under ($100K)</td>
							        <td class="lineup-tracker-data lineup-ou">100000</td>
							    </tr>
							    <tr>
							        <td>Per player $ left</td>
							        <td class="lineup-tracker-data lineup-per-player-left">12500</td>
							    </tr>
							    <tr>
							        <td>Total FPTS</td>
							        <td class="lineup-tracker-data lineup-total-fpts">0.00</td>
							    </tr>
							</table>

						</div>

						<button class="btn btn-primary save-lineup" type="button">Save</button>

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
					
					<div class="widget-content table-scroll">

						<table id='daily-stats' class="table table-bordered daily-stats">
							
							<thead>
								<tr>
									<th data-sort='string'>Name</th>
									<th data-sort='string'>Pos</th>
									<th data-sort='int'>Salary</th>
									<th data-sort='float'>LA VR</th>
									<th data-sort='float'>CV</th>
									<th data-sort='float'>CV FPPM</th>
									<th data-sort='string'>Opp</th>
									<th data-sort='float'>PS</th>
									<th data-sort='string'>Line</th>
									
									<?php if (isset($players[1]['actual_min'])) { ?>

									<th data-sort='int'>A MIN</th>
									<th data-sort='float'>A FPTS</th>
									<th data-sort='float'>A FPPM</th>
									<th data-sort='float'>A VR</th>

									<?php } ?>
									
									<th data-sort='float'>LA (%)</th>

									<th data-sort='int'>2013 GP</th>
									<th data-sort='float'>MPG</th>
									<th data-sort='float'>LA FPPG</th>
									<th data-sort='float'>LA FPPM</th>
									<th data-sort='float'>CV</th>
									<th data-sort='float'>CV FPPM</th>
									<th data-sort='float'>LA VR</th>

									<th data-sort='float'>MPG CH</th>
									<th data-sort='int'>L15 GP</th>
									<th data-sort='float'>MPG</th>
									<th data-sort='float'>LA FPPG</th>
									<th data-sort='float'>LA FPPM</th>
									<th data-sort='float'>LA VR</th>
								</tr>
							</thead>

							<tbody>
								
									<?php 



									foreach ($players as $player) 
									{
										if (isset($player['actual_min'])) 
										{ 
											$actual_fpts = ' data-actual-fpts="'.$player['actual_fpts'].'"'; 
										} 
										else 
										{
											$actual_fpts = '';
										}

										echo '<tr class="'.$player['team'].' position-'.$player['position'].' show-row row-info" data-salary="'.$player['salary'].'"' .'data-cv="'.$player['cv_2013'].'"' .'data-cv-fppm="'.$player['cv_fppm_ds_2013'].'"'.$actual_fpts.'>';

											echo '<td class="player"><a target="_blank" href="'.base_url().'players/game_log/'.$player['url_segment'].'">'.$player['name'].'</a> (<a target="_blank" href="http://espn.go.com/nba/teams/schedule?team='.$player['team'].'">'.$player['team'].'</a>) <i class="fa fa-plus-square"></i></td>';
											echo '<td>'.$player['position'].'</td>';
											echo '<td>'.$player['salary'].'</td>';
											echo '<td>'.$player['vr_2013_la'].'</td>';
											echo '<td>'.$player['cv_2013'].'</td>';
											echo '<td>'.$player['cv_fppm_ds_2013'].'</td>';
											echo '<td><a class="dvp-link" target="_blank" href= "'.base_url().'teams/overview/'.str_replace('@', '', $player['opponent']).'" data-team-dvp-url="'.base_url().'daily/get_team_dvp/'.str_replace('@', '', $player['opponent']).'/'.$chosen_date.'/dvp-link'.'">'.$player['opponent'].'</a></td>';
											echo '<td>'.$player['ps'].'</td>';
											echo '<td>'.$player['line'].'</td>';

											if (isset($player['actual_min'])) 
											{ 

											echo '<td>'.$player['actual_min'].'</td>';
											echo '<td>'.$player['actual_fpts'].'</td>';
											echo '<td>'.$player['actual_fppm'].'</td>';
											echo '<td class="actual-stats-column">'.$player['actual_vr'].'</td>';

											}

											echo '<td>'.$player['line_adj'].'</td>';
				
											echo '<td>'.$player['gp_2013'].'</td>';
											echo '<td>'.$player['mpg_2013'].'</td>';
											echo '<td>'.$player['fppg_2013_la'].'</td>';
											echo '<td>'.$player['fppm_2013_la'].'</td>';
											echo '<td>'.$player['cv_2013'].'</td>';
											echo '<td>'.$player['cv_fppm_ds_2013'].'</td>';
											echo '<td class="vr-column">'.$player['vr_2013_la'].'</td>';

											echo '<td>'.$player['mpg_ch'].'</td>';
											echo '<td>'.$player['gp_last_15_days'].'</td>';
											echo '<td>'.$player['mpg_last_15_days'].'</td>';
											echo '<td>'.$player['fppg_last_15_days_la'].'</td>';
											echo '<td>'.$player['fppm_last_15_days_la'].'</td>';
											echo '<td class="vr-column">'.$player['vr_last_15_days_la'].'</td>';

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

