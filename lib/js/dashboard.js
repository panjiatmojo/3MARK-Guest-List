jQuery(document).ready(function($) {

    ajaxUrl = '/wp-admin/admin-ajax.php';
	
	drawRejectionChart();	
	
	$('#emgl-rejection-ratio-chart-refresh').click(function()
	{
		drawRejectionChart();
	})
	
	function drawRejectionChart()
	{

    var data = {
        'action': 'emgl_get_rejection_ratio',
		'interval' : $('#emgl-rejection-ratio-interval').val()
    }
        $.post(ajaxUrl, data, function(response) {
			/**	send ajax request	**/
            jsonString = response.match(/({.+})/); //	extract only json string part
            jsonArray = JSON.parse(jsonString[0]); //	parse the json string into json array
			//console.log(jsonArray);
			jsonArray = jsonArray.data;
			
			var dataA = {};
			dataA.name = "Rejection Ratio";
			dataA.type = "spline";
			dataA.tooltip = {'valueSuffix':'%'};
			dataA.data = [];
			
			var dataB = {};
			dataB.name = "Spammer Visit";
			dataB.type = "column";
			dataB.yAxis = 1;
			dataB.data = [];
			
			for(i=0; i< jsonArray.length; i++)
			{
				dataA.data[i] = [parseFloat(jsonArray[i].time)*1000, parseFloat(jsonArray[i].rejection_ratio)]; 
				dataB.data[i] = [parseFloat(jsonArray[i].time)*1000, parseFloat(jsonArray[i].total_spammer)]; 
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
        yAxis: [
		{ // Primary yAxis
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
        }
		],
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
			

            try {
            } catch (e) {}


        });
		
	}

});