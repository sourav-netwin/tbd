<!-- /.row -->

<div class="row">
    <?php echo form_open_multipart('houseproduct/add', array('id' => 'products_form', 'class' => 'form-horizontal', 'autocomplete' => 'off','autocomplete'=>'off')); ?>

    <div class="col-md-5 col-md-offset-1">

        <div class="form-group">
            <div class="row">
                <div class="col-md-12">
                    <label for="product_name">Product Name <span>*</span></label>

                    <input type="text" class="form-control" name="product_name" placeholder="Product Name" value="<?php echo set_value('product_name'); ?>">
                    <div class="error">
                        <?php echo form_error('product_name'); ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="row">
                <div class="col-md-12">
                    <label for="product_description">Product Description <span>*</span></label>
                    <textarea class="form-control" name="product_description" placeholder="Product Description"><?php echo set_value('product_description'); ?></textarea>
                    <div class="error">
                        <?php echo form_error('product_description'); ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="row">
                <div class="col-md-6">
                    <label for="sku">SKU </label>
                    <input type="text" class="form-control" name="sku" placeholder="SKU" value="<?php echo set_value('sku'); ?>">
                    <div class="error">
                        <?php echo form_error('sku'); ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <label for="brand">Brand <span>*</span></label>
                    <input type="text" class="form-control" name="brand" placeholder="Brand" readonly="" value="<?php echo $retailer_details['CompanyName'] ?>">
                    <div class="error">
                        <?php echo form_error('brand'); ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="row">
                <div class="col-md-12">
                    <label for="product_main_category" >Main Category <span>*</span></label>
                    <?php if (!empty($main_categories)) { ?>
                        <select class="form-control select-filter" id="product_main_category" name="product_main_category">
                            <option value="">Select Main Category</option>
                            <?php foreach ($main_categories as $category) { ?>
                                <option value="<?php echo $category['Id']; ?>" <?php echo set_select('product_main_category', $category['Id']); ?>><?php echo $category['CategoryName']; ?></option>
                            <?php } ?>
                        </select>
                    <?php } ?>
                    <div class="error">
                        <?php echo form_error('product_main_category'); ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="row">
                <div class="col-md-6">
                    <label for="product_parent_category">Category <span>*</span></label>

                    <select class="form-control select-filter" id="product_parent_category" name="product_parent_category">
                        <option value="">Select Category</option>
                        <?php
                        if (!empty($parent_category)) {
                            foreach ($parent_category as $category) {
                                ?>
                                <option value="<?php echo $category['Id']; ?>" <?php echo set_select('product_parent_category', $category['Id']); ?>><?php echo $category['CategoryName']; ?></option>
                                <?php
                            }
                        }
                        ?>
                    </select>
                    <div class="error">
                        <?php echo form_error('product_parent_category'); ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <label for="product_sub_category">Sub Category</label>
                    <select class="form-control select-filter" id="product_sub_category" name="product_sub_category">
                        <option value="">Select Sub Category</option>
                        <?php
                        if (!empty($sub_category)) {
                            foreach ($sub_category as $category) {
                                ?>
                                <option value="<?php echo $category['Id']; ?>" <?php echo set_select('product_sub_category', $category['Id']); ?>><?php echo $category['CategoryName']; ?></option>
                                <?php
                            }
                        }
                        ?>
                    </select>
                    <div class="error">
                        <?php echo form_error('product_sub_category'); ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="row">
                <div class="col-md-6">
                    <label for="product_rate" >RRP (R) <span>*</span></label>
                    <input type="text" class="form-control" name="product_rate" placeholder="RRP" value="<?php echo set_value('product_rate'); ?>">
                    <div class="error">
                        <?php echo form_error('product_rate'); ?>
                    </div>
                </div>
            </div>
        </div>
        <input type="hidden" value="<?php echo $retailer_details['Id']; ?>" id="ret_sel" />

        <div class="form-group">
            <div class="row">
                <div class="col-md-12">
                    <button type="submit" class="btn btn-primary btn-xs block full-width m-b">Add</button>
                    <a class="btn btn-danger btn-xs block full-width m-b" href="<?php echo site_url('/products'); ?>">Cancel</a>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="btn-group profile_image_group">
            <label for="inputImage" class="btn btn-primary btn-xs product_image">
                <span title="Upload Image" style="background: rgba(254, 254, 254, 0.7) url('<?php echo base_url() ?>../assets/admin/img/upload.png') no-repeat scroll center center / 25px 25px;">
                <input type="file" accept="image/*" name="product_image"  class="hide" id="inputImage">
                </span>
                <!--<div id="image_text">Upload Product Image</div>-->
            </label>
        </div>
        <div class="error">
            <?php echo form_error('product_image'); ?>
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
   activateMenu('admin/houseproduct');
});
</script>