
<?php

// chuck the stock prices arrays for the two-column layout
$num_per_chunk = round( count($stocks) / 2, 0, PHP_ROUND_HALF_DOWN);
$stock_chunks = array_chunk( $stocks, $num_per_chunk, true);
$price_chunks = array_chunk( $stock_prices, $num_per_chunk, true);
 ?>


<h1>Buy & Sell Stocks</h1>

<div class="container">
	<div class="row">
		<div class="col-6">
			<h4 class="center">Current Stock Prices</h4>
		</div>
	</div>
	<div class="row">
		<div class="col-3">
			<ul class="list-group list-group-flush">
				<?php
					foreach($stock_chunks[0] as $i => $stock){
						echo '<li class="list-group-item">' . $stock['code'] . "<span>$ " . number_format( $price_chunks[0][$i]['price'], 2) . '<i class="fas fa-angle-double-right trend-' . $price_chunks[0][$i]['trend'] . '"></i></span></li>';
				}?>

			</ul>
		</div>
		<div class="col-3">
			<ul class="list-group list-group-flush">
				<?php
					foreach($stock_chunks[1] as $i => $stock){
						echo '<li class="list-group-item">' . $stock['code'] . "<span>$ " . number_format( $price_chunks[1][$i]['price'], 2) . '<i class="fas fa-angle-double-right trend-' . $price_chunks[1][$i]['trend'] . '"></i></span></li>';
				}?>

			</ul>
		</div>

		<div class="col-6">
			<div id="bank_hist_container">
				<canvas></canvas>
			</div>

            <div class="alert alert-info" role="alert">
			    Your Current Bank Balance is: $ <b><?php echo number_format($bank_balance, 2); ?></b>
            </div>

			<div class="row">
      			<div class="col-6">
					<h5>Buy Stocks</h5>
					<form action="buysell" method="post">
						<div class="form-row">
							<div class="col-4">
								<input type="number" class="form-control" name="buy_num_stock" min="0" step="1" required placeholder="Qty.">
							</div>
							<div class="col-8">
								<select name="buy_which_stock" class="form-control">
									<option selected disabled>Stock</option>
									<?php
									foreach($stocks as $stock){
										echo "<option data-max='". $max_buy[$stock['stock_id']] ."' value='".$stock['stock_id']."'>" . $stock['code'] . "</option>";
									}
									?>
								</select>
							</div>

						</div>
						<div class="form-row">
							<div class="col-12">

								<button type="submit" class="btn btn-success float_right">Buy</button>
								<button id="buymax" class="btn btn-secondary float_right">Max</button>
							</div>
						</div>
					</form>
      			</div>
      			<div class="col-6">

					<h5>Sell Stocks</h5>

					<form action="buysell" method="post">
						<div class="form-row">

							<div class="col-4">
								<input type="number" class="form-control" name="sell_num_stock" min="1" step="1" required placeholder="Qty.">
							</div>

							<div class="col-8">

								<select name="sell_which_stock" class="form-control">
									<option selected disabled>Stock (Owned)</option>
									<?php
									foreach($portfolio_stocks as $stock){
										echo "<option data-all='".$stock['num_owned']."' value='".$stock['stock_id']."'>" . $stock['code'] . " (" . $stock['num_owned'] . ")</option>";
									}
									?>
								</select>
							</div>
						</div>
						<div class="form-row">
							<div class="col-12">
								<button type="submit" class="btn btn-danger float_right">Sell</button>
								<button id="sellall" class="btn btn-secondary float_right">All</button>
							</div>
						</div>
					</form>
		      </div>
		    </div>

			<?php if( isset($result[0]) ):?>
			<div class="row">
				<div class="col-12">

						<div class="alert alert-<?php echo $result[0];?> tx_result" role="alert">
							<?php echo $result[1];?>
						</div>

				</div>
			</div>
			<?php endif; ?>

		</div>
	</div>
</div>



<script>
	//pass dataset from php to js for chart-drawing with Chartjs
	var bank_hist = <?php echo json_encode( $bank_history ); ?>;
</script>
