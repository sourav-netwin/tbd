<!-- /.row -->
<style>
    .cards_buttons {
        margin-left: 27% !important;
    }
</style>
<div class="row">
    <?php echo form_open_multipart('cards/add', array('id' => 'cards_form', 'class' => 'form-horizontal','autocomplete'=>'off')); ?>
    <div class="col-md-6 col-md-offset-1">

        <div class="form-group">
            <div class="row">
                <div class="col-md-12 mr-bt-45">
                    <div class="btn-group mobile_slider_image_group">
                        <label for="inputImage" class="btn btn-primary btn-xs mobile_slider_image">
                            <span title="Upload Image" style="background: rgba(254, 254, 254, 0.7) url('<?php echo base_url() ?>../assets/admin/img/upload.png') no-repeat scroll center center / 25px 25px;">
                                <input type="file" accept="image/*" name="card_image" class="hide" id="inputImage" value="">
                            </span>
                            <!--<div id="image_text">Upload Slider Image</div>-->
                            <div class="info_text"><em>Please upload a 220*360 image</em></div>
                            <div class="error" style="line-height: 0px;">
                                <?php echo form_error('card_image'); ?>
                            </div>
                        </label>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-xs-3">
                        <label for="CardTitle">Title <span>*</span></label>
                    </div>
                    <div class="col-xs-9">
                       <input type="text" class="form-control" name="CardTitle" placeholder="Card Title" value="<?php echo set_value('CardTitle'); ?>">
                       <div class="error">
                        <?php echo form_error('CardTitle'); ?>
                       </div>
                    </div>
                </div>
                
                <div class="form-group row">
                    <div class="col-xs-3">
                        <label for="CardDescription">Description <span>*</span></label>
                    </div>
                    <div class="col-xs-9">
                        <textarea class="form-control" name="CardDescription" placeholder="Description" rows="2"><?php echo set_value('CardDescription'); ?></textarea>
                        <div class="error">
                                <?php echo form_error('AdvertisementDescription'); ?>
                            </div>
                    </div>
                </div>
                
            </div>
        </div>

        <div class="form-group cards_buttons">
            <div class="row">
                <div class="col-md-12 col-md-offset-1">
                    <button type="submit" class="btn btn-primary btn-xs block full-width m-b">Add</button>
                    <a class="btn btn-danger btn-xs block full-width m-b" href="<?php echo site_url('/cards'); ?>">Cancel</a>
                </div>
            </div>
        </div>

    </div>
</form>
</div>
<script>
$(function(){
   activateMenu('admin/cards');
});
</script>