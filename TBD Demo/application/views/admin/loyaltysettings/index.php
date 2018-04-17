<?php echo form_open('loyaltysettings', array('id' => 'settings_form', 'class' => 'form-horizontal', 'autocomplete' => 'off')); ?>
<div class="row">
    <div class="col-sm-5 col-sm-offset-1">
        <div class="row form-group">
            <div class="col-sm-12">
                <label><b>Set loyalty points get to user after doing following operations</b></label>                
            </div>            
        </div>        
    </div>
</div>


<div class="row">
    <div class="col-sm-5 col-sm-offset-1">
        <div class="row form-group">
            <div class="col-sm-6">
                <label>App install</label>
                <input type="text" class="form-control" maxlength="3" id="app_install" name="app_install" value="<?php echo (int)$loyaltySettings['app_install']; ?>" />
                <div class="error"><?php echo form_error('app_install'); ?></div>
            </div>            
            
            <div class="col-sm-6">
                <label>Store Checkin</label>
                <input type="text" class="form-control" maxlength="3" id="store_checkin" name="store_checkin" value="<?php echo (int)$loyaltySettings['store_checkin']; ?>" />
                <div class="error"><?php echo form_error('store_checkin'); ?></div>
            </div>  
        </div>        
    </div>
    
    <div class="col-sm-5 ">
        <div class="row form-group">
            <div class="col-sm-6">
                <label>Product Reviews</label>
                <input type="text" class="form-control" maxlength="3" id="product_reviews" name="product_reviews" value="<?php echo (int)$loyaltySettings['product_reviews']; ?>"/>
                <div class="error"><?php echo form_error('product_reviews'); ?></div>
            </div>
            
            <div class="col-sm-6">
                <label>Referrer</label>
                <input type="text" class="form-control" maxlength="3" id="referrer" name="referrer" value="<?php echo (int)$loyaltySettings['referrer']; ?>" />
                <div class="error"><?php echo form_error('referrer'); ?></div>
            </div>
            
        </div>        
    </div>
    
</div>

<div class="row">
    <div class="col-sm-5 col-sm-offset-1">
        <div class="row form-group">
            <div class="col-sm-12">
                <label><b>App Shares</b></label>                
            </div>            
        </div>        
    </div>
</div>

<div class="row">
    <div class="col-sm-5 col-sm-offset-1">
        <div class="row form-group">
            <div class="col-sm-6">
                <label>Facebook</label>
                <input type="text" class="form-control" maxlength="3" id="app_shares_facebook" name="app_shares_facebook" value="<?php echo (int)$loyaltySettings['app_shares_facebook']; ?>" />
                <div class="error"><?php echo form_error('app_shares_facebook'); ?></div>
            </div>            
            
            <div class="col-sm-6">
                <label>Twitter</label>
                <input type="text" class="form-control" maxlength="3" id="app_shares_twitter" name="app_shares_twitter" value="<?php echo (int)$loyaltySettings['app_shares_twitter']; ?>" />
                <div class="error"><?php echo form_error('app_shares_twitter'); ?></div>
            </div>  
        </div>        
    </div>
    
    <div class="col-sm-5 ">
        <div class="row form-group">
            <div class="col-sm-6">
                <label>Email</label>
                <input type="text" class="form-control" maxlength="3" id="app_shares_email" name="app_shares_email" value="<?php echo (int)$loyaltySettings['app_shares_email']; ?>"/>
                <div class="error"><?php echo form_error('app_shares_email'); ?></div>
            </div>
            
            <div class="col-sm-6">
                <label>Google</label>
                <input type="text" class="form-control" maxlength="3" id="app_shares_google" name="app_shares_google" value="<?php echo (int)$loyaltySettings['app_shares_google']; ?>" />
                <div class="error"><?php echo form_error('app_shares_google'); ?></div>
            </div>
            
        </div>        
    </div>
    
</div>

<div class="row">
    <div class="col-sm-5 col-sm-offset-1">
        <div class="row form-group">
            <div class="col-sm-6">
                <label>WhatsApp</label>
                <input type="text" class="form-control" maxlength="3" id="app_shares_whatsApp" name="app_shares_whatsApp" value="<?php echo $loyaltySettings['app_shares_whatsApp']; ?>" />
                <div class="error"><?php echo form_error('app_shares_whatsApp'); ?></div>
            </div>
        </div>        
    </div>
</div>

<div class="row">
    <div class="col-sm-5 col-sm-offset-1">
        <div class="row form-group">
            <div class="col-sm-12">
                <label><b>Product Shares</b></label>                
            </div>            
        </div>        
    </div>
</div>

<div class="row">
    <div class="col-sm-5 col-sm-offset-1">
        <div class="row form-group">
            <div class="col-sm-6">
                <label>Facebook</label>
                <input type="text" class="form-control" maxlength="3" id="product_shares_facebook" name="product_shares_facebook" value="<?php echo (int)$loyaltySettings['product_shares_facebook']; ?>" />
                <div class="error"><?php echo form_error('product_shares_facebook'); ?></div>
            </div>            
            
            <div class="col-sm-6">
                <label>Twitter</label>
                <input type="text" class="form-control" maxlength="3" id="product_shares_twitter" name="product_shares_twitter" value="<?php echo (int)$loyaltySettings['product_shares_twitter']; ?>" />
                <div class="error"><?php echo form_error('product_shares_twitter'); ?></div>
            </div>  
        </div>        
    </div>
    
    <div class="col-sm-5 ">
        <div class="row form-group">
            <div class="col-sm-6">
                <label>Email</label>
                <input type="text" class="form-control" maxlength="3" id="product_shares_email" name="product_shares_email" value="<?php echo (int)$loyaltySettings['product_shares_email']; ?>"/>
                <div class="error"><?php echo form_error('product_shares_email'); ?></div>
            </div>
            
            <div class="col-sm-6">
                <label>Google</label>
                <input type="text" class="form-control" maxlength="3" id="product_shares_google" name="product_shares_google" value="<?php echo (int)$loyaltySettings['product_shares_google']; ?>" />
                <div class="error"><?php echo form_error('product_shares_google'); ?></div>
            </div>
            
        </div>        
    </div>
    
</div>

<div class="row">
    <div class="col-sm-5 col-sm-offset-1">
        <div class="row form-group">
            <div class="col-sm-6">
                <label>WhatsApp</label>
                <input type="text" class="form-control" maxlength="3" id="product_shares_whatsApp" name="product_shares_whatsApp" value="<?php echo $loyaltySettings['product_shares_whatsApp']; ?>" />
                <div class="error"><?php echo form_error('product_shares_whatsApp'); ?></div>
            </div>
        </div>        
    </div>
</div>



<div class="row">
    <div class=" col-sm-offset-1 col-sm-10">
        <input type="submit" value="Update" class="btn btn-primary btn-xs" />
        <input type="hidden" name="id" id="id" value="<?php echo $loyaltySettings['Id']; ?>">
    </div> 
</div>
</form>