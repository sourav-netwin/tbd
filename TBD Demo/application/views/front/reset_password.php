<section class="one-section">
    <div class="container">
        <h2 class="underline">Reset Password</h2>
        <?php echo form_open(front_url() . 'reset_password/' . $token, array('id' => 'reset_password_form', 'class' => 'form-horizontal custom_form mtop-40')); ?>
        <div style="display:none">
            <input name="csrf_tbd_token" value="<?php echo $this -> security -> get_csrf_hash(); ?>" type="hidden">
        </div>
        <div class="form-group">
            <label for="" class="col-sm-3 col-md-2 control-label">Password<span class="text-danger">*</span></label>
            <div class="col-sm-6 col-md-4">
                <input type="password" class="form-control" id="password" name="password" placeholder="Password" value="<?php echo set_value('password') ?>">
                <div class="error">
                    <?php echo form_error('password'); ?>
                </div>
            </div>
        </div>
        <div class="form-group">
            <label for="" class="col-sm-3 col-md-2 control-label">Repeat Password<span class="text-danger">*</span></label>
            <div class="col-sm-6 col-md-4">
                <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Repeat Password" value="<?php echo set_value('confirm_password') ?>">
                <div class="error">
                    <?php echo form_error('confirm_password'); ?>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-6">
                <input type="submit" class="btn btn-login" value="Submit" data-loading-text="Submitting...">
                <a href="<?php echo front_url(); ?>login"><small>Login</small></a>
            </div>
        </div>
        </form>
    </div>
</section>