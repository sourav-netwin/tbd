<!-- /.row -->

<div class="row">
    <?php echo form_open_multipart('categories/edit_post/' . $category_details['Id'], array('id' => 'categories_form', 'class' => 'form-horizontal','autocomplete'=>'off')); ?>
    <?php /* ?>
    <div class="col-md-5 col-md-offset-1">
     <?php */ ?>
    
        <div class="col-xs-7 col-xs-offset-1">
        <div id="groupArea">
            <div class="form-group" >
                <div class="row">
                    <div class="col-md-12" style="margin-bottom: -15px">
                        <div class="col-md-12" style="margin:0px; background-color: #F2F2F2; padding-left: 2px !important;">
                        <label for="groupId[]" style="width: 85%;text-align: left;font-weight: bold;margin-bottom: 0px;">Group<span>*</span></label>
                        <div style="margin-left: 5px">
                            <?php foreach ($store_groups as $store_group): ?>
                            <div class="promo-fix">
                                <input type="checkbox" class="icheck-minimal-check one_required" name='groupId[]' id="groupId_<?php echo $store_group['Id']; ?>" value="<?php echo $store_group['Id']; ?>" <?php echo in_array($store_group['Id'], $categories_storegroups) ? 'checked="checked"' : '' ?> >
                                <?php echo $store_group['GroupName']; ?>                        
                            </div>
                            <?php endforeach; ?>
                        </div>
                        </div>
                    </div>                 
                </div>
            </div>

            <div class="col-md-12" style="margin-bottom: 10px">
                <div class="row">                
                    <div class="col-md-12 showError" style="margin:0px; padding-left: 2px !important;">                    
                        <div class="error" id="groupId_error">
                            <?php echo form_error('groupId'); ?>
                        </div>
                    </div> 
                </div>
            </div>
        </div>
        
        <div class="form-group">
            <div class="row">
                <div class="col-md-12">
                    <label for="category_title">Category Name <span>*</span></label>
                    <input type="text" class="form-control" name="category_title" placeholder="Category Title" value="<?php echo $category_details['CategoryName']; ?>">
                    <div class="error">
                        <?php echo form_error('category_title'); ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="row">
                <div class="col-md-12">
                    <label for="category_description">Category Description</label>
                    <textarea class="form-control" name="category_description" placeholder="Category Description"><?php echo $category_details['CategoryDescription']; ?></textarea>
                    <div class="error">
                        <?php echo form_error('category_description'); ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="row">
                <div class="col-md-12">
                    <label for="parent_category">Parent Category</label>
                    <select class="form-control" id="parent_category" name="parent_category">
                        <option value="0">Parent</option>
                        <?php
                        foreach ($category_arr as $key => $category) {
                            foreach ($category as $c) {
                                if ($key == 0) {
                                    if (empty($category_arr[$c['id']])) {
                                        if ($c['id'] != $category_details['Id']) {
                                            ?>
                                            <option value="<?php echo $c['id']; ?>" <?php echo ( $category_details['ParentCategory'] == $c['id'] ) ? "selected" : ""; ?>><?php echo $c['name']; ?></option>
                                            <?php
                                        }
                                    }
                                    else {
                                        if ($c['id'] != $category_details['Id']) {
                                            ?>
                                            <option value="<?php echo $c['id']; ?>" <?php echo ( $category_details['ParentCategory'] == $c['id'] ) ? "selected" : ""; ?>><?php echo $c['name']; ?></option>
                                            <?php
                                            foreach ($category_arr[$c['id']] as $sub_category) {
                                                if ($sub_category['id'] != $category_details['Id']) {
                                                    ?>
                                                    <option value="<?php echo $sub_category['id']; ?>" class="sub_category" <?php echo ( $category_details['ParentCategory'] == $sub_category['id'] ) ? "selected" : ""; ?>><?php echo $sub_category['name']; ?></option>
                                                <?php }
                                            }
                                            ?>
                                        <?php
                                        }
                                    }
                                }
                            }
                        }
                        ?>
                    </select>
                    <div class="error">
                        <?php echo form_error('parent_category'); ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="row">
                <div class="col-md-12">
                    <button type="submit" class="btn btn-primary btn-xs block full-width m-b">Update</button>
                    <!--<a class="btn btn-danger btn-xs block full-width m-b" href="<?php //echo site_url('/categories');  ?>">Cancel</a>-->
                </div>
            </div>
        </div>
    </div>
<?php
$image_path = front_url() . CATEGORY_IMAGE_PATH . "/medium/" . $category_details['CategoryIcon'];
$style = ( $category_details['CategoryIcon'] != '' || file_exists('./' . CATEGORY_IMAGE_PATH . "/medium/" . $category_details['CategoryIcon']) ) ? 'style="background-image: url(' . $image_path . ')"' : '';
?>
    <div class="col-xs-3">
        <div class="btn-group profile_image_group" id="category_icon_container" style="display:none;">
            <label for="inputImage" class="btn btn-primary btn-xs category_image" <?php echo $style ?>>
                <span title="Upload Image" style="background: rgba(254, 254, 254, 0.7) url('<?php echo base_url() ?>../assets/admin/img/upload.png') no-repeat scroll center center / 25px 25px;">
                    <input type="file" accept="image/*" name="category_icon" class="hide" id="inputImage">
                </span>
                <!--<div id="image_text">Upload Category Icon</div>-->
            </label>
        </div>
        <div class="error">
<?php echo form_error('category_icon'); ?>
        </div>
        <input type="hidden" name="old_icon" id="old_icon" value=<?php echo $category_details['CategoryIcon'] ?>>
    </div>
    <!-- Add Modal -->

    <input type="hidden" name="image-x" id="image-x">
    <input type="hidden" name="image-y" id="image-y">
    <input type="hidden" name="image-width" id="image-width">
    <input type="hidden" name="image-height" id="image-height">


    </form>
</div>