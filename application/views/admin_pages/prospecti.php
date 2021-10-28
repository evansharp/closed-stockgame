<main class="col-md-10" style="display: <?php echo $admin_nav == 'prospecti' ? 'block':'none'; ?>">
    <div class="justify-content-between align-items-center">
        <h1>Prospecti</h1>
    </div>
    <form action="<?php echo base_url() . 'admin/prospecti'; ?>" method="post" class="">
    <div class="row">
        <div class="col">
                <textarea name="edit_prospectus_text" id="prospectus_field" class="form-control"></textarea>
        </div>
    </div>
    <div class="row">
        <div clas="col">
            <select name="edit_prospectus_stock" class="form-control">
                <?php foreach($stocks as $stock){
                    echo "<option value='". $stock['stock_id'] ."'>". $stock['code'] ."</option>";
                }?>
            </select>
        </div>
        <div clas="col">
            <button type="submit" class="btn btn-primary"> Save Prospectus</button>
        </div>
    </div>
    </form>
</main>
