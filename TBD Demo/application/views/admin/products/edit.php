<?php echo form_open_multipart('products/edit_post/' . $Id, array('id' => 'products_form', 'class' => 'form-horizontal','autocomplete'=>'off')); ?>
<div class="row">

    <div class="col-xs-7 col-xs-offset-1">


        <div class="form-group">
            <div class="row">
                <div class="col-md-12">
                    <label for="product_name">Product Name <span>*</span></label>

                    <input type="text" class="form-control" name="product_name" placeholder="Product Name" value="<?php echo $ProductName ?>">
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
                    <textarea class="form-control" name="product_description" placeholder="Product Description"><?php echo $ProductDescription ?></textarea>
                    <div class="error">
                        <?php echo form_error('product_description'); ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="row">
                <div class="col-xs-6">
                    <label for="product_main_category" >Main Category <span>*</span></label>
                    <?php if (!empty($main_categories)) { ?>
                        <select class="form-control select-filter" id="product_main_category" name="product_main_category">
                            <?php foreach ($main_categories as $category) { ?>
                                <option value="<?php echo $category['Id']; ?>" <?php echo ( $main_parent_cat_id == $category['Id'] ) ? "selected" : ""; ?>><?php echo $category['CategoryName']; ?></option>
                            <?php } ?>
                        </select>
                    <?php } ?>
                    <div class="error">
                        <?php echo form_error('product_main_category'); ?>
                    </div>
                </div>

                <div class="col-xs-6">
                    <label for="brand">Brand <span>*</span></label>
                    <input type="text" class="form-control" name="brand" placeholder="Brand" value="<?php echo $Brand ?>">
                    <div class="error">
                        <?php echo form_error('brand'); ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="row">
                <div class="col-xs-6">
                    <label for="product_parent_category">Category <span>*</span></label>

                    <select class="form-control select-filter" id="product_parent_category" name="product_parent_category">
                        <option value="">Select Category</option>
                        <?php if (!empty($parent_category)) { ?>
                            <?php foreach ($parent_category as $category) { ?>
                                <option value="<?php echo $category['Id']; ?>" <?php echo ( $parent_cat_id == $category['Id'] ) ? "selected" : ""; ?>><?php echo $category['CategoryName']; ?></option>
                                <?php
                            }
                        }
                        ?>
                    </select>

                    <div class="error">
                        <?php echo form_error('product_parent_category'); ?>
                    </div>

                </div>
                <div class="col-xs-6">
                    <label for="sku">SKU </label>
                    <input type="text" class="form-control" name="sku" placeholder="SKU" value="<?php echo $SKU ?>">
                    <div class="error">
                        <?php echo form_error('sku'); ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="row">
                <div class="col-xs-6">
                    <label for="product_sub_category">Sub Category </label>

                    <select class="form-control select-filter" id="product_sub_category" name="product_sub_category">
                        <option value="">Select Sub Category</option>
                        <?php if (!empty($sub_category)) { ?>
                            <?php foreach ($sub_category as $category) { ?>
                                <option value="<?php echo $category['Id']; ?>" <?php echo ( $CategoryId == $category['Id'] ) ? "selected" : ""; ?>><?php echo $category['CategoryName']; ?></option>
                            <?php } ?>
                        <?php } ?>
                    </select>

                    <div class="error">
                        <?php echo form_error('product_sub_category'); ?>
                    </div>
                </div>
                <div class="col-xs-6">
                    <label for="product_rate" >RRP (R)<span>*</span></label>
                    <input type="text" class="form-control" name="product_rate" placeholder="RRP" value="<?php echo $RRP ?>">
                    <div class="error">
                        <?php echo form_error('product_rate'); ?>
                    </div>
                </div>
            </div>
        </div>

        <!--        <div class="form-group">
                    <div class="row">
                        
                    </div>
                </div>-->

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
        $image_path = front_url() . PRODUCT_IMAGE_PATH . "medium/" . $ProductImage;
        $style = ($ProductImage != '' || file_exists('./' . PRODUCT_IMAGE_PATH . "/medium/" . $ProductImage) ) ? 'style="background-image: url(' . $image_path . ')"' : '';
    }
    ?>
    <div class="col-xs-3">
        <div class="btn-group profile_image_group">
            <label for="inputImage" class="btn btn-primary btn-xs product_image"  <?php echo $style ?>>
                <span title="Upload Image" style="background: rgba(254, 254, 254, 0.7) url('<?php echo base_url()?>../assets/admin/img/upload.png') no-repeat scroll center center / 25px 25px;">
                    <input type="file" accept="image/*" name="product_image"  class="hide" id="inputImage">
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