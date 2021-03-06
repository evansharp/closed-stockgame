<h2>Update Stock Prices</h2>
	<form action="admin" method="post">
	<table class="pure-table">
		<thead>
			<tr>
				<th></th>
				<?php foreach($stocks as $stock){
					echo "<td>" . $stock['code'] . "</td>";
				}?>
			</tr>
		</thead>
		<tbody>
			<tr>
				<th>Current Market Price</th>
			<?php foreach($stocks as $stock){
					foreach($stock_prices as $price){
						if($price['stock_id'] == $stock['stock_id']){
							echo "<td> $" . $price['price'] . "</td>";
						}
					}
				}?>
			</tr>
			<tr>
				<th>Market Cap</th>
			<?php foreach($stocks as $stock){
					echo "<td> ". $stock['num_shares'] ." / " . $stock['total_shares'] . "</td>";
				}?>
			</tr>
			<tr>
				<th>Price Adjust</th>
				<?php foreach($stocks as $stock){
					echo '<td>
								<input type="hidden" name="update_stock_price['. $stock['code'] .'][id]" value="'. $stock['stock_id'].'">
								<input type="text" name="update_stock_price['. $stock['code'] .'][price]" class="stock_update_field" required>
								</td>';
				}?>
			</tr>
		</tbody>

	</table>
	<button type="submit"> Save New Prices</button>
	</form>
<hr>

<h2>Segments</h2>
<table class="pure-table">
			<thead>
				<tr>
					<th>Id</th>
					<th>Name</th>
					<th>Volitility Coefficient</th>
					<th colspan="2">Actions</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach($segments as $seg): ?>
					<tr>
						<form action="<?php echo base_url() . 'admin'; ?>" method="post">
						<input type="hidden" name="edit_segment_id" value="<?php echo $seg['segment_id'];?>">
						<td><?php echo $seg['segment_id'];?></td>
						<td><input type="text" name="edit_segment_name" value="<?php echo $seg['segment_name'];?>"></td>
						<td><input type="text" name="edit_segment_vol" value="<?php echo $seg['segment_volitility'];?>"></td>
						<td><button type="submit">Save Changes</button></td>
						</form>
						<td>
							<form action="<?php echo base_url() . 'admin'; ?>" method="post">
							<input type="hidden" name="delete_segment_id" value="<?php echo $seg['segment_id'];?>">
							<button type="submit">Delete</button>
							</form>
						</td>

					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>

<div class="pure-g">
	<form action="<?php echo base_url() . 'admin'; ?>" method="post">
	<div class="pure-u-2-3"><input type="text" name="add_segment_name" placeholder="New Segment Name"></div>
	<div class="pure-u-1-3"><button type="submit"> Create Segment</button></div>
	</form>
</div>

<hr>

<h2> Stocks </h2>
<table class="pure-table">
			<thead>
				<tr>
					<td>Id</td>
					<td>Name</td>
					<td>Segment (id#)</td>
					<td>Market Cap</td>
					<td>Action</td>
				</tr>
			</thead>
			<tbody>
				<?php foreach($stocks as $stock): ?>
					<tr>

						<td><?php echo $stock['stock_id'];?></td>
						<td><?php echo $stock['name'];?></td>
						<td><?php echo $stock['segment_name']?> (<?php echo $stock['segment_id'];?>)</td>
						<td>
							<form action="<?php echo base_url() . 'admin'; ?>" method="post">
							<input type="text" size="8" name="edit_market_cap" value="<?php echo $stock['total_shares'];?>"></td>
						<td>

								<input type="hidden" name="edit_stock_id" value="<?php echo $stock['stock_id'];?>">
								<button type="submit">Save Change</button>
							</form>

							<form action="<?php echo base_url() . 'admin'; ?>" method="post">
								<input type="hidden" name="delete_stock_id" value="<?php echo $stock['stock_id'];?>">
								<button type="submit">Delete</button>
							</form>

						</td>
						</form>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
<div class="pure-g">

	<form action="<?php echo base_url() . 'admin'; ?>" method="post">

	<div class="pure-u-1-4"><input type="text" name="add_stock_name" placeholder="New Stock Name"></div>
	<div class="pure-u-1-4"><input type="text" name ="add_stock_code" placeholder="Code"></div>
	<div class="pure-u-1-4">
		<select name="add_stock_segment">
			<?php foreach($segments as $seg):
				echo "<option value='".$seg['segment_id']."'> ".$seg['segment_name']." </option>";
			endforeach; ?>
		</select>
	</div>
	<div class="pure-u-1-8"><input type="text" name ="add_stock_initnumshares" placeholder="# of Shares"></div>
	<div class="pure-u-1-8"><input type="text" name ="add_stock_initprice" placeholder="IPO"></div>

	<div class="pure-u-1"><button type="submit"> Create Stock</button></div>
	</form>

	<div class="pure-u-1">
		<h3>Prospectus</h3>
		<form action="<?php echo base_url() . 'admin'; ?>" method="post">
			<div class="pure-g">
				<div class="pure-u-1-4">
					<select name="edit_prospectus_stock">
						<?php foreach($stocks as $stock){
							echo "<option value='". $stock['stock_id'] ."'>". $stock['code'] ."</option>";
						}?>
					</select>
				</div>
				<div class="pure-u-3-4"><textarea name="edit_prospectus_text" id="prospectus_field"></textarea></div>
				<div class="pure-u-1"><button type="submit"> Save Prospectus</button></div>
			</div>
		</form>
	</div>
</div>

<hr>

<h2>Settings</h2>

<div class="pure-g">
	<form action="<?php echo base_url() . 'admin'; ?>" method="post">

	<div class="pure-u-1-3">
		<input type="radio" name="set_setting_game_on" value="yes" <?php if($game_on == "yes"){ echo "checked"; }?>> Online<br>
  	<input type="radio" name="set_setting_game_on" value="no" <?php if($game_on == "no"){ echo "checked"; }?>> Offline<br>
	</div>
	<div class="pure-u-1-3">Game Online</div>
	<div class="pure-u-1-3"></div>

	<div class="pure-u-1"><button type="submit"> Save Settings</button></div>
	</form>

	<form action="<?php echo base_url() . 'admin'; ?>" method="post">
	<div class="pure-u-1"><h3>Reset Game</h3></div>

	<div class="pure-u-1-3">
		<input type="hidden" name="reset" value="tsarbomba">
		<button type="submit" class="button-red">Reset Game</button>
	</div>
	<div class="pure-u-1-3"></div>
	<div class="pure-u-1-3"></div>
	</form>
</div>
