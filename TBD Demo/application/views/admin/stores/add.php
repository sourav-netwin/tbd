<!-- /.row -->
<style>
 .promo-fix-group {
    float: left;
    margin-bottom: 15px;
    margin-top: 15px;
    width: 87px !important;
 }
</style>
<div class="row">
    <?php echo form_open('stores/add', array('id' => 'stores_form', 'class' => 'form-horizontal', ' autocomplete' => 'off')); ?>
    <div class="col-md-5 col-md-offset-1">

        <?php /* ?>
        <div class="form-group">
            <div class="row">
                <div class="col-md-12">
                    <label for="groupId[]" >Group <span>*</span></label>
                    <select name="groupId[]" id="groupId" class="form-control select-filter" multiple="multiple" style="height:50px">
                        <?php foreach ($store_groups as $store_group): ?>
                            
                            <option value="<?php echo $store_group['Id'] ?>" ><?php echo $store_group['GroupName'] ?></option>
                        
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
                
        <div class="form-group" >
            <div class="row">
                <div class="col-xs-12" style="margin-bottom: -15px">
                    <div class="col-xs-12" style="margin:0px; border: 1px solid #d2d6de; padding-left: 1px !important;">
                    <label for="" style="width: 100%;text-align: left;font-weight: bold;margin-bottom: 0px;margin-left: 2px;">Group<span>*</span></label>
                    <div style="margin-left: 4px">
                        <?php foreach ($store_groups as $store_group): ?>
                        <div class="promo-fix-group">
                            <input type="checkbox" class="icheck-minimal-check one_required" name='groupId[]' id="groupId_<?php echo $store_group['Id']; ?>" value="<?php echo $store_group['Id']; ?>" <?php echo in_array($store_group['Id'], $retailers_storegroups) ? 'checked="checked"' : '' ?> >
                            <?php echo $store_group['GroupName']; ?>                        
                        </div>
                        <?php endforeach; ?>
                    </div>
                     </div>
                </div>                 
            </div>
        </div>
        <div style="clear:both"></div>
        <div class="form-group" >
            <div class="col-md-12">
                <div class="row">                
                    <div class="col-md-12 showError">                    
                        <div class="error" id="groupId_error">
                            <?php echo form_error('groupId'); ?>
                        </div>
                    </div> 
                </div>
            </div>
        </div>
        
        <div class="form-group">
            <div class="row">
                <?php
                //Admin Users
                if ($this -> session -> userdata('user_level') < 3) {
                    ?>
                    <div class="col-xs-6">
                        <label for="retailers">Retailers <span>*</span></label>
                        <select class="form-control select-filter" id="retailers" name="retailers">
                            <option value="">Select Retailer</option>
                            <?php
                            if (!empty($retailers)) :
                                foreach ($retailers as $retailer) {
                                    echo "<option value='" . $retailer['Id'] . "'" . (( @$_POST['retailers'] == $retailer['Id'] ) ? 'selected' : '') . ">" . $retailer['CompanyName'] . "</option>";
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
                    <div class="<?php echo ( $this -> session -> userdata('user_type') != 3 ) ? 'col-xs-6' : 'col-xs-12'; ?>">

                        <label for="store_format">Store Format <span>*</span></label>
                        <select class="form-control select-filter" id="store_format" name="store_format">
                            <option value="">Select Store Format</option>
                            <?php
                            if (!empty($store_formats)) :
                                foreach ($store_formats as $store_format) {
                                    echo "<option value='" . $store_format['Id'] . "'" . ( ( @$_POST['store_format'] == $store_format['Id'] ) ? 'selected' : '') . ">" . $store_format['StoreType'] . "</option>";
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
                <div class="col-xs-4">
                    <label for="store_id">Store ID<span>*</span></label>
                    <input type="text" class="form-control" name="store_id" placeholder="Store Id" value="<?php echo @$_POST['store_id'] ?>">
                    <div class="error">
                        <?php echo form_error('store_id'); ?>
                    </div>
                </div>
                <div class="col-xs-8">
                    <label for="store_name">Store Name <span>*</span></label>
                    <input type="text" class="form-control" name="store_name" placeholder="Store Name" value="<?php echo @$_POST['store_name'] ?>">
                    <div class="error">
                        <?php echo form_error('store_name'); ?>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="form-group">
            <div class="row">
                <div class="col-md-12">
                    <label for="building">Address Line 1 <span></span></label>
                    <input type="text" class="form-control" name="building" id="building" placeholder="Building" maxlength="100" value="<?php echo @$_POST['building']; ?>">
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
                    <input type="text" class="form-control" name="street_address" id="street_address"placeholder="Street Address" value="<?php echo @$_POST['street_address']; ?>">
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
                        <option value=""> Select State </option>
                        <?php foreach ($states as $state): ?>
                            <option value="<?php echo $state['Id'] ?>" <?php echo ( @$_POST['state'] == $state['Id'] ) ? 'selected' : '' ?>><?php echo $state['Name'] ?></option>
                        <?php endforeach ?>
                    </select>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="row">
                <div class="col-md-6">
                    <label for="city">City <span>*</span></label>
                    <input type="text" class="form-control" id="city" name="city" placeholder="City" value="<?php echo @$_POST['city'] ?>">
                    <div class="error">
                        <?php echo form_error('city'); ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <label for="zip">Zip Code<span>*</span></label>
                    <input type="text" class="form-control" id="zip" name="zip" placeholder="Zip Code" value="<?php echo @$_POST['zip'] ?>">
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

        <div class="form-group">
            <div class="row">
                <div class="col-md-6">
                    <label for="latitude">Latitude <span>*</span></label>
                    <input type="text" class="form-control" name="latitude"  id="latitude" placeholder="Latitude" value="<?php echo @$_POST['latitude'] ?>">
                    <div class="error">
                        <?php echo form_error('latitude'); ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <label for="longitude">Longitude <span>*</span></label>
                    <input type="text" class="form-control" name="longitude" id="longitude" placeholder="Longitude" value="<?php echo @$_POST['longitude'] ?>">
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
                    <input type="text" class="form-control" name="store_contact_person" placeholder="Store Contact Person" value="<?php echo @$_POST['store_contact_person'] ?>" maxlength="50">
                    <div class="error">
                        <?php echo form_error('store_contact_person'); ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <label for="store_contact_person">Store Contact Phone<span></span></label>
                    <input type="text" class="form-control" name="store_contact_tel" placeholder="Store Contact Telephone" value="<?php echo @$_POST['store_contact_tel'] ?>" maxlength="30">
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
                                    <input type="checkbox" value="<?php echo $key; ?>" checked name="open_days[]"/> <?php echo $val; ?>
                                </label>
                            </div>
                            <div class="input-group bootstrap-timepicker timepicker">
                                <input type="text" class="form-control timepicker picker" name="open_hours[]" placeholder="Open Time" value="From" Size="6">
                                <!--                            </div>
                                                            <div class="input-group bootstrap-timepicker timepicker">-->
                                <input type="text" class="form-control timepicker picker" name="open_hours[]" placeholder="Close Time" value="To" Size="6">
                            </div>
                        </div>
                        <?php
                        $i++;
                    endforeach;
                    ?>
                    <?php if ($i == '7') { ?>
                        <div class="col-md-6">&nbsp;</div>
                    <?php } ?>

                </div>
                <div class="col-md-12">
                    <div id="store_time_errors"></div>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="row">
                <div class="col-md-12">
                    <button type="submit" class="btn btn-primary btn-xs block full-width m-b">Add</button>
                    <a class="btn btn-danger btn-xs block full-width m-b" href="<?php echo site_url('/stores'); ?>">Cancel</a>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-5" id="map_div"></div>
</form>
</div>
<script>
    $(function(){
        activateMenu('admin/stores');
    });
</script>