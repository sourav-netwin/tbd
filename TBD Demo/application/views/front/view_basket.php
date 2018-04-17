<section class="one-section">
    <div class="container">
        <div class="clearfix">
            <div class="prd_list_container_2">
                <div class="basket_container">
                    <h2 class="dtl_ttl">My Basket (<?php echo $user_basket_products_count ?>)</h2>
                    <div class="row">
                        <?php
                        if (!empty($user_basket)) {
                            $total_price = 0;
                            ?>

                            <?php
                            foreach ($user_basket as $key => $ub) {

                                if ($key % 2) {
                                    $class = "grey_bg_row";
                                }
                                else {
                                    $class = "white_bg_row";
                                }
                                ?>

                                <?php
                                $product_link = front_url() . 'productdetails/' . urlencode($ub['ProductName']) . '/' . $this -> encrypt -> encode($ub['Id']);
                                ?>
                                <div class="col-xs-12 <?php echo $class ?>" data-product="<?php echo $ub['Id'] ?>">
                                    <div class="col-xs-2"><div class="prd_img"> <a href="<?php echo $product_link ?>">
                                                <?php
                                                if ($ub['ProductImage'])
                                                    $product_image = front_url() . PRODUCT_IMAGE_PATH . "small/" . $ub['ProductImage'];
                                                else
                                                    $product_image = front_url() . PRODUCT_IMAGE_PATH . "small/" . DEFAULT_PRODUCT_IMAGE_NAME;
                                                ?>
                                                <img class="img-thumbnail img-responsive" src="<?php echo $product_image ?>" alt="">
                                            </a></div></div>
                                    <div class="col-xs-5">  <div class="prd_name vcenter">
                                            <a href="<?php echo $product_link ?>"><?php echo $ub['ProductName'] ?></a>
                                        </div></div>
                                    <div class="col-xs-2">
                                        <div class="input-group">
                                            <span class="input-group-addon minus-cart" style="cursor: pointer">-</span>
                                            <input type="text" style="width: 39px;padding: 0" value="<?php echo $ub['ProductCount'] ?>" class="text-center basket-pr-change form-control" data-id="<?php echo $ub['basket_id'] ?>" />
                                            <span class="input-group-addon plus-cart" style="cursor: pointer">+</span>
                                        </div>

                                    </div>
                                    <div class="col-xs-2"> 
                                        <div class="prd_price vcenter" style="text-align: center !important">
                                            <?php
                                            $price_get = $ub['Price'];

                                            $total_price = $total_price + $price_get;
                                            $price_arr = explode('.', $price_get);
                                            if (!isset($price_arr[1])) {
                                                $price_arr[1] = '00';
                                            }
                                            echo '<span class="number-font basket-num"><span class="b-main">' . $price_arr[0] . '</span><span class="b-sub">' . $price_arr[1] . '</span></span>';
                                            ?>
                                        </div>
                                    </div>
                                    <div class="col-xs-1 vcenter"><a href="javascript:void(0);" class="remove_basket" data-product="<?php echo $ub['Id'] ?>"><i class="fa fa-trash-o fa-2x"></i></a>
                                    </div>
                                </div>



                                <?php
                            }
                            if ($key % 2) {
                                $class = "white_bg_row";
                            }
                            else {

                                $class = "grey_bg_row";
                            }
                            ?>
                            <div class="col-xs-12 <?php echo $class ?>">
                                <div class="col-xs-8 text-right">Total Price: </div>
                                <?php
                                $tot_arr = explode('.', number_format($total_price, 2));
                                ?>
                                <div class="col-xs-3 prd_price"><span class="number-font basket-tot-num"><span class="b-main"><?php echo $tot_arr[0]; ?></span><span class="b-sub"><?php echo $tot_arr[1] ?></span></span></div>
                                <div class="col-xs-1">&nbsp;</div>
                            </div>


                        <?php } ?>
                        <div class="col-xs-12">
                            <h2 class="dtl_ttl">Where To Shop Today?<br/><span class="small_text"> ( * Indicates price may vary if some products not present for that retailer )</span></h2>
                            <?php if (!empty($user_basket_other_retailer)) { ?>

                                <ul class="cart_list">
                                    <?php foreach ($user_basket_other_retailer as $key => $value) { ?>
                                        <li>
                                            <a class="thumbnail">
                                                <img class="media-object" src="<?php echo front_url() . RETAILER_IMAGE_PATH . 'medium/' . $key; ?>" alt="">

                                                <div class="basket_price">
                                                    R<?php echo $value[0] ?><?php echo ( $value[1] == 0 ) ? '*' : '' ?>
                                                </div>
                                            </a>
                                        </li>
                                    <?php } ?>
                                <?php } ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>