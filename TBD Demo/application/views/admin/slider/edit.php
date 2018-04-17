<!-- /.row -->

<div class="row">
    <?php echo form_open_multipart('slider/edit_post/' . $Id, array('id' => 'slider_form', 'class' => 'form-horizontal','autocomplete'=>'off')); ?>

    <?php
    $image_path = front_url() . SLIDER_IMAGE_PATH . '/' . $Image;
    $style = ( $Image != '' && file_exists('./' . SLIDER_IMAGE_PATH . '/' . $Image) ) ? 'style="margin-bottom: 40px;background-image: url(' . $image_path . ') "' : '';
    ?>

    <div class="col-md-12">
        <div class="btn-group slider_image_group">
            <label for="inputImage" class="btn btn-primary btn-xs slider_image" <?php echo $style; ?>>
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
        <input type="hidden" name="old_photo" id="old_photo" value="<?php echo $Image ?>">
    </div>

    <div class="col-md-12 text-center">
        <button type="submit" class="btn btn-primary btn-xs block full-width m-b">Update</button>
    </div>


    <!-- Add Modal -->


</form>
</div>
