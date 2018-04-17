<div class="row">
    <?php echo form_open('account/change_password', array('id' => 'user_change_password', 'class' => 'form-horizontal')); ?>
        <div class="col-md-5 col-md-offset-1">

            <div class="form-group">
                <div class="row">
                    <div class="col-md-12">
                        <label for="password">Current Password</label>
                        <input type="password" name="old_password" class="form-control" placeholder="Current Password">
                        <div class="error">
                            <?php echo form_error('old_password'); ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="row">
                    <div class="col-md-6">
                        <label for="password">New Password</label>
                        <input type="password" name="password" class="form-control" placeholder="New Password">
                        <div class="error">
                            <?php echo form_error('password'); ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label for="password">Confirm Password</label>
                        <input type="password" name="confirm_password" class="form-control" placeholder="Retype Password">
                        <div class="error">
                            <?php echo form_error('confirm_password'); ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="row">
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-primary btn-xs block full-width m-b">Submit</button>
                        <a class="btn btn-danger btn-xs block full-width m-b" href="<?php echo site_url('/home/dashboard'); ?>">Cancel</a>
                    </div>
                </div>
            </div>

        </div>
    </form>
</div>