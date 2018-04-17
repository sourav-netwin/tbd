<!-- /.row -->
<div class="row search_filter_container">
    <div class="col-lg-12">
        <div class="col-lg-9">
            <div class="form-group pull-left">
                <label for="search">SEARCH</label>
                <input type="text" class="form-control" id="search_filter" name="search_filter">
            </div>
        </div>
        <div class="col-lg-3 text-right">
            <a class="btn btn-primary btn-xs" href="retailers/add"> Add Retailer</a>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="table-responsive">
            <table id="retailers-table" class="table table-bordered table-hover table-striped dataTables">
                <thead>
                    <tr>
                        <th>Company Name</th>
                        <th>Corporate Brand</th>
                        <th>Store Format</th>
                        <th>Category</th>
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
                <div class="modal-body">
                    <div class="image-crop">
                        <img src="<?php echo $this->config->item('admin_assets'); ?>img/product.jpg">
                        <input type="hidden" name="aspect_ratio" id="aspect_ratio" value="any">
                    </div>
                    <div><a id="crop-button" class="btn btn-primary btn-xs block full-width m-b">Select</a></div>
                </div>
            </div>
        </div>
    </div>