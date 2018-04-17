<!-- /.row -->
<style>
    .select2{
        width: 100% !important;
    }
</style>
<div class="row search_filter_container">
    <div class="col-lg-12">
        <div class="col-lg-4">
            <div class="form-group pull-left">
                <label for="search">SEARCH</label>
                <input type="text" class="form-control" id="search_filter" name="search_filter">
            </div>
        </div>
<!--        <div class="col-lg-6">
            <div class="form-group pull-left">
                <select class="form-control select-filter" id="sel_special" name="sel_special">
                    <option value="">Select Special</option>
                </select>
            </div>
        </div>-->
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="table-responsive" id="special-product-listing">
            <table id="store-catalogue-table" class="table table-bordered table-hover table-striped dataTables">
                <thead>
                    <tr>
                        <!--<th width="1%" class="no-sort"><input type="checkbox" /></th>-->
                        <th width="45%">Product Name</th>
                        <th width="30%">Store</th>
                        <th width="5%">Default Price</th>
                        <th width="5%">Offer Price</th>
                        <th width="7%" class="alignCenter">Offer Start Date</th>
                        <th width="7%" class="alignCenter">Offer End Date</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>