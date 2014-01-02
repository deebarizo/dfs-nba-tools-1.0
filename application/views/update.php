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
	      				
	      				<i class="icon-edit"></i><h3>Update Stats</h3>

			      	</div>

			      	<div class="widget-content">

			      		<?php if ($message != 'Form validation error.') { ?>

			      			<p style="color:red"><?php echo $message; ?></p>

			      		<?php } ?>

						<?php echo form_open(base_url().'update'); ?>

							<p>
							        <label for="date"><h4>Date for IRL Stats Update</h4></label> 
							        <input id="date" type="date" name="date" value="<?php echo $yesterday_date; ?>" />
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