<h1>Tickers</h1>

<div class="container">
	<div class="row justify-content-lg-center">
		<div class="col-10">
			<div id="ticker_container">
				<canvas></canvas>
				<a href="#" class="btn btn-outline-secondary btn-sm float-right">Reset Zoom</a>
			</div>
		</div>
	</div>
</div>

<script>
	//pass datasets from php to js for chart-drawing with Chartjs
	var ticker_all = <?php echo json_encode( $ticker_all ); ?>;
</script>
