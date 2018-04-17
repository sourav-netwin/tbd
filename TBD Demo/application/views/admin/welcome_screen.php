<!-- /.row -->

<div class="row welcome_screen">
    <div class="col-md-12">
        <p> Hello <b><?php echo $this->session->userdata('user_full_name'); ?></b>
            <br />
        </p>
    </div>

    <div class="col-md-12">
        <!--<p>Welcome to <a><b>The Best Deals.</b></a></p>-->
        <p>We can see this is your first time here. To make your experience enjoyable, we need to make sure that your Store information is correct.</p>
        <p>This only has to be done once.</p>
    </div>

     <div class="col-md-12">
        <p>Lets start by validating your Information:</p>
        <p>This includes the correct <strong>STORE</strong> details, contact information, address and Trading Hours</p>
        <br/>
    </div>

    <div class="col-md-12">
      <p>
      <a href="<?php echo site_url('/home/store/new'); ?>" class="btn btn-primary btn-xs block full-width m-b">Continue</a>
      </p>
    </div>
</div>