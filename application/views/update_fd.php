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
							        <label for="url"><h4>FD Salaries URL</h4></label> 
							        <input style='width: 650px' id="url" type="url" name="url" />
							        <?php echo form_error('url'); ?>
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