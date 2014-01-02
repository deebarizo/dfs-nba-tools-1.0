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
	      				
	      				<i class="icon-edit"></i><h3>Update Stats</h3>

			      	</div>

			      	<div class="widget-content">

			      		<?php if ($success === true) { ?>

			      			<h3 style="color:red">Success!</h3>

			      		<?php } ?>

						<?php echo form_open('admin/ds_nba_add_csv'); ?>

							<p>
							        <label for="date"><h3>Date</h3></label> 
							        <input id="date" type="date" name="date" value="<?php echo $today_date; ?>" />
							        <?php echo form_error('date'); ?>
							</p>

							<p>
							        <br>
							        <?php echo form_submit( 'submit', 'Submit'); ?>
							</p>

						<?php echo form_close(); ?>
			      		
		      		</div> 
		      		
	      		</div> 

	      	</div>

		  </div> <!-- /row -->

	    </div> <!-- /container -->
	    
	</div> <!-- /main-inner -->
    
</div> <!-- /main -->		