<!-- /.row -->
<style>
    .chk-home-page { margin-top:30px;}
    .margin20{margin-left:30px;}
    .margintop20 {margin-top: 114px !important;padding-top: 83px !important;}
    .ads-image-group{ float:left; margin-left: -26px;}
</style>
<div class="row">
    <?php echo form_open_multipart('advertisements/add', array('id' => 'advertisement_form', 'class' => 'form-horizontal products_add_form', 'autocomplete' => 'off','autocomplete'=>'off')); ?>

    <div class="col-md-5 col-md-offset-1">

        <div class="form-group">
            <div class="row">
                <div class="col-md-6">
                    <label for="MainCategoryId" >Main Category <span>*</span></label>
                    <?php if (!empty($main_categories)) { ?>
                        <select class="form-control select-filter" id="MainCategoryId" name="MainCategoryId">
                            <option value="">Select Main Category</option>
                            <?php foreach ($main_categories as $category) { ?>
                                <option value="<?php echo $category['Id']; ?>" <?php echo set_select('MainCategoryId', $category['Id']); ?>><?php echo $category['CategoryName']; ?></option>
                            <?php } ?>
                        </select>
                    <?php } ?>
                    <div class="error">
                        <?php echo form_error('product_main_category'); ?>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="chk-home-page">    
                        <input type="checkbox" class="icheck-minimal-check one_required" name='home_page' id="home_page" value="1" >
                            Home Page 
                    </div>                    
                    <div class="error">
                        <?php echo form_error('home_page'); ?>
                    </div>
                </div>
                
            </div>
        </div>
        
        <div class="form-group">
            <div class="row">
                <div class="col-md-12">
                    <label for="product_name">Advertisement Title <span>*</span></label>

                    <input type="text" class="form-control" name="AdvertisementTitle" placeholder="Advertisement Title" value="<?php echo set_value('AdvertisementTitle'); ?>">
                    <div class="error">
                        <?php echo form_error('AdvertisementTitle'); ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="row">
                <div class="col-md-12">
                    <label for="AdvertisementDescription">Description <span>*</span></label>
                    <textarea class="form-control" name="AdvertisementDescription" placeholder="Description" rows="1"><?php echo set_value('AdvertisementDescription'); ?></textarea>
                    <div class="error">
                        <?php echo form_error('AdvertisementDescription'); ?>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="form-group">
            <div class="row">
                <div class="col-md-12">
                    <label for="AdvertisementUrl">Advertisement URL </label>
                    <input type="text" class="form-control" name="AdvertisementUrl" placeholder="Advertisement Url" value="<?php echo set_value('AdvertisementUrl'); ?>">
                    <div class="error">
                        <?php echo form_error('AdvertisementUrl'); ?>
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
                        <label for="price_to" >End Date <span>*</span></label>

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
                <div class="col-md-12">
                    <button type="submit" class="btn btn-primary btn-xs block full-width m-b">Add</button>
                    <a class="btn btn-danger btn-xs block full-width m-b" href="<?php echo site_url('/advertisements'); ?>">Cancel</a>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-5 margin20">
        <div class="btn-group profile_image_group ads-image-group">
            <label for="inputImage" class="btn btn-primary btn-xs product_image">
                <span title="Upload Image" style="background: rgba(254, 254, 254, 0.7) url('<?php echo base_url() ?>../assets/admin/img/upload.png') no-repeat scroll center center / 25px 25px;">
                <input type="file" accept="image/*" name="AdvertisementImage"  class="hide" id="inputImage">
                </span>
                <!--<div id="image_text">Upload Advertisement Image</div>-->
            </label>
        </div>
        <div class="error">
            <?php echo form_error('AdvertisementImage'); ?>
        </div>
        
        <div class="form-group margintop20">
            <div class="row">
                <div class="col-md-12">
                    New Client: <input type="radio" class="rdobtn" name="client_type" id="new_client" value="new" checked="yes" />
                    Existing Client: <input type="radio" class="rdobtn" name="client_type" id="existing_client" value="existing"/>
                </div>
            </div>
        </div>
        
        <div id="new_client_area" style="display:none">
            
            <div class="form-group">
                <div class="row">
                    <div class="col-md-6">
                        <label for="product_name">Company Name <span>*</span></label>

                        <input type="text" class="form-control" name="CompanyName" placeholder="Company Name" value="<?php echo set_value('CompanyName'); ?>">
                        <div class="error">
                            <?php echo form_error('CompanyName'); ?>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <label for="product_name">Client Email <span>*</span></label>

                        <input type="text" class="form-control" name="ClientEmail" placeholder="Client Email" value="<?php echo set_value('ClientEmail'); ?>">
                        <div class="error">
                            <?php echo form_error('ClientEmail'); ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="row">
                    <div class="col-md-6">
                        <label for="product_name">Contact Number <span>*</span></label>

                        <input type="text" class="form-control" name="ContactNumber" placeholder="Contact Number" value="<?php echo set_value('ContactNumber'); ?>">
                        <div class="error">
                            <?php echo form_error('ContactNumber'); ?>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <label for="product_name">Contact Person <span>*</span></label>

                        <input type="text" class="form-control" name="ContactPerson" placeholder="ContactPerson" value="<?php echo set_value('ContactPerson'); ?>">
                        <div class="error">
                            <?php echo form_error('ContactPerson'); ?>
                        </div>
                    </div>                    
                </div>
            </div>        
        </div> 
        
        <div id="existing_client_area" style="display:none">
           <div class="form-group">
            <div class="row">
                
                    <div class="col-md-6">
                        <label for="retailers">Retailers <span>*</span></label><br>

                        <select class="form-control select-filter" id="RetailerId" name="RetailerId" style="width:200px;">
                            <option value="">Select Retailer</option>
                            <?php
                            if (!empty($retailers)) :
                                foreach ($retailers as $retailer) {
                                    echo "<option value='" . $retailer['Id'] . "'" . (( @$_POST['retailers'] == $retailer['Id'] ) ? 'selected' : '') . ">" . $retailer['CompanyName'] . "</option>";
                                }
                            endif;
                            ?>
                        </select>
                        <div class="error">
                            <?php echo form_error('RetailerId'); ?>
                        </div>
                    </div>
                
                    
                
                    <div class="col-md-6">

                        <label for="store_format">Store Format <span>*</span></label><br>
                        <select class="form-control select-filter" id="StoreTypeId" name="StoreTypeId" style="width:200px;">
                            <?php /* ?>
                            <option value="">Select Store Format</option>
                            <?php
                            if (!empty($store_formats)) :
                                foreach ($store_formats as $store_format) {
                                    echo "<option value='" . $store_format['Id'] . "'" . ( ( @$_POST['store_format'] == $store_format['Id'] ) ? 'selected' : '') . ">" . $store_format['StoreType'] . "</option>";
                                }
                            endif;
                            ?>
                            <?php */ ?>
                        </select>
                        <div class="error">
                            <?php echo form_error('StoreTypeId'); ?>
                        </div>
                    </div>
               
            </div>
          </div>           
           
           <div class="form-group">
            <div class="row">
                <div class="col-md-6">
                    <label for="retailers">Stores <span>*</span></label><br>
                    <select class="form-control select-filter" id="StoreId" name="StoreId" style="width:200px;">                            
                    </select>
                    <div class="error">
                        <?php echo form_error('stores'); ?>
                    </div>
                </div>
            </div>
          </div>
           
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
   activateMenu('admin/advertisements');
});
</script>