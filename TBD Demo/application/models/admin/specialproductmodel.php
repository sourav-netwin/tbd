<?php

/*
 * Author: Name:PM
 * Purpose: Model for controlling database interactions regarding the content.
 * Date:14-09-2015
 * Dependency: None
 */

class Specialproductmodel extends CI_Model {
    /*
     * Purpose: Constructor.
     * Date: 14-09-2015
     * Input Parameter: None
     * Output Parameter: None
     */

    public function __construct() {
        parent::__construct();
    }

    public function get_special_product_details1($id) {
        $this -> db -> select('products.ProductName,productspecials.*,stores.StreetAddress,stores.City,state.Name');
        $this -> db -> from('productspecials');
        $this -> db -> join('products', 'products.Id = productspecials.ProductId');
        $this -> db -> join('stores', 'stores.Id = productspecials.StoreId', 'left');
        $this -> db -> join('state', 'state.Id = stores.StateId', 'left');
        $this -> db -> where('productspecials.Id', $id);
        $this -> db -> limit(1);
        $query = $this -> db -> get();

        if ($query -> num_rows() == 1) {
            return $query -> row_array();
        }
        else {
            return FALSE;
        }
    }

    public function get_special_product_details($id) {
        $this -> db -> select('retailers.CompanyName, stores.StoreName, stores.Id as StoreId, storestypes.StoreType, storestypes.Id as StoreTypeId, main_parent_category.CategoryName AS main_parent_cat, main_parent_category.Id as main_parent_catId,  products.ProductName, productspecials.*');
        $this -> db -> from('productspecials');
        $this -> db -> join('products', 'products.Id = productspecials.ProductId');
        $this -> db -> join('retailers', 'retailers.Id = productspecials.RetailerId');
        $this -> db -> join('stores', 'stores.Id = productspecials.StoreId', 'left');
        $this -> db -> join('storestypes', 'storestypes.Id = stores.StoreTypeId');
        $this -> db -> join('categories', 'categories.Id = products.CategoryId');
        $this -> db -> join('categories parent_category', 'parent_category.Id = categories.ParentCategory');
        $this -> db -> join('categories main_parent_category', 'main_parent_category.Id = parent_category.ParentCategory');
        $this -> db -> where('productspecials.Id', $id);
        $this -> db -> limit(1);
        $query = $this -> db -> get();

        if ($query -> num_rows() == 1) {
            return $query -> row_array();
        }
        else {
            return FALSE;
        }
    }

    public function update_special_product($id, $data) {
        $this -> db -> where('SpecialId', $id);
        //$this -> db -> where('Id', $id);
        if ($this -> db -> update('productspecials', $data)) {           
            return TRUE;
        }
        return FALSE;
    }

    /* Added on 22 March 2017(MK): Update price of the all records for the particular productId from the special. */
    public function update_special_product_information($where, $data) {
        $this -> db -> where($where);
        if ($this -> db -> update('productspecials', $data)) {
            //echo $this -> db -> last_query();die; 
            return TRUE;
        }
        return FALSE;
    }
    
    public function update_store_price($where, $data) {

        $this -> db -> where($where);
        if ($this -> db -> update('storeproducts', $data)) {
            return TRUE;
        }
        return FALSE;
    }

    public function add_special_product($insert_data) {
        $this -> db -> insert('productspecials', $insert_data);
        return $this -> db -> insert_id();
    }

    public function delete_product($product_id) {

        $this -> db -> delete('productspecials', array('id' => $product_id));
    }

    public function change_status($store_id, $status) {

        $data = array('IsActive' => $status);
        $this -> db -> where('Id', $store_id);
        $this -> db -> update('productspecials', $data);

        return TRUE;
    }

    public function approve_product($product_id) {

        $data = array('IsApproved' => 1, 'ApprovedBy' => $this -> session -> userdata('user_id'));
        $this -> db -> where('Id', $product_id);
        if ($this -> db -> update('productspecials', $data)) {
            return TRUE;
        }
        return TRUE;
    }

    public function validate_offer($data, $store_list) {
        $this -> db -> where($data);
        $this -> db -> where_in($store_list);
        $this -> db -> from('productspecials', $data);

        $query = $this -> db -> get();
        if ($query -> num_rows() > 0) {
            return FALSE;
        }
        else {
            return TRUE;
        }
    }

    public function validate_offer_any($data) {
        $this -> db -> select('Id');
        $this -> db -> where($data);
        $this -> db -> from('productspecials');

        $query = $this -> db -> get();
        if ($query -> num_rows() > 0) {
            return $query -> row_array();
        }
        else {
            return FALSE;
        }
    }

    public function validate_store_product($product_id, $retailer_id, $store) {
        $this -> db -> where(array(
            'RetailerId' => $retailer_id,
            'StoreId' => $store,
            'ProductId' => $product_id
        ));

        $this -> db -> from('storeproducts');

        $query = $this -> db -> get();

        if ($query -> num_rows() <= 0) {

            // Get the product price
            $this -> db -> select('products.Id,products.RRP');

            $this -> db -> where(
                array(
                    'products.IsRemoved' => 0
            ));
            $this -> db -> where('products.Id', $product_id);
            $query = $this -> db -> get('products');

            $products = $query -> row_array();

            $insert_data = array(
                'ProductId' => $product_id,
                'RetailerId' => $retailer_id,
                'StoreId' => $store,
//                'StoreTypeId' => $store_format_id,
                'Price' => number_format($products['RRP']),
                'CreatedBy' => $this -> session -> userdata('user_id'),
                'CreatedOn' => date('Y-m-d H:i:s'),
                'IsActive' => 1
            );

            $this -> db -> insert('storeproducts', $insert_data);
            return $this -> db -> insert_id();
        }
    }

    public function get_default_price($product) {
        $this -> db -> select('RRP')
            -> from('products')
            -> where('Id', $product);
        $query = $this -> db -> get();
        if ($query -> num_rows() > 0) {
            return $query -> row_array();
        }
        else {
            return FALSE;
        }
    }

    public function get_special_details() {
        $current_retailer = '';
        $current_storetype = '';
        $current_store = '';
        $retailers = '';
        $storrTypes = '';
        $stores = '';

//        if ($this -> session -> userdata('user_type') == 3) {
//            $current_retailer = $this -> session -> userdata('user_retailer_id');
//        }
//        if ($this -> session -> userdata('user_type') == 5) {
//            $current_retailer = $this -> session -> userdata('user_retailer_id');
//            $current_storetype = $this -> session -> userdata('user_store_format_id');
//        }
//        if ($this -> session -> userdata('user_type') == 6) {
//            $current_retailer = $this -> session -> userdata('user_retailer_id');
//            $current_storetype = $this -> session -> userdata('user_store_format_id');
//            $current_store = $this -> session -> userdata('user_store_id');
//        }

        $this -> db -> _protect_identifiers = false;
        $this -> db -> select('a.*')
            -> from('specials as a')
            -> join('special_state as b', 'b.SpecialId = a.Id', 'left')
            -> join('special_stores as c', 'c.SpecialId = a.Id', 'left')
            -> join('state as d', 'd.Id = b.StateId or b.AllStates = 1', 'left')
            -> join('stores as e', 'e.Id = c.StoreId or c.AllStores = 1', 'left')
            -> join('retailers as f', 'f.Id = e.RetailerId', 'left')
            -> join('storestypes as g', 'g.Id = e.StoreTypeId', 'left')
            -> where('a.IsActive', 1)
            -> where('a.IsRemoved', 0)
            -> where('now() <= a.SpecialTo');
        if ($this -> session -> userdata('user_type') == 3) {
           $this -> db -> where('(f.Id = ' . $this -> session -> userdata('user_retailer_id') . ' and a.IsRetailer = 1)', NULL, FALSE);
        }
        if ($this -> session -> userdata('user_type') == 5) {
            $this -> db -> where(' (f.Id = ' . $this -> session -> userdata('user_retailer_id') . ' or g.Id = ' . $this -> session -> userdata('user_store_format_id') . ' and a.IsStoreType = 1)', NULL, FALSE);
            //$this -> datatables -> where('g.Id', $this -> session -> userdata('user_store_format_id'));
        }
        if ($this -> session -> userdata('user_type') == 6) {
            $this -> db -> where('(f.Id = ' . $this -> session -> userdata('user_retailer_id') . ' or g.Id = ' . $this -> session -> userdata('user_store_format_id') . ' or e.Id = ' . $this -> session -> userdata('user_store_id') . ' and a.IsStore = 1)', NULL, FALSE);
            //$this -> datatables -> where('g.Id', $this -> session -> userdata('user_store_format_id'));
            //$this -> datatables -> where('e.Id', $this -> session -> userdata('user_store_id'));
        }
        $this -> db -> where('c.RetailerId', $this -> session -> userdata('user_retailer_id'));
        $this -> db -> group_by('a.Id');
        $query = $this -> db -> get();
       //echo $this -> db -> last_query();die;
        if ($query -> num_rows() > 0) {
            return $query -> result_array();
        }
        else {
            return FALSE;
        }
    }

    public function get_special_details_id($id) {
        $this -> db -> select('*')
            -> from('specials')
            -> where('Id', $id)
            //-> where('IsActive', 1)
            -> where('IsRemoved', 0)
            -> limit(1);
        $query = $this -> db -> get();
        if ($query -> num_rows() > 0) {
            return $query -> row_array();
        }
        else {
            return FALSE;
        }
    }

    public function get_product_special_details_id($id) {
        $this -> db -> select('*')
            -> from('productspecials')
            -> where('Id', $id)
            -> where('IsActive', 1)
            -> limit(1);
        $query = $this -> db -> get();
        if ($query -> num_rows() > 0) {
            return $query -> row_array();
        }
        else {
            return FALSE;
        }
    }

    public function insert_special_data($data) {
        $table = 'specials';
        if ($this -> db -> insert($table, $data)) {
            return $this -> db -> insert_id();
        }
        return FALSE;
    }

    public function update_special_data($update_data, $special_sel) {
        $table = 'specials';
        $this -> db -> where('Id', $special_sel);
        if ($this -> db -> update($table, $update_data)) {
            $update_new_data = array(
                'PriceAppliedFrom' => $update_data['SpecialFrom'],
                'PriceAppliedTo' => $update_data['SpecialTo']
            );
            $this -> db -> where('SpecialId', $special_sel);
            $this -> db -> update('productspecials', $update_new_data);
            return TRUE;
        }
        return FALSE;
    }

    public function get_special_data($special_sel) {
        $this -> db -> select('*')
            -> from('specials')
            -> where('Id', $special_sel)
            -> where('IsActive', 1)
            -> where('IsRemoved', 0)
            -> limit(1);
        $query = $this -> db -> get();
        if ($query -> num_rows() > 0) {
            return $query -> row_array();
        }
        else {
            return FALSE;
        }
    }

    public function get_main_categories() {
        $this -> db -> select('Id, CategoryName')
            -> from('categories')
            -> where('ParentCategory', 0)
            -> where('IsActive', 1)
            -> where('IsRemoved', 0);
        $query = $this -> db -> get();

        if ($query -> num_rows() >= 1) {
            return $query -> result_array();
        }
        else {
            return TRUE;
        }
    }

    public function get_parent_categories($main_cat) {
        $this -> db -> select('Id, CategoryName')
            -> from('categories')
            -> where('ParentCategory', $main_cat)
            -> where('IsActive', 1)
            -> where('IsRemoved', 0);
        $query = $this -> db -> get();

        if ($query -> num_rows() >= 1) {
            return $query -> result_array();
        }
        else {
            return TRUE;
        }
    }

    public function get_store_product_details($product_id) {
        $this -> db -> select('a.ProductId,b.ProductName, a.RetailerId,c.CompanyName, a.StoreId,d.StoreName, a.StoreTypeId,e.StoreType')
            -> from('storeproducts as a')
            -> join('products as b', 'a.ProductId = b.Id')
            -> join('retailers as c', 'a.RetailerId = c.Id')
            -> join('stores as d', 'a.StoreId = d.Id')
            -> join('storestypes as e', 'a.StoreTypeId = e.Id')
            -> where('b.Id', $product_id)
            -> where('a.IsActive', 1)
            -> where('a.IsRemoved', 0)
            -> limit(1);
        $query = $this -> db -> get();
        if ($query -> num_rows() >= 1) {
            return $query -> row_array();
        }
        else {
            return TRUE;
        }
    }

    public function get_specials_details($special_id) {
        $this -> db -> select('*')
            -> from('specials')
            -> where('Id', $special_id)
            -> where('IsActive', 1)
            -> where('IsRemoved', 0)
            -> limit(1);
        $query = $this -> db -> get();

        if ($query -> num_rows() >= 1) {
            return $query -> row_array();
        }
        else {
            return TRUE;
        }
    }

    public function delete_special($id) {
        $where = array(
            'Id' => $id
        );
        if ($this -> db -> delete('specials', $where)) {
            $where = array(
                'SpecialId' => $id
            );
            $this -> db -> delete('productspecials', $where);
            return TRUE;
        }
        return FALSE;
    }

    public function delete_special_product($id) {
        $where = array(
            'Id' => $id
        );
        if ($this -> db -> delete('productspecials', $where)) {
            return TRUE;
        }
        return FALSE;
    }
    
    /* Added on 22 March 2017(MK): Delete all records for the particular productId from the special. */
    public function delete_special_product_from_stores($productId, $retailerId, $specialId) {
        $where = array(
            'ProductId' => $productId,
            'RetailerId' => $retailerId,
            'SpecialId' => $specialId
        );
        if ($this -> db -> delete('productspecials', $where)) {
            return TRUE;
        }
        return FALSE;
    }

    public function getSpecialProductDetails($special_id) {
        $this -> db -> select('a.ProductId, b.ProductName, a.RetailerId, c.CompanyName, a.StoreId, d.StoreName, a.StoreTypeId, a.SpecialId, a.PriceForAllStores, f.Price as ActualPrice, a.SpecialQty, a.SpecialPrice, a.PriceAppliedFrom, a.PriceAppliedTo')
            -> from('productspecials as a')
            -> join('products as b', 'a.ProductId = b.Id')
            -> join('retailers as c', 'a.RetailerId = c.Id')
            -> join('stores as d', 'a.StoreId = d.Id')
            -> join('storestypes as e', 'a.StoreTypeId = e.Id')
            -> join('storeproducts as f', 'f.ProductId=b.Id and f.RetailerId = c.Id and f.StoreId = d.Id and f.StoreTypeId = e.Id')
            -> where('');
    }

    public function get_specials() {
        $this -> db -> select('Id, SpecialName')
            -> from('specials')
            -> where('IsActive', 1)
            -> where('IsRemoved', 0);
        $query = $this -> db -> get();

        if ($query -> num_rows() > 0) {
            return $query -> result_array();
        }
        else {
            return FALSE;
        }
    }

    public function get_device_user_ids() {
        $this -> db -> select('a.UserId,b.PrefLatitude,b.PrefLongitude,b.PrefDistance,b.state,b.FirstName,b.LastName')
            -> from('userdevices as a')
            -> join('users as b', 'b.Id = a.UserId')
            -> where('b.IsActive', 1)
            -> where('b.IsRemoved', 0)               
            -> group_by('a.UserId');
        $query = $this -> db -> get();
        if ($query -> num_rows() > 0) {
            return $query -> result_array();
        }
        return FALSE;
    }

    /*
     *   -> where('a.StateId', $stateId) // Condition added 06 March 2017    
     */
    public function get_device_user_stores($latitude, $longitude, $distance, $store_array,$stateId=0) {
        $this -> db -> select('MIN((6371 * acos( cos( radians(' . $latitude . ') ) * cos( radians( a.Latitude ) ) * cos( radians( a.Longitude ) - 
radians(' . $longitude . ') ) + sin( radians(' . $latitude . ') ) * sin( radians( a.Latitude ) ) ) ))  AS distance,a.StoreName,a.Id, a.StateId', FALSE)
            -> from('stores as a')
            //-> join('retailers as b', 'b.Id = a.RetailerId')
            //-> join('storestypes as c', 'c.Id = a.StoreTypeId')
            -> where('a.StateId', $stateId) 
            -> where_in('a.Id', $store_array)
            -> group_by('a.Id')
            -> having('distance <= ' . $distance)
            -> order_by('distance');
        $query = $this -> db -> get();
        //echo $this -> db -> last_query();die;
        if ($query -> num_rows() > 0) {
            $resultl_array = $query -> result_array();
            return $resultl_array;
            //return $resultl_array[0]['StoreName'];
        }
        return FALSE;
    }

    public function get_device_tokens($users) {
        $this -> db -> select('UserId,DeviceId')
            -> from('userdevices')
            -> where_in('UserId', $users);
        $query = $this -> db -> get();
        if ($query -> num_rows() > 0) {
            return $query -> result_array();
        }
        return FALSE;
    }


    public function get_special_pending_count() {
        $this -> db -> select('a.Id,a.SpecialName,count(a.Id) as count', false)
            -> from('specials as a')
            -> join('productspecials as b', 'a.Id = b.SpecialId')
            -> where('b.IsApproved', 0)
            -> group_by('a.Id');
        $query = $this -> db -> get();
        if ($query -> num_rows() > 0) {
            return $query -> result_array();
        }
        return FALSE;
    }

//    public function get_special_pending_details($special_id) {
//        $this -> db -> select('ProductId, RetailerId, StoreId, StoreTypeId')
//            -> from('productspecials')
//            -> where('SpecialId', $special_id)
//            -> where('IsApproved', 0)
//            -> where('IsActive', 1);
//        $query = $this -> db -> get();
//        if ($query -> num_rows() > 0) {
//            return $query -> result_array();
//        }
//        return FALSE;
//    }

    public function get_special_pending_details($special_id) {
        $this -> db -> select('a.ProductId,b.ProductName,a.RetailerId,c.CompanyName,a.StoreTypeId,d.StoreType,a.StoreId,e.StoreName,e.Latitude,e.Longitude,a.SpecialPrice,a.SpecialQty,f.SpecialName,f.SpecialFrom,f.SpecialTo')
            -> from('productspecials as a')
            -> join('products as b', 'b.Id = a.ProductId')
            -> join('retailers as c', 'c.Id = a.RetailerId')
            -> join('storestypes as d', 'd.Id = a.StoreTypeId')
            -> join('stores as e', 'e.Id = a.StoreId')
            -> join('specials as f', 'f.Id = a.SpecialId')
            -> where('a.SpecialId', $special_id)
            -> where('a.IsApproved', 0);
        $query = $this -> db -> get();
        if ($query -> num_rows() > 0) {
            return $query -> result_array();
        }
        return FALSE;
    }

    public function get_special_pending_details_single($id) {
        $this -> db -> select('a.ProductId,b.ProductName,a.RetailerId,c.CompanyName,a.StoreTypeId,d.StoreType,a.StoreId,e.StoreName,a.SpecialPrice,a.SpecialQty,f.SpecialName')
            -> from('productspecials as a')
            -> join('products as b', 'b.Id = a.ProductId')
            -> join('retailers as c', 'c.Id = a.RetailerId')
            -> join('storestypes as d', 'd.Id = a.StoreTypeId')
            -> join('stores as e', 'e.Id = a.StoreId')
            -> join('specials as f', 'f.Id = a.SpecialId')
            -> where('a.Id', $id)
            -> where('a.IsApproved', 0);
        $query = $this -> db -> get();
        if ($query -> num_rows() > 0) {
            return $query -> row_array();
        }
        return FALSE;
    }

    public function validate_default_price($ProductId, $RetailerId, $StoreTypeId, $StoreId, $one_item_price) {
        $this -> db -> select('Price')
            -> from('storeproducts')
            -> where(array(
                'RetailerId' => $RetailerId,
                'StoreTypeId' => $StoreTypeId,
                'StoreId' => $StoreId,
                'ProductId' => $ProductId,
            ))
            -> limit(1);
        $query = $this -> db -> get();
        if ($query -> num_rows() == 1) {
            $data_array = $query -> row_array();
            if ($data_array['Price'] > $one_item_price) {
                return TRUE;
            }
            else {
                return FALSE;
            }
        }
        else {
            return FALSE;
        }
    }

    public function get_terms_details() {
        $this -> db -> select('*')
            -> from('special_terms')
            -> where(
                array(
                    'IsActive' => 1,
                    'IsRemoved' => 0
                )
        );
        $query = $this -> db -> get();
        if ($query -> num_rows() > 0) {
            return $query -> result_array();
        }
        return FALSE;
    }

    public function get_state_stores($all_states, $state_list, $search_string = '', $all_store_format = FALSE, $store_format_list = array()) {
        $this -> db -> select('concat(a.StoreTypeId,\':\',a.Id) as Id, a.StoreName', FALSE)
            -> from('stores as a')
            -> join('state as b', 'a.StateId = b.Id')
            -> where(array(
                'a.IsActive' => 1,
                'a.IsRemoved' => 0,
                'b.IsActive' => 1,
                'b.IsRemoved' => 0
            ));

        if (!$all_states) {
            $this -> db -> where_in('a.StateId', $state_list);
        }
        if ($this -> session -> userdata('user_type') == 6) {
            $this -> db -> where('a.Id', $this -> session -> userdata('user_store_id'));
        }
        if ($this -> session -> userdata('user_type') == 5) {
            $this -> db -> where('( a.RetailerId = ' . $this -> session -> userdata('user_retailer_id') . ' and a.StoreTypeId = ' . $this -> session -> userdata('user_store_format_id') . ')', NULL, FALSE);
        }
        if ($this -> session -> userdata('user_type') == 3) {
            if (!empty($store_format_list)) {
                $this -> db -> where_in('a.StoreTypeId', $store_format_list);
            }
            $this -> db -> where('a.RetailerId', $this -> session -> userdata('user_retailer_id'));
        }

        if ($search_string != '') {
            $this -> db -> like('a.StoreName', $search_string);
        }

        $query = $this -> db -> get();
//        echo $this -> db -> last_query();
//        die;
        if ($query -> num_rows() > 0) {
            return $query -> result_array();
        }
        return FALSE;
    }

    public function get_state_storeformats($all_states, $state_list) {
        $this -> db -> select('a.Id, a.StoreType')
            -> from('storestypes as a')
            -> join('retailers as b', 'b.Id = a.RetailerId')
            -> join('state as c', 'c.Id = b.StateId')
            -> where(array(
                'b.Id' => $this -> session -> userdata('user_retailer_id'),
                'a.IsActive' => 1,
                'a.IsRemoved' => 0,
                'b.IsActive' => 1,
                'b.IsRemoved' => 0,
                'c.IsActive' => 1,
                'c.IsRemoved' => 0
            ));

        if (!$all_states && $this -> session -> userdata('user_type') != 3) {
            $this -> db -> where_in('c.Id', $state_list);
        }

        $query = $this -> db -> get();
//        echo $this -> db -> last_query();
//        die;
        if ($query -> num_rows() > 0) {
            return $query -> result_array();
        }
        return FALSE;
    }

    public function insert_special_state($insert_data) {
        if ($this -> db -> insert('special_state', $insert_data)) {
            return TRUE;
        }
        return FALSE;
    }

    public function insert_special_state_batch($insert_data) {
        if ($this -> db -> insert_batch('special_state', $insert_data)) {
            return TRUE;
        }
        return FALSE;
    }

    public function insert_special_store($insert_data) {
        if ($this -> db -> insert('special_stores', $insert_data)) {
            return TRUE;
        }
        return FALSE;
    }

    public function insert_special_store_batch($insert_data) {
        if ($this -> db -> insert_batch('special_stores', $insert_data)) {
            return TRUE;
        }
        return FALSE;
    }

    public function delete_special_state($where) {
        if ($this -> db -> delete('special_state', $where)) {
            return TRUE;
        }
        return FALSE;
    }

    public function delete_special_store($where) {
        if ($this -> db -> delete('special_stores', $where)) {
            return TRUE;
        }
        return FALSE;
    }

    public function get_selected_states($id) {
        $this -> db -> select('StateId,AllStates')
            -> from('special_state')
            -> where(array(
                'SpecialId' => $id,
                'IsActive' => 1,
                'IsRemoved' => 0
            ));
        $query = $this -> db -> get();
        if ($query -> num_rows() > 0) {
            return $query -> result_array();
        }
        return FALSE;
    }

    public function get_selected_stores($id) {
        $this -> db -> select('StoreId,AllStores')
            -> from('special_stores')
            -> where(array(
                'SpecialId' => $id,
                'IsActive' => 1,
                'IsRemoved' => 0
            ));
        $query = $this -> db -> get();
        if ($query -> num_rows() > 0) {
            return $query -> result_array();
        }
        return FALSE;
    }

    public function get_selected_store_types($id) {
        $this -> db -> select('distinct StoreTypeId as StoreTypeId,AllStoreTypes', FALSE)
            -> from('special_stores')
            -> where(array(
                'SpecialId' => $id,
                'IsActive' => 1,
                'IsRemoved' => 0
            ));
        $query = $this -> db -> get();
        if ($query -> num_rows() > 0) {
            return $query -> result_array();
        }
        return FALSE;
    }

    //Testing
    public function get_retailer_stores($retailer_id, $store_id = '') {
        $this -> db -> select('Id, RetailerId, StoreTypeId')
            -> from('stores')
            -> where(array(
                'RetailerId' => $retailer_id,
                'IsActive' => 1,
                'IsRemoved' => 0
            ));
        if ($store_id) {
            $this -> db -> where('Id != ' . $store_id);
        }
        $query = $this -> db -> get();
        if ($query -> num_rows() > 0) {
            return $query -> result_array();
        }
        return FALSE;
    }

    public function get_specials_test() {
        $this -> db -> select('*')
            -> from('productspecials')
            -> where(array(
                'SpecialId' => 14,
                'IsActive' => 1
            ));
        $query = $this -> db -> get();
//        echo $this -> db -> last_query();die;
        if ($query -> num_rows() > 0) {
            return $query -> result_array();
        }
        return FALSE;
    }

    public function insert_special_batch($special_all) {
        if ($this -> db -> insert_batch('productspecials', $special_all)) {
            return TRUE;
        }
        else {
            return FALSE;
        }
    }

    public function get_store_list_copy($retailer_id, $store_type_id) {
        $this -> db -> select('Id')
            -> from('stores')
            -> where(array(
                'RetailerId' => $retailer_id,
                'StoreTypeId' => $store_type_id,
                'IsActive' => 1,
                'IsRemoved' => 0
            ));
        $this -> db -> where('Id != 539');
        $query = $this -> db -> get();
        if ($query -> num_rows() > 0) {
            return $query -> result_array();
        }
        return FALSE;
    }

    public function get_store_products_copy($retailer_id, $store_type_id, $store_id) {
        $this -> db -> select('*')
            -> from('storeproducts')
            -> where(array(
                'RetailerId' => $retailer_id,
                'StoreTypeId' => $store_type_id,
                'StoreId' => $store_id,
                'IsActive' => 1,
                'IsRemoved' => 0
            ));
        $query = $this -> db -> get();
        if ($query -> num_rows() > 0) {
            return $query -> result_array();
        }
        return FALSE;
    }

    public function check_pro_available($retailer_id, $store_type_id, $store, $ProductId) {
        $this -> db -> select('count(*) as count', false)
            -> from('storeproducts')
            -> where(array(
                'RetailerId' => $retailer_id,
                'StoreTypeId' => $store_type_id,
                'StoreId' => $store,
                'ProductId' => $ProductId,
                'IsActive' => 1,
                'IsRemoved' => 0
            ));
        $query = $this -> db -> get();
        if ($query -> num_rows() > 0) {
            $res_arr = $query -> row_array();
            if ($res_arr['count'] > 0) {
                return FALSE;
            }
            return TRUE;
        }
    }

    public function insert_store_products_batch($products_all) {
        if ($this -> db -> insert_batch('storeproducts', $products_all)) {
            return TRUE;
        }
        else {
            return FALSE;
        }
    }

    public function get_special_group_stores($special_id) {
        $this -> db -> _protect_identifiers = false;
        $this -> db -> select('a.SpecialName, c.RetailerId,b.StateId, c.StoreTypeId, c.StoreId as Id, b.AllStates, c.AllStores', FALSE)
            -> from('specials as a')
            -> join('special_state as b', 'a.Id = b.SpecialId')
            -> join('special_stores as c', 'a.Id = c.SpecialId')
            -> where(
                array(
                    'a.Id' => $special_id,
                    'a.IsActive' => 1,
                    'a.IsRemoved' => 0
                )
        );
        if ($this -> session -> userdata('user_type') == 6) {
            $this -> db -> where('(c.RetailerId = ' . $this -> session -> userdata('user_retailer_id') . ' and c.StoreTypeId = ' . $this -> session -> userdata('user_store_format_id') . ' and c.StoreId = ' . $this -> session -> userdata('user_store_id') . ')', NULL, FALSE);
        }
        if ($this -> session -> userdata('user_type') == 5) {
            $this -> db -> where('(c.RetailerId = ' . $this -> session -> userdata('user_retailer_id') . ' and c.StoreTypeId = ' . $this -> session -> userdata('user_store_format_id') . ')', NULL, FALSE);
        }
        if ($this -> session -> userdata('user_type') == 3) {
            $this -> db -> where('(c.RetailerId = ' . $this -> session -> userdata('user_retailer_id') . ')', NULL, FALSE);
        }
        $this -> db -> group_by('c.StoreId');
        $query = $this -> db -> get();
//        echo $this -> db -> last_query();die;
        if ($query -> num_rows() > 0) {
            return $query -> result_array();
        }
        return FALSE;
    }

    public function get_pricewatch_enabled_user_store() {
        $this -> db -> select('a.UserId,b.PrefLatitude,b.PrefLongitude,b.PrefDistance,c.UserId,c.StoreId,d.PriceWatch')
            -> from('userdevices as a')
            -> join('users as b', 'b.Id = a.UserId')
            -> join('userpreferredbrands as c', 'c.UserId = b.Id')
            -> join('usernotificationsetting as d', 'd.UserId = b.Id and(d.PriceWatch = 1)')
            -> where(
                array(
                    'b.IsActive' => 1,
                    'b.IsRemoved' => 0
                )
            )
            -> group_by('a.UserId');
        $query = $this -> db -> get();
        //echo $this->db->last_query();exit;
        if ($query -> num_rows() > 0) {
            return $query -> result_array();
        }
        return FALSE;
    }
    
    
    
    
    public function get_special_enabled_user_store() {
        $this -> db -> select('a.UserId,b.PrefLatitude,b.PrefLongitude,b.PrefDistance,c.UserId,c.StoreId,d.Specials,d.NearStore,d.PreferredStoreOnly')
            -> from('userdevices as a')
            -> join('users as b', 'b.Id = a.UserId')
            -> join('userpreferredbrands as c', 'c.UserId = b.Id')
            -> join('usernotificationsetting as d', 'd.UserId = b.Id and(d.Specials = 1 or d.NearStore = 1)')
            -> where(
                array(
                    'b.IsActive' => 1,
                    'b.IsRemoved' => 0
                )
            )
            -> group_by('a.UserId');
        $query = $this -> db -> get();
        if ($query -> num_rows() > 0) {
            return $query -> result_array();
        }
        return FALSE;
    }

    public function get_store_state($store_id) {
        $this -> db -> select('StateId')
            -> from('stores')
            -> where(
                array(
                    'Id' => $store_id,
                    'IsActive' => 1,
                    'IsRemoved' => 0
                )
        );
        $query = $this -> db -> get();
        if ($query -> num_rows() > 0) {
            return $query -> row_array();
        }
        return FALSE;
    }

    public function change_status_special($special_id, $status) {

        $data = array('IsActive' => $status);
        $this -> db -> where('Id', $special_id);
        $this -> db -> update('specials', $data);

        return TRUE;
    }

    public function add_special_backup($special_array) {
        $this -> db -> select('Id')
            -> from('SpecialBackup')
            -> where('RetailerId', $this -> session -> userdata('user_retailer_id'))
            -> limit(1);
        $query = $this -> db -> get();
        if ($query -> num_rows() > 0) {
            $res_arr = $query -> row_array();
            $data_id = $res_arr['Id'];
            $update_data = array(
                'SpecialData' => $special_array
            );
            $where = array(
                'Id' => $data_id
            );
            $this -> db -> where($where);
            $this -> db -> update('SpecialBackup', $update_data);
        }
        else {
            $insert_data = array(
                'SpecialData' => $special_array,
                'RetailerId' => $this -> session -> userdata('user_retailer_id')
            );
            $this -> db -> insert('SpecialBackup', $insert_data);
        }
    }
    
    public function remove_special_backup(){
        $this -> db -> select('Id')
            -> from('SpecialBackup')
            -> where('RetailerId', $this -> session -> userdata('user_retailer_id'))
            -> limit(1);
        $query = $this -> db -> get();
        if ($query -> num_rows() > 0) {
            $res_arr = $query -> row_array();
            $data_id = $res_arr['Id'];
            $update_data = array(
                'SpecialData' => "{}",
                'AddedStores' => ''
            );
            $where = array(
                'Id' => $data_id
            );
            $this -> db -> where($where);
            $this -> db -> update('SpecialBackup', $update_data);
        }
        
    }

    public function get_special_backup() {
        $this -> db -> select('SpecialData,AddedStores')
            -> from('SpecialBackup')
            -> where('RetailerId', $this -> session -> userdata('user_retailer_id'))
            -> limit(1);
        $query = $this -> db -> get();
        if ($query -> num_rows() > 0) {
            $res_arr = $query -> row_array();
            return array(
                'SpecialData' => $res_arr['SpecialData'],
                'AddedStores' => $res_arr['AddedStores']
            );
        }
        else {
            return "{}";
        }
    }

    public function get_user_notification_settings($user_id) {
        $this -> db -> select('*')
            -> from('usernotificationsetting')
            -> where('UserId', $user_id)
            -> limit(1);
        $query = $this -> db -> get();
        if ($query -> num_rows() > 0) {
            return $query -> row_array();
        }
        return FALSE;
    }

    public function get_user_preferred_brands($user_id) {
        $this -> db -> select('*')
            -> from('userpreferredbrands')
            -> where('UserId', $user_id)
            -> limit(1);
        $query = $this -> db -> get();
        if ($query -> num_rows() > 0) {
            return $query -> row_array();
        }
        return FALSE;
    }
    
    
     public function get_preferred_brands_for_users($user_id) {
        $this -> db -> select('StoreId')
            -> from('userpreferredbrands')
            -> where('UserId', $user_id)
            -> limit(1);
        $query = $this -> db -> get();
        if ($query -> num_rows() > 0) {
            return $query -> row_array();
        }
        return FALSE;
    }

    public function get_store_details($id) {
        $this -> db -> select('*')
            -> from('stores')
            -> where(
                array(
                    'Id' => $id,
                    'IsActive' => 1,
                    'IsRemoved' => 0
                )
            )
            -> limit(1);
        $query = $this -> db -> get();
        if ($query -> num_rows() > 0) {
            return $query -> row_array();
        }
        return FALSE;
    }

    public function update_added_store($Id, $RetailerId) {
        $this -> db -> select('AddedStores')
            -> from('SpecialBackup')
            -> where('RetailerId', $RetailerId);
        $query = $this -> db -> get();
        if ($query -> num_rows() > 0) {
            $res_array = $query -> row_array();
            if ($res_array['AddedStores'] == '' || $res_array['AddedStores'] == NULL) {
                $added_arr = explode(',', $res_array['AddedStores']);
                if(!in_array($Id, $added_arr)){
                    $update_data = array(
                        'AddedStores' => $Id
                    );
                    $where = array(
                        'RetailerId' => $RetailerId
                    );
                    $this -> db -> where($where);
                    $this -> db -> update('SpecialBackup', $update_data);
                }
            }
            else {
                $update_data = array(
                    'AddedStores' => $res_array['AddedStores'] . ',' . $Id
                );
                $where = array(
                    'RetailerId' => $RetailerId
                );
                $this -> db -> where($where);
                $this -> db -> update('SpecialBackup', $update_data);
            }
        }
    }
    
    
    
    /*
     *  Get Stores names from specials 
     */
    public function get_specials_stores($special_id) {
        $this -> db -> select('a.StoreId, b.StoreName,a.Id, a.SpecialId', FALSE)
            -> from('special_stores as a')            
            -> join('stores as b', 'b.Id = a.StoreId')                
            -> where('a.SpecialId', $special_id)
            -> order_by('b.StoreName');
         $this -> db -> group_by('a.StoreId');
         
         $query = $this -> db -> get(); 
         //echo $this -> db -> last_query();die;
         if ($query -> num_rows() > 0) {
            return  $query -> result_array();            
         }
         return FALSE; 
    }
    
    /*
     *  Check Price Watch alert is set for product by the user  
     */
    public function check_pricewatch_alert($product, $user) {
        $this -> db -> select('count(*) as count', FALSE)
            -> from('userspricealerts as a')
            -> join('usernotificationsetting as b', 'a.UserId = b.UserId and b.PriceWatch = 1')
            -> where('a.UserId', $user)
            -> where('a.ProductId', $product);
        $query = $this -> db -> get();
        
        //echo $this -> db -> last_query();die;
        if ($query -> num_rows() > 0) {
            $result_array = $query -> row_array();
            if ($result_array['count'] > 0) {
                return TRUE;
            }
            return FALSE;
        }
        return FALSE;
    }
    
     public function check_price_alert($product, $user) {
        $this -> db -> select('count(*) as count', FALSE)
            -> from('userspricealerts as a')
            -> join('usernotificationsetting as b', 'a.UserId = b.UserId and b.PriceChange = 1')
            -> where('a.UserId', $user)
            -> where('a.ProductId', $product);
        $query = $this -> db -> get();
        if ($query -> num_rows() > 0) {
            $result_array = $query -> row_array();
            if ($result_array['count'] > 0) {
                return TRUE;
            }
            return FALSE;
        }
        return FALSE;
    }
    
    /*
     *  Check Special exists for particular retailser , particular store within specified date range.  
     */
    public function check_special_exists($special_from,$special_to, $stores, $specialId=0,$is_regional_special=0) {        
        $recordCount = 0;
        $this -> db -> select('Id as SpecialId, SpecialName',FALSE);
        if($specialId > 0 )
        {
            $this->db->where('Id <>',(int)$specialId);
        }
        
        # Retailers
        if($this -> session -> userdata('user_type') == 3 )
        {
            $this->db->where('IsRetailer',1);
        }
        
        # Store format
        if($this -> session -> userdata('user_type') == 5 )
        {
            $this->db->where('IsStoreType',1);
        }
        
        # Stores
        if($this -> session -> userdata('user_type') == 6 )
        {
            $this->db->where('IsStore',1);
        }        
        
        $this->db-> where(
                array(                   
                    'IsActive' => 1,
                    'IsRemoved' => 0,
                    'IsRegional'=>$is_regional_special
                )
        );
        $this->db->where('((SpecialFrom BETWEEN "'. date('Y-m-d', strtotime($special_from)). '" and "'. date('Y-m-d', strtotime($special_to)).'" ) OR (SpecialTo BETWEEN "'. date('Y-m-d', strtotime($special_from)). '" and "'. date('Y-m-d', strtotime($special_to)).'") OR ( SpecialFrom >= "'.date('Y-m-d', strtotime($special_from)).'" AND SpecialTo <= "'.date('Y-m-d', strtotime($special_to)).'" ))');
        $query = $this -> db -> get('specials');
        
        if ($query -> num_rows() > 0) {
            $specials =  $query -> result_array();
            
            foreach( $specials as $special )
            {
                $splId = $special['SpecialId'];                
                foreach ($stores as $store) {
                    $store_arr = explode(':', $store);
                    $storeTypeId = $store_arr[0];
                    $storeId = $store_arr[1];

                    #Check Special exists                
                    $this -> db -> select('Id as SpecialStoreId')            
                    -> where(
                            array(                   
                                'SpecialId' => $splId,
                                'RetailerId' => $this -> session -> userdata('user_retailer_id'),
                                'StoreTypeId' => $storeTypeId,
                                'StoreId' => $storeId
                            )
                        );
                    $query = $this -> db -> get('special_stores ');                    
                    if ($query -> num_rows() > 0) {
                        $recordCount++;
                        break;
                    }   
                }
            }             
        }
        return $recordCount;
    }
    
    
    public function get_special_store_count( $special_id,$userType,$user_retailer_id,$user_store_format_id,$user_store_id )
    {
        $this -> session -> userdata('user_type');
        
        $this->db->select('COUNT(distinct h.ProductId) as store_cnt');
        $this->db->from('productspecials AS h');
        $this->db->where( array( 'h.SpecialId' => $special_id) );
         if( $userType == 3)
         {
             $this->db->where( array( 'h.RetailerId' => $user_retailer_id ) );
         }
        
         if( $userType == 5)
         {
             $this->db->where( array( 'h.RetailerId' => $user_retailer_id, 'h.StoreTypeId' => $user_store_format_id ) );
         }
         
         if( $userType == 6)
         {
             $this->db->where( array( 'h.RetailerId' => $user_retailer_id, 'h.StoreTypeId' => $user_store_format_id, 'h.StoreId' => $user_store_id ) );
         }
         
        $query = $this->db->get();

        return $query->row()->store_cnt;
        
    }
    
    
    public function add_special_combo_products_backup($special_id,$special_product_id,$special_combo_products) {
        $this -> db -> select('Id')
            -> from('special_combo_products_backup')
            -> where('RetailerId', $this -> session -> userdata('user_retailer_id'))
            -> where('SpecialId', $special_id)    
            -> where('SpecialProductId', $special_product_id)         
            -> limit(1);
        $query = $this -> db -> get();
        
        if ($query -> num_rows() > 0) {
            $res_arr = $query -> row_array();
            $data_id = $res_arr['Id'];
            $update_data = array(
                'ComboProductsData' => $special_combo_products
            );
            $where = array(
                'Id' => $data_id
            );
            $this -> db -> where($where);
            $this -> db -> update('special_combo_products_backup', $update_data);
        }
        else {
            $insert_data = array(
                'RetailerId' => $this -> session -> userdata('user_retailer_id'),
                'SpecialId' => $special_id,
                'SpecialProductId' => $special_product_id,
                'ComboProductsData' => $special_combo_products
            );
            $this -> db -> insert('special_combo_products_backup', $insert_data);
        }
    }
    
    
    public function get_special_combo_products_backup($special_id,$special_product_id) {
        
        $this -> db -> select('ComboProductsData')
            -> from('special_combo_products_backup')
            -> where('RetailerId', $this -> session -> userdata('user_retailer_id'))
            -> where('SpecialId', $special_id)    
            -> where('SpecialProductId', $special_product_id)    
            -> limit(1);
        $query = $this -> db -> get();
        
        //echo $this->db->last_query();exit;
        
        if ($query -> num_rows() > 0) {
            $res_arr = $query -> row_array();
            return array(
                'ComboProductsData' => $res_arr['ComboProductsData']
            );
        }
        else {
            return "{}";
        }
    }
    
    /* Function to set combo products for special products */
    public function set_combo_products($specialId) {   
        $comboProductsCounter = 0;
        
        $this->db->select('Id, ProductId, RetailerId, SpecialId');
        $this->db->from('productspecials');
        $this->db->where( array( 'SpecialId' => $specialId) );
        $this -> db -> group_by('ProductId');
        $query = $this->db->get();
        
        if ($query -> num_rows() > 0) {
            $specialProducts = $query -> result_array();
            
            if($specialProducts)
            {
                foreach ($specialProducts as $specialProduct)
                { 
                    //$splProdId = 7303; 

                    $this->db->select('Id,ComboProductsData');
                    $this->db->from('special_combo_products_backup');
                    $this->db->where('RetailerId', $specialProduct['RetailerId']);
                    $this->db->where('SpecialId', $specialProduct['SpecialId']);
                    //$this->db->where('SpecialProductId', $splProdId);
                    $this->db->where('SpecialProductId', $specialProduct['ProductId']);
                    $this->db-> limit(1);
                    $queryComboProducts = $this->db->get();
                    
                    if ($queryComboProducts -> num_rows() > 0) {
                        
                        $comboProducts = $queryComboProducts -> row_array(); 

                        if($comboProducts)
                        {
                            $comboProductsData = json_decode($comboProducts['ComboProductsData']);
                            foreach ($comboProductsData as $comboProduct)
                            {
                                $insert_data = array(
                                    'RetailerId' => $specialProduct['RetailerId'],
                                    'SpecialId' => $specialId,
                                    'SpecialProductId' => $specialProduct['ProductId'],
                                    'ProductId' => $comboProduct->product_id,
                                    'Quantity' => $comboProduct->qty
                                );
                                $this -> db -> insert('special_combo_products', $insert_data);
                                
                                $comboProductsCounter++;
                            } // foreach ($comboProductsData as $comboProduct)
                        }
                    }
                }// foreach ($specialProducts as $specialProduct)
            } // if($specialProducts)
        }
        else {
            
        }
        
        # Delete Combo Products fro specials 
        $this -> db -> where('SpecialId', $specialId);
        $this -> db -> delete('special_combo_products_backup');
  
        return true;
    }
    
    public function get_comboProductCount($special_id,$special_product_id) {
        $this -> db -> select('ComboProductsData')
            -> from('special_combo_products_backup')
            -> where('RetailerId', $this -> session -> userdata('user_retailer_id'))
            -> where('SpecialId', $special_id)    
            -> where('SpecialProductId', $special_product_id)    
            -> limit(1);
        $query = $this -> db -> get();
        
        //echo $this->db->last_query();exit;
        
        if ($query -> num_rows() > 0) {
            $res_arr = $query -> row_array();
            $comboProductsData = $res_arr['ComboProductsData'];
            
            if($comboProductsData == "{}")
            {
                return 0;
            }else{
                return 1;
            }
        }
        else {
            return 0;
        }
        
    }
    
    
    
    public function get_years() {
        
        $years = array();        
        $index = 0;
        $currentYear = date('Y');
        
       
        for( $i=0; $i < 10; $i++ )
        {
           $years[$index]['yearId'] = $currentYear - $i;
           $years[$index]['yearName'] = $currentYear - $i;
           $index++;
        }
        
        return $years;
    }
	
    public function get_user_detailsOfUserId($user_id) {
		
        $this -> db -> from('users');
        $this -> db -> where('Id', $user_id);
        $this -> db -> limit(1);
        $query = $this -> db -> get();

        if ($query -> num_rows() == 1) {
            return $query -> row_array();
        }
        else {
            return FALSE;
        }
    }
	public function getAllStoresOfSpecialId($id) {
        $this -> db -> select('stores.Id,stores.StoreName');
        $this -> db -> from('stores');
        $this -> db -> join('special_stores', 'stores.Id = special_stores.StoreId');
        $this -> db -> join('specials', 'specials.Id = special_stores.SpecialId');
        $this -> db -> where('specials.Id', $id);
        $query = $this -> db -> get();
       return $query -> result_array();      
    }
    
	public function get_special_enabled_user_by_storeId($storeId) {
        $this -> db -> select('a.UserId,b.PrefLatitude,b.PrefLongitude,b.PrefDistance,c.UserId,c.StoreId,d.Specials,d.NearStore,d.PreferredStoreOnly')
            -> from('userdevices as a')
            -> join('users as b', 'b.Id = a.UserId')
            -> join('userpreferredbrands as c', 'c.UserId = b.Id')
            -> join('usernotificationsetting as d', 'd.UserId = b.Id and(d.Specials = 1 or d.NearStore = 1)')
            -> where(
                array(
                    'b.IsActive' => 1,
                    'b.IsRemoved' => 0,
					'c.StoreId'=>$storeId
                )
            )
            -> group_by('a.UserId');
        $query = $this -> db -> get();
        if ($query -> num_rows() > 0) {
            return $query -> result_array();
        }
        return FALSE;
    }
	
	public function getAllDetailsOfSpecialIdAndStoreId($special_id,$storeId) {
        $this -> db -> select('a.ProductId,b.ProductName,a.RetailerId,c.CompanyName,a.StoreTypeId,d.StoreType,a.StoreId,e.StoreName,e.Latitude,e.Longitude,a.SpecialPrice,a.SpecialQty,f.SpecialName,f.SpecialFrom,f.SpecialTo')
            -> from('productspecials as a')
            -> join('products as b', 'b.Id = a.ProductId')
            -> join('retailers as c', 'c.Id = a.RetailerId')
            -> join('storestypes as d', 'd.Id = a.StoreTypeId')
            -> join('stores as e', 'e.Id = a.StoreId')
            -> join('specials as f', 'f.Id = a.SpecialId')
            -> where('a.SpecialId', $special_id)
			-> where('e.Id', $storeId)
            -> where('a.IsApproved', 0);
        $query = $this -> db -> get();
		
        if ($query -> num_rows() > 0) {
            return $query -> result_array();
        }
        return FALSE;
    }
	
} 