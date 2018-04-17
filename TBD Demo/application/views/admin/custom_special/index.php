<!-- /.row -->
<style>
    .select2{
        width: 100% !important;
    }
    .housebrand-chk{
        padding-top:10px !important;
    }
    .prod_prc, .prod_def, .coupon_amount, .prd_qty {
         text-align: right !important; 
    }
    .center-aligned-column {
         text-align: center !important; 
    }
    .right-aligned-column {
         text-align: right !important; 
    }
    .label-warning {
        background-color: #FFCC99 !important; /* #FFB97C, #FFA5A5 */
    }
    .highlight-red{
        color: #FF0000
    }
    .coupon-column-background{
        background-color: #FF0000;
    }
</style>
<div class="row">
    <section class="col-xs-10 col-xs-offset-1" style="padding-right: 0px;padding-left: 0px;">
        <nav>
            <ol class="cd-multi-steps text-center" style="text-align: left;">
                <li class="current" id="lio"><em style="font-style: normal;">Select Special</em></li>
                <li id="lit"><em style="font-style: normal;">Select Products</em></li>
                <li id="lir"><em style="font-style: normal;">Confirm Pricing</em></li>
                <li id="lif"><em style="font-style: normal;">Finish</em></li>
            </ol>
        </nav>
    </section>
</div>
<div class="row">
    <div id="specials_add_div">
        <?php echo form_open_multipart('', array('id' => 'special_add_form', 'class' => 'form-horizontal', ' autocomplete' => 'off')); ?>
        <div class="col-xs-5 col-xs-offset-1">

            <div class="form-group">
                <div class="row">
                    <div class="col-md-12">
                        <label for="special_name_sel">Special <span>*</span></label>
                        <select name="special_name_sel" id="special_name_sel" class="form-control select-filter">
                            <option value=""> Select Special </option>
                            <?php foreach ($specials as $special_name): ?>
                                <option value="<?php echo $special_name['Id'] ?>" <?php echo set_select('special_name_sel', $special_name['Id']); ?>><?php echo $special_name['SpecialName'] ?></option>
                            <?php endforeach ?>
                        </select>
                    </div>
                </div>
            </div>

<!--            <div class="form-group" id="special_name_cont">
                <div class="row">
                    <div class="col-md-12">
                        <label for="special_name" >Special Name <span>*</span></label>
                        <input type="text" class="form-control" id="special_name" name="special_name" placeholder="Special Name" value="<?php //echo set_value('special_name'); ?>">
                        <div class="error">
                            <?php //echo form_error('special_name'); ?>
                        </div>
                    </div>
                </div>
            </div>-->

            <div class="form-group">
                <div class="btn-group profile_image_group" style="max-width: unset">
                    <label for="inputImage" class="btn btn-primary btn-xs spbanner_image">
<!--                        <span title="Upload Banner" style="background: rgba(254, 254, 254, 0.7) url('<?php //echo base_url() ?>../assets/admin/img/upload.png') no-repeat scroll center center / 25px 25px;">
                            <input type="file" accept="image/*" name="spbanner_image" class="hide" id="inputImage">
                        </span>-->
                    </label>
                </div>
                <div class="img-preview img-preview-sm"></div>
            </div>



            <div class="form-group">
                <div class="row">
                    <div class="col-md-6">
                        <label for="price_from">Special From <span>*</span></label>

                        <div class="input-group">
                            <div class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </div>
                            <input type="text" class="form-control" name="price_from" id="price_from" placeholder="Price From" value="" disabled="">
                        </div>
                        <div class="error">
                            <?php echo form_error('price_from'); ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label for="price_to" >Special To <span>*</span></label>

                        <div class="input-group">
                            <div class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </div>
                            <input type="text" class="form-control" name="price_to" id="price_to" placeholder="Price To" value="" disabled="">
                        </div>
                        <div class="error">
                            <?php echo form_error('price_to'); ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="row">
                    <div class="col-md-12">
                        <label for="special_name" >Terms and conditions for this specials</label><br />
                        <div id="special_terms"></div>
<!--                        <textarea class="form-control" id="special_terms" name="special_terms" disabled=""></textarea>
                        <div class="error">
                            <?php //echo form_error('special_terms'); ?>
                        </div>-->
                    </div>
                </div>
            </div>


            <div class="form-group">
                <div class="row">
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-primary btn-xs block full-width m-b">Select Products</button>
                        <!--<a class="btn btn-danger btn-xs block full-width m-b" href="<?php //echo site_url('/users');      ?>">Cancel</a>-->
                    </div>
                </div>
            </div>
        </div>


        <!-- Add Modal -->
        <div class="modal fade" id="imageModal" data-keyboard="false" data-backdrop="static">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="myModalLabel"> <span id="form-action">Select</span></h4>
                    </div>
                    <div class="modal-body" >
                        <div class="image-crop">
                            <img src="<?php echo $this -> config -> item('admin_assets'); ?>img/default.gif">
                        </div>
                        <div><a id="crop-button" class="btn btn-primary btn-xs block full-width m-b">Select</a></div>
                        <input type="hidden" name="image-x" id="image-x">
                        <input type="hidden" name="image-y" id="image-y">
                        <input type="hidden" name="image-width" id="image-width">
                        <input type="hidden" name="image-height" id="image-height">

                        <input type="hidden" name="aspect_ratio" id="aspect_ratio" value="2.80">
                    </div>
                </div>
            </div>
        </div>
        </form>
    </div>
</div>