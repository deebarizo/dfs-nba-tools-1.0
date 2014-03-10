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
		    
	      		<div class="widget">
	      			
	      			<div class="widget-header">
	      				
	      				<h3><i class="fa fa-upload"></i> Update FD Salaries</h3>

			      	</div>

			      	<div class="widget-content">

			      		<?php if ($message != 'Form validation error.') { ?>

			      			<p style="color:red"><?php echo $message; ?></p>

			      		<?php } ?>

						<?php echo form_open(base_url().'update/fd'); ?>

							<p>
							        <label for="date"><h4>Date for FD Salaries Update</h4></label> 
							        <input id="date" type="date" name="date" value="<?php echo $today_date; ?>" />
							        <?php echo form_error('date'); ?>
							</p>

							<p>
							        <label for="raw_data"><h3>Raw Data</h3></label>
									<textarea id="raw_data" name="raw_data" rows="32" style="width: 500px" /></textarea>
							        <?php echo form_error('raw_data'); ?>
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