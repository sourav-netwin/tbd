<!-- /.row -->

<div class="row">
    <?php echo form_open('content/edit_post/'.$content['Id'],array('id'=>'content_form', 'class' => 'form-horizontal','autocomplete'=>'off')); ?>
        <div class="col-md-10 col-md-offset-1">

        <div class="form-group">
            <div class="row">
                <div class="col-md-12">
                    <label for="menu_title">Menu Title</label>
                    <input type="text" class="form-control" name="menu_title" placeholder="Menu Title" value="<?php echo $content['MenuName']; ?>" readonly>
                </div>
            </div>
        </div>
        <div class="form-group">
                <div class="row">
                    <div class="col-md-6">
                        <label for="browser_title">Browser Title <span>*</span></label>
                        <input type="text" class="form-control" name="browser_title" placeholder="Browser Title" value="<?php echo $content_details['BrowserTitle']; ?>">
                        <div class="error">
                            <?php echo form_error('browser_title'); ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label for="page_title">Page Title <span>*</span></label>
                        <input type="text" class="form-control" name="page_title" placeholder="Page Title" value="<?php echo $content_details['PageTitle']; ?>">
                        <div class="error">
                            <?php echo form_error('page_title'); ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <div class="col-md-6">
                        <label for="meta_description">Meta Description<span>*</span></label>
                        <textarea class="form-control" name="meta_description" placeholder="Meta Description"><?php echo $content_details['MetaDescription']; ?></textarea>
                        <div class="error">
                            <?php echo form_error('meta_description'); ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label for="keywords">Keywords <span>*</span></label>
                        <textarea class="form-control" name="keywords" placeholder="Keywords"><?php echo $content_details['Keywords']; ?></textarea>
                        <div class="error">
                            <?php echo form_error('keywords'); ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <div class="col-md-12" style="width:99%;">
                        <label for="content">Content <span>*</span></label>
                        <textarea class="form-control" name="content" id="content" placeholder="Content"><?php echo $content_details['Content']; ?></textarea>
                        <div class="error">
                            <?php echo form_error('content'); ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="row">
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-primary block full-width m-b btn-xs">Update</button>
                        <!--<a class="btn btn-danger block full-width m-b btn-xs" href="<?php //echo site_url('/content'); ?>">Cancel</a>-->
                    </div>
                </div>
            </div>

        </div>
    </form>
</div>