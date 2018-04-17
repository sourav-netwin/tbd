<?php $this->load->view('admin/includes/header'); ?>

<body class="hold-transition login-page">
    <div class="login-box">

         <div class="login-logo">
            <a href="<?php echo base_url(); ?>"><img src="<?php echo $this->config->item('admin_assets'); ?>img/logo.png" class="" alt="User Image"></a>
             <span class="text"><b>The Best Deals</b></span>
        </div>

        <div class="login-box-body box box-primary">
            <p class="login-box-msg">Reset Password</p>
            <?php if (isset($message)) { ?>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="alert alert-success alert-dismissable">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                            <?php echo $message ?>
                        </div>
                    </div>
                </div>
            <?php } ?>
            <?php echo form_open('home/reset_password'); ?>
                <input type="hidden" value="<?php echo $this->input->get('tkn') ?>" name="tkn">
                <div class="form-group">
                    <input type="password" name="password" class="form-control" placeholder="Password">
                    <div class="error">
                        <?php echo form_error('password'); ?>
                    </div>
                </div>

                <div class="form-group">
                    <input type="password" name="confirm_password" class="form-control" placeholder="Retype Password">
                    <div class="error">
                        <?php echo form_error('confirm_password'); ?>
                    </div>
                </div>

                <button type="submit" class="btn btn-default">Submit</button>
                <a href="<?php echo base_url();?>home/login"><small>Login</small></a>
            </form>
        </div><!-- /.login-box-body -->
    </div><!-- /.login-box -->

        <?php $this->load->view('admin/includes/footer'); ?>
