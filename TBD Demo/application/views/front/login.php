<section class="one-section">
    <div class="container">
        <div class="sm-sec">
            <!--<h2 class="underline"></h2>
            <p>Select the user to sign with</p>-->
            <div class="g_title_wrap">
                Customer Sign In
            </div>
            <div class="user_img col-xs-3 mr-tp-20">
                <img src="<?php echo $this -> config -> item('front_assets'); ?>img/sign-in.png" class="img-responsive">
            </div>
            <div class="form_wrap col-xs-offset-1 col-xs-6 mr-tp-20">
                <?php echo form_open(front_url() . 'login/signup', array('id' => 'login_form', 'class' => 'form-horizontal custom_form')); ?>
                <div class="form-group">
                    <label for="email" class="control-label mr-lt-15">Email</label>
                    <div class="col-xs-12">
                        <input type="email" class="form-control" id="email" name="email" placeholder="Email" value="<?php echo ( set_value('email') != '' ? set_value('email') : $this -> input -> cookie('email', TRUE) ); ?>">
                        <div class="error">
                            <?php echo form_error('email'); ?>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="password" class="control-label mr-lt-15">Password</label>
                    <div class="col-xs-12">
                        <input type="password" class="form-control" id="password" name="password" placeholder="Password" value="<?php echo ( set_value('password') != '' ? set_value('password') : $this -> input -> cookie('password', TRUE) ); ?>">
                        <div class="error">
                            <?php echo form_error('password'); ?>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-xs-6">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="remember" id="remember" <?php echo ( $this -> input -> cookie('email', TRUE) != '' && $this -> input -> cookie('password', TRUE) != '' ) ? 'checked' : '' ?>>  <span>Remember me</span>
                            </label>
                        </div>
                        <div class="form-group mr-tp-5 mr-lt-m-4">
                            <a href="javascript:void(0);" data-toggle="modal" data-target="#forgot_password_modal">Forgot Password?</a>
                        </div>
                    </div>
                    <div class="col-xs-6 text-right">
                        <input type="submit" class="btn btn-xs btn-login" value="Sign in">
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-xs-12 text-center">
                        <p>You can also Sign In with</p>
                        <ul class="social ">
                            <li><a href="javascript:void(0)"><i class="fa fa-facebook circle" data-called-from="login"></i></a></li>
                            <li><a href="javascript:void(0)"><i class="fa fa-twitter circle" data-called-from="login"></i></a></li>
                            <li><a href="<?php echo $this -> instagram_api -> instagramLogin() ?>" class="social-registration"  title="Instagram"><i class="fa fa-instagram circle" data-called-from="registration"></i></a></li>
                        </ul>
                    </div>
                </div>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- Forgot Password modal -->
<div class="modal fade" id="forgot_password_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Forgot Password</h4>
            </div>
            <div class="modal-body">
                <?php echo form_open(front_url() . 'login/forgot_password', array('id' => 'forgot_password_form', 'class' => 'form-horizontal custom_form')); ?>
                <div class="form-group">
                    <label for="forgot_pwd_email" class="col-sm-1 control-label">Email</label>
                    <div class="col-sm-9">
                        <input type="email" class="form-control" id="forgot_pwd_email" name="forgot_pwd_email" placeholder="Email" value="<?php echo set_value('forgot_pwd_email'); ?>">
                        <div class="error">
                            <?php echo form_error('forgot_pwd_email'); ?>
                        </div>
                    </div>
                </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-login" name="forgot_pwd_submit" id="forgot_pwd_submit">Send</button>
            </div>
            <input type="hidden" name="forgot_pwd_error" id="forgot_pwd_error" value="<?php echo $this -> session -> userdata('forgot_pwd_error'); ?>">
            <?php
            $this -> session -> unset_userdata('forgot_pwd_error');
            ?>
        </div>
    </div>
</div>