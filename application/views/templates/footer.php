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
<script src='<?php echo base_url().'js/jquery.qtip.min.js'; ?>'></script>

<script src='<?php echo base_url().'js/application.js'; ?>'></script>

</body>

</html>
