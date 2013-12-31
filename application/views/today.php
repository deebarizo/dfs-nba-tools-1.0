<div class="main">
	
	<div class="main-inner">

	    <div class="container">

      	  <div class="row">

      	  	<div class="span12">
	      	  	<h2><?php echo $h2_tag; ?></h2>
	      	  	<p>Advanced DFS Stats</p>
	      	</div>

      	  </div>

      	  <div class="row">
	
		  	<div class="span12">

				<div class="widget">

					<div class="widget-header">
						<h3><i class="fa fa-th-list"></i> Options</h3>
					</div> <!-- /widget-header -->
					
					<div class="widget-content">

			      		<div class="game-buttons">

			      			<?php foreach ($matchups['has_lines'] as $key => $game) { ?>

			      				<button type='button' class="btn game-button" data-toggle="button" name='' id=''><?php echo $game['team_abbr1'].' vs '.$game['team_abbr2'];?></button>
			      				
			      			<?php } ?>
			      			
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

						<table id='daily-stats' class="table table-bordered table-striped">
							
							<thead>
								<tr>
									<th data-sort='string'>Name</th>
									<th data-sort='string'>Opp</th>
									<th data-sort='string'>Line</th>
									<th data-sort='float'>FPTS +/-</th>
									<th data-sort='int'>Salary</th>

									<th data-sort='int'>GP</th>
									<th data-sort='float'>MPG</th>
									<th data-sort='float'>LA FPPG</th>
									<th data-sort='float'>CV</th>
									<th data-sort='float'>LA VR</th>

									<th data-sort='int'>Last 15 GP</th>
									<th data-sort='float'>MPG</th>
									<th data-sort='float'>FPPG</th>
									<th data-sort='float'>CV</th>
									<th data-sort='float'>VR</th>
									<!--
									<th data-sort='int'>Last Game MIN</th>
									<th data-sort='float'>FPTS</th>
									<th data-sort='float'>VR</th>
									-->
								</tr>
							</thead>

							<tbody>
								
									<?php 

									foreach ($players as $player) 
									{
										echo '<tr class="'.$player['team'].' position-'.$player['position'].'">';

											echo '<td>'.$player['name_team_position'].'</td>';
											echo '<td>'.$player['opponent'].'</td>';
											echo '<td>'.$player['line'].'</td>';
											echo '<td>'.$player['fpts_plus_minus'].'</td>';
											echo '<td>'.$player['salary'].'</td>';

											echo '<td>'.$player['gp_2013'].'</td>';
											echo '<td>'.$player['mpg_2013'].'</td>';
											echo '<td>'.$player['fppg_2013_la'].'</td>';
											echo '<td>'.$player['cv_2013'].'</td>';
											echo '<td>'.$player['vr_2013_la'].'</td>';

											echo '<td>'.$player['gp_last_15_days'].'</td>';
											echo '<td>'.$player['mpg_last_15_days'].'</td>';
											echo '<td>'.$player['fppg_last_15_days'].'</td>';
											echo '<td>'.$player['cv_last_15_days'].'</td>';
											echo '<td>'.$player['vr_last_15_days'].'</td>';

											# echo '<td>'.$player['mpg_last_game'].'</td>';
											# echo '<td>'.$player['fppg_last_game'].'</td>';
											# echo '<td>'.$player['vr_last_game'].'</td>';

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

