<!-- /.row -->

<div class="row">
    <?php echo form_open_multipart(front_url() . 'admin/specialmanagement/edit_post/' . $Id, array('id' => 'special_edit_form', 'class' => 'form-horizontal', ' autocomplete' => 'off')); ?>
    <div class="col-xs-5 col-xs-offset-1">

        <div class="form-group" id="special_name_cont">
            <div class="row">
                <div class="col-md-12">
                    <label for="special_name" >Special Name <span>*</span></label>
                    <input type="text" class="form-control" id="special_name" name="special_name" placeholder="Special Name" value="<?php echo $SpecialName; ?>">
                    <div class="error">
                        <?php echo form_error('special_name'); ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="btn-group profile_image_group" style="max-width: unset">
                <?php
                $image_full = '';
                if ($SpecialBanner) {
                    $image_full = 'style="background: url(\'' . front_url() . SPECIAL_IMAGE_PATH . 'medium/' . $SpecialBanner . '\') no-repeat scroll 0% 0% / 340px 144px;\'';
                }
                ?>
                <label for="inputImage" class="btn btn-primary btn-xs spbanner_image" <?php echo $image_full; ?>>
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
                        <input type="text" class="form-control" name="price_from" id="price_from" placeholder="Price From" value="<?php echo $SpecialFrom; ?>">
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
                        <input type="text" class="form-control" name="price_to" id="price_to" placeholder="Price To" value="<?php echo $SpecialTo; ?>">
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
                        <input type="checkbox" value="1" name="regional_special" <?php if( $IsRegional ==1 ) { ?> checked="checked" <?php } ?> />&nbsp;Regional Special<br />
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
                    <!--<textarea class="form-control" id="special_terms" name="special_terms" ><?php //echo $TermsAndConditions            ?></textarea>-->
                    <?php
                    $tc_arr = [];
                    if ($TermsAndConditions) {
                        $tc_arr = explode(',', $TermsAndConditions);
                    }
                    if ($terms) {
                        foreach ($terms as $term) {
                            if (in_array($term['Id'], $tc_arr)) {
                                ?>
                                <input type="checkbox" value="<?php echo $term['Id'] ?>" name="sp_t_and_c[]" checked="checked" />&nbsp;<?php echo $term['TermsText'] ?><br />
                                <?php
                            }
                            else {
                                ?>
                                <input type="checkbox" value="<?php echo $term['Id'] ?>" name="sp_t_and_c[]" />&nbsp;<?php echo $term['TermsText'] ?><br />
                                <?php
                            }
                        }
                    }
                    ?>
                    <div class="error">
                        <?php echo form_error('special_terms'); ?>
                    </div>
                </div>
            </div>
        </div>


        <div class="form-group">
            <div class="row">
                <div class="col-md-12">
                    <button type="submit" class="btn btn-primary btn-xs block full-width m-b">Update</button>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xs-5">
        <?php
        if ($this -> session -> userdata('user_type') != 6) {
            ?>
            <div class="form-group col-xs-12">
                <label for="states">States <span>*</span></label>
                <div id="state_list" name="state_list" class="checkbox_lists" style="max-height: 200px;">
                    <?php
                    if ($states) {
                        $cnt = 1;

                        foreach ($states as $key => $state) {
                            if ($cnt == 1) {
                                $all_checked = '';
                                $all_states ? $all_checked = 'checked="checked"' : '';
                                echo '<div class="col-md-12"><div class="col-md-12">
                                    <div class="row">
                                                <div class="checkbox">
                                                    <label>
                                                        <input type="checkbox" ' . $all_checked . ' class="sp_ch_1" name="all_states" value="1" id="all_states"><label>All States
                                                    </label>
                                                 </div>
                                            </div>
                                           </div></div>';
                            }
                            $checked = '';
                            if ($selected_state_list) {
                                in_array($state['Id'], $selected_state_list) ? $checked = 'checked="checked"' : '';
                            }
                            $all_states ? $checked = 'checked="checked"' : '';
                            echo ' <div class="col-md-6">';
                            echo '  <div class="col-md-12"><div class="row">
                                                    <div class="checkbox">
                                                        <label>
                                                            <input type="checkbox" class="special_state sp_ch_1" ' . $checked . ' name="state_special_list[]" value="' . $state['Id'] . '"><label>' . $state['Name'] . '
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
                        <?php
                        if ($store_formats) {
                            $cnt = 1;
                            foreach ($store_formats as $store_format) {
                                if ($cnt == 1) {
                                    $all_checked = '';
                                    $all_store_types ? $all_checked = 'checked="checked"' : '';
                                    echo '<div class="col-md-12"><div class="col-md-12">
                                    <div class="row">
                                                <div class="checkbox">
                                                    <label>
                                                        <input type="checkbox" class="sp_sf_1" name="all_store_formats" ' . $all_checked . ' value="1" id="all_store_formats"><label>All Stores
                                                    </label>
                                                 </div>
                                            </div>
                                           </div></div>';
                                }
                                $checked = '';
                                if ($selected_store_type_list) {

                                    in_array($store_format['Id'], $selected_store_type_list) ? $checked = 'checked="checked"' : '';
                                }
                                $all_store_types ? $checked = 'checked="checked"' : '';
                                echo ' <div class="col-md-6">';
                                echo '  <div class="col-md-12"><div class="row">
                                                    <div class="checkbox">
                                                        <label>
                                                            <input type="checkbox" class="special_store_format sp_sf_1" ' . $checked . ' name="store_special_format_list[]" value="' . $store_format['Id'] . '"><label>' . $store_format['StoreType'] . '
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
                </div>
                <?php
            }
            ?>

            <div class="form-group col-xs-12">
                <label for="store_formats">Stores <span>*</span>&nbsp;&nbsp;<input type="text" placeholder="Search Store" id="store_search" /></label>
                <div id="store_special_list" name="store_special_list" class="checkbox_lists" style="max-height: 200px;">
                    <?php
                    if ($stores) {
                        $cnt = 1;
                        foreach ($stores as $store) {
                            $store_arr = explode(':', $store['Id']);
                            if ($cnt == 1) {
                                $all_checked = '';
                                $all_stores ? $all_checked = 'checked="checked"' : '';
                                echo '<div class="col-md-12"><div class="col-md-12">
                                    <div class="row">
                                                <div class="checkbox">
                                                    <label>
                                                        <input type="checkbox" class="sp_st_1" name="all_stores" ' . $all_checked . ' value="1" id="all_stores"><label>All Stores
                                                    </label>
                                                 </div>
                                            </div>
                                           </div></div>';
                            }
                            $checked = '';
                            if ($selected_store_list) {
                                in_array($store_arr[1], $selected_store_list) ? $checked = 'checked="checked"' : '';
                            }
                            $all_stores ? $checked = 'checked="checked"' : '';
                            echo ' <div class="col-md-12">';
                            echo '  <div class="col-md-12"><div class="row">
                                                    <div class="checkbox">
                                                        <label>
                                                            <input type="checkbox" class="special_store sp_st_1" ' . $checked . ' name="store_special_list[]" value="' . $store['Id'] . '"><label>' . $store['StoreName'] . '
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
            </div>
            <?php
        }
        else {
            ?>
            <input class="special_state sp_ch_1" type="hidden" name="state_special_list[]" value="<?php echo $store_state['StateId'] ?>" />
            <input class="special_store sp_st_1" type="hidden" name="store_special_list[]" value="<?php echo $this -> session -> userdata('user_store_format_id').':'.$this -> session -> userdata('user_store_id') ?>" />
            <?php
        }
        ?>
    </div>

    <!-- Add Modal -->
    <input type="hidden" name="image-x" id="image-x">
    <input type="hidden" name="image-y" id="image-y">
    <input type="hidden" name="image-width" id="image-width">
    <input type="hidden" name="image-height" id="image-height">

    <!-- Add Modal -->
    </form>
</div>