<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
        <title>The Best Deal</title>

        <!-- Bootstrap -->
        <link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=Raleway|Varela+Round|Baloo+Bhai|Oswald" />
        <link href="<?php echo $this -> config -> item('front_assets'); ?>css/bootstrap.css" rel="stylesheet">
        <link href="<?php echo $this -> config -> item('front_assets'); ?>css/bootstrap-select.css" rel="stylesheet">
        <link href="<?php echo $this -> config -> item('front_assets'); ?>css/custom.css" rel="stylesheet">
        <link href="<?php echo $this -> config -> item('front_assets'); ?>css/style.css" rel="stylesheet">
        <link href="<?php echo $this -> config -> item('front_assets'); ?>css/custom_responsive.css" rel="stylesheet">
        <link href="<?php echo $this -> config -> item('front_assets'); ?>css/font-awesome.css" rel="stylesheet">
        <link href="<?php echo $this -> config -> item('front_assets'); ?>css/jquery-ui.css" rel="stylesheet">
        <link href="<?php echo $this -> config -> item('front_assets'); ?>css/plugins/nouislider.min.css" rel="stylesheet">

        <?php if ($this -> router -> fetch_class() == 'productdetails') { ?>
            <!-- Map -->
            <link href="<?php echo $this -> config -> item('front_assets'); ?>css/plugins/zoomify.css" rel="stylesheet">

        <?php } ?>

        <!-- Toastr CSS -->
        <link href="<?php echo $this -> config -> item('front_assets'); ?>css/plugins/toastr.min.css" rel="stylesheet" type="text/css">

        <!-- Toggles CSS -->
        <link href="<?php echo $this -> config -> item('front_assets'); ?>css/plugins/toggles.css" rel="stylesheet" type="text/css">
        <link href="<?php echo $this -> config -> item('front_assets'); ?>css/plugins/toggles-light.css" rel="stylesheet" type="text/css">

        <!-- Raty CSS -->
        <link href="<?php echo $this -> config -> item('front_assets'); ?>css/jquery.raty.css" rel="stylesheet">

        <link href="<?php echo $this -> config -> item('front_assets'); ?>css/plugins/yamm.css" rel="stylesheet">

        <link href="<?php echo $this -> config -> item('front_assets'); ?>css/plugins/cropper/cropper.min.css" rel="stylesheet">
        <link href="http://www.phpformbuilder.pro/phpformbuilder/plugins/popover/dist/jquery.webui-popover.min.css" rel="stylesheet" media="screen">

        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
          <script src="js/html5shiv.min.js"></script>
          <script src="js/respond.min.js"></script>
        <![endif]-->
        <script type="text/javascript">
            var user_det_pg = '';
            var tbd_csrf = "<?php echo $this -> security -> get_csrf_hash(); ?>";
        </script>
    </head>
    <body>
        <nav class="navbar navbar-default">
            <div class="container">
                <!-- Brand and toggle get grouped for better mobile display -->
                <div class="navbar-header">

                    <a class="navbar-brand" href="<?php echo front_url() ?><?php
        if ($this -> session -> userdata('userid')) {
            echo 'topoffers';
        }
        ?>">
                        <img src="<?php echo $this -> config -> item('front_assets'); ?>img/TBD-logo-small.png" class="img-responsive header-logo-small" alt="the best deal">
                        <span> <img src="<?php echo $this -> config -> item('front_assets'); ?>img/logo-text.png" border="0"></span>
                    </a>
                </div>

                <?php
                if ($this -> session -> userdata('userid')) {
                    echo form_open(front_url() . 'productslist/search', array('method' => 'get', 'id' => 'search_form', 'class' => 'navbar-form navbar-left', 'role' => 'search'));
                    ?>
                    <div class="input-group">


                        <input type="text" id="search_text" name="search_text" class="form-control" placeholder="Search Product" style="display:none" value="<?php
                if (isset($search_text)) {
                    echo $search_text;
                }
                    ?>">
                        <span class="input-group-btn">
                            <button class="btn btn-search pull-right " type="button" id="search_button">Go!</button>
                        </span>

                    </div><!-- /input-group -->
                    </form>
                    <div class="quickListheader">
                        <span><a class="btn btn-quick-shop" role="button" data-toggle="collapse" data-target="#quickListDisplay" aria-expanded="true" aria-controls="quickListDisplay" >Quick Shopping List</a>

                        </span>
                        <div class="collapse" id="quickListDisplay">
                            <?php echo form_open(front_url() . 'filter/quickshopping_list', array('id' => 'quick_search_form')); ?>
                            <div class="quickshopping-list">
                                <small class="quick-note-head">Search to View On-the-Fly Pricing</small>
                                <input type="text" id="quick-list-search" placeholder="Search Text" />
                                <div class="quick-auto-list" id="quick-auto-list">
                                </div>
                                <div class="search_quick_area">
                                    <table>
                                        <?php
                                        if ($quick_shopping_list) {
                                            $total_price = 0;
                                            foreach ($quick_shopping_list as $item) {
                                                ?>
                                                <tr data-pi="<?php echo $item['product_id'] ?>" data-ri="<?php echo $item['retailer_id'] ?>" data-sti="<?php echo $item['store_type_id'] ?>" data-si="<?php echo $item['store_id'] ?>">
                                                    <td><?php echo $item['name'] ?></td><td><input type="text" value="<?php echo $item['count'] ?>" class="quick_count click_sel" /></td><td class="number-font <?php echo $item['is_special'] == 1 ? 'text-danger' : '' ?>  quick_price"><?php echo $item['price'] ?></td><td><a href="javascript:void(0)" class="quick-remove"><i class="fa fa-close"></i></a></td>
                                                </tr>
                                                <?php
                                                $total_price += round($item['price'], 2);
                                            }
                                            $total_price = $total_price.'';
                                            $total_array = explode('.',$total_price);
                                            if(!isset($total_array[1])){
                                                $total_price = $total_price.'.00';
                                            }
                                            elseif(strlen($total_array[1]) == 1){
                                                $total_price = $total_array[0].'.'.$total_array[1].'0';
                                            }
                                            ?>
                                            <tfoot class="quick-foot">
                                                <tr>
                                                    <td>Total</td>
                                                    <td colspan="2" class="number-font text-right" id="quick_tot_price"><?php echo $total_price; ?></td>
                                                </tr>
                                            </tfoot>
                                            <?php
                                        }
                                        ?>
                                    </table>
                                </div>
                                <!--<small>type list items and then click search</small>-->
    <!--                            <textarea class="search_area" style="white-space: pre-line;" wrap="hard" id="quick_search_text" name="shopping_list" placeholder="Bread
                                          Butter"> <?php //echo nl2br(str_replace(',', '&#13;&#10;', $this -> session -> userdata('shopping_list')));          ?></textarea>-->
                                <!--<button class="btn quick_search_button" type="button" disabled="">Search</button>-->
                            </div>
                            </form>
                        </div>
                    </div>
                    <?php
                }
                ?>
                <span class="visible-xs inline"><a href="<?php echo front_url() . 'faq' ?>" class="btn btn-help">Need help</a></span>
                <span class="navbar-right view_basket_btn">
                    <a href="<?php echo front_url() . 'viewbasket' ?>" class="btn view_basket" style="margin-top: 8px;"><img style="width: 35px" src="<?php echo base_url() ?>../assets/front/img/tbd_cart.gif" /></a>
                </span>

                <?php if ($this -> session -> userdata('userid')) { ?>
                    <ul class="nav navbar-nav navbar-right user_profile_menu">
                        <li class="dropdown">
                            <a href="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Welcome <?php echo character_limiter($this -> session -> userdata('name'), 6); ?> <span class="caret"></span></a>
                            <ul class="dropdown-menu">
                                <li>
                                    <a href="<?php echo front_url() ?>my_profile">
                                        <img src="<?php echo $this -> config -> item('front_assets'); ?>img/menu_user.png">
                                        Profile
                                    </a>
                                </li>
                                <li>
                                    <a href="<?php echo front_url() ?>my_profile#wishlists">
                                        <img src="<?php echo $this -> config -> item('front_assets'); ?>img/menu_wishlist.png">
                                        Wishlists
                                    </a>
                                </li>
                                <li>
                                    <a href="<?php echo front_url() ?>my_profile#pricealerts">
                                        <img src="<?php echo $this -> config -> item('front_assets'); ?>img/menu_dollar.png">
                                        Price Alerts
                                    </a>
                                </li>
                                <li>
                                    <a href="<?php echo front_url() ?>my_profile#favorites">
                                        <img src="<?php echo $this -> config -> item('front_assets'); ?>img/menu_fav.png">
                                        Favorites
                                    </a>
                                </li>
                                <li>
                                    <a href="<?php echo front_url() ?>my_profile#notification">
                                        <img src="<?php echo $this -> config -> item('front_assets'); ?>img/menu_notifications.png">
                                        Notifications
                                    </a>
                                </li>
                                <li>
                                    <a href="<?php echo front_url() ?>logout">
                                        <img src="<?php echo $this -> config -> item('front_assets'); ?>img/menu_logout.png">
                                        Logout
                                    </a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                    <?php
                }
                else {
                    ?>
                    <ul class="nav navbar-nav navbar-right">
                        <li class="right-brd"><a href="javascript:void(0)" id="user_login">Sign In</a></li>
                        <li><a href="<?php echo front_url() ?>registration">Registration</a></li>
                    </ul>
                    <input type="hidden" id="instaPath" value="<?php echo $this -> instagram_api -> instagramLogin() ?>" />
                    <input type="hidden" id="googlePath" value="<?php echo $this -> instagram_api -> instagramLogin() ?>" />
                    <input type="hidden" id="uval1" value="<?php echo ( set_value('email') != '' ? set_value('email') : $this -> input -> cookie('email', TRUE) ); ?>" />
                    <input type="hidden" id="uval2" value="<?php echo ( set_value('password') != '' ? set_value('password') : $this -> input -> cookie('password', TRUE) ); ?>" />
                    <input type="hidden" id="uval3" value="<?php echo ( $this -> input -> cookie('email', TRUE) != '' && $this -> input -> cookie('password', TRUE) != '' ) ? 'checked' : '' ?>" />

                    <div class="modal fade" id="forgot_password_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="z-index: 9999999;">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                    <h4 class="modal-title" id="myModalLabel">Forgot Password</h4>
                                </div>
                                <div class="modal-body">
                                    <?php echo form_open(front_url() . 'login/forgot_password', array('id' => 'forgot_password_form', 'class' => 'form-horizontal custom_form')); ?>
                                    <div class="form-group">
                                        <label for="forgot_pwd_email" class="col-sm-1 control-label">Email</label>
                                        <div class="col-sm-9">
                                            <input type="email" class="form-control" id="forgot_pwd_email" name="forgot_pwd_email" placeholder="Email" value="<?php echo set_value('forgot_pwd_email'); ?>">
                                            <div class="error">
                                                <?php echo form_error('forgot_pwd_email'); ?>
                                            </div>
                                        </div>
                                    </div>
                                    </form>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-login" name="forgot_pwd_submit" id="forgot_pwd_submit">Send</button>
                                </div>
                                <input type="hidden" name="forgot_pwd_error" id="forgot_pwd_error" value="<?php echo $this -> session -> userdata('forgot_pwd_error'); ?>">
                                <?php
                                $this -> session -> unset_userdata('forgot_pwd_error');
                                ?>
                            </div>
                        </div>
                    </div>
                <?php } ?>
                <span class="navbar-right hidden-xs"><a href="<?php echo front_url() . 'faq' ?>" class="btn btn-help">Need help</a></span>
            </div><!-- /.container-fluid -->
        </nav>
        <input type="hidden" id="base_url" name="base_url" value="<?php echo front_url(); ?>">