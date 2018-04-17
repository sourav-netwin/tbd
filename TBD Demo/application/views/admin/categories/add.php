<!-- /.row -->
<div class="row">
    <?php echo form_open_multipart('categories/add/' . $parent_category, array('id' => 'categories_form', 'class' => 'form-horizontal','autocomplete'=>'off')); ?>
    <div class="col-md-6 col-md-offset-1">

        <div id="groupArea">
            <div class="form-group" >
                <div class="row">
                    <div class="col-xs-12" style="margin-bottom: -15px">
                        <div class="col-xs-12" style="margin:0px; background-color: #F2F2F2; padding-left: 1px !important;">
                        <label for="groupId[]" style="width: 85%;text-align: left;font-weight: bold;margin-bottom: 0px;">Group<span>*</span></label>
                        <div style="margin-left: 5px">
                            <?php foreach ($store_groups as $store_group): ?>
                            <div class="promo-fix">
                                <input type="checkbox" class="icheck-minimal-check one_required" name='groupId[]' id="groupId_<?php echo $store_group['Id']; ?>" value="<?php echo $store_group['Id']; ?>" <?php echo in_array($store_group['Id'], $retailers_storegroups) ? 'checked="checked"' : '' ?> >
                                <?php echo $store_group['GroupName']; ?>                        
                            </div>
                            <?php endforeach; ?>
                        </div>
                        </div>
                    </div>                 
                </div>
            </div>
            <div class="form-group" >
                <div class="col-md-12">
                    <div class="row">                
                        <div class="col-md-12 showError">                    
                            <div class="error" id="groupId_error">
                                <?php echo form_error('groupId'); ?>
                            </div>
                        </div> 
                    </div>
                </div>
            </div>
        </div>
        
        <div class="form-group">
            <div class="row">
                <div class="col-md-12">
                    <label for="category_title">Category Name <span>*</span></label>
                    <input type="text" class="form-control" name="category_title" placeholder="Category Title" value="<?php echo @$_POST['category_title']; ?>">
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
                    <textarea class="form-control" name="category_description" placeholder="Category Description"><?php echo @$_POST['category_description']; ?></textarea>
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
                    <select class="form-control" id="parent_category" name="parent_category" <?php echo ( $parent_category > 0 ) ? "disabled" : ""; ?>>
                        <option value="0">Parent</option>
                        <?php
                        foreach ($category_arr as $key => $category) {
                            foreach ($category as $c) {
                                if ($key == 0) {
                                    if (empty($category_arr[$c['id']])) {
                                        ?>
                                        <option value="<?php echo $c['id']; ?>" <?php echo ( $parent_category == $c['id'] || @$_POST['parent_category'] == $c['id'] ) ? "selected" : ""; ?>><?php echo $c['name']; ?></option>
                                    <?php
                                    }
                                    else {
                                        ?>
                                        <option value="<?php echo $c['id']; ?>" <?php echo ( $parent_category == $c['id'] || @$_POST['parent_category'] == $c['id'] ) ? "selected" : ""; ?>><?php echo $c['name']; ?></option>
                                        <?php foreach ($category_arr[$c['id']] as $sub_category) { ?>
                                            <option value="<?php echo $sub_category['id']; ?>" class="sub_category" <?php echo ( $parent_category == $sub_category['id'] || @$_POST['parent_category'] == $sub_category['id'] ) ? "selected" : ""; ?>><?php echo $sub_category['name']; ?></option>
                                        <?php } ?>
                                    <?php
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
                <?php if ($parent_category > 0) { ?>
                    <input type="hidden" name="existing_parent_id" id="existing_parent_id" value="<?php echo $parent_category ?>">
                <?php } ?>
            </div>
        </div>

        <div class="form-group">
            <div class="row">
                <div class="col-md-12">
                    <button type="submit" class="btn btn-primary btn-xs block full-width m-b">Add</button>
                    <a class="btn btn-danger btn-xs block full-width m-b" href="<?php echo site_url('/categories'); ?>">Cancel</a>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="btn-group profile_image_group" id="category_icon_container">
            <label for="inputImage" class="btn btn-primary btn-xs category_image">
                <span title="Upload Image" style="background: rgba(254, 254, 254, 0.7) url('<?php echo base_url() ?>../assets/admin/img/upload.png') no-repeat scroll center center / 25px 25px;">
                    <input type="file" accept="image/*" name="category_icon" class="hide" id="inputImage">
                </span>
                <!--<div id="image_text">Upload Category Icon</div>-->
            </label>
        </div>
        <div class="error">
            <?php echo form_error('category_icon'); ?>
        </div>
    </div>
    <!-- Add Modal for cropping -->
    <div class="modal fade" id="imageModal" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel"> <span id="form-action">Select Image</span></h4>
                </div>
                <div class="modal-body" >

                    <div class="image-crop">
                        <img src="<?php echo $this -> config -> item('admin_assets'); ?>img/category.jpg">
                    </div>


                    <div><a id="crop-button" class="btn btn-primary btn-xs block full-width m-b">Select</a></div>
                    <input type="hidden" name="image-x" id="image-x">
                    <input type="hidden" name="image-y" id="image-y">
                    <input type="hidden" name="image-width" id="image-width">
                    <input type="hidden" name="image-height" id="image-height">
                </div>
            </div>
        </div>
    </div>
</form>
</div>
<script>
    $(function(){
        activateMenu('admin/categories');
    });
</script>
