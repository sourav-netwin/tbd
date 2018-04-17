<div class="prd_wrap">
    <span class="fav">
        <?php if (array_key_exists('is_favorite', $product)) { ?>
            <a href="javascript:void(0);" class="fav_product" data-product-id="<?php echo $product['Id'] ?>" data-is-fav="<?php echo ( $product['is_favorite'] ) ? 1 : 0 ?>">
                <?php if ($product['is_favorite']) { ?>
                    <img src="<?php echo $this -> config -> item('front_assets'); ?>img/fav_degault.png">
                    <?php
                }
                else {
                    ?>
                    <img src="<?php echo $this -> config -> item('front_assets'); ?>img/fav_added.png">
                <?php } ?>
            </a>
            <?php
        }
        else {
            ?>
            <img src="<?php echo $this -> config -> item('front_assets'); ?>img/fav_degault.png">
        <?php } ?>

    </span>
    <?php if (isset($product['SpecialQty']) && isset($product['SpecialPrice'])) { ?>
        <span class="spacial">
            <img src="<?php echo $this -> config -> item('front_assets'); ?>img/special.png">
        </span>
    <?php } ?>

    <?php
    $product_link = front_url() . 'productdetails/' . urlencode($product['ProductName']) . '/' . $this -> encrypt -> encode($product['Id']);
    ?>
    <div class="prd_img">
        <a href="<?php echo $product_link ?>">
            <?php
            if ($product['ProductImage'])
                $product_image = front_url() . PRODUCT_IMAGE_PATH . "medium/" . $product['ProductImage'];
            else
                $product_image = front_url() . PRODUCT_IMAGE_PATH . "medium/" . DEFAULT_PRODUCT_IMAGE_NAME;
            ?>
            <img src="<?php echo $product_image ?>" class="img-responsive" alt="">
        </a>
    </div>
    <div class="prd_name">
        <a href="<?php echo $product_link ?>"><?php echo character_limiter($product['ProductName'], 30); ?></a>
    </div>
    <?php if (isset($product['SpecialQty']) && isset($product['SpecialPrice'])) { ?>
        <div class="prd_price discount">
            <div>
                <span class="strikout">
                    <?php echo 'R' . $product['store_price']; ?>
                </span>
            </div>
            &nbsp;<div class="new_price<?php echo ( $product['SpecialQty'] > 1 ) ? ' badge_wrap' : ''; ?>">
                <?php
                if ($product['SpecialQty'] > 1)
                    echo '<div class="badge_cust">' . $product['SpecialQty'] . '</div><div class="dis_off"> for ';
                echo 'R' . $product['SpecialPrice'];

                if ($product['SpecialQty'] > 1) {
                    $unit_price = number_format(($product['SpecialPrice'] / $product['SpecialQty']), 2);
                    ?>
                    <div class="unit_price">(1 for <?php echo $unit_price ?>)</div>
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
    <div class="prd_price">
        <?php echo 'R' . $product['store_price']; ?>
    </div>
<?php } ?>
<div class="ratings">
    <span data-score="<?php echo ( $product['avg_rating'] ) ? $product['avg_rating'] : 0 ?>" class="product_rating"></span>
    <span class="counter">
        <a href="">
            <?php echo $product['reviews_count'] ?> Reviews
        </a>
    </span>
</div>
<a class="btn btn-grey btn-block add_to_basket" href="javascript:void(0);" data-product="<?php echo $product['Id']; ?>">Add to basket</a>
</div>
