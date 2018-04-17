<!-- /.row -->
<style>
    .form-horizontal .form-group {
        margin-right: 0px !important;
        margin-left: 0px !important;
    }
</style>
<div class="row">
    <?php echo form_open('stores/edit_post/' . $Id, array('id' => 'stores_form', 'class' => 'form-horizontal', 'autocomplete' => 'off')); ?>
    <div class="col-md-5 col-md-offset-1">

        <?php /* ?>
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
        <?php */ ?>
         
         
        <div class="form-group">
            <div class="row">
                <div class="col-xs-2">
                    <label for="store_id">Store Id<span>*</span></label>
                    <input type="text" class="form-control" name="store_id" placeholder="Store Id" value="<?php echo $StoreId ?>">
                    <div class="error">
                        <?php echo form_error('store_id'); ?>
                    </div>
                </div>
                <div class="col-xs-10" style="margin-bottom: -15px">
                    <label for="store_name">Store Name <span>*</span></label>
                    <input type="text" class="form-control" name="store_name" placeholder="Store Name" value="<?php echo $StoreName ?>">
                    <div class="error">
                        <?php echo form_error('store_name'); ?>
                    </div>
                </div>
            </div>
        </div>
        
        
        <div class="form-group">
            <div class="row">
                <div class="col-xs-12" style="margin-bottom: -15px;">
                <div class="col-xs-12" style="margin:0px; border: 1px solid #d2d6de; padding-left: 2px !important;">
                    <label for="none" style="width: 100%;text-align: left;font-weight: bold;margin-bottom: 0px;">Subscription Type<span>*</span></label>
                    <div style="margin-left: 5px">
                    <div class="promo-fix">
                        <label>
                            <input type="checkbox" class="icheck-minimal-check" checked="checked" disabled>
                            Standard
                        </label>
                    </div>
                    <div class="promo-fix">
                        <label>
                            <input type="checkbox" class="icheck-minimal-check" name="promo_premium" value="1" <?php echo $store_promos['Premium'] == '1' ? 'checked="checked"' : '' ?> >
                            Premium
                        </label>
                    </div>
                    <div class="promo-fix">
                        <label>
                            <input type="checkbox" class="icheck-minimal-check" name="promo_concierge" value="1" <?php echo $store_promos['Concierge'] == '1' ? 'checked="checked"' : '' ?>>
                            Concierge
                        </label>
                    </div>
                    <div class="promo-fix">
                        <label>
                            <input type="checkbox" class="icheck-minimal-check" name="promo_messenger" value="1" <?php echo $store_promos['Messenger'] == '1' ? 'checked="checked"' : '' ?>>
                            Messenger+
                        </label>
                    </div>
<!--                    <div class="promo-fix">
                        <label>
                            <input type="checkbox" class="icheck-minimal-check" name="promo_admanager" value="1" <?php //echo $store_promos['AdManager'] == '1' ? 'checked="checked"' : '' ?>>
                            AdManager
                        </label>
                    </div>-->
               </div>     
              </div>     
              </div>
            </div>
        </div>

        <div class="col-md-12" style="margin-bottom: 10px"><div class="row"></div></div> 
        
        <div class="form-group" >
            <div class="row">
                <div class="col-xs-12" style="margin-bottom: -15px">
                    <div class="col-xs-12" style="margin:0px; border: 1px solid #d2d6de; padding-left: 2px !important;">
                    <label for="groupId[]" style="width: 85%;text-align: left;font-weight: bold;margin-bottom: 0px;">Group<span>*</span></label>
                    <div style="margin-left: 5px">
                        <?php foreach ($store_groups as $store_group): ?>
                        <div class="promo-fix">
                            <input type="checkbox" class="icheck-minimal-check one_required" name='groupId[]' id="groupId_<?php echo $store_group['Id']; ?>" value="<?php echo $store_group['Id']; ?>" <?php echo in_array($store_group['Id'], $retailers_storegroups) ? 'checked="checked"' : '' ?> >
                            <?php echo $store_group['GroupName']; ?>                        
                        </div>
                        <?php endforeach; ?>
                    </div>
                     </div>
                </div>                 
            </div>
        </div>
        
        <div class="col-md-12" style="margin-bottom: 10px">
            <div class="row">                
                 <div class="col-md-12 showError" style="margin:0px; padding-left: 2px !important;">                    
                    <div class="error" id="groupId_error">
                        <?php echo form_error('groupId'); ?>
                    </div>
                </div> 
            </div>
        </div>
        
        <div class="col-md-12" style="margin-bottom: 10px"><div class="row"></div></div> 
        <div class="form-group">
            <div class="row">
                <?php
                //Admin Users
                if ($this -> session -> userdata('user_level') < 3) {
                    ?>
                    <div class="col-md-6">
                        <label for="retailers">Retailers <span>*</span></label>
                        <select class="form-control select-filter" id="retailers" name="retailers">
                            <option value="<?php echo $RetailerId; ?>"><?php echo $CompanyName; ?></option>
                            <?php
                            if (!empty($retailers)) :
                                foreach ($retailers as $retailer) {
                                    if ($RetailerId != $retailer['Id'])
                                        echo "<option value='" . $retailer['Id'] . "'>" . $retailer['CompanyName'] . "</option>";
                                }
                            endif;
                            ?>
                        </select>
                        <div class="error">
                            <?php echo form_error('retailers'); ?>
                        </div>
                    </div>
                <?php } ?>
                <?php
                //Retailers Users
                if ($this -> session -> userdata('user_level') <= 3) {
                    ?>
                    <div class="<?php echo ( $this -> session -> userdata('user_type') != 3 ) ? 'col-md-6' : 'col-md-12'; ?>">
                        <label for="store_format">Store Format <span>*</span></label>
                        <select class="form-control select-filter" id="store_format" name="store_format">
                            <?php
                            if (!empty($store_formats)) :
                                foreach ($store_formats as $store_format) {
                                    $selected = "";
                                    if ($StoreTypeId == $store_format['Id']) {
                                        $selected = "selected=\"selected\"";
                                    }
                                    echo "<option " . $selected . " value='" . $store_format['Id'] . " '>" . $store_format['StoreType'] . "</option>";
                                }
                            endif;
                            ?>
                        </select>
                        <div class="error">
                            <?php echo form_error('store_format'); ?>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>



        <div class="form-group">
            <div class="row">
                <div class="col-md-6">
                    <label for="latitude">Latitude <span>*</span></label>
                    <input type="text" class="form-control" name="latitude"  id="latitude" placeholder="Latitude" value="<?php echo $Latitude ?>">
                    <div class="error">
                        <?php echo form_error('latitude'); ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <label for="longitude">Longitude <span>*</span></label>
                    <input type="text" class="form-control" name="longitude" id="longitude" placeholder="Longitude" value="<?php echo $Longitude ?>">
                    <div class="error">
                        <?php echo form_error('longitude'); ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="row">
                <div class="col-md-6">
                    <label for="store_contact_person">Store Contact Person<span></span></label>
                    <input type="text" class="form-control" name="store_contact_person" placeholder="Store Contact Person" value="<?php echo $ContactPerson ?>" maxlength="50">
                    <div class="error">
                        <?php echo form_error('store_contact_person'); ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <label for="store_contact_person">Store Contact Phone<span></span></label>
                    <input type="text" class="form-control" name="store_contact_tel" placeholder="Store Contact Telephone" value="<?php echo $ContactPersonNumber ?>" maxlength="30">
                    <div class="error">
                        <?php echo form_error('store_contact_tel'); ?>
                    </div>
                </div>
            </div>
        </div>

        <?php $days = array('1' => 'Mon', '2' => 'Tue', '3' => 'Wen', '4' => 'Thu', '5' => 'Fri', '6' => 'Sat', '7' => 'Sun',); ?>

        <div class="form-group">
            <div class="row">
                <div class="col-md-12">
                    <label for="open_days">Store Time</label><a href="javascript:void(0)" title="Copy Monday's time to all days" class="col-md-offset-2" id="copy_time">Copy time</a>
                    <?php
                    $i = 1;
                    foreach ($days as $key => $val):
                        if ($i % 2 != 0) {
                            ?>
                        </div><div class="store_time_row">
                        <?php } ?>
                        <div class="col-md-6">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" value="<?php echo $key; ?>" name="open_days[]" <?php echo ($store_timings[$key - 1]['OpenCloseStatus'] == '1') ? "checked" : ""; ?>/> <?php echo $val; ?>
                                </label>
                            </div>
    <!--                            <input type="text" class="form-control" name="open_hours[]" placeholder="Open Hours" value=" <?php echo ($store_timings[$key - 1]['OpenCloseStatus'] == '1') ? $store_timings[$key - 1]['OpenCloseTimeFrom'] : ""; ?>">-->
                            <?php
                            $from = "";
                            $to = "";
                            if ($store_timings[($key - 1)]['OpenCloseStatus'] == '1') {
                                $time = explode('-', $store_timings[($key - 1)]['OpenCloseTimeFrom']);
                                if ($time['0'])
                                    $from = $time['0'];
                                if ($time['1'])
                                    $to = $time['1'];
                            } else {
                                $from = "";
                                $to = "";
                            }
                            ?>
                            <div class="input-group bootstrap-timepicker timepicker">
                                <input type="text" class="form-control timepicker picker" name="open_hours[]" placeholder="Open Time" value="<?php echo $from ?>" Size="6">
                                <!--                            </div>
                                                            <div class="input-group bootstrap-timepicker timepicker">-->
                                <input type="text" class="form-control timepicker picker" name="open_hours[]" placeholder="Close Time" value="<?php echo $to ?>" Size="6">
                            </div>
                        </div>
                        <?php
                        $i++;
                    endforeach;
                    ?>
                    <?php if ($i == '7') { ?>
                        <div class="col-md-6">&nbsp;</div>
                    <?php } ?>
                    <div class="col-md-12">
                        <div id="store_time_errors"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-5">
        <div class="form-group">
            <div class="row">
                <div class="col-md-12">
                    <label for="building">Address Line 1 <span></span></label>
                    <input type="text" class="form-control" name="building" id="building" placeholder="Building" maxlength="100" value="<?php echo $Building ?>">
                    <div class="error">
                        <?php echo form_error('building'); ?>
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
                        <option value="<?php echo $StateId ?>"><?php echo $Name ?></option>
                        <?php
                        foreach ($states as $state):
                            if ($StateId != $state['Id'])
                                
                                ?>
                            <option value="<?php echo $state['Id'] ?>"><?php echo $state['Name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="row">
                <div class="col-md-6">
                    <label for="city">City <span>*</span></label>
                    <input type="text" class="form-control" id="city" name="city" placeholder="City" value="<?php echo $City ?>">
                    <div class="error">
                        <?php echo form_error('city'); ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <label for="zip">Zip Code<span>*</span></label>
                    <input type="text" class="form-control" id="zip" name="zip" placeholder="Zip Code" value="<?php echo $Zip ?>">
                    <div class="error">
                        <?php echo form_error('zip'); ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="row">
                <div class="col-md-12">
                    <div><a href="javascript:void(0);" id="display_lat_long" class="btn btn-default btn-xs">Get Latitude & Longitude</a></div>
                </div>
            </div>
        </div>
        <div class="col-md-12" id="map_div" style="margin-top: 0 !important">

        </div>

    </div>

    <div class="form-group">
        <div class="row">
            <div class="col-xs-10 col-xs-offset-1">
                <button type="submit" class="btn btn-primary btn-xs block full-width m-b" id="submitBtn">Update</button>
                <!--<a class="btn btn-danger btn-xs block full-width m-b" href="<?php //echo site_url('/stores');        ?>">Cancel</a>-->
            </div>
        </div>
    </div>
</form>
</div>