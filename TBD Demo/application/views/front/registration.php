<section class="one-section">
    <div class="container">
        <div class="sm-sec">
            <h2 class="underline">Registration</h2>
            <div class="form-group">
                <div class="col-md-12 col-md-12">
                    <p>You can Sign Up with</p>
                    <div class="social"><ul class="social pull-left social-container front-pg">
                            <li><a href="javascript:void(0)" class="social-registration login-fb" title="Facebook" style="background:url('<?php echo $this -> config -> item('front_assets'); ?>img/social/facebook_50.png') no-repeat scroll 0% 0% / 30px 30px"></a></li>
                            <li><a href="javascript:void(0)" class="social-registration login-twitter"  title="Twitter" style="background:url('<?php echo $this -> config -> item('front_assets'); ?>img/social/twitter_50.png') no-repeat scroll 0% 0% / 30px 30px"></a></li>
                            <li><a href="<?php echo $this -> instagram_api -> instagramLogin() ?>" class="social-registration"  title="Instagram" style="background:url('<?php echo $this -> config -> item('front_assets'); ?>img/social/instagram_50.jpg') no-repeat scroll 0% 0% / 30px 30px"></a></li>
                            <li><a href="javascript:void(0)" id="registration-email"  title="Email" style="background:url('<?php echo $this -> config -> item('front_assets'); ?>img/social/mail_50.jpg') no-repeat scroll 0% 0% / 30px 30px"></a></li>
                        </ul>
                    </div>

                </div>
            </div>
            <div id="registration-form" class="form-group col-sm-12 hide">
                <?php echo form_open(front_url() . 'registration/register#n', array('id' => 'registration_form', 'class' => 'form-horizontal custom_form mtop-40')); ?>

                <div style="display:none">
                    <input type="hidden" name="csrf_tbd_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
                </div>
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-3 col-md-2 col-md-2 control-label">Name <span class="text-danger">*</span></label>
                    <div class="col-sm-3 col-md-2">
                        <input type="text" class="form-control" id="first_name" name="first_name" placeholder="First Name" value="<?php echo set_value('first_name') ?>">
                        <div class="error">
                            <?php echo form_error('first_name'); ?>
                        </div>
                    </div>
                    <div class="col-sm-3 col-md-2">
                        <input type="text" class="form-control" id="last_name" name="last_name" placeholder="Last Name" value="<?php echo set_value('last_name') ?>">
                        <div class="error">
                            <?php echo form_error('last_name'); ?>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="" class="col-sm-3 col-md-2 control-label">Telephone (Home) <span class="text-danger">*</span></label>
                    <div class="col-sm-6 col-md-4">
                        <input type="text" class="form-control" id="telephone" name="telephone" placeholder="Telephone (Home)" value="<?php echo set_value('telephone') ?>">
                        <div class="error">
                            <?php echo form_error('telephone'); ?>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="" class="col-sm-3 col-md-2 control-label">Mobile No.<span class="text-danger">*</span></label>
                    <div class="col-sm-6 col-md-4">
                        <input type="text" class="form-control" id="mobile_number" name="mobile_number" placeholder="Mobile No." value="<?php echo set_value('mobile_number') ?>">
                        <div class="error">
                            <?php echo form_error('mobile_number'); ?>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="" class="col-sm-3 col-md-2 control-label">Email Address<span class="text-danger">*</span></label>
                    <div class="col-sm-6 col-md-4">
                        <input type="text" class="form-control" id="email" name="email" placeholder="Email Address" value="<?php echo set_value('email') ?>">
                        <div class="error">
                            <?php echo form_error('email'); ?>
                        </div>
                    </div>
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
                    <label for="" class="col-sm-3 col-md-2 control-label">Address<span class="text-danger">*</span></label>
                    <div class="col-sm-6 col-md-4">
                        <input type="text" class="form-control" id="house_number" name="house_number" placeholder="House No." value="<?php echo set_value('house_number') ?>">
                        <div class="error">
                            <?php echo form_error('house_number'); ?>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-offset-3 col-md-offset-2  col-sm-6 col-md-4">
                        <input type="text" class="form-control" id="street_name" name="street_name" placeholder="Street Name" value="<?php echo set_value('street_name') ?>">
                        <div class="error">
                            <?php echo form_error('street_name'); ?>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-offset-3 col-md-offset-2  col-sm-6 col-md-4">
                        <input type="text" class="form-control" id="suburb" name="suburb" placeholder="Suburb" value="<?php echo set_value('suburb') ?>">
                        <div class="error">
                            <?php echo form_error('suburb'); ?>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-offset-3 col-md-offset-2  col-sm-6 col-md-4">
                        <input type="text" class="form-control" id="city" name="city" placeholder="City" value="<?php echo set_value('city') ?>">
                        <div class="error">
                            <?php echo form_error('city'); ?>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-offset-3 col-md-offset-2  col-sm-6 col-md-4">
                        <input type="text" class="form-control" id="province" name="province" placeholder="Province" value="<?php echo set_value('province') ?>">
                        <div class="error">
                            <?php echo form_error('province'); ?>
                        </div>
                    </div>
                </div>
               	<div class="form-group">
                    <label for="" class="col-sm-3 col-md-2 control-label">Pin Code<span class="text-danger">*</span></label>
                    <div class="col-sm-6 col-md-4">
                        <input type="text" class="form-control" id="pin_code" name="pin_code" placeholder="Pin Code" value="<?php echo set_value('pin_code') ?>">
                        <div class="error">
                            <?php echo form_error('pin_code'); ?>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-offset-3 col-md-offset-2 col-sm-6">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="terms_conditions" id="terms_conditions">  <span>I agree with</span>
                            </label>
                            <a href="javascript:void(0);" data-toggle="modal" data-target="#terms_conditions_modal">Terms & Conditions</a>
                        </div>
                        <div class="error">
                            <?php echo form_error('terms_conditions'); ?>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-6">
                        <input type="submit" class="btn btn-login" value="Sign in" data-loading-text="Signing in...">
                    </div>
                </div>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- Terms and conditions modal -->
<div class="modal fade" id="terms_conditions_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Terms and conditions</h4>
            </div>
            <div class="modal-body">
                <?php echo $terms_and_conditions; ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>