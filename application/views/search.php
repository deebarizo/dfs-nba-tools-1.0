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
	      				
	      				<h3><i class="fa fa-search"></i> Search</h3>

			      	</div>

			      	<div class="widget-content">

						<?php echo form_open(base_url().'search'); ?>

					        <label for="player" class="inline"><h4>Player</h4></label> 
					        <input id="player" type="text" name="player" />
					        <?php echo form_error('player'); ?>

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