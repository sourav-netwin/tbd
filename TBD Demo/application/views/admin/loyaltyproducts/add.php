<!-- /.row -->

<div class="row">
    <?php echo form_open_multipart('loyaltyproducts/add', array('id' => 'products_form', 'class' => 'form-horizontal products_add_form', 'autocomplete' => 'off','autocomplete'=>'off')); ?>

    <div class="col-md-5 col-md-offset-1">

        <div class="form-group">
            <div class="row">
                <div class="col-md-12">
                    <label for="product_name">Product Name <span>*</span></label>

                    <input type="text" class="form-control" name="LoyaltyTitle" placeholder="Product Name" value="<?php echo set_value('LoyaltyTitle'); ?>">
                    <div class="error">
                        <?php echo form_error('LoyaltyTitle'); ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="row">
                <div class="col-md-12">
                    <label for="product_description">Description <span>*</span></label>
                    <textarea class="form-control" name="LoyaltyDescription" placeholder="Description"><?php echo set_value('LoyaltyDescription'); ?></textarea>
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
                                <option value="<?php echo $category['Id']; ?>" <?php echo set_select('CategoryId', $category['Id']); ?>><?php echo $category['CategoryName']; ?></option>
                            <?php } ?>
                        </select>
                    <?php } ?>
                    <div class="error">
                        <?php echo form_error('CategoryId'); ?>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="form-group">
            <div class="row">
                <div class="col-md-12">
                    <label for="BrandName">Brand Name <span>*</span></label>
                    <input type="text" class="form-control" name="BrandName" placeholder="Brand Name" value="<?php echo set_value('BrandName'); ?>">
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
                            <input type="text" class="form-control" name="StartDate" id="StartDate" placeholder="Start Date" value="">
                        </div>
                        <div class="error">
                            <?php echo form_error('StartDate'); ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label for="price_to" >EndDate <span>*</span></label>

                        <div class="input-group">
                            <div class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </div>
                            <input type="text" class="form-control" name="EndDate" id="EndDate" placeholder="EndDate" value="">
                        </div>
                        <div class="error">
                            <?php echo form_error('EndDate'); ?>
                        </div>
                    </div>
                </div>
            </div>
        
        <div class="form-group">
            <div class="row">
                <div class="col-md-6">
                    <label for="product_rate" >Loyalty Points <span>*</span></label>
                    <input type="text" class="form-control" name="LoyaltyPoints" placeholder="Loyalty Points" value="<?php echo set_value('LoyaltyPoints'); ?>">
                    <div class="error">
                        <?php echo form_error('LoyaltyPoints'); ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="row">
                <div class="col-md-12">
                    <button type="submit" class="btn btn-primary btn-xs block full-width m-b">Add</button>
                    <a class="btn btn-danger btn-xs block full-width m-b" href="<?php echo site_url('/loyaltyproducts'); ?>">Cancel</a>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="btn-group profile_image_group">
            <label for="inputImage" class="btn btn-primary btn-xs product_image">
                <span title="Upload Image" style="background: rgba(254, 254, 254, 0.7) url('<?php echo base_url() ?>../assets/admin/img/upload.png') no-repeat scroll center center / 25px 25px;">
                <input type="file" accept="image/*" name="ProductImage"  class="hide" id="inputImage">
                </span>
                <!--<div id="image_text">Upload Product Image</div>-->
            </label>
        </div>
        <div class="error">
            <?php echo form_error('ProductImage'); ?>
        </div>
    </div>

    <!-- Add Modal -->
    <div class="modal fade" id="imageModal"  data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel"> <span id="form-action">Select Image</span></h4>
                </div>
                <div class="modal-body">
                    <div class="image-crop">
                        <img src="<?php echo $this->config->item('admin_assets'); ?>img/product.jpg">
                    </div>

                    <div><a id="crop-button" class="btn btn-primary btn-xs block full-width m-b">Select</a></div>
                    <input type="hidden" name="image-x" id="image-x">
                    <input type="hidden" name="image-y" id="image-y">
                    <input type="hidden" name="image-width" id="image-width">
                    <input type="hidden" name="image-height" id="image-height">
                    
                    <input type="hidden" name="aspect_ratio" id="aspect_ratio" value="any">
                </div>
            </div>
        </div>
    </div>

</form>
</div>
<script>
$(function(){
   activateMenu('admin/loyaltyproducts');
});
</script>