<!-- /.row -->
<div class="row search_filter_container">
    <div class="col-lg-12">
        <div class="col-lg-8">
            <div class="form-group pull-left">
                <label for="search">SEARCH</label>
                <input type="text" class="form-control" id="search_filter" name="search_filter">
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="table-responsive">
            <div id="baseUrl" data-id="<?php echo base_url(); ?>">     
            <table id="social-users-table" class="table table-bordered table-hover table-striped dataTables">
                <thead>
                    <tr>
                        <th width="20%">Name</th>
                        <th width="30%">Email</th>
                        <th width="12%">Social Network</th>
                        <th width="12%">Registration Date</th>
                        <!--<th width="30%">Loyalty Points</th>-->
                        <th style="max-width: 65px;min-width: 65px">Actions</th>
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
