<h1>Your Stock Portfolio</h1>

<div class="pure-g">
	<div class="pure-u-1">
		<div class="ct-chart ct-major-tenth" id="portfolio_chart"></div>
	</div>

	<div class="pure-u-1-4">
		<p class="center legend bank_legend">Bank Balance: <b>$ <?php echo number_format($bank_value,2); ?></b></p>
	</div>
	<div class="pure-u-1-4">
		<p class="center legend portfolio_legend">Portfolio Value: <b>$ <?php echo number_format($portfolio_value,2); ?></b></p>
	</div>
	<div class="pure-u-1-4">
		<p class="center legend total_legend">Net Worth: <b>$ <?php echo number_format($total_value,2); ?></b></p>
	</div>

	<div class="pure-u-1-4">
		<p class="center legend">Number of Trades: <b><?php echo $trade_count; ?></b></p>
	</div>

	<div class="pure-u-3-5">
		<table class="pure-table stocks_table">
			<thead>
				<tr>
					<td>Stock</td>
					<td>Sector</td>
					<td>Current Price</td>
					<td>You Own</td>
					<td>Current Value</td>
				</tr>
			</thead>
			<tbody>
				<?php foreach($portfolio as $stock){
					echo "<tr>";
					echo "<td>" . $stock['code'] . "</td>";
					echo "<td>" . $stock['segment'] . "</td>";
					echo "<td>$ " . number_format($stock['price'],2) . "</td>";
					echo "<td>" . $stock['owned'] . "</td>";
					echo "<td>$ " . number_format($stock['value'],2) . "</td>";
					echo "</tr>";
				}?>
			</tbody>
		</table>
	</div>
	<div class="pure-u-2-5">
		<h3 class="center">Value Breakdown</h3>
		<div class="ct-chart ct-perfect-fourth" id="portfolio_segments_chart"></div>
	</div>

</div><!-- end page grid -->

<!--
<pre>
	<?php
	echo "update series<br>";
	var_dump( $updates_series ); ?>
	<hr>
	<?php
	echo "bank series<br>";
	var_dump( $bank_series ); ?>
	<hr>
	<?php
	echo "portfolio series<br>";
	var_dump( $portfolio_series ); ?>
	<hr>
	<?php
	echo "total series<br>";
	var_dump( $total_series ); ?>
	<hr>
	<?php
	echo "portfolio obj<br>";
	var_dump( $portfolio ); ?>
</pre>
-->

<script>
	var bank_series = <?php echo json_encode($bank_series); ?>;
	var portfolio_series = <?php echo json_encode($portfolio_series); ?>;
	var total_series = <?php echo json_encode($total_series); ?>;
	var portfolio = <?php echo json_encode($portfolio); ?>;
	var updates_series = <?php echo json_encode($updates_series); ?>;
	var game_start = "<?php echo $game_start; ?>";
</script>
