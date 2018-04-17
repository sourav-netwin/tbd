<!-- /.row -->

<div class="row">
    <?php echo form_open('storeproducts/edit_post/' . $store_product_details['Id'], array('id' => 'storeproducts_form', 'class' => 'form-horizontal', 'autocomplete' => 'off')); ?>
    <div class="col-xs-10 col-xs-offset-1">

        <?php //if ($this->session->userdata('user_level') < 3) { ?>
        <!--            <div class="form-group">
                        <div class="row">
                            <div class="col-md-12">
                                <label for="retailers">Retailers <span>*</span></label>
                                <input type="text" class="form-control" value="<?php //echo $store_product_details['CompanyName'];    ?>" readonly>
                                <input type="hidden" name="retailers" value="<?php //echo $store_product_details['RetailerId']    ?>">
                                <div class="error">
        <?php //echo form_error('retailers'); ?>
                                </div>
                            </div>
                        </div>
                    </div>-->
        <?php //} ?>
        <?php
        if ($store_product_details['LogoImage']) {
            ?>
            <div class="form-group">
                <div class="row">
                    <div class="col-md-12 text-center">
                        <div>
                            <img style="max-height: 35px" src="<?php echo front_url().RETAILER_IMAGE_PATH.'small/'.$store_product_details['LogoImage'];?>" />
                        </div>
                    </div>
                </div>
            </div>
            <?php
        }
        ?>

        <?php if ($this -> session -> userdata('user_level') <= 3) { ?>
            <div class="form-group">
                <div class="row">
                    <div class="col-xs-6">
                        <label for="store_formats">Store Formats <span>*</span></label>
                        <input type="text" class="form-control" value="<?php echo $store_product_details['StoreType']; ?>" readonly>
                        <input type="hidden" id="store_formats" name="store_formats" value="<?php echo $store_product_details['StoreTypeId'] ?>">
                    </div>
                    <div class="col-xs-6">
                        <label for="stores">Store <span>*</span></label>
                        <input type="text" class="form-control" value="<?php echo $store_product_details['StoreName']; ?>" readonly>
                        <input type="hidden" id="stores" name="stores" value="<?php echo $store_product_details['StoreId'] ?>">
                        <div class="error">
                            <?php echo form_error('stores'); ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php
        }
        else if ($this -> session -> userdata('user_level') <= 4) {
            ?>
            <div class="form-group">
                <div class="row">
                    <div class="col-md-12">
                        <label for="stores">Store <span>*</span></label>
                        <input type="text" class="form-control" value="<?php echo $store_product_details['StoreName']; ?>" readonly>
                        <input type="hidden" id="stores" name="stores" value="<?php echo $store_product_details['StoreId'] ?>">
                        <div class="error">
                            <?php echo form_error('stores'); ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>

        <div class="form-group">
            <div class="row">
                <div class="col-md-12">
                    <label for="product_main_category">Category <span>*</span></label>
                    <input type="text" class="form-control" value="<?php echo $store_product_details['main_parent_cat']; ?>" readonly>
                    <input type="hidden" id="product_main_category" name="product_main_category" value="<?php echo $store_product_details['main_parent_catId'] ?>">
                    <div class="error">
                        <?php echo form_error('product_main_category'); ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="row">
                <div class="col-md-12">
                    <label for="products">Product <span>*</span></label>
                    <input type="text" class="form-control" id="products" name="products" value="<?php echo $store_product_details['ProductName']; ?>" readonly>
                    <input type="hidden" name="products" value="<?php echo $store_product_details['ProductId'] ?>">
                    <div class="error">
                        <?php echo form_error('products'); ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="row">
                <div class="col-md-6">
                    <label for="price">Price (R)<span>*</span></label>
                    <input type="text" class="form-control" name="price" placeholder="Price" value="<?php echo $store_product_details['Price'] ?>">
                    <div class="error">
                        <?php echo form_error('price'); ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="row">
                <div class="col-md-12">
                    <button type="submit" class="btn btn-primary btn-xs block full-width m-b">Update</button>
                    <!--<a class="btn btn-danger btn-xs block full-width m-b" href="<?php //echo site_url('/storeproducts');    ?>">Cancel</a>-->
                </div>
            </div>
        </div>

    </div>
</form>
</div>