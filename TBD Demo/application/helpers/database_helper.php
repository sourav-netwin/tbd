<?php

/*
 * function that generate the action buttons edit, delete
 * This is just showing the idea you can use it in different view or whatever fits your needs
 */

function user_get_buttons($id) {
    $html = '<span class="actions">';
    $html .= '<a href="#" class="delete"><i class="fa fa-fw fa-med fa-trash-o" title="Delete user"></i></a>';
    $html .= '&nbsp;<a href="javascript:void(0)" data-href="' . base_url() . 'users/edit/' . $id . '" title="Edit user" class="edit" data-id="' . $id . '"><i class="fa fa-fw fa-med fa-edit"></i></a>';
    $html .= '&nbsp;<a href="' . base_url() . 'users/change_password/' . $id . '" title="Change password"><i class="fa fa-fw fa-med fa-unlock-alt"></i></a>';
    $html .= '</span>';

    return $html;
}

function tandc_get_buttons($id) {
    $html = '<span class="actions">';
    $html .= '<a href="#" class="delete"><i class="fa fa-fw fa-med fa-trash-o" title="Delete T & C"></i></a>';
    $html .= '&nbsp;<a href="javascript:void(0)" data-href="' . base_url() . 'tandcmanagement/edit/' . $id . '" title="Edit T & C" class="edit" data-id="' . $id . '"><i class="fa fa-fw fa-med fa-edit"></i></a>';
    $html .= '</span>';

    return $html;
}

function catlg_get_buttons($id) {
    $html = '<span class="actions">';
    $html .= '<a href="#" class="ins_special"><i class="fa fa-fw fa-med fa-plus" title="Add special"></i></a>';
    $html .= '</span>';

    return $html;
}

function special_get_buttons($id) {
    $html = '<span class="actions">';
    $html .= '<a href="javascript:void(0)" data-href="' . base_url() . 'specialmanagement/edit/' . $id . '" title="Edit Special" class="edit"><i class="fa fa-fw fa-med fa-edit"></i></a>';
    $html .= '&nbsp;<a href="#" class="delete"><i class="fa fa-fw fa-med fa-trash-o" title="Delete Special"></i></a>';
    
    $html .= '</span>';

    return $html;
}

function specials_get_select($id) {
    $html = '\'<select class="prd_qty"><option ';
    $html .= $id == 1 ? 'selected="selected"' : '';
    $html .= ' value="1">1</option>
            <option ';
    $html .= $id == 2 ? 'selected="selected"' : '';
    $html .= ' value="2">2</option>
            <option ';
    $html .= $id == 3 ? 'selected="selected"' : '';
    $html .= ' value="3">3</option>
            <option ';
    $html .= $id == 4 ? 'selected="selected"' : '';
    $html .= ' value="4">4</option>
            <option ';
    $html .= $id == 5 ? 'selected="selected"' : '';
    $html .= ' value="5">5</option>
            <option ';
    $html .= $id == 6 ? 'selected="selected"' : '';
    $html .= ' value="6">6</option>
            <option ';
    $html .= $id == 7 ? 'selected="selected"' : '';
    $html .= ' value="7">7</option>
            <option ';
    $html .= $id == 8 ? 'selected="selected"' : '';
    $html .= ' value="8">8</option>
            <option ';
    $html .= $id == 9 ? 'selected="selected"' : '';
    $html .= ' value="9">9</option>
            <option ';
    $html .= $id == 10 ? 'selected="selected"' : '';
    $html .= ' value="10">10</option>
            <option ';
    $html .= $id == 11 ? 'selected="selected"' : '';
    $html .= ' value="11">11</option>
            <option ';    
    $html .= $id == 12 ? 'selected="selected"' : '';
    $html .= ' value="12">12</option>
            <option ';
    $html .= $id == 13 ? 'selected="selected"' : '';
    $html .= ' value="13">13</option>
            <option ';
    $html .= $id == 14 ? 'selected="selected"' : '';
    $html .= ' value="14">14</option>
            <option ';
    $html .= $id == 15 ? 'selected="selected"' : '';
    $html .= ' value="15">15</option></select>\'';
    
    return $html;
}

function social_user_get_buttons($id) {
    
    $html = '<span class="actions">';
    $html .= '<a href="#" class="delete"><i class="fa fa-fw fa-med fa-trash-o" title="Delete user"></i></a>';
    $html .= '&nbsp;<a href="javascript:void(0)" data-href="' . base_url() . 'users/edit/' . $id . '" title="Edit user" class="edit" data-id="' . $id . '"><i class="fa fa-fw fa-med fa-edit"></i></a>';
    $html .= '&nbsp;<a href="javascript:void(0)" data-href="' . base_url() . 'users/showLoyalty/' . $id . '" title="Show Loyalty" class="loyalty" data-id="lyl_' . $id . '"><i class="fa fa-fw fa-med fa-eye"></i></a>';
    $html .= '</span>';

    return $html;
}

function get_action_buttons($id, $type) {
    $html = '<span class="actions">';
    $html .= '<a href="#" class="delete" title="Delete"><i class="fa fa-fw fa-med fa-trash-o"></i></a>';
    $html .= '&nbsp;<a href="javascript:void(0)" data-href="' . base_url() . $type . '/edit/' . $id . '" title="Edit" class="Edit"><i class="fa fa-fw fa-med fa-edit"></i></a>';

    $html .= '</span>';

    return $html;
}
function get_house_action_buttons($id, $type) {
    $html = '<span class="actions">';
    $html .= '<a href="#" class="delete" title="Delete"><i class="fa fa-fw fa-med fa-trash-o"></i></a>';
    $html .= '&nbsp;<a href="javascript:void(0)" data-href="' . base_url() . $type . '/edit/' . $id . '" title="Edit" class="Edit"><i class="fa fa-fw fa-med fa-edit"></i></a>';

    $html .= '</span>';

    return $html;
}

function get_image($image, $type) {
    $ci = & get_instance();
    switch ($type) {
        case 'Retailer':
            $folder_path = RETAILER_IMAGE_PATH . 'medium/';
            break;

        case 'Product':
            $folder_path = PRODUCT_IMAGE_PATH . 'medium/';
            break;
    }

    $html = '<img src="' . $ci -> config -> item('front_url') . $folder_path . $image . '" width="120px;">';

    return $html;
}

function get_edit_button($id, $type) {
    $html = '<span class="actions">';
    $html .= '<a href="javascript:void(0)" data-href="' . base_url() . $type . '/edit/' . $id . '" class="edit"><i class="fa fa-fw fa-med fa-edit"></i></a>';
    $html .= '</span>';

    return $html;
}

function get_store_format_buttons($id, $store_count, $retailer_id) {
    $html = '<span><a href="' . base_url() . 'stores/index/' . $retailer_id . '/' . $id . '"> <span class="badge">' . $store_count . '</span></a></span>';
    return $html;
}

function get_user_count($user_count, $type, $id) {
    $html = '<span><a href="'.  base_url().'stores/add_store_user/'.$id.'" > <span class="badge">' . $user_count . '</span></a></span>';
    return $html;
}

function loyalty_tandc_get_buttons($id) {
    $html = '<span class="actions">';
    $html .= '<a href="#" class="delete"><i class="fa fa-fw fa-med fa-trash-o" title="Delete T & C"></i></a>';
    $html .= '&nbsp;<a href="javascript:void(0)" data-href="' . base_url() . 'loyaltyterms/edit/' . $id . '" title="Edit T & C" class="edit" data-id="' . $id . '"><i class="fa fa-fw fa-med fa-edit"></i></a>';
    $html .= '</span>';

    return $html;
}

function get_loyalty_order_action_buttons($id, $type) {
    $html = '<span class="actions">';    
    $html .= '&nbsp;<a href="javascript:void(0)" data-href="' . base_url() . $type . '/edit/' . $id . '" title="Edit" class="Edit"><i class="fa fa-fw fa-med fa-edit"></i></a>';
    $html .= '</span>';

    return $html;
}


function loyaltypoints_get_buttons($id) {
    
    $html = '<span class="actions">';        
    $html .= '&nbsp;<a href="javascript:void(0)" data-href="' . base_url() . 'loyaltypoints/showLoyalty/' . $id . '" title="Show Loyalty" class="loyalty" data-id="lyl_' . $id . '"><i class="fa fa-fw fa-med fa-eye"></i></a>';
    $html .= '</span>';

    return $html;
}

function get_customspecials_action_buttons($id, $type) {
    $html = '<span class="actions">';
    $html .= '&nbsp;<a href="javascript:void(0)" data-href="' . base_url() . $type . '/show_combo_products/' . $id . '" title="Combo Offer" class="combo_offer" id="' . $id . '" ><i class="fa fa-fw fa-med fa-plus-circle"></i></a>';
    $html .= '</span>';

    return $html;
}

function combo_products_get_select($id) {
    $html = '\'<select class="comboprod_qty"><option ';
    $html .= $id == 1 ? 'selected="selected"' : '';
    $html .= ' value="1">1</option>
            <option ';
    $html .= $id == 2 ? 'selected="selected"' : '';
    $html .= ' value="2">2</option>
            <option ';
    $html .= $id == 3 ? 'selected="selected"' : '';
    $html .= ' value="3">3</option>
            <option ';
    $html .= $id == 4 ? 'selected="selected"' : '';
    $html .= ' value="4">4</option>
            <option ';
    $html .= $id == 5 ? 'selected="selected"' : '';
    $html .= ' value="5">5</option>
            <option ';
    $html .= $id == 6 ? 'selected="selected"' : '';
    $html .= ' value="6">6</option>
            <option ';
    $html .= $id == 7 ? 'selected="selected"' : '';
    $html .= ' value="7">7</option>
            <option ';
    $html .= $id == 8 ? 'selected="selected"' : '';
    $html .= ' value="8">8</option>
            <option ';
    $html .= $id == 9 ? 'selected="selected"' : '';
    $html .= ' value="9">9</option>
            <option ';
    $html .= $id == 10 ? 'selected="selected"' : '';
    $html .= ' value="10">10</option>
            <option ';
    $html .= $id == 11 ? 'selected="selected"' : '';
    $html .= ' value="11">11</option>
            <option ';    
    $html .= $id == 12 ? 'selected="selected"' : '';
    $html .= ' value="12">12</option>
            <option ';
    $html .= $id == 13 ? 'selected="selected"' : '';
    $html .= ' value="13">13</option>
            <option ';
    $html .= $id == 14 ? 'selected="selected"' : '';
    $html .= ' value="14">14</option>
            <option ';
    $html .= $id == 15 ? 'selected="selected"' : '';
    $html .= ' value="15">15</option></select>\'';
    
    return $html;
}