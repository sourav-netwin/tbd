<!-- /.row -->
<?php echo validation_errors(); ?>
<div class="row">
    <?php echo form_open_multipart('retailers/add', array('id' => 'retailers_form', 'class' => 'form-horizontal','autocomplete'=>'off')); ?>

    <div class="col-md-5 col-md-offset-1">

        <div class="form-group">
            <div class="row">
                <div class="col-md-12">
                    <label for="groupId[]" >Group <span>*</span></label>
                    <select name="groupId[]" id="groupId" class="form-control select-filter" multiple="multiple" style="height:50px">
                        <?php foreach ($store_groups as $store_group): ?>
                            
                            <option value="<?php echo $store_group['Id'] ?>" ><?php echo $store_group['GroupName'] ?></option>
                        
                        <?php endforeach; ?>
                    </select>                    
                </div>
                
                <div class="col-md-12">                    
                    <div class="error" id="groupId_error">
                        <?php echo form_error('groupId'); ?>
                    </div>
                </div>                
            </div>
        </div>
        
        <div class="form-group">
            <div class="row">
                <div class="col-md-12">
                    <label for="company_name">Company Name <span>*</span></label>
                    <input type="text" class="form-control" name="company_name" placeholder="Company Name" value="<?php echo set_value('company_name'); ?>">
                    <div class="error">
                        <?php echo form_error('company_name'); ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="row">
                <div class="col-md-12">
                    <label for="company_description">Company Description <span>*</span></label>

                    <textarea class="form-control" name="company_description" placeholder="Company Description"> <?php echo set_value('company_description'); ?></textarea>
                    <div class="error">
                        <?php echo form_error('company_description'); ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="row">
                <div class="col-md-12">
                    <label for="street_address">Street Address </label>
                    <input type="text" class="form-control" name="street_address" id="street_address"placeholder="Street Address" value="<?php echo @$_POST['street_address']; ?>">
                    <div class="error">
                        <?php echo form_error('street_address'); ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="row">
                <div class="col-md-12">
                    <label for="state" >State </label>
                    <select name="state" id="state" class="form-control select-filter">
                        <option value=""> Select State </option>
                        <?php foreach ($states as $state): ?>
                            <option value="<?php echo $state['Id'] ?>" <?php echo ( @$_POST['state'] == $state['Id'] ) ? 'selected' : '' ?>><?php echo $state['Name'] ?></option>
                        <?php endforeach ?>
                    </select>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="row">
                <div class="col-md-6">
                    <label for="city">City</label>
                    <input type="text" class="form-control" id="city" name="city" placeholder="City" value="<?php echo @$_POST['city'] ?>">
                    <div class="error">
                        <?php echo form_error('city'); ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <label for="zip">Zip Code</label>
                    <input type="text" class="form-control" id="zip" name="zip" placeholder="Zip Code" value="<?php echo @$_POST['zip'] ?>">
                    <div class="error">
                        <?php echo form_error('zip'); ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="row"><h3 class="box-title">Retailer Admin Details</h3></div>
        <div class="form-group">
            <div class="row">
                <div class="col-md-6">
                    <label for="first_name" >Retailer Admin First Name <span>*</span></label>
                    <input type="text" class="form-control" name="first_name" placeholder="First Name" value="<?php echo set_value('first_name'); ?>">
                    <div class="error">
                        <?php echo form_error('first_name'); ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <label  for="last_name">Retailer Admin Last Name  <span>*</span></label>
                    <input type="text" class="form-control" name="last_name" placeholder="Last Name" value="<?php echo set_value('last_name'); ?>">
                    <div class="error">
                        <?php echo form_error('last_name'); ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="row">
                <div class="col-md-12">
                    <label for="email" >Email Address <span>*</span></label>
                    <input type="email" class="form-control" name="email" placeholder="Email Address" value="">
                    <div class="error">
                        <?php echo form_error('email'); ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="row">
                <div class="col-md-6">
                    <label for="password">New Password <span>*</span></label>
                    <input type="password" name="password" id="password" class="form-control" placeholder="New Password">
                    <div class="error">
                        <?php echo form_error('password'); ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <label for="confirm_password">Confirm Password <span>*</span></label>
                    <input type="password" name="confirm_password" class="form-control" placeholder="Confirm Password">
                    <div class="error">
                        <?php echo form_error('confirm_password'); ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="row">
                <div class="col-md-6">
                    <label for="contact_tel">Contact Telephone<span></span></label>
                    <input type="text" class="form-control" name="contact_tel" placeholder="Contact Telephone" value="<?php echo @$_POST['contact_tel'] ?>" maxlength="30">
                    <div class="error">
                        <?php echo form_error('contact_tel'); ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="row">
                <div class="col-md-12">
                    <button type="submit" class="btn btn-primary btn-xs block full-width m-b">Add</button>
                    <a class="btn btn-danger btn-xs block full-width m-b" href="<?php echo site_url('/retailers'); ?>">Cancel</a>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="btn-group profile_image_group">
            <label for="inputImage" class="btn btn-primary btn-xs retailer_image">
                <span title="Upload Image" style="background: rgba(254, 254, 254, 0.7) url('<?php echo base_url() ?>../assets/admin/img/upload.png') no-repeat scroll center center / 25px 25px;">
                    <input type="file"  accept="image/*" name="logo" placeholder="Logo" value="" class="hide" id="inputImage">
                </span>
                <!--<div id="image_text">Upload Company Logo</div>-->
            </label>
        </div>

        <div class="error">
            <?php echo form_error('logo'); ?>
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
                        <img src="<?php echo $this -> config -> item('admin_assets'); ?>img/logo.jpg">
                    </div>

                    <div><a id="crop-button" class="btn btn-primary btn-xs block full-width m-b">Select</a></div>
                    
                    <input type="hidden" name="image-x" id="image-x">
                    <input type="hidden" name="image-y" id="image-y">
                    <input type="hidden" name="image-width" id="image-width">
                    <input type="hidden" name="image-height" id="image-height">

                    <input type="hidden" name="aspect_ratio" id="aspect_ratio" value="2.80">
                </div>
            </div>
        </div>
    </div>

</form>
</div>