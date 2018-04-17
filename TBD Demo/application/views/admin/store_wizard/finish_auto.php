<!-- /.row -->

<div class="row welcome_screen  ">
    <div class="col-md-12">
        <p> Congrats <b><?php echo $this->session->userdata('user_full_name'); ?></b>
            <br />
        </p>
    </div>
    <div class="col-md-12">
        <p>We’re all done. #<?php echo $added_cat_count; ?> were added to your Store.</p>
        <p>That was quick.</p>
    </div>
    <div class="col-md-12">
        <p>Now all YOU have to do is start building your Promos</p>
    </div>
    <div class="col-md-12">
        <p><a href="<?php echo base_url() ?>home/dashboard" class="btn btn-primary btn-xs block full-width m-b">Let's start</a></p>
    </div>
</div>