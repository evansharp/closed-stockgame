$(document).ready(function(){
//draw main ticker if present
	if( $('#chart_ticker_all').length ){
		
		//init the data object
		var data = {
			labels : [],
			series : []
		};
		
		var options = {
										lineSmooth: false,
										plugins: [ 
											Chartist.plugins.tooltip({currency: '$'}) 
										]
		};
		
		//convert php json obj back to assoc. array
		var ticker_all_arr = Object.keys( ticker_all ).map(function(key) { return ticker_all[key]; });
		
		//use the first stock 'series' in the dataset to pull the timestamps of price updates from. It's close enough.
		for(var k = 0; k < ticker_all_arr[0].length; k++){
			var ts = moment(ticker_all_arr[0][k].timestamp * 1000);
			data.labels.push( ts.format( "MMM DD HH:mm" ) );
		}
		
		//loop through each per-stock array to create the graph series
		for(var i = 0; i < ticker_all_arr.length; i++){
			var price_series = [];
			for(var j = 0; j < ticker_all_arr[i].length; j++ ){
				//{meta: 'description', value: 1 }
				var obj = { meta: ticker_all_arr[i][j].code, value: parseFloat(ticker_all_arr[i][j].price) };
				price_series.push( obj );
			}
			data.series.push( price_series );
		}	
		
		new Chartist.Line('#chart_ticker_all', data, options);
		
	
	
		//init the data object for this chart, it will be an array of data-objects
			var data_segs = [];
			
		//console.log(ticker_segments);
		//loop through all segment data sets and draw their graphs, if present
		for(var a = 0; a < ticker_segments.length; a++){
			
			var this_seg_arr = Object.keys(ticker_segments[a]).map(function(key) { return ticker_segments[a][key]; });
			//console.log(this_seg_arr);
			//init this data array obj for this chart
			data_segs[a] = {
				labels : [],
				series : []
			};
			
			
			//use the labels (timestamps) from the all-data graph above
			data_segs[a].labels = data.labels;
			
			for(var b = 0; b < this_seg_arr.length; b++){
				var price_series_seg = [];
				
				var this_timepoint_arr = Object.keys( this_seg_arr[b] ).map(function(key) { return this_seg_arr[b][key]; });
				
				for(var c = 0; c < this_timepoint_arr.length; c++){
					//{meta: 'description', value: 1 }
					var obj = { meta: this_timepoint_arr[c].code, value: parseFloat(this_timepoint_arr[c].price) };
					price_series_seg.push( obj );
				}
				data_segs[a].series.push( price_series_seg );
		}	
		
		//use options obj from all-data graph
		
		new Chartist.Line('#chart_ticker_' + a , data_segs[a], options);
		}
	}
});