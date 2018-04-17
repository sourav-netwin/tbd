<!-- /.row -->
<div class="row search_filter_container">
    <div class="col-lg-12">
        <div class="col-xs-7 col-md-7 col-sm-7">
            <div class="form-group pull-left">
                <label for="search">SEARCH</label>
                <input type="text" class="form-control" id="search_filter" name="search_filter">
            </div>
            <select class="form-control select-filter search_select" id="role_search" name="role_search">
                <option value="0">Select User Role</option>
                <?php 
                    if( !empty ($user_roles) ) : 
                        foreach ($user_roles as $user_role) 
                        {
                           echo "<option value='".$user_role['Id']."'>".$user_role['Type']."</option>";
                        }
                    endif;
                ?>
            </select>
        </div>
        <div class="col-xs-5 col-md-5 col-sm-5">
            <a class="btn btn-primary btn-xs pull-right" href="users/add"> Add User</a>
        </div>        
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="table-responsive">
            <table id="users-table" class="table table-bordered table-hover table-striped dataTables">
                <thead>
                    <tr>
                        <th width="25%">Name</th>
                        <th width="23%">Email</th>
                        <th width="25%">Company Name</th>
                        <th width="15%">Role</th>
                        <th style="max-width: 80px;min-width: 80px">Actions</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="imageModal" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel"> <span id="form-action">Select Image</span></h4>
                </div>
                <div class="modal-body" >

                        <div class="image-crop">
                            <img src="<?php echo $this->config->item('admin_assets'); ?>img/default.gif">
                        </div>


                    <div><a id="crop-button" class="btn btn-primary btn-xs block full-width m-b">Select</a></div>
                </div>
            </div>
        </div>
    </div>
