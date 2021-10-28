<main class="col-md-10" style="display: <?php echo $admin_nav == 'stocks' ? 'block':'none'; ?>">
    <div class="justify-content-between align-items-center">
        <h1>Stocks Managment</h1>
    </div>
    <h2> Manually Update Stock Prices </h2>

    <form action="<?php echo base_url();?>admin/stocks" method="post" class="form-inline">

    <?php
        $num_per_chunk = round( count($stocks) / 3, 0, PHP_ROUND_HALF_DOWN);
        $stock_chunks = array_chunk( $stocks, $num_per_chunk, true);
        $price_chunks = array_chunk( $stock_prices, $num_per_chunk, true);
        ?>

    <div class="row">
        <?php foreach($stock_chunks as $g => $chunk): ?>
        <div class="col-4">
            <ul class="list-group list-group-flush">
                <?php
                    foreach($chunk as $i => $stock){
                        echo '<li class="list-group-item">';
                        echo  $stock['code']. " (id: ". $stock['stock_id'] .")";
                        echo '<input type="hidden" name="update_stock_price['. $stock['code'] .'][id]" value="'. $stock['stock_id'].'">
                            <input type="text" name="update_stock_price['. $stock['code'] .'][price]" class="stock_update_field form-control" required value="'.number_format( $price_chunks[$g][$i]['price'], 2).'">';
                        echo "</li>";
                }?>

            </ul>
        </div>
        <?php endforeach;?>
    </div>
    <div class="row">
        <div class="col-4">
            <button type="submit" class="btn btn-primary">Save Prices</button>
        </div>
    </div>
    </form>

    <h2>Edit Segments</h2>
    <table class="table">
        <thead>
            <tr>
                <td>Id</td>
                <td>Name</td>
                <td>Is Comodity?</td>
                <td>Action</td>
            </tr>
        </thead>
        <tbody>
            <?php foreach($segments as $seg): ?>
                <tr>
                    <form action="<?php echo base_url()?>admin/stocks" method="post">

                    <td><?php echo $seg['segment_id'];?></td>
                    <td><?php echo $seg['segment_name'];?></td>
                    <td><?php if($seg['is_comodity']){ echo '<i class="fa fa-lg fa-check"></i>'; } ?></td>
                    <td>
                        <input type="hidden" name="delete_segment_id" value="<?php echo $seg['segment_id'];?>">
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </td>
                    </form>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <form action="<?php echo base_url()?>admin/stocks" method="post">
    <div class="row">
        <div class="col-5"><input type="text" name="add_segment_name" placeholder="New Segment Name" class="form-control"></div>
        <div class="col-3"><input type="checkbox" name="is_comodity" class="form-check-input"> <label for="is_comodity">Comodity?</label></div>
        <div class="col-4 text-right"><button type="submit" class="btn btn-primary"> Create Segment</button></div>
    </div>
    </form>

    <h2> Edit Stocks </h2>
    <table class="table table-stripped">
        <thead>
            <tr>
                <td>Id</td>
                <td>Name</td>
                <td>Segment (id#)</td>
                <td>Action</td>
            </tr>
        </thead>
        <tbody>
            <?php foreach($stocks as $stock): ?>
                <tr>
                    <form action="<?php echo base_url()?>admin/stocks" method="post">
                    <td><?php echo $stock['stock_id'];?></td>
                    <td><?php echo $stock['name'];?></td>
                    <td><?php echo $stock['segment_name']?> (<?php echo $stock['segment_id'];?>)</td>
                    <td>
                        <input type="hidden" name="delete_stock_id" value="<?php echo $stock['stock_id'];?>">
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </td>
                    </form>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <form action="<?php echo base_url()?>admin/stocks" method="post">
    <div class="form-row">
        <div class="form-group col-md-6">
            <input type="text" class="form-control" name="add_stock_name" placeholder="New Stock Name">
        </div>
        <div class="form-group col-md-2">
            <input type="text" class="form-control" name ="add_stock_code" placeholder="Code">
        </div>
        <div class="form-group col-md-4">
            <input type="text" class="form-control" name ="add_stock_initprice" placeholder="IPO">
        </div>
    </div>
    <div class="form-row justify-content-between">
        <div class="form-group col-md-4">
            <select class="form-control" name="add_stock_segment">
                <?php foreach($segments as $seg):
                    echo "<option value='".$seg['segment_id']."'> ".$seg['segment_name']." </option>";
                endforeach; ?>
            </select>
        </div>

        <div class="form-group col-md-2 text-right">
            <button type="submit" class="btn btn-primary"> Create Stock </button>
        </div>
    </div>
    </form>

</main>
