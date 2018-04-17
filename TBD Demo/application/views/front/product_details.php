<section class="one-section">
    <?php
    if ($product_details['ProductName']) {
        ?>
        <div class="container">
            <div class="clearfix">
                <div class="filter_wrap">
                    <div class="fix">
                        <?php if (!empty($retailers)) { ?>
                            <div class="supermarket_select">
                                <div class="btn-group small-drop-img" id="retailer_select">
                                    <button id="retailer_select_btn" type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-retailer-id="<?php echo $user_preferred_retailer -> Id ?>">
                                        <img src="<?php echo front_url() . RETAILER_IMAGE_PATH . 'small/' . $user_preferred_retailer -> LogoImage; ?>"> 
                                    </button>
                                    <ul class="dropdown-menu def_retailer">
                                        <?php foreach ($retailers as $retailer) { ?>
                                            <li>
                                                <a href="javascript:void(0);" data-retailer-id="<?php echo $retailer['Id'] ?>">
                                                    <img src="<?php echo front_url() . RETAILER_IMAGE_PATH . 'small/' . $retailer['LogoImage']; ?>">
                                                </a>
                                            </li>
                                        <?php } ?>
                                    </ul>
                                </div>
                            </div>
                        <?php } ?>
                        <?php if (!empty($nearest_stores)) { ?>
                            <div class="supermarket_select">
                                <div class="btn-group sel-small" id="store_select">
                                    <button id="store_select_btn" type="button" class="btn btn-default dropdown-toggle btn-text-small btn-no-pd-sd" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-store-id="<?php echo $user_preferred_retailer -> StoreId ?>">
                                        <?php echo $user_preferred_retailer -> StoreName ?> 
                                    </button>
                                    <ul class="dropdown-menu font-small">
                                        <?php foreach ($nearest_stores as $store) { ?>
                                            <li>
                                                <a href="javascript:void(0);" data-store-id="<?php echo $store['Id'] ?>">
                                                    <?php echo $store['StoreName'] . ' (' . round($store['distance'], 2) . 'km)' ?>
                                                </a>
                                            </li>
                                        <?php } ?>
                                    </ul>
                                </div>
                            </div>
                        <?php } ?>
                        <ul class="goo-collapsible">
                            <li class="header">FILTER BY</li>
                            <li class="dropdown"><a class="" href="javascript:void(0);" >Distance</a>
                                <ul id="dist_range_filter">
                                    <li>
                                        <div class="radio">
                                            <label><input type="radio" name="dist_sel" value="1" autocomplete="off"> Up to 1KM</label>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="radio">
                                            <label><input type="radio" name="dist_sel" value="5" autocomplete="off"> Up to 5KM</label>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="radio">
                                            <label><input type="radio" name="dist_sel" value="25" autocomplete="off"> Up to 25KM</label>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="radio">
                                            <label><input type="radio" name="dist_sel" value="100" autocomplete="off"> Up to 100KM</label>
                                        </div>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </div>
                <?php echo $this -> load -> view('front/cart'); ?>
                <div class="prd_list_container_2">
                    <div class="manage_content">
                        <div class="pt-24 custom_5_7">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="thumbnail prd-det-pg">
    <!--                                        <span class="zoom">
                                            <a href="javascript:void(0);" title="Zoom">
                                                <img src="<?php //echo $this -> config -> item('front_assets');                                                               ?>img/zoom.png">
                                            </a>
                                        </span>-->
                                        <?php
                                        if ($product_details['ProductImage']) {
                                            $product_image = front_url() . PRODUCT_IMAGE_PATH . "medium/" . $product_details['ProductImage'];
                                        }
                                        else {
                                            $product_image = front_url() . PRODUCT_IMAGE_PATH . "medium/" . DEFAULT_PRODUCT_IMAGE_NAME;
                                        }
                                        ?>
                                        <img src="<?php echo $product_image ?>" data-zoom-image="<?php echo $product_image ?>" style="max-width: 100%; max-height: 200px" alt=""  <?php echo $product_details['ProductImage'] ? 'id="prod-zoom"' : '' ?>>
                                    </div>

                                </div>

                                <div class="col-md-9 prd_dtl">
                                    <h2 class="dtl_ttl">
                                        <?php echo $product_details['ProductName']; ?> 
                                    </h2>
                                    <div class="row mr-bt-15">
                                        <div class="col-xs-8">
                                            <div class="col-xs-7">
                                                <?php if (isset($product_details['SpecialQty']) && isset($product_details['SpecialPrice'])) { ?>
                                                    <div class="prd_price discount_large <?php echo $product_details['SpecialQty'] > 1 ? 'vertical-large' : '' ?>">
                                                        <?php
                                                        $disp_price = $product_details['store_price'];
                                                        $price_arr = explode('.', $disp_price);
                                                        ?>
            <!--                                                    <div class="text-right" style="margin-bottom: 10px;"><span class = "strikout font-grey number-font"><?php //echo $price_arr[0]                    ?><span class="subscript"><?php //echo $price_arr[1]                    ?></span></span></div>-->
                                                        &nbsp;<div class="new_price mr-bt-15 <?php echo ( $product_details['SpecialQty'] > 1 ) ? ' badge_wrap1' : ''; ?>">
                                                            <?php
                                                            if ($product_details['SpecialQty'] > 1) {
                                                                ?>
                                                                <div class="prod-big-dis number-font"><span>2</span><span>FOR</span></div><div>
                                                                <?php
                                                            }
                                                            //echo '<div class="badge_cust number-font">' . $product_details['SpecialQty'] . '</div><div class="dis_off_large"><span class="font-md mul-sub"> for </span>';
                                                            $sp_disp_price = $product_details['SpecialPrice'];
                                                            $sp_price_arr = explode('.', $sp_disp_price);

                                                            echo '<span class="number-font">' . $sp_price_arr[0] . '<span class="subscript">' . $sp_price_arr[1] . '</span></span></div>';



                                                            if ($product_details['SpecialQty'] > 1) {
                                                                $unit_price = number_format(($product_details['SpecialPrice'] / $product_details['SpecialQty']), 2);
                                                                ?>

                                                            </div>
                                                            <div class="unit_price">(<span class="number-font font-small">1</span> for <span class="number-font font-small"><?php echo $unit_price ?></span>)</div>
                                                            <?php
                                                            if ($product_details['SpecialPrice'] && $product_details['special_id_get']) {
                                                                ?>
                                                                <div class="prod-la-stsp">STORE SPECIAL</div> 
                                                                <?php
                                                            }
                                                            ?>
                                                            <?php
                                                        }
                                                        ?>
        <!--<span class="offer_end_date text-danger font-small weight-normal">Offer ends: <?php //echo date('d M Y', strtotime($product_details['PriceAppliedTo']))              ?></span>--> 
                                                    </div>

                                                    <?php
                                                }
                                                else {
                                                    ?>
                                                    <div class="prd_price prd_large">
                                                        <?php
                                                        $disp_price = $product_details['store_price'];
                                                        if ($disp_price) {
                                                            $price_arr = explode('.', $disp_price);
                                                            echo '<span class="number-font">' . $price_arr[0] . '<span class="subscript">' . $price_arr[1] . '</span></span>';
                                                        }
                                                        else {
                                                            ?>
                                                            <div class="prd-na">OUT OF STOCK</div>
                                                            <?php
                                                        }
                                                        ?>
                                                    </div>
                                                <?php } ?>
                                            </div>

                                            <div class="col-xs-5" style="border-right: 1px solid;">
                                                <div class="prd-share">
                                                    <div class="ratings text-center-imp">

                                                        <span  data-score="<?php echo ( $product_details['avg_rating'] ) ? $product_details['avg_rating'] : 0 ?>" class="product_detail_rating display-inline-block"></span>
                                                    </div>
                                                    <div class="font-12">
                                                        <div style="width: 105px; margin: auto"><?php echo $product_views ?> <span style="float: right">Views</span></div>
                                                        <div style="width: 105px; margin: auto"><span id="share_count"><?php echo $product_shares ?></span> <span style="float: right">Shares</span></div>
                                                        <div style="width: 105px; margin: auto"><?php echo $product_details['reviews_count'] ?><span style="float: right">Reviews</span></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xs-4">
                                            <div class="pro-bs-cnt text-center">
                                                <div class="pl-arrow"></div>
                                                <div class="bst-head">Order Count</div>
                                                <div class="input-group">
                                                    <span class="input-group-addon" id="minus-cart" style="cursor: pointer">-</span>
                                                    <input type="text" style="width:35px;padding: 0px;" class="form-control text-center" value="1" id="basket-pro-count" />
                                                    <span class="input-group-addon" id="plus-cart" style="cursor: pointer">+</span>
                                                </div>
                                                <div id="bskt-cnt-sub"><input type="button" value="Add" /></div>
                                            </div>
                                            <div class="prd-share <?php
                                                if (isset($product_details['SpecialQty']) && isset($product_details['SpecialPrice'])) {
                                                    if ($product_details['SpecialQty'] > 1) {
                                                        echo 'vertical-large';
                                                    }
                                                }
                                                ?> ">
                                                <div class="mr-tp-10">QUICK ACTIONS</div>
                                                <button class="btn btn-xs btn-primary btn-grey-light btn-hr-lg" title="<?php echo ( $product_details['is_favorite'] ) ? 'Remove from Favourites' : 'Add to Favourites' ?>" id="add_to_fav" data-product-id="<?php echo $product_details['Id'] ?>" data-special-id="<?php echo $product_details['special_id_get'] ?>" data-is-fav="<?php echo ( $product_details['is_favorite'] ) ? 1 : 0 ?>" ><i class="fa fa-star <?php echo ( $product_details['is_favorite'] ) ? 'text-danger' : '' ?>"></i></button>
                                                <button class="btn btn-xs btn-primary btn-grey-light btn-hr-rd" data-toggle="modal" data-target="#add_to_list_modal" title="Add to Wishlist" ><i class="fa fa-th-list"></i></button>
                                                <button class="btn btn-xs btn-primary btn-grey-light btn-hr-vt" id="add_to_basket" title="Add to Basket" data-product="<?php echo $product_details['Id']; ?>" data-special-id="<?php echo $product_details['special_id_get'] ?>" ><i class="fa fa-cart-plus"></i></button>
                                                <button class="btn btn-xs btn-primary btn-grey-light btn-hr-gr" title="<?php echo $product_details['price_alert'] ? 'Turn off ' : 'Turn on ' ?>Price Watch" id="price_watch" <?php echo $product_details['price_alert'] ? 'is_active="1"' : 'is_active="0"' ?> ><i class="fa fa-money <?php echo $product_details['price_alert'] ? 'text-danger' : '' ?>"></i></button>


                                                <div class="mr-tp-10">SHARE THIS DEAL</div>
                                                <div class="row mr-lt-m-15">
                                                    <div class="col-xs-12" style="margin-bottom: 10px;">
                                                        <ul class="social_small_xs left-small with-image">
                                                            <li><a href="javascript:void(0)" id="fb_test" style="background:url('<?php echo $this -> config -> item('front_assets') ?>img/social/facebook_200.png')no-repeat scroll 0% 0% / 31px 30px;"></a></li>
                                                            <li><a href="javascript:void(0)" id="twitter-share-button" style="background:url('<?php echo $this -> config -> item('front_assets') ?>img/social/twitter_200.png')no-repeat scroll 0% 0% / 31px 30px;"></a></li>
                                                            <li><a href="javascript:void(0)" style="background:url('<?php echo $this -> config -> item('front_assets') ?>img/social/google_200.png')no-repeat scroll 0% 0% / 31px 30px;"></a></li>
                                                            <li><a href="javascript:void(0)" style="background:url('<?php echo $this -> config -> item('front_assets') ?>img/social/instagram_200.png')no-repeat scroll 0% 0% / 31px 30px;"></a></li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php
                                    if ($product_details['SpecialPrice'] && $product_details['SpecialPrice'] > 0) {
                                        ?>
                                        <div class="row mr-bt-15">
                                            <div class="col-xs-8">
                                                <div class="col-xs-3">
                                                    <div class="red-block">
                                                        <?php
                                                        $one_price = $product_details['SpecialPrice'];
                                                        if ($product_details['SpecialQty'] > 1) {
                                                            $one_price = $product_details['SpecialPrice'] / $product_details['SpecialQty'];
                                                        }
                                                        echo round(100 - (($one_price / $product_details['store_price']) * 100))
                                                        ?> %
                                                        <br />OFF
                                                    </div>
                                                </div>
                                                <div class="col-xs-3 price-norm-prd-1">
                                                    Normal Price<br />
                                                    <span class="number-font">
                                                        <?php
                                                        $store_pr = round($product_details['store_price'], 2);
                                                        $store_pr_array = explode('.', $store_pr);
                                                        if (!isset($store_pr_array[1])) {
                                                            $store_pr = $store_pr . '.00';
                                                        }
                                                        echo $store_pr;
                                                        ?>
                                                    </span>
                                                </div>
                                                <div class="col-xs-3 price-norm-prd-2">
                                                    Unit Price<br />
                                                    <span class="number-font">
                                                        <?php
                                                        $unit_pr = '0.00';
                                                        if ($product_details['SpecialPrice'] && $product_details['SpecialPrice'] > 0) {
                                                            $unit_pr = $product_details['SpecialPrice'];
                                                            if ($product_details['SpecialQty'] > 1) {
                                                                $unit_pr = round($product_details['SpecialPrice'] / $product_details['SpecialQty'], 2);
                                                            }
                                                            $unit_pr_arr = explode('.', $unit_pr);
                                                            if (!isset($unit_pr_arr[1])) {
                                                                $unit_pr = $unit_pr . '.00';
                                                            }
                                                        }
                                                        echo $unit_pr;
                                                        ?>
                                                    </span>
                                                </div>
                                                <div class="col-xs-3 price-norm-prd-3">
                                                    You Save<br /><span class="number-font"><?php
                                                $save_pr = round($product_details['save_price'], 2);
                                                $save_pr_array = explode('.', $save_pr);
                                                if (!isset($save_pr_array[1])) {
                                                    $save_pr = $save_pr . '.00';
                                                }
                                                echo $save_pr;
                                                        ?></span>
                                                </div>
                                            </div>
                                        </div>
                                        <?php
                                    }
                                    ?>

                                    <!--                                <div class="row" >
                                                                        <div class="red_title">Product Description</div>
                                                                        <p><?php //echo $product_details['ProductDescription'];                                            ?></p>
                                                                    </div>-->

                                </div>
                            </div>
                            <input type="hidden" autocomplete="off" id="prodid" value="<?php echo $product_details['Id']; ?>" />
                            <input type="hidden" autocomplete="off" id="init_dist" value="<?php echo $preferances[0]['PrefDistance']; ?>" />

                            <div class="row">
                                <div class="col-xs-9">
                                    <div id="tabs">
                                        <ul>
                                            <li><a href="#tabs-1">PRICE COMPARISON</a></li>
                                            <li><a href="#tabs-2">PRICE WATCH</a></li>
                                            <li><a href="#tabs-3">REVIEWS & RATING<span class="rev_count"><?php echo count($product_reviews) ?></span></a></li>
                                        </ul>
                                        <div id="tabs-1">
                                            <div  id="prod_comp_div">
                                                <?php if (!empty($product_comparison)) { ?>
                                                    <div class="row">
                                                        <div class="col-xs-12">
                                                            <div class="comp_wrap">
                                                                <?php
                                                                $i = 0;
                                                                foreach ($product_comparison as $product) {
                                                                    if (trim($product['Price']) || trim($product['SpecialPrice'])) {
                                                                        ?>
                                                                        <div class="product_comparison_container row <?php echo ( ($i % 2) != 0 ) ? 'grey-bg' : '' ?>" <?php echo ( ( $i + 1 ) > $this -> config -> item('product_detail_comparison_limit') ) ? 'style=display:none;' : '' ?>>
                                                                            <div class="col-xs-3">
                                                                                <img src="<?php echo front_url() . RETAILER_IMAGE_PATH . 'small/' . $product['LogoImage']; ?>" class="img-responsive">
                                                                            </div>
                                                                            <div class="col-xs-6">
                                                                                <?php echo $product['StoreName'] ?>
                                                                            </div>
                                                                            <?php
                                                                            if ($product['SpecialQty'] != '' && $product['SpecialPrice'] != '') {
                                                                                $pr_nw = '0.00';
                                                                                $price_ar = explode('.', round($product['SpecialPrice'] / $product['SpecialQty'], 2));
                                                                                if (!isset($price_ar[1])) {
                                                                                    $pr_nw = $price_ar[0] . '.00';
                                                                                }
                                                                                elseif (strlen($price_ar[1]) < 2) {
                                                                                    $pr_nw = $price_ar[0] . '.' . $price_ar[1] . '0';
                                                                                }
                                                                                else {
                                                                                    $pr_nw = $price_ar[0] . '.' . $price_ar[1];
                                                                                }
                                                                                ?>
                                                                                <div class="col-xs-3 text-right">
                                                                                    <span class=""><?php echo $pr_nw; ?></span>
                                                                                </div>
                                                                                <?php
                                                                            }
                                                                            else {
                                                                                ?>
                                                                                <div class="col-xs-3 text-right">
                                                                                    <span class=""><?php echo $product['Price']; ?></span>
                                                                                </div>
                                                                            <?php } ?>

                                                                        </div>
                                                                        <?php
                                                                        $i++;
                                                                    }
                                                                }
                                                                ?>
                                                                <?php if (count($product_comparison) > $this -> config -> item('product_detail_comparison_limit')) { ?>
                                                                    <a href="javascript:void(0);" class="btn btn-block btn-blue mt-10" id="compare_all">Compare all Stores(<?php echo count($product_comparison) ?>)</a>
                                                                <?php } ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                        </div>
                                        <div id="tabs-2">
                                            <canvas id="myChart"></canvas>
                                        </div>

                                        <div id="tabs-3">
                                            <div class="row">
                                                <div class="col-xs-6">
                                                    <?php echo form_open(front_url() . 'productdetails/add_review', array('id' => 'add_review_form')); ?>
                                                    <textarea class="form-control review_box" rows="3" placeholder="Add review" name="review" id="review"></textarea>
                                                    <div class="clearfix mtb-10">
                                                        <div class="rat_section">
                                                            <div class="ratings">
                                                                <span data-score="0" class="add_review_rating"></span>
                                                            </div>
                                                        </div>
                                                        <div class="btn-sectio">
                                                            <input type="button" id="add_review" name="add_review" class="btn btn-grey" value="SUBMIT" data-loading-text="Saving...">
                                                        </div>
                                                    </div>
                                                    <input type="hidden" name="rating" id="rating">
                                                    </form>
                                                </div>
                                                <div class="col-xs-6">
                                                    <div class="comment_sec">
                                                        <?php
                                                        if (!empty($product_reviews)) {
                                                            $i = 1;
                                                            $plc_cnt = 1;
                                                            foreach ($product_reviews as $review) {
                                                                $image = front_url() . USER_IMAGE_PATH . $review['ProfileImage'];
                                                                if (!file_exists($image))
                                                                    $image = front_url() . USER_IMAGE_PATH . 'small/default.gif';

                                                                //if ($plc_cnt == 1) {
                                                                echo '<div class="row rating-div">';
                                                                //}
                                                                if ($plc_cnt == 1 || $plc_cnt == 2) {
                                                                    //echo '<div class="col-xs-6">';
                                                                }
                                                                ?>
                                                                <div <?php echo ( $i > $this -> config -> item('product_detail_review_limit') ) ? 'style="display:none"' : '' ?>>
                                                                    <div class="media">
                                                                        <div class="media-left">
                                                                            <a href="#">
                                                                                <img class="media-object" src="<?php echo $image ?>" alt="User Image">
                                                                            </a>
                                                                        </div>
                                                                        <div class="media-body">
                                                                            <h4 class="media-heading rev-head">
                                                                                <?php echo $review['FirstName'] . " " . $review['LastName'] ?>

                                                                            </h4>
                                                                            <div class="ratings">
                                                                                <span data-score="<?php echo $review['Rating'] ?>" class="review_rating"></span>
                                                                            </div>
                                                                            <?php echo $review['Review'] ?>
                                                                            <div class="time_sec timeago" title="<?php echo date("c", strtotime($review['CreatedOn'])); ?>"></div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <?php
                                                                //if ($plc_cnt == 1) {
                                                                echo '</div>';
//                                                                    if ($plc_cnt == 2) {
//                                                                        //echo '</div>';
//                                                                        $plc_cnt = 1;
//                                                                    }
//                                                                    else {
//                                                                        if ($i == sizeof($product_reviews)) {
//                                                                            echo '</div>';
//                                                                        }
//                                                                        //$plc_cnt++;
//                                                                    }
                                                                //}
                                                                $i++;
                                                            }
                                                        }
                                                        ?>
                                                        <?php if (count($product_reviews) > $this -> config -> item('product_detail_review_limit')) { ?>
                                                            <div class="text-right" id="view_all_reviews">
                                                                <a href="javascript:void(0);">VIEW ALL</a>
                                                            </div>
                                                        <?php } ?>
                                                    </div>
                                                </div>
                                            </div>



                                        </div>
                                    </div>
                                </div>
                                <div class="col-xs-3">
                                    <?php if (isset($store_details['ContactPersonNumber'])) { ?>
                                        <div class="row">
                                            <div class="store-det-pro">
                                                <span>CALL STORE</span>
                                                <span><strong><?php echo $store_details['ContactPersonNumber'] ?></strong></span>
                                            </div>
                                        </div>
                                        <?php
                                    }
                                    if (isset($store_details['StoreTime']['OpenTime']) && isset($store_details['StoreTime']['CloseTime'])) {
                                        ?>
                                        <div class="row mr-tp-10">
                                            <div class="store-det-pro">
                                                <span>STORE TIMES</span>
                                                <span><strong><?php echo $store_details['StoreTime']['OpenTime'] ?></strong></span>
                                                <span><strong><?php echo $store_details['StoreTime']['CloseTime'] ?></strong></span>
                                            </div>
                                        </div>
                                        <?php
                                    }
                                    if (isset($store_details['map_url'])) {
                                        ?>
                                        <div class="row mr-tp-10">
                                            <div class="store-det-pro">
                                                <span>TAKE ME THERE</span>
                                                <span><a href="<? echo $store_details['map_url'] ?>" target="_blank" ><i class="fa fa-external-link-square" style="font-size: 20px"></i></a></span>
                                            </div>
                                        </div>
                                        <?php
                                    }
                                    if (isset($product_details['PriceAppliedFrom']) && isset($product_details['PriceAppliedTo'])) {
                                        ?>
                                        <div class="row mr-tp-10">
                                            <div class="store-det-pro">
                                                <span>PROMO DATES</span>
                                                <span><strong><?php echo $product_details['PriceAppliedFrom'] ? date('d M', strtotime($product_details['PriceAppliedFrom'])) : 'NA' ?></strong></span>
                                                <span><strong><?php echo $product_details['PriceAppliedTo'] ? date('d M', strtotime($product_details['PriceAppliedTo'])) : 'NA' ?></strong></span>
                                            </div>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                            <?php if (!empty($related_products)) { ?>
                                <div class="row">
                                    <div class="col-xs-12 text-center prd-special-head">
                                        SIMILAR PRODUCTS
                                    </div>
                                </div>
                                <div class="row">
                                    <?php foreach ($related_products as $related_product) { ?>
                                        <div class="col-xs-6 col-sm-6 col-md-3 grid_5">
                                            <div class="prd_wrap">
                                                <div class="fav-star <?php echo $related_product['is_favorite'] ? 'is-added' : 'is-not-added' ?>">
                                                    <?php if (array_key_exists('is_favorite', $related_product)) { ?>
                                                        <a href="javascript:void(0)" class="link-spl fav_product" title="Favourite" data-product-id="<?php echo $related_product['Id'] ?>"  data-is-fav="<?php echo ( $related_product['is_favorite'] ) ? 1 : 0 ?>"><i class="fa fa-star"></i></a>
                                                        <?php
                                                    }
                                                    else {
                                                        ?>
                                                        <a href="javascript:void(0)" class="link-spl fav_product" data-product-id="<?php echo $related_product['Id'] ?>"  data-is-fav="0"><i class="fa fa-star"></i></a>
                                                        <?php
                                                    }
                                                    ?>
                                                </div>

                                                <?php if (isset($related_product['SpecialQty']) && isset($related_product['SpecialPrice'])) { ?>
                                                                                                                                                                                                                                                                                <!--                    <span class="spacial">
                                                                                                                                                                                                                                                                                            <img src="<?php //echo $this -> config -> item('front_assets');                      ?>img/special.png">
                                                                                                                                                                                                                                                                                        </span>-->
                                                    <div class="special_banner">
                                                        ON SPECIAL
                                                    </div>
                                                    <div class="special_banner with-discount">
                                                        <?php
                                                        $one_price = $related_product['SpecialPrice'];
                                                        if ($related_product['SpecialQty'] > 1) {
                                                            $one_price = $related_product['SpecialPrice'] / $related_product['SpecialQty'];
                                                        }
                                                        echo round(100 - (($one_price / $related_product['store_price']) * 100))
                                                        ?> %
                                                    </div>
                                                <?php } ?>

                                                <?php
                                                $product_link = front_url() . 'productdetails/' . urlencode(encode_per($related_product['ProductName'])) . '/' . $this -> encrypt -> encode($related_product['Id']);
                                                ?>
                                                <div class="prd_img">
                                                    <a href="<?php echo $product_link ?>">
                                                        <?php
                                                        if ($related_product['ProductImage']) {
                                                            $product_image = $related_product['ProductImage'];
                                                        }
                                                        else {
                                                            $product_image = front_url() . PRODUCT_IMAGE_PATH . "medium/" . DEFAULT_PRODUCT_IMAGE_NAME;
                                                        }
                                                        ?>
                                                        <img src="<?php echo $product_image ?>" class="img-responsive" alt="">
                                                    </a>
                                                </div>
                                                <div class="prd_name">
                                                    <a href="<?php echo $product_link ?>"><?php echo character_limiter($related_product['ProductName'], 30); ?></a>
                                                </div>
                                                <?php if (isset($related_product['SpecialQty']) && isset($related_product['SpecialPrice'])) { ?>
                                                    <div class="prd_price discount mr-bt-30 number-font prd_small">

                                                        &nbsp;<div class="new_price<?php echo ( $related_product['SpecialQty'] > 1 ) ? ' badge_wrap' : ''; ?>">
                                                            <?php
                                                            if ($related_product['SpecialQty'] <= 1) {
                                                                ?>
                                                                <div class="">
                                                                    <span class="strikout number-font">
                                                                        <?php
                                                                        $price_arr = explode('.', $related_product['store_price']);
                                                                        echo $price_arr[0] . '<span class="subscript">' . $price_arr[1] . '</span>';
                                                                        ?>
                                                                    </span>
                                                                </div><br />
                                                                <?php
                                                            }
                                                            ?>
                                                            <?php
                                                            if ($related_product['SpecialQty'] > 1) {
                                                                echo '<div class="badge_cust number-font">' . $related_product['SpecialQty'] . '</div><div class="dis_off">';
                                                                ?>
                                                                <div class="">
                                                                    <span class="strikout number-font">
                                                                        <?php
                                                                        $price_arr = explode('.', $related_product['store_price']);
                                                                        echo $price_arr[0] . '<span class="subscript">' . $price_arr[1] . '</span>';
                                                                        ?>
                                                                    </span>
                                                                </div><br />
                                                                <?php
                                                                echo '<span class="mul-sub">for</span> ';
                                                            }
                                                            $price_arr = explode('.', $related_product['SpecialPrice']);
                                                            echo $price_arr[0] . '<span class="subscript number-font">' . $price_arr[1] . '</span>';

                                                            if ($related_product['SpecialQty'] > 1) {
                                                                $unit_price = number_format(($related_product['SpecialPrice'] / $related_product['SpecialQty']), 2);
                                                                ?>
                                                                <div class="unit_price">(<span class="number-font font-small">1</span> for <span class="number-font font-small"><?php echo $unit_price ?></span>)</div> 
                                                                <?php
                                                                if ($product_details['SpecialPrice'] && $product_details['special_id_get']) {
                                                                    ?>
                                                                    <!--<div class="prod-la-stsp">STORE SPECIAL</div>--> 
                                                                    <?php
                                                                }
                                                                ?>
                                                            </div>
                                                            <?php
                                                        }
                                                        ?>
                                                    </div>
                                                </div>
                                                <?php
                                            }
                                            else {
                                                ?>
                                                <div class="prd_price prd_nml number-font">
                                                    <?php
                                                    $price_arr = explode('.', $related_product['store_price']);
                                                    echo $price_arr[0] . '<span class="subscript">' . $price_arr[1] . '</span>';
                                                    ?>
                                                </div>
                                            <?php } ?>

                                            <?php if ($this -> router -> fetch_class() != 'topoffers') { ?>
                                                <div class="ratings">
                                                    <span data-score="<?php echo ( $related_product['avg_rating'] ) ? $related_product['avg_rating'] : 0 ?>" class="product_rating"></span>
                                                    <span class="counter">
                                                        <a href="">
                                                            <?php echo $related_product['reviews_count'] ?> Reviews
                                                        </a>
                                                    </span>
                                                </div>
                                            <?php } ?>
                                            <a class="btn btn-grey btn-block add_to_basket" href="javascript:void(0);" data-product="<?php echo $related_product['Id']; ?>">Add to basket</a>
                                        </div>
                                    </div>
                                    <?php
                                }
                            }
                            ?>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
    </div>
    </section>
    <?php
}
else {
    ?>
    <div class="text-danger text-center text-bold mr-bt-15">The product you searching for is not found!</div>
<?php }
?>

<!-- Add to list Modal -->
<div class="modal fade" id="add_to_list_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Add to WishList</h4>
            </div>
            <div class="modal-body">
                <?php echo form_open(front_url() . 'productdetails/add_to_list', array('id' => 'add_to_list_form', 'class' => 'form-horizontal custom_form')); ?>
                <div style="display:none"><input type="hidden" name="csrf_tbd_token" value="<?php echo $this -> security -> get_csrf_hash(); ?>"></div>
                 <?php if (!empty($user_wishlist)) { ?>
                    <div class="form-group" id="existing_list_container">
                        <label for="existing_list" class="col-sm-3 control-label">Existing List</label>
                        <div class="col-sm-6">
                            <select class="form-control" id="existing_list" name="existing_list">
                                <option value="">Select a WishList</option>
                                <?php foreach ($user_wishlist as $list) { ?>
                                    <option value="<?php echo $list['Id'] ?>"><?php echo $list['WishlistDescription'] ?></option>
                                <?php } ?>
                            </select>
                            <div class="error">
                                <?php echo form_error('existing_list'); ?>
                            </div>
                        </div>
                    </div>
                <?php } ?>

                <div class="form-group" id="new_list_container">
                    <label for="new_list" class="col-sm-3 control-label">Create New</label>
                    <div class="col-sm-6">
                        <input type="text" class="form-control" id="new_list" name="new_list" placeholder="New WishList" value="">
                        <div class="error">
                            <?php echo form_error('new_list'); ?>
                        </div>
                    </div>
                </div>
                
                <input type="hidden" name="special_id" id="special_id" autocomplete="off" value="<?php echo $product_details['special_id_get'] ?>">
                <input type="hidden" name="product" id="product" autocomplete="off" value="<?php echo $product_details['Id'] ?>">
                <input type="hidden" name="product_name" autocomplete="off" value="<?php echo $product_name ?>" />
                <input type="hidden" name="product_price" autocomplete="off" id="product_price" value="<?php echo $product_details['store_price'] ?>">
                <input type="hidden" name="product_special_price" autocomplete="off" id="product_special_price" value="<?php echo $product_details['SpecialPrice'] ?>">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-danger" id="add_to_list_btn" name="add_to_list_btn" data-loading-text="Adding...">Add</button>
            </div>
        </div>
    </div>
</div>
<?php if (isset($product_details['SpecialQty']) && isset($product_details['SpecialPrice'])) {
    ?>
    <input type="hidden" id="offer_stdt" value="<?php echo date('d M Y', strtotime($product_details['PriceAppliedFrom'])) ?>" />
    <input type="hidden" id="offer_eddt" value="<?php echo date('d M Y', strtotime($product_details['PriceAppliedTo'])) ?>" />
    <input type="hidden" id="spl_cnt_nm" value="<?php echo $product_details['SpecialQty'] ?>" />
    <input type="hidden" id="retailer_nm" value="<?php echo $product_details['CompanyName'] ?>" />
    <?php
}
?>
<input type="hidden" id="pro_name" value="<?php echo $product_details['ProductName'] ?>" />
<input type="hidden" id="description" value="<?php echo $product_details['ProductDescription'] ?>" />
<input type="hidden" id="prod_sh_price" value="<?php echo $product_details['SpecialPrice'] ? $product_details['SpecialPrice'] : $product_details['store_price'] ?>" />
<?php
if ($product_details['ProductImage']) {
    ?>
    <input type="hidden" id="prod_sh_pic" value="<?php echo front_url() . PRODUCT_IMAGE_PATH . "medium/" . $product_details['ProductImage'] ?>" />
    <?php
}
else {
    ?>
    <input type="hidden" id="prod_sh_pic" value="<?php echo front_url() . PRODUCT_IMAGE_PATH . "medium/" . DEFAULT_PRODUCT_IMAGE_NAME ?>" />
    <?php
}
?>

<!-- Image zoom Modal -->
<div class="modal fade" id="zoom_img_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">&nbsp;</h4>
            </div>
            <div class="modal-body">
                <img src="<?php echo front_url() . PRODUCT_IMAGE_PATH . 'large/' . $product_details['ProductImage']; ?>">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>