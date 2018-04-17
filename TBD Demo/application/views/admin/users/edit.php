<!-- /.row -->

<div class="row">
    <?php echo form_open_multipart('users/edit_post/' . $Id, array('id' => 'user_form', 'class' => 'form-horizontal',' autocomplete'=>'off')); ?>
    
        <div class="col-xs-7 col-xs-offset-1">

            <div class="form-group">
                <div class="row">
                    <div class="col-sm-6 col-md-6">
                        <label for="telephone" >First Name <span>*</span></label>
                        <input type="text" class="form-control" name="first_name" placeholder="First Name" value="<?php echo $FirstName; ?>">
                        <div class="error">
                            <?php echo form_error('first_name'); ?>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-6">
                        <label for="mobile" >Last Name <span>*</span></label>
                        <input type="text" class="form-control" name="last_name" placeholder="Last Name" value="<?php echo $LastName; ?>">
                        <div class="error">
                            <?php echo form_error('last_name'); ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="form-group">
                <div class="row">
                    <?php
                    if ($UserRole > 0) {
                        ?>
                        <div class="col-sm-6 col-md-6">
                            <label for="user_role">User Role <span>*</span></label>
                            <select name="user_role"  class="form-control select-filter">
                                <option value=""> Select User Role </option>
                                <?php foreach ($user_roles as $user_role): ?>
                                    <option value="<?php echo $user_role['Id'] ?>" <?php echo ( $UserRole == $user_role['Id'] ) ? "selected" : ""; ?>><?php echo $user_role['Type'] ?></option>
                                <?php endforeach ?>
                            </select>
                        </div>
                        <?php
                    }
                    ?>
                    <div class="<?php echo $UserRole > 0 ? 'col-sm-6 col-md-6' : 'col-sm-12 col-md-12' ?>">
                        <label for="email" >Email Address <span>*</span></label>
                        <input type="email" class="form-control" name="email" placeholder="Email Address" value="<?php echo $Email; ?>">
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
                            <input type="text" class="form-control" name="telephone" placeholder="Telephone (Home)" value="<?php echo $TelephoneFixed; ?>">
                            <div class="error">
                                <?php echo form_error('telephone'); ?>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-6">
                            <label for="mobile" >Mobile No. <span>*</span></label>
                            <input type="text" class="form-control" name="mobile" placeholder="Mobile No." value="<?php echo $Mobile; ?>">
                            <div class="error">
                                <?php echo form_error('mobile'); ?>
                            </div>
                        </div>
                    </div>
                </div>
            <div class="form-group">
                <div class="row">
                    <div class="col-md-12">
                        <label for="street_address">Street Address</label>
                        <input type="text" class="form-control" id="street_address" name="street_address" placeholder="Street Address" value="<?php echo $StreetAddress; ?>">
                        <div class="error">
                            <?php echo form_error('street_address'); ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="row">
                    <div class="col-sm-4 col-md-4">
                        <label for="state" >State</label>
                        <select name="state"  class="form-control select-filter">
                            <option value=""> Select State </option>
                            <?php foreach ($states as $state): ?>
                                <option value="<?php echo $state['Id'] ?>" <?php echo ( $State == $state['Id'] ) ? 'selected' : '' ?>><?php echo $state['Name'] ?></option>
                            <?php endforeach ?>
                        </select>
                        <!--<input type="text" class="form-control" name="state" placeholder="state" value="<?php //echo $State; ?>">-->
                    </div>
                    <div class="col-sm-4 col-md-4">
                        <label for="city" >City</label>
                        <input type="text" class="form-control" name="city" placeholder="City" value="<?php echo $City; ?>">
                    </div>
                    <div class="col-sm-4 col-md-4">
                        <label for="pin_code">Zip Code</label>
                        <input type="text" class="form-control" name="pin_code" placeholder="Zip Code" value="<?php echo $PinCode; ?>">
                    </div>
                </div>
            </div>


            <div class="form-group">
                <div class="row">
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-primary btn-xs block full-width m-b">Update</button>
                        <!--<a class="btn btn-danger btn-xs block full-width m-b" href="<?php //echo site_url('/users');     ?>">Cancel</a>-->
                    </div>
                </div>
            </div>

        </div>
        <?php
        $image_path = front_url() . USER_IMAGE_PATH . '/medium/' . $ProfileImage;
        $style = ( $ProfileImage != '' && file_exists('./' . USER_IMAGE_PATH . '/medium/' . $ProfileImage) ) ? 'style="background-image: url(' . $image_path . ') "' : '';
        ?>
        <div class="col-xs-3">
            <div class="btn-group profile_image_group">
                <label for="inputImage" class="btn btn-primary btn-xs profile_image" <?php echo $style ?>>
                    <span title="Upload Image" style="background: rgba(254, 254, 254, 0.7) url('<?php echo base_url() ?>../assets/admin/img/upload.png') no-repeat scroll center center / 25px 25px;">
                        <input type="file" accept="image/*" name="profile_image" class="hide" id="inputImage">
                    </span>
                    <!--<div id="image_text">Upload Profile Image</div>-->
                    <div class="error" style="line-height: 0px;">
                        <?php echo form_error('profile_image'); ?>
                    </div>
                </label>
            </div>
            <input type="hidden" name="old_photo" id="old_photo" value="<?php echo $ProfileImage ?>">

        </div>
    



    <!-- Add Modal -->

    <input type="hidden" name="image-x" id="image-x">
    <input type="hidden" name="image-y" id="image-y">
    <input type="hidden" name="image-width" id="image-width">
    <input type="hidden" name="image-height" id="image-height">

    </form>
</div>
