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

		function get_position()
		{
			var position = $('#position-drop-down').val();
			if (position == 'all') { return 'all'; }
			if (position == 'forward') { return 'position-F'; }
			if (position == 'guard') { return 'position-G'; }
			if (position == 'center') { return 'position-C'; }			
		}

		function get_teams_in_game(two_teams)
		{
			var team = new Object();
			team[1] = two_teams.split(/ vs /)[0];
			team[2] = two_teams.split(/ vs /)[1];

			return team;
		}

		function get_teams()
		{
			var teams = new Array();

			$(".game-button.show-game").each(function(index) 
			{
				var two_teams = $(this).text();

				var team = get_teams_in_game(two_teams);

				teams.push(team[1]);
				teams.push(team[2]);
			});	

			return teams;
		}

		function get_options()
		{
			var options = new Object();
			options['position'] = get_position();
			options['teams'] = get_teams();
			return options;
		}

		function show_or_hide_rows()
		{
			$('.show-row').show();
			$('.hide-row').hide();			
		}

		$('select[name=position-drop-down]').change(function() 
		{
			var options = get_options();

			$('.show-row').removeClass('show-row').addClass('hide-row');

			if (options['position'] == 'all')
			{
				var position_class = '';
			}
			else
			{
				var position_class = '.'+options['position'];
			}

			for (var i=0; i < options['teams'].length; i++) 
			{
				$('.'+options['teams'][i]+position_class).addClass('show-row').removeClass('hide-row');
			};	

			show_or_hide_rows();
		}); 
	
		$('.game-button').click(function() 
		{
			var two_teams = $(this).text();

			var team = get_teams_in_game(two_teams);

			var options = get_options();

			for (var i=1; i <= 2; i++) 
			{
				if ($(this).hasClass('hide-game'))
				{
					if (options['position'] == 'all')
					{
						$('.'+team[i]).addClass('show-row');
						$('.'+team[i]).removeClass('hide-row');
					}
					else
					{
						$('.'+team[i]+'.'+options['position']).addClass('show-row');
						$('.'+team[i]+'.'+options['position']).removeClass('hide-row');
					}

					if (i == 2)
					{
						$(this).addClass('show-game');
						$(this).removeClass('hide-game');						
					}
				} 
				else if ($(this).hasClass('show-game'))
				{
					$('.'+team[i]).addClass('hide-row');
					$('.'+team[i]).removeClass('show-row');

					if (i == 2)
					{
						$(this).addClass('hide-game');
						$(this).removeClass('show-game');
					}
				}
			}

			show_or_hide_rows();
		});
	});

</script>

</body>

</html>
