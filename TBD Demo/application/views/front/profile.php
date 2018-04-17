<section class="one-section">
    <div class="container">
        <div class="clearfix">
            <?php //echo $this -> load -> view('front/cart'); ?>
            <div class="prd_list_container_2" style="">
                <div class="manage_content">
                    <h2 class="underline" style="font-size: 25px">My Account</h2>
                    <div class="">
                        <div class="tabs-left">
                            <ul id="myTab" class="nav nav-tabs" style="margin-bottom: 9px;">
                                <li class="active">
                                    <a href="#profile" data-toggle="tab">
                                        <!--<span class="user_profile"><img src="<?php echo $this -> config -> item('front_assets'); ?>img/default.gif" class="img-responsive"></span>-->
                                        Profile
                                    </a>
                                </li>
                                <li>
                                    <a href="#wishlists" data-toggle="tab">
                                        <!--<span class="grey_box"><img src="<?php echo $this -> config -> item('front_assets'); ?>img/wishlist.png" class="img-responsive" /></span>-->
                                        Wishlists
                                    </a>
                                </li>
                                <li>
                                    <a href="#pricealerts" data-toggle="tab">
                                        <!--<span class="grey_box"><img src="<?php echo $this -> config -> item('front_assets'); ?>img/price_alert.png" class="img-responsive" /></span>-->
                                        Price Alerts
                                    </a>
                                </li>
                                <li>
                                    <a href="#favorites" data-toggle="tab">
                                        <!--<span class="grey_box"><img src="<?php echo $this -> config -> item('front_assets'); ?>img/fav.png" class="img-responsive" /></span>-->
                                        Favorites
                                    </a>
                                </li>
                                <li>
                                    <a href="#notification" data-toggle="tab">
                                        <!--<span class="grey_box"><img src="<?php echo $this -> config -> item('front_assets'); ?>img/notification.png" class="img-responsive" /></span>-->
                                        Notification
                                    </a>
                                </li>
                                <li>
                                    <a href="<?php echo front_url() ?>logout" >
                                        <!--<span class="grey_box"><img src="<?php echo $this -> config -> item('front_assets'); ?>img/logout.png" class="img-responsive" /></span>-->
                                        Logout
                                    </a>
                                </li>
                            </ul>
                            <div id="myTabContent" class="tab-content" >
                                <div class="tab-pane fade in active" id="profile">
                                    <h3 class="grey_bg_head_green" style="font-size: 25px">Edit Profile</h3>
                                    <?php echo form_open_multipart(front_url() . 'registration/edit_profile', array('id' => 'edit_profile_form', 'class' => 'form-horizontal custom_form block')); ?>
                                    <div style="display:none">
                                        <input type="hidden" name="csrf_tbd_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
                                    </div>
                                    <div class="row">
                                        <div class="col-xs-6">
                                            <div class="row form-group">
                                                <div class="col-xs-6">
                                                    <?
                                                    $first_name = '';
                                                    $last_name = '';
                                                    if(!empty(trim($user_details['FirstName'])) && !empty(trim($user_details['LastName']))){
                                                        $first_name = $user_details['FirstName'];
                                                        $last_name = $user_details['LastName'];
                                                    }
                                                    elseif(empty(trim($user_details['LastName']))){
                                                        $user_name_arr = explode(' ', $user_details['FirstName']);
                                                        $first_name = $user_name_arr[0];
                                                        $last_name = trim(str_replace($user_name_arr[0], '', $user_details['FirstName']));
                                                    }
                                                    ?>
                                                    <label for="first_name" class="control-label">First Name <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" id="first_name" name="first_name" placeholder="First Name" value="<?php echo $first_name ?>">
                                                    <div class="error">
                                                        <?php echo form_error('first_name'); ?>
                                                    </div>
                                                </div>
                                                <div class="col-xs-6">
                                                    <label for="last_name" class="control-label">Last Name</label>
                                                    <input type="text" class="form-control" id="last_name" style="margin-top: 0px !important;" name="last_name" placeholder="Last Name" value="<?php echo $last_name ?>">
                                                    <div class="error">
                                                        <?php echo form_error('last_name'); ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row form-group">
                                                <div class="col-xs-6">
                                                    <label for="telephone" class="control-label">Telephone (Home) <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" id="telephone" name="telephone" placeholder="Telephone (Home)" value="<?php echo $user_details['TelephoneFixed'] ?>">
                                                    <div class="error">
                                                        <?php echo form_error('telephone'); ?>
                                                    </div>
                                                </div>
                                                <div class="col-xs-6">
                                                    <label for="mobile_number" class="control-label">Mobile No.<span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" id="mobile_number" name="mobile_number" placeholder="Mobile No." value="<?php echo $user_details['Mobile'] ?>">
                                                    <div class="error">
                                                        <?php echo form_error('mobile_number'); ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row form-group">
                                                <div class="col-xs-12">
                                                    <label for="" class="control-label">Email Address<span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" id="email" name="email" placeholder="Email Address" value="<?php echo $user_details['Email'] ?>">
                                                    <div class="error">
                                                        <?php echo form_error('email'); ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xs-6">
                                            <?php
                                            $ProfileImage = $user_details['ProfileImage'];
                                            $image_path = front_url() . USER_IMAGE_PATH . '/medium/' . $ProfileImage;
                                            $style = ( $ProfileImage != '' && file_exists('./' . USER_IMAGE_PATH . '/medium/' . $ProfileImage) ) ? 'style="background-image: url(' . $image_path . ') "' : '';
                                            ?>
                                            <div class="btn-group profile_image_group">
                                                <label for="inputImage" class="btn btn-primary btn-xs profile_image mr-bt-15" <?php echo $style ?>>
                                                    <input type="file" accept="image/*" name="profile_image" class="hide" id="inputImage">
                                                    <div id="image_text" style="line-height: 20px">Upload Profile Image</div>
                                                    <div class="error" style="line-height: 0px;">
                                                        <?php echo form_error('profile_image'); ?>
                                                    </div>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <!--                                    <div class="form-group">
                                                                        <label for="" class="col-sm-4 col-md-3 control-label">Address<span class="text-danger">*</span></label>
                                                                        <div class="col-sm-8 col-md-6">
                                                                            <input type="text" class="form-control" id="house_number" name="house_number" placeholder="House No." value="<?php //echo $user_details['HouseNumber']                 ?>">
                                                                            <div class="error">
                                    <?php //echo form_error('house_number'); ?>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <div class="col-sm-offset-3 col-md-offset-3  col-sm-8 col-md-6">
                                                                            <input type="text" class="form-control" id="street_name" name="street_name" placeholder="Street Name" value="<?php //echo $user_details['StreetAddress']                 ?>">
                                                                            <div class="error">
                                    <?php //echo form_error('street_name'); ?>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <div class="col-sm-offset-3 col-md-offset-3  col-sm-8 col-md-6">
                                                                            <input type="text" class="form-control" id="suburb" name="suburb" placeholder="Suburb" value="<?php //echo $user_details['Suburb']                             ?>">
                                                                            <div class="error">
                                    <?php //echo form_error('suburb'); ?>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <div class="col-sm-offset-3 col-md-offset-3  col-sm-8 col-md-6">
                                                                            <input type="text" class="form-control" id="city" name="city" placeholder="City" value="<?php //echo $user_details['City']                             ?>">
                                                                            <div class="error">
                                    <?php //echo form_error('city'); ?>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <div class="col-sm-offset-3 col-md-offset-3  col-sm-8 col-md-6">
                                                                            <input type="text" class="form-control" id="province" name="province" placeholder="Province" value="<?php //echo $user_details['State']                             ?>">
                                                                            <div class="error">
                                    <?php //echo form_error('province'); ?>
                                                                            </div>
                                                                        </div>
                                                                    </div>-->
                                    <div class="row form-group">

                                        <div class="col-xs-6">
                                            <div class="row form-group">
                                                <div class="col-xs-12">
                                                    <label for="" class="control-label">Address<span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" id="us_address" name="us_address" placeholder="Address">
                                                </div>
                                            </div>
                                            <div class="row form-group">
                                                <div class="col-xs-6">
                                                    <label for="" class="control-label">Province<span class="text-danger">*</span></label>
                                                    <select class="form-control" id="province" name="province">
                                                        <option value="">Select Province</option>
                                                        <?php
                                                        if ($provinces) {
                                                            foreach ($provinces as $province) {
                                                                ?>
                                                                <option value="<?php echo $province['Id'] ?>" <?php echo $user_details['State'] == $province['Id'] ? 'selected="selected"' : '' ?>><?php echo $province['Name'] ?></option>
                                                                <?php
                                                            }
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                                <div class="col-xs-6">
                                                    <label for="" class="control-label">City<span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" id="city" name="city" placeholder="City" value="<?php echo $user_details['City'] ?>">
                                                </div>
                                            </div>
                                            <div class="row form-group">
                                                <div class="col-xs-6">
                                                    <label for="" class="control-label">Postal Code<span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" id="pin_code" name="pin_code" placeholder="Pin Code" value="<?php echo $user_details['PinCode'] ?>">
                                                    <div class="error">
                                                        <?php echo form_error('pin_code'); ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row form-group">
                                                <div class="col-xs-6">
                                                    <label for="" class="col-sm-4 col-md-3 control-label">Latitude<span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" id="us_latitude" name="us_latitude" placeholder="Latitude" value="<?php echo $user_details['PrefLatitude'] ?>">
                                                    <input type="hidden" id="set_lat" value="<?php echo $user_details['PrefLatitude'] ?>" />
                                                    <input type="hidden" id="set_lon" value="<?php echo $user_details['PrefLongitude'] ?>" />
                                                    <input type="hidden" id="set_dist" value="<?php echo $user_details['PrefDistance'] ?>" />
                                                </div>
                                                <div class="col-xs-6">
                                                    <label for="" class="col-sm-4 col-md-3 control-label">Longitude<span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" id="us_longitude" name="us_longitude" placeholder="Longitude" value="<?php echo $user_details['PrefLongitude'] ?>">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xs-6" style="overflow: hidden;" >
                                            <label for="" class="control-label">Choose location</label>
                                            <div style="width: 508px; height: 400px" id="location-div">

                                            </div>
                                        </div>
                                    </div>


                                    <div class="row form-group">
                                        <div class="col-xs-12">
                                            <label for="" class="control-label">Preferred Distance</label>
                                            <div id="slider" style="margin-top: 25px"></div>
                                            <input type="hidden" id="us_radius_hd" name="us_radius_hd" value="<?php echo ($user_details['PrefDistance'] * 1000) ?>" />
                                            <input type="hidden" name="image-x" id="image-x">
                                            <input type="hidden" name="image-y" id="image-y">
                                            <input type="hidden" name="image-width" id="image-width">
                                            <input type="hidden" name="image-height" id="image-height">
                                        </div>
                                    </div>
                                    <!--                                    <div class="form-group">
                                                                            <div class="col-md-12">
                                                                                <strong>Change product notification location preference</strong>
                                                                            </div>
                                                                        </div>-->

                                    <div class="form-group">
                                        <div class="col-sm-12 text-center">
                                            <input type="submit" class="btn btn-login" value="Save" data-loading-text="Saving...">
                                        </div>
                                    </div>
                                    <div class="modal fade" id="imageModal" data-keyboard="false" data-backdrop="static">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h4 class="modal-title" id="myModalLabel"> <span id="form-action">Select</span></h4>
                                                </div>
                                                <div class="modal-body" >
                                                    <div class="image-crop">
                                                        <img src="<?php echo $this -> config -> item('admin_assets'); ?>img/default.gif">
                                                    </div>
                                                    <div class="img-select-btn"><a id="crop-button" class="btn btn-primary btn-xs block full-width m-b">Select</a></div>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    </form>
                                </div>
                                <div class="tab-pane fade" id="wishlists">
                                    <h3 class="grey_bg_head_green">Your Wishlist</h3>
                                    <div class="text-right border-btm">
                                        <a href="#" class="btn btn-grey add-btn"  data-toggle="modal" data-target="#add_to_list_modal">Add</a>
                                    </div>
                                    <div class="prd_list_wrap block">
                                        <div class="row"></div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="pricealerts">
                                    <h3 class="grey_bg_head_green">Price alerts</h3>
                                    <div class="text-right border-btm">
                                        <a href="#" class="btn btn-grey remove-btn remove_price_alert">Remove all</a>
                                    </div>
                                    <div class="prd_list_wrap block">
                                        <div class="row"></div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="favorites">
                                    <h3 class="grey_bg_head_green">Favorites</h3>
                                    <div class="text-right border-btm">
                                        <a href="#" class="btn btn-grey remove-btn remove_favorites">Remove all</a>
                                    </div>
                                    <div class="prd_list_wrap block">
                                        <div class="row"></div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="notification">
                                    <h3 class="grey_bg_head_green">Notifications</h3>
                                    <div class="text-right border-btm">
                                        <a href="#" class="btn btn-grey remove-btn remove_notifications">Remove all</a>
                                    </div>
                                    <div class="prd_list_wrap block">
                                        <div class="row"></div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="logout">
                                    <p>logout</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
</div>
</div>
</section>

<!-- Add to list Modal -->
<div class="modal fade" id="add_to_list_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Create WishList</h4>
            </div>
            <div class="modal-body">
                <?php echo form_open(front_url() . 'my_profile/create_list', array('id' => 'add_to_list_form', 'class' => 'form-horizontal custom_form')); ?>

                <div class="form-group" id="new_list_container">
                    <label for="new_list" class="col-sm-3 control-label">Create New</label>
                    <div class="col-sm-6">
                        <input type="text" class="form-control" id="new_list" name="new_list" placeholder="New WishList" value="">
                        <div class="error">
                            <?php echo form_error('new_list'); ?>
                        </div>
                    </div>
                </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-danger" id="add_to_list_btn" name="add_to_list_btn" data-loading-text="Adding...">Add</button>
            </div>
        </div>
    </div>
</div>