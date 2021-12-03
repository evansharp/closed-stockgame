function display_update( selected_id ){
        var filtered_data = auto_updates_template.by_stock[ selected_id ];

        Object.keys(filtered_data).forEach(function(key){
            //flatten and replace update num in property set
            filtered_data[key].update_num = key;
        });

            // can't reinit certain options, so destroy and re-creates
            if(typeof updateEditor !== "undefined"){
                updateEditor.destroy();
            }

            // template table
            updateEditor = $('#updates_template_editor').DataTable({
                data: filtered_data,
                columns: [{
                        data: 'id',
                        title: 'Database id',
                        readonly: true
                    },
                    {
                        data: 'update_num',
                        title: 'Update #',
                        readonly: true
                    },
                    {
                        data: 'price',
                        title: 'Price'
                    }],
                order: [[ 0, 'asc' ]],
                pageLength: 50,
                searching: false,

                // start editor options
                // https://github.com/KasperOlesen/DataTable-AltEditor/blob/master/example/03_ajax_objects/example3.js

                dom: 'Bfrtip',
                select: 'single',
                responsive: true,
                altEditor: true,
                closeModalOnSuccess: true,
                buttons: [{
                        extend: 'selected', // Bind to Selected row
                        text: 'Edit',
                        name: 'edit'        // do not change name
                    }],
                onEditRow: function(datatable, rowdata, success, error) {
                    $('#editRowBtn').html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>').prop('disabled', true);

                    $.ajax({
                        url: 'ajax/edit_auto_updates_template',
                        method: "POST",
                        data: rowdata,
                        success: success,
                        error: error
                    }).done(function(){
                        $('#editRowBtn').html('Edit').prop('disabled', false);
                    });
                }
            });
            updateEditor.buttons().container().appendTo( $('#updates_template_editor_actions') );
            // populate delete button on select
            updateEditor.on('select', function(e, dt, type, indexes){
                if ( type === 'row' ) {
                    var target_update = updateEditor.rows( indexes ).data().pluck( 'update_num' );
                    $('#updates_template_editor_action_delete').data('target-update', target_update[0]);
                    console.log( target_update[0] );
                }
            });

            // draw chart of update prices:
            if( $('#auto_update_stock_chart_container canvas').length > 0){
                var chartData = {
                    labels: [],
                    datasets: [
                        {
            				label: [],
            				data: [],
            				//backgroundColor: "",
            				borderColor: "#B4B034"
                		}
                    ]
                };
                filtered_data.forEach( (update, i) => {
                    chartData.labels.push( update.update_num );
                    chartData.datasets[0].label.push( update.update_num );
                    chartData.datasets[0].data.push( update.price );
                });
                var auto_price_ctx = $('#auto_update_stock_chart_container canvas');
            	if (auto_price_ctx) {
            	 	var chart2 = new Chart(auto_price_ctx, {
            	 		type: 'line',
            	 		data: chartData,
            			options: {
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
            							return "Update #" + titleString;
            						},
            						label : function(tooltipItem, data) {
                                    	return '$ ' + tooltipItem.yLabel;
                                	}
            					}
            			 	},
            		 	}
            		});
                }
            }   // end template price chart
}

$(document).ready(function(){

    $('.data_table').DataTable({
        pageLength: 20,
        responsive: true,
        info: false,
        searching: false,
        lengthChange: false
    });

    var updateEditor, selected_stock = "";

    //init auto update editor
    if($('#updates_template_editor_selector a').length > 0){
        var first_stock = $('#updates_template_editor_selector a').first().attr("data-stock");
        $('#updates_template_editor_selected').text($('#updates_template_editor_selector a').first().text() + " [" + first_stock + "]");
        display_update( first_stock );

        //listen to the selector dropdown!
        $('#updates_template_editor_selector a').click(function(e){
            e.preventDefault();
            selected_stock = e.target.getAttribute("data-stock");
            $('#updates_template_editor_selected').text(e.target.innerText + " [" + selected_stock + "]");
            display_update(selected_stock);
        });

        //add button
        $('#updates_template_editor_action_add').click(function(){
            function update_add_display( selected ){
                const rows = $('[data-update-stock-id]');
                rows.each( (i, e) => {
                    var jEl = $(e);
                    var row_stock_id = jEl.attr('data-update-stock-id');

                    jEl.children('.auto_update_price_before')
                            .text( auto_updates_template['by_update'][ selected ][row_stock_id].price );

                    var next = parseInt( selected );

                    if(next != lastUpdate){
                        next++;
                        var p = auto_updates_template['by_update'][ next ][row_stock_id].price;
                    }else{
                        var p = "-";
                    }
                    jEl.children('.auto_update_price_after').text( p );
                });
            }

            var lastUpdate = Object.keys(auto_updates_template['by_update']).reduce((a, b) => {
                return Math.max(a, b);
            });

            var selected = $('#add_auto_update [name=insertPoint]').val();
            $('#insertPoint_after').text( selected );
            $('[name="update_after"]').val( selected );

            //pre populate table of prices
            update_add_display(selected);

            // listen to slider for update insert point in order
            $('#add_auto_update [name=insertPoint]')
                .attr('max', lastUpdate)
                .on('input change', function (e){
                    const val = e.target.value;

                    $('#insertPoint_after').text( val );

                    // do stuff here to update prices of each stock in
                    //display table before and after proposed new update point
                    update_add_display( val );
                    $('[name="update_after"]').val( val );
            });

        });

        // delete button
        // update selected for deletion is put into deletebutton data by select callback
        $('#confirm_update_delete').on('show.bs.modal', function(e) {
            var button = $(e.relatedTarget);
            var $modal = $(this);
            var target_update_num = button.data('target-update');

            $modal.find('.modal-header > span').html( target_update_num );
            $modal.find('.modal-footer input').val( target_update_num );

            $modal.find('.modal-body .row').each(function(i,e){
                $(e).children('.price').html( auto_updates_template.by_update[target_update_num][$(e).data('stock-id')].price );
            });
        });
    }


    // player activity graph
    if( $('#player_activity_chart_container canvas').length > 0 ){
        var chartData = {
            labels: [],
            datasets: []
        };

        for( const date in toArr(activity)[0] ){
            chartData.labels.push( moment( date ).format('ll') );
        }

        for(const player_name in activity ){
            var logins = [];
            for( day_total in activity[player_name] ){
                logins.push( activity[player_name][day_total] );
            }

    		chartData.datasets.push( {
    				label: player_name,
    				data: logins,
    				backgroundColor: "",
    				borderColor: ""
    			});

    	}

        // data needs colours
    	const dataLength = chartData.datasets.length;

    	const colorRangeInfo = {
       		colorStart: 0,
       		colorEnd: 1,
       		useEndAsStart: true,
    	}
    	const colorScale = d3.interpolateRainbow;

    	var colours = interpolateColors(dataLength, colorScale, colorRangeInfo);

        chartData.datasets.forEach((e, i, a) => {
    		if(e.label == "Evan Sharp"){
    			chartData.datasets[i].hidden = true;
    		}
    		chartData.datasets[i].backgroundColor = colours[i];
    	});

        var ctx = $('#player_activity_chart_container canvas');
        var activity_chart = new Chart(ctx,{
            type: 'bar',
            data: chartData,
            options: {
                legend: {
                    position: 'right'
                },
                tooltips: {
                  displayColors: true,
                  callbacks:{
                    mode: 'x',
                  },
                },
                scales: {
                  xAxes: [{
                    stacked: true,
                    gridLines: {
                      display: false,
                    }
                  }],
                  yAxes: [{
                    stacked: true,
                    ticks: {
                      beginAtZero: true,
                    },
                    type: 'linear',
                  }]
                }
            }
        });
    }// end player activity

    // dynamic player delete confirm modal
    $('#delete_player_confirm_modal').on('show.bs.modal', function (event) {
        // Button that triggered the modal
        var button = event.relatedTarget
        // Extract info from data-bs-* attributes
        var player = button.getAttribute('data-playername')

        var blankspace = this.querySelector('.modal-body span')
        blankspace.textContent = player

        var id = button.getAttribute('data-player-id')
        var confirm = this.querySelector('#delete_confirm')
        confirm.setAttribute('value', id)
    });
    $('#delete_player_confirm_modal #delete_confirm').click(function(){

    });

});
