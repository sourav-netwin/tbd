<!-- /.row -->
<div class="row search_filter_container">
    <div class="col-lg-12">
        <div class="col-xs-8 col-sm-8 col-md-8">
            <div class="form-group pull-left">
                <label for="search">SEARCH</label>
                <input type="text" class="form-control" id="search_special_filter" name="search_special_filter" style="display: inline;margin-right: 10px;width: auto;">
                     
            </div> 
            
            <?php if ($years) { ?>
                <select class="form-control search_product_select select-filter" id="yearId" name="yearId" style="width:110px">
                    <option value="">Select Year</option>
                    <?php
                    if (!empty($years)) :
                        foreach ($years as $year) {
                            echo "<option value='" . $year['yearId'] . "'" . ( ( (int)$year['yearId'] == $yearId ) ? 'selected' : '' ) . ">" . $year['yearName'] . "</option>";
                        }
                    endif;
                    ?>
                </select>
            <?php } ?>            
                
             <select class="form-control search_product_select select-filter" id="monthId" name="monthId" style="width:130px">
                    <option value="">Select Month</option>
                    <option value="1" <?php if($monthId == 1){?> selected="selected" <?php } ?> >January</option>
                    <option value="2" <?php if($monthId == 2){?> selected="selected" <?php } ?> >February</option>
                    <option value="3" <?php if($monthId == 3){?> selected="selected" <?php } ?> >March</option>
                    <option value="4" <?php if($monthId == 4){?> selected="selected" <?php } ?> >April</option>
                    <option value="5" <?php if($monthId == 5){?> selected="selected" <?php } ?> >May</option>
                    <option value="6" <?php if($monthId == 6){?> selected="selected" <?php } ?> >June</option>
                    <option value="7" <?php if($monthId == 7){?> selected="selected" <?php } ?> >July</option>
                    <option value="8" <?php if($monthId == 8){?> selected="selected" <?php } ?> >August</option>
                    <option value="9" <?php if($monthId == 9){?> selected="selected" <?php } ?> >September</option>
                    <option value="10" <?php if($monthId == 10){?> selected="selected" <?php } ?> >October</option>
                    <option value="11" <?php if($monthId == 11){?> selected="selected" <?php } ?> >November</option>
                    <option value="12" <?php if($monthId == 12){?> selected="selected" <?php } ?> >December</option>
            </select>
            
        </div>
        <div class="col-xs-4 col-sm-4 col-md-4 text-right">
            <a class="btn btn-primary btn-xs pull-right" href="specialmanagement/add"> Add Special</a>
        </div>        
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="table-responsive">
            <table id="specials-table" class="table table-bordered table-hover table-striped dataTables">
                <thead>
                    <tr>
                        <th width="40%">Special</th>
                        <th width="10%">From</th>
                        <th width="10%">To</th>
                        <th width="5%">Count</th>
                        <th width="10%">Store Count</th>
                        <!--<th width="30%">Terms and Conditions</th>-->
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
                    <input type="hidden" name="aspect_ratio" id="aspect_ratio" value="2.80">
                </div>
            </div>
        </div>
    </div>
