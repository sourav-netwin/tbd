<!-- /.row -->
<div class="row">
    <?php echo form_open_multipart('stores/add_store_user', array('id' => 'storeformat_user_form', 'class' => 'form-horizontal',' autocomplete'=>'off')); ?>

    <div class="col-md-5 col-md-offset-1">

        <div class="form-group">
            <div class="row">
                <div class="col-md-12">
                    <label for="store">Store <span>*</span></label>
                    <select class="form-control select-filter" id="store" name="store">
                        <?php
                        if (!empty($stores)) :
                            foreach ($stores as $store) {
                                $store_id == $store['Id'] ? $selected = "selected=selected" : $selected ='' ;
                                
                                echo "<option ".$selected."  value='" . $store['Id'] . " '>" . $store['StoreName'] . "</option>";
                            }
                        endif;
                        ?>
                    </select>
                    <div class="error">
                        <?php echo form_error('store'); ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="row">
                <div class="col-md-6">
                    <label for="first_name" > First Name <span>*</span></label>
                    <input type="text" class="form-control" name="first_name" placeholder="First Name" value="<?php echo set_value('first_name'); ?>">
                    <div class="error">
                        <?php echo form_error('first_name'); ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <label  for="last_name">Last Name  <span>*</span></label>
                    <input type="text" class="form-control" name="last_name" placeholder="Last Name" value="<?php echo set_value('last_name'); ?>">
                    <div class="error">
                        <?php echo form_error('last_name'); ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="row">
                <div class="col-md-12">
                    <label for="email" >Email Address <span>*</span></label>
                    <input type="email" class="form-control" name="email" placeholder="Email Address" value="">
                    <div class="error">
                        <?php echo form_error('email'); ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="row">
                <div class="col-md-6">
                    <label for="password">New Password <span>*</span></label>
                    <input type="password" name="password" id="password" class="form-control" placeholder="New Password">
                    <div class="error">
                        <?php echo form_error('password'); ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <label for="confirm_password">Confirm Password <span>*</span></label>
                    <input type="password" name="confirm_password" class="form-control" placeholder="Confirm Password">
                    <div class="error">
                        <?php echo form_error('confirm_password'); ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="row">
                <div class="col-md-6">
                    <label for="contact_tel">Contact Telephone<span></span></label>
                    <input type="text" class="form-control" name="contact_tel" placeholder="Contact Telephone" value="<?php echo @$_POST['contact_tel'] ?>" maxlength="30">
                    <div class="error">
                        <?php echo form_error('contact_tel'); ?>
                    </div>
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
</form>
</div>
<script>
$(function(){
   activateMenu('admin/stores');
});
</script>