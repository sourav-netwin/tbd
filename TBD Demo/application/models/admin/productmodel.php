<?php

/*
 * Author: Name:PM
 * Purpose: Model for controlling database interactions regarding the content.
 * Date:05-09-2015
 * Dependency: None
 */

class Productmodel extends CI_Model {
    /*
     * Purpose: Constructor.
     * Date: 05-09-2015
     * Input Parameter: None
     * Output Parameter: None
     */

    public function __construct() {
        parent::__construct();
    }

    public function get_products() {
        $this -> db -> select('products.Id,products.ProductName,products.ProductImage,products.IsActive,products.RRP');

        $this -> db -> where(
            array(
                'products.IsRemoved' => 0
        ));
        $this -> db -> order_by("ProductName");
        $query = $this -> db -> get('products');

        return $query -> result_array();
    }

    public function get_product_details($product_id) {
        $this -> db -> select('categories.CategoryName, products.*, parent_category.Id as parent_cat_id, parent_category.CategoryName as parent_cat, main_parent_category.Id as main_parent_cat_id, main_parent_category.CategoryName as main_parent_cat');
        $this -> db -> from('products');
        $this -> db -> join('categories', 'categories.Id = products.CategoryId', 'left');
        $this -> db -> join('categories parent_category', 'parent_category.ID = products.ParentCategoryId', 'left');
        $this -> db -> join('categories main_parent_category', 'main_parent_category.Id = products.MainCategoryId', 'left');
        $this -> db -> where('products.Id', $product_id);
        $this -> db -> limit(1);
        $query = $this -> db -> get();

        if ($query -> num_rows() == 1) {
            return $query -> row_array();
        }
        else {
            return FALSE;
        }
    }

    public function update_product($product_id, $data) {

        $this -> db -> where('Id', $product_id);
        $this -> db -> update('products', $data);
    }

    public function add_product($data) {
        if ($this -> db -> insert('products', $data)) {
            return $this -> db -> insert_id();
        }
        return false;
    }

    public function delete_product($product_id) {

        $data = array('IsRemoved' => "1");
        $this -> db -> where('Id', $product_id);
        $this -> db -> update('products', $data);

        return TRUE;
    }

    public function change_status($product_id, $status) {

        $data = array('IsActive' => $status);
        $this -> db -> where('Id', $product_id);
        $this -> db -> update('products', $data);

        return TRUE;
    }

    /**
     * This function is used to get products to store the products
     * @param type $main_parent_category_id
     * @param type $retailer_id
     * @return type
     */
    public function get_products_by_category($main_parent_category_id, $product_ids = array(), $retailer_id = '') {

        $retailer_id = $retailer_id == '' ? 0 : $retailer_id;
        $this -> db -> select("products.ProductName, products.Brand, products.SKU,products.RRP,products.Id as Id, products.IsActive AS active, categories.CategoryName, parent_category.CategoryName AS parent_cat,main_parent_category.CategoryName AS main_parent_cat");
        $this -> db -> join('categories', 'categories.Id = products.CategoryId', 'left');
        $this -> db -> join('categories parent_category', 'parent_category.ID = products.ParentCategoryId');
        $this -> db -> join('categories main_parent_category', 'main_parent_category.Id = products.MainCategoryId');

        $cond = array('products.IsRemoved' => '0',
            'products.IsActive' => '1');

        if ($main_parent_category_id != 0) {
            if ($main_parent_category_id != 0)
                $cond = $cond + array("products.MainCategoryId" => $main_parent_category_id);
        }

        $this -> db -> where($cond);
        $this -> db -> where('(HouseId is null or HouseId = ' . $retailer_id . ')');

        if ($product_ids)
            $this -> db -> where_not_in('products.Id', $product_ids);

        $query = $this -> db -> get('products');

        return $query -> result_array();
    }

    public function get_products_by_name($search_product, $product_ids = array(), $retailer_id = '') {

        $retailer_id = $retailer_id == '' ? 0 : $retailer_id;
        $this -> db -> select("products.ProductName, products.MainCategoryId, products.Brand, products.SKU,products.RRP,products.Id as Id, products.IsActive AS active, categories.CategoryName, parent_category.CategoryName AS parent_cat,main_parent_category.CategoryName AS main_parent_cat");
        $this -> db -> join('categories', 'categories.Id = products.CategoryId', 'left');
        $this -> db -> join('categories parent_category', 'parent_category.ID = products.ParentCategoryId');
        $this -> db -> join('categories main_parent_category', 'main_parent_category.Id = products.MainCategoryId');

        $cond = array('products.IsRemoved' => '0',
            'products.IsActive' => '1');

//        if ($main_parent_category_id != 0) {
//            if ($main_parent_category_id != 0)
//                $cond = $cond + array("products.MainCategoryId" => $main_parent_category_id);
//        }

        $this -> db -> where($cond);
        $this -> db -> where('(HouseId is null or HouseId = ' . $retailer_id . ')');
        $this -> db -> like('products.ProductName', $search_product);

        if ($product_ids)
            $this -> db -> where_not_in('products.Id', $product_ids);

        $query = $this -> db -> get('products');

        return $query -> result_array();
    }

    /**
     * Get users having a price alert enalbed
     */
    public function get_price_alert_users($product_id) {

        $this -> db -> select('userspricealerts.UserId,userdevices.DeviceId,userdevices.DeviceType,');
        $this -> db -> join('userdevices', 'userdevices.UserId = userspricealerts.UserId AND userdevices.IsRemoved =0 AND userdevices.IsActive=1', 'left');

        $this -> db -> where(array(
            'userspricealerts.ProductId' => $product_id
        ));

        $query = $this -> db -> get('userspricealerts');
        return $query -> result_array();
    }
    /*
     *  Get users whose products marked as favorite
     */

    public function get_favorite_users($product_id) {

        $this -> db -> select('usersfavorite.UserId,userdevices.DeviceId,userdevices.DeviceType,');
        $this -> db -> join('userdevices', 'userdevices.UserId = usersfavorite.UserId AND userdevices.IsRemoved =0 AND userdevices.IsActive=1', 'left');
        $this -> db -> join('usernotificationsetting', 'usernotificationsetting.UserId = usersfavorite.UserId');

        $this -> db -> where(array(
            'usersfavorite.ProductId' => $product_id,
            'usernotificationsetting.FavoriteNotification' => 1
        ));

        $query = $this -> db -> get('usersfavorite');
        return $query -> result_array();
    }
    /*
     *  Get users whose products marked as favorite
     */

    public function get_wishlist_users($product_id) {

        $this -> db -> select('userwishlistproducts.UserId,userdevices.DeviceId,userdevices.DeviceType,');
        $this -> db -> join('userdevices', 'userdevices.UserId = userwishlistproducts.UserId AND userdevices.IsRemoved =0 AND userdevices.IsActive=1', 'left');
        $this -> db -> join('usernotificationsetting', 'usernotificationsetting.UserId = userwishlistproducts.UserId');

        $this -> db -> where(array(
            'userwishlistproducts.ProductId' => $product_id,
            'usernotificationsetting.WishlistNotification' => 1
        ));

        $query = $this -> db -> get('userwishlistproducts');
        return $query -> result_array();
    }
    /*
     * Get if product name exist
     */

    public function check_product_by_name($name) {
        $this -> db -> from('products');
        $this -> db -> where('ProductName', ($name));
        $this -> db -> where('IsRemoved', 0);
        $this -> db -> limit(1);
        $query = $this -> db -> get();

        if ($query -> num_rows() >= 1) {
            return FALSE;
        }
        else {
            return TRUE;
        }
    }

    public function check_product_by_name_edit($name, $id) {
        $this -> db -> from('products');
        $this -> db -> where('ProductName', ($name));
        $this -> db -> where('Id != ', $id);
        $this -> db -> where('IsRemoved', 0);
        $this -> db -> limit(1);
        $query = $this -> db -> get();

        if ($query -> num_rows() >= 1) {
            return FALSE;
        }
        else {
            return TRUE;
        }
    }

    public function get_all_active_stores() {
        $this -> db -> select('a.Id,a.StoreName,a.RetailerId,b.CompanyName,a.StoreTypeId,c.StoreType')
            -> from('stores as a')
            -> join('retailers as b', 'a.RetailerId = b.Id')
            -> join('storestypes as c', 'a.StoreTypeId = c.Id')
            -> where('a.IsActive', 1)
            -> where('b.IsActive', 1)
            -> where('c.IsActive', 1)
            -> where('a.IsRemoved', 0)
            -> where('b.IsRemoved', 0)
            -> where('c.IsRemoved', 0);
        $query = $this -> db -> get();
        if ($query -> num_rows() > 0) {
            return $query -> result_array();
        }
        return FALSE;
    }

    public function insert_to_all_stores($data) {
        if ($this -> db -> insert_batch('storeproducts', $data)) {
            return TRUE;
        }
        return FALSE;
    }
}

?>