<div class="cart_wrap">
    <div class="sec_title">MY BASKET (<?php echo $user_basket_products_count ?>)</div>
    <?php
    if (isset($user_preferred_retailer -> LogoImage)) {
        ?>
        <div class="cart-retailer">
            <img src="<?php echo front_url() ?>assets/images/retailers/small/<?php echo $user_preferred_retailer -> LogoImage ?>" />
            <span class="prd_nml number-font" style="float: right;position: relative;top: 5px;">
                <?php
                if (!empty($user_basket)) {
                    $price_arr = explode('.', $user_basket_total);
                    echo $price_arr[0] . '<span class="subscript" style="top: -5px !important;">' . $price_arr[1] . '</span>';
                }
                ?>
            </span>
        </div>
        <?php
    }
    ?>
        <?php if (!empty($user_basket)) { ?>
        <ul class="cart_list">
    <?php foreach ($user_basket as $ub) { ?>
                <li>
                    <div class="media cart_media">
                        <div class="media-left">
                            <?php
                            $product_link = front_url() . 'productdetails/' . urlencode($ub['ProductName']) . '/' . $this -> encrypt -> encode($ub['Id']);
                            ?>
                            <a href="<?php echo $product_link ?>">
                                <?php
                                if ($ub['ProductImage'])
                                    $product_image = front_url() . PRODUCT_IMAGE_PATH . "small/" . $ub['ProductImage'];
                                else
                                    $product_image = front_url() . PRODUCT_IMAGE_PATH . "small/" . DEFAULT_PRODUCT_IMAGE_NAME;
                                ?>
                                <img class="media-object img-thumbnail img-responsive" src="<?php echo $product_image ?>" alt="">
                            </a>
                        </div>
                        <div class="media-body">
                            <div class="prd_name">
                                <a href="<?php echo $product_link ?>"><?php echo $ub['ProductName'] ?></a>
                            </div>
                            <div class="prd_price prc-cart prd_nml number-font">
                                <?php
                                $price_arr = explode('.', $ub['Price']);
                                echo $price_arr[0] . '<span class="subscript">' . $price_arr[1] . '</span>';
                                ?>
                            </div>
                        </div>
                    </div>
                </li>
            <?php } ?>
    <?php if ($user_basket_products_count > $this -> config -> item('my_basket_limit')) { ?>
                <div class="text-right view_basket">
                    <a href="<?php echo front_url() . 'viewbasket' ?>">VIEW ALL</a>
                </div>
        <?php } ?>
        </ul>
    <?php } ?>
<div class="sec_title">ALTERNATE PRICING<!--<br><span class="small_text">* Indicates price may vary if some products not present for that retailer</span>--></div>
        <?php if (!empty($user_basket_other_retailer)) { ?>
        <ul class="cart_list cart_alter">
    <?php foreach ($user_basket_other_retailer as $key => $value) { ?>
                <li>
                    <a href="javascript:void(0)">
                        <div class="media today_media">
                            <div class="media-left">
                                <img class="media-object" src="<?php echo front_url() . RETAILER_IMAGE_PATH . 'small/' . $key; ?>" alt="">
                            </div>
                            <div class="media-body">
                                <div class="prd_price side-cart-price text-danger prd_xs number-font">
                                    <?php
                                    echo ( $value[1] == 0 ) ? '*' : '';
                                    $price_arr = explode('.', $value[0]);
                                    echo $price_arr[0] . '<span class="subscript">' . $price_arr[1] . '</span>';
                                    ?>
                                </div>
                            </div>
                        </div>
                    </a>
                </li>
        <?php } ?>
        </ul>
<?php } ?>
</div>