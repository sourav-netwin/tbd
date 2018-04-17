<section class="one-section">
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
                                <ul class="dropdown-menu">
                                    <?php foreach ($retailers as $retailer) { ?>
                                        <li>
                                            <a href="javascript:void(0);" data-retailer-id="<?php echo $retailer['Id'] ?>">
                                                <img alt="<?php echo $retailer['CompanyName'] ?>" src="<?php echo front_url() . RETAILER_IMAGE_PATH . 'small/' . $retailer['LogoImage']; ?>">
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
                                                <?php echo $store['StoreName'].' ('.round($store['distance'],2).'km)' ?>
                                            </a>
                                        </li>
                                    <?php } ?>
                                </ul>
                            </div>
                        </div>
                    <?php } ?>
                    <ul class="goo-collapsible">
                        <li class="header">Filter By</li>
                        <?php if ($this -> router -> fetch_class() == 'topoffers') {
                            ?>
                            <li class="dropdown"><a class="" href="javascript:void(0);" >Distance</a>
                                <ul id="top_dist_range_filter">
                                    <li>
                                        <div class="radio">
                                            <label><input type="radio" value="1" name="dist_sel" autocomplete="off"> Up to 1KM</label>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="radio">
                                            <label><input type="radio" value="5" name="dist_sel" autocomplete="off"> Up to 5KM</label>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="radio">
                                            <label><input type="radio" value="25" name="dist_sel" autocomplete="off"> Up to 25KM</label>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="radio">
                                            <label><input type="radio" value="100" name="dist_sel" autocomplete="off"> Up to 100KM</label>
                                        </div>
                                    </li>
                                </ul>
                            </li>
                            <?php }
                        ?>
                        <?php if ($is_top_offer == 0) { ?>
                            <div class="checkbox">
                                <label class="offer_select">
                                    <input type="checkbox" name="offer_select" id="offer_select"> On offer
                                </label>
                            </div>
                        <?php } ?>
                        <?php if (!empty($price_range)) { ?>
                            <li class="dropdown"><a class="" href="javascript:void(0);" >Price</a>
                                <ul id="price_range_filter">
                                    <?php foreach ($price_range as $range) { ?>
                                        <li>
                                            <div class="checkbox">
                                                <label>
                                                    <input type="checkbox" data-min="<?php echo $range['min']; ?>" data-max="<?php echo $range['max']; ?>">
                                                    <?php
                                                    if ($range['min'] == 0) {
                                                        echo 'below R ' . $range['max'];
                                                    }
                                                    else {
                                                        ?>
                                                        R <?php echo $range['min']; ?> <?php echo ( $range['max'] > 0 ) ? '- R ' . $range['max'] : ''; ?>
                                        <?php } ?>
                                                </label>
                                            </div>
                                        </li>
                            <?php } ?>
                                </ul>
                            </li>
<?php } ?>
                        </li>
                    </ul>
                </div>
            </div>

<?php echo $product_list; ?>
<?php echo $this -> load -> view('front/cart'); ?>
        </div>
    </div>
</section>