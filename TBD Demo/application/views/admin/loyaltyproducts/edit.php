<?php echo form_open_multipart('loyaltyproducts/edit_post/' . $Id, array('id' => 'products_form', 'class' => 'form-horizontal','autocomplete'=>'off')); ?>
<div class="row">
    <div class="col-xs-7 col-xs-offset-1">
        <div class="form-group">
            <div class="row">
                <div class="col-md-12">
                    <label for="product_name">Product Name <span>*</span></label>

                    <input type="text" class="form-control" name="LoyaltyTitle" placeholder="Product Name" value="<?php echo $LoyaltyTitle ?>">
                    <div class="error">
                        <?php echo form_error('LoyaltyTitle'); ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="row">
                <div class="col-md-12">
                    <label for="LoyaltyDescription">Product Description <span>*</span></label>
                    <textarea class="form-control" name="LoyaltyDescription" placeholder="Product Description"><?php echo $LoyaltyDescription ?></textarea>
                    <div class="error">
                        <?php echo form_error('LoyaltyDescription'); ?>
                    </div>
                </div>
            </div>
        </div>
        
        
        <div class="form-group">
            <div class="row">
                <div class="col-md-12">
                    <label for="CategoryId" >Categories <span>*</span></label>
                    <?php if (!empty($categories)) { ?>
                        <select class="form-control select-filter" id="CategoryId" name="CategoryId">
                            <option value="">Select Category</option>
                            <?php foreach ($categories as $category) { ?>
                                <option value="<?php echo $category['Id']; ?>" <?php echo ( $CategoryId == $category['Id'] ) ? "selected" : ""; ?>><?php echo $category['CategoryName']; ?></option>
                            <?php } ?>
                        </select>
                    <?php } ?>                    
                    <div class="error" id="">
                        <?php echo form_error('CategoryId'); ?>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="form-group">
            <div class="row">
                <div class="col-md-12">
                    <label for="BrandName">Brand Name <span>*</span></label>
                    <input type="text" class="form-control" name="BrandName" placeholder="Brand Name" value="<?php echo $BrandName ?>">
                    <div class="error">
                        <?php echo form_error('BrandName'); ?>
                    </div>
                </div>
            </div>
        </div> 
        
        <div class="form-group">
            <div class="row">
                <div class="col-md-6">
                    <label for="StartDate">Start Date <span>*</span></label>

                    <div class="input-group">
                        <div class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                        </div>
                        <input type="text" class="form-control" name="StartDate" id="StartDate" placeholder="Start Date" value="<?php echo $StartDate; ?>">
                    </div>
                    <div class="error">
                        <?php echo form_error('StartDate'); ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <label for="EndDate" >EndDate <span>*</span></label>

                    <div class="input-group">
                        <div class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                        </div>
                        <input type="text" class="form-control" name="EndDate" id="EndDate" placeholder="EndDate" value="<?php echo $EndDate; ?>">
                    </div>
                    <div class="error">
                        <?php echo form_error('EndDate'); ?>
                    </div>
                </div>
            </div>
         </div>
        
        
        <div class="form-group">
            <div class="row">                
                <div class="col-xs-6">
                    <label for="product_rate" >Loyalty Points<span>*</span></label>
                    <input type="text" class="form-control" name="LoyaltyPoints" placeholder="Loyalty Points" value="<?php echo $LoyaltyPoints ?>">
                    <div class="error">
                        <?php echo form_error('LoyaltyPoints'); ?>
                    </div>
                </div>
            </div>
        </div>
        

        <div class="form-group">
            <div class="row">
                <div class="col-md-12">
                    <button type="button" id="submit_product_edit" class="btn btn-primary btn-xs block full-width m-b">Update</button>
                    <!--<a class="btn btn-danger block full-width m-b" href="<?php //echo site_url('/products');     ?>">Cancel</a>-->
                </div>
            </div>
        </div>
    </div>
    <?php
    $style = "";
    if ($ProductImage) {
        $image_path = front_url() . LOYALTY_PRODUCT_IMAGE_PATH . "medium/" . $ProductImage;
        $style = ($ProductImage != '' || file_exists('./' . LOYALTY_PRODUCT_IMAGE_PATH . "/medium/" . $ProductImage) ) ? 'style="background-image: url(' . $image_path . ')"' : '';
    }
    ?>
    <div class="col-xs-3">
        <div class="btn-group profile_image_group">
            <label for="inputImage" class="btn btn-primary btn-xs product_image"  <?php echo $style ?>>
                <span title="Upload Image" style="background: rgba(254, 254, 254, 0.7) url('<?php echo base_url()?>../assets/admin/img/upload.png') no-repeat scroll center center / 25px 25px;">
                    <input type="file" accept="image/*" name="ProductImage"  class="hide" id="inputImage">
                </span>
                <!--<div id="image_text">Upload Product Image</div>-->
            </label>
        </div>
        <input type="hidden" name="old_product_image" id="old_product_image" value=<?php echo $ProductImage ?>>

    </div>
    <!-- Add Modal -->
    <input type="hidden" name="image-x" id="image-x">
    <input type="hidden" name="image-y" id="image-y">
    <input type="hidden" name="image-width" id="image-width">
    <input type="hidden" name="image-height" id="image-height">

</div>
</form>