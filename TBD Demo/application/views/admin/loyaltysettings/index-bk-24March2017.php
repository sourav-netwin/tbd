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
            <div class="col-sm-12">
                <label>Product Reviews</label>
                <input type="text" class="form-control" maxlength="3" id="product_reviews" name="product_reviews" value="<?php echo $loyaltySettings['product_reviews']; ?>"/>
                <div class="error"><?php echo form_error('product_reviews'); ?></div>
            </div> 
            
        </div>        
    </div>
</div>


<div class="row">
    <div class="col-sm-5 col-sm-offset-1">
        <div class="row form-group">
            <div class="col-sm-12">
                <label>Product Shares</label>
                <input type="text" class="form-control" maxlength="3" id="product_shares" name="product_shares" value="<?php echo $loyaltySettings['product_shares']; ?>"/>
                <div class="error"><?php echo form_error('product_shares'); ?></div>
            </div>            
        </div>        
    </div>
</div>


<div class="row">
    <div class="col-sm-5 col-sm-offset-1">
        <div class="row form-group">
            <div class="col-sm-12">
                <label>App shares</label>
                <input type="text" class="form-control" maxlength="3" id="app_shares" name="app_shares" value="<?php echo $loyaltySettings['app_shares']; ?>" />
                <div class="error"><?php echo form_error('app_shares'); ?></div>
            </div>            
        </div>        
    </div>
</div>

<div class="row">
    <div class="col-sm-5 col-sm-offset-1">
        <div class="row form-group">
            <div class="col-sm-12">
                <label>Referrer</label>
                <input type="text" class="form-control" maxlength="3" id="referrer" name="referrer" value="<?php echo $loyaltySettings['referrer']; ?>" />
                <div class="error"><?php echo form_error('referrer'); ?></div>
            </div>            
        </div>        
    </div>
</div>

<div class="row">
    <div class="col-sm-5 col-sm-offset-1">
        <div class="row form-group">
            <div class="col-sm-12">
                <label>App install</label>
                <input type="text" class="form-control" maxlength="3" id="app_install" name="app_install" value="<?php echo $loyaltySettings['app_install']; ?>" />
                <div class="error"><?php echo form_error('app_install'); ?></div>
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