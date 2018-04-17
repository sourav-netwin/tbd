<!-- /.row -->

<div class="row">
    <?php echo form_open_multipart('users/add', array('id' => 'user_form', 'class' => 'form-horizontal',' autocomplete'=>'off')); ?>
    <div class="col-md-5 col-md-offset-1">

        <div class="form-group">
            <div class="row">
                <div class="col-md-12">
                    <label for="user_role">User Role <span>*</span></label>
                    <select name="user_role" class="form-control select-filter">
                        <option value=""> Select User Role </option>
                        <?php foreach ($user_roles as $user_role): ?>
                            <option value="<?php echo $user_role['Id'] ?>" <?php echo set_select('user_role', $user_role['Id']); ?>><?php echo $user_role['Type'] ?></option>
                        <?php endforeach ?>
                    </select>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="row">
                <div class="col-md-6">
                    <label for="first_name" >Name <span>*</span></label>
                    <input type="text" class="form-control" name="first_name" alpha-numeric placeholder="First Name" value="<?php echo set_value('first_name'); ?>">
                    <div class="error">
                        <?php echo form_error('first_name'); ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <label>&nbsp;</label>
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
                    <input type="email" class="form-control" name="email" placeholder="Email Address" value="<?php echo set_value('email'); ?>">
                    <div class="error">
                        <?php echo form_error('email'); ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group">
                <div class="row">
                    <div class="col-sm-6 col-md-6">
                        <label for="telephone" >Telephone (Home) <span>*</span></label>
                        <input type="text" class="form-control" name="telephone" placeholder="Telephone (Home)" autocomplete="false" value="<?php echo set_value('telephone'); ?>">
                        <div class="error">
                            <?php echo form_error('telephone'); ?>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-6">
                        <label for="mobile" >Mobile No. <span>*</span></label>
                        <input type="text" class="form-control" name="mobile" placeholder="Mobile No."  value="<?php echo set_value('mobile'); ?>" autocomplete="false">
                        <div class="error">
                            <?php echo form_error('mobile'); ?>
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
                <div class="col-md-12">
                    <label for="street_address">Street Address</label>
                    <input type="text" class="form-control" id="street_address" name="street_address" placeholder="Street Address" value="<?php echo @$_POST['street_address']; ?>">
                    <div class="error">
                        <?php echo form_error('street_address'); ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="row">
                <div class="col-md-12">
                    <label for="state" >State</label>
                    <select name="state"  class="form-control select-filter">
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
                    <label for="city" >City</label>
                    <input type="text" class="form-control" name="city" placeholder="City" value="<?php echo @$_POST['city'] ?>">
                </div>
                <div class="col-md-6">
                    <label for="pin_code">Zip Code</label>
                    <input type="text" class="form-control" name="pin_code" placeholder="Zip Code" numeric-decimal value="<?php echo @$_POST['pin_code'] ?>">
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="row">
                <div class="col-md-12">
                    <button type="submit" class="btn btn-primary btn-xs block full-width m-b">Add</button>
                    <a class="btn btn-danger btn-xs block full-width m-b" href="<?php echo site_url('/users'); ?>">Cancel</a>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="btn-group profile_image_group">
            <label for="inputImage" class="btn btn-primary btn-xs profile_image">
                <span title="Upload Image" style="background: rgba(254, 254, 254, 0.7) url('<?php echo base_url()?>../assets/admin/img/upload.png') no-repeat scroll center center / 25px 25px;">
                <input type="file" accept="image/*" name="profile_image" class="hide" id="inputImage">
                </span>
                <div class="error" style="line-height: 0px;">
                    <?php echo form_error('profile_image'); ?>
                </div>
            </label>
        </div>
        <div class="img-preview img-preview-sm"></div>
    </div>


    <!-- Add Modal -->
    <div class="modal fade" id="imageModal" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel"> <span id="form-action">Select</span></h4>
                </div>
                <div class="modal-body" >
                    <div class="image-crop">
                        <img src="<?php echo $this->config->item('admin_assets'); ?>img/default.gif">
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
</div>
</form>
<script>
$(function(){
   activateMenu('admin/users');
});
</script>