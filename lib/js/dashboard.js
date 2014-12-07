jQuery(document).ready(function($) {

    ajaxUrl = '/wp-admin/admin-ajax.php'; //	define the ajax url default

    drawRejectionChart(); //	draw the rejection chart
    activateVisitorData(); //	activate the visitor data view
    activateBlockedVisitorData(); //	activate the blocked visitor view

    /**	attach listener to clean visitor data button	**/
    $('#clean-visitor-data').click(function() {
        var data = {
            'action': 'emgl_cleanup_visitor_data',
            'beforeSend': function() {
                showLoadingScreen();
            }
        }
        ajaxUrl = '/wp-admin/admin-ajax.php';

        //	trigger store visitor data
        $.post(ajaxUrl, data, function(response) {
            response = response.match(/{.*}/);
            response = JSON.parse(response);
            hideLoadingScreen();
            alert(response.message);
        });
    });

    /**	attach listener to execute spammer analysis	**/
    $('#execute-spammer-analysis').click(function() {
        var data = {
            'action': 'emgl_spammer_analysis',
            'beforeSend': function() {
                showLoadingScreen();
            }
        }
        ajaxUrl = '/wp-admin/admin-ajax.php';

        //	trigger store visitor data
        $.post(ajaxUrl, data, function(response) {
            response = response.match(/{.*}/);
            response = JSON.parse(response);
            hideLoadingScreen();
            alert(response.message);
        });
    });

    function activateVisitorData() {
        $('#refresh-visitor-data').click(function() {
            updateVisitorData();
        });

        $('#emgl-visitor-container .pagination-page').click(function() {
            updateVisitorData(this);
        });
		
        $('#emgl-visitor-data-table table tbody tr td').click(function() {
            showVisitorDetail(this);
        });

		$('#emgl-visitor-data-table table tbody tr').css('cursor', 'pointer');

		$('#emgl-visitor-data-table .emgl-block-action-button').click(function() {
			blockUnblockVisitor(this);
		});

		$('#emgl-visitor-data-table .emgl-block-action').closest('td').unbind('click');
    }

    function updateVisitorData(selector) {

        var data = {
            'action': 'emgl_show_visitor_data',
            'page': $(selector).attr('data-page'),
            'total_row': $('#emgl-visitor-data-total-row').val(),
            'beforeSend': function() {
                showLoadingScreen();
            }

        }
        ajaxUrl = '/wp-admin/admin-ajax.php';

        //	trigger store visitor data
        $.post(ajaxUrl, data, function(response) {
            response = response.match(/{.*}/);
            response = JSON.parse(response);
            table = response.result;
            pagination = response.pagination;

            $('#emgl-visitor-data-table').html(table);
            $('#emgl-visitor-data-pagination').html(pagination);
            hideLoadingScreen();
            activateVisitorData();

        });
    }

    function activateBlockedVisitorData() {
        $('#refresh-blocked-visitor-data').click(function() {
            updateBlockedVisitorData(this);
        });

        $('#emgl-blocked-visitor-container .pagination-page').click(function() {
            updateBlockedVisitorData(this);
        });
		
        $('#emgl-blocked-visitor-data-table table tbody tr td').click(function() {
            showVisitorDetail(this);
        });
		
		$('#emgl-blocked-visitor-data-table table tbody tr').css('cursor', 'pointer');
		
		$('#emgl-blocked-visitor-data-table .emgl-block-action-button').click(function() {
			blockUnblockVisitor(this);
		});

		$('#emgl-blocked-visitor-data-table .emgl-block-action').closest('td').unbind('click');

    }
	
	function showVisitorDetail(selector)
	{
		var visitorDetail = $(selector).parent('tr').find('textarea[name=em-blocked-detail]').val();
		
		var parameter = {
			'selector': 'body',
			'content' : visitorDetail	
		}
		showFloatingScreen(parameter);	
	}


    function updateBlockedVisitorData(selector) {
        var data = {
            'action': 'emgl_show_blocked_visitor_data',
            'page': $(selector).attr('data-page'),
            'total_row': $('#emgl-blocked-visitor-data-total-row').val(),
            'beforeSend': function() {
                showLoadingScreen();
            }
        }
        ajaxUrl = '/wp-admin/admin-ajax.php';

        //	trigger store visitor data
        $.post(ajaxUrl, data, function(response) {
            response = response.match(/{.*}/);
            response = JSON.parse(response);
            table = response.result;
            pagination = response.pagination;

            $('#emgl-blocked-visitor-data-table').html(table);
            $('#emgl-blocked-visitor-data-pagination').html(pagination);
            hideLoadingScreen();
            activateBlockedVisitorData();

        });

    }

    $('#emgl-rejection-ratio-chart-refresh').click(function() {
        drawRejectionChart();
    })

    function drawRejectionChart() {

        var data = {
            'action': 'emgl_get_rejection_ratio',
            'interval': $('#emgl-rejection-ratio-interval').val(),
            'beforeSend': function() {
                showLoadingScreen();
            }
        }
        $.post(ajaxUrl, data, function(response) {
            /**	send ajax request	**/
            jsonString = response.match(/({.+})/); //	extract only json string part
            jsonArray = JSON.parse(jsonString[0]); //	parse the json string into json array
            //console.log(jsonArray);
            jsonArray = jsonArray.data;

            var dataA = {};
            dataA.name = "Rejection Ratio";
            dataA.type = "line";
            dataA.tooltip = {
                'valueSuffix': '%'
            };
            dataA.data = [];

            var dataB = {};
            dataB.name = "Spammer Visit";
            dataB.type = "column";
            dataB.yAxis = 1;
            dataB.data = [];

            for (i = 0; i < jsonArray.length; i++) {
                dataA.data[i] = [parseFloat(jsonArray[i].time) * 1000, parseFloat(jsonArray[i].rejection_ratio)];
                dataB.data[i] = [parseFloat(jsonArray[i].time) * 1000, parseFloat(jsonArray[i].total_spammer)];
            }

            var data = [dataB, dataA];

            //console.log(data);

            var chartContainer = $('#emgl-rejection-ratio-chart-container');

            chartContainer.highcharts({
                chart: {
                    zoomType: 'xy'
                },
                title: {
                    text: 'Spammer Rejection Ratio'
                },
                xAxis: {
                    type: 'datetime',
                    title: {
                        text: 'Date'
                    }
                },
                yAxis: [{ // Primary yAxis
                    labels: {
                        format: '{value}%',
                        style: {
                            color: Highcharts.getOptions().colors[1]
                        }
                    },
                    title: {
                        text: 'Rejection Ratio',
                        style: {
                            color: Highcharts.getOptions().colors[1]
                        }
                    }
                }, { // Secondary yAxis
                    title: {
                        text: 'Spammer Visit',
                        style: {
                            color: Highcharts.getOptions().colors[0]
                        }
                    },
                    labels: {
                        format: '{value}',
                        style: {
                            color: Highcharts.getOptions().colors[0]
                        }
                    },
                    opposite: true
                }],
                tooltip: {
                    shared: true
                },
                legend: {
                    align: 'right',
                    x: -70,
                    verticalAlign: 'top',
                    y: 20,
                    floating: true,
                    backgroundColor: (Highcharts.theme && Highcharts.theme.background2) || 'white',
                    borderColor: '#CCC',
                    borderWidth: 1,
                    shadow: false
                },
                series: data
            });

            hideLoadingScreen();


            try {} catch (e) {}


        });

    }

    function showLoadingScreen(selector) {
        var selector = (selector !== undefined) ? selector : 'body';
        hideLoadingScreen(selector); //	remove any existing loading screen

        var screenLoading = "<div id=\"emgl-loading-screen\" style=\"width:100%;height:100%;position:fixed;top:0;bottom:0;right:0;left:0;z-index:100000;display:none;background:#000;opacity:.8;text-align:center;line-height:100%;\">" +
            "<img src='" + emgl_dashboard_vars.image_folder + "\loading.gif' style='position:absolute;top:0;bottom:0;right:0;left:0;margin:auto;width:80px;height:10px;'>" +
            +"</div>";

        /**	append the loading screen into specific selector	**/
        $(selector).append(screenLoading);
        $("#emgl-loading-screen").show();

        /**	store the function into window global variable	**/
        window.emglLoadingScreen = setTimeout(function() {
            hideLoadingScreen(selector);
        }, 60000);
    }

    function hideLoadingScreen(selector) {
        var selector = (selector !== undefined) ? selector : 'body';
        clearTimeout(window.emglLoadingScreen);
        $(selector + " #emgl-loading-screen").remove();
    }

    function showFloatingScreen(parameter) {
		var content = (parameter.content !== undefined) ? parameter.content : 'body';
        var selector = (parameter.selector !== undefined) ? parameter.selector : 'body';
        hideFloatingScreen(selector); //	remove any existing loading screen

        var floatingScreen = "<div id=\"emgl-floating-screen\" style=\"width:100%;height:100%;position:fixed;top:0;bottom:0;right:0;left:0;z-index:100000;display:none;background:#000;opacity:.8;text-align:center;line-height:100%;\"></div>" +
            "<div id=\"emgl-floating-screen-wrapper\" style='border-radius:3px;overflow:hidden;position:fixed;top:0;bottom:0;right:0;left:0;margin:auto;width:80%;height:80%;background:#FFF;opacity:1;z-index:100001;'><div id=\"emgl-floating-screen-menu\" style=\"display:block;\"><div id=\"emgl-floating-screen-close\" style=\"position:absolute;opacity:0.8;right:30px;top:20px;height:30px;width:30px;background:#F64747;color:#FFF;text-align:center;line-height:30px;border-radius:15px;font-weight:bold;cursor:pointer;font-family:Verdana, Geneva, sans-serif\">X</div></div><div id=\"emgl-floating-screen-content\" style=\"margin:auto;width:96%;overflow:scroll;padding:2%;\">"+ content +"&nbsp;</div></div>";

        /**	append the loading screen into specific selector	**/
        $(selector).append(floatingScreen);
        $("#emgl-floating-screen").show();
		
		var screenHeight = $('#emgl-floating-screen-wrapper').css('height');
		
		console.log(screenHeight);
		
		var newCss = {
			'height' : parseFloat(screenHeight)*0.9,
			'padding-top' : parseFloat(screenHeight)*0.05,
			'padding-bottom' : parseFloat(screenHeight)*0.05
		}
		
		console.log(newCss);
		
		$('#emgl-floating-screen-content').css(newCss);
		
		$('#emgl-floating-screen-close').click(function(parameter)
		{
			hideFloatingScreen(parameter);	
		});
    }
	
	function hideFloatingScreen(parameter) {
        var selector = (parameter.selector !== undefined) ? parameter.selector : 'body';
        $(selector + " #emgl-floating-screen").remove();
        $(selector + " #emgl-floating-screen-wrapper").remove();
    }
	
	
	function blockUnblockVisitor(selector) {
		
		parameter = {};
		
		parameter.ip_address = $(selector).siblings('.emgl-ip-address').val();
		parameter.block_action = $(selector).siblings('.emgl-block-action').val();
		
        var data = {
            'action': 'emgl_block_visitor_data',
            'block_action': parameter.block_action,
            'ip_address': parameter.ip_address,
            'beforeSend': function() {
                showLoadingScreen();
            }

        }
        ajaxUrl = '/wp-admin/admin-ajax.php';

        //	trigger store visitor data
        $.post(ajaxUrl, data, function(response) {
            response = response.match(/{.*}/);
            response = JSON.parse(response);
			if(response.status == 'success')
			{
				alert('success block/unblock visitor');	
				
				var parentSelector = $(selector).parents('tr');
				/**	update the visitor type into new value	**/
				if(parameter.block_action == 'block')
				{
					parentSelector.find('.emgl-visitor-type').html('spammer');
					parentSelector.find('.emgl-block-action').val('unblock');
					parentSelector.find('.emgl-block-action-button').val('UNBLOCK');
					
				}
				else if(parameter.block_action == 'unblock')
				{
					parentSelector.find('.emgl-visitor-type').html('human');	
					parentSelector.find('.emgl-block-action').val('block');
					parentSelector.find('.emgl-block-action-button').val('BLOCK');
				}
			}
			else
			{
				/**	if not successful then show alert message	**/
				alert(response.message);	
			}
            hideLoadingScreen();

        });
    }


});