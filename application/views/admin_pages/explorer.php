<main class="col-md-10" style="display: <?php echo $admin_nav == 'explorer' ? 'block':'none'; ?>">
    <div class="justify-content-between align-items-center">
        <h1>Player Explorer</h1>
    </div>

    <table id="portfolio_explorer" class="table data_table">
        <thead>
            <tr>
                <th scope="col"> Player Name </td>
                <th scope="col"> Bank Balance </td>
                <th scope="col"> Portfolio Worth </td>
                <th scope="col"> Net Worth </td>
                <th scope="col"> Trades </td>
                <th scope="col"> Last Trade </td>
                <th scope="col"> Portfolio </td>
            </tr>
        </thead>
        <tbody>

            <?php
                foreach($explorer as $player => $data){
                    echo "<tr>";
                        //name
                        echo "<td>".$data['name']."</td>";

                        //bank balance
                        echo "<td> $". number_format($data['bank_balance'],2) ."</td>";

                        //Portfolio Worth
                        $stocks_worth = 0;
                        foreach( $data['portfolio'] as $stock ){
                            $stocks_worth += $stock['value'];
                        }
                        echo "<td> $". number_format($stocks_worth ,2) ."</td>";

                        //net Worth
                        $worth = $data['bank_balance'] + $stocks_worth;
                        echo "<td> $". number_format($worth,2)." </td>";

                        //trades
                        echo "<td>". $data['num_trades'] ."</td>";

                        //last trade
                        echo "<td>". $data['last_trade'] ."</td>";

                        //portfolio
                        echo '<td><button type="button" class="btn btn-primary" data-toggle="modal" data-target="#portfolio-explorer-'. $data['id'] .'">Show</button></td>';
                    echo "</tr>";
                }
            ?>
        </tbody>
    </table>
</main>

<?php
// ---------------------------- Portfolio explorer modal ---------------->

foreach($explorer as $player => $data):?>

	<div class="modal fade" id="portfolio-explorer-<?php echo $data['id'];?>" role="dialog" tabindex="-1">

		<div class="modal-dialog modal-lg">

			<div class="modal-content">

				<div class="modal-header">
					<h4><?php echo $data['name'];?>'s Porftolio</h4>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
	          			<span aria-hidden="true">&times;</span>
	        		</button>
	      		</div>

				<div class="modal-body">
					<table class="table data_table table-striped table-hover">
						<thead>
							<tr>
								<td>Stock</td>
								<td>Current Price</td>
								<td>They Own</td>
								<td>Current Value</td>
							</tr>
						</thead>
						<tbody>
							<?php foreach($data['portfolio'] as $stock){
								echo "<tr>";
								echo "<td>" . $stock['code'] . "</td>";
								echo "<td>$ " . number_format($stock['price'],2) . "</td>";
								echo "<td>" . $stock['owned'] . "</td>";
								echo "<td>$ " . number_format($stock['value'],2) . "</td>";
								echo "</tr>";
							}?>
						</tbody>
					</table>

					<h4> Trade History </h4>

					<table class="table data_table table-striped table-hover">
						<thead>
							<tr>
								<td> Datetime </td>
								<td> Stock </td>
								<td> Tx Price</td>
								<td> Trade </td>
								<td> Trade Value</td>
							</tr>
						</thead>
						<tbody>
							<?php foreach($data['history'] as $trade){
								$code = '';
								foreach($stocks as $rec){
									if($rec['stock_id'] == $trade['stock_id']){
										$code = $rec['code'];
									}
								}

								echo "<tr>";
								echo "<td>" . date("F j, Y, g:i a", $trade['timestamp']) . "</td>";
								echo "<td>" . $code . "(" . $trade['stock_id'] . ")</td>";
								echo "<td>$ " . number_format($trade['tx_price'],2) . "</td>";
								echo "<td>" . $trade['tx'] . "</td>";
								echo "<td>$ " . number_format($trade['tx'] * $trade['tx_price'], 2) . "</td>";
								echo "</tr>";
							}?>
						</tbody>
					</table>

					<pre>
						<?php //print_r($data['history']); ?>
					</pre>

					<h4> Portfolio History </h4>

					<table class="table data_table table-striped table-hover">
						<thead>
							<tr>
								<td> Datetime </td>
								<td> Action </td>
								<td> Bank Balance </td>
								<td> Portfolio Value</td>
								<td> Portfolio Value Change </td>
								<td> Net Worth </td>


							</tr>
						</thead>
						<tbody>

							<?php
							$last_value = 0.0;

							foreach($data['portfolio_history'] as $snapshot){
								$action_result = '';
								$cash = $snapshot['net_worth'] - $snapshot['portfolio_value'];
								$value_delta= 0.0;

								if($last_value < $snapshot['portfolio_value']){
									// portfolio value increased
									$value_delta = $snapshot['portfolio_value'] - $last_value;
									if($snapshot['action'] == 'update')
										$action_result = "increase";
								}else{
									// portfolio value decreased

									$value_delta = $snapshot['portfolio_value'] - $last_value;
									if($snapshot['action'] == 'update')
										$action_result = "decrease";
								}

								echo "<tr>";
								echo "<td>" . date("F j, Y, g:i a", $snapshot['timestamp']) . "</td>";
								echo "<td class='post_hist_act-". $snapshot['action'] ."'> ". $snapshot['action'] . "</td>";
								echo "<td>$ " . number_format($cash, 2) . "</td>";
								echo "<td>$" . number_format($snapshot['portfolio_value'], 2) . "</td>";
								echo "<td class='post_hist_delta-". $action_result ."'>$ " . number_format($value_delta, 2) . "</td>";
								echo "<td>$ " . number_format($snapshot['net_worth'], 2) . "</td>";

								echo "</tr>";
								$last_value = $snapshot['portfolio_value'];
							}?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
<?php endforeach;?>
