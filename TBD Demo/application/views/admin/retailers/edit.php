<?php echo form_open_multipart('retailers/edit_post/' . $Id, array('id' => 'retailers_form', 'class' => 'form-horizontal','autocomplete'=>'off')); ?>

<div class="row">
    <div class="col-xs-7 col-xs-offset-1">
        
        <div class="form-group">
            <div class="row">
                <div class="col-md-12">
                    <label for="groupId[]" >Group <span>*</span></label>
                    <select name="groupId[]" id="groupId" class="form-control select-filter" multiple="multiple" style="height:50px">
                        <?php
                        foreach ($store_groups as $store_group):
                           if (in_array($store_group['Id'], $retailers_storegroups)){                            
                        ?>
                            <option value="<?php echo $store_group['Id'] ?>" selected="selected" ><?php echo $store_group['GroupName'] ?></option>
                        <?php } else { ?>
                            <option value="<?php echo $store_group['Id'] ?>" ><?php echo $store_group['GroupName'] ?></option>
                        <?php } ?>
                        <?php endforeach; ?>
                    </select>                    
                </div>
                
                <div class="col-md-12">                    
                    <div class="error" id="groupId_error">
                        <?php echo form_error('groupId'); ?>
                    </div>
                </div>                
            </div>
        </div>
        
        <div class="form-group">
            <div class="row">
                <div class="col-md-12">
                    <label for="company_name">Company Name <span>*</span></label>
                    <input type="text" class="form-control" name="company_name" placeholder="Company Name" value="<?php echo $CompanyName; ?>">
                    <div class="error">
                        <?php echo form_error('company_name'); ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="row">
                <div class="col-md-12">
                    <label for="company_description">Company Description <span>*</span></label>
                    <textarea class="form-control" name="company_description" placeholder="Company Description"><?php echo $CompanyDescription; ?></textarea>
                    <div class="error">
                        <?php echo form_error('company_description'); ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="row">
                <div class="col-md-12">
                    <label for="street_address">Street Address <span>*</span></label>
                    <input type="text" class="form-control" name="street_address" id="street_address" placeholder="Street Address" value="<?php echo $StreetAddress ?>">
                    <div class="error">
                        <?php echo form_error('street_address'); ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="row">
                <div class="col-md-12">
                    <label for="state" >State <span>*</span></label>
                    <select name="state" id="state" class="form-control select-filter">

                        <?php
                        foreach ($states as $state):
                            if ($StateId != $state['Id'])
                                
                                ?>
                            <option value="<?php echo $state['Id'] ?>" <?php if ($StateId == $state['Id']) echo "selected"; ?>><?php echo $state['Name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="row">
                <div class="col-md-6">
                    <label for="city">City </label>
                    <input type="text" class="form-control" id="city" name="city" placeholder="City" value="<?php echo $City ?>">
                    <div class="error">
                        <?php echo form_error('city'); ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <label for="zip">Zip Code</label>
                    <input type="text" class="form-control" id="zip" name="zip" placeholder="Zip Code" value="<?php echo $Zip ?>">
                    <div class="error">
                        <?php echo form_error('zip'); ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="row"><h3 class="box-title">Retailer Admin Details</h3></div>
        <div class="form-group">
            <div class="row">
                <div class="col-md-6">
                    <label for="first_name" >Retailer Admin First Name <span>*</span></label>
                    <input type="text" class="form-control" name="first_name" placeholder="First Name" value="<?php echo $FirstName; ?>">
                    <div class="error">
                        <?php echo form_error('first_name'); ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <label>Retailer Admin Last Name </label>
                    <input type="text" class="form-control" name="last_name" placeholder="Last Name" value="<?php echo $LastName; ?>">
                    <div class="error">
                        <?php echo form_error('last_name'); ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="row">
                <div class="col-md-6">
                    <label for="email" >Email Address <span>*</span></label>
                    <input type="email" class="form-control" name="email" placeholder="Email Address" value="<?php echo $Email; ?>">
                    <div class="error">
                        <?php echo form_error('email'); ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <label for="contact_tel">Contact Telephone<span></span></label>
                    <input type="text" class="form-control" name="contact_tel" placeholder="Contact Telephone" value="<?php echo $TelephoneFixed; ?>" maxlength="30">
                    <div class="error">
                        <?php echo form_error('contact_tel'); ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="row">
                <div class="col-md-12">
                    <button type="submit" class="btn btn-primary btn-xs block full-width m-b">Update</button>
                    <!--<a class="btn btn-danger btn-xs block full-width m-b" href="<?php //echo site_url('/retailers');   ?>">Cancel</a>-->
                </div>
            </div>
        </div>
    </div>
    <?php
    $image_path = front_url() . RETAILER_IMAGE_PATH . 'medium/' . $LogoImage;
    $style = ( $LogoImage != '' && file_exists('./' . RETAILER_IMAGE_PATH . 'medium/' . $LogoImage) ) ? 'style="background-image: url(' . $image_path . ')"' : '';
    ?>
    <div class="col-xs-3">
        <div class="btn-group profile_image_group">
            <label for="inputImage" class="btn btn-primary btn-xs retailer_image" <?php echo $style ?>>
                <span title="Upload Image" style="background: rgba(254, 254, 254, 0.7) url('<?php echo base_url() ?>../assets/admin/img/upload.png') no-repeat scroll center center / 25px 25px;">
                    <input type="file"  accept="image/*" name="logo" placeholder="Logo" value="" class="hide" id="inputImage" >
                </span>
                <!--<div id="image_text">Upload Company Logo</div>-->
            </label>
        </div>
        
        
        <div class="form-group">
            <div class="row" style="margin-bottom:35px;"></div>
        </div>
        
        <!-- background-color: #F2F2F2; -->
        <div class="form-group">
            <div class="row">
                <div class="col-xs-12" style="margin-bottom: -15px;">
                <div class="col-xs-12" style="margin-left:10px; border: 1px solid #d2d6de; padding-left: 2px !important;">
                    <label for="none" style="width: 100%;text-align: left;font-weight: bold;margin: 10px 10px 0 10px">Subscription Type<span>*</span></label>
                    <div style="margin-left: 10px">
                    <div class="promo-fix1">
                        <label>
                            <input type="checkbox" class="icheck-minimal-check" checked="checked" disabled>
                            Standard
                        </label>
                    </div>
                    <div class="promo-fix1">
                        <label>
                            <input type="checkbox" class="icheck-minimal-check" name="promo_premium" value="1" <?php echo $store_promos['Premium'] == '1' ? 'checked="checked"' : '' ?> >
                            Premium
                        </label>
                    </div>
                    <div class="promo-fix1">
                        <label>
                            <input type="checkbox" class="icheck-minimal-check" name="promo_concierge" value="1" <?php echo $store_promos['Concierge'] == '1' ? 'checked="checked"' : '' ?>>
                            Concierge
                        </label>
                    </div>
                    <div class="promo-fix1">
                        <label>
                            <input type="checkbox" class="icheck-minimal-check" name="promo_messenger" value="1" <?php echo $store_promos['Messenger'] == '1' ? 'checked="checked"' : '' ?>>
                            Messenger+
                        </label>
                    </div>
               </div>     
              </div>     
              </div>
            </div>
        </div>
        
         
        <input type="hidden" name="old_logo" id="old_logo" value=<?php echo $LogoImage ?>>
        <input type="hidden" name="user_id" id="user_id" value=<?php echo $RetailerAdminId ?>>
    </div>

    <!-- Add Modal -->

    <input type="hidden" name="image-x" id="image-x">
    <input type="hidden" name="image-y" id="image-y">
    <input type="hidden" name="image-width" id="image-width">
    <input type="hidden" name="image-height" id="image-height">



</div>
</form>