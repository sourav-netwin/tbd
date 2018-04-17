<style>
    .table > tbody > tr > td, .table > tbody > tr > th, .table > tfoot > tr > td,.table > tfoot > tr > th, .table > thead > tr > td, .table > thead > tr > th{ 
        border-top: 0px solid #f4f4f4;
        border-collapse: none;
    }
    .table tr:last-child td {
        border-bottom: 0px solid #dfdfdf;
    }
    .inner-spacing{ padding-left:50px; padding-right:10px;}
</style>


<?php echo form_open('', array('id' => 'notification_form', 'class' => 'form-horizontal', 'autocomplete' => 'off')); ?>
<div class="row">
    <div class="col-sm-5">
        <div class="row">
            <div class="col-xs-12 text-center" style="height:23px;">
                
            </div> 
        </div>
        <div class="row form-group">
            <div class="col-sm-12">
                <label>Subject</label>
                <input type="text" class="form-control" maxlength="50" id="notif_subject" name="notif_subject" />
            </div>            
        </div>
        <div class="row form-group">
            <div class="col-sm-12">
                <label>Content</label>
                <textarea class="form-control" maxlength="200" id="notif_conent" name="notif_content" rows="6.5"></textarea>
            </div>            
        </div>
        
        <div class="row">
            <div class="col-sm-12">
                <input type="submit" value="Send" class="btn btn-primary btn-xs" />
            </div> 
        </div>
        
    </div>
    
    <div class="col-sm-7">
        <div class="row">
            <div class="col-xs-12 text-center">
                <strong><h4>Filters</h4></strong>
            </div> 
        </div>
        
        <div class="row">
            <div class="col-xs-12">
                <table class="table table-bordered123" id="notif-filter-table" cellpadding="5" cellspacing="5" >
                    <tbody>
                        <tr>
                            <th width="18%">Region</th>
                            <td >
                                <select id="sel_region" name="sel_region">
                                    <option value="">Select Region</option>
                                    <option value="all">All</option>
                                    <?php
                                    if ($states) {
                                        foreach ($states as $state) {
                                            ?>
                                            <option value="<?php echo $state['Id'] ?>"><?php echo $state['Name'] ?></option>
                                            <?php
                                        }
                                    }
                                    ?>
                                </select>
                            </td>
                        </tr>

                        <tr>
                            <th >Retailer</th>
                            <td >
                                <select id="sel_retailer" name="sel_retailer">
                                    <option value="">Select Retailer</option>
                                </select>
                            </td>
                        </tr>


                        <tr>
                            <th >Store Format</th>
                            <td >
                                <select id="sel_storetype" name="sel_storetype">
                                    <option value="">Select Store Format</option>
                                </select>
                            </td>
                        </tr>
                        
                        
                        <tr>
                            <th >Gender</th>
                            <td id="chkArea"><input type="checkbox" class="icheck-minimal-check notif_check" id="notif_male" name="notif_male" /> Male
                                &nbsp;&nbsp;
                                <input type="checkbox" class="icheck-minimal-check notif_check" id="notif_female" name="notif_female" on/> Female

                                
                                <span class="inner-spacing"> <strong>Device</strong> </span>
                                
                                <input type="checkbox" class="icheck-minimal-check notif_check" id="notif_android" name="notif_android" /> Android
                                &nbsp;&nbsp;
                                <input type="checkbox" class="icheck-minimal-check notif_check" id="notif_iphone" name="notif_iphone" /> IPhone
                                </td>
                        </tr>

                        <?php /* ?>
                        <tr>
                            <th >Gender</th>
                            <td><input type="checkbox" class="icheck-minimal-check notif_check" id="notif_male" name="notif_male" /> Male
                                &nbsp;&nbsp;
                                <input type="checkbox" class="icheck-minimal-check notif_check" id="notif_female" name="notif_female" /> Female</td>

                        </tr>

                        <tr>
                            <th >Device</th>
                            <td>
                                <input type="checkbox" class="icheck-minimal-check notif_check" id="notif_android" name="notif_android" /> Android
                                &nbsp;&nbsp;
                                <input type="checkbox" class="icheck-minimal-check notif_check" id="notif_iphone" name="notif_iphone" /> IPhone
                            </td>
                        </tr>
                         <?php */ ?>
                        
                        <tr>
                            <th >Users</th>
                            <td >
                                <select id="sel_user" name="sel_user">
                                    <option value="">Select User</option>
                                    <?php
                                    if ($users) {
                                        foreach ($users as $user) {
                                            
                                            $showUserInfo = $user['FullName'];
                                            $showUserInfo = $user['Email']!="" ? $showUserInfo." - ".$user['Email'] : $showUserInfo;
                                            $showUserInfo = $user['Mobile']!="" ? $showUserInfo." - ".$user['Mobile'] : $showUserInfo;
                                            
                                            ?>
                                            <option value="<?php echo $user['Id'] ?>"><?php echo $showUserInfo; ?></option>
                                            <?php
                                        }
                                    }
                                    ?>
                                </select>
                            </td>
                        </tr>

                    </tbody>
                </table>
            </div>
          </div>
    </div>
</div>

<?php /* ?>
<div class="row">
    <div class="col-xs-12 text-center">
        <strong><h4>Filters</h4></strong>
    </div> 
</div>
<div class="row">
    <div class="col-xs-12">
        <table class="table table-bordered" id="notif-filter-table">
            <tbody>
                <tr>
                    <th >Region</th>
                    <th >Store Format</th>
                    <th colspan="2">
                        Gender
                    </th>
                    <th colspan="2">
                        Device
                    </th>
                </tr>
                <tr>
                    <td >
                        <select id="sel_region" name="sel_region">
                            <option value="">Select Region</option>
                            <?php
                            if ($states) {
                                foreach ($states as $state) {
                                    ?>
                                    <option value="<?php echo $state['Id'] ?>"><?php echo $state['Name'] ?></option>
                                    <?php
                                }
                            }
                            ?>
                        </select>
                    </td>
                    <td >
                        <select id="sel_storetype" name="sel_storetype">
                            <option value="">Select Store Format</option>
                        </select>
                    </td>
                    <td><input type="checkbox" class="icheck-minimal-check notif_check" id="notif_male" name="notif_male" /> Male</td>
                    <td><input type="checkbox" class="icheck-minimal-check notif_check" id="notif_female" name="notif_female" /> Female</td>
                    <td><input type="checkbox" class="icheck-minimal-check notif_check" id="notif_android" name="notif_android" /> Android</td>
                    <td><input type="checkbox" class="icheck-minimal-check notif_check" id="notif_iphone" name="notif_iphone" /> IPhone</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>


<div class="row">
    <div class=" col-sm-offset-1 col-sm-10">
        <input type="submit" value="Send" class="btn btn-primary btn-xs" />
    </div> 
</div>
<?php */ ?>

</form>