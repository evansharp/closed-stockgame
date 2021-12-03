<main class="col-md-10">
    <div class="justify-content-between align-items-center">
        <h1>Auto Updates</h1>
    </div>
    <div class="container">
        <div class="row">
            <div class="col-4" id="updates_template_editor_actions">
                <button type="button" id="updates_template_editor_action_add" class="btn btn-success">Add</button>
                <button type="button" id="updates_template_editor_action_delete" class="btn btn-danger" data-toggle="modal" data-target="#confirm_update_delete"
                data-target-update="">Delete</button>
            </div>
            <div class="col-5 text-right align-middle"><span id="updates_template_editor_selected"></span></div>
            <div class="col-3 ">
                <div class="btn-group" id="updates_template_editor_selector">
                  <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Stock to Edit
                  </button>
                  <div class="dropdown-menu">
                      <?php foreach($stocks as $stock){
                          echo '<a class="dropdown-item" href="#" data-stock="'.$stock['stock_id'].'">'.$stock['name'].' ('. $stock['code'].')</a>';
                    }?>
                  </div>
                </div>
            </div>
        </div>
        <div class="row justify-content-lg-center">
    		<div class="col-10">
    			<div id="auto_update_stock_chart_container">
    				<canvas></canvas>
    			</div>
    		</div>
    	</div>
        <div class="row">
            <div class="col">
                <table id="updates_template_editor" class="table table-striped table-hover"></table>
            </div>
        </div>
    </div>
</main>

<?php // ---------------------------- Add Auto Update modal ----------------> ?>

<div class="modal fade" id="add_auto_update" tabindex="-1" role="dialog" aria-labelledby="add_auto_update_title" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="add_auto_update_title">Add an Auto Update</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">

			<div class="container">
				<div class="row">
					<div class="col">
						<p>New Update After: <span class="text-center" id="insertPoint_after"></span></p>
					</div>
				</div>
				<div class="row">
					<div class="col">
						<input type="range" name="insertPoint" min="0" max="2">
					</div>
				</div>
			</div>
            <div class="row">

                <div class="container auto_update_price_table">
                    <div class="row justify-content-lg-center">
                        <div class="col-2 font-weight-bold">
                            Stock
                        </div>
                        <div class="col-2 text-center font-weight-bold">
                            Price Before
                        </div>
                        <div class="col-2 text-center font-weight-bold">
                            New Price
                        </div>
                        <div class="col-2 text-center font-weight-bold">
                            Price After
                        </div>
                    </div>
                    <form action="<?php echo base_url();?>admin/auto_updates" method="post" id="new_prices">
                        <input type="hidden" name="update_after" value="">
                    <?php foreach($stocks as $i => $stock):?>
    				<div class="row justify-content-lg-center"
                        data-update-stock-id="<?php echo $stock['stock_id'];?>">
                        <div class="col-2">
                            <?php echo $stock['code'];?>
                        </div>
    					<div class="col-2 text-center auto_update_price_before">
                        </div>
                        <div class="col-2 input-group auto_update_price_new">
                            <div class="input-group-prepend">
                                <div class="input-group-text">$</div>
                            </div>
                            <input type="text" class="form-control text-center"
                                name="new_auto_update_prices[<?php echo $stock['stock_id']?>]" required>
                        </div>
                        <div class="col-2 text-center auto_update_price_after">
                        </div>
                    </div>
                    <?php endforeach;?>
                    </form>
                </div>
            </div>
      </div>
      <div class="modal-footer">

        <button type="submit" form="new_prices" class="btn btn-primary" id="add_new_update_button">
            Add New Update
        </button>
        <button class="btn btn-secondary" data-dismiss="modal">Close</button>

      </div>
    </div>
  </div>
</div>
<?php // ---------------------------- Confirm Delete Auto Update modal ----------------> ?>
<div class="modal fade" id="confirm_update_delete" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                Confirm Delete Auto-Update #<span></span>
            </div>
            <div class="modal-body">
                <div class="container">
                    <?php foreach($stocks as $i => $stock):?>
                    <div class="row" data-stock-id="<?php echo $stock['stock_id'];?>">
                        <div class="col-4"><?php echo $stock['code'];?></div>
                        <div class="col-8 price"></div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="modal-footer">
                <form action="<?php echo base_url();?>admin/auto_updates" method="post">
                    <input type="hidden" name="target_update_num" value="">
                    <button class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger" id="delete_update_button">Delete</a>
                </form>
            </div>
        </div>
    </div>
</div>
