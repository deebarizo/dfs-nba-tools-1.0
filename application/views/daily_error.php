<div class="main">
	
	<div class="main-inner">

	    <div class="container">

      	  <div class="row">

      	  	<div class="span12">
	      	  	<h3><?php echo $h2_tag; ?></h3>
				<div style='display:inline-block'><strong>Date:</strong></div>
				<div style='display:inline-block'>
					<form action="">
						<select class="date-drop-down" name="date-drop-down">
			      			<?php foreach ($dates as $key => $date) { ?>
			      				<option value="<?php echo base_url().'daily/ds/'.$date; ?>"<?php echo $chosen_date == $date ? ' selected' : ''; ?>><?php echo $date; ?></option>
			      			<?php } ?>
						</select>
					</form>
				</div>
	      	</div>

      	  </div>

      	  <div class="row">
	
		  	<div class="span12">

				<div class="widget">

					<div class="widget-header">
						<h3><i class="fa fa-th-list"></i> Options</h3>
					</div> <!-- /widget-header -->
					
					<div class="widget-content">

						<?php echo $error; ?>

					</div>

				</div>

			</div>

		  </div>

	    </div> <!-- /container -->
	    
	</div> <!-- /main-inner -->
    
</div> <!-- /main -->

