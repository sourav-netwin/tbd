<div class="prd_list_container" style="">
    <div class="manage_content">
        <h2 class="grey_heading"><?php echo $category_name ?> <small>at <?php echo ucwords( $user_preferred_retailer->CompanyName ); ?> (<span id="product_count"><?php echo $count_products; ?></span>)</small></h2>
        <div class="panel panel-offer">
            <div class="panel-heading"><?php echo $category_name ?></div>
            <div class="panel-body">
                <?php if( !empty( $retailers ) ) { ?>
                    <div class="subtitle">from across <?php echo $all_retailers_count; ?> supermarkets</div>
                    <ul class="store" id="top_offer_retailers">
                        <?php foreach ( $retailers as $retailer ) { ?>
                                <li>
                                    <a href="javascript:void(0);" class="thumbnail" data-retailer-id="<?php echo $retailer['Id']; ?>">
                                       <img src="<?php echo front_url().RETAILER_IMAGE_PATH.$retailer['LogoImage']; ?>" class="img-responsive">
                                    </a>
                                </li>
                        <?php } ?>
                    </ul>
                    <?php if( count( $retailers ) > $this->config->item('top_offer_retailer_limit') ) { ?>
                            <div class="subtitle">
                                <a href="javascript:void(0);" id="all_supermarkets" data-display="all">See all supermarkets</a>
                            </div>
                    <?php } ?>
                <?php } ?>
            </div>
        </div>
        <div class="prd_list_wrap">
            <div class="row">
                <?php if( !empty( $top_offers ) ) { ?>
                <?php echo $top_offers;  ?>
            <?php } else { ?>
                <small>No products available in <?php echo $category_name ?> at  <?php echo ucwords( $user_preferred_retailer->CompanyName ); ?></small>
            <?php } ?>
            </div>
        </div>
        <?php if( $count_products > $this->config->item('top_offer_product_limit') ) { ?>
            <div class="text-center">
                <h4>
                    <a href="javascript:void(0);" id="show_more">Show more</a>
                </h4>
            </div>
    <?php } ?>
    </div>
    <input type="hidden" name="last_product" id="last_product" value="<?php echo $last_product_id ?>">
    <input type="hidden" name="category_id" id="category_id" value="<?php echo $category_id; ?>">
    <input type="hidden" name="category_type" id="category_type" value="<?php echo $category_type; ?>">
</div>