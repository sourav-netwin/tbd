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
            <!--<a class="btn btn-primary btn-xs" href="loyaltyorders/add"> Add Order</a>-->           
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="table-responsive" id="product_listing">
            <table id="loyaltyorders-table" class="table table-bordered table-hover table-striped dataTables">
                <thead>
                    <tr>
                        <th width="5%">ID</th>
                        <th width="10%">Order Number</th>
                        <th width="20%">Name</th>                        
                        <th width="10%">Email</th>
                        <th width="10%">Order Total</th>
                        <th width="10%">Status</th>
                        <th width="15%">Order Date</th>                        
                        <!--<th width="12%">Loyalty Points</th>-->
                        <th width="5%">Actions</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

