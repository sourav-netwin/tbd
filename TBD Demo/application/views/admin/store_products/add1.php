<!-- /.row -->

<div class="row">
    <div class="col-lg-10">
        <?php echo form_open('storeproducts/add', array('id' => 'storeproducts_form', 'class' => 'form-horizontal','autocomplete'=>'off')); ?>

        <div class="form-group">
            <label for="products" class="col-sm-3 control-label">Products <span>*</span></label>
            <div class="col-sm-5 col-md-4">
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

        <div class="form-group">
            <label for="retailers" class="col-sm-3 control-label">Retailers <span>*</span></label>
            <div class="col-sm-5 col-md-4">
                <select class="form-control select-filter" id="retailers" name="retailers">
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

        <div class="form-group">
            <label for="price_store" class="col-sm-3 control-label">Price for all stores <span>*</span></label>
            <div class="col-sm-5 col-md-4">
                <div class="radio">
                    <label>
                        <input type="radio" name="price_store" id="price_store" value="0" checked>
                        Individual
                    </label>
                    <label>
                        <input type="radio" name="price_store" id="price_store" value="1">
                        All
                    </label>
                </div>
                <div class="error">
                    <?php echo form_error('price_store'); ?>
                </div>
            </div>
        </div>
        
        <div class="form-group">
            <label for="stores" class="col-sm-3 control-label">Store <span>*</span></label>
            <div class="col-sm-5 col-md-4">
                <select class="form-control select-filter" id="stores" name="stores">
                    <option value="">Select Store</option>
                    <?php
                    if (!empty($stores)) :
                        foreach ($stores as $store) {
                            echo "<option value='" . $store['Id'] . "'>" . $store['StreetAddress'] ." ".$store['City']." ".$store['Name']. "</option>";
                        }
                    endif;
                    ?>
                </select>
                <div class="error">
                    <?php echo form_error('stores'); ?>
                </div>
            </div>
        </div>

        <div class="form-group">
            <label for="price" class="col-sm-3 control-label">Price <span>*</span></label>
            <div class="col-sm-5 col-md-4">
                <input type="text" class="form-control" name="price" placeholder="Price" value="">
                <div class="error">
                    <?php echo form_error('price'); ?>
                </div>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-3 control-label"></label>
            <div class="col-sm-5 col-md-4">
                <button type="submit" class="btn btn-primary btn-xs block full-width m-b">Add</button>
                <a class="btn btn-primary btn-xs block full-width m-b" href="<?php echo site_url('/storeproducts'); ?>">Cancel</a>
            </div>
        </div>
        </form>
    </div>
</div>