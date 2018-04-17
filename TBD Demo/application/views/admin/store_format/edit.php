<!-- /.row -->
<style>
    .form-horizontal .form-group {
        margin-right: 0px !important;
        margin-left: 0px !important;
    }
</style>
<div class="row">
    <?php echo form_open_multipart('storeformat/edit/'.$store_format['Id'], array('id' => 'storeformat_form', 'class' => 'form-horizontal','autocomplete'=>'off')); ?>
    <input id="retailer_id" type="hidden" name="retailer_id" value="<?php echo $retailer_id; ?>">
    <input id="storeformat_id" type="hidden" name="storeformat_id" value="<?php echo $store_format['Id']; ?>">

    <div class="col-md-6 col-md-offset-1">

        <div class="form-group" >
            <div class="row">
                <div class="col-xs-12" style="margin-bottom: -15px">
                    <div class="col-xs-12" style="margin:0px; border: 1px solid #d2d6de; padding-left: 2px !important;">
                    <label for="groupId[]" style="width: 85%;text-align: left;font-weight: bold;margin-bottom: 0px;">Group<span>*</span></label>
                    <div style="margin-left: 5px">
                        <?php foreach ($store_groups as $store_group): ?>
                        <div class="promo-fix">
                            <input type="checkbox" class="icheck-minimal-check one_required" name='groupId[]' id="groupId_<?php echo $store_group['Id']; ?>" value="<?php echo $store_group['Id']; ?>" <?php echo in_array($store_group['Id'], $storeformat_storegroups) ? 'checked="checked"' : '' ?> >
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
        
        <div class="form-group">
            <div class="row">
                <div class="col-md-12">
                    <label for="store_format_name" >Name <span>*</span></label>

                    <input type="text" class="form-control" name="storeformat_name" placeholder="Store Format Name" value="<?php echo $store_format['StoreType']; ?>">
                    <div class="error">
                        <?php echo form_error('store_format_name'); ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="row">
                <div class="col-md-12">
                    <button type="submit" class="btn btn-primary btn-xs block full-width m-b">Edit</button>
                    <a class="btn btn-danger btn-xs block full-width m-b" href="<?php echo site_url('/retailers/'.$retailer_id."/storeformat/index"); ?>">Cancel</a>
                </div>
            </div>
        </div>

    </div>
    <?php
       $image_path = front_url() . STORE_FORMAT_IMAGE_PATH . 'medium/' . $store_format['Logo'];
       $style = ( $store_format['Logo'] != '' && file_exists('./' . STORE_FORMAT_IMAGE_PATH . 'medium/' . $store_format['Logo']) ) ? 'style="background-image: url(' . $image_path . ')"' : '';
    ?>

    <div class="col-md-3">
        <div class="btn-group profile_image_group">
            <label for="inputImage" class="btn btn-primary btn-xs retailer_image " <?php echo $style ?>>
                <input type="file"  accept="image/*" name="logo" placeholder="Logo" value="" class="hide" id="inputImage">
                <div id="image_text">Upload Logo</div>
            </label>
        </div>

        <div class="error">
            <?php echo form_error('logo'); ?>
        </div>
    </div>


    <!-- Add Modal -->
    <div class="modal fade" id="imageModal" data-keyboard="false" data-backdrop="static">
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

                    <input type="hidden" name="old_logo" id="old_logo" value=<?php echo $store_format['Logo'] ?>>

                </div>
            </div>
        </div>
    </div>

</form>
</div>