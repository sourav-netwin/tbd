<?php

/*
 * Author: Name:PM
 * Purpose: Model for controlling database interactions regarding the content.
 * Date:10-09-2015
 * Dependency: None
 */

class Storeproductmodel extends CI_Model {
    /*
     * Purpose: Constructor.
     * Date: 10-09-2015
     * Input Parameter: None
     * Output Parameter: None
     */

    public function __construct() {
        parent::__construct();
    }

    public function get_store_product_details($store_product_id) {
        $this -> db -> select('retailers.CompanyName,retailers.LogoImage, stores.StoreName, stores.Id as StoreId, storestypes.StoreType, storestypes.Id as StoreTypeId, main_parent_category.CategoryName AS main_parent_cat, main_parent_category.Id as main_parent_catId, storeproducts.Price, products.ProductName, storeproducts.ProductId, storeproducts.RetailerId, storeproducts.Id');
        $this -> db -> from('storeproducts');
        $this -> db -> join('products', 'products.Id = storeproducts.ProductId');
        $this -> db -> join('retailers', 'retailers.Id = storeproducts.RetailerId');
        $this -> db -> join('stores', 'stores.Id = storeproducts.StoreId', 'left');
        $this -> db -> join('storestypes', 'storestypes.Id = stores.StoreTypeId');
        $this -> db -> join('categories', 'categories.Id = products.CategoryId', 'left');
        $this -> db -> join('categories parent_category', 'parent_category.ID = products.ParentCategoryId', 'left');
        $this -> db -> join('categories main_parent_category', 'main_parent_category.Id = products.MainCategoryId', 'left');
        $this -> db -> where('storeproducts.Id', $store_product_id);
        $this -> db -> limit(1);
        $query = $this -> db -> get();
        if ($query -> num_rows() == 1) {
            return $query -> row_array();
        }
        else {
            return FALSE;
        }
    }

    public function update_store_product($store_product_id, $data) {

        $this -> db -> where('Id', $store_product_id);
        $this -> db -> update('storeproducts', $data);
    }

    public function add_store_product($insert_data) {
        $this -> db -> insert('storeproducts', $insert_data);
        return $this -> db -> insert_id();
    }

    public function change_status($store_id, $status) {

        $data = array('IsActive' => $status);
        $this -> db -> where('Id', $store_id);
        $this -> db -> update('storeproducts', $data);

        return TRUE;
    }

    public function get_store_products($retailer_id, $main_parent_category_id = 0) {

        $this -> db -> select('products.Id, products.ProductName');

        $this -> db -> where(
            array(
                'storeproducts.RetailerId' => $retailer_id,
                'storeproducts.IsActive' => '1',
                'products.IsActive' => '1',
                'products.IsRemoved' => '0',
        ));

        $this -> db -> join('products', 'products.Id = storeproducts.ProductId');
        $this -> db -> join('categories', 'categories.Id = products.CategoryId');
        $this -> db -> join('categories parent_category', 'parent_category.Id = categories.ParentCategory');
        $this -> db -> join('categories main_parent_category', 'main_parent_category.Id = parent_category.ParentCategory');

        if ($main_parent_category_id != 0) {
            $this -> db -> where(array("main_parent_category.Id" => $main_parent_category_id));
        }

        $query = $this -> db -> get('storeproducts');

        return $query -> result_array();
    }

    public function get_products_by_retailer($retailer_id, $store_id) {

        $this -> db -> select('ProductId');

        $this -> db -> where(array('RetailerId' => $retailer_id));
        $this -> db -> where_in('StoreId', $store_id);

        $query = $this -> db -> get('storeproducts');

        return $query -> result_array();
    }

    public function get_products_by_store($retailer_id, $store_id, $product_id) {
        $this -> db -> select('Id');

        $this -> db -> where('RetailerId', $retailer_id);
        $this -> db -> where('StoreId', $store_id);
        $this -> db -> where('ProductId', $product_id);

        $query = $this -> db -> get('storeproducts');
        if ($query -> num_rows() == 1) {
            return true;
        }
        else {
            return false;
        }
    }

    public function get_store_detais_from_id($store_id) {
        $this -> db -> select('*')
            -> from('stores')
            -> where('IsActive', 1)
            -> where('IsRemoved', 0)
            -> where('Id', $store_id)
            -> limit(1);
        $query = $this -> db -> get();
        if ($query -> num_rows() > 0) {
            return $query -> row_array();
        }
        else {
            return FALSE;
        }
    }

    public function get_added_products_by_store($retailer_id, $store_type_id, $store_id) {
        $this -> db -> select('ProductId');

        $this -> db -> where('RetailerId', $retailer_id);
        $this -> db -> where('StoreTypeId', $store_type_id);
        $this -> db -> where('StoreId', $store_id);

        $query = $this -> db -> get('storeproducts');
//        echo $this -> db -> last_query();die;
        if ($query -> num_rows() > 0) {
            return $query -> result_array();
        }
        else {
            return false;
        }
    }

    public function get_products_catalogue_add($categories, $product_ids = array(), $retailer_id) {
        $retailer_id = $retailer_id == '' ? 0 : $retailer_id;
        $this -> db -> select("products.ProductName, products.MainCategoryId, products.Brand, products.SKU,products.RRP,products.Id as Id, products.IsActive AS active, categories.CategoryName, parent_category.CategoryName AS parent_cat,main_parent_category.CategoryName AS main_parent_cat");
        $this -> db -> join('categories', 'categories.Id = products.CategoryId', 'left');
        $this -> db -> join('categories parent_category', 'parent_category.ID = products.ParentCategoryId and parent_category.IsActive = 1 and parent_category.IsRemoved = 0');
        $this -> db -> join('categories main_parent_category', 'main_parent_category.Id = products.MainCategoryId and main_parent_category.IsActive = 1 and main_parent_category.IsRemoved = 0');

        $cond = array('products.IsRemoved' => '0',
            'products.IsActive' => '1');

        $this -> db -> where($cond);
        $this -> db -> where('(HouseId is null or HouseId = ' . $retailer_id . ')');
        if (!empty($product_ids)) {
            $this -> db -> where_not_in('products.Id', $product_ids);
        }
        $this -> db -> where_in('main_parent_category.Id', $categories);

        $query = $this -> db -> get('products');

        return $query -> result_array();
    }

    public function get_products_catalogue_add_count($categories, $product_ids = array(), $retailer_id) {
        $retailer_id = $retailer_id == '' ? 0 : $retailer_id;
        $this -> db -> select("count(*) as count");
        $this -> db -> join('categories', 'categories.Id = products.CategoryId', 'left');
        $this -> db -> join('categories parent_category', 'parent_category.ID = products.ParentCategoryId and parent_category.IsActive = 1 and parent_category.IsRemoved = 0');
        $this -> db -> join('categories main_parent_category', 'main_parent_category.Id = products.MainCategoryId and main_parent_category.IsActive = 1 and main_parent_category.IsRemoved = 0');

        $cond = array(
            'products.IsRemoved' => '0',
            'products.IsActive' => '1'
            );

        $this -> db -> where($cond);
        $this -> db -> where('(HouseId is null or HouseId = ' . $retailer_id . ')');
        if (!empty($product_ids)) {
            $this -> db -> where_not_in('products.Id', $product_ids);
        }

        $this -> db -> where_in('main_parent_category.Id', $categories);

        $query = $this -> db -> get('products');
//        echo $this -> db -> last_query();die;

        return $query -> row_array();
    }

    public function add_storeproduct_batch($insert_data) {
        if ($this -> db -> insert_batch('storeproducts', $insert_data)) {
            return TRUE;
        }
        return FALSE;
    }

    public function get_excel_details($product_id) {
        $details = [];
        $this -> db -> select('a.ProductName,b.CategoryName as main_cat,c.CategoryName as parent_cat,d.CategoryName as category')
            -> from('products as a')
            -> join('categories as b', 'b.Id = a.MainCategoryId')
            -> join('categories as c', 'c.Id = a.ParentCategoryId')
            -> join('categories as d', 'd.Id = a.CategoryId', 'left')
            -> where('a.Id', $product_id);
        $query = $this -> db -> get();
        if ($query -> num_rows() > 0) {
            $data_arr = $query -> row_array();
            $details = $data_arr;
        }
        return $details;
    }

    public function update_store_price($update_data, $where) {
        $this -> db -> where($where);
        if($this -> db -> update('storeproducts', $update_data)){
            return TRUE;
        }
        else{
            return FALSE;
        }
    }
    
    public function get_store_data_single($id) {
        $this -> db -> select('a.ProductId,b.ProductName,a.RetailerId,c.CompanyName,a.StoreTypeId,d.StoreType,a.StoreId,e.StoreName,a.Price')
            -> from('storeproducts as a')
            -> join('products as b','b.Id = a.ProductId')
            -> join('retailers as c','c.Id = a.RetailerId')
            -> join('storestypes as d','d.Id = a.StoreTypeId')
            -> join('stores as e','e.Id = a.StoreId')
            -> where('a.Id', $id)
            -> where('a.IsActive', 1)
            -> where('a.IsRemoved', 0);
        $query = $this -> db -> get();
        if ($query -> num_rows() > 0) {
            return $query -> row_array();
        }
        return FALSE;
    }
    
    
    public function change_price_in_all_stores($storeProductId, $price, $retailer_id) {
        
        $productId = 0;
        
        # Get Product information
        $this -> db -> select('storeproducts.ProductId');
        $this -> db -> from('storeproducts');
        $this -> db -> where('storeproducts.Id', $storeProductId);
        $query = $this -> db -> get();
        $storeProductInfo =  $query -> row_array();
        
        if($storeProductInfo)
        {
            $productId = $storeProductInfo['ProductId'];
        }
        
        if($productId > 0 && $retailer_id > 0 )
        {
            $data = array('Price' => $price, 'ModifiedOn' => date('Y-m-d H:i:s'));
            $this -> db -> where('ProductId', $productId);
            $this -> db -> where('RetailerId', $retailer_id);
            $this -> db -> update('storeproducts', $data);
        }
        return TRUE;
    }
    
}

?>