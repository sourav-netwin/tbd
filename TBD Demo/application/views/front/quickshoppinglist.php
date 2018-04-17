<section class="one-section">
    <div class="container">
        <div class="clearfix">
            <div class="filter_wrap">
                <div class="fix">
                    <?php if (!empty($retailers)) { ?>
                        <div class="supermarket_select">
                            <div class="btn-group" id="retailer_select">
                                <button id="retailer_select_btn" type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-retailer-id="<?php echo $user_preferred_retailer->Id ?>">
                                    <img src="<?php echo front_url() . RETAILER_IMAGE_PATH . 'small/' . $user_preferred_retailer->LogoImage; ?>"> <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu">
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
                            <div class="btn-group" id="store_select">
                                <button id="store_select_btn" type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-store-id="<?php echo $user_preferred_retailer->StoreId ?>">
                                    <?php echo $user_preferred_retailer->StoreName ?> <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu">
                                    <?php foreach ($nearest_stores as $store) { ?>
                                        <li>
                                            <a href="javascript:void(0);" data-store-id="<?php echo $store['Id'] ?>">
                                                <?php echo $store['StoreName'] ?>
                                            </a>
                                        </li>
                                    <?php } ?>
                                </ul>
                            </div>
                        </div>
                    <?php } ?>
                    <div class="delete_list">
                        <span class="sec_title">Shopping list</span>
                        <span class="ClearButton"><a href=""><i class="fa fa-trash"></i></a></span>
                    </div>
                    <?php echo form_open(front_url() . 'filter/quickshopping_list', array('id' => 'quicklist_form')); ?>

                        <div class="form-group">
                            <textarea class="form-control quicklist_box" style="white-space: pre-line;" wrap="hard" id="quick_search_text" name="shopping_list" placeholder="Bread
                                      Butter"> <?php echo nl2br(str_replace(',', '&#13;&#10;', $this->session->userdata('shopping_list'))); ?></textarea>
                        </div>

                        <div class="form-group">
                            <button class="btn quick_search_button" id="quicklist_button" type="submit">Search</button>
                        </div>
                    </form>
                </div>
            </div>
            <?php echo $this->load->view('front/cart'); ?>

            <!--  Add Quick Shopping List -->
            <div class="prd_list_container" style="">
                <h2 class="grey_heading">Quick Shopping List <small>at <?php echo ucwords($user_preferred_retailer->CompanyName); ?> (<?php echo count($products); ?>)</small></h2>

                <input type="hidden" name="quick_search_count" id="quick_search_count" value="<?php echo count($products); ?>">
                <?php
                $i = 0;
                foreach ($products as $key => $product) {
                    ?>
                    <div class="manage_content">
                        <div class="panel panel-offer">
                            <div class="grey_bg_head"><?php echo $key; ?> (<?php echo count($product); ?>)</div>
                        </div>
                    </div>
                    <?php if(!empty($product)) { ?>
                    <ul class="flexisel_<?php echo $i ?>">
                        <?php foreach ($product as $key => $product) { ?>
                            <li>
                                <?php $data['product'] = $product; ?>
                                <?php echo $this->load->view('front/product_wrap', $data); ?>
                            </li>
                        <?php } ?>
                    </ul>
                    <?php } else { ?>
                    <div class="white_bg_row">No results found for <b><?php echo $key?></b> in <?php echo ucwords($user_preferred_retailer->CompanyName); ?></div>
                    <?php } ?>
                    <?php $i++;
                } ?>
            </div>
        </div>
    </div>

</section>