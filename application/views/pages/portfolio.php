<h1>My Stock Portfolio</h1>
<div class="container">
	<div class="row justify-content-lg-center">
		<div class="col-10">
			<div id="portfolio_chart_container">
				<canvas></canvas>
				<a href="#" class="btn btn-outline-secondary btn-sm float_right">Reset Zoom</a>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col">
			<p class="center legend bank_legend">Bank Balance:
				<b>$ <?php echo number_format($bank_value,2); ?></b>
			</p>
		</div>
		<div class="col">
			<p class="center legend portfolio_legend">Portfolio Value:
				<b>$ <?php echo number_format($portfolio_value,2); ?></b>
			</p>
		</div>
		<div class="col">
			<p class="center legend total_legend">Net Worth:
				<b>$ <?php echo number_format($total_value,2); ?></b>
			</p>
		</div>
		<div class="col">
			<p class="center legend">Number of Trades:
				<b><?php echo $trade_count; ?></b>
			</p>
		</div>
	</div>
	<div class="row">
		<div class="col-6">
			<table class="table table-hover">
				<thead>
					<tr>
						<th>Stock</td>
						<th>Sector</td>
						<th>Current Price</td>
						<th>You Own</td>
						<th>Current Value</td>
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

		<div class="col-6">
			<h4 class="center">Portfolio Composition</h4>
			<div id="portfolio_comp_chart_container">
				<canvas></canvas>
		</div>
	</div>
</div>

<script>
	var bank_series = <?php echo json_encode($bank_series); ?>;
	var portfolio_series = <?php echo json_encode($portfolio_series); ?>;
	var total_series = <?php echo json_encode($total_series); ?>;
	var portfolio = <?php echo json_encode($portfolio); ?>;
</script>
