<h1 id="buysell">Buy & Sell Stocks</h1>

<div class="pure-g">

	<div class="pure-u-1">
		<h3>Current Market Prices</h3>
		<?php for($i = 0; $i < count($stocks); $i++): ?>

		<table class="pure-table center">
			<thead>
				<tr>
					<th></th>
					<?php foreach($stocks[$i] as $stock){
						echo "<td>" . $stock['code'] . "</td>";
					}?>
				</tr>
			</thead>
			<tbody>
				<tr>
					<th>Current Market Price</th>
				<?php foreach($stocks[$i] as $stock){
						foreach($stock_prices[$i] as $price){
							if($price['stock_id'] == $stock['stock_id']){
								echo "<td> $" . number_format($price['price'],2) . "</td>";
							}
						}
					}?>
				</tr>
				<tr>
					<th>For Sale</th>
				<?php foreach($stocks[$i] as $stock){
						echo "<td> ". $stock['num_shares'] ." / " . $stock['total_shares'] . "</td>";
					}?>
				</tr>
			</tbody>
		</table>

		<?php endfor; ?>
	</div>

	<div class="pure-u-1">
		<p>Your cash balance is: $ <?php echo number_format($bank_balance,2); ?>
		<?php if($result[0] == 'err'):?>
		<span id="buysell_error" class="tx_result"><?php echo $result[1];?></span>
		<?php elseif($result[0] == 'suc'):?>
		<span id="buysell_success" class="tx_result"><?php echo $result[1];?></span>
		<?php endif; ?>
		</p>
	</div>

	<div class="pure-u-1-2">
		<h2>Buy Stocks</h2>
		<div class="pure-g">
			<form action="buysell" method="post">
				<div class="pure-u-1-3">
					<input type="number" name="buy_num_stock" min="0" step="1" required>
				</div>
				<div class="pure-u-1-3">
					<select name="buy_which_stock">
						<?php for($i = 0; $i < count($stocks); $i++){
										foreach($stocks[$i] as $stock){
											if($stock['num_shares'] > 0){
												echo "<option value='".$stock['stock_id']."'>" . $stock['code'] . "</option>";
											}
										}
						}?>
					</select>
				</div>
				<div class="pure-1-3">
					<button type="submit" class="pure-button-primary">Buy</button>
				</div>


			</form>
		</div>
	</div>

	<div class="pure-u-1-2">
		<h2>Sell Stocks</h2>
		<div class="pure-g">
			<form action="buysell" method="post">
				<div class="pure-u-1-3">
					<input type="number" name="sell_num_stock" min="1" step="1" required>
				</div>
				<div class="pure-u-1-3">
					<select name="sell_which_stock">
						<?php
										foreach($portfolio_stocks as $stock){
											echo "<option value='".$stock['stock_id']."'>" . $stock['code'] . " (" . $stock['num_owned'] . ")</option>";
										}
						?>
					</select>
				</div>
				<div class="pure-1-3">
					<button type="submit" class="pure-button-primary">Sell</button>
				</div>


			</form>
		</div>
	</div>

</div>


<pre>
	<?php //print_r($bank_balance); ?>
</pre>
