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

<script type="text/javascript">

	$(document).ready(function() 
	{
		$('#daily-stats').stupidtable();

		$('select[name=position-drop-down]').change(function () 
		{

		}); 
	
		$('.game-button').click(function() 
		{
			var teams_in_game = $(this).text();
			var team1 = teams_in_game.split(/ vs /)[0];
			var team2 = teams_in_game.split(/ vs /)[1];

			var position = $('#position-drop-down').val();
			if (position == 'all') { position_class = 'all'; }
			if (position == 'forward') { position_class = 'position-F'; }
			if (position == 'guard') { position_class = 'position-G'; }
			if (position == 'center') { position_class = 'position-C'; }

			if ($(this).hasClass('hide-game'))
			{
				if (position_class == 'all')
				{
					$('.'+team1).addClass('show-row');
					$('.'+team1).removeClass('hide-row');

					$('.'+team2).addClass('show-row');
					$('.'+team2).removeClass('hide-row');		
				}
				else
				{
					$('.'+team1+'.'+position_class).addClass('show-row');
					$('.'+team1+'.'+position_class).removeClass('hide-row');

					$('.'+team2+'.'+position_class).addClass('show-row');
					$('.'+team2+'.'+position_class).removeClass('hide-row');
				}

				$(this).addClass('show-game');
				$(this).removeClass('hide-game');
			} 
			else if ($(this).hasClass('show-game'))
			{
				$('.'+team1).addClass('hide-row');
				$('.'+team1).removeClass('show-row');

				$('.'+team2).addClass('hide-row');
				$('.'+team2).removeClass('show-row');

				$(this).addClass('hide-game');
				$(this).removeClass('show-game');
			}

			$('.show-row').show();
			$('.hide-row').hide();
		});
	});

</script>

</body>

</html>
