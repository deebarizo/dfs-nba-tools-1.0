<div class="main">
	
	<div class="main-inner">

	    <div class="container">

      	  <div class="row">

      	  	<div class="span12">
	      	  	<h3><?php echo $h2_tag; ?></h3>
	      	</div>

      	  </div>

      	  <div class="row">

		  	<div class="span12">

				<div class="widget widget-table">

					<div class="widget-header">
						<h3><i class="fa fa-user"></i> <?php echo $game_log[0]['name'].' ('.trim($game_log[0]['position']).')'; ?></h3>
					</div> <!-- /widget-header -->
					
					<div class="widget-content">

					  	<div class="player-fpts-distribution">
					  	</div>

					</div>

				</div>

			</div>
	
		  	<div class="span12">

				<div class="widget widget-table">

					<div class="widget-header">
						<h3><i class="fa fa-user"></i> <?php echo $game_log[0]['name'].' ('.trim($game_log[0]['position']).')'; ?></h3>
					</div> <!-- /widget-header -->
					
					<div class="widget-content">

						<table class="table table-bordered daily-stats">
							
							<thead>
								<tr>
									<th data-sort='string'>DATE</th>
									<th data-sort='string'>OPP</th>
									<th data-sort='string'>SCORE</th>

									<th data-sort='string'>START</th>
									<th data-sort='float'>MIN</th>
									<th data-sort='string'>FGM-FGA</th>
									<th data-sort='string'>3PM-3PA</th>
									<th data-sort='string'>FTM-FTA</th>
									
									<th data-sort='int'>OREB</th>
									<th data-sort='int'>DREB</th>
									<th data-sort='int'>TREB</th>
									<th data-sort='int'>AST</th>
									<th data-sort='int'>BLK</th>
									<th data-sort='int'>STL</th>
									<th data-sort='int'>PF</th>
									<th data-sort='int'>TO</th>
									<th data-sort='int'>PTS</th>

									<th data-sort='int'>FPTS</th>
								</tr>
							</thead>

							<tbody>
								
									<?php 

									foreach ($game_log as $value) 
									{
										echo '<tr>';
											echo '<td>'.$value['date'].'</td>';
											
											$opponent = ($value['opponent'] == $value['team2'] ? '@'.$value['opponent'] : $value['opponent']);

											echo '<td>'.$opponent.'</td>';

											echo '<td><a target="_blank" href="'.$value['url_espn'].'">';

											if ($value['team'] == $value['team1'])
											{
												if ($value['score1'] > $value['score2'])
												{
													echo '<span style="color: green">W</span> '.$value['score1'].'-'.$value['score2'];
												}
												else
												{
													echo '<span style="color: red">L</span> '.$value['score1'].'-'.$value['score2'];
												}
											}
											else
											{
												if ($value['score1'] < $value['score2'])
												{
													echo '<span style="color: green">W</span> '.$value['score2'].'-'.$value['score1'];
												}
												else
												{
													echo '<span style="color: red">L</span> '.$value['score2'].'-'.$value['score1'];
												}												
											}

											echo '</a> | <a target="_blank" href="'.$value['pm_link'].'">PM</a></td>';

											echo '<td>'.ucfirst($value['starter']).'</td>';

											if ($value['played'] == 'yes')
											{
												echo '<td>'.$value['minutes'].'</td>';
												echo '<td>'.$value['fgm'].'-'.$value['fga'].'</td>';
												echo '<td>'.$value['threepm'].'-'.$value['threepa'].'</td>';
												echo '<td>'.$value['ftm'].'-'.$value['fta'].'</td>';

												echo '<td>'.$value['oreb'].'</td>';
												echo '<td>'.$value['dreb'].'</td>';
												echo '<td>'.$value['reb'].'</td>';
												echo '<td>'.$value['ast'].'</td>';
												echo '<td>'.$value['blk'].'</td>';
												echo '<td>'.$value['stl'].'</td>';
												echo '<td>'.$value['pfouls'].'</td>';
												echo '<td>'.$value['turnovers'].'</td>';
												echo '<td>'.$value['pts'].'</td>';

												echo '<td>'.$value['fpts_ds'].'</td>';	
											}
											else
											{
												echo '<td colspan="13" style="text-align: center;">'.$value['played'].'</td>';

												echo '<td>0.00</td>';	
											}

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

