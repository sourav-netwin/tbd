<!-- /.row -->
<div class="row search_filter_container">
    <div class="col-lg-12">
        <div class="col-xs-7 col-md-7 col-sm-7">
            <div class="form-group pull-left">
                <label for="search">Search:</label>
                <input type="text" class="form-control" id="search_filter_ed" name="search_filter_ed">
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="table-responsive">
            <table id="specials-edit-table" class="table table-bordered table-hover table-striped dataTables">
                <thead>
                    <tr>
                        <th width="40%">Product Name</th>
                        <!--<th width="20%">Retailer</th>-->
                        <!--<th width="20%">Store</th>-->
                        <th width="5%">Default Price</th>
                        <th width="5%">Offer Price</th>
                        <th width="7%">Quantity</th>
                        <th width="2%"></th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
    <div class="form-group">
        <div class="row">
            <div class="col-xs-10 col-xs-offset-1">
                <button type="button" class="btn btn-primary btn-xs block full-width m-b" id="update_spl_prd">Update</button>
                <a class="btn btn-danger btn-xs block full-width m-b" href="<?php echo site_url('/specialmanagement'); ?>">Cancel</a>
            </div>
        </div>
    </div>
    
    <?php if($specialStoreNames) { /*?>
       <div class="row">
        <div class="col-lg-12" style="padding:40px;">
                <b> From Stores </b><br>
                <?php echo $specialStoreNames; ?>
            </div>
        </div>
    <?php */ } //if($specialStoreNames)  ?>
        
    <div class="row">
        <div class="col-lg-12" style="padding-left:30px;padding-right:30px;">
            <h3> From Stores </h3>
            <?php foreach ($specialStores as $specialStore){ ?>
            <div class="col-xs-3" style="border:1px solid #ccc; height:30px;padding:5px;">
                <?php echo $specialStore; ?>
            </div>   
            <?php } //foreach ($specialStores as $specialStore) ?>
        </div>
     </div>
    
</div>
<input type="hidden" id="spec_sel" value="<?php echo $special_id; ?>">