<!-- /.row -->

<div class="row">
    <?php echo form_open_multipart('mobileslider/edit_post/' . $Id, array('id' => 'mobile_slider_form', 'class' => 'form-horizontal', 'autocomplete' => 'off')); ?>

    <?php
    $image_path = front_url() . MOBILE_SLIDER_IMAGE_PATH . '/' . $Image;
    $style = ( $Image != '' && file_exists('./' . MOBILE_SLIDER_IMAGE_PATH . '/' . $Image) ) ? 'style="margin-bottom: 40px;background-image: url(' . $image_path . ') "' : '';
    ?>

    <div class="col-xs-10 col-xs-offset-1">
        <div class="btn-group mobile_slider_image_group">
            <label for="inputImage" class="btn btn-primary btn-xs mobile_slider_image" <?php echo $style; ?>>
                <span title="Upload Image" style="background: rgba(254, 254, 254, 0.7) url('<?php echo base_url() ?>../assets/admin/img/upload.png') no-repeat scroll center center / 25px 25px;">
                    <input type="file" accept="image/*" name="mobile_slider_image" class="hide" id="inputImage" value="">
                </span>
                <!--<div id="image_text">Upload Slider Image</div>-->
                <div class="info_text"><em>Please upload a 540*882 image</em></div>
                <div class="error" style="line-height: 0px;">
                    <?php echo form_error('mobile_slider_image'); ?>
                </div>
            </label>
        </div>
        <input type="hidden" name="old_photo" id="old_photo" value="<?php echo $Image ?>">
    </div>
    <div class="form-group row">
        <div class="col-xs-3 col-xs-offset-1">
            <label>Text</label>
        </div>
        <div class="col-xs-7">
            <input type="text" class="form-control" name="slider_text" value="<?php echo $Text ?>" />
        </div>
    </div>
    <div class="form-group row">
        <div class="col-xs-3 col-xs-offset-1">
            <label>Text Color</label>
        </div>
        <div class="col-xs-7">
            <input type="text" class="form-control color_pick" readonly="readonly" name="slider_color" value="<?php echo $Color ?>" />
        </div>
    </div>
    <div class="form-group row">
        <div class="col-xs-3 col-xs-offset-1">
            <label>Text BG Color</label>
        </div>
        <div class="col-xs-7">
            <input type="text" class="form-control color_pick" readonly="readonly" name="slider_bg_color" value="<?php echo $BgColor ?>" />
        </div>
    </div>

    <div class="col-md-12 text-center">
        <button type="submit" class="btn btn-primary btn-xs block full-width m-b">Update</button>
    </div>


    <!-- Add Modal -->


</form>
</div>
