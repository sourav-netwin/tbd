<!-- /.row -->

<div class="row">
      <?php echo form_open_multipart('users/add', array('id' => 'user_form', 'class' => 'form-horizontal',' autocomplete'=>'off')); ?>

    <div class="col-md-3">
        <div class="btn-group profile_image_group">
            <label for="inputImage" class="btn btn-primary btn-xs profile_image">
               <input type="file" accept="image/*" name="profile_image" class="hide" id="inputImage">
                Upload Profile Image
            </label>
        </div>
    </div>

    <div class="col-md-6">
         <div class="form-group">
            <div class="row">
                <div class="col-md-6">
                    <label for="first_name" >Name <span>*</span></label>
                    <input type="text" class="form-control" name="first_name" placeholder="First Name" value="<?php echo set_value('first_name'); ?>">
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
                <div class="col-md-6">
                    <label for="email" >Email Address <span>*</span></label>
                    <input type="email" class="form-control" name="email" placeholder="Email Address" value="">
                    <div class="error">
                        <?php echo form_error('email'); ?>
                    </div>
                </div>
                 <div class="col-md-6">
                    <label for="user_role">User Role <span>*</span></label>
                    <select name="user_role"  class="form-control select-filter">
                        <option value=""> Select Role </option>
                        <?php foreach ($user_roles as $user_role): ?>
                            <option value="<?php echo $user_role['Id'] ?>"><?php echo $user_role['Type'] ?></option>
                        <?php endforeach ?>
                    </select>
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
                    <input type="text" class="form-control" name="street_address" placeholder="Street Address" value="">
                    <div class="error">
                        <?php echo form_error('street_address'); ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="row">
                 <div class="col-md-4">
                    <label for="state" >State</label>
                    <select name="state"  class="form-control select-filter">
                        <option value=""> Select State </option>
                        <?php foreach ($states as $state): ?>
                            <option value="<?php echo $state['Id'] ?>"><?php echo $state['Name'] ?></option>
                        <?php endforeach ?>
                    </select>
                </div>
                <div class="col-md-5">
                    <label for="city" >City</label>
                    <input type="text" class="form-control" name="city" placeholder="City" value="">
                </div>
                <div class="col-md-3">
                    <label for="pin_code">Pin Code</label>
                    <input type="text" class="form-control" name="pin_code" placeholder="Pin Code" value="">
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
        </form>
    </div>
</div>
