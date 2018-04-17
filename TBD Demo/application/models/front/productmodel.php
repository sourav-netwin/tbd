<?php

/*
 * Author:  PM
 * Purpose: User related functions
 * Date:    15-10-2015
 */

class Productmodel extends CI_Model {

    public function __construct() {
        parent::__construct();
    }
    /*  Get Top offers for today
     *   Return - Array of offers
     */

    public function get_top_offers($price_range = array(), $last_product_special_id = 0) {

        //Get the nearest store
        $user_preference = $this -> get_user_preference();
        $store_id = $user_preference['StoreId'];
        $retailer_id = $user_preference['RetailerId'];
        $this->db->_protect_identifiers=false;
        $this -> db -> select('products.Id,
                           products.ProductName,
                           products.ProductImage,
                           products.ProductDescription,
                           products.RRP,
                           products.Brand,
                           products.SKU,
                           COUNT(productsreviews.ID) AS reviews_count,
                           AVG(productsreviews.rating) AS avg_rating,
                           usersfavorite.ID AS is_favorite,
                           storeproducts.Price AS store_price,
                           productspecials.SpecialQty,
                           productspecials.SpecialPrice,
                           productspecials.Id as SpecialId,
                           specials.IsStore,
                           specials.Id as special_id', FALSE);
        $this -> db -> join('productspecials', 'productspecials.ProductId = products.Id AND DATE(productspecials.PriceAppliedFrom) <= "' . date('Y-m-d') . '" AND DATE(productspecials.PriceAppliedTo) >= "' . date('Y-m-d') . '" AND productspecials.RetailerId =' . $retailer_id . " AND productspecials.StoreId=" . $store_id . " AND productspecials.IsActive=1 AND productspecials.IsApproved =1 ");
        $this -> db -> join('specials', 'specials.Id = productspecials.SpecialId');
        $this -> db -> join('productsreviews', 'productsreviews.ProductId = products.Id', 'left');
        $this -> db -> join('usersfavorite', 'usersfavorite.ProductId = products.Id AND usersfavorite.SpecialId = specials.Id AND usersfavorite.UserId =' . $this -> session -> userdata('userid'), 'left');
        $this -> db -> join('storeproducts', 'storeproducts.RetailerId =' . $retailer_id . " AND storeproducts.StoreId=" . $store_id . " AND storeproducts.ProductId = products.Id AND storeproducts.IsActive=1");
        

        $this -> db -> where(array(
            'products.IsActive' => 1,
            'products.IsRemoved' => 0,
        ));

        $this -> db -> order_by('productspecials.Id', 'DESC');
        $this -> db -> group_by('products.Id, productspecials.SpecialId');

        if ($last_product_special_id > 0)
            $this -> db -> where("productspecials.Id <", $last_product_special_id);
     //  $this -> db -> limit($this -> config -> item('top_offer_product_limit'));

        if (!empty($price_range)) {
            $filter_query = "";

            //Price Range Filter
            foreach ($price_range as $range) {
                if ($filter_query != '') {
                    $filter_query .= " OR ";
                }

                $filter_query .= '(';

                if ($range['min'])
                    $filter_query .= '(storeproducts.Price >=' . preg_replace("/[^0-9,.]/", "", $range['min']) . " )";

                if ($range['min'] && $range['max'])
                    $filter_query .= 'and';

                if ($range['max'])
                    $filter_query .='(storeproducts.Price <=' . preg_replace("/[^0-9,.]/", "", $range['max']) . ")";

                $filter_query .= ')';
            }

            if ($filter_query != '') {
                $this -> db -> where("(" . $filter_query . ")");
            }
        }

        $query = $this -> db -> get('products');
	//	echo $query->num_rows();exit;
        //echo $this -> db -> last_query();die;
        return $query -> result_array();
		
    }
    /*  Get Count of Top offers for today
     *   Return - Array of offers
     */

    public function get_top_offers_count($price_range = array()) {

        //Get the nearest store
        $user_preference = $this -> get_user_preference();
        $store_id = $user_preference['StoreId'];
        $retailer_id = $user_preference['RetailerId'];

        $this -> db -> select('products.Id', FALSE);

        $this -> db -> join('productsreviews', 'productsreviews.ProductId = products.Id', 'left');
        $this -> db -> join('usersfavorite', 'usersfavorite.ProductId = products.Id AND usersfavorite.UserId =' . $this -> session -> userdata('userid'), 'left');
        $this -> db -> join('storeproducts', 'storeproducts.ProductId = products.Id AND storeproducts.RetailerId =' . $retailer_id . " AND storeproducts.StoreId=" . $store_id . " AND storeproducts.IsActive=1");
        $this -> db -> join('productspecials', 'productspecials.ProductId = products.Id AND DATE(productspecials.PriceAppliedFrom) <= "' . date('Y-m-d') . '" AND DATE(productspecials.PriceAppliedTo) >= "' . date('Y-m-d') . '" AND productspecials.RetailerId =' . $retailer_id . " AND productspecials.StoreId=" . $store_id . " AND productspecials.IsActive=1 AND productspecials.IsApproved =1 ");
        $this -> db -> join('specials', 'specials.Id = productspecials.SpecialId');

        $this -> db -> where(array(
            'products.IsActive' => 1,
            'products.IsRemoved' => 0,
        ));

        $this -> db -> order_by('productspecials.Id', 'DESC');
        $this -> db -> group_by('products.Id');

        if (!empty($price_range)) {
            $filter_query = "";

            //Price Range Filter
            foreach ($price_range as $range) {
                if ($filter_query != '') {
                    $filter_query .= " OR ";
                }

                $filter_query .= '(';

                if ($range['min'])
                    $filter_query .= '(productspecials.SpecialPrice >=' . preg_replace("/[^0-9,.]/", "", $range['min']) . " )";

                if ($range['min'] && $range['max'])
                    $filter_query .= 'and';

                if ($range['max'])
                    $filter_query .='(productspecials.SpecialPrice <=' . preg_replace("/[^0-9,.]/", "", $range['max']) . ")";

                $filter_query .= ')';
            }

            if ($filter_query != '') {
                $this -> db -> where("(" . $filter_query . ")");
            }
        }

        $query = $this -> db -> get('products');
        return $query -> result_array();
    }
    /*  Get user preference of logged in user
     */

    public function get_user_preference() {
        $this -> db -> select('a.RetailerId,a.StoreId,b.StoreTypeId');
        $this -> db -> from('userpreferredbrands as a');
        $this -> db -> join('stores as b', 'a.StoreId = b.Id');

        $this -> db -> where(array(
            'a.IsActive' => 1,
            'a.IsRemoved' => 0,
            'b.IsActive' => 1,
            'b.IsRemoved' => 0,
            'a.UserId' => $this -> session -> userdata('userid')
        ));
        $this -> db -> limit(1);
        $query = $this -> db -> get();

        return $query -> row_array();
    }

    // Function to get max and min price range for products
    public function get_price_range($category_type = '', $category_id = '') {

        //Get the nearest store
        $user_preference_details = $this -> get_user_preference();
        $store_id = $user_preference_details['StoreId'];
        $retailer_id = $user_preference_details['RetailerId'];

        $this -> db -> select_max('storeproducts.Price', 'max_price');
        $this -> db -> select_min('storeproducts.Price', 'min_price');
        $this -> db -> join('storeproducts', 'storeproducts.ProductId = products.Id AND storeproducts.RetailerId =' . $retailer_id . " AND storeproducts.StoreId=" . $store_id . " AND storeproducts.IsActive=1");

        if ($category_type != '' && $category_id != '') {
            $this -> db -> join('categories sub_category', 'sub_category.Id = products.CategoryId', 'left');
            $this -> db -> join('categories parent_category', 'parent_category.Id = products.ParentCategoryId', 'left');
            $this -> db -> join('categories main_parent_category', 'main_parent_category.Id = products.MainCategoryId', 'left');

            if ($category_type == 'main') {
                $this -> db -> where('main_parent_category.Id', $category_id);
            }
            else if ($category_type == 'parent') {
                $this -> db -> where('parent_category.Id', $category_id);
            }
            else {
                $this -> db -> where('sub_category.Id', $category_id);
            }
        }
        $this -> db -> where(array(
            'products.IsActive' => 1,
            'products.IsRemoved' => 0,
        ));

        $query = $this -> db -> get('products');

        $result = $query -> row_array();

        $range = ProductPriceRange($result['max_price'], $result['min_price']);
        return $range;
    }

    // Mark/Unmark a product as favourite
    public function toggle_favourite($product_id, $special_id, $is_fav) {
        if ($is_fav == 1) {
            $this -> db -> delete('usersfavorite', array('ProductId' => $product_id, 'SpecialId' => $special_id));
        }
        else {
            $ins_arr = array('ProductId' => $product_id
                , 'SpecialId' => $special_id
                , 'UserId' => $this -> session -> userdata('userid')
                , 'CreatedOn' => date('Y-m-d H:i:s')
                , 'IsActive' => 1
            );
            $this -> db -> insert('usersfavorite', $ins_arr);
        }
        return 'success';
    }

    // Function to get products
    public function get_products($category_type, $category_id, $price_range = array(), $last_product_id = 0, $show_offer = 0, $product_ids = array(), $search = "") {
        //Get user preference
        $user_preference = $this -> get_user_preference();
        $store_id = $user_preference['StoreId'];
        $retailer_id = $user_preference['RetailerId'];

        $this -> db -> select('products.Id,
                           products.ProductName,
                           storeproducts.RetailerId,
                           storeproducts.StoreId,
                           storeproducts.StoreTypeId,
                           products.ProductImage,
                           products.ProductDescription,
                           products.RRP,
                           products.Brand,
                           products.SKU,
                           COUNT(productsreviews.ID) AS reviews_count,
                           AVG(productsreviews.rating) AS avg_rating,
                           usersfavorite.ID AS is_favorite,
                           storeproducts.Price AS store_price,
                           specials.IsStore,
                           specials.Id as special_id', FALSE);
        $this -> db -> from('products');
        $this -> db -> join('productsreviews', 'productsreviews.ProductId = products.Id', 'left');
        $this -> db -> join('usersfavorite', 'usersfavorite.ProductId = products.Id AND usersfavorite.UserId =' . $this -> session -> userdata('userid'), 'left');
        $this -> db -> join('storeproducts', 'storeproducts.ProductId = products.Id AND storeproducts.RetailerId =' . $retailer_id . " AND storeproducts.StoreId=" . $store_id . " AND storeproducts.IsActive=1");
        $this -> db -> join('categories sub_category', 'sub_category.Id = products.CategoryId', 'left');
        $this -> db -> join('categories parent_category', 'parent_category.Id = products.ParentCategoryId', 'left');
        $this -> db -> join('categories main_parent_category', 'main_parent_category.Id = products.MainCategoryId', 'left');

        $join = 'left';
        if ($show_offer == 1) {
            $join = "";
        }

        $this -> db -> select('productspecials.SpecialQty, productspecials.SpecialPrice');
        $this -> db -> join('productspecials', 'productspecials.ProductId = products.Id AND DATE(productspecials.PriceAppliedFrom) <= "' . date('Y-m-d') . '" AND DATE(productspecials.PriceAppliedTo) >= "' . date('Y-m-d') . '" AND productspecials.RetailerId =' . $retailer_id . " AND productspecials.StoreId=" . $store_id . " AND productspecials.IsActive=1 AND productspecials.IsApproved =1 ", $join);
        $this -> db -> join('specials', 'specials.Id = productspecials.SpecialId', $join);
        $this -> db -> order_by('productspecials.Id', 'DESC');

        $this -> db -> where(array(
            'products.IsActive' => 1,
            'products.IsRemoved' => 0,
        ));

        $this -> db -> order_by('products.Id', 'DESC');
        $this -> db -> group_by('products.Id, specials.Id');

        if ($category_id) {
            if ($category_type == 'main') {
                $this -> db -> where('main_parent_category.Id', $category_id);
            }
            else if ($category_type == 'parent') {
                $this -> db -> where('parent_category.Id', $category_id);
            }
            else {
                $this -> db -> where('sub_category.Id', $category_id);
            }
        }

        if (!empty($price_range)) {
            $filter_query = "";

            //Price Range Filter
            foreach ($price_range as $range) {
                if ($filter_query != '') {
                    $filter_query .= " OR ";
                }

                $filter_query .= '(';

                if ($range['min'])
                    $filter_query .= '(storeproducts.Price >=' . preg_replace("/[^0-9,.]/", "", $range['min']) . " )";

                if ($range['min'] && $range['max'])
                    $filter_query .= 'and';

                if ($range['max'])
                    $filter_query .='(storeproducts.Price <=' . preg_replace("/[^0-9,.]/", "", $range['max']) . ")";

                $filter_query .= ')';
            }

            if ($filter_query != '') {
                $this -> db -> where("(" . $filter_query . ")");
            }
        }

        //Keyword Search
        if (!empty($search)) {
            $this -> db -> like('products.ProductName', $search, 'both');
        }

        if ($product_ids) {
            $this -> db -> where_in('products.Id', $product_ids);
        }
        else {

            if ($last_product_id > 0)
                $this -> db -> where("products.Id <", $last_product_id);
            $this -> db -> limit($this -> config -> item('top_offer_product_limit'));
        }

        $query = $this -> db -> get();
//        echo $this->db->last_query();
        return $query -> result_array();
    }

    // Function to get products count
    public function get_products_count($category_type, $category_id, $show_offer = 0, $search = "") {
        //Get user preference
        $user_preference = $this -> get_user_preference();
        $store_id = $user_preference['StoreId'];
        $retailer_id = $user_preference['RetailerId'];

        $this -> db -> select('products.Id');
        $this -> db -> from('products');
        $this -> db -> join('productsreviews', 'productsreviews.ProductId = products.Id', 'left');
        $this -> db -> join('usersfavorite', 'usersfavorite.ProductId = products.Id AND usersfavorite.UserId =' . $this -> session -> userdata('userid'), 'left');
        $this -> db -> join('storeproducts', 'storeproducts.ProductId = products.Id AND storeproducts.RetailerId =' . $retailer_id . " AND storeproducts.StoreId=" . $store_id . " AND storeproducts.IsActive=1");
        $this -> db -> join('categories sub_category', 'sub_category.Id = products.CategoryId', 'left');
        $this -> db -> join('categories parent_category', 'parent_category.Id = products.ParentCategoryId', 'left');
        $this -> db -> join('categories main_parent_category', 'main_parent_category.Id = products.MainCategoryId', 'left');


        $join = 'left';
        if ($show_offer == 1) {
            $join = "";
        }

        $this -> db -> select('productspecials.SpecialQty, productspecials.SpecialPrice');
        $this -> db -> join('productspecials', 'productspecials.ProductId = products.Id AND DATE(productspecials.PriceAppliedFrom) <= "' . date('Y-m-d') . '" AND DATE(productspecials.PriceAppliedTo) >= "' . date('Y-m-d') . '" AND productspecials.RetailerId =' . $retailer_id . " AND productspecials.StoreId=" . $store_id . " AND productspecials.IsActive=1 AND productspecials.IsApproved =1 ", $join);
        $this -> db -> join('specials', 'specials.Id = productspecials.SpecialId', $join);
        $this -> db -> order_by('productspecials.Id', 'DESC');


        $this -> db -> where(array(
            'products.IsActive' => 1,
            'products.IsRemoved' => 0,
        ));

        //Keyword Search
        if (!empty($search)) {
            $this -> db -> like('products.ProductName', $search, 'both');
        }

        $this -> db -> order_by('products.Id', 'DESC');
        $this -> db -> group_by('products.Id');

        if ($category_id) {
            if ($category_type == 'main') {
                $this -> db -> where('main_parent_category.Id', $category_id);
            }
            else if ($category_type == 'parent') {
                $this -> db -> where('parent_category.Id', $category_id);
            }
            else {
                $this -> db -> where('sub_category.Id', $category_id);
            }
        }

        $query = $this -> db -> get();

        $this -> db -> last_query();

        return $query -> num_rows();
    }
    /* Get product details
     * Param - int: id of the product
     * Return - Array : array containing product details
     */

    public function get_product_details($product_name, $product_id, $special_id = '') {
        $user_preference = $this -> get_user_preference();
        $store_id = $user_preference['StoreId'];
        $retailer_id = $user_preference['RetailerId'];
        $user_id = $this -> session -> userdata('userid');
        $special_append = '';
        $special_join = 'left';
        if($special_id != ''){
            $special_append = ' and specials.Id = '.$special_id;
            $special_join = '';
        }
        $this->db->_protect_identifiers=false;
        $this -> db -> select('products.Id,
                           products.ProductName,
                           products.ProductImage,
                           products.ProductDescription,
                           products.RRP,
                           products.Brand,
                           products.SKU,
                           COUNT(productsreviews.ID) AS reviews_count,
                           AVG(productsreviews.rating) AS avg_rating,
                           usersfavorite.ID AS is_favorite,
                           storeproducts.Price AS store_price,
                           userspricealerts.ID AS price_alert,
                           productspecials.SpecialQty,
                           productspecials.SpecialPrice,
                           case when productspecials.SpecialPrice is not null and productspecials.SpecialPrice > 0 then ((storeproducts.Price*productspecials.SpecialQty) - (productspecials.SpecialPrice)) else 0 end as save_price,
                           case when productspecials.PriceAppliedFrom is null or productspecials.PriceAppliedFrom = \'0000-00-00 00:00:00\' then \'\' else DATE_FORMAT(productspecials.PriceAppliedFrom,\'%d%b\') end as PriceAppliedFrom,
                            case when productspecials.PriceAppliedTo is null or productspecials.PriceAppliedTo = \'0000-00-00 00:00:00\' then \'\' else DATE_FORMAT(productspecials.PriceAppliedTo,\'%d%b\') end as PriceAppliedTo,
                           main_category.CategoryName as main_cat_name,
                           main_category.Id as main_cat_id,
                           parent_category.CategoryName as parent_cat_name,
                           parent_category.Id as parent_cat_id,
                           category.CategoryName as cat_name,
                           category.Id as cat_id,
                           productspecials.PriceAppliedTo,
                           storeproducts.StoreId,
                           retailers.CompanyName', false);
        $this -> db -> from('products');
        $this -> db -> join('productsreviews', 'productsreviews.ProductId = products.Id', 'left');
        $this -> db -> join('usersfavorite', 'usersfavorite.ProductId = products.Id AND usersfavorite.UserId =' . $user_id, 'left');
        $this -> db -> join('userspricealerts', 'userspricealerts.ProductId = products.Id AND userspricealerts.UserId =' . $user_id, 'left');
        $this -> db -> join('storeproducts', 'storeproducts.ProductId = products.Id AND storeproducts.RetailerId =' . $retailer_id . " AND (storeproducts.StoreId=" . $store_id . " OR (storeproducts.StoreId=0 AND storeproducts.PriceForAllStores=1)) AND storeproducts.IsActive=1", 'left');
        $this -> db -> join('productspecials', 'productspecials.ProductId = products.Id AND DATE(productspecials.PriceAppliedFrom) <= "' . date('Y-m-d') . '" AND DATE(productspecials.PriceAppliedTo) >= "' . date('Y-m-d') . '" AND productspecials.RetailerId =' . $retailer_id . " AND (productspecials.StoreId=" . $store_id . " OR (productspecials.StoreId=0 AND productspecials.PriceForAllStores=1)) AND productspecials.IsActive=1 AND productspecials.IsApproved =1 ", 'left');
        $this -> db -> join('retailers', 'retailers.Id = productspecials.RetailerId', 'left');
        $this -> db -> join('categories as main_category', 'products.MainCategoryId = main_category.Id', 'left');
        $this -> db -> join('categories as parent_category', 'products.ParentCategoryId =parent_category.Id', 'left');
        $this -> db -> join('categories as category', 'products.CategoryId =category.Id', 'left');
        $this -> db -> join('specials', 'specials.Id = productspecials.SpecialId '.$special_append, $special_join);

        $this -> db -> where(array(
            'products.IsActive' => 1,
            'products.IsRemoved' => 0
        ));

        $this -> db -> where('products.Id', $product_id);
        $this -> db -> where('products.ProductName', $product_name);
        $this -> db -> limit(1);

        $query = $this -> db -> get();
        
//        echo $this -> db -> last_query();die;


        return $query -> row_array();
    }
    /* Function to add price alert for a product
     * param - array: array to insert
     * return - id: id of inserted record
     */

    public function add_price_alert($data) {
        $this -> db -> insert('userspricealerts', $data);
        return $this -> db -> insert_id();
    }
    /* Function to add price alert for a product
     * param - array: array data to be removed
     */

    public function remove_price_alert($data) {
        $this -> db -> delete('userspricealerts', $data);
    }
    /* Function to get product comparison
     * param - int: product id to compare
     * return - array: array of data
     */

    public function compare_product($product_id) {
        $user_id = $this -> session -> userdata('userid');
        $CI = &get_instance();
        $user_details = $CI -> usermodel -> get_user_details($user_id);

        $lat = $user_details['Latitude'];
        $long = $user_details['Longitude'];
        $lat = $user_details['PrefLatitude'];
        $long = $user_details['PrefLongitude'];
        $distance = $user_details['PrefDistance'];

        $user_preference = $this -> get_user_preference();
        $retailer_id = $user_preference['RetailerId'];

        if ($lat != "" && $long != "") {

            $this -> db -> select('MIN((6371 * acos( cos( radians(' . $lat . ') ) * cos( radians( stores.Latitude ) ) * cos( radians( stores.Longitude ) - radians(' . $long . ') ) + sin( radians(' . $lat . ') ) * sin( radians( stores.Latitude ) ) ) ))  AS distance');
        }

        $this -> db -> select('retailers.Id,
                           retailers.CompanyName,
                           retailers.LogoImage,
                           storeproducts.Price,
                           productspecials.SpecialQty,
                           productspecials.SpecialPrice,
                           stores.StoreName');

        $this -> db -> join('stores', 'stores.RetailerId = retailers.Id AND stores.IsActive =1 AND  stores.IsRemoved =0', 'left');
        $this -> db -> join('storeproducts', 'storeproducts.RetailerId = retailers.Id AND storeproducts.StoreId = stores.Id ', 'left');
        $this -> db -> join('productspecials', 'productspecials.RetailerId =stores.RetailerId AND DATE(productspecials.PriceAppliedFrom) <= "' . date('Y-m-d') . '" AND DATE(productspecials.PriceAppliedTo) >= "' . date('Y-m-d') . '" AND  (productspecials.StoreId= stores.Id OR (productspecials.StoreId=0 AND productspecials.PriceForAllStores=1)) AND productspecials.IsActive=1 AND productspecials.IsApproved =1 AND productspecials.productId= ' . $product_id, 'left');
        $this -> db -> join('specials', 'specials.Id = productspecials.SpecialId', 'left');

        $this -> db -> group_by('retailers.Id');

        if ($lat != "" && $long != "" && $distance != "") {
            $this -> db -> having('distance between 0 and ' . $distance);
        }

        $this -> db -> where_not_in('retailers.Id', $retailer_id);
        $this -> db -> where('retailers.IsActive', 1);

        $this -> db -> where('storeproducts.productId', $product_id);

        $this -> db -> where('retailers.IsRemoved', 0);

        $this -> db -> order_by('storeproducts.Price', 'DESC');
        $this -> db -> order_by('productspecials.SpecialPrice', 'DESC');

        $query = $this -> db -> get('retailers');




        return $query -> result_array();
    }
    /* Function to get product comparison
     * param - int: product id to compare
     * return - array: array of data
     */

    public function compare_product_user($details) {
        $user_id = $this -> session -> userdata('userid');
        $CI = &get_instance();
        $user_details = $CI -> usermodel -> get_user_details($user_id);

//        $lat = $details['lat'];
//        $long = $details['lng'];
        $lat = $user_details['PrefLatitude'];
        $long = $user_details['PrefLongitude'];
        $distance = $details['dist'];

        $user_preference = $this -> get_user_preference();
        $retailer_id = $user_preference['RetailerId'];

        if ($lat != "" && $long != "") {

            $this -> db -> select('MIN((6371 * acos( cos( radians(' . $lat . ') ) * cos( radians( stores.Latitude ) ) * cos( radians( stores.Longitude ) - radians(' . $long . ') ) + sin( radians(' . $lat . ') ) * sin( radians( stores.Latitude ) ) ) ))  AS distance');
        }

        $this -> db -> select('retailers.Id,
                           retailers.CompanyName,
                           retailers.LogoImage,
                           storeproducts.Price,
                           productspecials.SpecialQty,
                           productspecials.SpecialPrice');

        $this -> db -> join('stores', 'stores.RetailerId = retailers.Id AND stores.IsActive =1 AND  stores.IsRemoved =0', 'left');
        $this -> db -> join('storeproducts', 'storeproducts.RetailerId = retailers.Id AND storeproducts.StoreId = stores.Id ', 'left');
        $this -> db -> join('productspecials', 'productspecials.RetailerId =stores.RetailerId AND DATE(productspecials.PriceAppliedFrom) <= "' . date('Y-m-d') . '" AND DATE(productspecials.PriceAppliedTo) >= "' . date('Y-m-d') . '" AND  (productspecials.StoreId= stores.Id OR (productspecials.StoreId=0 AND productspecials.PriceForAllStores=1)) AND productspecials.IsActive=1 AND productspecials.IsApproved =1 AND productspecials.productId= ' . $details['prodId'], 'left');
        $this -> db -> join('specials', 'specials.Id = productspecials.SpecialId', 'left');

        $this -> db -> group_by('retailers.Id');

        if (isset($distance[0]) && $lat != "" && $long != "") {
            $cnt = 1;
            $apnd = '';
            foreach ($distance as $val) {
                if ($cnt == 1) {
                    $apnd .= '(distance between ' . $val['min'] . ' and ' . $val['max'] . ')';
                }
                else {
                    $apnd .= ' or (distance between ' . $val['min'] . ' and ' . $val['max'] . ')';
                }
                $cnt++;
            }
            $this -> db -> having($apnd);
        }

        $this -> db -> where_not_in('retailers.Id', $retailer_id);
        $this -> db -> where('retailers.IsActive', 1);

        $this -> db -> where('storeproducts.productId', $details['prodId']);

        $this -> db -> where('retailers.IsRemoved', 0);

        $this -> db -> order_by('storeproducts.Price', 'DESC');
        $this -> db -> order_by('productspecials.SpecialPrice', 'DESC');

        $query = $this -> db -> get('retailers');



        return $query -> result_array();
    }
    /* Function to get product reviews and rating
     * param - int: product id to compare
     * return - array: array of data
     */

    public function get_product_reviews($product_id) {
        $this -> db -> select('productsreviews.Id,
                           productsreviews.Review,
                           productsreviews.Rating,
                           productsreviews.CreatedOn,
                           productsreviews.UserId,
                           users.FirstName,
                           users.LastName,
                           users.ProfileImage');

        $this -> db -> join('users', 'users.Id = productsreviews.UserId', 'left');

        $this -> db -> where(array(
            'productsreviews.ProductId' => $product_id
        ));

        $this -> db -> order_by('productsreviews.CreatedOn', 'DESC');

        $query = $this -> db -> get('productsreviews');
        return $query -> result_array();
    }

    public function add_review_rating($product_id, $review_comment, $review_rating) {
        $user_id = $this -> session -> userdata('userid');
        $this -> db -> select(array('Id'));
        $this -> db -> from('productsreviews');
        $this -> db -> where(array(
            'UserId' => $user_id,
            'ProductId' => $product_id
        ));

        $this -> db -> limit(1);
        $query = $this -> db -> get();

        if ($query -> num_rows() == 0) {
            $data = array(
                'UserId' => $user_id,
                'ProductId' => $product_id,
                'Review' => $review_comment,
                'Rating' => $review_rating,
                'CreatedOn' => date("Y-m-d H:i:s")
            );

            $this -> db -> insert('productsreviews', $data);


            return $this -> db -> insert_id();
        }
        else {
            return FALSE;
        }
    }
    /* Add to basket
     *  param - product_id : Id of product to add to basket
     *  return - Id of inserted record
     */

    public function add_to_basket($special_id, $product_id, $count) {
        $ins_arr = array('SpecialId' => $special_id, 'ProductId' => $product_id
            , 'UserId' => $this -> session -> userdata('userid')
        );

        $this -> db -> select("Id");
        $this -> db -> from("userbasket");
        $this -> db -> where(array( 'SpecialId' => $special_id, 'ProductId' => $product_id, 'UserId' => $this -> session -> userdata('userid')
        ));
        $query = $this -> db -> get();

        if ($query -> num_rows() > 0) {
            return 'duplicate';
        }
        else {
            $ins_arr = array(
                'SpecialId' => $special_id,
                'ProductId' => $product_id
                , 'ProductCount' => $count
                , 'UserId' => $this -> session -> userdata('userid')
                , 'CreatedBy' => $this -> session -> userdata('userid')
                , 'CreatedOn' => date("Y/m/d H:i:s")
            );
            $this -> db -> insert('userbasket', $ins_arr);
            return $this -> db -> insert_id();
        }
    }

    public function get_favorite_products() {

        $user_preference = $this -> get_user_preference();
        $retailer_id = $user_preference['RetailerId'];
        $store_id = $user_preference['StoreId'];

        $user_id = $this -> session -> userdata('userid');

        $this -> db -> select('products.Id,
                           products.ProductName,
                           products.ProductImage,
                           products.ProductDescription,
                           products.RRP,
                           products.Brand,
                           products.SKU,
                           COUNT(productsreviews.ID) AS reviews_count,
                           AVG(productsreviews.rating) AS avg_rating,
                           usersfavorite.ID AS is_favorite,
                           storeproducts.Price AS store_price,
                           productspecials.SpecialQty,
                           productspecials.SpecialPrice,
                           productspecials.Id as SpecialId,
                           specials.IsStore,
                           specials.Id as special_id
                           ');

        $this -> db -> join('productsreviews', 'productsreviews.ProductId = usersfavorite.ProductId', 'left');
        $this -> db -> join('products', 'products.Id = usersfavorite.ProductId');
        $this -> db -> join('storeproducts', 'storeproducts.ProductId = products.Id AND storeproducts.RetailerId =' . $retailer_id . " AND (storeproducts.StoreId=" . $store_id . " OR (storeproducts.StoreId=0 AND storeproducts.PriceForAllStores=1)) AND storeproducts.IsActive=1");
        $this -> db -> join('productspecials', 'productspecials.ProductId = products.Id AND DATE(productspecials.PriceAppliedFrom) <= "' . date('Y-m-d') . '" AND DATE(productspecials.PriceAppliedTo) >= "' . date('Y-m-d') . '" AND productspecials.RetailerId =' . $retailer_id . " AND (productspecials.StoreId=" . $store_id . " OR (productspecials.StoreId=0 AND productspecials.PriceForAllStores=1)) AND productspecials.IsActive=1 AND productspecials.IsApproved =1 AND productspecials.SpecialId = usersfavorite.SpecialId", 'left');
        $this -> db -> join('specials', 'specials.Id = productspecials.SpecialId', 'left');

        $this -> db -> where(array(
            'products.IsActive' => 1,
            'products.IsRemoved' => 0,
            'usersfavorite.UserId' => $user_id,
        ));

        $this -> db -> order_by('productspecials.Id', 'DESC');        
        //$this -> db -> group_by('products.Id');
        $this -> db -> group_by('usersfavorite.ProductId, usersfavorite.SpecialId');

        $query = $this -> db -> get('usersfavorite');

        //echo $this -> db -> last_query(); exit;   
        
        return $query -> result_array();
    }

    public function get_alert_products() {
        $user_preference = $this -> get_user_preference();
        $retailer_id = $user_preference['RetailerId'];
        $store_id = $user_preference['StoreId'];

        $user_id = $this -> session -> userdata('userid');

        $this -> db -> select('products.Id,
                           products.ProductName,
                           products.ProductImage,
                           products.ProductDescription,
                           products.RRP,
                           products.Brand,
                           products.SKU,
                           COUNT(productsreviews.ID) AS reviews_count,
                           AVG(productsreviews.rating) AS avg_rating,
                           usersfavorite.ID AS is_favorite,
                           storeproducts.Price AS store_price,
                            productspecials.SpecialQty,
                           productspecials.SpecialPrice');

        $this -> db -> join('products', 'products.Id = userspricealerts.ProductId');
        $this -> db -> join('productsreviews', 'productsreviews.ProductId = products.Id', 'left');
        $this -> db -> join('usersfavorite', 'usersfavorite.ProductId = products.Id AND usersfavorite.UserId =' . $user_id, 'left');
        $this -> db -> join('storeproducts', 'storeproducts.ProductId = products.Id AND storeproducts.RetailerId =' . $retailer_id . " AND (storeproducts.StoreId=" . $store_id . " OR (storeproducts.StoreId=0 AND storeproducts.PriceForAllStores=1)) AND storeproducts.IsActive=1");
        $this -> db -> join('productspecials', 'productspecials.ProductId = products.Id AND DATE(productspecials.PriceAppliedFrom) <= "' . date('Y-m-d') . '" AND DATE(productspecials.PriceAppliedTo) >= "' . date('Y-m-d') . '" AND productspecials.RetailerId =' . $retailer_id . " AND (productspecials.StoreId=" . $store_id . " OR (productspecials.StoreId=0 AND productspecials.PriceForAllStores=1)) AND productspecials.IsActive=1 AND productspecials.IsApproved =1", 'left');
        $this -> db -> join('specials', 'specials.Id = productspecials.SpecialId', 'left');

        $this -> db -> where(array(
            'products.IsActive' => 1,
            'products.IsRemoved' => 0,
            'userspricealerts.UserId' => $user_id,
        ));

        $this -> db -> order_by('productspecials.Id', 'DESC');

        $this -> db -> group_by('products.Id');


        $query = $this -> db -> get('userspricealerts');

        return $query -> result_array();
    }

    public function delete_favorite_products() {
        $this -> db -> where('UserId', $this -> session -> userdata('userid'));
        $this -> db -> delete('usersfavorite');
    }

    public function delete_pricealerts_products() {
        $this -> db -> where('UserId', $this -> session -> userdata('userid'));
        $this -> db -> delete('userspricealerts');
    }

    public function add_product_view($product_id, $user_id) {
        $user_preference = $this -> get_user_preference();
        $retailer_id = $user_preference['RetailerId'];
        $store_id = $user_preference['StoreId'];

        $this -> db -> select('(TIME_TO_SEC(current_timestamp()) - TIME_TO_SEC(ViewDate))/60 as time_diff', false);
        $this -> db -> where(array(
            'ProductId' => $product_id,
            'RetailerId' => $retailer_id,
            'UserId' => $user_id,
            'StoreId' => $store_id,
        ));
        $this -> db -> order_by('Id', 'desc');
        $this -> db -> limit(1);
        $query = $this -> db -> get('productviews');
        $add = TRUE;
        if ($query -> num_rows() == 1) {
            $result_array = $query -> row_array();
            if ($result_array['time_diff'] < 15) {
                $add = FALSE;
            }
        }
        if ($add) {
            $data = array(
                'ProductId' => $product_id,
                'RetailerId' => $retailer_id,
                'StoreId' => $store_id,
                'UserId' => $user_id
                //'ViewDate' => date("Y-m-d H:m:s")
            );

            $this -> db -> insert('productviews', $data);
            return $this -> db -> insert_id();
        }
        return TRUE;
    }

    public function get_user_location_preferences($user_id) {
        $this -> db -> select('PrefLatitude,
                           PrefLongitude,
                           PrefDistance');
        $this -> db -> where(array(
            'Id' => $user_id
        ));
        $query = $this -> db -> get('users');
        return $query -> result_array();
    }

//    public function get_chart_details($product_id) {
//        $query = $this -> db -> query("select concat(month_name,' - ',year_from) as month_year,avg_price  from(
//SELECT DATE_FORMAT(STR_TO_DATE(month(PriceAppliedFrom), '%m'), '%b') as month_name, 
//year(PriceAppliedFrom) as year_from, month(PriceAppliedFrom) as month_from, avg(SpecialPrice) as avg_price FROM productspecials where ProductId = " . $product_id . " 
//group by month(PriceAppliedFrom),year(PriceAppliedFrom) order by year(PriceAppliedFrom) desc,month(PriceAppliedFrom) desc limit 12) 
//productspecials order by year_from asc, month_from asc");
//        return $query -> result_array();
//    }

    public function get_chart_details($product_id) {
        $query = $this -> db -> query("select case when a.SpecialQty > 1 then ROUND(a.SpecialPrice/a.SpecialQty, 2) else ROUND(a.SpecialPrice,2) end as SpecialPrice, concat(DATE_FORMAT(a.CreatedOn,'%d'),'-',DATE_FORMAT(STR_TO_DATE(month(a.CreatedOn), '%m'), '%b')) as day_month,
b.CompanyName,c.StoreName,e.RRP,e.CreatedOn
from productspecials as a 
join retailers as b on a.RetailerId = b.Id
join stores as c on a.StoreId = c.Id
join storeproducts as d on d.RetailerId = c.RetailerId and d.StoreTypeId = c.StoreTypeId and c.Id = d.StoreId and a.ProductId = d.ProductId
join products as e on e.Id = d.ProductId
join specials as f on f.Id = a.SpecialId and f.IsActive = 1 and f.IsRemoved = 0
WHERE (a.CreatedOn BETWEEN SUBDATE(CURDATE(), INTERVAL " . PRODUCT_CHART . " MONTH) AND NOW()) 
and a.ProductId = " . $product_id . " and a.IsActive = 1 and a.IsApproved = 1 and b.IsActive = 1 and b.IsRemoved = 0 and c.IsActive = 1 and c.IsRemoved = 0 group by SpecialPrice,day_month order by day_month");
        return $query -> result_array();
    }

    public function get_product_views($product_id) {
        $this -> db -> select('count(ProductId) as count')
            -> from('productviews')
            -> where('ProductId', $product_id);
        $query = $this -> db -> get();
        return $query -> row_array();
    }

    public function get_product_shares($product_id) {
        $this -> db -> select('count(ProductId) as count')
            -> from('product_shares')
            -> where('ProductId', $product_id)
            -> where('IsActive', 1)
            -> where('IsRemoved', 0);
        $query = $this -> db -> get();
        return $query -> row_array();
    }

    public function insert_share_details($insert_data) {
        $this -> db -> insert('product_shares', $insert_data);
    }

    public function get_store_details($store_id) {
        $this -> db -> select("a.StoreName,b.OpenCloseDay, b.OpenCloseTimeFrom, case when a.Latitude is null then '' else a.Latitude end as Latitude, case when a.Longitude is null then '' else a.Longitude end as Longitude, a.StreetAddress, case when a.Zip is null then '' else a.Zip end as Zip, a.ContactPersonNumber, c.Name as StateName,d.LogoImage", false)
            -> from('stores as a')
            -> join('storetimings as b', 'a.Id = b.StoreId  and `b`.`OpenCloseStatus` = \'1\'', 'left')
            -> join('state as c', 'c.Id = a.StateId', 'left')
            -> join('retailers as d', 'd.Id = a.RetailerId')
            -> where('a.Id', $store_id);
        $query = $this -> db -> get();
        return $query -> result_array();
    }

    public function get_related_products($retailer_id, $store_format_id, $store_id, $product_id, $MainCategoryId, $ParentCategoryId, $CategoryId, $user_id) {
        $this -> db -> select('a.Id,a.ProductName,a.ProductImage,a.ProductDescription,a.RRP,a.Brand,a.SKU,COUNT(f.ID) AS reviews_count,case when AVG(f.rating) is null then \'\' else AVG(f.rating) end AS avg_rating,g.Price AS store_price,a.CategoryId,case when h.ID is null  then \'\' else h.ID end  AS is_favorite,b.SpecialQty,b.SpecialPrice', false)
            -> from('products as a')
            -> join('productspecials as b', 'a.Id = b.ProductId and now() between b.PriceAppliedFrom and b.PriceAppliedTo')
            -> join('categories as c', 'c.Id = a.MainCategoryId')
            -> join('categories as d', 'd.Id = a.ParentCategoryId')
            -> join('categories as e', 'e.Id = a.CategoryId', 'left')
            -> join('productsreviews as f', 'f.ProductId = b.ProductId', 'left')
            -> join('storeproducts as g', 'g.ProductId = a.Id AND g.RetailerId = ' . $retailer_id . ' AND (g.StoreId=' . $store_id . ' OR (g.StoreId=0 AND g.PriceForAllStores=1)) AND g.IsActive=1')
            -> join('usersfavorite as h', 'h.ProductId = a.Id AND h.UserId =' . $user_id, 'left')
            -> join('specials as i', 'i.Id = b.SpecialId and i.IsActive = 1 and i.IsRemoved = 0')
            -> where('a.Id != ' . $product_id)
            -> where('if(e.Id is null,a.ParentCategoryId = ' . $ParentCategoryId . ',a.CategoryId = ' . $CategoryId . ')')
            -> where('b.RetailerId = ' . $retailer_id . ' and b.StoreTypeId = ' . $store_format_id . ' and b.StoreId = ' . $store_id)
            -> where('a.IsActive', 1)
            -> where('a.IsRemoved', 0)
            -> group_by('a.id')
            -> having('a.id is not null')
            -> limit(20);
        $query = $this -> db -> get();
        if ($query -> num_rows() > 0) {
            return $query -> result_array();
        }
        return FALSE;
    }

    public function get_categories($product_id) {
        $this -> db -> select('MainCategoryId,ParentCategoryId,CategoryId')
            -> from('products')
            -> where('Id', $product_id)
            -> where('IsActive', 1)
            -> where('IsRemoved', 0);
        $query = $this -> db -> get();
        if ($query -> num_rows() > 0) {
            return $query -> row_array();
        }
        return FALSE;
    }
}
