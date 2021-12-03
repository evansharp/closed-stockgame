$(document).ready(function(){

if( typeof ticker_all !== 'undefined' ){
	//convert php json obj back to assoc. array
	var ticker_all_arr = toArr(ticker_all);
	console.log(ticker_all);

	//init the data object
	var chartData = {
  		labels: [],
  		datasets: []
	};

	//loop through each per-stock array to create the graph series
	for(var i = 0; i < ticker_all_arr.length; i++){
		chartData.datasets.push( {
				label: ticker_all_arr[i][0]['code'],
				data: [],
				backgroundColor: "",
				borderColor: "",
				tension: 0 // draw straight lines between points
			});

		for(var j = 0; j < ticker_all_arr[i].length; j++ ){

			chartData.datasets[i].data.push({
					x: moment.unix( ticker_all_arr[i][j]['timestamp'] ).format('YYYY-MM-DD HH:mm:ss'),
					y: parseFloat( ticker_all_arr[i][j]['price'] )
				});
		}
	}
	console.log(chartData);

	// data needs colours
	const dataLength = chartData.datasets.length;

	const colorRangeInfo = {
   		colorStart: 0,
   		colorEnd: 1,
   		useEndAsStart: true,
	}
	const colorScale = d3.interpolateRdYlGn;

	var colours = interpolateColors(dataLength, colorScale, colorRangeInfo);

	// only show market cap initially
	// apply colours
	chartData.datasets.forEach((e, i, a) => {
		if(e.label != "Market Cap"){
			chartData.datasets[i].hidden = true;
		}
		chartData.datasets[i].borderColor = colours[i];

		if(colours[i].indexOf('a') == -1){
    		var result = colours[i].replace(')', ', 0.2)').replace('rgb', 'rgba');
		}
		chartData.datasets[i].backgroundColor = result;
	});

	var ticker_ctx = $('#ticker_container canvas');
	if (ticker_ctx) {
	 	var ticker1 = new Chart(ticker_ctx, {
	 		type: 'line',
	 		data: chartData,
			options: {
			    scales: {
			      xAxes: [{
			        type: 'time',
			        //distribution: 'linear',
			      }],
			      title: {
			        display: false,
			      }
			    },
	    		legend: {
					position: 'right',
					align: 'start',
					title: {
						display: true,
						text: "click to show"
					},
					cursor: "pointer",
		            itemclick: function (e) {
		                if (typeof (e.dataSeries.visible) === "undefined" || e.dataSeries.visible) {
		                    e.dataSeries.visible = false;
		                } else {
		                    e.dataSeries.visible = true;
		                }

		                e.chart.render();
		            }
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

							// Drag-to-zoom effect can be customized
							// drag: {
							// 	 borderColor: 'rgba(225,225,225,0.3)'
							// 	 borderWidth: 5,
							// 	 backgroundColor: 'rgb(225,225,225)',
							// 	 animationDuration: 0
							// },

							// Zooming directions. Remove the appropriate direction to disable
							// Eg. 'y' would only allow zooming in the y direction
							// A function that is called as the user is zooming and returns the
							// available directions can also be used:
							//   mode: function({ chart }) {
							//     return 'xy';
							//   },
							mode: 'xy',

							rangeMin: {
								// Format of min zoom range depends on scale type
								x: null,
								y: null
							},
							rangeMax: {
								// Format of max zoom range depends on scale type
								x: null,
								y: null
							},

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

	$('#ticker_container a').click(function(e){
		e.preventDefault();
		ticker1.resetZoom();
	})
}
});
