<ul class="cart_list"><h3 class="grey_bg_head_green"><?php echo $wishlists[0]['WishlistDescription']; ?>(<?php echo $wishlists[0]['products_count'] ?>)</h3>
    <div class="text-right border-btm">

    </div>
    <div class="prd_list_wrap block">
        <div class="row">
            <?php
            foreach ($wishlists_products as $key => $product) {

                if ($key % 2) {
                    $class = "grey_bg_row";
                }
                else {
                    $class = "white_bg_row";
                }
                ?>


                <?php
                $product_link = front_url() . 'productdetails/' . urlencode($product['ProductName']) . '/' . $this -> encrypt -> encode($product['ProductId']);
                ?>
                <div class="col-xs-12 <?php echo $class ?>" data-product="<?php echo $product['ProductId'] ?>">
                    <div class="col-xs-2"><div class="prd_img"> <a href="<?php echo $product_link ?>">
                                <?php
                                if ($product['ProductImage'])
                                    $product_image = front_url() . PRODUCT_IMAGE_PATH . "small/" . $product['ProductImage'];
                                else
                                    $product_image = front_url() . PRODUCT_IMAGE_PATH . "small/" . DEFAULT_PRODUCT_IMAGE_NAME;
                                ?>
                                <img class="img-thumbnail img-responsive" src="<?php echo $product_image ?>" alt="">
                            </a></div></div>
                    <div class="col-xs-6">  <div class="prd_name">
                            <a href="<?php echo $product_link ?>"><?php echo $product['ProductName'] ?></a>
                        </div></div>
                    <div class="col-xs-3" style="padding-top: 7px;"> <div class="prd_price prd_sml">
                            <?php
                            $price_arr = explode('.',$product['RRP']);
                            echo '<span class="number-font">'.$price_arr[0].'<span class="subscript">'.$price_arr[1].'</span></span>';
                                ?>
                        </div></div>
                    <div class="col-xs-1"><a href="javascript:void(0);" class="remove_wishlist_product" data-id="<?php echo $product['ProductId'] ?>" data-id="<?php echo $product['UserWishlistId'] ?>"><i class="fa fa-trash-o fa-2x"></i></a>
                    </div>
                </div>
            <?php } ?>

        </div>
    </div>