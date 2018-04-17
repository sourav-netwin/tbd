<!-- /.row -->
<div class="row search_filter_container">
    <div class="col-lg-12">
        <div class="col-lg-8">
            <div class="form-group pull-left">
                <label for="search">SEARCH</label>
                <input type="text" class="form-control" id="search_filter" name="search_filter">
            </div>
        </div>
         <div class="col-lg-4">
            <a class="btn btn-primary btn-xs pull-right" href="users/add_store_user"> Add User</a>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="table-responsive">
            <table id="admins-table" class="table table-bordered table-hover table-striped dataTables">
                <thead>
                    <tr>
                        <th width="25%">Name</th>
                        <th width="23%">Email</th>
                        <th width="15%">Role</th>
                        <th width="25%">Company Name</th>
                        <th width="12%">Actions</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>

        </div>
    </div>
</div>