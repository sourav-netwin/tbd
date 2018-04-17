<?php

/*
 * Author: Name:AS
 * Purpose: Model for controlling database interactions regarding the content.
 * Date:24-10-2016
 * Dependency: None
 */

class Searchmodel extends CI_Model {
    /*
     * Purpose: Constructor.
     * Date: 24-10-2016
     * Input Parameter: None
     *  Output Parameter: None
     */

    public function __construct() {
        parent::__construct();
    }

    public function get_products($category_type, $category_id, $price_range = array(), $last_product_id = 0, $show_offer = 0, $product_ids = array(), $search = "", $user_id, $start = 0) {
        //Get user preference
        $user_preference = $this -> get_user_preference($user_id);
        $store_id = $user_preference['StoreId'];
        $retailer_id = $user_preference['RetailerId'];

        $this -> db -> select('products.Id,
                           products.ProductName,
                           products.ProductImage,
                           products.ProductDescription,
                           products.RRP,
                           products.Brand,
                           products.SKU,
                           COUNT(productsreviews.ID) AS reviews_count,
                           AVG(productsreviews.rating) AS avg_rating,
                           case when usersfavorite.SpecialId = specials.Id then "1" else "0" end as is_favorite,
                           storeproducts.Price AS store_price,
                           case when productspecials.PriceAppliedFrom is null or productspecials.PriceAppliedFrom = \'0000-00-00 00:00:00\' then \'\' else DATE_FORMAT(productspecials.PriceAppliedFrom,\'%d %b\') end as PriceAppliedFrom,
                           case when productspecials.PriceAppliedTo is null or productspecials.PriceAppliedTo = \'0000-00-00 00:00:00\' then \'\' else DATE_FORMAT(productspecials.PriceAppliedTo,\'%d %b\') end as PriceAppliedTo,
                           case when specials.IsStore is not null then specials.IsStore else 0 end as IsStore,
                           case when specials.Id is not null then specials.Id else 0 end as special_id', FALSE);
        $this -> db -> from('products');
        $this -> db -> join('productsreviews', 'productsreviews.ProductId = products.Id', 'left');
        $this -> db -> join('usersfavorite', 'usersfavorite.ProductId = products.Id AND usersfavorite.UserId =' . $user_id, 'left');
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
        $this -> db -> join('specials', 'specials.Id = productspecials.SpecialId and specials.IsActive = 1 and specials.IsRemoved = 0',$join);
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
            $this -> db -> limit($this -> config -> item('top_offer_product_limit'), $start);
        }

        $query = $this -> db -> get();
        //echo $this->db->last_query();exit;
        return $query -> result_array();
    }

    public function get_products_WIP($category_type, $category_id, $price_range = array(), $last_product_id = 0, $show_offer = 0, $product_ids = array(), $search = "", $user_id, $start = 0) {
        //Get user preference
        $user_preference = $this -> get_user_preference($user_id);
        $store_id = $user_preference['StoreId'];
        $retailer_id = $user_preference['RetailerId'];

        $subquery1 ='( 
                SELECT count(productsreviews.ID) as reviews_count 
                FROM productsreviews 
                WHERE `productsreviews`.`ProductId` = `products`.`Id`
            ) as reviews_count';       
        $this->db->select($subquery1);
        
        $subquery2 ='( 
                SELECT AVG(productsreviews.rating) AS avg_rating
                FROM productsreviews 
                WHERE `productsreviews`.`ProductId` = `products`.`Id`
            ) as avg_rating';       
        $this->db->select($subquery2);
        
        /*
        $subquery3 ='( 
                SELECT storeproducts.Price 
                FROM storeproducts 
                WHERE `storeproducts`.`ProductId` = `products`.`Id` AND storeproducts.RetailerId ='.$retailer_id.' 
                AND storeproducts.StoreId='.$store_id.' AND storeproducts.IsActive=1
            ) as store_price';       
        $this->db->select($subquery3);
        */
        
        $this -> db -> select('products.Id,
                           products.ProductName,
                           products.ProductImage,
                           products.ProductDescription,
                           products.RRP,
                           products.Brand,
                           products.SKU,
                           storeproducts.Price as store_price,
                           case when productspecials.PriceAppliedFrom is null or productspecials.PriceAppliedFrom = \'0000-00-00 00:00:00\' then \'\' else DATE_FORMAT(productspecials.PriceAppliedFrom,\'%d %b\') end as PriceAppliedFrom,
                           case when productspecials.PriceAppliedTo is null or productspecials.PriceAppliedTo = \'0000-00-00 00:00:00\' then \'\' else DATE_FORMAT(productspecials.PriceAppliedTo,\'%d %b\') end as PriceAppliedTo,
                           case when specials.IsStore is not null then specials.IsStore else 0 end as IsStore,
                           case when specials.Id is not null then specials.Id else 0 end as special_id', FALSE);
        //$this -> db -> from('products','storeproducts');  
        $this->db->from('products, storeproducts');
        //$this -> db -> join('usersfavorite', 'usersfavorite.ProductId = products.Id AND usersfavorite.UserId =' . $user_id, 'left');        
        $this -> db -> join('categories sub_category', 'sub_category.Id = products.CategoryId', 'left');
        $this -> db -> join('categories parent_category', 'parent_category.Id = products.ParentCategoryId', 'left');
        $this -> db -> join('categories main_parent_category', 'main_parent_category.Id = products.MainCategoryId', 'left');
        

        $join = 'left';
        if ($show_offer == 1) {
            $join = "";
        }

        $this -> db -> select('productspecials.SpecialQty, productspecials.SpecialPrice');
        $this -> db -> join('productspecials', 'productspecials.ProductId = products.Id AND DATE(productspecials.PriceAppliedFrom) <= "' . date('Y-m-d') . '" AND DATE(productspecials.PriceAppliedTo) >= "' . date('Y-m-d') . '" AND productspecials.RetailerId =' . $retailer_id . " AND productspecials.StoreId=" . $store_id . " AND productspecials.IsActive=1 AND productspecials.IsApproved =1 ", $join);
        $this -> db -> join('specials', 'specials.Id = productspecials.SpecialId and specials.IsActive = 1 and specials.IsRemoved = 0',$join);
        

        $this -> db -> where(array(
            'products.IsActive' => 1,
            'products.IsRemoved' => 0,
        ));
        
        $cond1 ="storeproducts.ProductId = products.Id";
        $this -> db -> where($cond1);
        
        $this -> db -> where(array(
            'storeproducts.RetailerId' => $retailer_id,
            'storeproducts.StoreId' => $store_id,
            'storeproducts.IsActive'=> 1
        ));

        //$this -> db -> order_by('products.Id', 'DESC');
        $this -> db -> order_by('products.ProductName', 'ASC');
        //$this -> db -> order_by('productspecials.Id', 'DESC');
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
            
            //$this -> db -> limit($this -> config -> item('top_offer_product_limit'), $start);
        }

        $query = $this -> db -> get();
        //echo $this->db->last_query();exit;
        return $query -> result_array();
    }
    
    public function get_user_preference($user_id) {
        $this -> db -> select('u.RetailerId,u.StoreId,r.CompanyName as RetailerName ,s.StoreName');
        $this -> db -> from('userpreferredbrands as u');        
        $this -> db -> join('retailers as r', 'r.Id = u.RetailerId', 'left');
        $this -> db -> join('stores as s', 's.Id = u.StoreId', 'left');
        $this -> db -> where(array(
            'u.IsActive' => 1,
            'u.IsRemoved' => 0,
            'u.UserId' => $user_id
        ));
        $this -> db -> limit(1);
        $query = $this -> db -> get();

        return $query -> row_array();
    }
    
    public function get_user_preference_details($user_id) {
        $this -> db -> select('u.RetailerId,u.StoreId,r.CompanyName as RetailerName ,s.StoreName, users.PrefLatitude,users.PrefLongitude,users.PrefDistance');
        $this -> db -> from('userpreferredbrands as u');
        $this -> db -> join('users', 'users.Id = u.UserId', 'left');
        $this -> db -> join('retailers as r', 'r.Id = u.RetailerId', 'left');
        $this -> db -> join('stores as s', 's.Id = u.StoreId', 'left');
        $this -> db -> where(array(
            'u.IsActive' => 1,
            'u.IsRemoved' => 0,
            'u.UserId' => $user_id
        ));
        $this -> db -> limit(1);
        $query = $this -> db -> get();

        return $query -> row_array();
    }
    
    
    /*
     *  Function to get the stores where product is in any special 
     */
    
    public function get_product_specials_stores($user_id, $product_id) {
        # Get user preference
        $user_preference = $this -> get_user_preference($user_id);
        $store_id = $user_preference['StoreId'];
        $retailer_id = $user_preference['RetailerId'];
                
        
        $this -> db -> select('ps.Id as productSpecialId, ps.ProductId, ps.RetailerId, ps.StoreTypeId, ps.StoreId, p.ProductName, r.CompanyName as RetailerName, s.StoreName,ps.SpecialQty, ps.SpecialPrice',FALSE);
        $subquery1 ='( 
                SELECT storeproducts.Price 
		 FROM storeproducts 
		 WHERE `storeproducts`.`ProductId` = p.`Id` AND storeproducts.RetailerId = ps.RetailerId
		 AND storeproducts.StoreId=ps.StoreId AND storeproducts.IsActive=1
                 limit 1
            ) as store_price';       
        $this->db->select($subquery1);
        
        $this -> db -> from('productspecials as ps');
        $this -> db -> join('products as p', 'p.Id = ps.ProductId');
        $this -> db -> join('retailers as r', 'r.Id = ps.RetailerId');
        $this -> db -> join('stores as s', 's.Id = ps.StoreId');
        $this -> db -> where(array(
            'ps.IsActive' => 1,
            'ps.IsApproved' => 1,
            'ps.ProductId' => $product_id
        ));
        
        $this -> db -> where('ps.PriceAppliedFrom <= ', date('Y-m-d'));
        $this -> db -> where('ps.PriceAppliedTo >= ', date('Y-m-d'));
        
        if( $store_id > 0 )
        {
            $this -> db -> where('ps.StoreId <> ', $store_id);
        }
         
        //$this -> db -> order_by('store_price', 'ASC');
        $this -> db -> order_by('s.StoreName', 'ASC');
        
        $query = $this -> db -> get();
        //echo $this->db->last_query();exit;
        return $query -> result_array();
    }
    
    
    
   public function get_categories_with_products($search = "", $user_id) {
        //Get user preference
        $user_preference = $this -> get_user_preference($user_id);
        $store_id = $user_preference['StoreId'];
        $retailer_id = $user_preference['RetailerId'];
        
        $this -> db -> select('products.Id,
                           products.ProductName,
                           products.ProductImage,
                           products.ProductDescription,
                           products.RRP,
                           products.Brand,
                           products.SKU,
                           storeproducts.Price as store_price,
                           case when productspecials.PriceAppliedFrom is null or productspecials.PriceAppliedFrom = \'0000-00-00 00:00:00\' then \'\' else DATE_FORMAT(productspecials.PriceAppliedFrom,\'%d %b\') end as PriceAppliedFrom,
                           case when productspecials.PriceAppliedTo is null or productspecials.PriceAppliedTo = \'0000-00-00 00:00:00\' then \'\' else DATE_FORMAT(productspecials.PriceAppliedTo,\'%d %b\') end as PriceAppliedTo,
                           case when specials.IsStore is not null then specials.IsStore else 0 end as IsStore,
                           case when specials.Id is not null then specials.Id else 0 end as special_id,
                           case when products.CategoryId > 0  then concat(parent_category.CategoryName  , " ", sub_category.CategoryName ) else parent_category.CategoryName end as ProductCategoryName,
                           case when products.CategoryId > 0  then products.CategoryId else products.ParentCategoryId end as ProductCategoryId
                           ', FALSE);
        $this->db->from('products, storeproducts');
        $this -> db -> join('categories sub_category', 'sub_category.Id = products.CategoryId', 'left');
        $this -> db -> join('categories parent_category', 'parent_category.Id = products.ParentCategoryId', 'left');
        $this -> db -> join('categories main_parent_category', 'main_parent_category.Id = products.MainCategoryId', 'left');
        $join = 'left';
        
        $this -> db -> select('productspecials.SpecialQty, productspecials.SpecialPrice');
        $this -> db -> join('productspecials', 'productspecials.ProductId = products.Id AND DATE(productspecials.PriceAppliedFrom) <= "' . date('Y-m-d') . '" AND DATE(productspecials.PriceAppliedTo) >= "' . date('Y-m-d') . '" AND productspecials.RetailerId =' . $retailer_id . " AND productspecials.StoreId=" . $store_id . " AND productspecials.IsActive=1 AND productspecials.IsApproved =1 ", $join);
        $this -> db -> join('specials', 'specials.Id = productspecials.SpecialId and specials.IsActive = 1 and specials.IsRemoved = 0',$join);
        

        $this -> db -> where(array(
            'products.IsActive' => 1,
            'products.IsRemoved' => 0,
        ));
        
        $cond1 ="storeproducts.ProductId = products.Id";
        $this -> db -> where($cond1);
        
        $this -> db -> where(array(
            'storeproducts.RetailerId' => $retailer_id,
            'storeproducts.StoreId' => $store_id,
            'storeproducts.IsActive'=> 1
        ));
        
        $this -> db -> order_by('ProductCategoryName', 'ASC');
        $this -> db -> group_by('products.Id, specials.Id');

        //Keyword Search
        if (!empty($search)) {
            $this -> db -> like('products.ProductName', $search, 'both');
        }

        $query = $this -> db -> get();
        //echo $this->db->last_query();exit;
        return $query -> result_array();
    }
    
    
    
    public function get_products_by_category($search = "", $user_id, $category_id=0) {
        //Get user preference
        $user_preference = $this -> get_user_preference($user_id);
        $store_id = $user_preference['StoreId'];
        $retailer_id = $user_preference['RetailerId'];

        
        $subquery1 ='( 
                SELECT count(productsreviews.ID) as reviews_count 
                FROM productsreviews 
                WHERE `productsreviews`.`ProductId` = `products`.`Id`
            ) as reviews_count';       
        $this->db->select($subquery1);
        
        $subquery2 ='( 
                SELECT AVG(productsreviews.rating) AS avg_rating
                FROM productsreviews 
                WHERE `productsreviews`.`ProductId` = `products`.`Id`
            ) as avg_rating';       
        $this->db->select($subquery2);
        
        
        $this -> db -> select('products.Id,
                           products.ProductName,
                           products.ProductImage,
                           products.ProductDescription,
                           products.RRP,
                           products.Brand,
                           products.SKU,
                           storeproducts.Price as store_price,
                           case when productspecials.PriceAppliedFrom is null or productspecials.PriceAppliedFrom = \'0000-00-00 00:00:00\' then \'\' else DATE_FORMAT(productspecials.PriceAppliedFrom,\'%d %b\') end as PriceAppliedFrom,
                           case when productspecials.PriceAppliedTo is null or productspecials.PriceAppliedTo = \'0000-00-00 00:00:00\' then \'\' else DATE_FORMAT(productspecials.PriceAppliedTo,\'%d %b\') end as PriceAppliedTo,
                           case when specials.IsStore is not null then specials.IsStore else 0 end as IsStore,
                           case when specials.Id is not null then specials.Id else 0 end as special_id,
                           case when products.CategoryId > 0  then concat(parent_category.CategoryName  , " ", sub_category.CategoryName ) else parent_category.CategoryName end as ProductCategoryName,
                           case when products.CategoryId > 0  then products.CategoryId else products.ParentCategoryId end as ProductCategoryId
                           ', FALSE);
        $this->db->from('products, storeproducts');
        $this -> db -> join('categories sub_category', 'sub_category.Id = products.CategoryId', 'left');
        $this -> db -> join('categories parent_category', 'parent_category.Id = products.ParentCategoryId', 'left');
        $this -> db -> join('categories main_parent_category', 'main_parent_category.Id = products.MainCategoryId', 'left');
        $join = 'left';
        
        $this -> db -> select('productspecials.SpecialQty, productspecials.SpecialPrice');
        $this -> db -> join('productspecials', 'productspecials.ProductId = products.Id AND DATE(productspecials.PriceAppliedFrom) <= "' . date('Y-m-d') . '" AND DATE(productspecials.PriceAppliedTo) >= "' . date('Y-m-d') . '" AND productspecials.RetailerId =' . $retailer_id . " AND productspecials.StoreId=" . $store_id . " AND productspecials.IsActive=1 AND productspecials.IsApproved =1 ", $join);
        $this -> db -> join('specials', 'specials.Id = productspecials.SpecialId and specials.IsActive = 1 and specials.IsRemoved = 0',$join);
        $this -> db -> order_by('productspecials.Id', 'DESC');

        $this -> db -> where(array(
            'products.IsActive' => 1,
            'products.IsRemoved' => 0,
        ));
        
        $cond1 ="storeproducts.ProductId = products.Id";
        $this -> db -> where($cond1);
        
        $this -> db -> where(array(
            'storeproducts.RetailerId' => $retailer_id,
            'storeproducts.StoreId' => $store_id,
            'storeproducts.IsActive'=> 1
        ));

        $this -> db -> order_by('products.Id', 'DESC');
        $this -> db -> group_by('products.Id, specials.Id');

        //Keyword Search
        if (!empty($search)) {
            $this -> db -> like('products.ProductName', $search, 'both');
        }
        
        if($category_id > 0 ){
           $categoryCond ="( products.CategoryId = $category_id or products.ParentCategoryId = $category_id )";       
            $this->db->where($categoryCond); 
        }

        $query = $this -> db -> get();
        //echo $this->db->last_query();exit;
        return $query -> result_array();
    }
    
    
    public function get_products_by_category_and_brand($search = "", $user_id, $category_id=0, $brand="", $special_only="") {
        //Get user preference
        $user_preference = $this -> get_user_preference($user_id);
        $store_id = $user_preference['StoreId'];
        $retailer_id = $user_preference['RetailerId'];
        $productCount = 0;
        
        if($category_id > 0 ){
           # Function to check product count for the category  
           $productCount = $this->get_product_count($category_id);
        }
        
        # Get productIds         
        $productIds = array(); 
        
        
        $this -> db -> select('products.Id', FALSE);
        $this->db->from('products');
		$this -> db -> where(array(
			'products.IsActive' => 1,
			'products.IsRemoved' => 0
		));	
        $categorySearchCond = "";
        if (!empty($search)) 
        {
            $categorySearchCond = "( products.ProductDescription REGEXP '[[:<:]]".$search."[[:>:]]' = 1 OR products.ProductName REGEXP '[[:<:]]".$search."[[:>:]]' = 1 )";
            $this -> db -> where($categorySearchCond);
        }
        
        if($category_id > 0 ){
           if($productCount > 0 )
           {
               
             $this -> db -> where('products.CategoryId', $category_id);
           }else{
               
            $this -> db -> where('products.ParentCategoryId', $category_id);
            $this -> db -> where('products.CategoryId', 0);
           }
        }
        
        $productIdsQuery = $this -> db -> get();
        //echo $this->db->last_query();exit;
        $results = $productIdsQuery -> result_array();
        
        foreach($results as $result)
        {
          $productIds[] =  $result['Id']; 
        }
        
        
        # Get actual product information 
        
        $subquery1 ='( 
                SELECT count(productsreviews.ID) as reviews_count 
                FROM productsreviews 
                WHERE `productsreviews`.`ProductId` = `products`.`Id`
            ) as reviews_count';       
        $this->db->select($subquery1);
        
        $subquery2 ='( 
                SELECT AVG(productsreviews.rating) AS avg_rating
                FROM productsreviews 
                WHERE `productsreviews`.`ProductId` = `products`.`Id`
            ) as avg_rating';       
        $this->db->select($subquery2);
        
        //products.ProductName,
        //case when products.HouseId is null then products.ProductDescription else concat(retailers.CompanyName," ",products.ProductName) end as ProductName,
        
        $this -> db -> select('products.Id,
                           case when products.HouseId is null then products.ProductDescription else concat(retailers.CompanyName," ",products.ProductName) end as ProductName,
                           products.ProductImage,
                           products.ProductDescription,
                           products.RRP,
                           products.Brand,
                           products.SKU,
                           storeproducts.Price as store_price,
                           case when productspecials.PriceAppliedFrom is null or productspecials.PriceAppliedFrom = \'0000-00-00 00:00:00\' then \'\' else DATE_FORMAT(productspecials.PriceAppliedFrom,\'%d %b\') end as PriceAppliedFrom,
                           case when productspecials.PriceAppliedTo is null or productspecials.PriceAppliedTo = \'0000-00-00 00:00:00\' then \'\' else DATE_FORMAT(productspecials.PriceAppliedTo,\'%d %b\') end as PriceAppliedTo,
                           case when specials.IsStore is not null then specials.IsStore else 0 end as IsStore,
                           case when specials.Id is not null then specials.Id else 0 end as special_id
                           ', FALSE);
        $this->db->from('products, storeproducts');  
        
        if($special_only == 'No')
        {
           $join = 'left'; 
        }
        
        
        $this -> db -> select('productspecials.SpecialQty, productspecials.SpecialPrice');
        $this -> db -> join('retailers', 'retailers.Id = products.HouseId and retailers.IsActive = 1 and retailers.IsRemoved = 0', 'left');
        $this -> db -> join('productspecials', 'productspecials.ProductId = products.Id AND DATE(productspecials.PriceAppliedFrom) <= "' . date('Y-m-d') . '" AND DATE(productspecials.PriceAppliedTo) >= "' . date('Y-m-d') . '" AND productspecials.RetailerId =' . $retailer_id . " AND productspecials.StoreId=" . $store_id . " AND productspecials.IsActive=1 AND productspecials.IsApproved =1 ", $join);
        $this -> db -> join('specials', 'specials.Id = productspecials.SpecialId and specials.IsActive = 1 and specials.IsRemoved = 0',$join);
        
        $this -> db -> where(array(
            'products.IsActive' => 1,
            'products.IsRemoved' => 0,
        ));
        
        if($productIds)
        {
          $this -> db -> where_in('products.Id', $productIds);  
        }
        
        $cond1 ="storeproducts.ProductId = products.Id";
        $this -> db -> where($cond1);
        
        $this -> db -> where(array(
            'storeproducts.RetailerId' => $retailer_id,
            'storeproducts.StoreId' => $store_id,
            'storeproducts.IsActive'=> 1
        ));
 
        //Keyword Search
        
        /*
        if (!empty($search)) {
          $this -> db -> like('products.ProductName', $search, 'both');
        }
        */
        
        $searchCond = "";
        if (!empty($search)) 
        {
            $searchCond = "( products.ProductDescription REGEXP '[[:<:]]".$search."[[:>:]]' = 1 OR products.ProductName REGEXP '[[:<:]]".$search."[[:>:]]' = 1 )";
            $this -> db -> where($searchCond);
        }


        $this -> db -> order_by('products.ProductName', 'ASC');
        $this -> db -> group_by('products.Id, specials.Id');

        
        
        if($category_id > 0 ){
           if($productCount > 0 )
           {
               
             $this -> db -> where('products.CategoryId', $category_id);
           }else{
               
            $this -> db -> where('products.ParentCategoryId', $category_id);
            $this -> db -> where('products.CategoryId', 0);
           }
        }
        
        
        if($brand)
        {
           $this -> db -> where('products.Brand', $brand); 
        }
        
        $this -> db -> where('storeproducts.Price > ',0);
        
        $query = $this -> db -> get();
        //echo $this->db->last_query();exit;
        return $query -> result_array();
    }
    
    public function get_products_by_category_and_brand_old($search = "", $user_id, $category_id=0, $brand="") {
        //Get user preference
        $user_preference = $this -> get_user_preference($user_id);
        $store_id = $user_preference['StoreId'];
        $retailer_id = $user_preference['RetailerId'];
        $productCount = 0;
        
        if($category_id > 0 ){
           # Function to check product count for the category  
           $productCount = $this->get_product_count($category_id);
        }
        
        $subquery1 ='( 
                SELECT count(productsreviews.ID) as reviews_count 
                FROM productsreviews 
                WHERE `productsreviews`.`ProductId` = `products`.`Id`
            ) as reviews_count';       
        $this->db->select($subquery1);
        
        $subquery2 ='( 
                SELECT AVG(productsreviews.rating) AS avg_rating
                FROM productsreviews 
                WHERE `productsreviews`.`ProductId` = `products`.`Id`
            ) as avg_rating';       
        $this->db->select($subquery2);
        
        
        $this -> db -> select('products.Id,
                           products.ProductName,
                           products.ProductImage,
                           products.ProductDescription,
                           products.RRP,
                           products.Brand,
                           products.SKU,
                           storeproducts.Price as store_price,
                           case when productspecials.PriceAppliedFrom is null or productspecials.PriceAppliedFrom = \'0000-00-00 00:00:00\' then \'\' else DATE_FORMAT(productspecials.PriceAppliedFrom,\'%d %b\') end as PriceAppliedFrom,
                           case when productspecials.PriceAppliedTo is null or productspecials.PriceAppliedTo = \'0000-00-00 00:00:00\' then \'\' else DATE_FORMAT(productspecials.PriceAppliedTo,\'%d %b\') end as PriceAppliedTo,
                           case when specials.IsStore is not null then specials.IsStore else 0 end as IsStore,
                           case when specials.Id is not null then specials.Id else 0 end as special_id,
                           case when products.CategoryId > 0  then concat(parent_category.CategoryName  , " ", sub_category.CategoryName ) else parent_category.CategoryName end as ProductCategoryName,
                           case when products.CategoryId > 0  then products.CategoryId else products.ParentCategoryId end as ProductCategoryId
                           ', FALSE);
        $this->db->from('products, storeproducts');
        $this -> db -> join('categories sub_category', 'sub_category.Id = products.CategoryId', 'left');
        $this -> db -> join('categories parent_category', 'parent_category.Id = products.ParentCategoryId', 'left');
        $this -> db -> join('categories main_parent_category', 'main_parent_category.Id = products.MainCategoryId', 'left');
        $join = 'left';
        
        $this -> db -> select('productspecials.SpecialQty, productspecials.SpecialPrice');
        $this -> db -> join('productspecials', 'productspecials.ProductId = products.Id AND DATE(productspecials.PriceAppliedFrom) <= "' . date('Y-m-d') . '" AND DATE(productspecials.PriceAppliedTo) >= "' . date('Y-m-d') . '" AND productspecials.RetailerId =' . $retailer_id . " AND productspecials.StoreId=" . $store_id . " AND productspecials.IsActive=1 AND productspecials.IsApproved =1 ", $join);
        $this -> db -> join('specials', 'specials.Id = productspecials.SpecialId and specials.IsActive = 1 and specials.IsRemoved = 0',$join);
        
        $this -> db -> where(array(
            'products.IsActive' => 1,
            'products.IsRemoved' => 0,
        ));
        
        $cond1 ="storeproducts.ProductId = products.Id";
        $this -> db -> where($cond1);
        
        $this -> db -> where(array(
            'storeproducts.RetailerId' => $retailer_id,
            'storeproducts.StoreId' => $store_id,
            'storeproducts.IsActive'=> 1
        ));
 
        $this -> db -> order_by('products.ProductName', 'ASC');
        $this -> db -> group_by('products.Id, specials.Id');

        //Keyword Search
        if (!empty($search)) {
            $this -> db -> like('products.ProductName', $search, 'both');
        }
        
        if($category_id > 0 ){
           //$categoryCond ="( products.CategoryId = $category_id or products.ParentCategoryId = $category_id )"; 
           //$this->db->where($categoryCond);        
           
           if($productCount > 0 )
           {
             $this -> db -> where('products.CategoryId', $category_id);
           }else{
            $this -> db -> where('products.ParentCategoryId', $category_id);
            $this -> db -> where('products.CategoryId', 0);
           }
        }
        
        if($brand)
        {
           $this -> db -> where('products.Brand', $brand); 
        }
        
        
        $query = $this -> db -> get();
        //echo $this->db->last_query();exit;
        return $query -> result_array();
    }
    
    
    /*
     *  Function to get the product count for the category
     */
    public function get_product_count($category_id) {
        $this -> db -> select('count(p.Id) as productCount');
        $this -> db -> from('products as p');
        
        $this -> db -> where(array(
            'p.IsActive' => 1,
            'p.IsRemoved' => 0,
            'p.CategoryId' => $category_id
        )); 
        
        
        $query = $this -> db -> get();
        //echo $this->db->last_query();exit;
        
        if ($query -> num_rows() > 0 ) {
            $result = $query -> row_array();
            return $result['productCount'];
        }
        else {
            return 0;
        }   
    }
    
    
    /*
     *  Function to get the stores where product is in any special 
     */
    
    public function chatbot_get_product_specials_stores($user_id, $product_id,$lat=0,$long=0) {
        # Get user preference
        $user_preference = $this -> get_user_preference_details($user_id);
        $store_id = $user_preference['StoreId'];
        $retailer_id = $user_preference['RetailerId'];
        
        if($lat != 0 && $long != 0)
        {
           $this -> db -> select('ROUND((6371 * acos( cos( radians(' . $lat . ') ) * cos( radians( s.Latitude ) ) * cos( radians( s.Longitude ) - radians(' . $long . ') ) + sin( radians(' . $lat . ') ) * sin( radians( s.Latitude ) ) ) ),2) AS distance', FALSE);
        }
        
        $this -> db -> select('ps.Id as productSpecialId, ps.ProductId, ps.RetailerId, ps.StoreTypeId, ps.StoreId, p.ProductName, r.CompanyName as RetailerName, s.StoreName,ps.SpecialQty, ps.SpecialPrice',FALSE);
        $subquery1 ='( 
                SELECT storeproducts.Price 
		 FROM storeproducts 
		 WHERE `storeproducts`.`ProductId` = p.`Id` AND storeproducts.RetailerId = ps.RetailerId
		 AND storeproducts.StoreId=ps.StoreId AND storeproducts.IsActive=1
                 limit 1
            ) as store_price';       
        $this->db->select($subquery1);
        
        $this -> db -> from('productspecials as ps');
        $this -> db -> join('products as p', 'p.Id = ps.ProductId');
        $this -> db -> join('retailers as r', 'r.Id = ps.RetailerId');
        $this -> db -> join('stores as s', 's.Id = ps.StoreId');
        $this -> db -> where(array(
            'ps.IsActive' => 1,
            'ps.IsApproved' => 1,
            'ps.ProductId' => $product_id
        ));
        
        $this -> db -> where('ps.PriceAppliedFrom <= ', date('Y-m-d'));
        $this -> db -> where('ps.PriceAppliedTo >= ', date('Y-m-d'));
        
        if( $store_id > 0 )
        {
            $this -> db -> where('ps.StoreId <> ', $store_id);
        }
        
        $this -> db -> order_by('s.StoreName', 'ASC');
        
        $query = $this -> db -> get();
        //echo $this->db->last_query();exit;
        return $query -> result_array();
    }
    
    /*
     *  Function to get the stores which having particular product
     */
    
    public function chatbot_get_product_stores($user_id, $product_id, $lat,$long,$prefDistance,$storeIds) {
        # Get user preference
        $user_preference = $this -> get_user_preference_details($user_id);
        $store_id = $user_preference['StoreId'];
        $retailer_id = $user_preference['RetailerId'];
        
        
        if($lat != 0 && $long != 0)
        {
           $this -> db -> select('ROUND((6371 * acos( cos( radians(' . $lat . ') ) * cos( radians( s.Latitude ) ) * cos( radians( s.Longitude ) - radians(' . $long . ') ) + sin( radians(' . $lat . ') ) * sin( radians( s.Latitude ) ) ) ),2) AS distance', FALSE);
        }
        $this -> db -> select('s.RetailerId, s.StoreTypeId, s.StoreId, r.CompanyName as RetailerName, s.StoreName',FALSE);
        $subquery1 ='( 
                SELECT storeproducts.Price 
		 FROM storeproducts 
		 WHERE `storeproducts`.`ProductId` = '.$product_id.' AND storeproducts.RetailerId = s.RetailerId
		 AND storeproducts.StoreId = s.Id AND storeproducts.IsActive=1
                 limit 1
            ) as store_price';       
        $this->db->select($subquery1);
        
       $subquery2 ='(SELECT CONCAT_WS("-", ps.Id,ps.SpecialQty, ps.SpecialPrice) FROM productspecials as ps WHERE ps.RetailerId = s.RetailerId  AND DATE(ps.PriceAppliedFrom) <= "'. date('Y-m-d').'" AND DATE(ps.PriceAppliedTo) >= "'. date('Y-m-d').'" AND (ps.StoreId= s.Id OR (ps.StoreId=0 AND ps.PriceForAllStores=1)) AND ps.IsActive=1 AND ps.IsApproved =1 AND ps.productId= '.$product_id.' limit 1 ) as Special_Qty_Price'; 
       $this->db->select($subquery2,FALSE);
        
        $this -> db -> from('stores as s');        
        $this -> db -> join('retailers as r', 'r.Id = s.RetailerId');        
        $this -> db -> where(array(
            's.IsActive' => 1,
            's.IsRemoved' => 0
        ));
        
        if( $store_id > 0 )
        {
            $this -> db -> where('s.Id <> ', $store_id);
        }
        
        if( $storeIds > 0 )
        {
            $this -> db -> where_in('s.Id', $storeIds);
        }
       
        $cond1 = 'ROUND((6371 * acos( cos( radians(' . $lat . ') ) * cos( radians( s.Latitude ) ) * cos( radians( s.Longitude ) - radians(' . $long . ') ) + sin( radians(' . $lat . ') ) * sin( radians( s.Latitude ) ) ) ),2) <= '.$prefDistance;
        $this -> db -> where($cond1);
        
        $this -> db -> order_by('s.StoreName', 'ASC');
        //$this -> db -> order_by('distance', 'ASC');
        
        $query = $this -> db -> get();
        //echo $this->db->last_query();exit;
        return $query -> result_array();
    }
    
     /*
     *  Function to get the categories with products counts
     */
    public function chatbot_get_categories_with_products($search = "", $user_id, $special_only="") {
        
        # Get productIds         
        $productIds = array();       
        $this -> db -> select('products.Id', FALSE);
        $this->db->from('products');
	$this -> db -> where(array(
		'products.IsActive' => 1,
		'products.IsRemoved' => 0
	));	
	$categorySearchCond = "";
	if (!empty($search)) 
	{
	  $categorySearchCond = "( products.ProductDescription REGEXP '[[:<:]]".$search."[[:>:]]' = 1 OR products.ProductName REGEXP '[[:<:]]".$search."[[:>:]]' = 1 )";
	  $this -> db -> where($categorySearchCond);
	}		
	$productIdsQuery = $this -> db -> get();        
        $results = $productIdsQuery -> result_array();
        
        foreach($results as $result)
        {
          $productIds[] =  $result['Id']; 
        }
        
        //Get user preference
        $user_preference = $this -> get_user_preference($user_id);
        $store_id = $user_preference['StoreId'];
        $retailer_id = $user_preference['RetailerId'];
        
        $this -> db -> select('products.Id,
                               products.Brand,
                               case when products.CategoryId > 0  then concat(parent_category.CategoryName  , " > ", sub_category.CategoryName ) else parent_category.CategoryName end as ProductCategoryName,
                               case when products.CategoryId > 0  then products.CategoryId else products.ParentCategoryId end as ProductCategoryId
                               ', FALSE);
        $this->db->from('products, storeproducts');
        $this -> db -> join('categories sub_category', 'sub_category.Id = products.CategoryId', 'left');
        $this -> db -> join('categories parent_category', 'parent_category.Id = products.ParentCategoryId', 'left');
        $this -> db -> join('categories main_parent_category', 'main_parent_category.Id = products.MainCategoryId', 'left');
       
        if($special_only == 'No')
        {
            $join = 'left'; 
        }

        $this -> db -> join('productspecials', 'productspecials.ProductId = products.Id AND DATE(productspecials.PriceAppliedFrom) <= "' . date('Y-m-d') . '" AND DATE(productspecials.PriceAppliedTo) >= "' . date('Y-m-d') . '" AND productspecials.RetailerId =' . $retailer_id . " AND productspecials.StoreId=" . $store_id . " AND productspecials.IsActive=1 AND productspecials.IsApproved =1 ", $join);
        $this -> db -> join('specials', 'specials.Id = productspecials.SpecialId and specials.IsActive = 1 and specials.IsRemoved = 0',$join);
        
        if($productIds)
        {
            $this -> db -> where_in('products.Id',$productIds);
        }
        
        $this -> db -> where('storeproducts.Price > ',0);
        $this -> db -> where(array(
            'products.IsActive' => 1,
            'products.IsRemoved' => 0
        ));
        
        $cond1 ="storeproducts.ProductId = products.Id";
        $this -> db -> where($cond1);
        
        
        $searchCond = "";
        if (!empty($search)) 
        {
          $searchCond = "( products.ProductDescription REGEXP '[[:<:]]".$search."[[:>:]]' = 1 OR products.ProductName REGEXP '[[:<:]]".$search."[[:>:]]' = 1 )";
          $this -> db -> where($searchCond);
        }
        
        
        
        $this -> db -> where(array(
            'storeproducts.RetailerId' => $retailer_id,
            'storeproducts.StoreId' => $store_id,
            'storeproducts.IsActive'=> 1
        ));
        
        /*
        //Keyword Search
        if (!empty($search)) {
            $this -> db -> like('products.ProductName', $search, 'both');
        }
        */
        
        $this -> db -> order_by('ProductCategoryName', 'ASC');
        $this -> db -> order_by('products.Brand', 'ASC');
        
        $this -> db -> group_by('products.Id, specials.Id');

        
        $query = $this -> db -> get();
        //echo $this->db->last_query();exit;
        return $query -> result_array();
    }
    
    
    
    public function get_shopping_list_search_products($category_type, $category_id, $price_range = array(), $last_product_id = 0, $show_offer = 0, $product_ids = array(), $search = "", $user_id, $start = 0) {
        //Get user preference
        $user_preference = $this -> get_user_preference($user_id);
        $store_id = $user_preference['StoreId'];
        $retailer_id = $user_preference['RetailerId'];

        $subquery1 ='( 
                SELECT count(productsreviews.ID) as reviews_count 
                FROM productsreviews 
                WHERE `productsreviews`.`ProductId` = `products`.`Id`
            ) as reviews_count';       
        $this->db->select($subquery1);
        
        $subquery2 ='( 
                SELECT AVG(productsreviews.rating) AS avg_rating
                FROM productsreviews 
                WHERE `productsreviews`.`ProductId` = `products`.`Id`
            ) as avg_rating';       
        $this->db->select($subquery2);
        
        
        
        $this -> db -> select('products.Id,
                           products.ProductName,
                           products.ProductImage,
                           products.ProductDescription,
                           products.RRP,
                           products.Brand,
                           products.SKU,
                           storeproducts.Price as store_price,
                           case when productspecials.PriceAppliedFrom is null or productspecials.PriceAppliedFrom = \'0000-00-00 00:00:00\' then \'\' else DATE_FORMAT(productspecials.PriceAppliedFrom,\'%d %b\') end as PriceAppliedFrom,
                           case when productspecials.PriceAppliedTo is null or productspecials.PriceAppliedTo = \'0000-00-00 00:00:00\' then \'\' else DATE_FORMAT(productspecials.PriceAppliedTo,\'%d %b\') end as PriceAppliedTo,
                           case when specials.IsStore is not null then specials.IsStore else 0 end as IsStore,
                           case when specials.Id is not null then specials.Id else 0 end as special_id', FALSE);
        //$this -> db -> from('products','storeproducts');  
        $this->db->from('products, storeproducts');
        //$this -> db -> join('usersfavorite', 'usersfavorite.ProductId = products.Id AND usersfavorite.UserId =' . $user_id, 'left');        
        $this -> db -> join('categories sub_category', 'sub_category.Id = products.CategoryId', 'left');
        $this -> db -> join('categories parent_category', 'parent_category.Id = products.ParentCategoryId', 'left');
        $this -> db -> join('categories main_parent_category', 'main_parent_category.Id = products.MainCategoryId', 'left');
        

        $join = 'left';
        if ($show_offer == 1) {
            $join = "";
        }

        $this -> db -> select('productspecials.SpecialQty, productspecials.SpecialPrice');
        $this -> db -> join('productspecials', 'productspecials.ProductId = products.Id AND DATE(productspecials.PriceAppliedFrom) <= "' . date('Y-m-d') . '" AND DATE(productspecials.PriceAppliedTo) >= "' . date('Y-m-d') . '" AND productspecials.RetailerId =' . $retailer_id . " AND productspecials.StoreId=" . $store_id . " AND productspecials.IsActive=1 AND productspecials.IsApproved =1 ", $join);
        $this -> db -> join('specials', 'specials.Id = productspecials.SpecialId and specials.IsActive = 1 and specials.IsRemoved = 0',$join);
        

        $this -> db -> where(array(
            'products.IsActive' => 1,
            'products.IsRemoved' => 0,
        ));
        
        $cond1 ="storeproducts.ProductId = products.Id";
        $this -> db -> where($cond1);
        
        $this -> db -> where(array(
            'storeproducts.RetailerId' => $retailer_id,
            'storeproducts.StoreId' => $store_id,
            'storeproducts.IsActive'=> 1
        ));

        //$this -> db -> order_by('products.Id', 'DESC');
        $this -> db -> order_by('products.ProductName', 'ASC');
        //$this -> db -> order_by('productspecials.Id', 'DESC');
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
            
            //$this -> db -> limit($this -> config -> item('top_offer_product_limit'), $start);
        }

        $query = $this -> db -> get();
        //echo $this->db->last_query();exit;
        return $query -> result_array();
    }
    
    
    /*
     *  Function to get all stores with distance 
     */
    
    public function get_all_stores($lat=0,$long=0,$prefDistance=0) {
        
        /*
        if($lat != 0 && $long != 0)
        {
           $this -> db -> select('ROUND((6371 * acos( cos( radians(' . $lat . ') ) * cos( radians( s.Latitude ) ) * cos( radians( s.Longitude ) - radians(' . $long . ') ) + sin( radians(' . $lat . ') ) * sin( radians( s.Latitude ) ) ) ),2) AS distance', FALSE);
        }
        */
        
        //$this -> db -> select('s.Id, s.StoreName',FALSE);
        $this -> db -> select('s.Id',FALSE);
        
        $this -> db -> from('stores as s');                
        $this -> db -> where(array(
            's.IsActive' => 1,
            's.IsRemoved' => 0
        ));
        
        if( $store_id > 0 )
        {
            $this -> db -> where('s.Id <> ', $store_id);
        }
        
        $cond1 = 'ROUND((6371 * acos( cos( radians(' . $lat . ') ) * cos( radians( s.Latitude ) ) * cos( radians( s.Longitude ) - radians(' . $long . ') ) + sin( radians(' . $lat . ') ) * sin( radians( s.Latitude ) ) ) ),2) <= '.$prefDistance;
        $this -> db -> where($cond1);
        
        $this -> db -> order_by('s.StoreName', 'ASC');
        
        $query = $this -> db -> get();
        //echo $this->db->last_query();exit;
        return $query -> result_array();
    }
    
    /*
     *  Function to get all stores with distance 
     */
    
    public function get_product_details($product_id) {
        $this -> db -> select('p.Id,case when p.HouseId is null then p.ProductDescription else concat(retailers.CompanyName," ",p.ProductName) end as ProductName',FALSE);
        $this -> db -> from('products as p');
        $this -> db -> join('retailers', 'retailers.Id = p.HouseId and retailers.IsActive = 1 and retailers.IsRemoved = 0', 'left');
        $this -> db -> where(array(
            'p.IsActive' => 1,
            'p.IsRemoved' => 0
        ));
        
        if( $product_id > 0 )
        {
            $this -> db -> where('p.Id', $product_id);
        }
        
        $query = $this -> db -> get();
        //echo $this->db->last_query();exit;
        return $query -> row_array();
    }
    
}