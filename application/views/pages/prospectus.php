<div class="container">
<div class="row">
<?php foreach($prospecti as $k => $prospectus):
	if($k % 2 == 0){
  	echo '<div class="col-sm-6">';
    }
	?>
  <div class="card prospectus left">
      <div class="card-body">
		  <h5 class="mb-0">
	          <?php echo $prospectus['name'];?> (<?php echo $prospectus['code'];?>)
		</h5>
        <p><?php echo $prospectus['prospectus'];?></p>
		<aside class="text-muted">Sector: <?php echo $prospectus['segment_name']?></aside>
      </div>
  </div>
<?php
  if($k % 2 != 0){
	echo "</div>";
  }
 	endforeach;?>
</div>
</div>
