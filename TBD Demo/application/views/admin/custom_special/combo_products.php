<style>
    .select2{
        width: 200px !important;
    }
</style>
<!-- /.row -->

<div class="row">
    <div class="col-xs-10 col-xs-offset-1">
        
        
        <div class="row search_filter_container">
            <div class="col-lg-12">
                <div class="col-xs-12 col-md-12 col-sm-12">
                    <div class="form-group pull-left">
                        <label for="search">SEARCH</label>
                        <input type="text" class="form-control search_small" id="search_filter" name="search_filter">
                    </div> 
                    <div class="form-group pull-right">
                        <button id="comboproduct_add" class="btn btn-primary btn-xs block full-width m-b" type="button">&nbsp;&nbsp;Add&nbsp;&nbsp;</button>
                    </div> 
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-lg-12">
                <div class="table-responsive" id="product_listing">
                    <table id="combo-products-table" class="table table-bordered table-hover table-striped dataTables">
                        <thead>
                            <tr>
                                <th width="5%">ID</th>
                                <th width="65%">Product Name</th>                        
                                <th width="10%">Quantity</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="form-group">
            <div class="row">
            <div class="col-md-12">                
                <a id="comboproduct_back" class="btn btn-danger btn-xs block full-width m-b" href="javascript:void(0)">Back</a>
            </div>
            </div>
        </div>

</div>
</div>