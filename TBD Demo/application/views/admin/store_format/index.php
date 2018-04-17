<!-- /.row -->
<div class="row search_filter_container">
    <div class="col-lg-12">
        <div class="col-lg-4">
            <div class="form-group pull-left">
                <label for="search">SEARCH</label>
                <input type="text" class="form-control" id="search_filter" name="search_filter">
            </div>
        </div>
        <div class="col-lg-4">
            <?php 
            if ($this -> session -> userdata('user_type') != 3) { 
                ?>
                <select id="retailer_sel" name="retailer_sel" class="form-control select-filter">
                    <option value="">Select Retailer</option>
                    <?php
                    if($retailers){
                        foreach($retailers as $retailer){
                            ?>
                    <option value="<?php echo $retailer['Id']?>" <?php echo $ret_id == $retailer['Id'] ? 'selected="selected"' : '' ?>><?php echo $retailer['CompanyName']?></option>
                    <?php
                        }
                    }
                    ?>
                </select>
            <?php
            } 
            ?>

        </div>
        <div class="col-lg-4 text-right">
            <?php if ($this -> session -> userdata('user_type') != 3) { ?>
                <a class="btn btn-primary btn-xs" href="<?php echo site_url('/retailers'); ?>" > Back </a>
            <?php } ?>

            <?php if ($this -> session -> userdata('user_type') != 3) { ?>
                <a class="btn btn-primary btn-xs" href="/admin/storeformat/add/<?php echo $ret_id ?>" > Add Store Format</a>
                <a id="add-user-btn" class="btn btn-primary btn-xs" href="/admin/storeformat/add_store_format_user/<?php echo $ret_id ?>" title="Store Format User"> Add User</a>
            <?php }
            else {
                ?>
                <a class="btn btn-primary btn-xs" href="<?php echo site_url('storeformat/add'); ?>" > Add Store Format</a>
                <a id="add-user-btn" class="btn btn-primary btn-xs" href="<?php echo site_url('storeformat/add_store_format_user'); ?>" title="Store Format User"> Add User</a>
<?php } ?>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="table-responsive">
            <table id="storeformat-table" class="table table-bordered table-hover table-striped dataTables">
                <thead>
                    <tr>
                        <th>Store Formats</th>
                        <th>Stores</th>
                        <th>Users</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<!--Add/Edit Forms-->

<!-- Add Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"> <span id="form-action">Add</span> Store Format- <?php echo $retailer['CompanyName']; ?></h4>
            </div>
            <div class="modal-body">

<?php echo form_open_multipart('storeformat/add', array('id' => 'storeformat_form', 'class' => 'form-horizontal')); ?>
                <input id="retailer_id" type="hidden" name="retailer_id" value="<?php echo $ret_id; ?>">

                <div class="form-group">
                    <label for="store_format_name" class="col-sm-3 control-label">Name <span>*</span></label>
                    <div class="col-sm-8">
                        <input type="text" class="form-control" name="storeformat_name" placeholder="Store Format Name" value="<?php echo set_value('store_format_name'); ?>">
                        <div class="error">
<?php echo form_error('store_format_name'); ?>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label"></label>
                    <div class="col-sm-8">
                        <button type="submit" class="btn btn-primary btn-xs block full-width m-b">Add</button>
                        <a class="btn btn-primary btn-xs block full-width m-b" data-dismiss="modal" aria-label="Close">Cancel</a>
                    </div>
                </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Add Modal -->
<div class="modal fade" id="myEditModal" tabindex="-1" role="dialog" aria-labelledby="myEditModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myEditModalLabel"> <span id="form-action">Edit</span> Store Format- <?php echo $retailer['CompanyName']; ?></h4>
            </div>
            <div class="modal-body">

<?php echo form_open_multipart('storeformat/edit', array('id' => 'storeformat_form_edit', 'class' => 'form-horizontal')); ?>
                <input id="retailer_id" type="hidden" name="retailer_id" value="<?php echo $retailer['Id']; ?>">
                <input id="storeformat_id" type="hidden" name="storeformat_id" value="">

                <div class="form-group">
                    <label for="store_format_name" class="col-sm-3 control-label">Name <span>*</span></label>
                    <div class="col-sm-8">
                        <input type="text" class="form-control" id="storeformat_name" name="storeformat_name" placeholder="Store Format Name" >
                        <div class="error">
<?php echo form_error('store_format_name'); ?>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label"></label>
                    <div class="col-sm-8">
                        <button type="submit" class="btn btn-primary btn-xs block full-width m-b">Update</button>
                        <a class="btn btn-primary btn-xs block full-width m-b" data-dismiss="modal" aria-label="Close">Cancel</a>
                    </div>
                </div>
                </form>
            </div>
        </div>
    </div>
</div>