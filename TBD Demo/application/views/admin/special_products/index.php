<!-- /.row -->
<div class="row search_filter_container">
    <div class="col-lg-12">
        <div class="col-lg-8">
            <div class="form-group pull-left">
                <label for="search">SEARCH</label>
                <input type="text" class="form-control" id="search_filter" name="search_filter">
            </div>
            <?php if (!empty($specials)) { ?>
                <select class="form-control select-filter search_product_select" id="special_category" name="special_category">
                    <option value="">Select Special</option>
                    <?php foreach ($specials as $special) { ?>
                        <option value="<?php echo $special['Id']; ?>"><?php echo $special['SpecialName']; ?></option>
                    <?php } ?>
                </select>
            <?php } ?>
            <!--            <div class="input-group search_calender">
                            <div class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </div>
                            <input type="text" class="form-control" name="price_from" id="price_from" placeholder="Offer From" value="">
                        </div>
                        <div class="input-group search_calender">
                            <div class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </div>
                            <input type="text" class="form-control" name="price_to" id="price_to" placeholder="Offer To" value="">
                        </div>-->
        </div>
        <!--        <div class="col-lg-4">
                    <a class="btn btn-primary btn-xs pull-right" href="specialproducts/add"> Add Special Product</a>
                </div>-->
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="table-responsive" id="special-product-listing">
            <table id="special-products-table" class="table table-bordered table-hover table-striped dataTables">
                <thead>
                    <tr>
                        <th width="35%">Product Name</th>
                        <th width="15%">Store</th>
                        <th>Special Name</th>
                        <th>Special Price</th>
                        <th class="alignCenter">Offer Start Date</th>
                        <th class="alignCenter">Offer End Date</th>
                        <th style="max-width: 80px;min-width: 80px">Actions</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>