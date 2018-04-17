<!-- /.row -->

<div class="row wid-row">
    <div class="col-xs-12"> 
        <div class="row wid-row"> 
            <div class="col-xs-4 margin-top-10">
                <div class="wid-det-box">
                    <div class="corner-box"></div>
                    <div class="wid-head">
                        No of registered users
                    </div>
                    <div class="wid-boby">
                        <div class="wid-body-main-text"><a href="javascript:users_details_show()"><?php echo number_format($users['users_count']); ?></a>
                            <div class="wid-body-text-sub">Users</div>
                        </div>
                        <!--<div class="wid-body-sub-text">Followers</div>-->
                    </div>
                    <div class="wid-footer">
                        <div class="wid-ft-right">
                            <span class="down-arrow-custom"></span>&nbsp;<span class="right-text">25%</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xs-4 margin-top-10">
                <div class="wid-det-box">
                    <div class="corner-box"></div>
                    <div class="wid-head">
                        Total Products
                    </div>
                    <div class="wid-boby">
                        <div class="wid-body-main-text"><a href="javascript:products_details_show()"><?php echo number_format($products['products_count']); ?></a>
                            <div class="wid-body-text-sub">Products</div>
                        </div>
                        <!--<div class="wid-body-sub-text">Followers</div>-->
                    </div>
                    <div class="wid-footer">
                        <div class="wid-ft-right">
                            <span class="up-arrow-custom"></span>&nbsp;<span class="right-text">25%</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-xs-4 margin-top-10">
                <div class="wid-det-box">
                    <div class="corner-box"></div>
                    <div class="wid-head">
                        Test title
                    </div>
                    <div class="wid-boby">
                        <input type="text" readonly="" value="75" class="knob1">
                    </div>
                    <div class="wid-footer">
                        <div class="wid-ft-right">
                            <span class="up-arrow-custom"></span>&nbsp;<span class="right-text">25%</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xs-4 margin-top-10">
                <div class="wid-det-box">
                    <div class="corner-box"></div>
                    <div class="wid-head">
                        Daily view (6 months)
                    </div>
                    <div class="wid-boby">
                        <div id="view_graph" class="wid-bar-chart"></div>
                    </div>
                    <div class="wid-footer">
                        <div class="wid-ft-right">
                            <span class="up-arrow-custom"></span>&nbsp;<span class="right-text">25%</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xs-4 margin-top-10">
                <div class="wid-det-box">
                    <div class="corner-box"></div>
                    <div class="wid-head">
                        Test title
                    </div>
                    <div class="wid-boby">
                        <div id="mychart1" class="wid-bar-chart"></div>
                    </div>
                    <div class="wid-footer">
                        <div class="wid-ft-right">
                            <span class="up-arrow-custom"></span>&nbsp;<span class="right-text">25%</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xs-4 margin-top-10">
                <div class="wid-det-box">
                    <div class="corner-box"></div>
                    <div class="wid-head">
                        Total Categories
                    </div>
                    <div class="wid-boby">
                        <div class="wid-body-main-text"><?php echo number_format($categories['categories_count']); ?>
                            <div class="wid-body-text-sub">Categories</div>
                        </div>
                        <!--<div class="wid-body-sub-text">Followers</div>-->
                    </div>
                    <div class="wid-footer">
                        <div class="wid-ft-right">
                            <span class="down-arrow-custom"></span>&nbsp;<span class="right-text">25%</span>
                        </div>
                    </div>
                </div>
            </div>
            



        </div>

    </div>
</div>

<div class="row">
<!--    <div class="col-lg-3 col-xs-6">
         small box 
        <div class="small-box bg-aqua">
            <div class="inner">
                <h3><?php //echo $users['users_count']; ?></h3>
                <p>Users</p>
            </div>
            <div class="icon">
                <i class="fa fa-user"></i>
            </div>
            <a href="javascript:users_details_show()" class="small-box-footer">
                More info <i class="fa fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
    <div class="col-lg-3 col-xs-6">
         small box 
        <div class="small-box bg-green">
            <div class="inner">
                <h3><?php //echo $products['products_count']; ?></h3>
                <p>Products</p>
            </div>
            <div class="icon">
                <i class="fa  fa-shopping-cart"></i>
            </div>
            <a href="javascript:products_details_show()" class="small-box-footer">
                More info <i class="fa fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
    <div class="col-lg-3 col-xs-6">
         small box 
        <div class="small-box bg-yellow">
            <div class="inner">
                <h3><?php //echo $categories['categories_count']; ?></h3>
                <p>Categories</p>
            </div>
            <div class="icon">
                <i class="fa fa-list"></i>
            </div>
            <a href="javascript:products_details_show()" class="small-box-footer">
                More info <i class="fa fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>-->
    <!--    <div class="col-md-2 col-sm-6 col-xs-12">
            <div class="main_det_head">Users</div>
            <span class="main_det_center"><a href="javascript:users_details_show()"><?php echo $users['users_count']; ?></a></span> 
            <div class="info-box top-h" id="users_count"> 
            </div>
        </div>
        <div class="col-md-2 col-sm-6 col-xs-12">
            <div class="main_det_head">Products</div>
            <span class="main_det_center"><a href="javascript:products_details_show()"><?php echo $products['products_count']; ?></a></span> 
            <div class="info-box top-h" id="products_count"> 
            </div>
        </div>
        <div class="col-md-2 col-sm-6 col-xs-12">
            <div class="main_det_head">Categories</div>
            <span class="main_det_center"><a href="javascript:void(0)"><?php echo $categories['categories_count']; ?></a></span> 
            <div class="info-box top-h" id="categories_count"> 
            </div>
        </div>-->
</div>
<div class="row" id="home_main_details">
    <input type="button" id="back_btn_dash" class="btn btn-default btn-xs pull-right" style="margin-right: 20px" value="<< Back" />
    <div class="col-sm-12" id="dash_bc"><ul></ul></div>
    <div class="col-sm-12" id="main_expansion">

    </div>
    <div class="col-sm-12" id="main_details_expansion">

    </div>
</div>
<div class="row">

    <div class="col-md-12">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">Monthly Recap Report</h3>
            </div><!-- /.box-header -->
            <div class="box-body">
                <div class="row">
                    <div class="col-md-8">
                        <p class="text-center">
                            <strong id="monthly-recap-report-duration"></strong>
                        </p>
                        <div class="chart">
                            <!-- Sales Chart Canvas -->
                            <canvas id="salesChart" style="height: 180px;"></canvas>
                        </div><!-- /.chart-responsive -->
                    </div><!-- /.col -->
                    <div class="col-md-4">
                        <p class="text-center">
                            <strong>Popular Categories</strong>
                        </p>
                        <div id="popular-category-view"></div>
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- ./box-body -->
            <div class="box-footer">
                <div class="row">
                    <div class="col-sm-3 col-xs-6">
                        <div class="description-block border-right">
                            <span class="description-percentage text-green"><i class="fa fa-caret-up"></i> 17%</span>
                            <h5 class="description-header">$35,210.43</h5>
                            <span class="description-text">TOTAL REVENUE</span>
                        </div><!-- /.description-block -->
                    </div><!-- /.col -->
                    <div class="col-sm-3 col-xs-6">
                        <div class="description-block border-right">
                            <span class="description-percentage text-yellow"><i class="fa fa-caret-left"></i> 0%</span>
                            <h5 class="description-header">$10,390.90</h5>
                            <span class="description-text">TOTAL COST</span>
                        </div><!-- /.description-block -->
                    </div><!-- /.col -->
                    <div class="col-sm-3 col-xs-6">
                        <div class="description-block border-right">
                            <span class="description-percentage text-green"><i class="fa fa-caret-up"></i> 20%</span>
                            <h5 class="description-header">$24,813.53</h5>
                            <span class="description-text">TOTAL PROFIT</span>
                        </div><!-- /.description-block -->
                    </div><!-- /.col -->
                    <div class="col-sm-3 col-xs-6">
                        <div class="description-block">
                            <span class="description-percentage text-red"><i class="fa fa-caret-down"></i> 18%</span>
                            <h5 class="description-header">1200</h5>
                            <span class="description-text">GOAL COMPLETIONS</span>
                        </div><!-- /.description-block -->
                    </div>
                </div><!-- /.row -->
            </div><!-- /.box-footer -->
        </div><!-- /.box -->
    </div><!-- /.col -->

</div>
<div class="row">

    <div class="col-md-8">
        <!-- MAP & BOX PANE -->
        <div class="box box-success">
            <div class="box-header with-border">
                <h3 class="box-title">Visitors Report</h3>
            </div><!-- /.box-header -->
            <div class="box-body no-padding">
                <div class="row">
                    <div class="col-md-9 col-sm-8">
                        <div class="pad">
                            <!-- Map will be created here -->
                            <div id="world-map-markers" style="height: 325px;"></div>
                        </div>
                    </div><!-- /.col -->
                    <div class="col-md-3 col-sm-4">
                        <div class="pad box-pane-right bg-green" style="min-height: 280px">
                            <div class="description-block margin-bottom">
                                <div class="sparkbar pad" data-color="#fff">90,70,90,70,75,80,70</div>
                                <h5 class="description-header">8390</h5>
                                <span class="description-text">Visits</span>
                            </div><!-- /.description-block -->
                            <div class="description-block margin-bottom">
                                <div class="sparkbar pad" data-color="#fff">90,50,90,70,61,83,63</div>
                                <h5 class="description-header">30%</h5>
                                <span class="description-text">Referrals</span>
                            </div><!-- /.description-block -->
                            <div class="description-block">
                                <div class="sparkbar pad" data-color="#fff">90,50,90,70,61,83,63</div>
                                <h5 class="description-header">70%</h5>
                                <span class="description-text">Organic</span>
                            </div><!-- /.description-block -->
                        </div>
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.box-body -->
        </div><!-- /.box -->



    </div><!-- /.col -->

    <div id="embed-api-auth-container"></div>
    <div id="view-selector-container"></div>
    <div id="view-name"></div>
    <div id="active-users-container"></div>
</div>



</div>
<!-- /.col -->
</div>



<!-- /.row -->

