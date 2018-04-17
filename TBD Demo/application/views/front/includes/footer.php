<?php if (!empty($categories)) { ?>
    <section class="com_sec green-bg">
        <div class="container">
            <ul class="categories">
                <?php foreach ($categories as $key => $category) { ?>
                    <li><a href="<?php echo front_url() . 'productslist/index/main/' . $this -> encrypt -> encode($key) ?>"><?php echo $category; ?></a></li>
                <?php } ?>
            </ul>
        </div>
        <div class="social-footer">
            <ul class="social front-pg">
                <li><a title="Facebook" href="" style="background:url('<?php echo $this -> config -> item('front_assets'); ?>img/social/facebook_50.png') no-repeat scroll 0% 0% / 30px 30px"></i></a></li>
                <li><a title="Twitter" href="" style="background:url('<?php echo $this -> config -> item('front_assets'); ?>img/social/twitter_50.png') no-repeat scroll 0% 0% / 30px 30px"></a></li>
                <li><a title="Google Plus" href="" style="background:url('<?php echo $this -> config -> item('front_assets'); ?>img/social/google_50.png') no-repeat scroll 0% 0% / 30px 30px"></a></li>
                <li><a title="Instagram" href="" style="background:url('<?php echo $this -> config -> item('front_assets'); ?>img/social/instagram_50.jpg') no-repeat scroll 0% 0% / 30px 30px"></a></li>
                <li><a title="Retailer Login" id="retailer_login" href="javascript:void(0)" style="background:url('<?php echo $this -> config -> item('front_assets'); ?>img/retailer.png') no-repeat scroll 0% 0% / 75px 30px;background-position: 0px;"></a></li>
            </ul>
        </div>

    </section>
<?php } ?>

<div class="modal fade" id="terms_conditions_footer_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Terms and conditions</h4>
            </div>
            <div class="modal-body">
                <?php echo $this -> site_data['terms_and_conditions_glb']; ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<div class="help-email" id="popover-link"> 
    <i class="fa fa-envelope"></i>
    <!--<img src="<?php //echo base_url()   ?>../assets/front/img/mail_help.png" />-->
</div>
<div id="real-web-mail">
    <div class="web-mail-arrow"></div>
    <div class="mail-help-header">
        GET IN TOUCH
    </div>
    <?php echo form_open('', array('method' => 'post', 'id' => 'email_help_form')); ?>
    <div class="form-group">
        <div class="row">
            <div class="col-xs-12">
                <input class="form-control" placeholder="User Name*" type="text" name="user_name">
            </div>
        </div>
    </div>
    <div class="form-group">
        <div class="row">
            <div class="col-xs-12">
                <input class="form-control" placeholder="Email*" type="email" name="user_email">
            </div>
        </div>
    </div>
    <div class="form-group">
        <div class="row">
            <div class="col-xs-12">
                <input class="form-control" placeholder="Subject*" type="text" name="email_subject">
            </div>
        </div>
    </div>
    <div class="form-group">
        <div class="row">
            <div class="col-xs-12">
                <textarea placeholder="Email content*" style="max-height: 95px; width: 100%; resize: none" name="email_body"></textarea>
            </div>
        </div>
    </div>
    <div class="form-group">
        <div class="row">
            <div class="col-xs-12 text-right">
                <button type="submit" name="submit-btn" id="email-submit-btn" value="1" class="btn btn-success btn-xs">Submit <span class="glyphicon glyphicon-circle-arrow-right append"></span></button>
            </div>
        </div>
    </div>
</form>
</div> 
<footer class="footer">
    © 2015-2016 The Best Deals. All rights reserved.
</footer>

<!-- Toastr Messages -->
<div id="success_message" class="hide"><?php echo $this -> session -> userdata('success_message'); ?></div>
<div id="error_message" class="hide"><?php echo $this -> session -> userdata('error_message'); ?></div>
<?php
$this -> session -> unset_userdata('success_message');
$this -> session -> unset_userdata('error_message');
?>
<!-- End toastr messages -->

<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script src="<?php echo $this -> config -> item('front_assets'); ?>js/jquery.min.js"></script>
<script src="<?php echo $this -> config -> item('front_assets'); ?>js/jquery-ui.js"></script>
<script src="<?php echo $this -> config -> item('front_assets'); ?>js/plugins/nouislider.min.js"></script>
<script src="<?php echo $this -> config -> item('front_assets'); ?>js/plugins/wNumb.js"></script>
<!-- Include all compiled plugins (below), or include individual files as needed -->
<script src="<?php echo $this -> config -> item('front_assets'); ?>js/bootstrap.min.js"></script>
<script src="<?php echo $this -> config -> item('front_assets'); ?>js/jquery.parallax.js"></script>
<script src="<?php echo $this -> config -> item('front_assets'); ?>js/jquery.flexisel.js"></script>
<script type="text/javascript" src="<?php echo $this -> config -> item('front_assets'); ?>js/bootstrap-tabcollapse.js"></script>
<!-- Toastr Javascript -->
<script src="<?php echo $this -> config -> item('front_assets'); ?>js/plugins/toastr.min.js"></script>
<script src="<?php echo $this -> config -> item('front_assets'); ?>js/custom.js"></script>
<!-- Validate JavaScript -->
<script src="<?php echo $this -> config -> item('front_assets'); ?>js/plugins/jquery.validate.min.js"></script>

<?php if ($this -> router -> fetch_class() == 'login') { ?>
    <script src="<?php echo $this -> config -> item('front_assets'); ?>js/pages/registration.js"></script>
<?php } ?>

<script src="<?php echo $this -> config -> item('front_assets'); ?>js/pages/topoffers.js"></script>

<?php if ($this -> router -> fetch_class() == 'registration' || $this -> router -> fetch_class() == 'my_profile' || $this -> router -> fetch_class() == 'productdetails') { ?>
    <!-- Map -->
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCRXOgcXC1MO7dyCZwTt9IuW0Fz8upWpDM&libraries=places&sensor=false" ></script>
    <script src="<?php echo $this -> config -> item('front_assets'); ?>js/plugins/locationpicker.jquery.js"></script>

<?php } ?>
<?php if ($this -> router -> fetch_class() == 'productdetails') { ?>
    <!-- Map -->
    <script src="<?php echo $this -> config -> item('front_assets'); ?>js/plugins/elevatezoom/jquery.elevatezoom.min.js"></script>
    <script src="<?php echo $this -> config -> item('front_assets'); ?>js/plugins/zoomify.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.3.0/Chart.min.js"></script>

<?php } ?>

<!-- Load JS as per controller loaded -->
<script src="<?php echo $this -> config -> item('front_assets'); ?>js/pages/<?php echo $this -> router -> fetch_class(); ?>.js"></script>


<!-- Loader js -->
<script src="<?php echo $this -> config -> item('front_assets'); ?>js/loadingoverlay.min.js"></script>

<!-- Raty js -->
<script src="<?php echo $this -> config -> item('front_assets'); ?>js/jquery.raty.js"></script>

<!-- Toggles js -->
<script src="<?php echo $this -> config -> item('front_assets'); ?>js/plugins/toggles.min.js"></script>

<!-- Time ago js -->
<script src="<?php echo $this -> config -> item('front_assets'); ?>js/plugins/jquery.timeago.js"></script>

<script src="<?php echo $this -> config -> item('front_assets'); ?>js/plugins/cropper/cropper.min.js"></script>

<script src="<?php echo $this -> config -> item('front_assets'); ?>js/jquery.webui-popover.js"></script>

<script>
    //Add below js only for home page
    $(function() {
        $('a[href*=#]:not([href=#])').click(function() {
            if (location.pathname.replace(/^\//,'') == this.pathname.replace(/^\//,'') && location.hostname == this.hostname) {
                var target = $(this.hash);
                target = target.length ? target : $('[name=' + this.hash.slice(1) +']');
                if (target.length) {
                    $('html,body').animate({
                        scrollTop: target.offset().top
                    }, 1000);
                    return false;
                }
            }
        });
    });
</script>
</body>
</html>