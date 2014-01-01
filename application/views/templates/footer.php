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

		function options_change()
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

			if (options['chosen_team'] == 'all')
			{
				for (var i=0; i < options['teams'].length; i++) 
				{
					$('.'+options['teams'][i]+position_class).addClass('show-row').removeClass('hide-row');
				};	
			}
			else
			{
				$('.'+options['chosen_team']+position_class).addClass('show-row').removeClass('hide-row');
			}

			show_or_hide_rows();
		}

		function get_options()
		{
			var options = new Object();
			options['position'] = get_position();
			options['teams'] = get_teams();
			options['chosen_team'] = $('#team-drop-down').val();
			return options;
		}

		function show_or_hide_rows()
		{
			$('.show-row').show();
			$('.hide-row').hide();			
		}

		function show_hide_team_drop_down_options(toggle, team)
		{
			$("#team-drop-down option").each(function()
			{
			    var team_in_drop_down = $(this).val();

			    for (var i = 1; i <= 2; i++) 
			    {
			    	if (team[i] == team_in_drop_down)
			    	{
			    		if (toggle == 'show') { $(this).show(); }
			    		if (toggle == 'hide') { $(this).hide(); }
			    	}
			    }
			});			
		}

		$('select[name=position-drop-down]').change(function() 
		{
			options_change();
		}); 

		$('select[name=team-drop-down]').change(function() 
		{
			options_change();
		}); 
	
		$('.game-button').click(function() 
		{
			$('#team-drop-down').val('all');

			var two_teams = $(this).text();

			var team = get_teams_in_game(two_teams);

            if ($(this).hasClass('hide-game'))
            {
				show_hide_team_drop_down_options('show', team);

				$(this).addClass('show-game');
                $(this).removeClass('hide-game');                                                
            } 
            else if ($(this).hasClass('show-game'))
            {
				show_hide_team_drop_down_options('hide', team);

				$(this).addClass('hide-game');
                $(this).removeClass('show-game');
            }

			options_change();
		});
	});

</script>

</body>

</html>
