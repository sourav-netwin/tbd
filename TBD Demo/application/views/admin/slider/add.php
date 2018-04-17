<!-- /.row -->

<div class="row">
    <?php echo form_open_multipart('slider/add', array('id' => 'slider_form', 'class' => 'form-horizontal','autocomplete'=>'off')); ?>
    <div class="col-md-5 col-md-offset-1">

        <div class="form-group">
            <div class="row">
                <div class="col-md-12">
                    <div class="btn-group slider_image_group">
                        <label for="inputImage" class="btn btn-primary btn-xs slider_image">
                            <span title="Upload Image" style="background: rgba(254, 254, 254, 0.7) url('<?php echo base_url() ?>../assets/admin/img/upload.png') no-repeat scroll center center / 25px 25px;">
                                <input type="file" accept="image/*" name="slider_image" class="hide" id="inputImage" value="">
                            </span>
                            <!--<div id="image_text">Upload Slider Image</div>-->
                            <div class="info_text"><em>Please upload a 1530*649 image</em></div>
                            <div class="error" style="line-height: 0px;">
                                <?php echo form_error('slider_image'); ?>
                            </div>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group slider_buttons">
            <div class="row">
                <div class="col-md-12 col-md-offset-1">
                    <button type="submit" class="btn btn-primary btn-xs block full-width m-b">Add</button>
                    <a class="btn btn-danger btn-xs block full-width m-b" href="<?php echo site_url('/slider'); ?>">Cancel</a>
                </div>
            </div>
        </div>

    </div>
</form>
</div>