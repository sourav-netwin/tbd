<style>
    .select2{
        width: 200px !important;
    }
</style>
<!-- /.row -->
<div class="row search_filter_container">
    <div class="col-lg-12">
        <div class="col-xs-12 col-md-12 col-sm-12">
            <div class="form-group pull-left">
                <label for="search">SEARCH</label>
                <input type="text" class="form-control search_small" id="search_filter" name="search_filter">
            </div>     
        </div>
        <div class="col-xs-12 text-right">
            <a class="btn btn-primary btn-xs" href="advertisements/add"> Add Advertisement</a>            
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="table-responsive" id="product_listing">
            <table id="advertisement-table" class="table table-bordered table-hover table-striped dataTables">
                <thead>
                    <tr>
                        <th width="5%">ID</th>
                        <th width="60%">Adverisment Title</th>                        
                        <th width="10%">StartDate</th>
                        <th width="10%">EndDate</th>
                        <th width="15%">Actions</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
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