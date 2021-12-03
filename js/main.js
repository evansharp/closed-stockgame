//util for converting json string into assoc. array
function toArr( string ){
 	return Object.keys( string ).map(function(key) {
		return string[key];
	});
}

$(document).ready(function(){

	if( typeof bank_hist !== 'undefined' ){
		//convert php json obj back to assoc. array
		var bank_hist_arr = toArr( bank_hist );

		//init the data object
		var chartData = {
	  		labels: [],
	  		datasets: [{
				data: [],
				label: "Bank Balance",
				backgroundColor: "RGBA(85, 164, 81, 0.25)",
				borderColor: "RGBA(85, 164, 81, 1.00)",
				steppedLine: true
			}]
		};

		//https://stackoverflow.com/questions/60244808/how-can-i-create-a-time-series-line-graph-in-chart-js
		for(var i = 0; i < bank_hist_arr[0].length; i++){
			chartData.datasets[0].data.push( {
				x: moment.unix(bank_hist_arr[0][i]).format('YYYY-MM-DD HH:mm:ss'),
				y: parseFloat( bank_hist_arr[1][i] )
			});
		}

		console.log(chartData);

		var ticker_ctx = $('#bank_hist_container canvas');
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
				    }
				  }
			});

		}
	}

	setTimeout(function () {
  		$('.tx_result').slideUp();
  	}, 3000);

	$('#buymax').click(function(e){
		e.preventDefault();
		$('input[name="buy_num_stock"]').val( $('select[name="buy_which_stock"] option:selected').attr('data-max') );
	});

	$('#sellall').click(function(e){
		e.preventDefault();
		$('input[name="sell_num_stock"]').val( $('select[name="sell_which_stock"] option:selected').attr('data-all') );
	});

});
