<h1>Your Trade History</h1>

<div class="pure-g">
	<div class="pure-u-1">
		<table class="pure-table">
			<thead>
				<tr>
					<td>Time</td>
					<td>Stock</td>
					<td>Trade</td>
					<td>Price at the Time</td>
					<td>Trade Value</td>
				</tr>
			</thead>
			<tbody>
				<?php foreach($history as $tx){
					echo "<tr>";
					echo "<td>" . $tx['time'] . "</td>";
					echo "<td>" . $tx['code'] . "</td>";
					echo "<td>" . $tx['trade'] . "</td>";
					echo "<td>$ " . $tx['price'] . "</td>";
					echo "<td>$ " . $tx['trade_val'] . "</td>";
					echo "</tr>";
				}?>
			</tbody>
		</table>	
	</div>
</div>

<!--
<pre>
	<?php
	echo "history<br>";
	var_dump( $history ); ?>
</pre>-->