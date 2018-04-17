<!-- /.row -->

<div class="row">
    <?php echo form_open_multipart('account/profile', array('id' => 'user_change_password', 'class' => 'form-horizontal')); ?>
        <div class="col-md-5 col-md-offset-1">
            <div class="form-group">
                <div class="row">
                    <div class="col-md-6">
                        <label for="first_name" >Name <span>*</span></label>
                        <input type="text" class="form-control" name="first_name" placeholder="First Name" value="<?php echo $FirstName; ?>" maxlength="50">
                        <div class="error">
                            <?php echo form_error('first_name'); ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label>&nbsp;</label>
                        <input type="text" class="form-control" name="last_name" placeholder="Last Name" value="<?php echo $LastName; ?>" maxlength="50">
                        <div class="error">
                            <?php echo form_error('last_name'); ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="row">
                    <div class="col-md-12">
                        <label for="email" >Email Address <span>*</span></label>
                        <input type="email" class="form-control" name="email" placeholder="Email Address" value="<?php echo $Email; ?>" readonly>
                        <div class="error">
                            <?php echo form_error('email'); ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="row">
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-primary btn-xs block full-width m-b">Update</button>
                        <a class="btn btn-danger btn-xs block full-width m-b" href="<?php echo site_url('/home/dashboard'); ?>">Cancel</a>
                    </div>
                </div>
            </div>

            <div class="form-group">&nbsp;</div>

        </div>
        <?php
            $image_path = front_url().USER_IMAGE_PATH.'/medium/'.$ProfileImage;
            $style = ( $ProfileImage != '' && file_exists( './'.USER_IMAGE_PATH.'/medium/'.$ProfileImage ) ) ? 'style="background-image: url('.$image_path.')"' : '' ;
        ?>
        <div class="col-md-3">
            <div class="btn-group profile_image_group">
                <label for="inputImage" class="btn btn-primary btn-xs profile_image" <?php echo $style ?>>
                    <input type="file" accept="image/*" name="profile_image" class="hide" id="inputImage">
                    <div id="image_text">Upload Profile Image</div>
                </label>
            </div>
            <div class="error">
                <?php echo form_error('profile_image'); ?>
            </div>
            <input type="hidden" name="old_photo" id="old_photo" value="<?php echo $ProfileImage ?>">
        </div>

      <!-- Add Modal -->
    <div class="modal fade" id="imageModal" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                     <h4 class="modal-title" id="myModalLabel"> <span id="form-action">Crop Image</span></h4>
                </div>
                <div class="modal-body">
                    <div class="image-crop">
                        <img src="<?php echo $this->config->item('admin_assets'); ?>img/product.jpg">
                    </div>
                    <div><a id="crop-button" class="btn btn-primary btn-xs block full-width m-b">Crop</a></div>
                    <input type="hidden" name="image-x" id="image-x">
                    <input type="hidden" name="image-y" id="image-y">
                    <input type="hidden" name="image-width" id="image-width">
                    <input type="hidden" name="image-height" id="image-height">

                </div>
            </div>
        </div>
    </div>
    </form>
</div>