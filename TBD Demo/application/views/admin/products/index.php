<style>
    .select2{
        width: 145px !important;
    }
    .center-aligned-column {
        text-align: center !important;
    }
    .label-warning {
        background-color: #FFCC99 !important; /* #FFB97C, #FFA5A5 */
    }
</style>
<!-- /.row -->
<div class="row search_filter_container">
    <div class="col-lg-12">
        <div class="col-xs-9 col-sm-9 col-md-9">
            <div class="form-group pull-left">
                <label for="search">SEARCH</label>
                <input type="text" class="form-control search_small" id="search_filter" name="search_filter" style="width: 200px !important">
            </div>
            <?php if (!empty($main_categories)) { ?>
                <select class="form-control search_product_select select-filter" id="product_main_category" name="product_main_category">
                    
                    <option value="0">Select Main Category</option>
                    <?php foreach ($main_categories as $category) { ?>
                        <option value="<?php echo $category['Id']; ?>"><?php echo $category['CategoryName']; ?></option>
                    <?php } ?>
                </select>
            <?php } ?>
            <select class="form-control select-filter search_product_select" id="product_parent_category" name="product_parent_category">
                <option value="0">Select Category</option>
                <?php if (!empty($parent_category)) {
                    foreach ($parent_category as $category) {
                        ?>
                        <option value="<?php echo $category['Id']; ?>"><?php echo $category['CategoryName']; ?></option>
                    <?php }
                }
                ?>
            </select>
            <select class="form-control select-filter search_product_select" id="product_sub_category" name="product_sub_category">
                <option value="0">Select Sub Category</option>
                <?php if (!empty($sub_category)) {
                    foreach ($sub_category as $category) {
                        ?>
                        <option value="<?php echo $category['Id']; ?>"><?php echo $category['CategoryName']; ?></option>
    <?php }
}
?>
            </select>
        </div>
        <div class="col-xs-3 col-sm-3 col-md-3 text-right"> 
            <a class="btn btn-primary btn-xs" href="products/add"> Add Product</a>
            <a type="button" class="btn btn-primary btn-xs" data-toggle="modal" data-target="#myModal">
                Import Products
            </a>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="table-responsive" id="product_listing">
            <table id="products-table" class="table table-bordered table-hover table-striped dataTables">
                <thead>
                    <tr>
                        <th width="25%">Product Name</th>
                        <th width="15%">Main Category</th>
                        <th width="20%">Category</th>
                        <th width="15%">Sub Category</th>
                        <th width="13%" class="center-aligned-column">Actions</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Import Products</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-12">
<?php echo form_open_multipart('products/import', array('id' => 'import_products_form', 'class' => 'form-horizontal')); ?>

                        <div class="col-md-5 col-md-offset-1">
                            <div class="btn-group profile_image_group">
                                <label title="Upload excel file" for="inputImage1" class="btn btn-primary excel_image">
                                    <input type="file" name="import_file" accept="file/*" class="hide" id="inputImage1">
                                    <div id="image_text">Upload Excel file</div>
                                </label>
                            </div>
                            <div class="error">
<?php echo form_error('import_file'); ?>
                            </div>
                        </div>

                        <div class="col-md-5 col-md-offset-1">
                            <div class="btn-group profile_image_group">
                                <label title="Upload zip file" for="import_zip_file" class="btn btn-primary zip_image">
                                    <input type="file" name="import_zip_file" accept="file/*" class="hide import_zip_file" id="import_zip_file">
                                    <div id="image_folder_text">Upload Product Images</div>
                                </label>
                            </div>
                            <div class="error">
<?php echo form_error('import_zip_file'); ?>
                            </div>
                        </div>
                        </form>
                    </div>
                    <div class="form-group">&nbsp;</div>
                    <div class="form-group">&nbsp;</div>
                    <div class="form-group">&nbsp;</div>
                    <div class="col-lg-12 col-md-offset-1">
                        You can download the sample file <a href="<?php echo $this -> config -> item('front_url') . SAMPLE_IMPORT_FILE_PATH; ?>product.xls">here</a>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary btn-xs block full-width m-b" id="import_products">Import</button>
                <button type="button" class="btn btn-danger btn-xs block full-width m-b" data-dismiss="modal" aria-label="Close">Cancel</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="imageModal"  data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel"> <span id="form-action">Select Image</span></h4>
            </div>
            <div class="modal-body">
                <div class="image-crop">
                    <img src="<?php echo $this -> config -> item('admin_assets'); ?>img/product.jpg">
                </div>
                <input type="hidden" name="aspect_ratio" id="aspect_ratio" value="any">
                <div><a id="crop-button" class="btn btn-primary btn-xs block full-width m-b">Select</a></div>
            </div>
        </div>
    </div>
</div>