$(document).ready(function(){

if( 	typeof bank_series !== 'undefined' &&
		typeof portfolio_series !== 'undefined' &&
		typeof total_series !== 'undefined'
	){

	var data = [
		toArr(bank_series),
		toArr(portfolio_series),
		toArr(total_series),
	];

	//init the data object
	var chartData = {
  		labels: [],
  		datasets: []
	};

	//loop through data arrays to generate datasets
	for(a = 0; a < data.length; a++){
		chartData.datasets.push( {
				data: [],
				backgroundColor: "",
				borderColor: "",
				tension: 0 // draw straight lines
			});

		//colours to match display legend
		if( a == 0 ){
			//bank
			chartData.datasets[a].backgroundColor = "RGBA(0, 174, 86, 0.25)";
			chartData.datasets[a].borderColor = "RGBA(0, 174, 86, 1.00)";
			chartData.datasets[a].steppedLine= true;
			chartData.datasets[a].label = "Bank Balance";
		}else if( a == 1 ){
			//portfolio
			chartData.datasets[a].backgroundColor = "RGBA(255, 74, 60, 0.25)";
			chartData.datasets[a].borderColor = "RGBA(255, 74, 60, 1.00)";
			chartData.datasets[a].label = "Portfolio Value";
		}else if( a == 2 ){
			//total value
			chartData.datasets[a].backgroundColor = "RGBA(250, 201, 0, 0.25)";
			chartData.datasets[a].borderColor = "RGBA(250, 201, 0, 1.00)";
			chartData.datasets[a].label = "Net Worth";
		}

		for(var i = 0; i < data[a].length; i++){
				let tmp = '';
				if( "log" in data[a][i] ){
					if( typeof data[a][i]['log'][0] !== 'undefined'){
						tmp = data[a][i]['log'][0];
					}
				}

				chartData.datasets[a].data.push( {
						x: moment( data[a][i]['x'] ).format('YYYY-MM-DD HH:mm:ss'),
						y: parseFloat( data[a][i]['y'] ),
						meta: tmp
					});


		}
	}

	var ticker_ctx = $('#portfolio_chart_container canvas');
	if (ticker_ctx) {
	 	var chart1 = new Chart(ticker_ctx, {
	 		type: 'line',
	 		data: chartData,
			options: {
			    scales: {
			      xAxes: [{
			        type: 'time',
			        distribution: 'linear',
			      }],
			      title: {
			        display: false,
			      }
			    },
	    		legend: {
					display: false,
				},
				tooltips: {
					position: 'nearest',
					// les demo:
					// https://stackoverflow.com/questions/38622356/chart-js-tooltiptemplate-not-working
					callbacks:{
						title: function(tooltipItems, data){
							var titleString = tooltipItems[0].label;
							return moment(titleString).format('lll');
						},
						label : function(tooltipItem, data) {
                        	return data.datasets[tooltipItem.datasetIndex].label + ': $' + tooltipItem.yLabel;
                    	}
					}
			 	},
				plugins: {
					zoom: {
						pan: { enabled: false },
						zoom: {
							enabled: true,
							// Enable drag-to-zoom behavior
							drag: true,
							mode: 'xy',
							// Speed of zoom via mouse wheel
							// (percentage of zoom on a wheel event)
							speed: 0.1,
							// Minimal zoom distance required before actually applying zoom
							threshold: 2,
							// On category scale, minimal zoom level before actually applying zoom
							sensitivity: 3
						}
					}
				}
		 	}
		});
	}
	$('#portfolio_chart_container a').click(function(e){
		e.preventDefault();
		chart1.resetZoom();
	})

}
if( typeof portfolio !== 'undefined'){
	chartData = {
	    datasets: [{
	        data: [],
			backgroundColor: []
	    }],
	    labels: []
	};


	for(b = 0; b < portfolio.length; b++){
		chartData.datasets[0].data.push( (portfolio[b].price * parseInt(portfolio[b].owned)).toFixed(2) );
		chartData.labels.push( portfolio[b].code );
	}

	// data needs colours
	const dataLength = chartData.datasets[0].data.length;

	const colorRangeInfo = {
   		colorStart: 0,
   		colorEnd: 1,
   		useEndAsStart: true,
	}
	const colorScale = d3.interpolateSinebow;

	var colours = interpolateColors(dataLength, colorScale, colorRangeInfo);

	// apply colours
	chartData.datasets[0].data.forEach((e, i, a) => {
		chartData.datasets[0].backgroundColor.push( colours[i] );
	});

	var port_comp_ctx = $('#portfolio_comp_chart_container canvas');
	var chart2 = new Chart(port_comp_ctx, {
    	type: 'doughnut',
    	data: chartData,
    	options: {
			legend: {
				display: false,
			},
			tooltips: {
				callbacks:{
					 title: function(tooltipItem, data){
						return data.labels[ tooltipItem[0].index ];
					},
					label : function(tooltipItem, data) {
						return "$ " + data.datasets[0].data[tooltipItem.index];
					}
				}
			}
		}
	});
}
});
