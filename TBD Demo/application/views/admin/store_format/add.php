<!-- /.row -->
<style>
 .promo-fix-group {
    float: left;
    margin-bottom: 15px;
    margin-top: 15px;
    width: 87px !important;
 }
</style>
<div class="row">
    <?php echo form_open_multipart('storeformat/add', array('id' => 'storeformat_form', 'class' => 'form-horizontal','autocomplete'=>'off')); ?>
    <input id="retailer_id" type="hidden" name="retailer_id" value="<?php echo $retailer_id; ?>">

    <div class="col-md-5 col-md-offset-1">

        <div class="form-group" >
            <div class="row">
                <div class="col-xs-12" style="margin-bottom: -15px">
                    <div class="col-xs-12" style="margin:0px; border: 1px solid #d2d6de; padding-left: 1px !important;">
                    <label for="groupId[]" style="width: 100%;text-align: left;font-weight: bold;margin-bottom: 0px;margin-left: 2px;">Group<span>*</span></label>
                    <div style="margin-left: 4px">
                        <?php foreach ($store_groups as $store_group): ?>
                        <div class="promo-fix-group">
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
        
        <div class="form-group">
            <div class="row">
                <div class="col-md-12">
                    <label for="store_format_name" >Name <span>*</span></label>

                    <input type="text" class="form-control" name="storeformat_name" placeholder="Store Format Name" value="<?php echo set_value('store_format_name'); ?>">
                    <div class="error">
                        <?php echo form_error('store_format_name'); ?>
                    </div>
                 </div>
            </div>
        </div>

        <div class="form-group">
            <div class="row">
                <div class="col-md-12">
                    <button type="submit" class="btn btn-primary btn-xs block full-width m-b">Add</button>
                    <a class="btn btn-danger btn-xs block full-width m-b" href="<?php echo site_url("/storeformat/index/".$retailer_id); ?>">Cancel</a>
                </div>
            </div>
        </div>

    </div>
 <div class="col-md-3">
        <div class="btn-group profile_image_group">
            <label for="inputImage" class="btn btn-primary btn-xs retailer_image">
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
                </div>
            </div>
        </div>
    </div>

</form>
</div>