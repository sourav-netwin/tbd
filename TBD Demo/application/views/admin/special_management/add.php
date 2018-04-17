<!-- /.row -->
<style>
    .select2{
        width: 100% !important;
    }
</style>
<div class="row">
    <div id="specials_add_div">
        <?php echo form_open_multipart(front_url() . 'admin/specialmanagement/add', array('id' => 'special_add_form', 'class' => 'form-horizontal', ' autocomplete' => 'off')); ?>
        <div class="col-xs-5 col-xs-offset-1">

            <div class="form-group" id="special_name_cont">
                <div class="row">
                    <div class="col-md-12">
                        <label for="special_name" >Special Name <span>*</span></label>
                        <input type="text" class="form-control" id="special_name" name="special_name" placeholder="Special Name" value="<?php echo set_value('special_name'); ?>">
                        <div class="error">
                            <?php echo form_error('special_name'); ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="btn-group profile_image_group" style="max-width: unset">
                    <label for="inputImage" class="btn btn-primary btn-xs spbanner_image">
                        <span title="Upload Banner" style="background: rgba(254, 254, 254, 0.7) url('<?php echo base_url() ?>../assets/admin/img/upload.png') no-repeat scroll center center / 25px 25px;">
                            <input type="file" accept="image/*" name="spbanner_image" class="hide" id="inputImage">
                        </span>
                        <div class="error" style="line-height: 0px;">
                            <?php echo form_error('profile_image'); ?>
                        </div>
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
                            <input type="text" class="form-control" name="price_from" id="price_from" placeholder="Price From" value="">
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
                            <input type="text" class="form-control" name="price_to" id="price_to" placeholder="Price To" value="">
                        </div>
                        <div class="error">
                            <?php echo form_error('price_to'); ?>
                        </div>
                    </div>
                </div>
            </div>

            <?php if ($this -> session -> userdata('user_type') == 3) {  // Only for Retailer ?>
            <div class="form-group" id="regional_special_area">
                <div class="row">
                    <div class="col-md-12">
                        <label for="regional_special" >Special Type</label><br />
                        <input type="checkbox" value="1" name="regional_special" />&nbsp;Regional Special<br />
                        <div class="error">
                            <?php echo form_error('regional_special'); ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php } // if ($this -> session -> userdata('user_type') == 3) ?>
            
            <div class="form-group" id="special_name_cont">
                <div class="row">
                    <div class="col-md-12">
                        <label for="special_name" >Add terms and conditions for this specials</label><br />
                        <!--<textarea class="form-control" id="special_terms" name="special_terms" value="<?php //echo set_value('special_terms');                  ?>"></textarea>-->
                        <?php
                        if ($terms) {
                            foreach ($terms as $term) {
                                ?>
                                <input type="checkbox" value="<?php echo $term['Id'] ?>" name="sp_t_and_c[]" />&nbsp;<?php echo $term['TermsText'] ?><br />
                                <?php
                            }
                        }
                        ?>
                        <div class="error">
                            <?php echo form_error('sp_t_and_c'); ?>
                        </div>
                    </div>
                </div>
            </div>


            <div class="form-group">
                <div class="row">
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-primary btn-xs block full-width m-b">Add</button>
                        <a class="btn btn-danger btn-xs block full-width m-b" href="<?php echo site_url('/specialmanagement'); ?>">Cancel</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xs-5">
            <?php
            if ($this -> session -> userdata('user_type') != 6) {
                ?>
                <div class="form-group col-xs-12">
                    <label for="store_formats">States <span>*</span></label>
                    <div id="state_list" name="state_list" class="checkbox_lists" style="max-height: 200px;">
                        <?php
                        if ($states) {
                            $cnt = 1;
                            foreach ($states as $key => $state) {
                                if ($cnt == 1) {
                                    echo '<div class="col-md-12"><div class="col-md-12">
                                    <div class="row">
                                                <div class="checkbox">
                                                    <label>
                                                        <input type="checkbox" class="sp_ch_1" name="all_states" value="1" id="all_states"><label>All States
                                                    </label>
                                                 </div>
                                            </div>
                                           </div></div>';
                                }
                                echo ' <div class="col-md-6">';
                                echo '  <div class="col-md-12"><div class="row">
                                                    <div class="checkbox">
                                                        <label>
                                                            <input type="checkbox" class="special_state sp_ch_1" name="state_special_list[]" value="' . $state['Id'] . '"><label>' . $state['Name'] . '
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>';
                                echo ' </div>';
                                $cnt++;
                            }
                        }
                        ?>
                    </div>
                    <div class="error">
                        <?php echo form_error('store_special_list'); ?>
                    </div>
                </div>
                <?php
                if ($this -> session -> userdata('user_type') == 3) {
                    ?>
                    <div class="form-group col-xs-12">
                        <label for="store_formats">Store Formats <span>*</span></label>
                        <div id="store_special_format" name="store_special_format" class="checkbox_lists" style="max-height: 200px;">

                        </div>
                    </div>
                    <?php
                }
                ?>
                <div class="form-group col-xs-12">
                    <label for="store_special_list">Stores <span>*</span>&nbsp;&nbsp;<input type="text" placeholder="Search Store" id="store_search" /></label>
                    <div id="store_special_list" name="store_special_list" class="checkbox_lists" style="max-height: 200px;">

                    </div>
                </div>
                <?php
            }
            else{
                ?>
            <input class="special_state sp_ch_1" type="hidden" name="state_special_list[]" value="<?php echo $store_state['StateId'] ?>" />
            <input class="special_store sp_st_1" type="hidden" name="store_special_list[]" value="<?php echo $this -> session -> userdata('user_store_format_id').':'.$this -> session -> userdata('user_store_id') ?>" />
            <?php
            }
            ?>
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
    </div>
</div>
</form>
<script>
    $(function(){
        activateMenu('admin/specialmanagement');
    });
</script>