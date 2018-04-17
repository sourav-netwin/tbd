<!-- /.row -->

<div id="tabs">
    <?php
    if ($this -> session -> userdata('user_type') == 6) {
        ?>
        <input type="hidden" id="is_store" value="1" />
        <input type="hidden" id="retailer_sel" value="<?php echo $retailer_id; ?>" />
        <input type="hidden" id="storetype_sel" value="<?php echo $store_format_id; ?>" />
        <input type="hidden" id="store_sel" value="<?php echo $store_id; ?>" />
        <?php
    }
    if ($this -> session -> userdata('user_level') == 3) {
        ?>
        <input type="hidden" id="is_retailer" value="1" />
        <input type="hidden" id="retailer_sel" value="<?php echo $retailer_id; ?>" />
        <?php
    }
    if ($this -> session -> userdata('user_type') == 5) {
        ?>
        <input type="hidden" id="is_stype" value="1" />
        <input type="hidden" id="retailer_sel" value="<?php echo $retailer_id; ?>" />
        <input type="hidden" id="storetype_sel" value="<?php echo $store_format_id; ?>" />
        <?php
    }
    ?>
    <ul>
        <li><a href="#tabs-1">ADD PRODUCT BY CUSTOMIZED LIST</a></li>
        <li><a href="#tabs-2">ADD PRODUCT BY CATEGORY</a></li>
    </ul>
    <div id="tabs-1" class="active">

        <div class="row add_stpr">
            <?php if ($step == 'new') { ?>
                <div class="col-md-12 welcome_header">
                    <p><button type="button" class="btn btn-primary btn-xs block full-width m-b">STEP 3 </button>
                        Please search a product & then select the product which you want in your store.
                        Here you can change the price of the product.
                    </p>
                </div>
            <?php } ?>
            <?php echo form_open('storeproducts/add_custom/' . $step, array('id' => 'storeproducts_search_form', 'class' => 'form-horizontal', 'autocomplete' => 'off')); ?>
            <div class="col-md-5 ">

                <?php
                //Admin Users
                if ($this -> session -> userdata('user_level') < 3) {
                    ?>
                    <div class="form-group">
                        <div class="col-xs-8">
                            <label for="retailers_store_search">Retailers <span>*</span></label>
                            <select class="form-control select-filter" id="retailers_store_search" name="retailers_store_search">
                                <option value="">Select Retailer</option>
                                <?php
                                if (!empty($retailers)) :
                                    foreach ($retailers as $retailer) {
                                        echo "<option value='" . $retailer['Id'] . "'" . ( ( $retailer['Id'] == @$_POST['retailers_store'] ) ? 'selected' : '' ) . ">" . $retailer['CompanyName'] . "</option>";
                                    }
                                endif;
                                ?>
                            </select>
                            <div class="error">
                                <?php echo form_error('retailers'); ?>
                            </div>
                        </div>
                        <div class="col-xs-4" style="margin-top: 22px;">
                            <div style="max-height: 35px" id="ret_demo_disp_src"></div>
                        </div>

                    </div>
                <?php } ?>

                <div class="form-group">
                    <div class="col-md-12">
                        <label for="product_name_search">Search product <span>*</span></label>
                        <input type="text" class="form-control" id="product_name_search" name="product_name_search" placeholder="bread" />
                        <div class="error">
                            <?php echo form_error('product_name_search'); ?>
                        </div>
                    </div>
                </div>
                <?php
                //Retailers Users
                if ($this -> session -> userdata('user_level') <= 3) {
                    ?>
                    <div class="form-group">
                        <div class="col-md-12">
                            <label for="store_formats">Store Formats <span>*</span></label>
                            <div id="store_formats_search" name="store_formats_search" class="checkbox_lists">
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
                                <?php echo form_error('store_format_list'); ?>
                            </div>
                        </div>
                    </div>
                <?php } ?>
                <?php
                //Retailers Users & Store Format Users
                if ($this -> session -> userdata('user_level') <= 4) {
                    ?>
                    <div class="form-group">
                        <div class="col-md-12">
                            <label for="store_formats">Stores <span>*</span></label>
                            <div  name="stores_search" id="stores_search" class="checkbox_lists">
                                <?php
                                if (!empty($stores)) {

                                    $stores_chunks = array_chunk($stores, 2);

                                    foreach ($stores_chunks as $key => $stores_chunk) {

                                        echo ' <div class="col-md-6">';

                                        if ($key == 0) {
                                            echo '<div class="col-md-12"><div class="row">
                                                <div class="checkbox">
                                                    <label>
                                                    <input type="checkbox" name="stores_list_search[]" value="0" id="all_stores"><label>All Stores
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
                                                            <input type="checkbox" name="stores_list_search[]" value="' . $store['Id'] . '"><label>' . $store['StoreName'] . '
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
                <?php } ?>

                <div class="form-group">
                    <div class="col-md-12">
                        <button type="button" id="list_product_search" class="btn btn-primary btn-xs block full-width m-b">Search</button>
                    </div>
                </div>
            </div>

            <div class="col-md-12">
                <div class="table-responsive store-product-search-listing" id="store-product-search-list">
                    <table id="store-product-search-list-table" class="table table-bordered table-hover table-striped dataTables">
                        <thead>
                            <tr>
                                <th width="1%"><input type="checkbox" id="select_products_search"></th>
                                <th width="20%">Product Name</th>
                                <th width="13%">Product Brand</th>
                                <!--<th width="8%">SKU</th>-->
                                <th width="15%">Category</th>
                                <th width="8%">Price</th>
                                <th width="8%">New Price</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="7" align="center">
                                    No products avaliable
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="col-md-5 col-md-offset-1">
                <div class="form-group">
                    <div class="row">
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-primary btn-xs block full-width m-b">Add</button>
                            <?php if ($step != 'new') { ?>
                                <a class="btn btn-danger btn-xs block full-width m-b" href="<?php echo site_url('/storeproducts'); ?>">Cancel</a>
                                <?php
                            }
                            else {
                                ?>
                                <a class="btn btn-danger btn-xs block full-width m-b" href="<?php echo site_url('/storeproducts/product_catalogue'); ?>">Back</a>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>

            </form>
        </div>


    </div>
    <div id="tabs-2">
        <div class="row add_stpr">
            <?php if ($step == 'new') { ?>
                <div class="col-md-12 welcome_header">
                    <p><button type="button" class="btn btn-primary btn-xs block full-width m-b">STEP 3 </button>
                        Please select a category & then select the product which you want in your store.
                        Here you can change the price of the product.
                    </p>
                </div>
            <?php } ?>
            <?php echo form_open('storeproducts/add/' . $step, array('id' => 'storeproducts_form', 'class' => 'form-horizontal', 'autocomplete' => 'off')); ?>
            <div class="col-md-5 ">

                <?php
                //Admin Users
                if ($this -> session -> userdata('user_level') < 3) {
                    ?>
                    <div class="form-group">
                        <div class="col-xs-8">
                            <label for="retailers">Retailers <span>*</span></label>
                            <select class="form-control select-filter" id="retailers_store" name="retailers">
                                <option value="">Select Retailer</option>
                                <?php
                                if (!empty($retailers)) :
                                    foreach ($retailers as $retailer) {
                                        echo "<option value='" . $retailer['Id'] . "'" . ( ( $retailer['Id'] == @$_POST['retailers_store'] ) ? 'selected' : '' ) . ">" . $retailer['CompanyName'] . "</option>";
                                    }
                                endif;
                                ?>
                            </select>
                            <div class="error">
                                <?php echo form_error('retailers'); ?>
                            </div>

                        </div>
                        <div class="col-xs-4" style="margin-top: 22px;">
                            <div style="max-height: 35px" id="ret_demo_disp"></div>
                        </div>
                    </div>
                <?php } ?>

                <div class="form-group">
                    <div class="col-md-12">
                        <label for="product_main_category">Category <span>*</span></label>
                        <select class="form-control select-filter" id="product_main_category" name="product_main_category">
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
                <?php
                //Retailers Users
                if ($this -> session -> userdata('user_level') <= 3) {
                    ?>
                    <div class="form-group">
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
                <?php } ?>
                <?php
                //Retailers Users & Store Format Users
                if ($this -> session -> userdata('user_level') <= 4) {
                    ?>
                    <div class="form-group">
                        <div class="col-md-12">
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
                <?php } ?>

                <div class="form-group">
                    <div class="col-md-12">
                        <button type="button" id="list_product" class="btn btn-primary btn-xs block full-width m-b">List All</button>
                    </div>
                </div>
            </div>

            <div class="col-md-12">
                <div class="table-responsive store-product-listing" id="store-product-list">
                    <table id="store-product-list-table" class="table table-bordered table-hover table-striped dataTables">
                        <thead>
                            <tr>
                                <th width="1%"><input type="checkbox" id="select_products"></th>
                                <th width="40%">Product Name</th>
                                <th width="24%">Product Brand</th>
                                <!--<th width="8%">SKU</th>-->
                                <th width="20%">Category</th>
                                <th width="8%">Price</th>
                                <th width="8%">New Price</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="7" align="center">
                                    No products avaliable
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="col-md-5 col-md-offset-1">
                <div class="form-group">
                    <div class="row">
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-primary btn-xs block full-width m-b">Add</button>
                            <?php if ($step != 'new') { ?>
                                <a class="btn btn-danger btn-xs block full-width m-b" href="<?php echo site_url('/storeproducts'); ?>">Cancel</a>
                                <?php
                            }
                            else {
                                ?>
                                <a class="btn btn-danger btn-xs block full-width m-b" href="<?php echo site_url('/storeproducts/product_catalogue'); ?>">Back</a>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>

            </form>
        </div>
    </div>
</div>

<script>
    $(function(){
        activateMenu('admin/storeproducts');
    });
</script>