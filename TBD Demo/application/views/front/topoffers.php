<div class="prd_list_container" style="" id="topoffer-container">
    <div class="manage_content">
        <h2 class="grey_heading">Top Offers <small>at <?php echo ucwords( $user_preferred_retailer->CompanyName ); ?> (<?php echo $count_top_offers; ?>)</small></h2>
        <div class="panel panel-offer">
            <div class="panel-heading">Todays</div>
            <div class="panel-body">
                <div class="offer_head">
                    <span>TOP</span>  OFFERS
                </div>
                <input type="hidden" id="init_dist" value="<?php echo $location_preference[0]['PrefDistance']; ?>" />
                <?php if( !empty( $retailers ) ) { ?>
                    <div class="subtitle">from across <?php echo $all_retailers_count; ?> Supermarkets</div>
                    <ul class="store" id="top_offer_retailers">
                        <?php foreach ( $retailers as $retailer ) { ?>
                                <li>
                                    <a href="javascript:void(0);" class="thumbnail small-thumb" data-retailer-id="<?php echo $retailer['Id']; ?>">
                                       <img src="<?php echo front_url().RETAILER_IMAGE_PATH.'small/'.$retailer['LogoImage']; ?>" class="img-responsive">
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
        <div class="prd_list_wrap special_offer">
            <div class="row">
            <?php if( !empty( $top_offers ) ) { ?>
                <?php echo $top_offers;  ?>
            <?php } else { ?>
                <small>No special offer available for now at  <?php echo ucwords( $user_preferred_retailer->CompanyName ); ?></small>
            <?php } ?>
            </div>
        </div>
        <?php if( $count_top_offers > $this->config->item('top_offer_product_limit') ) { ?>
            <div class="text-center">
                <h4>
                    <a href="javascript:void(0);" id="show_more">Show more</a>
                </h4>
            </div>
    <?php } ?>
    <input type="hidden" name="last_offer_product" id="last_offer_product" value="<?php echo $last_special_product_id ?>">
    </div>
</div>