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

		  	<div class="span4">

				<div class="widget">

					<div class="widget-header">
						<h3><i class="fa fa-medkit"></i> Player News</h3>
					</div> <!-- /widget-header -->
					
					<div class="widget-content">						

						<p><a target='_blank' href='http://www.rotoworld.com/playernews/nba/basketball-player-news'>Rotoworld.com</a></p>
						
					</div> <!-- /widget-content -->

				</div> <!-- /widget -->

		    </div> <!-- /span -->

		  	<div class="span4">

				<div class="widget widget-table">

					<div class="widget-header">
						<h3><i class="fa fa-crosshairs"></i> Games With Lines</h3>
					</div> <!-- /widget-header -->
					
					<div class="widget-content">

						<table id='simple-table' class="table table-bordered table-striped">
							
							<thead>
								<tr>
									<th data-sort='string'>Team</th>
									<th data-sort='string'>Opponent</th>
									<th data-sort='float'>FPTS +/-</th>
								</tr>
							</thead>

							<tbody>
								
									<?php 

									foreach ($matchups['has_lines'] as $game) 
									{
										for ($i = 1; $i <= 2; $i++) 
										{
											if ($i == 1) { $opp = 2; }
											if ($i == 2) { $opp = 1; }

											echo '<tr>';

											echo '<td>'.$game['team_abbr'.$i].'</td>';
											if ($opp == 2) { $home_game = '@'; } else { $home_game = ''; }
											echo '<td>'.$home_game.$game['team_abbr'.$opp].'</td>';
											echo '<td>'.$game['fpts_plus_minus'.$i].'</td>';

											echo '</tr>';
										}	
									}

									?> 
								
							</tbody>
						
						</table>

					</div>

				</div>

			</div>

		  	<div class="span4">

				<div class="widget widget-table">

					<div class="widget-header">
						<h3><i class="fa fa-crosshairs"></i> Games Without Lines</h3>
					</div> <!-- /widget-header -->
					
					<div class="widget-content">

						<table id='simple-table2' class="table table-bordered table-striped">
							
							<thead>
								<tr>
									<th data-sort='string'>Team</th>
									<th data-sort='string'>Opponent</th>
									<th data-sort='float'>FPTS +/-</th>
								</tr>
							</thead>

							<tbody>
								
									<?php 

									foreach ($matchups['no_lines'] as $game) 
									{
										for ($i = 1; $i <= 2; $i++) 
										{
											if ($i == 1) { $opp = 2; }
											if ($i == 2) { $opp = 1; }

											echo '<tr>';

											echo '<td>'.$game['team_abbr'.$i].'</td>';
											if ($opp == 2) { $home_game = '@'; } else { $home_game = ''; }
											echo '<td>'.$home_game.$game['team_abbr'.$opp].'</td>';
											echo '<td>'.$game['fpts_plus_minus'.$i].'</td>';

											echo '</tr>';
										}	
									}

									?> 
								
							</tbody>
						
						</table>

					</div> <!-- /widget-content -->

				</div> <!-- /widget -->

		    </div> <!-- /span -->

	      </div> <!-- /row -->

	    </div> <!-- /container -->
	    
	</div> <!-- /main-inner -->
    
</div> <!-- /main -->

