$(document).ready(function(){
	if($('#portfolio_chart').length ){
		var portfolio_labels = [];

		for (var a = 0; a < updates_series.length; a++){
			portfolio_labels.push( moment( updates_series[a], 'YYYY-MM-DD H:m:s' ).format('MMM DD HH:mm') );
		}

		//bank value series object
		var bksd = [];
		for( var i = 0; i < bank_series.length; i ++ ){
			bksd.push( {
										x		: new Date( moment(bank_series[i].x, 'YYYY-MM-DD H:m:s' ).format() ),
										y		: bank_series[i].y,
										meta: 'Bank Balance: $ ' + bank_series[i].y + ' at ' + moment(bank_series[i].x, 'YYYY-MM-DD H:m:s' ).format('MMM DD HH:mm'),
									} );
		}

		//portfolio value series object
		var ptsd = [];
		for( var j = 0; j < portfolio_series.length; j ++ ){
			ptsd.push( {
										x		: new Date( moment(bank_series[j].x, 'YYYY-MM-DD H:m:s' ).format() ),
										y		: portfolio_series[j].y,
										meta: 'Portfolio Value: $ ' + portfolio_series[j].y + ' at ' + moment(portfolio_series[j].x, 'YYYY-MM-DD H:m:s' ).format('MMM DD HH:mm'),
									} );
		}

		//combined value series object
		var cbsd = [];
		for( var k = 0; k < total_series.length; k ++ ){
			cbsd.push( {
										x		: new Date( moment(bank_series[k].x, 'YYYY-MM-DD H:m:s' ).format() ),
										y		: total_series[k].y,
										meta: 'Net Worth: $ ' + total_series[k].y + ' at ' + moment(total_series[k].x, 'YYYY-MM-DD H:m:s' ).format('MMM DD HH:mm'),
									} );
		}

		var port_chart_data = {
									labels: portfolio_labels,
									series: [
											{
													name: 'bank-value',
											    data: bksd
		    								}
		    								,{
		    									name: 'portfolio-value',
		    									data: ptsd
		    								},{
		    									name: 'combined-value',
		    									data: cbsd
		    								}
    								]};

		var port_chart_options = {
									lineSmooth: false,
									series: {
										'bank-value': {
    								},
    								'portfolio-value': {
											showArea: true
    								},
    								'combined-value': {
											//animated with css
    								}
									},
									axisY: {
											type: Chartist.AutoScaleAxis,
											onlyInteger: true,
											},
									 //axisX: {
									 //			type: Chartist.FixedScaleAxis,
									 //			divisor: portfolio_labels.length,
									 //			labelInterpolationFnc: function( value ) {
									 //				console.log(value);
									 //				return moment(value).format('MMM D HH:ss');
									 //			}
									 //		},
									plugins: [
											Chartist.plugins.tooltip({
												transformTooltipTextFnc:function(value){return '';} //disable value parameter
											})
										]
  								};

		new Chartist.Line('#portfolio_chart', port_chart_data, port_chart_options);


		var seg_labels = [];
		//prepare and draw segment chart
		var total_value = 0;
		for (var a = 0; a < portfolio.length; a++){
			total_value += parseInt(portfolio[a].value);

		}

		var seg_percentages = [];
		for (var b = 0; b < portfolio.length; b++){
			var percent = (portfolio[b].value / total_value) * 100;
			seg_percentages.push( percent );
			seg_labels.push( portfolio[b].code + " " + Math.round( percent ) + "%" );
		}
		if(seg_percentages.length < 1){
			seg_labels.push('No Stocks Yet');
			seg_percentages.push('100');
		}

		var data_seg_pie = {
			labels: seg_labels,
			series: seg_percentages
		}

		var options_seg_pie = {
			chartPadding: 30,
		    labelOffset: 70,
		    labelDirection: 'explode',
				labelInterpolationFnc: function(value) {
    			return value
  			}
		};

		new Chartist.Pie('#portfolio_segments_chart', data_seg_pie, options_seg_pie);
	}
});
