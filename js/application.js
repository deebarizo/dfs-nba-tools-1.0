$(document).ready(function() 
{
	// Daily table sorter

	$('#daily-stats').stupidtable(); 

	// Redirect link on date drop down menu

	$('.date-drop-down').change(function() 
	{
		window.location = $(this).val();
	});

	// Daily options

	function get_options()
	{
		var options = new Object();
		options['position'] = get_position();
		options['teams'] = get_teams();
		options['chosen_team'] = $('.team-drop-down').val();
		options['salary'] = $('.salary-input').val();
		options['salary-toggle'] = $('input:radio[name=salary-toggle]:checked').val();
		return options;
	}

	function get_position()
	{
		var position = $('.position-drop-down').val();
		if (position == 'all') { return 'all'; }
		if (position == 'forward') { return 'position-F'; }
		if (position == 'guard') { return 'position-G'; }
		if (position == 'center') { return 'position-C'; }			
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

	function get_teams_in_game(two_teams)
	{
		var team = new Object();
		team[1] = two_teams.split(/ vs /)[0];
		team[2] = two_teams.split(/ vs /)[1];

		return team;
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

		var row_data = $('.row-info').map(function() 
		{
		    return $(this).data('salary');
		}).get();

		for (var i = 0; i < row_data.length; i++) 
		{
			if (options['salary-toggle'] == 'greater-than')
			{
				if (row_data[i] >= options['salary'])
				{
					$('[data-salary="'+row_data[i]+'"]').addClass('valid-salary');
				}

				continue;
			}

			if (options['salary-toggle'] == 'less-than')
			{
				if (row_data[i] <= options['salary'])
				{
					$('[data-salary="'+row_data[i]+'"]').addClass('valid-salary');
				}
			}			
		};

		if (options['chosen_team'] == 'all')
		{
			$('.line-chart-rotations').hide();
			$('.chosen-team-rotation h4').text('')

			for (var i=0; i < options['teams'].length; i++) 
			{
				$('.'+options['teams'][i]+position_class+'.valid-salary').removeClass('valid-salary').addClass('show-row').removeClass('hide-row');
			};	
		}
		else
		{
			$('.'+options['chosen_team']+position_class+'.valid-salary').removeClass('valid-salary').addClass('show-row').removeClass('hide-row');

			// Line chart for NBA rotations

			$('.line-chart-rotations').show();
			$('h4.chosen-team-rotation').text(options['chosen_team']+' Rotations')

			var chosen_date = $('.date-drop-down option:selected').text();

		    $.ajax({
		            url: 'http://localhost/dfsnbatools/daily/get_team_rotation/'+options['chosen_team']+'/'+chosen_date,
		            type: 'POST',
		            dataType: 'json',
		            success: function(games)
		            {
		            	var rotation_dates = [];
		            	var player_data = [];

						for (var i = games.length - 1; i >= 0; i--) 
						{
		            		rotation_dates.push(games[i][0].date);

		            		for (var n = 0; n < games[i].length; n++) 
		            		{
		            			player_data.push({name: games[i][n].name, 
		            								minutes: games[i][n].minutes,
		            								date: games[i][n].date,
		            								starter: games[i][n].starter});
		            		};
		            	};

		            	console.log(games);
		            	console.log(player_data);

				        $('div.chosen-team-rotation').highcharts({
				            chart: {
				                type: 'line'
				            },
				            title: {
				                text: options['chosen_team']+' Rotations'
				            },
				            xAxis: {
				                categories: rotation_dates
				            },
			                yAxis: {
			                    title: {
			                        text: 'Minutes'
			                    }
			                },
				            series: [{
				                name: 'Tokyo',
				                data: [7.0, 6.9, 9.5, 14.5, 18.4, 21.5, 25.2, 26.5, 23.3, 18.3, 13.9, 9.6]
				            }, {
				                name: 'London',
				                data: [3.9, 4.2, 5.7, 8.5, 11.9, 15.2, 17.0, 16.6, 14.2, 10.3, 6.6, 4.8]
				            }]
				        });
		            }	
				}); 	
		}

		show_or_hide_rows();
	}

	function show_hide_team_drop_down_options(toggle, team)
	{
		$(".team-drop-down option").each(function()
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

	function show_or_hide_rows()
	{
		$('.show-row').show();
		$('.hide-row').hide();			
	}

	$('select[name=position-drop-down]').change(function() 
	{
		options_change();
	}); 

	$('select[name=team-drop-down]').change(function() 
	{
		options_change();
	}); 

	$('.salary-input').on('input', function()
	{
		options_change();
	});

	$("input[name=salary-toggle]:radio").change(function () 
	{
		options_change();
	});

	$('.salary-reset').click(function() 
	{
		$('.salary-input').val('');
		$('#greater-than').prop('checked', true);

		options_change();
	});

	$('.game-button').click(function() 
	{
		$('.team-drop-down').val('all');

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