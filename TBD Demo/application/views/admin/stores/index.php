<style>
    .select2{
        width: 145px !important;
    }
    .row-warning{
        background-color: #ED3237 !important;
    }
</style>
<!-- /.row -->
<div class="row search_filter_container">
    <div class="col-lg-12">
        <div class="col-xs-8 col-sm-8 col-md-8">
            <div class="form-group pull-left">
                <label for="search">SEARCH</label>
                <input type="text" class="form-control" id="search_filter" name="search_filter" style="width: 200px !important">
            </div>
            <?php
            //Admin Users
            if ($this -> session -> userdata('user_level') < 3) {
                ?>
                <select class="form-control search_product_select select-filter" id="retailers" name="retailers">
                    <option value="">Select Retailer</option>
                    <?php
                    if (!empty($retailers)) :
                        foreach ($retailers as $retailer) {
                            echo "<option value='" . $retailer['Id'] . "'" . ( ( $retailer['Id'] == $retailer_id ) ? 'selected' : '' ) . ">" . $retailer['CompanyName'] . "</option>";
                        }
                    endif;
                    ?>
                </select>
            <?php } ?>
            <?php
            //Retailers Users
            if ($this -> session -> userdata('user_level') <= 3) {
                ?>
                <select class="form-control search_product_select select-filter" id="store_format" name="store_format">
                    <option value="">Select Store Format</option>
                    <?php
//                    if (!empty($store_formats)) :
//                        foreach ($store_formats as $store_format) {
//                            echo "<option value='" . $store_format['Id'] . "'" . ( ( $store_format['Id'] == $store_format_id ) ? 'selected' : '' ) . ">" . $store_format['StoreType'] . "</option>";
//                        }
//                    endif;
                    ?>
                </select>
            <?php } ?>
            <select class="form-control search_product_select select-filter" id="region" name="region">
                <option value="">Select Region</option>
                <?php
                    if (!empty($states)) :
                        foreach ($states as $state) {
                            echo "<option value='" . $state['Id'] . "'>" . $state['Name'] . "</option>";
                        }
                    endif;
                    ?>
            </select>
        </div>
        <div class="col-xs-4 col-sm-4 col-md-4 text-right">
            <a class="btn btn-primary btn-xs" href="<?php echo base_url() ?>stores/add"> Add Store</a>
            <a class="btn btn-primary btn-xs" href="<?php echo base_url() ?>stores/add_store_user" title="Store User"> Add User</a>
            <a type="button" class="btn btn-primary btn-xs" data-toggle="modal" data-target="#myModal">
                Import Stores
            </a>
            <?php
            if ($this -> session -> userdata('user_type') == 3) {
                ?>
                <a class="btn btn-primary btn-xs" href="javascript:void(0)" id="add_product_to_new_stores">Add Catalogue to New Stores</a>
                <?php
            }
            ?>

        </div>
    </div>

</div>
<div class="row">
    <div class="col-lg-12">
        <div class="table-responsive">
            <table id="stores-table" class="table table-bordered table-hover table-striped dataTables">
                <thead>
                    <tr>
                        <th width="20%">Store Name</th>
                        <th width="15%">Retailer Company</th>
                        <th width="13%">Store Formats</th>
                        <th width="5%">State</th>
                        <th width="30%">Address</th>
                        <th width="7%">Users</th>
                        <th style="max-width: 65px;min-width: 65px">Actions</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="myModal"  role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Import Stores</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <?php echo form_open_multipart('stores/import', array('id' => 'import_stores_form', 'class' => 'form-horizontal')); ?>

                    <div class="col-md-5 col-md-offset-1">

                        <?php
                        //Admin Users
                        if ($this -> session -> userdata('user_level') < 3) {
                            ?>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label for="retailers">Retailers <span>*</span></label>
                                        <select class="form-control select-filter" id="retailers_import" name="retailers">
                                            <option value="">Select Retailer</option>
                                            <?php
                                            if (!empty($retailers)) :
                                                foreach ($retailers as $retailer) {
                                                    echo "<option value='" . $retailer['Id'] . "'>" . $retailer['CompanyName'] . "</option>";
                                                }
                                            endif;
                                            ?>
                                        </select>
                                        <div class="error">
                                            <?php echo form_error('retailers'); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                        <?php
                        //Retailers Users
                        if ($this -> session -> userdata('user_level') <= 3) {
                            ?>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label for="store_format">Store Format <span>*</span></label>
                                        <select class="form-control select-filter" id="store_format_import" name="store_format">
                                            <option value="">Select Store Format</option>
                                            <?php
                                            if (!empty($store_formats)) :
                                                foreach ($store_formats as $store_format) {
                                                    echo "<option value='" . $store_format['Id'] . "'>" . $store_format['StoreType'] . "</option>";
                                                }
                                            endif;
                                            ?>
                                        </select>
                                        <div class="error">
                                            <?php echo form_error('store_format'); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-12">
                                    You can download the sample file <a href="<?php echo $this -> config -> item('front_url') . SAMPLE_IMPORT_FILE_PATH; ?>stores_import.xls">here</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-5">
                        <div class="btn-group profile_image_group">
                            <label title="Upload excel file" for="inputImage" class="btn btn-primary btn-xs excel_image">
                                <input type="file" name="import_file" accept="file/*" class="hide" id="inputImage">
                                <div id="image_text">Upload Excel file</div>
                            </label>
                        </div>
                        <div class="error">
                            <?php echo form_error('import_file'); ?>
                        </div>
                    </div>

                    <div class="form-group">&nbsp;</div>
                    <div class="form-group">&nbsp;</div>
                    <div class="form-group">&nbsp;</div>
                    </form>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary btn-xs block full-width m-b" id="import_stores">Import</button>
                <button type="button" class="btn btn-danger btn-xs block full-width m-b" data-dismiss="modal" aria-label="Close">Cancel</button>
            </div>
        </div>
    </div>
</div>