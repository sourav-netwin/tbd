<?php if (!empty($top_offers)) { 
    $index=1;
    ?>
    <input type="hidden" id="pgClassName" name="pgClassName" value="<?php echo $this -> router -> fetch_class() ?>" >
    <?php foreach ($top_offers as $top_offer) { ?>

        <div class="col-xs-6 col-sm-6 col-md-3 grid_5 offer-items" id="fav-<?php echo $index; ?>">
            <div class="prd_wrap">
                <div class="fav-star <?php echo $top_offer['is_favorite'] ? 'is-added' : 'is-not-added' ?>">
                    <?php if (array_key_exists('is_favorite', $top_offer)) { ?>
                        <a href="javascript:void(0)" class="link-spl fav_product" title="Favourite" data-product-id="<?php echo $top_offer['Id'] ?>"  data-special-id="<?php echo $top_offer['special_id'] ?>" data-is-fav="<?php echo ( $top_offer['is_favorite'] ) ? 1 : 0 ?>" data-fav-item="item-<?php echo $index; ?>"><i class="fa fa-star"></i></a>
                        <?php
                    }
                    else {
                        ?>
                        <a href="javascript:void(0)" class="link-spl fav_product" data-product-id="<?php echo $top_offer['Id'] ?>"  data-special-id="<?php echo $top_offer['special_id'] ?>" data-is-fav="0" data-fav-item="item-<?php echo $index; ?>" ><i class="fa fa-star"></i></a>
                        <?php
                    }
                    ?>
                </div>


                <?php if (isset($top_offer['SpecialQty']) && isset($top_offer['SpecialPrice'])) { ?>
                                                                                                                                                                                                                                                                                                            <!--                    <span class="spacial">
                                                                                                                                                                                                                                                                                                                        <img src="<?php //echo $this -> config -> item('front_assets');                             ?>img/special.png">
                                                                                                                                                                                                                                                                                                                    </span>-->
                    <div class="special_banner">
                        ON SPECIAL
                    </div>
                    <div class="special_banner with-discount">
                        <?php
                        $one_price = $top_offer['SpecialPrice'];
                        if ($top_offer['SpecialQty'] > 1) {
                            $one_price = $top_offer['SpecialPrice'] / $top_offer['SpecialQty'];
                        }
                        echo round(100 - (($one_price / $top_offer['store_price']) * 100))
                        ?> %
                    </div>

                <?php } ?>

                <?php
                $store_special_link_add = '';
                if ($top_offer['IsStore'] == '1' || $top_offer['special_id']   ) {
                    $store_special_link_add = '/' . $this -> encrypt -> encode($top_offer['special_id']);
                }
                $product_link = front_url() . 'productdetails/' . urlencode(encode_per($top_offer['ProductName'])) . '/' . $this -> encrypt -> encode($top_offer['Id']) . $store_special_link_add;
                ?>
                <div class="prd_img">

                    <a href="<?php echo $product_link ?>">
                        <?php
                        if ($top_offer['ProductImage'])
                            $product_image = front_url() . PRODUCT_IMAGE_PATH . "medium/" . $top_offer['ProductImage'];
                        else
                            $product_image = front_url() . PRODUCT_IMAGE_PATH . "medium/" . DEFAULT_PRODUCT_IMAGE_NAME;
                        ?>
                        <img src="<?php echo $product_image ?>" class="img-responsive" alt="">
                    </a>

                </div>
                <div class="prd_name">
                    <a href="<?php echo $product_link ?>"><?php echo character_limiter($top_offer['ProductName'], 30); ?></a>
                </div>
                <div class="promo-pr-port number-font">
                    <?php if (isset($top_offer['SpecialQty']) && isset($top_offer['SpecialPrice'])) { ?>
                        <div class="price-half price-special">
                            <?php
                            if ($top_offer['SpecialQty'] > 1) {
                                ?>
                                <div class="special-multi"><span><?php echo $top_offer['SpecialQty'] ?></span><span>FOR</span></div>
                                <?php
                            }
                        }
                        else {
                            ?>
                            <div class="price-half price-normal">
                                <?php
                            }
                            if (isset($top_offer['SpecialQty']) && isset($top_offer['SpecialPrice'])) {
                                $price_arr = explode('.', $top_offer['SpecialPrice']);
                                echo '<span>' . $price_arr[0] . '</span><span class="superscript">' . $price_arr[1] . '</span>';
                            }
                            else {
                                $price_arr = explode('.', $top_offer['store_price']);
                                echo '<span>' . $price_arr[0] . '</span><span class="superscript">' . $price_arr[1] . '</span>';
                            }
                            ?>
                        </div>
                        <?php if (isset($top_offer['SpecialQty']) && isset($top_offer['SpecialPrice'])) { ?>
                            <div class="price-half text-right price-old">
                                <div class="pull-right text-old">WAS</div>
                                <?php
                                $price_arr = explode('.', $top_offer['store_price']);
                                echo '<span>' . $price_arr[0] . '</span><span class="superscript">' . $price_arr[1] . '</span>';
                                ?>
                            </div>
                        <? } ?>

                        <?php
                        if ($top_offer['IsStore'] == '1') {
                            ?>
                            <div class="store-spetial-part">
                                STORE SPECIAL
                            </div>
                            <?php
                        }
                        ?>
                    </div>

                    <?php if ($this -> router -> fetch_class() != 'topoffers') { ?>
                        <div class="ratings">
                            <span data-score="<?php echo ( $top_offer['avg_rating'] ) ? $top_offer['avg_rating'] : 0 ?>" class="product_rating"></span>
                            <span class="counter">
                                <a href="">
                                    <?php echo $top_offer['reviews_count'] ?> Reviews
                                </a>
                            </span>
                        </div>
                    <?php } ?>
                    <a class="btn btn-grey btn-block add_to_basket" href="javascript:void(0);" data-product="<?php echo $top_offer['Id']; ?>">Add to basket</a>
                </div>
            </div>
            <?php $index++;
        }
    }
    ?>