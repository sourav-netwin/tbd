<!-- /.row -->

<div class="row">
    <?php  if ($step == 'new') { ?>
     <div class="col-md-12 welcome_header">
        <p>
            <button type="button" class="btn btn-primary btn-xs block full-width m-b">STEP 1</button> Please validate your store information before we proceed.
        </p>
    </div>
    <?php } ?>
    <?php echo form_open('home/store/'.$step , array('id' => 'stores_form', 'class' => 'form-horizontal')); ?>
    <div class="col-md-5 col-md-offset-1">

        <div class="form-group">
            <div class="row">
                <div class="col-md-4">
                    <label for="store_id">Store Id<span>*</span></label>
                    <input type="text" class="form-control" name="store_id" placeholder="Store Id" value="<?php echo $StoreId ?>">
                    <div class="error">
                        <?php echo form_error('store_id'); ?>
                    </div>
                </div>
                <div class="col-md-8">
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
                <div class="col-md-8">
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
                <div class="col-md-12">
                    <label for="store_email">Store Email <span>*</span></label>
                    <input type="text" class="form-control" name="store_email" placeholder="Store Email" value="<?php echo $store_email ?>" disabled="">
                    <div class="error">
                        <?php echo form_error('store_email'); ?>
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

        <div class="form-group">
            <div class="row">
                <div class="col-md-6">
                    <label for="latitude">Latitude <span>*</span></label>
                    <input type="text" class="form-control" name="latitude"  id="latitude" readonly placeholder="Latitude" value="<?php echo $Latitude ?>">
                    <div class="error">
                        <?php echo form_error('latitude'); ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <label for="longitude">Longitude <span>*</span></label>
                    <input type="text" class="form-control" name="longitude" id="longitude" readonly placeholder="Longitude" value="<?php echo $Longitude ?>">
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
                            <?php
                             $from = "";
                              $to ="";
                            if ($store_timings[($key - 1)]['OpenCloseStatus'] == '1') {
                                $time = explode('-', $store_timings[($key - 1)]['OpenCloseTimeFrom']);
                                if ($time['0'])
                                    $from = $time['0'];
                                if ($time['1'])
                                    $to = $time['1'];
                            } else {
                                   $from = "";
                              $to ="";
                            }
                            ?>
                            <div class="input-group bootstrap-timepicker timepicker">
                                <input type="text" class="form-control timepicker picker" name="open_hours[]" placeholder="From" value="<?php echo $from ?>" Size="6">
                                <input type="text" class="form-control timepicker picker" name="open_hours[]" placeholder="To" value="<?php echo $to ?>" Size="6">
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

        <div class="form-group">
            <div class="row">
                <div class="col-md-12">
                    <button type="submit" class="btn btn-primary btn-xs block full-width m-b">Update</button>
                    <?php  if ($step == 'new') { ?>
                    <a class="btn btn-danger btn-xs block full-width m-b" href="<?php echo site_url('/home/store/next'); ?>">Next</a>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-5" id="map_div"></div>
</form>
</div>