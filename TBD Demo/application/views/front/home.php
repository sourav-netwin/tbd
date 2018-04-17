<!-- Slider Section -->
<?php if( !empty( $sliders )) { ?>
    <div id="carousel-example-generic" class="carousel slide custom-carousel carousel-fade" data-ride="carousel">
    	<!-- Indicators -->
        <ol class="carousel-indicators">
            <?php for( $i = 0; $i < count($sliders); $i++ ) { ?>
                <li data-target="#carousel-example-generic" data-slide-to="<?php echo $i; ?>" <?php echo ( $i == 0 ) ? 'class="active"' : '' ?>></li>
            <?php } ?>
    	</ol>
        <!-- Wrapper for slides -->
        <div class="carousel-inner" role="listbox">

            <!-- Code to display sliders dynamically that are added from admin side -->
            <?php
                    $i = 0;
                    foreach ($sliders as $slider)
                    {
            ?>
                        <!-- Slider -->
                        <div class="item <?php echo ( $i == 0 ) ? 'active' : ''; ?>" style="background:url(<?php echo front_url().SLIDER_IMAGE_PATH.$slider['Image']; ?>) no-repeat;">
                            <div class="black_overlay"></div>
                            <div class="container slider_content">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="content-wrap">
                                            <h1>Compare prices of Groceries</h1>
                                            <p class="lg">across multiple Supermarkets</p>
                                            <a id="saving-btn" class="btn btn-green btn-lg" href="javascript:void(0)" data-href="<?php echo front_url(). ( $this->session->userdata('userid') ? 'topoffers' : 'login' ) ?>">Start Saving</a>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
            <?php
                        $i++;
                    }
            ?>
        </div>
    </div>
<?php } ?>

<!-- How does it work section -->
<section class="section_first" id="how">
    <div class="container">
        <h1>How does it work ?	</h1>
        <div class="row">
            <div class="col-sm-4 col-md-3 col-md-offset-custom">
                <div class="ser_wrap">
                    <a href=""><div class="service_crcl"><img src="<?php echo $this->config->item('front_assets'); ?>img/search.png"></div></a>
                    <div class="title">
                        FIND THE BEST DEALS TODAY
                    </div>
                    <p>Whatever grocery items you are looking for, this platform will automatically find the best deal for you based on your shopping preferences and location.</p>
                </div>
            </div>
            <div class="col-sm-4 col-md-3">
                <div class="ser_wrap">
                    <a href=""><div class="service_crcl"><img src="<?php echo $this->config->item('front_assets'); ?>img/cart.png"></div></a>
                    <div class="title">
                        COMPARE BASKETS ACROSS 11 STORES
                    </div>
                    <p>You can create your own personal shopping basket, and compare prices as per basket or per individual item. View pricing across 11 stores.</p>
                </div>
            </div>
            <div class="col-sm-4 col-md-3">
                <div class="ser_wrap">
                    <a href=""><div class="service_crcl"><img src="<?php echo $this->config->item('front_assets'); ?>img/cash.png"></div></a>
                    <div class="title">
                        UP TO 30% OFF THE NORMAL PRICE
                    </div>
                    <p>You will never know if you are getting the best deal on the item you are shopping for. Now you can compare prices immediately and save up to 30% or even more on some items.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Saving time and tools section  -->
<section class="com_sec top-shadow">
    <div class="container">
        <h1>Your Shopping Tools</h1>
        <div class="row">
            <div class="col-md-3 col-md-offset-custom">
                <div class="media tool-media">
                    <div class="media-left">
                    <a href="#">
                        <div class="service_crcl_sm"><img src="<?php echo $this->config->item('front_assets'); ?>img/bill.png"></div>
                    </a>
                    </div>
                    <div class="media-body">
                        <div class="title">SHOPPING LISTS</div>
                        <p>Create as many customised shopping Lists as you require, then compare pricing across stores to find the best deal. You can store and access these lists anytime.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="media tool-media">
                    <div class="media-left">
                    <a href="#">
                        <div class="service_crcl_sm"><img src="<?php echo $this->config->item('front_assets'); ?>img/shop.png"></div>
                    </a>
                    </div>
                    <div class="media-body">
                        <div class="title">11 STORES IN 1</div>
                        <p>As we take on new Stores, your shopping experience increases and you automatically enjoy the benefit of searching for deals across all listed Stores.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="media tool-media">
                    <div class="media-left">
                    <a href="#">
                        <div class="service_crcl_sm"><img src="<?php echo $this->config->item('front_assets'); ?>img/switch.png"></div>
                    </a>
                    </div>
                    <div class="media-body">
                        <div class="title">SWITCH STORES</div>
                        <p>If you still aren’t happy with the your Basket pricing, you can switch stores and view the updated pricing on-the-fly. How amazing is that?</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Download app section -->
<section class="parallax-section" id="phone_wrap">
    <div class="container">
        <div class="row">
            <div class="col-sm-6 col-sm-push-6">
                <div class="down_wrap">
                    <h1>Download our App</h1>
                    <a href="https://itunes.apple.com/us/app/the-best-deals/id1206490613?ls=1&mt=8" target="_blank"><img class="" src="<?php echo $this->config->item('front_assets'); ?>img/ios_store.png"></a>
                    <a href="https://play.google.com/store/apps/details?id=com.thebestdeals" target="_blank"><img class="" src="<?php echo $this->config->item('front_assets'); ?>img/android_store.png"> </a>
                </div>
            </div>
            <div class="col-sm-6 col-sm-pull-6">
                <div class="center-img mob-cars relative">
                    <img src="<?php echo $this->config->item('front_assets'); ?>img/hand_slider.png" class="img-responsive">
                    <div id="carousel-example-generic-2" class="carousel slide" data-ride="carousel">
                        <!-- Wrapper for slides -->
                        <div class="carousel-inner" role="listbox">
                            <?php /* ?>
                            <div class="item active">
                              <img src="<?php echo $this->config->item('front_assets'); ?>img/splashscreen5.jpg" alt="...">
                            </div>
                            <div class="item">
                              <img src="<?php echo $this->config->item('front_assets'); ?>img/introscreen.jpg" alt="...">
                            </div>
                            <div class="item">
                              <img src="<?php echo $this->config->item('front_assets'); ?>img/introscreen2.jpg" alt="...">
                            </div>
                            <div class="item">
                              <img src="<?php echo $this->config->item('front_assets'); ?>img/introscreen3.jpg" alt="...">
                            </div>
                            <div class="item">
                              <img src="<?php echo $this->config->item('front_assets'); ?>img/home.jpg" alt="...">
                            </div>
                            <div class="item">
                              <img src="<?php echo $this->config->item('front_assets'); ?>img/sign_in.jpg" alt="...">
                            </div>
                            <?php  */ ?> 
                            
                            <div class="item active">
                              <img src="<?php echo $this->config->item('front_assets'); ?>img/front-app-slider1.jpg" alt="...">
                            </div>
                            <div class="item">
                              <img src="<?php echo $this->config->item('front_assets'); ?>img/front-app-slider2.jpg" alt="...">
                            </div>
                            <div class="item">
                              <img src="<?php echo $this->config->item('front_assets'); ?>img/front-app-slider3.jpg" alt="...">
                            </div>
                            <div class="item">
                              <img src="<?php echo $this->config->item('front_assets'); ?>img/front-app-slider4.jpg" alt="...">
                            </div>
                            <div class="item">
                              <img src="<?php echo $this->config->item('front_assets'); ?>img/front-app-slider5.jpg" alt="...">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
     </div>
</section>

<!-- Select store section -->
<?php if( !empty( $retailers ) ) { ?>
    <section class="com_sec retailer_sec">
        <div class="title text-center">SELECT A STORE | YOU CAN SWITCH ANYTIME</div>
        <div style="width:100%; padding: 0px 24px;text-align:center">
            <ul class="store" id="home_retailers">
                <?php foreach ($retailers as $retailer) { ?>
                        <li>
                            <a class="thumbnail">
                                <img src="<?php echo front_url().RETAILER_IMAGE_PATH.'small/'.$retailer['LogoImage']; ?>" class="img-responsive">
                            </a>
                        </li>
                <?php } ?>
            </ul>
        </div>
    </section>
<?php } ?>