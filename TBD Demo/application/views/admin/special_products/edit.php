
<!-- /.row -->

<div class="row">
    <?php echo form_open('specialproducts/edit_post/' . $special_product_details['Id'], array('id' => 'specialproducts_form', 'class' => 'form-horizontal','autocomplete'=>'off')); ?>
    <div class="col-md-10 col-md-offset-1">

        <div class="form-group">
            <div class="row">
                <div class="col-md-12">
                    <label for="product_main_category">Category <span>*</span></label>
                    <input type="text" class="form-control" value="<?php echo $special_product_details['main_parent_cat']; ?>" readonly>
                    <input type="hidden" id="product_main_category" name="product_main_category" value="<?php echo $special_product_details['main_parent_catId'] ?>">
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
                    <input type="text" class="form-control" id="products" name="products" value="<?php echo $special_product_details['ProductName']; ?>" readonly>
                    <input type="hidden" name="products" value="<?php echo $special_product_details['ProductId'] ?>">
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
                    <input type="text" class="form-control" value="<?php echo $special_product_details['StoreType']; ?>" readonly>
                    <input type="hidden" id="store_formats" name="store_formats" value="<?php echo $special_product_details['StoreTypeId'] ?>">
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="row">
                <div class="col-md-12">
                    <label for="stores">Store <span>*</span></label>
                    <input type="text" class="form-control" value="<?php echo $special_product_details['StoreName']; ?>" readonly>
                    <input type="hidden" id="stores" name="stores" value="<?php echo $special_product_details['StoreId'] ?>">
                    <div class="error">
                        <?php echo form_error('stores'); ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="row">
                <div class="col-md-6">
                    <label for="special_quantity" >Special Quantity <span>*</span></label>
                    <input type="text" class="form-control" name="special_quantity" id="special_quantity" placeholder="Special Quantity" value="<?php echo $special_product_details['SpecialQty'] ?>">
                    <div class="error">
                        <?php echo form_error('special_quantity'); ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <label for="special_price" >Special Price (R)<span>*</span></label>
                    <input type="text" class="form-control" name="special_price" placeholder="Special Price" value="<?php echo $special_product_details['SpecialPrice'] ?>">
                    <div class="error">
                        <?php echo form_error('special_price'); ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="row">
                <div class="col-md-6">
                    <label for="from_price">Special Offer From <span>*</span></label>

                    <div class="input-group">
                        <div class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                        </div>
                        <input type="text" class="form-control" name="from_price" id="from_price" placeholder="Price From" value="<?php echo date('Y-m-d', strtotime($special_product_details['PriceAppliedFrom'])); ?>">
                    </div>
                    <div class="error">
                        <?php echo form_error('from_price'); ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <label for="to_price" >Special Offer To <span>*</span></label>

                    <div class="input-group">
                        <div class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                        </div>
                        <input type="text" class="form-control" name="to_price" id="to_price" placeholder="Price To" value="<?php echo date('Y-m-d', strtotime($special_product_details['PriceAppliedTo'])); ?>">
                    </div>
                    <div class="error">
                        <?php echo form_error('to_price'); ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="row">
                <div class="col-md-12">
                    <button type="submit" class="btn btn-primary btn-xs block full-width m-b">Update</button>
                    <a class="btn btn-danger btn-xs block full-width m-b" href="<?php echo site_url('/specialproducts'); ?>">Cancel</a>
                </div>
            </div>
        </div>

        </form>
    </div>
</div>
