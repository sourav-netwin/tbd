<!-- /.row -->

<div class="row">
    <?php echo form_open_multipart('loyaltyterms/add', array('id' => 'tandc_form', 'class' => 'form-horizontal',' autocomplete'=>'off')); ?>
    <div class="col-md-5 col-md-offset-1">

        <div class="form-group">
            <div class="row">
                <div class="col-md-12">
                    <label for="terms_text">Terms and Conditions <span>*</span></label>
                    <textarea id="terms_text" name="terms_text" maxlength="300" style="resize: none; height: 100px" class="form-control"></textarea>
                </div>
            </div>
        </div>

        

        <div class="form-group">
            <div class="row">
                <div class="col-md-12">
                    <button type="submit" class="btn btn-primary btn-xs block full-width m-b">Add</button>
                    <a class="btn btn-danger btn-xs block full-width m-b" href="<?php echo site_url('/loyaltyterms'); ?>">Cancel</a>
                </div>
            </div>
        </div>
    </div>
</div>
</form>
<script>
$(function(){
   activateMenu('admin/loyaltyterms');
});
</script>