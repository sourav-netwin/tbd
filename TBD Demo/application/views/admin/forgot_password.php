<?php $this->load->view('admin/includes/header'); ?>
<body class="hold-transition login-page">
    <div class="login-box">

        <div class="login-logo">
            <a href="<?php echo base_url(); ?>"><img src="<?php echo $this->config->item('admin_assets'); ?>img/logo.png" class="" alt="User Image"></a>
            <span class="text"><b>The Best Deals</b></span>
        </div>

        <div class="login-box-body box box-primary">
            <p class="login-box-msg">Forgot Password</p>
            <?php if (isset($message)) { ?>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="alert alert-info alert-dismissable">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                            <?php echo $message ?>
                        </div>
                    </div>
                </div>
            <?php } ?>
            <?php echo form_open('home/send_password'); ?>

            <div class="form-group">
                <input type="email" class="form-control" name="email" placeholder="Email Address" value="">
                <div class="error">
                    <?php echo form_error('email'); ?>
                </div>
            </div>

            <button type="submit" class="btn btn-default">Submit</button>

            </form>
        </div><!-- /.login-box-body -->
    </div><!-- /.login-box -->

    <?php $this->load->view('admin/includes/footer'); ?>
