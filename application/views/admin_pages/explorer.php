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
                <th scope="col"> Actions </td>
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

                        //actions
                        echo '<td>

                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#portfolio-explorer-'. $data['id'] .'">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-zoom-in" viewBox="0 0 16 16">
  <path fill-rule="evenodd" d="M6.5 12a5.5 5.5 0 1 0 0-11 5.5 5.5 0 0 0 0 11zM13 6.5a6.5 6.5 0 1 1-13 0 6.5 6.5 0 0 1 13 0z"/>
  <path d="M10.344 11.742c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1 6.538 6.538 0 0 1-1.398 1.4z"/>
  <path fill-rule="evenodd" d="M6.5 3a.5.5 0 0 1 .5.5V6h2.5a.5.5 0 0 1 0 1H7v2.5a.5.5 0 0 1-1 0V7H3.5a.5.5 0 0 1 0-1H6V3.5a.5.5 0 0 1 .5-.5z"/>
</svg>
                        </button>

    <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#delete_player_confirm_modal" data-playername="'.$data['name'].'" data-player-id="'.$data['id'].'">

                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-person-x-fill" viewBox="0 0 16 16">
  <path fill-rule="evenodd" d="M1 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1H1zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm6.146-2.854a.5.5 0 0 1 .708 0L14 6.293l1.146-1.147a.5.5 0 0 1 .708.708L14.707 7l1.147 1.146a.5.5 0 0 1-.708.708L14 7.707l-1.146 1.147a.5.5 0 0 1-.708-.708L13.293 7l-1.147-1.146a.5.5 0 0 1 0-.708z"/>
</svg>
                        </button>
                        </td>';
                    echo "</tr>";
                }
            ?>
        </tbody>
    </table>
</main>
<?php
// ---------------------------- Delete User Confirm modal ---------------->?>
<div class="modal fade delete_player_confirm_modal" id="delete_player_confirm_modal" role="dialog" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Confirm Delete Player</h3>
            </div>
            <div class="modal-body">
                Are you sure you want to permeneantly and completely delete <b><span></span></b> from the game?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <form action="explorer" method="post">
                    <button type="submit" class="btn btn-danger btn-ok" id="delete_confirm" name="delete_user" value="">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>


<?php
// ---------------------------- Portfolio explorer modal ---------------->
foreach($explorer as $player => $data):?>

	<div class="modal fade portfolio_explorer_modal" id="portfolio-explorer-<?php echo $data['id'];?>" role="dialog" tabindex="-1">

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
