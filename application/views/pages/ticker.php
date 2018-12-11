<h1>Tickers</h1>

<div class="pure-g">
	<div class="pure-u-1">
		<div class="ct-chart ct-major-tenth" id="chart_ticker_all"></div>
	</div>

	<?php
		$count = 0;
		foreach($ticker_segments as $key => $segment){
			//top level key is segment_name from Ticker controller
		//	echo '<pre>'; print_r($key); echo '<br>'; print_r($segment); echo '</pre>';
			echo '<div class="pure-u-1-2">';
			echo '<h3>'. $key .'</h3>';
			echo '<div class="ct-chart ct-perfect-fourth" id="chart_ticker_' . $count . '"></div>';
			echo '</div>';

		$count++;
		}
	?>
</div>

<script>
	//pass data sets from php to js for chart-drawing with Chartist
	var ticker_all = <?php echo json_encode($ticker_all); ?>;
	console.log(ticker_all);

	var ticker_segments = [];
	<?php foreach($ticker_segments as $seg): ?>

	ticker_segments.push(<?php echo json_encode($seg); ?>);

	<?php endforeach; ?>
</script>
