<!-- /.row -->
<?php echo validation_errors(); ?>
<div class="row">
    <div class="col-lg-10">
        <?php echo form_open('retailers/assign_category/'.$RetailerId, array('id' => 'assign_category_form', 'class' => 'form-horizontal','autocomplete'=>'off')); ?>

        <div class="form-group">
            <label for="category" class="col-sm-3 control-label">Category <span>*</span></label>
            <div class="col-sm-8">
                <?php if( !empty ( $categories ) ) { ?>
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="category[]" id="all_category" value=""> All Categories
                            </label>
                        </div>
                <?php foreach ($categories as $category) { ?>
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="category[]" id="category" value="<?php echo $category['Id']?>" <?php echo ( is_array($retailer_assigned_categories) && in_array($category['Id'], $retailer_assigned_categories) ) ? "checked" : ""; ?>> <?php echo $category['CategoryName']?>
                            </label>
                        </div> 
                <?php }
                } ?>
                <div class="error">
                    <?php echo form_error('category'); ?>
                </div>
            </div>
        </div>
        <div class="form-group">
            <label for="category" class="col-sm-3 control-label"></label>
            <div class="col-sm-8">
                <div id="category_error"></div>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-3 control-label"></label>
            <div class="col-sm-8">
                <button type="submit" class="btn btn-primary btn-xs block full-width m-b">Save</button>
                <a class="btn btn-primary btn-xs block full-width m-b" href="<?php echo site_url('/retailers'); ?>">Cancel</a>
            </div>
        </div>
        </form>
    </div>
</div>