<div class="row">
    <?php echo form_open('users/change_password/' . $id, array('id' => 'change_password', 'class' => 'form-horizontal',' autocomplete'=>'off')); ?>

        <div class="col-md-5 col-md-offset-1">
            <input type="hidden" name="id" value="<?php echo $id ?>">

            <div class="form-group">
                <div class="row">
                    <div class="col-md-12">
                        <label for="name">Name</label>
                        <input type="text" class="form-control" name="name" placeholder="Name" readonly="true" value="<?php echo $user_details['FirstName']." ".$user_details['LastName']; ?>">
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
                        <button type="submit" class="btn btn-primary btn-xs block full-width m-b">Update</button>
                        <a class="btn btn-danger btn-xs block full-width m-b" href="<?php echo site_url('/users'); ?>">Cancel</a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>