<!-- /.row -->
<style type="text/css">
    .select2{
        width: 100% !important;
    }
    @media(max-width: 1135px){
        #search_filter{
            width: 110px !important;
        }
    }
</style>
<div class="row search_filter_container">
    <div class="col-lg-12">
        <div class="col-xs-6 col-md-6 col-sm-6">
            <div class="form-group pull-left col-xs-6">
                <label for="search">SEARCH</label>
                <input type="text" class="form-control" id="search_filter" name="search_filter">
            </div>
            <?php
            //Admin Users
            if ($this -> session -> userdata('user_level') < 3) {
                ?>
                <div class="col-xs-6">
                    <select class="form-control search_product_select select-filter" id="retailers" name="retailers">
                        <option value="">Select Retailer</option>
                        <?php
                        if (!empty($retailers)) :
                            foreach ($retailers as $retailer) {
                                echo "<option value='" . $retailer['Id'] . "'>" . $retailer['CompanyName'] . "</option>";
                            }
                        endif;
                        ?>
                    </select>
                </div>
            <?php } ?>
        </div>
        <div class="col-xs-6 col-md-6 col-sm-6 text-right">
            <a class="btn btn-primary btn-xs" href="storeproducts/add"> Add Product to Store</a>
            <a type="button" class="btn btn-primary btn-xs" data-toggle="modal" data-target="#myModal">
                Import Products to Store
            </a>
            <?php
            if ($this -> session -> userdata('user_type') == 6) {
            ?>
            <a type="button" class="btn btn-primary btn-xs" data-toggle="modal" data-target="#myPriceModal">
                Import Price to Store
            </a>
            <?
            }
            ?>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="table-responsive">
            <input type="hidden" id="usertype" name="usertype" value="<?php echo $this -> session -> userdata('user_type'); ?>" />
            <table id="store-products-table" class="table table-bordered table-hover table-striped dataTables">
                <thead>
                    <tr>
                        <th width="35%">Product Name</th>
                        <th width="15%">Retailer Company Name</th>
                        <th width="30%">Store</th>
                        <th width="10%">Price</th>
                        <th width="10%">Actions</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
    <div class="col-lg-10 col-lg-offset-1"><button type="button" class="btn btn-primary btn-xs" id="update_store_price">Update</button></div>
</div>

<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">First download a file to add product prices and then import it</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <?php echo form_open_multipart('storeproducts/export', array('id' => 'export_store_products_form', 'class' => 'form-horizontal')); ?>

                    <div class="col-md-10 col-md-offset-1">
                        <?php
                        //Admin Users
                        if ($this -> session -> userdata('user_level') < 3) {
                            ?>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label for="retailers">Retailers <span>*</span></label>
                                        <select class="form-control search_product_select select-filter" id="retailers_store" name="retailers">
                                            <option value="">Select Retailer</option>
                                            <?php
                                            if (!empty($retailers)) :
                                                foreach ($retailers as $retailer) {
                                                    echo "<option value='" . $retailer['Id'] . "'>" . $retailer['CompanyName'] . "</option>";
                                                }
                                            endif;
                                            ?>
                                        </select>
                                        <div class="error">
                                            <?php echo form_error('retailers'); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>

                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-12">
                                    <label for="product_main_category">Category <span>*</span></label>
                                    <select class="form-control search_product_select select-filter" id="product_main_category" name="product_main_category">
                                        <option value="">Select Main Category</option>
                                        <?php if (!empty($main_categories)) { ?>
                                            <?php foreach ($main_categories as $category) { ?>
                                                <option value="<?php echo $category['Id']; ?>" <?php echo set_select('product_main_category', $category['Id']); ?>><?php echo $category['CategoryName']; ?></option>
                                            <?php } ?>
                                        <?php } ?>
                                    </select>
                                    <div class="error">
                                        <?php echo form_error('product_main_category'); ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <?php
                        //Retailers Users
                        if ($this -> session -> userdata('user_level') <= 3) {
                            ?>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label for="store_formats">Store Formats <span>*</span></label>
                                        <div id="store_formats" name="store_formats" class="checkbox_lists">
                                            <?php
                                            if ($this -> session -> userdata('user_type') == 3) {
                                                $store_format_chunks = array_chunk($store_formats, 2);

                                                foreach ($store_format_chunks as $key => $store_formats_chunk) {

                                                    echo ' <div class="col-md-6">';
                                                    if ($key == 0) {
                                                        echo '<div class="col-md-12"><div class="row">
                                                <div class="checkbox">
                                                    <label>
                                                        <input type="checkbox" name="store_format_list[]" value="0" id="all_store_formats"><label>All Store Formats
                                                    </label>
                                                 </div>
                                            </div>
                                           </div>';
                                                    }
                                                    foreach ($store_formats_chunk as $store_format) {
                                                        echo '  <div class="col-md-12">
                                                <div class="row">
                                                    <div class="checkbox">
                                                        <label>
                                                            <input type="checkbox" name="store_format_list[]" value="' . $store_format['Id'] . '"><label>' . $store_format['StoreType'] . '
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>';
                                                    }

                                                    echo '</div>';
                                                }
                                            }
                                            ?>
                                        </div>
                                        <div class="error">
                                            <?php echo form_error('store_formats'); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>

                        <?php
                        //Retailers Users
                        if ($this -> session -> userdata('user_level') <= 4) {
                            ?>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-12" >
                                        <label for="store_formats">Stores <span>*</span></label>
                                        <div  name="stores" id="stores" class="checkbox_lists">
                                            <?php
                                            if (!empty($stores)) {

                                                $stores_chunks = array_chunk($stores, 2);

                                                foreach ($stores_chunks as $key => $stores_chunk) {

                                                    echo ' <div class="col-md-6">';

                                                    if ($key == 0) {
                                                        echo '<div class="col-md-12"><div class="row">
                                                <div class="checkbox">
                                                    <label>
                                                    <input type="checkbox" name="stores_list[]" value="0" id="all_stores"><label>All Stores
                                                    </label>
                                                 </div>
                                            </div>
                                           </div>';
                                                    }

                                                    foreach ($stores_chunk as $store) {
                                                        echo '  <div class="col-md-12">
                                                <div class="row">
                                                    <div class="checkbox">
                                                        <label>
                                                            <input type="checkbox" name="stores_list[]" value="' . $store['Id'] . '"><label>' . $store['StoreName'] . '
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>';
                                                    }

                                                    echo '</div>';
                                                }
                                            }
                                            ?>
                                            <div class="error">
                                                <?php echo form_error('stores'); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>

                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-12 text-right">
                                    <button type="submit" class="btn btn-primary btn-xs block full-width m-b">Download</button>
                                </div>
                            </div>
                        </div>

                    </div>
                    </form>
                </div>
                <div class="row">
                    <?php echo form_open_multipart('storeproducts/import', array('id' => 'import_store_products_form', 'class' => 'form-horizontal')); ?>
                    <div class="col-md-10 col-md-offset-1">
                        <h4><hr></h4>
                        <h4>Import File</h4>
                        <div class="col-md-5 col-md-offset-1">
                            <div class="btn-group profile_image_group">
                                <label title="Upload excel file" for="inputImage" class="btn btn-primary btn-xs excel_image">
                                    <input type="file" name="import_file" accept="file/*" class="hide" id="inputImage">
                                    <div id="image_text">Upload Excel file</div>
                                </label>
                            </div>
                            <div class="error">
                                <?php echo form_error('import_file'); ?>
                            </div>
                            <div class="form-group">&nbsp;</div>
                            <div class="form-group">&nbsp;</div>
                            <div class="form-group">&nbsp;</div>
                        </div>
                    </div>
                    </form>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary btn-xs block full-width m-b" id="import_store_products">Import</button>
                <button type="button" class="btn btn-danger btn-xs block full-width m-b" data-dismiss="modal" aria-label="Close">Cancel</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="myPriceModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Select the updated file you got from the email</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <?php echo form_open_multipart('storeproducts/importprice', array('id' => 'import_store_price_form', 'class' => 'form-horizontal')); ?>
                    <div class="col-md-10 col-md-offset-1">
                        <h4></h4>
                        <h4>Import File</h4>
                        <div class="col-md-5 col-md-offset-1">
                            <div class="btn-group profile_image_group">
                                <label title="Upload excel file" for="inputImage2" class="btn btn-primary btn-xs excel_image">
                                    <input type="file" name="import_price_file" accept="file/*" class="hide" id="inputImage2">
                                    <div id="image_text">Upload Excel file</div>
                                </label>
                            </div>
                            <div class="error">
                                <?php echo form_error('import_price_file'); ?>
                            </div>
                            <div class="form-group">&nbsp;</div>
                            <div class="form-group">&nbsp;</div>
                            <div class="form-group">&nbsp;</div>
                        </div>
                    </div>
                    </form>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary btn-xs block full-width m-b" id="import_store_prices">Import</button>
                <button type="button" class="btn btn-danger btn-xs block full-width m-b" data-dismiss="modal" aria-label="Close">Cancel</button>
            </div>
        </div>
    </div>
</div>