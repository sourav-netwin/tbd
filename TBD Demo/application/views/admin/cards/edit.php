<!-- /.row -->

<div class="row">
    <?php echo form_open_multipart('cards/edit_post/' . $Id, array('id' => 'cards_form', 'class' => 'form-horizontal', 'autocomplete' => 'off')); ?>

    <?php
    $image_path = front_url() . CARDS_IMAGE_PATH . '/' . $CardImage;
    $style = ( $CardImage != '' && file_exists('./' . CARDS_IMAGE_PATH . '/' . $CardImage) ) ? 'style="margin-bottom: 40px;background-image: url(' . $image_path . ') "' : '';
    ?>

    <div class="col-md-12 col-sm-12">
        
        <div class="col-md-4 col-sm-12">
            <div class="col-xs-10 col-xs-offset-1">
                <div class="btn-group mobile_slider_image_group">
                    <label for="inputImage" class="btn btn-primary btn-xs mobile_slider_image" <?php echo $style; ?>>
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
                <input type="hidden" name="old_photo" id="old_photo" value="<?php echo $CardImage ?>">
            </div>
        </div>
        
        <div class="col-md-8 col-sm-12">
                    <div class="form-group row">
                <div class="col-xs-3 col-xs-offset-1">
                    <label>Title</label>
                </div>
                <div class="col-xs-7">
                    <input type="text" class="form-control" name="CardTitle" value="<?php echo $CardTitle ?>" />
                </div>
            </div>
            <div class="form-group row">
                <div class="col-xs-3 col-xs-offset-1">
                    <label>Description</label>
                </div>
                <div class="col-xs-7">
                    <textarea class="form-control" name="CardDescription" placeholder="Card Description" rows="5"><?php echo str_replace("\'", "'", $CardDescription);  ?></textarea>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-12 text-center">
        <button type="submit" class="btn btn-primary btn-xs block full-width m-b">Update</button>
    </div>
    <!-- Add Modal -->

</form>
</div>
