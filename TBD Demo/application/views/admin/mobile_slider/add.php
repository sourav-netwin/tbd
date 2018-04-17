<!-- /.row -->

<div class="row">
    <?php echo form_open_multipart('mobileslider/add', array('id' => 'mobile_slider_form', 'class' => 'form-horizontal','autocomplete'=>'off')); ?>
    <div class="col-md-5 col-md-offset-1">

        <div class="form-group">
            <div class="row">
                <div class="col-md-12 mr-bt-45">
                    <div class="btn-group mobile_slider_image_group">
                        <label for="inputImage" class="btn btn-primary btn-xs mobile_slider_image">
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
                </div>
                <div class="form-group row">
                    <div class="col-xs-3">
                        <label>Text</label>
                    </div>
                    <div class="col-xs-9">
                        <input type="text" class="form-control" name="slider_text" />
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-xs-3">
                        <label>Text Color</label>
                    </div>
                    <div class="col-xs-9">
                        <input type="text" class="form-control color_pick" readonly="readonly" name="slider_color" />
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-xs-3">
                        <label>Text BG Color</label>
                    </div>
                    <div class="col-xs-9">
                        <input type="text" class="form-control color_pick" readonly="readonly" name="slider_bg_color" />
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group slider_buttons">
            <div class="row">
                <div class="col-md-12 col-md-offset-1">
                    <button type="submit" class="btn btn-primary btn-xs block full-width m-b">Add</button>
                    <a class="btn btn-danger btn-xs block full-width m-b" href="<?php echo site_url('/mobileslider'); ?>">Cancel</a>
                </div>
            </div>
        </div>

    </div>
</form>
</div>
<script>
$(function(){
   activateMenu('admin/mobileslider');
});
</script>