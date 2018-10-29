<h1>Class Investment Leaderboard</h1>

<div class="pure-g">
	<div class="pure-u-1">
		<table class="pure-table">
			<thead>
				<tr>
					<td>Name</td>
					<td>Net Worth</td>
					<td>Number of Trades</td>
				</tr>
			</thead>
			<tbody>
				<?php
					foreach($board as $i => $investor){
						if($investor['name'] == "Evan Sharp"){
							continue;
						}
						if($i == 0){
							$investor['name'] .= '  <i class="fa fa-trophy" aria-hidden="true"></i>'; 
						}
						echo "<tr>";
						echo "<td>". $investor['name'] ."</td>";
						echo "<td>$ ". $investor['worth'] ."</td>";
						echo "<td>". $investor['txs'] ."</td>";
						echo "</tr>";
					}?>
			</tbody>
		</table>
	</div>
</div>
