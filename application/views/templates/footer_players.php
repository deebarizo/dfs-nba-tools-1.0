<div class="push"></div> <!-- sticky footer -->

</div> <!-- sticky footer -->

<div class="footer">
	
	<div class="footer-inner">
		
		<div class="container">
			
			<div class="row">
				
    			<div class="span12">
					<?php function auto_copyright($year = 'auto'){ ?>
					   <?php if(intval($year) == 'auto'){ $year = date('Y'); } ?>
					   <?php if(intval($year) == date('Y')){ echo intval($year); } ?>
					   <?php if(intval($year) < date('Y')){ echo intval($year) . ' - ' . date('Y'); } ?>
					   <?php if(intval($year) > date('Y')){ echo date('Y'); } ?>
					<?php } ?>
    				&copy; <?php auto_copyright('2013'); ?> <a href='<?php echo base_url(); ?>'>DFS NBA Tools</a>
    			</div> <!-- /span12 -->
    			
    		</div> <!-- /row -->
    		
		</div> <!-- /container -->
		
	</div> <!-- /footer-inner -->
	
</div> <!-- /footer -->

<script src='http://code.jquery.com/jquery-1.10.2.min.js'></script>
<script src='<?php echo base_url().'js/bootstrap.min.js'; ?>'></script>

<script src='<?php echo base_url().'js/stupidtable.js'; ?>'></script>
<script src="<?php echo base_url().'js/highcharts.js'; ?>"></script>

<script type="text/javascript">

	$(function () {
	        $('.player-fpts-distribution').highcharts({
	            chart: {
	                type: 'column'
	            },
	            title: {
	                text: 'Player FPTS Distribution'
	            },
	            xAxis: {
	                categories: ['2P', '3P', 'FT', 'OREB', 'DREB', 'AST', 'TO', 'STL', 'BLK']
	            },
	            credits: {
	                enabled: false
	            },
	            legend: false,
	            series: [{
                	data: [
                		<?php 
                			echo 
                				$fpts_distribution['twop'].', '.
                				$fpts_distribution['threep'].', '.
                				$fpts_distribution['ft'].', '.
                				$fpts_distribution['oreb'].', '.
                				$fpts_distribution['dreb'].', '.
                				$fpts_distribution['ast'].', '.
                				$fpts_distribution['turnovers'].', '.
                				$fpts_distribution['stl'].', '.
                				$fpts_distribution['blk'];
                		?>
                	]
                }],
                plotOptions: {
                	column: {colorByPoint: true}
                }
	        });
	    });

</script>

</body>

</html>
