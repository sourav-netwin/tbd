<section class="one-section">
	<div class="container">
        <h2 class="underline">Set Email</h2>
        <?php echo form_open('', array('id' => 'set_email_form', 'class' => 'form-horizontal custom_form mtop-40')); ?>
            <div class="form-group">
                <label for="" class="col-sm-3 col-md-2 control-label">Email ID<span class="text-danger">*</span></label>
                <div class="col-sm-6 col-md-4">
                    <input type="text" class="form-control" id="email" name="email" placeholder="example@domain.com">
                    <div class="error">
                        <?php echo form_error('email'); ?>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-6">
                    <input type="submit" class="btn btn-login" value="Submit" data-loading-text="Submitting...">
                </div>
            </div>
        </form>
    </div>
</section>