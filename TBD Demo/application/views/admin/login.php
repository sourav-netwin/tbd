<?php $this->load->view('admin/includes/header'); ?>


<body class="hold-transition login-page">
    <div class="login-box">
        <div class="row">
            <div class="col-md-5 nopadding">
                <div class="login-logo">
                    <a href="<?php echo base_url(); ?>"><img src="<?php echo $this->config->item('admin_assets'); ?>img/logo.png" class="" alt="User Image"></a>
    <!--                <span class="text"><b>The Best Deals</b></span>-->
                </div>
            </div>
            <div class="col-md-6 nopadding">
                <div class="login-box-body box box-primary">
                    <p class="login-box-msg">Login</p>
                    <?php echo form_open('home/verifylogin'); ?>
                    <div class="form-group has-feedback">
                        <i class="fa fa-user"></i>
                        <input type="email" class="form-control" name="email" placeholder="Email Address" value="">
                        <div class="error">
                            <?php echo form_error('email'); ?>
                        </div>
                    </div>
                    <div class="form-group has-feedback">
                        <i class="fa fa-lock"></i>
                        <input type="password" name="password" class="form-control" placeholder="Password">
                        <div class="error">
                            <?php echo form_error('password'); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-8">
                            <button type="submit" class="btn btn-default">Sign In</button>
                            <a href="<?php echo base_url(); ?>home/forgot_password"  class="btn btn-default">Forgot password?</a>
                        </div><!-- /.col -->
                    </div>
                    </form>

                </div><!-- /.login-box-body -->
            </div><!-- /.login-box -->
        </div>
    </div>
    <?php $this->load->view('admin/includes/footer'); ?>
