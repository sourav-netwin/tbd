<section class="one-section">
    <div class="row">
        <div class="col-xs-4 col-xs-offset-4" style="background-color: #FFFFFF">
            <div class="sm-sec">
                <h2 class="underline" style="font-size: 20px;">Select your preferred Retailer and Store</h2>
                <p>We need your preferred retailer and store before proceeding.</p>
                <div class="form-group">
                    <div class="col-sm-12 col-md-12">
                        <?php if (!empty($retailers)) { ?>
                            <div class="supermarket_select">
                                <?php echo form_open(front_url() . 'registration/save_user_preference', array('id' => 'preference_form', 'class' => 'form-horizontal custom_form mtop-40')); ?>

                                <div style="display:none">
                                    <input type="hidden" name="csrf_tbd_token" value="<?php echo $this -> security -> get_csrf_hash(); ?>">
                                </div>

                                <div class="form-group">
                                    <label for="" class="col-sm-3 col-md-4 control-label">Retailer<span class="text-danger">*</span></label>
                                    <div class="col-sm-3 col-md-4">
                                        <div class="supermarket_select">
                                            <div class="btn-group" id="pref_retailer_select">
                                                <button id="pref_retailer_select_btn" type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-retailer-id="">
                                                    Select Retailer <span class="caret"></span>
                                                </button>
                                                <input type="hidden" value="" name="pref_retailers" id="pref_retailers">
                                                <ul class="dropdown-menu">
                                                    <li><a href="" data-store-id="">Select Retailer</a></li>
                                                    <?php foreach ($retailers as $retailer) { ?>
                                                        <li>
                                                            <a href="javascript:void(0);" data-retailer-id="<?php echo $retailer['Id'] ?>">
                                                                <img src="<?php echo front_url() . RETAILER_IMAGE_PATH . '/small/' . $retailer['LogoImage']; ?>">
                                                            </a>
                                                        </li>
                                                    <?php } ?>
                                                </ul>
                                            </div>
                                        </div>

                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="" class="col-sm-3 col-md-4 control-label">Store<span class="text-danger">*</span></label>
                                    <div class="col-sm-6 col-md-4">
                                        <div class="supermarket_select">
                                            <div class="btn-group" id="pref_store_select">
                                                <button id="pref_store_select_btn" type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-store-id="">
                                                    Select Store <span class="caret"></span>
                                                </button>
                                                <input type="hidden" value="" name="pref_stores" id="pref_stores">
                                                <ul class="dropdown-menu" >
                                                    <li><a href="" data-store-id="">Select Store</a></li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-sm-12 text-center">
                                        <input type="submit" class="btn btn-login" value="Proceed">
                                    </div>
                                </div>
                            </div>

                        <?php } ?>
                    </div>
                    <div class="row">
                        <div class="col-xs-12 text-center">
                            <ul class="social front-pg-md">
                                <li><a title="Facebook" href="" style="background:url('<?php echo $this -> config -> item('front_assets'); ?>img/social/facebook_50.png') no-repeat scroll 0% 0% / 35px 35px"></i></a></li>
                                <li><a title="Twitter" href="" style="background:url('<?php echo $this -> config -> item('front_assets'); ?>img/social/twitter_50.png') no-repeat scroll 0% 0% / 35px 35px"></a></li>
                                <li><a title="Google Plus" href="" style="background:url('<?php echo $this -> config -> item('front_assets'); ?>img/social/google_50.png') no-repeat scroll 0% 0% / 35px 35px"></a></li>
                                <li><a title="Instagram" href="" style="background:url('<?php echo $this -> config -> item('front_assets'); ?>img/social/instagram_50.jpg') no-repeat scroll 0% 0% / 35px 35px"></a></li>
                                <li><a title="Retailer Login" id="retailer_login" href="javascript:void(0)" style="background:url('<?php echo $this -> config -> item('front_assets'); ?>img/retailer.png') no-repeat scroll 0% 0% / 90px 35px;background-position: 0px;"></a></li>
                            </ul>
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>