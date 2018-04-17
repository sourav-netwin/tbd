<!-- /.row -->

<div class="row">
    <?php echo form_open('emailtemplate/edit_post/'.$Id,array('id'=>'email_template_form', 'class' => 'form-horizontal','autocomplete'=>'off')); ?>
        <div class="col-md-10 col-md-offset-1">

            <div class="form-group">
                <div class="row">
                    <div class="col-md-6">
                        <label for="email_from">Email From <span>*</span></label>
                        <input type="text" class="form-control" name="email_from" placeholder="Email From" value="<?php echo $FromEmail; ?>">
                        <div class="error">
                            <?php echo form_error('email_from'); ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label for="title">Title <span>*</span></label>
                        <input type="text" class="form-control" name="title" placeholder="Title" value="<?php echo $heading; ?>">
                        <div class="error">
                            <?php echo form_error('title'); ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="row">
                   <?php
                   if($EmailTo=='Admin') { ?>
                    <div class="col-md-6">
                        <label for="email_to">Email Address of <span>*</span></label>
                        <input type="text" class="form-control" name="email_to" placeholder="Browser Title" value="<?php echo $ToEmail; ?>">
                        <div class="error">
                            <?php echo form_error('email_to'); ?>
                        </div>
                    </div>
                    <input type="hidden" name="email_is" value="1" />
                    <?php }
                    else{
                        ?>
                    <input type="hidden" name="email_is" value="0" />
                    <?php
                    }?>
                    <div class="col-md-6">
                        <label for="page_title">To </label>
                        <input type="text" class="form-control" name="to" placeholder="To" value="<?php echo $EmailTo; ?>" readonly>
                    </div>
                </div>
            </div>

<!--            <div class="form-group">
                <div class="row">
                    <div class="col-md-12">
                        <label for="title">Title <span>*</span></label>
                        <input type="text" class="form-control" name="title" placeholder="Title" value="<?php //echo $heading; ?>">
                        <div class="error">
                            <?php //echo form_error('title'); ?>
                        </div>
                    </div>
                </div>
            </div>-->

            <div class="form-group">
                <div class="row">
                    <div class="col-md-12">
                        <label for="content">Content <span>*</span></label>
                        <textarea class="form-control" name="content" id="content" placeholder="Content"><?php echo $Content; ?></textarea>
                        <div class="error">
                            <?php echo form_error('content'); ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="row">
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-primary btn-xs block full-width m-b">Update</button>
                        <!--<a class="btn btn-danger btn-xs block full-width m-b" href="<?php //echo site_url('/emailtemplate'); ?>">Cancel</a>-->
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>