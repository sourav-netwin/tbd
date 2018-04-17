<!-- /.row -->

<div class="row">
    <?php echo form_open('specialproducts/add', array('id' => 'specialproducts_form', 'class' => 'form-horizontal', 'autocomplete' => 'off')); ?>
    <div class="col-md-5 col-md-offset-1">

        <div class="form-group">
            <div class="row">
                <div class="col-md-12">
                    <label for="product_main_category">Category <span>*</span></label>
                    <select class="form-control select-filter" id="product_main_category" name="product_main_category">
                        <option value="">Select Main Category</option>
                        <?php foreach ($main_categories as $category) { ?>
                            <option value="<?php echo $category['Id']; ?>" <?php echo set_select('product_main_category', $category['Id']); ?>><?php echo $category['CategoryName']; ?></option>
                        <?php } ?>
                    </select>
                    <div class="error">
                        <?php echo form_error('product_main_category'); ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="row">
                <div class="col-md-12">
                    <label for="products">Products <span>*</span></label>
                    <select class="form-control select-filter" id="products" name="products">
                        <option value="">Select Product</option>
                        <?php
                        if (!empty($products)) :
                            foreach ($products as $product) {
                                echo "<option value='" . $product['Id'] . "'>" . $product['ProductName'] . "</option>";
                            }
                        endif;
                        ?>
                    </select>
                    <div class="error">
                        <?php echo form_error('products'); ?>
                    </div>
                </div>
            </div>
        </div>

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
                                    echo '<div class="col-md-12">
                                              <div class="row">
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

        <div class="form-group">
            <div class="row">
                <div class="col-md-12" id="stores">
                    <label for="store_formats">Store Formats <span>*</span></label>
                    <div id="stores" name="stores" class="checkbox_lists">
                        <?php
                        if (!empty($stores[0])) {
                            ?>
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" name="stores_list[]" value="0" id="all_stores"><label>All Stores
                                            </label>
                                    </div>
                                </div>
                            </div>
                            <?php
                            foreach ($stores as $store) {
                                ?>
                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="checkbox">
                                            <label>
                                                <input type="checkbox" name="stores_list[]" value="<?php echo $store['Id'] ?>"><label><?php echo $store['StoreName'] ?>
                                                </label>
                                        </div>
                                    </div>
                                </div>
                                <?php
                            }
                        }
                        ?>
                    </div>
                </div>
                <div class="error">
                    <?php echo form_error('stores'); ?>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="row">
                <div class="col-md-6">
                    <label for="default_price" >Default Price <span>*</span></label>
                    <input type="text" class="form-control" name="default_price" id="default_price" placeholder="0.00" readonly="">
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="row">
                <div class="col-md-6">
                    <label for="special_quantity" >Special Quantity <span>*</span></label>
                    <input type="text" class="form-control" name="special_quantity" id="special_quantity" placeholder="Special Quantity" value="1">
                    <div class="error">
                        <?php echo form_error('special_quantity'); ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <label for="special_price" >Special Price (R)<span>*</span></label>
                    <input type="text" class="form-control" name="special_price" placeholder="Special Price" value="">
                    <div class="error">
                        <?php echo form_error('special_price'); ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="row">
                <div class="col-md-6">
                    <label for="price_from">Price From <span>*</span></label>

                    <div class="input-group">
                        <div class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                        </div>
                        <input type="text" class="form-control" name="price_from" id="price_from" placeholder="Price From" value="">
                    </div>
                    <div class="error">
                        <?php echo form_error('price_from'); ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <label for="price_to" >Price To <span>*</span></label>

                    <div class="input-group">
                        <div class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                        </div>
                        <input type="text" class="form-control" name="price_to" id="price_to" placeholder="Price To" value="">
                    </div>
                    <div class="error">
                        <?php echo form_error('price_to'); ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="row">
                <div class="col-md-12">
                    <button type="submit" class="btn btn-primary btn-xs block full-width m-b">Add</button>
                    <a class="btn btn-danger btn-xs block full-width m-b" href="<?php echo site_url('/specialproducts'); ?>">Cancel</a>
                </div>
            </div>
        </div>

        </form>
    </div>
</div>