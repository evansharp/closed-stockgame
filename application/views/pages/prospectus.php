<h1>Stocks Prospectus</h1>
<?php foreach($prospecti as $prospectus):?>
<div class="prospectus">
	<h3><?php echo $prospectus['name'];?> (<?php echo $prospectus['code'];?>)</h3>
	<p><?php echo $prospectus['prospectus'];?></p>
	<aside>Sector: <?php echo $prospectus['segment_name']?></aside>
	
	
</div>
<?php endforeach;?>

<!--
<pre>
	<?php
	var_dump($prospecti);?>
</pre>-->