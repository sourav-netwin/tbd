<?php 

/*
 * Author: Name:PHN
 * Purpose: Model for controlling database interactions regarding the content.
 * Date:04-09-2015
 * Dependency: None
 */

class productmodel extends CI_Model {
    /*
     * Purpose: Constructor.
     * Date: 04-09-2015
     * Input Parameter: None
     * Output Parameter: None
     */

    public $latitude;
    public $longitude;
    public $store_id;
    public $page_no;
    public $page_limit;

    public function __construct() {

        parent::__construct();
    }

    public function get_products($category_id, $retailer_id, $user_id, $product_ids = array(), $search_array = array(), $get_total = 0, $limit_start = 0, $brands = [], $store_id = '', $storetype_id = '') {
        $start_from = 0;

        //Get the nearest store
        if ($store_id == '') {
            $store_id = $this -> get_nearest_or_prefered_store($retailer_id);
        }

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
                           productspecials.SpecialQty,
                           productspecials.SpecialPrice,
                           case when productspecials.PriceAppliedFrom is null or productspecials.PriceAppliedFrom = \'0000-00-00 00:00:00\' then \'\' else DATE_FORMAT(productspecials.PriceAppliedFrom,\'%d %b\') end as PriceAppliedFrom,
                               case when productspecials.PriceAppliedTo is null or productspecials.PriceAppliedTo = \'0000-00-00 00:00:00\' then \'\' else DATE_FORMAT(productspecials.PriceAppliedTo,\'%d %b\') end as PriceAppliedTo,
                           case when specials.IsStore is not null then specials.IsStore else 0 end as IsStore,
                           case when specials.Id is not null then specials.Id else 0 end as special_id', FALSE);

        $this -> db -> join('productsreviews', 'productsreviews.ProductId = products.Id', 'left');
        $this -> db -> join('usersfavorite', 'usersfavorite.ProductId = products.Id AND usersfavorite.UserId =' . $user_id, 'left');
        $this -> db -> join('storeproducts', "storeproducts.ProductId = products.Id AND storeproducts.RetailerId = $retailer_id  AND (storeproducts.StoreId= $store_id  OR (storeproducts.StoreId=0 AND storeproducts.PriceForAllStores=1)) AND storeproducts.IsActive=1");
        $this -> db -> join('productspecials', 'productspecials.ProductId = products.Id AND DATE(productspecials.PriceAppliedFrom) <= "' . date('Y-m-d') . '" AND DATE(productspecials.PriceAppliedTo) >= "' . date('Y-m-d') . '" AND productspecials.RetailerId =' . $retailer_id . " AND (productspecials.StoreId=" . $store_id . " OR (productspecials.StoreId=0 AND productspecials.PriceForAllStores=1)) AND productspecials.IsActive=1 AND productspecials.IsApproved =1 ", 'left');
        $this -> db -> join('specials', 'specials.Id = productspecials.SpecialId and specials.IsActive = 1 and specials.IsRemoved = 0','left');

        $this -> db -> where(array(
            'products.IsActive' => 1,
            'products.IsRemoved' => 0,
        ));

        $this -> db -> order_by('productspecials.Id', 'DESC');
        $this -> db -> group_by('products.Id, specials.Id');

        if ($category_id) {
            $this -> db -> where("(products.CategoryId =" . $category_id . " or products.ParentCategoryId =" . $category_id . ")");
        }

        if ($product_ids) {
            $this -> db -> where_in('products.Id', $product_ids);
        }

        if (!empty($search_array)) {

            //Keyword Search
            if (!empty($search_array['keyword'])) {
                $this -> db -> like('products.ProductName', $search_array['keyword'], 'both');
            }
            $filter_query = "";
            //Price Range Filter
            if (!empty($search_array['price_range'])) {
                foreach ($search_array['price_range'] as $range) {

                    if (!empty($filter_query)) {
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
            }

            if (!empty($filter_query)) {
                $this -> db -> where("(" . $filter_query . ")");
            }
        }

        if (is_array($brands) && !empty($brands)) {
            //$this -> db -> where('products.Brand',$brand);
            $cnt = 1;
            $brand_filter = '(';
            foreach ($brands as $brand) {
                if ($cnt == 1) {
                    $brand_filter .= 'products.Brand = \'' . $brand['name'] . '\' ';
                }
                else {
                    $brand_filter .= ' OR products.Brand = \'' . $brand['name'] . '\' ';
                }
                $cnt++;
            }
            $brand_filter .= ')';
            $this -> db -> where($brand_filter);
        }

        if (!$get_total) { //Pagination
            //Get the limit & offset
            if ($this -> page_no != '' && $this -> page_limit != '') {

                $start_from = ($this -> page_no - 1) * $this -> page_limit;
            }
            //$this -> db -> limit($this -> page_limit, $start_from);


            $this -> db -> limit(API_PAGE_LIMIT, $limit_start);
            $query = $this -> db -> get('products');

            //echo $this->db->last_query();exit;

            return $query -> result_array();
        }
        else { //Get Number of products
            $query = $this -> db -> get('products');

            return $query -> num_rows();
        }
    }
    
    
    public function get_products_WIP($category_id, $retailer_id, $user_id, $product_ids = array(), $search_array = array(), $get_total = 0, $limit_start = 0, $brands = [], $store_id = '', $storetype_id = '') {
        $start_from = 0;

        //Get the nearest store
        if ($store_id == '') {
            $store_id = $this -> get_nearest_or_prefered_store($retailer_id);
        }

        //$this->db->select('SQL_CALC_FOUND_ROWS *', false);
        $this->db->select('SQL_CALC_FOUND_ROWS (products.Id) as pid', FALSE);
        
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
                           storeproducts.Price AS store_price,
                           productspecials.SpecialQty,
                           productspecials.SpecialPrice,
                           case when productspecials.PriceAppliedFrom is null or productspecials.PriceAppliedFrom = \'0000-00-00 00:00:00\' then \'\' else DATE_FORMAT(productspecials.PriceAppliedFrom,\'%d %b\') end as PriceAppliedFrom,
                               case when productspecials.PriceAppliedTo is null or productspecials.PriceAppliedTo = \'0000-00-00 00:00:00\' then \'\' else DATE_FORMAT(productspecials.PriceAppliedTo,\'%d %b\') end as PriceAppliedTo,
                           case when specials.IsStore is not null then specials.IsStore else 0 end as IsStore,
                           case when specials.Id is not null then specials.Id else 0 end as special_id', FALSE);

        
        
        //$this -> db -> join('productsreviews', 'productsreviews.ProductId = products.Id', 'left');
        //$this -> db -> join('usersfavorite', 'usersfavorite.ProductId = products.Id AND usersfavorite.UserId =' . $user_id, 'left');
        $this -> db -> join('storeproducts', "storeproducts.ProductId = products.Id AND storeproducts.RetailerId = $retailer_id  AND (storeproducts.StoreId= $store_id  OR (storeproducts.StoreId=0 AND storeproducts.PriceForAllStores=1)) AND storeproducts.IsActive=1");
        $this -> db -> join('productspecials', 'productspecials.ProductId = products.Id AND DATE(productspecials.PriceAppliedFrom) <= "' . date('Y-m-d') . '" AND DATE(productspecials.PriceAppliedTo) >= "' . date('Y-m-d') . '" AND productspecials.RetailerId =' . $retailer_id . " AND (productspecials.StoreId=" . $store_id . " OR (productspecials.StoreId=0 AND productspecials.PriceForAllStores=1)) AND productspecials.IsActive=1 AND productspecials.IsApproved =1 ", 'left');
        $this -> db -> join('specials', 'specials.Id = productspecials.SpecialId and specials.IsActive = 1 and specials.IsRemoved = 0','left');

        $this -> db -> where(array(
            'products.IsActive' => 1,
            'products.IsRemoved' => 0,
        ));

        $this -> db -> order_by('productspecials.Id', 'DESC');
        $this -> db -> group_by('products.Id, specials.Id');

        if ($category_id) {
            $this -> db -> where("(products.CategoryId =" . $category_id . " or products.ParentCategoryId =" . $category_id . ")");
        }

        if ($product_ids) {
            $this -> db -> where_in('products.Id', $product_ids);
        }

        if (!empty($search_array)) {

            //Keyword Search
            if (!empty($search_array['keyword'])) {
                $this -> db -> like('products.ProductName', $search_array['keyword'], 'both');
            }
            $filter_query = "";
            //Price Range Filter
            if (!empty($search_array['price_range'])) {
                foreach ($search_array['price_range'] as $range) {

                    if (!empty($filter_query)) {
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
            }

            if (!empty($filter_query)) {
                $this -> db -> where("(" . $filter_query . ")");
            }
        }

        if (is_array($brands) && !empty($brands)) {
            //$this -> db -> where('products.Brand',$brand);
            $cnt = 1;
            $brand_filter = '(';
            foreach ($brands as $brand) {
                if ($cnt == 1) {
                    $brand_filter .= 'products.Brand = \'' . $brand['name'] . '\' ';
                }
                else {
                    $brand_filter .= ' OR products.Brand = \'' . $brand['name'] . '\' ';
                }
                $cnt++;
            }
            $brand_filter .= ')';
            $this -> db -> where($brand_filter);
        }

        if (!$get_total) { //Pagination
            //Get the limit & offset
            if ($this -> page_no != '' && $this -> page_limit != '') {

                $start_from = ($this -> page_no - 1) * $this -> page_limit;
            }
            //$this -> db -> limit($this -> page_limit, $start_from);


            $this -> db -> limit(API_PAGE_LIMIT, $limit_start);
            $query = $this -> db -> get('products');

            //echo $this->db->last_query();exit;

            return $query -> result_array();
        }
        else { //Get Number of products
            $query = $this -> db -> get('products');

            return $query -> num_rows();
        }
    }
    
    public function get_hot_deals($category_id, $retailer_id, $user_id,$get_total = 0, $limit_start = 0) {
        # Get the nearest store
        $store_id = $this -> get_nearest_or_prefered_store($retailer_id);
        
        # Generate Query 
        $this->db->select('SQL_CALC_FOUND_ROWS (products.Id) as pid', FALSE);
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
        
        $this -> db -> select('sum(case when userwishlistproducts.UserWishlistId is not null then 1 else 0 end) as wish_lists,
                           products.Id,                           
                           case when products.HouseId is null then \'0\' else products.HouseId end as HouseId,
                           products.ProductName,
                           products.ProductImage,
                           products.ProductDescription,
                           products.RRP,
                           products.Brand,
                           products.SKU,
                           storeproducts.Price AS store_price,
                           productspecials.SpecialQty,
                           productspecials.SpecialPrice,
                           case when userbasket.Id is null then \'\' else userbasket.Id end as BasketId,
                           case when userspricealerts.Id is null then \'0\' else \'1\' end as price_alert,
                           case when productspecials.PriceAppliedFrom is null or productspecials.PriceAppliedFrom = \'0000-00-00 00:00:00\' then \'\' else DATE_FORMAT(productspecials.PriceAppliedFrom,\'%d %b\') end as PriceAppliedFrom,
                           case when productspecials.PriceAppliedTo is null or productspecials.PriceAppliedTo = \'0000-00-00 00:00:00\' then \'\' else DATE_FORMAT(productspecials.PriceAppliedTo,\'%d %b\') end as PriceAppliedTo,
                           specials.IsStore,
                           specials.Id as special_id', FALSE);

        $this -> db -> join('userspricealerts', 'userspricealerts.ProductId = products.Id AND userspricealerts.UserId =' . $user_id, 'left');
        $this -> db -> join('storeproducts', 'storeproducts.ProductId = products.Id AND storeproducts.RetailerId =' . $retailer_id . " AND (storeproducts.StoreId=" . $store_id . " OR (storeproducts.StoreId=0 AND storeproducts.PriceForAllStores=1)) AND storeproducts.IsActive=1");
        $this -> db -> join('productspecials', 'productspecials.ProductId = products.Id AND DATE(productspecials.PriceAppliedFrom) <= "' . date('Y-m-d') . '" AND DATE(productspecials.PriceAppliedTo) >= "' . date('Y-m-d') . '" AND productspecials.RetailerId =' . $retailer_id . " AND (productspecials.StoreId=" . $store_id . " OR (productspecials.StoreId=0 AND productspecials.PriceForAllStores=1)) AND productspecials.IsActive=1 AND productspecials.IsApproved =1 ");
        $this -> db -> join('specials', 'specials.Id = productspecials.SpecialId and specials.IsActive = 1 and specials.IsRemoved = 0');
        $this -> db -> join('userwishlistproducts', "userwishlistproducts.ProductId = products.Id and userwishlistproducts.UserId = ".$user_id." AND userwishlistproducts.SpecialId = specials.Id", 'left');
        $this -> db -> join('userwishlists', 'userwishlists.Id = userwishlistproducts.UserWishlistId and userwishlists.IsActive = 1 and userwishlists.IsRemoved = 0', 'left');
        //$this -> db -> join('usersfavorite', 'usersfavorite.ProductId = products.Id AND usersfavorite.SpecialId = specials.Id AND usersfavorite.UserId =' . $user_id, 'left');
        $this -> db -> join('userbasket', 'userbasket.ProductId = products.Id AND userbasket.SpecialId = specials.Id AND userbasket.UserId =' . $user_id, 'left');
        $this -> db -> where(array(
            'products.IsActive' => 1,
            'products.IsRemoved' => 0,
        ));

        $this -> db -> order_by('productspecials.Id', 'DESC');
        $this -> db -> group_by('products.Id, specials.Id');

        if ($category_id) {
            $this -> db -> where("products.CategoryId =$category_id or products.ParentCategoryId =$category_id");
        }
        
        # Add Pagination 
        if (!$get_total) {
            $this -> db -> limit(API_PAGE_LIMIT, $limit_start);
            $query = $this -> db -> get('products');
            //echo $this->db->last_query();exit;
            return $query -> result_array();
        }else { 
          //Get Number of products
          $query = $this -> db -> get('products');
          return $query -> num_rows();
        }
    }
    
    public function get_hot_deals_WORKING($category_id, $retailer_id, $user_id) {

        //Get the nearest store
        $store_id = $this -> get_nearest_or_prefered_store($retailer_id);
        $this->db->_protect_identifiers=false;
        $this -> db -> select('sum(case when userwishlistproducts.UserWishlistId is not null then 1 else 0 end) as wish_lists,
                           products.Id,
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
                           productspecials.SpecialQty,
                           productspecials.SpecialPrice,
                           case when userbasket.Id is null then \'\' else userbasket.Id end as BasketId,
                           case when userspricealerts.Id is null then \'0\' else \'1\' end as price_alert,
                           case when productspecials.PriceAppliedFrom is null or productspecials.PriceAppliedFrom = \'0000-00-00 00:00:00\' then \'\' else DATE_FORMAT(productspecials.PriceAppliedFrom,\'%d %b\') end as PriceAppliedFrom,
                           case when productspecials.PriceAppliedTo is null or productspecials.PriceAppliedTo = \'0000-00-00 00:00:00\' then \'\' else DATE_FORMAT(productspecials.PriceAppliedTo,\'%d %b\') end as PriceAppliedTo,
                           specials.IsStore,
                           specials.Id as special_id', false);

        $this -> db -> join('productsreviews', 'productsreviews.ProductId = products.Id', 'left');
        
        //$this -> db -> join('userbasket', 'userbasket.ProductId = products.Id AND userbasket.UserId =' . $user_id, 'left');
        $this -> db -> join('userspricealerts', 'userspricealerts.ProductId = products.Id AND userspricealerts.UserId =' . $user_id, 'left');
        $this -> db -> join('storeproducts', 'storeproducts.ProductId = products.Id AND storeproducts.RetailerId =' . $retailer_id . " AND (storeproducts.StoreId=" . $store_id . " OR (storeproducts.StoreId=0 AND storeproducts.PriceForAllStores=1)) AND storeproducts.IsActive=1");
        $this -> db -> join('productspecials', 'productspecials.ProductId = products.Id AND DATE(productspecials.PriceAppliedFrom) <= "' . date('Y-m-d') . '" AND DATE(productspecials.PriceAppliedTo) >= "' . date('Y-m-d') . '" AND productspecials.RetailerId =' . $retailer_id . " AND (productspecials.StoreId=" . $store_id . " OR (productspecials.StoreId=0 AND productspecials.PriceForAllStores=1)) AND productspecials.IsActive=1 AND productspecials.IsApproved =1 ");
        $this -> db -> join('specials', 'specials.Id = productspecials.SpecialId and specials.IsActive = 1 and specials.IsRemoved = 0');
        $this -> db -> join('userwishlistproducts', "userwishlistproducts.UserId = $user_id and userwishlistproducts.ProductId = products.Id AND userwishlistproducts.SpecialId = specials.Id", 'left');
        $this -> db -> join('userwishlists', 'userwishlists.Id = userwishlistproducts.UserWishlistId and userwishlists.IsActive = 1 and userwishlists.IsRemoved = 0', 'left');
        $this -> db -> join('usersfavorite', 'usersfavorite.ProductId = products.Id AND usersfavorite.SpecialId = specials.Id AND usersfavorite.UserId =' . $user_id, 'left');
        $this -> db -> join('userbasket', 'userbasket.ProductId = products.Id AND userbasket.SpecialId = specials.Id AND userbasket.UserId =' . $user_id, 'left');
        $this -> db -> where(array(
            'products.IsActive' => 1,
            'products.IsRemoved' => 0,
        ));

        $this -> db -> order_by('productspecials.Id', 'DESC');
        $this -> db -> group_by('products.Id, specials.Id');

        if ($category_id) {
            $this -> db -> where("products.CategoryId =$category_id or products.ParentCategoryId =$category_id");
        }

        $query = $this -> db -> get('products');
        //echo $this->db->last_query();die;

        return $query -> result_array();
    }
    
    public function get_hot_deals_WIP($category_id, $retailer_id, $user_id,$get_total = 0, $limit_start = 0) {
         
        //Get the nearest store
        $store_id = $this -> get_nearest_or_prefered_store($retailer_id);
        
        
        $this->db->select('SQL_CALC_FOUND_ROWS (products.Id) as pid', FALSE);
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
        
        $this -> db -> select('sum(case when userwishlistproducts.UserWishlistId is not null then 1 else 0 end) as wish_lists,
                           products.Id,
                           products.ProductName,
                           products.ProductImage,
                           products.ProductDescription,
                           products.RRP,
                           products.Brand,
                           products.SKU,                           
                           case when usersfavorite.SpecialId = specials.Id then "1" else "0" end as is_favorite,
                           storeproducts.Price AS store_price,
                           productspecials.SpecialQty,
                           productspecials.SpecialPrice,
                           case when userbasket.Id is null then \'\' else userbasket.Id end as BasketId,
                           case when userspricealerts.Id is null then \'0\' else \'1\' end as price_alert,
                           case when productspecials.PriceAppliedFrom is null or productspecials.PriceAppliedFrom = \'0000-00-00 00:00:00\' then \'\' else DATE_FORMAT(productspecials.PriceAppliedFrom,\'%d %b\') end as PriceAppliedFrom,
                           case when productspecials.PriceAppliedTo is null or productspecials.PriceAppliedTo = \'0000-00-00 00:00:00\' then \'\' else DATE_FORMAT(productspecials.PriceAppliedTo,\'%d %b\') end as PriceAppliedTo,
                           specials.IsStore,
                           specials.Id as special_id', FALSE);

        //$this -> db -> join('userbasket', 'userbasket.ProductId = products.Id AND userbasket.UserId =' . $user_id, 'left');
        $this -> db -> join('userspricealerts', 'userspricealerts.ProductId = products.Id AND userspricealerts.UserId =' . $user_id, 'left');
        $this -> db -> join('storeproducts', 'storeproducts.ProductId = products.Id AND storeproducts.RetailerId =' . $retailer_id . " AND (storeproducts.StoreId=" . $store_id . " OR (storeproducts.StoreId=0 AND storeproducts.PriceForAllStores=1)) AND storeproducts.IsActive=1");
        $this -> db -> join('productspecials', 'productspecials.ProductId = products.Id AND DATE(productspecials.PriceAppliedFrom) <= "' . date('Y-m-d') . '" AND DATE(productspecials.PriceAppliedTo) >= "' . date('Y-m-d') . '" AND productspecials.RetailerId =' . $retailer_id . " AND (productspecials.StoreId=" . $store_id . " OR (productspecials.StoreId=0 AND productspecials.PriceForAllStores=1)) AND productspecials.IsActive=1 AND productspecials.IsApproved =1 ");
        $this -> db -> join('specials', 'specials.Id = productspecials.SpecialId and specials.IsActive = 1 and specials.IsRemoved = 0');
        //$this -> db -> join('userwishlistproducts', "userwishlistproducts.UserId = ".$user_id." and userwishlistproducts.ProductId = products.Id AND userwishlistproducts.SpecialId = specials.Id", 'left');
        $this -> db -> join('userwishlistproducts', "userwishlistproducts.ProductId = products.Id and userwishlistproducts.UserId = ".$user_id." AND userwishlistproducts.SpecialId = specials.Id", 'left');
        $this -> db -> join('userwishlists', 'userwishlists.Id = userwishlistproducts.UserWishlistId and userwishlists.IsActive = 1 and userwishlists.IsRemoved = 0', 'left');
        $this -> db -> join('usersfavorite', 'usersfavorite.ProductId = products.Id AND usersfavorite.SpecialId = specials.Id AND usersfavorite.UserId =' . $user_id, 'left');
        $this -> db -> join('userbasket', 'userbasket.ProductId = products.Id AND userbasket.SpecialId = specials.Id AND userbasket.UserId =' . $user_id, 'left');
        $this -> db -> where(array(
            'products.IsActive' => 1,
            'products.IsRemoved' => 0,
        ));

        $this -> db -> order_by('productspecials.Id', 'DESC');
        $this -> db -> group_by('products.Id, specials.Id');

        if ($category_id) {
            $this -> db -> where("products.CategoryId =$category_id or products.ParentCategoryId =$category_id");
        }
        
        //$query = $this -> db -> get('products');
        if (!$get_total) { //Pagination
            
            //Get the limit & offset
            if ($this -> page_no != '' && $this -> page_limit != '') {

                $start_from = ($this -> page_no - 1) * $this -> page_limit;
            }
            //$this -> db -> limit($this -> page_limit, $start_from);


            $this -> db -> limit(API_PAGE_LIMIT, $limit_start);
            $query = $this -> db -> get('products');

            //echo $this->db->last_query();exit;

            return $query -> result_array();
        }
        else { //Get Number of products
           
             
            $query = $this -> db -> get('products');

            return $query -> num_rows();
        }
        
        //echo $this->db->last_query();die;

        return $query -> result_array();
    }
    

    /*
    public function get_hot_deals_old($category_id, $retailer_id, $user_id) {

        //Get the nearest store
        $store_id = $this -> get_nearest_or_prefered_store($retailer_id);
        $this->db->_protect_identifiers=false;
        $this -> db -> select('sum(case when userwishlistproducts.UserWishlistId is not null then 1 else 0 end) as wish_lists,
                           products.Id,
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
                           case when userbasket.Id is null then \'\' else userbasket.Id end as BasketId,
                           case when userspricealerts.Id is null then \'0\' else \'1\' end as price_alert,
                           case when productspecials.PriceAppliedFrom is null or productspecials.PriceAppliedFrom = \'0000-00-00 00:00:00\' then \'\' else DATE_FORMAT(productspecials.PriceAppliedFrom,\'%d%b\') end as PriceAppliedFrom,
                           case when productspecials.PriceAppliedTo is null or productspecials.PriceAppliedTo = \'0000-00-00 00:00:00\' then \'\' else DATE_FORMAT(productspecials.PriceAppliedTo,\'%d%b\') end as PriceAppliedTo,
                           specials.IsStore,
                           specials.Id as special_id', false);

        $this -> db -> join('productsreviews', 'productsreviews.ProductId = products.Id', 'left');
        $this -> db -> join('usersfavorite', 'usersfavorite.ProductId = products.Id AND usersfavorite.UserId =' . $user_id, 'left');
        $this -> db -> join('userbasket', 'userbasket.ProductId = products.Id AND userbasket.UserId =' . $user_id, 'left');
        $this -> db -> join('userspricealerts', 'userspricealerts.ProductId = products.Id AND userspricealerts.UserId =' . $user_id, 'left');
        $this -> db -> join('storeproducts', 'storeproducts.ProductId = products.Id AND storeproducts.RetailerId =' . $retailer_id . " AND (storeproducts.StoreId=" . $store_id . " OR (storeproducts.StoreId=0 AND storeproducts.PriceForAllStores=1)) AND storeproducts.IsActive=1");
        $this -> db -> join('productspecials', 'productspecials.ProductId = products.Id AND DATE(productspecials.PriceAppliedFrom) <= "' . date('Y-m-d') . '" AND DATE(productspecials.PriceAppliedTo) >= "' . date('Y-m-d') . '" AND productspecials.RetailerId =' . $retailer_id . " AND (productspecials.StoreId=" . $store_id . " OR (productspecials.StoreId=0 AND productspecials.PriceForAllStores=1)) AND productspecials.IsActive=1 AND productspecials.IsApproved =1 ");
        $this -> db -> join('specials', 'specials.Id = productspecials.SpecialId and specials.IsActive = 1 and specials.IsRemoved = 0');
        $this -> db -> join('userwishlistproducts', "userwishlistproducts.UserId = $user_id and userwishlistproducts.ProductId = products.Id", 'left');
        $this -> db -> join('userwishlists', 'userwishlists.Id = userwishlistproducts.UserWishlistId and userwishlists.IsActive = 1 and userwishlists.IsRemoved = 0', 'left');

        $this -> db -> where(array(
            'products.IsActive' => 1,
            'products.IsRemoved' => 0,
        ));

        $this -> db -> order_by('productspecials.Id', 'DESC');
        $this -> db -> group_by('products.Id, specials.Id');

        if ($category_id) {
            $this -> db -> where("products.CategoryId =$category_id or products.ParentCategoryId =$category_id");
        }

        $query = $this -> db -> get('products');
        //echo $this->db->last_query();die;

        return $query -> result_array();
    }
    */
    
    public function product_details($product_id, $retailer_id, $user_id, $special_id) {

        // case when products.HouseId is not null then concat(retailers.CompanyName,\' \',products.ProductName) else products.ProductDescription end as ProductName,
        $store_id = $this -> get_nearest_or_prefered_store($retailer_id);
        $special_appent = '';
        $special_join = 'left';
        if((int)$special_id > 0 ){
            $special_appent = ' and specials.Id = '.$special_id;
            $special_join = '';
        }
        $store_id = $store_id ? $store_id : 0;
        $this->db->_protect_identifiers=false;
        
        $this->db->select('SQL_CALC_FOUND_ROWS (products.Id) as pid', FALSE);
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
        
        //COUNT(productsreviews.ID) AS reviews_count,
        //AVG(productsreviews.rating) AS avg_rating,
                
        $this -> db -> select('sum(case when userwishlists.Id is not null and userwishlistproducts.Id is not null then 1 else 0 end) as wish_lists,
                           products.Id,
                           case when products.HouseId is not null then concat(retailers.CompanyName,\' \',products.ProductName) else products.ProductDescription end as ProductName,
                           products.ProductImage,
                           products.ProductDescription,
                           products.RRP,
                           products.Brand,
                           products.SKU,
                           case when userbasket.Id is null then \'\' else userbasket.Id end as BasketId,
                           
                           usersfavorite.ID AS is_favorite,
                           case when storeproducts.Price is null then "" else storeproducts.Price end AS store_price,
                           userspricealerts.ID AS price_alert,
                           productspecials.SpecialQty,
                           productspecials.SpecialPrice,
                           case when productspecials.PriceAppliedFrom is null or productspecials.PriceAppliedFrom = \'0000-00-00 00:00:00\' then \'\' else DATE_FORMAT(productspecials.PriceAppliedFrom,\'%d %b\') end as PriceAppliedFrom,
                           case when productspecials.PriceAppliedTo is null or productspecials.PriceAppliedTo = \'0000-00-00 00:00:00\' then \'\' else DATE_FORMAT(productspecials.PriceAppliedTo,\'%d %b\') end as PriceAppliedTo,
                           case when specials.IsStore is not null then specials.IsStore else 0 end as IsStore,
                           case when specials.Id is not null then specials.Id else 0 end as special_id,
                           case when productspecials.CouponAmount is not null then productspecials.CouponAmount else \'0.00\' end as CouponAmount', false);

        //$this -> db -> join('productsreviews', 'productsreviews.ProductId = products.Id', 'left');
        
        $this -> db -> join('userspricealerts', 'userspricealerts.ProductId = products.Id AND userspricealerts.UserId =' . $user_id, 'left');
        $this -> db -> join('storeproducts', 'storeproducts.ProductId = products.Id AND storeproducts.RetailerId =' . $retailer_id . " AND (storeproducts.StoreId=" . $store_id . " OR (storeproducts.StoreId=0 AND storeproducts.PriceForAllStores=1)) AND storeproducts.IsActive=1", 'left');
        $this -> db -> join('productspecials', 'productspecials.ProductId = products.Id AND DATE(productspecials.PriceAppliedFrom) <= "' . date('Y-m-d') . '" AND DATE(productspecials.PriceAppliedTo) >= "' . date('Y-m-d') . '" AND productspecials.RetailerId =' . $retailer_id . " AND (productspecials.StoreId=" . $store_id . " OR (productspecials.StoreId=0 AND productspecials.PriceForAllStores=1)) AND productspecials.IsActive=1 AND productspecials.IsApproved =1 ", 'left');
        $this -> db -> join('specials', 'specials.Id = productspecials.SpecialId and specials.IsActive = 1 and specials.IsRemoved = 0 '.$special_appent, $special_join);
        $this -> db -> join('retailers', 'retailers.Id = products.HouseId', 'left');

        /*
        $this -> db -> join('userbasket', 'userbasket.ProductId = products.Id AND userbasket.SpecialId = specials.Id AND userbasket.UserId =' . $user_id, 'left');
        $this -> db -> join('userwishlists', 'userwishlists.UserId = '.$user_id.' and userwishlists.IsActive = 1 and userwishlists.IsRemoved = 0', 'left');
        $this -> db -> join('userwishlistproducts', "userwishlistproducts.UserWishlistId = userwishlists.Id AND userwishlistproducts.SpecialId = specials.Id and userwishlistproducts.ProductId = ".$product_id, 'left');
        $this -> db -> join('usersfavorite', 'usersfavorite.ProductId = products.Id AND usersfavorite.SpecialId = specials.Id AND usersfavorite.UserId =' . $user_id, 'left');
       */
        
        //echo (int)$special_id; exit;
        
        if((int)$special_id > 0 ){
            $this -> db -> join('userbasket', 'userbasket.ProductId = products.Id AND userbasket.SpecialId = specials.Id AND userbasket.UserId =' . $user_id, 'left');
        }else{
            $this -> db -> join('userbasket', 'userbasket.ProductId = products.Id AND userbasket.SpecialId = 0 AND userbasket.UserId =' . $user_id, 'left');
        }
        
        $this -> db -> join('userwishlists', 'userwishlists.UserId = '.$user_id.' and userwishlists.IsActive = 1 and userwishlists.IsRemoved = 0', 'left');
        
        if((int)$special_id > 0 ){
           $this -> db -> join('userwishlistproducts', "userwishlistproducts.UserWishlistId = userwishlists.Id AND userwishlistproducts.SpecialId = specials.Id and userwishlistproducts.ProductId = ".$product_id, 'left'); 
        }else{
           $this -> db -> join('userwishlistproducts', "userwishlistproducts.UserWishlistId = userwishlists.Id AND userwishlistproducts.SpecialId = 0 and userwishlistproducts.ProductId = ".$product_id, 'left'); 
        }
        
        //$this -> db -> join('userwishlistproducts', "userwishlistproducts.UserWishlistId = userwishlists.Id AND userwishlistproducts.SpecialId = specials.Id and userwishlistproducts.ProductId = ".$product_id, 'left');
        
        $this -> db -> join('usersfavorite', 'usersfavorite.ProductId = products.Id AND ( usersfavorite.SpecialId = specials.Id OR usersfavorite.SpecialId = 0 ) AND usersfavorite.UserId =' . $user_id, 'left');
        
        $this -> db -> where(array(
            'products.IsActive' => 1,
            'products.IsRemoved' => 0
        ));

        $this -> db -> where('products.Id', $product_id);

        $query = $this -> db -> get('products');

       //echo $this->db->last_query();die;

        return $query -> row_array();
    }

    public function make_product_favorite($user_id, $product_id, $special_id) {

        $this -> db -> select(array('Id'));
        $this -> db -> from('usersfavorite');
        $this -> db -> where(array(
            'UserId' => $user_id,
            'ProductId' => $product_id,
            'SpecialId' => $special_id
        ));

        $this -> db -> limit(1);
        $query = $this -> db -> get();
        //echo $this->db->last_query();exit;
        
        //Make the product favorite
        if ($query -> num_rows() == 0) {

            $data = array(
                'UserId' => $user_id,
                'ProductId' => $product_id,
                'SpecialId' => $special_id,
                'CreatedOn' => date("Y-m-d H:i:s")
            );

            $this -> db -> insert('usersfavorite', $data);
            return "ADD";
        }
        else { // Remove product from favorite list
            $result = $query -> row_array();
            $this -> db -> where('Id', $result['Id']);
            $this -> db -> delete('usersfavorite');

            return "REMOVE";
        }
    }

    public function add_product_alert($user_id, $product_id) {

        $this -> db -> select(array('Id'));
        $this -> db -> from('userspricealerts');
        $this -> db -> where(array(
            'UserId' => $user_id,
            'ProductId' => $product_id
        ));
        $this -> db -> limit(1);
        $query = $this -> db -> get();

        //Make the product favorite
        if ($query -> num_rows() == 0) {

            $data = array(
                'UserId' => $user_id,
                'ProductId' => $product_id,
                'CreatedOn' => date("Y-m-d H:i:s")
            );

            $this -> db -> insert('userspricealerts', $data);
            return "ADD";
        }
        else { // Remove product from alert list
            $result = $query -> row_array();
            $this -> db -> where('Id', $result['Id']);
            $this -> db -> delete('userspricealerts');

            return "REMOVE";
        }
    }

    public function compare_product($product_id, $retailer_id , $store_id, $lat="", $long="") {
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
        $this -> db -> join('specials', 'specials.Id = productspecials.SpecialId and specials.IsActive = 1 and specials.IsRemoved = 0', 'left');

        $this -> db -> group_by('retailers.Id');
         $this -> db -> group_by('stores.Id');

        //$this -> db -> where_not_in('retailers.Id', $retailer_id);
         
        $this -> db -> where_not_in('stores.Id', $store_id); 
        $this -> db -> where('retailers.IsActive', 1);

        $this -> db -> where('storeproducts.productId', $product_id);

        $this -> db -> where('retailers.IsRemoved', 0);

        // Distance is added on 28 Feb 2017
        if ($lat != "" && $long != "") {
            $this -> db -> order_by('distance', 'ASC');
        }
        $this -> db -> order_by('storeproducts.Price', 'DESC');
        $this -> db -> order_by('productspecials.SpecialPrice', 'DESC');
        
         
        $query = $this -> db -> get('retailers');
       
        //echo $this->db->last_query();exit;
        
        return $query -> result_array();
    }
    
    
    public function compare_product_optimize_old($product_id, $retailer_id , $store_id, $lat="", $long="",$group_id) {
        if ($lat != "" && $long != "") {
            $this -> db -> select('MIN((6371 * acos( cos( radians(' . $lat . ') ) * cos( radians( stores.Latitude ) ) * cos( radians( stores.Longitude ) - radians(' . $long . ') ) + sin( radians(' . $lat . ') ) * sin( radians( stores.Latitude ) ) ) ))  AS distance');
        }
        
        $this -> db -> select('retailers.Id,
                           retailers.CompanyName,
                           retailers.LogoImage,
                           storeproducts.Price,                           
                           stores.StoreName');
        
       $subquery1 ='(SELECT SpecialQty FROM productspecials,specials WHERE specials.Id = productspecials.SpecialId and specials.IsActive = 1 and specials.IsRemoved = 0 AND productspecials.RetailerId =stores.RetailerId AND DATE(productspecials.PriceAppliedFrom) <= "'. date('Y-m-d').'" AND DATE(productspecials.PriceAppliedTo) >= "'. date('Y-m-d').'" AND (productspecials.StoreId= stores.Id OR (productspecials.StoreId=0 AND productspecials.PriceForAllStores=1)) AND productspecials.IsActive=1 AND productspecials.IsApproved =1 AND productspecials.productId= '.$product_id.') as Special_Qty';
       $subquery2 ='(SELECT SpecialPrice FROM productspecials,specials WHERE specials.Id = productspecials.SpecialId and specials.IsActive = 1 and specials.IsRemoved = 0 AND productspecials.RetailerId =stores.RetailerId AND DATE(productspecials.PriceAppliedFrom) <= "'.date('Y-m-d').'" AND DATE(productspecials.PriceAppliedTo) >= "'. date('Y-m-d').'" AND (productspecials.StoreId= stores.Id OR (productspecials.StoreId=0 AND productspecials.PriceForAllStores=1)) AND productspecials.IsActive=1 AND productspecials.IsApproved =1 AND productspecials.productId= '.$product_id.') as Special_Price';
       
       $this->db->select($subquery1);
       $this->db->select($subquery2);
        
       //$this -> db -> join('stores', 'stores.RetailerId = retailers.Id AND stores.IsActive =1 AND  stores.IsRemoved =0', 'left');
       $this -> db -> join('stores', 'stores.RetailerId = retailers.Id AND stores.IsActive =1 AND  stores.IsRemoved =0');
       if($group_id > 0 ){
           $this -> db -> join('stores_storegroups as ssg', 'stores.Id = ssg.StoreId');
       }
       $this -> db -> join('storeproducts', 'storeproducts.RetailerId = retailers.Id AND storeproducts.StoreId = stores.Id ', 'left');
       $this -> db -> where('storeproducts.productId', $product_id);
       $this -> db -> where_not_in('stores.Id', $store_id); 
       
       # Temporary solution - Added 28 April 2017 
       /*
        * Butchery 	- Independent Butcheries 	- 	50
	Liquor	 	- Independent Bottle Stores	- 	52	
	Pharmacies	- Independent Pharmacies	-	53
	Seafood		- independent Fishmongers	- 	51
        * 
        */
       $notAllowRetailer[]=50;
       $notAllowRetailer[]=51;
       $notAllowRetailer[]=52;
       $notAllowRetailer[]=53;
       
       if($group_id == 1 ){
          $this -> db -> where_not_in('retailers.Id', $notAllowRetailer);    
       }
       
       $this -> db -> where('retailers.IsActive', 1);
       $this -> db -> where('retailers.IsRemoved', 0);
       
       if($group_id > 0 )
       {
           $this -> db -> where('ssg.StoreGroupId', $group_id);
       }
       
       $this -> db -> group_by('retailers.Id');
       $this -> db -> group_by('stores.Id');
        
        // Distance is added on 28 Feb 2017
       if ($lat != "" && $long != "") {
            $this -> db -> order_by('distance', 'ASC');
       }
       $this -> db -> order_by('storeproducts.Price', 'DESC');
       //$this -> db -> order_by('productspecials.SpecialPrice', 'DESC');
        
       $query = $this -> db -> get('retailers');
       
       //echo $this->db->last_query();exit;
        
       return $query -> result_array();
    }
    
    
    public function compare_product_optimize($product_id, $retailer_id , $store_id, $lat="", $long="",$group_id) {
        if ($lat != "" && $long != "") {
            $this -> db -> select('MIN((6371 * acos( cos( radians(' . $lat . ') ) * cos( radians( stores.Latitude ) ) * cos( radians( stores.Longitude ) - radians(' . $long . ') ) + sin( radians(' . $lat . ') ) * sin( radians( stores.Latitude ) ) ) ))  AS distance');
        }
        
        $this -> db -> select('retailers.Id,
                           retailers.CompanyName,
                           retailers.LogoImage,
                           storeproducts.Price,                           
                           stores.StoreName');
        
       //$subquery1 ='(SELECT SpecialQty FROM productspecials,specials WHERE specials.Id = productspecials.SpecialId and specials.IsActive = 1 and specials.IsRemoved = 0 AND productspecials.RetailerId =stores.RetailerId AND DATE(productspecials.PriceAppliedFrom) <= "'. date('Y-m-d').'" AND DATE(productspecials.PriceAppliedTo) >= "'. date('Y-m-d').'" AND (productspecials.StoreId= stores.Id OR (productspecials.StoreId=0 AND productspecials.PriceForAllStores=1)) AND productspecials.IsActive=1 AND productspecials.IsApproved =1 AND productspecials.productId= '.$product_id.') as Special_Qty';
       //$subquery2 ='(SELECT SpecialPrice FROM productspecials,specials WHERE specials.Id = productspecials.SpecialId and specials.IsActive = 1 and specials.IsRemoved = 0 AND productspecials.RetailerId =stores.RetailerId AND DATE(productspecials.PriceAppliedFrom) <= "'.date('Y-m-d').'" AND DATE(productspecials.PriceAppliedTo) >= "'. date('Y-m-d').'" AND (productspecials.StoreId= stores.Id OR (productspecials.StoreId=0 AND productspecials.PriceForAllStores=1)) AND productspecials.IsActive=1 AND productspecials.IsApproved =1 AND productspecials.productId= '.$product_id.') as Special_Price';
       
       $subquery1 ='(SELECT CONCAT_WS("-", SpecialQty, SpecialPrice) FROM productspecials,specials WHERE specials.Id = productspecials.SpecialId and specials.IsActive = 1 and specials.IsRemoved = 0 AND productspecials.RetailerId =stores.RetailerId AND DATE(productspecials.PriceAppliedFrom) <= "'. date('Y-m-d').'" AND DATE(productspecials.PriceAppliedTo) >= "'. date('Y-m-d').'" AND (productspecials.StoreId= stores.Id OR (productspecials.StoreId=0 AND productspecials.PriceForAllStores=1)) AND productspecials.IsActive=1 AND productspecials.IsApproved =1 AND productspecials.productId= '.$product_id.') as Special_Qty_Price'; 
       $this->db->select($subquery1);
      // $this->db->select($subquery2);
        
       //$this -> db -> join('stores', 'stores.RetailerId = retailers.Id AND stores.IsActive =1 AND  stores.IsRemoved =0', 'left');
       $this -> db -> join('stores', 'stores.RetailerId = retailers.Id AND stores.IsActive =1 AND  stores.IsRemoved =0');
       if($group_id > 0 ){
           $this -> db -> join('stores_storegroups as ssg', 'stores.Id = ssg.StoreId');
       }
       $this -> db -> join('storeproducts', 'storeproducts.RetailerId = retailers.Id AND storeproducts.StoreId = stores.Id ', 'left');
       $this -> db -> where('storeproducts.productId', $product_id);
       $this -> db -> where_not_in('stores.Id', $store_id); 
       
       # Temporary solution - Added 28 April 2017 
       /*
        * Butchery 	- Independent Butcheries 	- 	50
	Liquor	 	- Independent Bottle Stores	- 	52	
	Pharmacies	- Independent Pharmacies	-	53
	Seafood		- independent Fishmongers	- 	51
        * 
        */
       $notAllowRetailer[]=50;
       $notAllowRetailer[]=51;
       $notAllowRetailer[]=52;
       $notAllowRetailer[]=53;
       
       if($group_id == 1 ){
          $this -> db -> where_not_in('retailers.Id', $notAllowRetailer);    
       }
       
       $this -> db -> where('retailers.IsActive', 1);
       $this -> db -> where('retailers.IsRemoved', 0);
       
       if($group_id > 0 )
       {
           $this -> db -> where('ssg.StoreGroupId', $group_id);
       }
       
       $this -> db -> group_by('retailers.Id');
       $this -> db -> group_by('stores.Id');
        
        // Distance is added on 28 Feb 2017
       if ($lat != "" && $long != "") {
            $this -> db -> order_by('distance', 'ASC');
       }
       $this -> db -> order_by('storeproducts.Price', 'DESC');
       //$this -> db -> order_by('productspecials.SpecialPrice', 'DESC');
        
       $query = $this -> db -> get('retailers');
       
       //echo $this->db->last_query();exit;
        
       return $query -> result_array();
    }
    
    
    public function compare_product_working_20March($product_id, $retailer_id , $lat="", $long="") {

       /* // Commented on 28 Feb And get parameters of WS
        //$lat = $this -> latitude;
        //$long = $this -> longitude;
         * 
         */

        //$lat="";
        //$long="";
        
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
        $this -> db -> join('specials', 'specials.Id = productspecials.SpecialId and specials.IsActive = 1 and specials.IsRemoved = 0', 'left');

        $this -> db -> group_by('retailers.Id');

        $this -> db -> where_not_in('retailers.Id', $retailer_id);
        $this -> db -> where('retailers.IsActive', 1);

        $this -> db -> where('storeproducts.productId', $product_id);

        $this -> db -> where('retailers.IsRemoved', 0);

        // Distance is added on 28 Feb 2017
        if ($lat != "" && $long != "") {
            $this -> db -> order_by('distance', 'ASC');
        }
        $this -> db -> order_by('storeproducts.Price', 'DESC');
        $this -> db -> order_by('productspecials.SpecialPrice', 'DESC');

        $query = $this -> db -> get('retailers');
         echo $this->db->last_query();exit;
        
        return $query -> result_array();
    }
    
    public function compare_product_old($product_id, $retailer_id , $lat="", $long="") {

       /* // Commented on 28 Feb And get parameters of WS
        //$lat = $this -> latitude;
        //$long = $this -> longitude;
         * 
         */

        $lat="";
        $long="";
        
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
        $this -> db -> join('specials', 'specials.Id = productspecials.SpecialId and specials.IsActive = 1 and specials.IsRemoved = 0', 'left');

        $this -> db -> group_by('retailers.Id');

        $this -> db -> where_not_in('retailers.Id', $retailer_id);
        $this -> db -> where('retailers.IsActive', 1);

        $this -> db -> where('storeproducts.productId', $product_id);

        $this -> db -> where('retailers.IsRemoved', 0);

        // Distance is added on 28 Feb 2017
        if ($lat != "" && $long != "") {
            $this -> db -> order_by('distance', 'ASC');
        }
        $this -> db -> order_by('storeproducts.Price', 'DESC');
        $this -> db -> order_by('productspecials.SpecialPrice', 'DESC');

        $query = $this -> db -> get('retailers');
        //echo $this->db->last_query();exit;
        
        return $query -> result_array();
    }
    

    public function get_favorite_products($user_id, $retailer_id) {
 
        # Get Preferred retailer and store 
        $store_id = 0;
        $preferred_details = $this->get_user_preferred_details($user_id);
        if($preferred_details)
        {
            $store_id = $preferred_details['store_id'];
        }
        
        if ($store_id) {
            $this->db->_protect_identifiers=false;
            $this -> db -> select('products.Id,
                               
                               products.ProductImage,
                               products.ProductDescription,
                               products.RRP,
                               products.Brand,
                               products.SKU,
                               COUNT(productsreviews.ID) AS reviews_count,
                               AVG(productsreviews.rating) AS avg_rating,
                               storeproducts.Price AS store_price,
                               productspecials.SpecialQty,
                               productspecials.SpecialPrice,
                               case when productspecials.PriceAppliedFrom is null or productspecials.PriceAppliedFrom = \'0000-00-00 00:00:00\' then \'\' else DATE_FORMAT(productspecials.PriceAppliedFrom,\'%d %b\') end as PriceAppliedFrom,
                               case when productspecials.PriceAppliedTo is null or productspecials.PriceAppliedTo = \'0000-00-00 00:00:00\' then \'\' else DATE_FORMAT(productspecials.PriceAppliedTo,\'%d %b\') end as PriceAppliedTo,
                               case when specials.IsStore is not null then specials.IsStore else 0 end as IsStore,
                               case when specials.Id is not null then specials.Id else 0 end as special_id',FALSE);
           
            $subquery1 = '(SELECT case when p.HouseId is null then p.ProductDescription else concat(retailers.CompanyName,\' \',p.ProductName) end as ProductName from products as p LEFT JOIN retailers ON retailers.Id = p.HouseId and retailers.IsActive = 1 and retailers.IsRemoved = 0  where p.Id = products.Id ) as ProductName';
            $this->db->select($subquery1);
            
            $this -> db -> join('productsreviews', 'productsreviews.ProductId = usersfavorite.ProductId', 'left');
            $this -> db -> join('products', 'products.Id = usersfavorite.ProductId');
            $this -> db -> join('storeproducts', 'storeproducts.ProductId = products.Id AND storeproducts.RetailerId =' . $retailer_id . " AND (storeproducts.StoreId=" . $store_id . " OR (storeproducts.StoreId=0 AND storeproducts.PriceForAllStores=1)) AND storeproducts.IsActive=1");
            $this -> db -> join('productspecials', 'productspecials.ProductId = products.Id and productspecials.SpecialId = usersfavorite.SpecialId AND DATE(productspecials.PriceAppliedFrom) <= "' . date('Y-m-d') . '" AND DATE(productspecials.PriceAppliedTo) >= "' . date('Y-m-d') . '" AND productspecials.RetailerId =' . $retailer_id . " AND (productspecials.StoreId=" . $store_id . " OR (productspecials.StoreId=0 AND productspecials.PriceForAllStores=1)) AND productspecials.IsActive=1 AND productspecials.IsApproved =1", 'left');
            $this -> db -> join('specials', 'specials.Id = usersfavorite.SpecialId and specials.IsActive = 1 and specials.IsRemoved = 0', 'left');

            $this -> db -> where(array(
                'products.IsActive' => 1,
                'products.IsRemoved' => 0,
                'usersfavorite.UserId' => $user_id,
            ));

            $this -> db -> order_by('productspecials.Id', 'DESC');

            $this -> db -> group_by('products.Id, specials.Id');

            //Get the limit & offset
            //        if ($this->page_no != '' && $this->page_limit != '') {
            //
    //            $start_from = ($this->page_no - 1) * $this->page_limit;
            //        }
            //
    //        $this->db->limit($this->page_limit, $start_from);

            $query = $this -> db -> get('usersfavorite');

            //echo $this -> db -> last_query();die;

            return $query -> result_array();
        }
        else {
            return FALSE;
        }
    }
    
    public function get_alert_products($user_id, $retailer_id) {

        // products.ProductName,
        // case when products.HouseId is null then products.ProductDescription else concat(retailers.CompanyName," ",products.ProductName) end as ProductName,
        $store_id = $this -> get_nearest_or_prefered_store($retailer_id);
        if ($store_id) {
            $this -> db -> select('products.Id,
                                userspricealerts.Id as alert_id,
                               
                               case when products.HouseId is null then products.ProductDescription else concat(retailers.CompanyName," ",products.ProductName) end as ProductName,
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
                               productspecials.SpecialPrice',FALSE);

            $this -> db -> join('products', 'products.Id = userspricealerts.ProductId');
            $this -> db -> join('productsreviews', 'productsreviews.ProductId = products.Id', 'left');
            $this -> db -> join('usersfavorite', 'usersfavorite.ProductId = products.Id AND usersfavorite.UserId =' . $user_id, 'left');
            $this -> db -> join('storeproducts', 'storeproducts.ProductId = products.Id AND storeproducts.RetailerId =' . $retailer_id . " AND (storeproducts.StoreId=" . $store_id . " OR (storeproducts.StoreId=0 AND storeproducts.PriceForAllStores=1)) AND storeproducts.IsActive=1");
            $this -> db -> join('productspecials', 'productspecials.ProductId = products.Id AND DATE(productspecials.PriceAppliedFrom) <= "' . date('Y-m-d') . '" AND DATE(productspecials.PriceAppliedTo) >= "' . date('Y-m-d') . '" AND productspecials.RetailerId =' . $retailer_id . " AND (productspecials.StoreId=" . $store_id . " OR (productspecials.StoreId=0 AND productspecials.PriceForAllStores=1)) AND productspecials.IsActive=1 AND productspecials.IsApproved =1", 'left');
            $this -> db -> join('specials', 'specials.Id = productspecials.SpecialId and specials.IsActive = 1 and specials.IsRemoved = 0', 'left');
            $this -> db -> join('retailers', 'retailers.Id = products.HouseId and retailers.IsActive = 1 and retailers.IsRemoved = 0', 'left');
            
            $this -> db -> where(array(
                'products.IsActive' => 1,
                'products.IsRemoved' => 0,
                'userspricealerts.UserId' => $user_id,
            ));

            $this -> db -> order_by('productspecials.Id', 'DESC');

            $this -> db -> group_by('products.Id');

            //        //Get the limit & offset
            //        if ($this->page_no != '' && $this->page_limit != '') {
            //
    //            $start_from = ($this->page_no - 1) * $this->page_limit;
            //        }
            //
    //        $this->db->limit($this->page_limit, $start_from);

            $query = $this -> db -> get('userspricealerts');

            return $query -> result_array();
        }
        else {
            return FALSE;
        }
    }

    public function get_products_by_shopping_list($retailer_id, $user_id, $shopping_list) {

        $results = array();

        foreach ($shopping_list as $shopping_list) {

            $this -> db -> select('products.Id');

            $this -> db -> where(array(
                'products.IsActive' => 1,
                'products.IsRemoved' => 0
            ));

            $this -> db -> like('products.ProductName', $shopping_list, 'both');

            $query = $this -> db -> get('products');

            $results[$shopping_list] = $query -> result_array();
        }

        return $results;
    }

    public function get_nearest_or_prefered_store($retailer_id) {

        $lat = $this -> latitude;
        $long = $this -> longitude;
        $store_id = $this -> store_id;

        //Check if user has selcted a store
        if ($this -> store_id != "") {

            return $store_id;
            //Check if lat & Long present
        }
        elseif ($lat != "" && $long != "") {

            $this -> db -> select('(6371 * acos( cos( radians(' . $lat . ') ) * cos( radians( stores.Latitude ) ) * cos( radians( stores.Longitude ) - radians(' . $long . ') ) + sin( radians(' . $lat . ') ) * sin( radians( stores.Latitude ) ) ) ) AS distance');
            $this -> db -> order_by('distance', 'ASC');
            //Default Store will be return
        }
        else {

            $this -> db -> order_by('stores.Id', 'ASC');
        }

        $this -> db -> select('stores.Id');

        $this -> db -> where(array(
            'stores.RetailerId' => $retailer_id,
            'stores.IsActive' => 1,
            'stores.IsRemoved' => 0
        ));

        $this -> db -> limit(1);

        $query = $this -> db -> get('stores');

//        $this -> db -> last_query();

        $result = $query -> row_array();

        $store_id = $result['Id'];

        return $store_id;
    }

    public function add_report_abuse($user_id, $product_id, $message) {
        $data = array(
            'UserId' => $user_id,
            'ProductId' => $product_id,
            'UserMessage' => $message,
            'CreatedOn' => date("Y-m-d H:i:s")
        );

        $result = $this -> db -> insert('productsabusereport', $data);

        if ($result) {
            return $this -> db -> insert_id();
        }
        else {
            return false;
        }
    }

    public function get_products_price_range($category_id, $retailer_id) {

        //Get the nearest store
        $store_id = $this -> get_nearest_or_prefered_store($retailer_id);

        $this -> db -> select_max('storeproducts.Price', 'max_price');
        $this -> db -> select_min('storeproducts.Price', 'min_price');
        $this -> db -> join('storeproducts', 'storeproducts.ProductId = products.Id AND storeproducts.RetailerId =' . $retailer_id . " AND (storeproducts.StoreId=" . $store_id . " OR (storeproducts.StoreId=0 AND storeproducts.PriceForAllStores=1)) AND storeproducts.IsActive=1");

        $this -> db -> where(array(
            'products.IsActive' => 1,
            'products.IsRemoved' => 0,
        ));

        if ($category_id) {
            $this -> db -> where("products.CategoryId =$category_id or products.ParentCategoryId =$category_id");
        }

        $query = $this -> db -> get('products');

        $result = $query -> row_array();

        return $result;
    }

    public function add_product_view($data) {

        $this -> db -> select('(TIME_TO_SEC(current_timestamp()) - TIME_TO_SEC(ViewDate))/60 as time_diff', false);
        $this -> db -> where(array(
            'ProductId' => $data['ProductId'],
            'RetailerId' => $data['RetailerId'],
            'UserId' => $data['UserId'],
            'StoreId' => $data['StoreId'],
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
            $this -> db -> insert('productviews', $data);
            return $this -> db -> insert_id();
        }
        return TRUE;
    }
    /* Function to get user basket data
     * return - array of user basket details
     */

    public function get_user_basket($user_id, $retailer_id) {

        //Get the nearest store
        $store_id = $this -> get_nearest_or_prefered_store($retailer_id);

        $this -> db -> select("products.Id, products.ProductName, products.ProductImage, storeproducts.Price,
                           productspecials.SpecialQty,
                           productspecials.SpecialPrice");
        $this -> db -> from('userbasket');

        $this -> db -> join('products', 'products.Id = userbasket.ProductId');
        $this -> db -> join('storeproducts', 'storeproducts.ProductId = products.Id AND storeproducts.RetailerId =' . $retailer_id . " AND (storeproducts.StoreId=" . $store_id . " OR (storeproducts.StoreId=0 AND storeproducts.PriceForAllStores=1)) AND storeproducts.IsActive=1");
        $this -> db -> join('productspecials', 'productspecials.ProductId = products.Id AND DATE(productspecials.PriceAppliedFrom) <= "' . date('Y-m-d') . '" AND DATE(productspecials.PriceAppliedTo) >= "' . date('Y-m-d') . '" AND productspecials.RetailerId =' . $retailer_id . " AND (productspecials.StoreId=" . $store_id . " OR (productspecials.StoreId=0 AND productspecials.PriceForAllStores=1)) AND productspecials.IsActive=1 AND productspecials.IsApproved =1 ", 'left');
        $this -> db -> join('specials', 'specials.Id = productspecials.SpecialId and specials.IsActive = 1 and specials.IsRemoved = 0', 'left');

        $this -> db -> where(array('userbasket.UserId' => $user_id));
        $this -> db -> group_by("storeproducts.ProductId");

        $this -> db -> order_by('userbasket.Id', 'Desc');

        $this -> db -> where(array(
            'products.IsActive' => 1,
            'products.IsRemoved' => 0,
        ));

        $query = $this -> db -> get();
        return $query -> result_array();
    }
    /* Function to get user basket data total for other retailers
     * return - array of user basket for other retailers details
     */

    public function get_user_basket_other_retailers($user_id, $retailer_id) {

        $this -> db -> select("retailers.LogoImage,retailers.LogoImage, storeproducts.Price");
        $this -> db -> from('userbasket');
        $this -> db -> join('products', 'products.Id = userbasket.ProductId');
        $this -> db -> join('storeproducts', 'products.Id = storeproducts.ProductId');
        $this -> db -> join('retailers', 'retailers.Id = storeproducts.RetailerId');
        $this -> db -> where(array('userbasket.UserId' => $user_id, 'retailers.Id !=' => $retailer_id));
        $this -> db -> group_by("storeproducts.RetailerId,storeproducts.ProductId");

        $this -> db -> where(array(
            'products.IsActive' => 1,
            'products.IsRemoved' => 0,
        ));
        $query = $this -> db -> get();
//        echo $this -> db -> last_query();die;
        return $query -> result_array();
    }

    public function get_store_details($retailer_id, $store_id) {
        $this -> db -> select("d.CompanyName as RetailerName, a.StoreName,b.OpenCloseDay, b.OpenCloseTimeFrom, case when a.Latitude is null then '' else a.Latitude end as Latitude, case when a.Longitude is null then '' else a.Longitude end as Longitude, a.StreetAddress, case when a.Zip is null then '' else a.Zip end as Zip, a.ContactPersonNumber, c.Name as StateName,d.LogoImage", false)
            -> from('stores as a')
            -> join('storetimings as b', 'a.Id = b.StoreId  and `b`.`OpenCloseStatus` = \'1\'', 'left')
            -> join('state as c', 'c.Id = a.StateId', 'left')
            -> join('retailers as d', 'd.Id = a.RetailerId')
            -> where('a.Id', $store_id);
        $query = $this -> db -> get();
//        echo $this -> db -> last_query();die;
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
        if ($this -> db -> insert('product_shares', $insert_data)) {
            return TRUE;
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

    public function get_related_products($retailer_id, $store_format_id, $store_id, $product_id, $MainCategoryId, $ParentCategoryId, $CategoryId, $user_id) {
        $this -> db -> select('sum(case when j.UserWishlistId is not null then 1 else 0 end) as wish_lists,a.Id,a.ProductName,a.ProductImage,a.ProductDescription,a.RRP,a.Brand,a.SKU,COUNT(f.ID) AS reviews_count,case when AVG(f.rating) is null then \'\' else AVG(f.rating) end AS avg_rating,g.Price AS store_price,a.CategoryId,case when h.ID is null  then \'\' else h.ID end  AS is_favorite,b.SpecialQty,b.SpecialPrice,i.IsStore,i.Id as special_id', false)
            -> from('products as a')
            -> join('productspecials as b', 'a.Id = b.ProductId and now() between b.PriceAppliedFrom and b.PriceAppliedTo')
            -> join('categories as c', 'c.Id = a.MainCategoryId')
            -> join('categories as d', 'd.Id = a.ParentCategoryId')
            -> join('categories as e', 'e.Id = a.CategoryId', 'left')
            -> join('productsreviews as f', 'f.ProductId = b.ProductId', 'left')
            -> join('storeproducts as g', 'g.ProductId = a.Id AND g.RetailerId = ' . $retailer_id . ' AND (g.StoreId=' . $store_id . ' OR (g.StoreId=0 AND g.PriceForAllStores=1)) AND g.IsActive=1')
            -> join('usersfavorite as h', 'h.ProductId = a.Id AND h.UserId =' . $user_id, 'left')
            -> join('specials as i', 'i.Id = b.SpecialId and i.IsActive = 1 and i.IsRemoved = 0')
            -> join('userwishlistproducts as j', "j.UserId = $user_id and j.ProductId = a.Id", 'left')
            -> join('userwishlists as k', 'k.Id = j.UserWishlistId and k.IsActive = 1 and k.IsRemoved = 0', 'left')
            -> where('a.Id != ' . $product_id)
            -> where('if(e.Id is null,a.ParentCategoryId = ' . $ParentCategoryId . ',a.CategoryId = ' . $CategoryId . ')')
            -> where('b.RetailerId = ' . $retailer_id . ' and b.StoreTypeId = ' . $store_format_id . ' and b.StoreId = ' . $store_id)
            -> where('a.IsActive', 1)
            -> where('a.IsRemoved', 0)
            -> group_by('a.id, i.Id')
            -> having('a.id is not null');
        $query = $this -> db -> get();
       // echo $this -> db -> last_query(); die;
        if ($query -> num_rows() > 0) {
            return $query -> result_array();
        }
        return FALSE;
    }

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

    public function get_additional_details($store_id) {
        $this -> db -> select('b.CompanyName as RetailerName, a.StoreName', FALSE)
            -> from('stores as a')
            -> join('retailers as b', 'b.Id = a.RetailerId')
            -> where(
                array(
                    'a.IsActive' => 1,
                    'a.IsRemoved' => 0,
                    'a.IsActive' => 1,
                    'a.IsRemoved' => 0,
                    'a.IsActive' => 1,
                    'a.IsRemoved' => 0,
                    'a.Id' => $store_id
                )
        );
        $query = $this -> db -> get();
        if($query -> num_rows() > 0){
            return $query -> row_array();
        }
        return FALSE;
    }
    
    public function delete_alerts($ids) {
        $this -> db -> where_in('Id', $ids);
        //Delete the notifications
        $this -> db -> delete('userspricealerts');
//        echo $this -> db -> last_query();die;
    }
    
    
    /*
     * Funtion to check user's preferred details ( Retailers and store)
     */
    public function get_user_preferred_details($userId) {
        $this->db->select('retailers.Id as retailer_id,retailers.CompanyName, stores.Id as store_id,stores.StoreName');
        $this->db->join('users', 'users.Id = userpreferredbrands.UserId');
        $this->db->join('retailers', 'retailers.Id = userpreferredbrands.RetailerId');
        $this->db->join('stores', 'stores.Id = userpreferredbrands.StoreId');

        $this->db->where(array(
            'users.IsActive' => 1,
            'users.IsRemoved' => 0,
            'retailers.IsActive' => 1,
            'retailers.IsRemoved' => 0,
            'stores.IsActive' => 1,
            'stores.IsRemoved' => 0,
            'userpreferredbrands.UserId' => $userId
        ));

        $query = $this->db->get('userpreferredbrands');

         if ($query->num_rows() > 0) {
            return $query->row_array();
        } else {
            return array();
        }
    }
    
    /*
     * Funtion to check user's details
     */
    public function get_user_details($user_id) {
        $this -> db -> select('*');
        $this -> db -> from('users');
        $this -> db -> where(array('Id' => $user_id));
        $this -> db -> limit(1);

        $query = $this -> db -> get();

        return $query -> row_array();
    }
    
    
    /* Function to check if user has marked any product as favorite */
    public function is_product_favorite($user_id, $product_id, $special_id) {
        $this -> db -> select(array('Id'));
        $this -> db -> from('usersfavorite');
        $this -> db -> where(array(
            'UserId' => $user_id,
            'ProductId' => $product_id,
            'SpecialId' => $special_id
        ));
        $this -> db -> limit(1);
        $query = $this -> db -> get();
        //echo $this->db->last_query();exit;
        if ($query -> num_rows()> 0 ) {
           return 1;
        }else { 
            return 0;
        }
    }
    
    public function get_promotions($retailer_id, $user_id,$store_id) {
        
        # Generate Query 
        $this->db->select('specials.Id as special_id, specials.SpecialName', FALSE);
        $this -> db -> join('productspecials', 'productspecials.SpecialId = specials.Id AND DATE(productspecials.PriceAppliedFrom) <= "' . date('Y-m-d') . '" AND DATE(productspecials.PriceAppliedTo) >= "' . date('Y-m-d') . '" AND productspecials.RetailerId =' . $retailer_id . " AND (productspecials.StoreId=" . $store_id . " OR (productspecials.StoreId=0 AND productspecials.PriceForAllStores=1)) AND productspecials.IsActive=1 AND productspecials.IsApproved =1 ");
        $this -> db -> where(array(
            'specials.IsActive' => 1,
            'specials.IsRemoved' => 0,
        ));

        $this -> db -> group_by('specials.Id');
        $query = $this -> db -> get('specials');
        //echo $this->db->last_query();exit;
        return $query -> result_array();
    }
    
    
    public function get_promotion_products($retailer_id,$store_id, $special_id, $user_id, $get_total,$limit_start = 0) {
        # Generate Query 
        $this->db->select('SQL_CALC_FOUND_ROWS (products.Id) as pid', FALSE);
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
        $this -> db -> select('sum(case when userwishlistproducts.UserWishlistId is not null then 1 else 0 end) as wish_lists,
                           products.Id,
                           case when products.HouseId is null then products.ProductDescription else concat(retailers.CompanyName," ",products.ProductName) end as ProductName,
                           products.ProductImage,
                           products.ProductDescription,
                           products.RRP,
                           products.Brand,
                           products.SKU,
                           storeproducts.Price AS store_price,
                           productspecials.SpecialQty,
                           productspecials.SpecialPrice,
                           case when userbasket.Id is null then \'\' else userbasket.Id end as BasketId,
                           case when userspricealerts.Id is null then \'0\' else \'1\' end as price_alert,
                           case when productspecials.PriceAppliedFrom is null or productspecials.PriceAppliedFrom = \'0000-00-00 00:00:00\' then \'\' else DATE_FORMAT(productspecials.PriceAppliedFrom,\'%d %b\') end as PriceAppliedFrom,
                           case when productspecials.PriceAppliedTo is null or productspecials.PriceAppliedTo = \'0000-00-00 00:00:00\' then \'\' else DATE_FORMAT(productspecials.PriceAppliedTo,\'%d %b\') end as PriceAppliedTo,
                           specials.IsStore,
                           specials.Id as special_id', FALSE);
        $this -> db -> join('retailers', 'retailers.Id = products.HouseId and retailers.IsActive = 1 and retailers.IsRemoved = 0', 'left');
        $this -> db -> join('userspricealerts', 'userspricealerts.ProductId = products.Id AND userspricealerts.UserId =' . $user_id, 'left');
        $this -> db -> join('storeproducts', 'storeproducts.ProductId = products.Id AND storeproducts.RetailerId =' . $retailer_id . " AND (storeproducts.StoreId=" . $store_id . " OR (storeproducts.StoreId=0 AND storeproducts.PriceForAllStores=1)) AND storeproducts.IsActive=1");
        $this -> db -> join('productspecials', 'productspecials.ProductId = products.Id AND DATE(productspecials.PriceAppliedFrom) <= "' . date('Y-m-d') . '" AND DATE(productspecials.PriceAppliedTo) >= "' . date('Y-m-d') . '" AND productspecials.RetailerId =' . $retailer_id . " AND (productspecials.StoreId=" . $store_id . " OR (productspecials.StoreId=0 AND productspecials.PriceForAllStores=1)) AND productspecials.IsActive=1 AND productspecials.IsApproved =1 ");
        $this -> db -> join('specials', 'specials.Id = productspecials.SpecialId and specials.IsActive = 1 and specials.IsRemoved = 0');
        $this -> db -> join('userwishlistproducts', "userwishlistproducts.ProductId = products.Id and userwishlistproducts.UserId = ".$user_id." AND userwishlistproducts.SpecialId = specials.Id", 'left');
        $this -> db -> join('userwishlists', 'userwishlists.Id = userwishlistproducts.UserWishlistId and userwishlists.IsActive = 1 and userwishlists.IsRemoved = 0', 'left');
        //$this -> db -> join('usersfavorite', 'usersfavorite.ProductId = products.Id AND usersfavorite.SpecialId = specials.Id AND usersfavorite.UserId =' . $user_id, 'left');
        $this -> db -> join('userbasket', 'userbasket.ProductId = products.Id AND userbasket.SpecialId = specials.Id AND userbasket.UserId =' . $user_id, 'left');
        $this -> db -> where(array(
            'products.IsActive' => 1,
            'products.IsRemoved' => 0,
        ));

        $this -> db -> order_by('productspecials.Id', 'DESC');
        $this -> db -> group_by('products.Id, specials.Id');

        if($special_id )
        {
            $this -> db -> where("specials.Id",$special_id);
        }
        
        if (isset($category_id)) {
            $this -> db -> where("products.CategoryId =$category_id or products.ParentCategoryId =$category_id");
        }
        
        # Add Pagination 
        if (!$get_total) {
            $this -> db -> limit(API_PAGE_LIMIT, $limit_start);
            //$this -> db -> limit(2, $limit_start);
            $query = $this -> db -> get('products');
            //echo $this->db->last_query();exit;
            return $query -> result_array();
        }else { 
          //Get Number of products
          $query = $this -> db -> get('products');
          return $query -> num_rows();
        }
    }
    
     /* Function to get store product price */
    public function get_product_store_price($product_id,$retailer_id,$storeTypeId,$store_id) {
        
        # Generate Query 
        $this->db->select('Price', FALSE);        
        $this -> db -> where(array(
            'IsActive' => 1,
            'IsRemoved' => 0,
            'ProductId' => $product_id,
            'RetailerId' => $retailer_id,
            'StoreTypeId' => $storeTypeId,
            'StoreId' => $store_id
        ));
        $query = $this -> db -> get('storeproducts');
        //echo $this->db->last_query();exit;
        
        if ($query -> num_rows() > 0) {
            $res_array = $query -> row_array();
            return $res_array['Price'];
        }else {
            return 0;
        }
    }
    
    public function get_product_list($category_id, $retailer_id, $user_id, $product_ids = array(), $search_array = array(), $get_total = 0, $limit_start = 0, $brands = [], $store_id = '', $storetype_id = '') {
        $start_from = 0;

        # Get Products in the Category
        $this->db->select('products.Id', FALSE);        
        $this -> db -> where(array(
            'IsActive' => 1,
            'IsRemoved' => 0
        ));
        if ($category_id) {
            $this -> db -> where("(products.CategoryId =" . $category_id . " or products.ParentCategoryId =" . $category_id . ")");
        }
        $query = $this -> db -> get('products');
        //echo $this->db->last_query();exit;
        
        $productIds = $allProductIds = array();
        
        if ($query -> num_rows() > 0) {
            $res_array = $query -> result_array();            
            
            foreach($res_array as $res)
            {
                $productIds[]= $res['Id']; 
            }
        }         
        
        //Get the nearest store
        if ($store_id == '') {
            $store_id = $this -> get_nearest_or_prefered_store($retailer_id);
        }

        $this->db->select('SQL_CALC_FOUND_ROWS (products.Id) as pid', FALSE);
        
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
        //products.ProductDescription as ProductName,
        $this -> db -> select('products.Id,
                           case when products.HouseId is null then products.ProductDescription else concat(retailers.CompanyName," ",products.ProductName) end as ProductName,
                           products.ProductImage,
                           products.ProductDescription,
                           products.RRP,
                           products.Brand,
                           products.SKU,                                                      
                           storeproducts.Price AS store_price', FALSE);
        $this -> db -> join('storeproducts', "storeproducts.ProductId = products.Id AND storeproducts.RetailerId = $retailer_id  AND (storeproducts.StoreId= $store_id  OR (storeproducts.StoreId=0 AND storeproducts.PriceForAllStores=1)) AND storeproducts.IsActive=1");
        $this -> db -> join('retailers', 'retailers.Id = products.HouseId and retailers.IsActive = 1 and retailers.IsRemoved = 0', 'left');
        $this -> db -> where(array(
            'products.IsActive' => 1,
            'products.IsRemoved' => 0            
        ));
        $this -> db -> order_by('products.ProductDescription', 'ASC');
        //$this -> db -> order_by('products.ProductName', 'ASC');
        $this -> db -> group_by('products.Id');

        if ($category_id) {
            $this -> db -> where("(products.CategoryId =" . $category_id . " or products.ParentCategoryId =" . $category_id . ")");
        }

        if ($product_ids) {
            $this -> db -> where_in('products.Id', $product_ids);
        }
        
        if ($productIds) {
            $this -> db -> where_in('products.Id', $productIds);
        }
        
        if (!empty($search_array)) {

            //Keyword Search
            if (!empty($search_array['keyword'])) {
                $this -> db -> like('products.ProductName', $search_array['keyword'], 'both');
            }
            $filter_query = "";
            //Price Range Filter
            if (!empty($search_array['price_range'])) {
                foreach ($search_array['price_range'] as $range) {

                    if (!empty($filter_query)) {
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
            }

            if (!empty($filter_query)) {
                $this -> db -> where("(" . $filter_query . ")");
            }
        }

        if (is_array($brands) && !empty($brands)) {
            //$this -> db -> where('products.Brand',$brand);
            $cnt = 1;
            $brand_filter = '(';
            foreach ($brands as $brand) {
                if ($cnt == 1) {
                    $brand_filter .= 'products.Brand = \'' . $brand['name'] . '\' ';
                }
                else {
                    $brand_filter .= ' OR products.Brand = \'' . $brand['name'] . '\' ';
                }
                $cnt++;
            }
            $brand_filter .= ')';
            $this -> db -> where($brand_filter);
        }

        if (!$get_total) { //Pagination
            //Get the limit & offset
            if ($this -> page_no != '' && $this -> page_limit != '') {

                $start_from = ($this -> page_no - 1) * $this -> page_limit;
            }
            //$this -> db -> limit($this -> page_limit, $start_from);


            $this -> db -> limit(API_PAGE_LIMIT, $limit_start);
            $query = $this -> db -> get('products');

            //echo $this->db->last_query();exit;

            return $query -> result_array();
        }
        else { //Get Number of products
            $query = $this -> db -> get('products');

            return $query -> num_rows();
        }
    }
    
    
    public function product_specials_details($product_id, $retailer_id, $store_id)
    {        
        $this -> db -> select('productspecials.Id,
                            productspecials.SpecialQty,
                           productspecials.SpecialPrice,
                           case when productspecials.PriceAppliedFrom is null or productspecials.PriceAppliedFrom = \'0000-00-00 00:00:00\' then \'\' else DATE_FORMAT(productspecials.PriceAppliedFrom,\'%d %b\') end as PriceAppliedFrom,
                           case when productspecials.PriceAppliedTo is null or productspecials.PriceAppliedTo = \'0000-00-00 00:00:00\' then \'\' else DATE_FORMAT(productspecials.PriceAppliedTo,\'%d %b\') end as PriceAppliedTo,
                           case when specials.IsStore is not null then specials.IsStore else 0 end as IsStore,
                           case when specials.Id is not null then specials.Id else 0 end as special_id', FALSE);
        $this -> db -> join('specials', 'specials.Id = productspecials.SpecialId and specials.IsActive = 1 and specials.IsRemoved = 0','left');
        $this -> db -> where(array(
            'productspecials.ProductId' => $product_id,
            'productspecials.RetailerId' => $retailer_id,
            'productspecials.StoreId' => $store_id,
            'productspecials.IsActive' => 1,
            'productspecials.IsApproved' => 1
        ));
        
        $this -> db -> where('productspecials.PriceAppliedFrom <= ', date('Y-m-d'));
        $this -> db -> where('productspecials.PriceAppliedTo >= ', date('Y-m-d'));
        
        $query = $this -> db -> get('productspecials');
        //echo $this->db->last_query();exit;
        
        if ($query -> num_rows() > 0) {
            return $res_array = $query -> row_array();
        }else {
            return FALSE;
        }
        
    }
    
    
    public function get_all_hot_deals($category_id, $retailer_id, $user_id) {
        # Get the nearest store
        $store_id = $this -> get_nearest_or_prefered_store($retailer_id);
        
        # Generate Query 
        //$this->db->select('SQL_CALC_FOUND_ROWS (products.Id) as pid', FALSE);
        
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
        
        $this -> db -> select('sum(case when userwishlistproducts.UserWishlistId is not null then 1 else 0 end) as wish_lists,
                           products.Id,                           
                           case when products.HouseId is null then \'0\' else products.HouseId end as HouseId,
                           products.ProductName,
                           products.ProductImage,
                           products.ProductDescription,
                           products.RRP,
                           products.Brand,
                           products.SKU,
                           storeproducts.Price AS store_price,
                           productspecials.SpecialQty,
                           productspecials.SpecialPrice,
                           case when userbasket.Id is null then \'\' else userbasket.Id end as BasketId,
                           case when userspricealerts.Id is null then \'0\' else \'1\' end as price_alert,
                           case when productspecials.PriceAppliedFrom is null or productspecials.PriceAppliedFrom = \'0000-00-00 00:00:00\' then \'\' else DATE_FORMAT(productspecials.PriceAppliedFrom,\'%d %b\') end as PriceAppliedFrom,
                           case when productspecials.PriceAppliedTo is null or productspecials.PriceAppliedTo = \'0000-00-00 00:00:00\' then \'\' else DATE_FORMAT(productspecials.PriceAppliedTo,\'%d %b\') end as PriceAppliedTo,
                           specials.IsStore,
                           specials.Id as special_id', FALSE);

        $this -> db -> join('userspricealerts', 'userspricealerts.ProductId = products.Id AND userspricealerts.UserId =' . $user_id, 'left');
        $this -> db -> join('storeproducts', 'storeproducts.ProductId = products.Id AND storeproducts.RetailerId =' . $retailer_id . " AND (storeproducts.StoreId=" . $store_id . " OR (storeproducts.StoreId=0 AND storeproducts.PriceForAllStores=1)) AND storeproducts.IsActive=1");
        $this -> db -> join('productspecials', 'productspecials.ProductId = products.Id AND DATE(productspecials.PriceAppliedFrom) <= "' . date('Y-m-d') . '" AND DATE(productspecials.PriceAppliedTo) >= "' . date('Y-m-d') . '" AND productspecials.RetailerId =' . $retailer_id . " AND (productspecials.StoreId=" . $store_id . " OR (productspecials.StoreId=0 AND productspecials.PriceForAllStores=1)) AND productspecials.IsActive=1 AND productspecials.IsApproved =1 ");
        $this -> db -> join('specials', 'specials.Id = productspecials.SpecialId and specials.IsActive = 1 and specials.IsRemoved = 0');
        $this -> db -> join('userwishlistproducts', "userwishlistproducts.ProductId = products.Id and userwishlistproducts.UserId = ".$user_id." AND userwishlistproducts.SpecialId = specials.Id", 'left');
        $this -> db -> join('userwishlists', 'userwishlists.Id = userwishlistproducts.UserWishlistId and userwishlists.IsActive = 1 and userwishlists.IsRemoved = 0', 'left');
        //$this -> db -> join('usersfavorite', 'usersfavorite.ProductId = products.Id AND usersfavorite.SpecialId = specials.Id AND usersfavorite.UserId =' . $user_id, 'left');
        $this -> db -> join('userbasket', 'userbasket.ProductId = products.Id AND userbasket.SpecialId = specials.Id AND userbasket.UserId =' . $user_id, 'left');
        $this -> db -> where(array(
            'products.IsActive' => 1,
            'products.IsRemoved' => 0,
        ));

        $this -> db -> order_by('productspecials.Id', 'DESC');
        $this -> db -> group_by('products.Id, specials.Id');

        if ($category_id) {
            $this -> db -> where("products.CategoryId =$category_id or products.ParentCategoryId =$category_id");
        }
        
        $this -> db -> limit(30);
        $query = $this -> db -> get('products');
        
        //echo $this->db->last_query();exit;
        return $query -> result_array();
            
        /*
        # Add Pagination 
        if (!$get_total) {
            $this -> db -> limit(API_PAGE_LIMIT, $limit_start);
            $query = $this -> db -> get('products');
            //echo $this->db->last_query();exit;
            return $query -> result_array();
        }else { 
          //Get Number of products
          $query = $this -> db -> get('products');
          return $query -> num_rows();
        }
        */
        
    }
    
    /*  Function to get active promotios */ 
     
    public function get_active_promotions() {
        $this->db->select('specials.Id as special_id, specials.SpecialName', FALSE);
        $this -> db -> join('productspecials','productspecials.SpecialId = specials.Id AND DATE(productspecials.PriceAppliedFrom) <= "' . date('Y-m-d') . '" AND DATE(productspecials.PriceAppliedTo) >= "' . date('Y-m-d') . '" AND productspecials.IsActive=1 AND productspecials.IsApproved =1 ');
        $this -> db -> where(array(
            'specials.IsActive' => 1,
            'specials.IsRemoved' => 0,
        ));
        $this -> db -> group_by('specials.Id');
        $query = $this -> db -> get('specials');
        return $query -> result_array();
    }
    
    /*  Functin to get special stores nearest to user from his current location */ 
    public function get_special_store_details($splId,$lat,$long,$prefDistance) {
        # Generate Query 
        if($lat != 0 && $long != 0)
        {
           $this -> db -> select('ROUND((6371 * acos( cos( radians(' . $lat . ') ) * cos( radians( s.Latitude ) ) * cos( radians( s.Longitude ) - radians(' . $long . ') ) + sin( radians(' . $lat . ') ) * sin( radians( s.Latitude ) ) ) ),2) AS distance', FALSE);
        }
        
        $this->db->select('ps.SpecialId, ps.RetailerId, ps.StoreId, s.StoreName, r.CompanyName as RetailerName,s.StoreTypeId', FALSE);
        $this -> db -> join('stores as s','s.Id = ps.StoreId');
        $this -> db -> join('retailers as r', 'r.Id = s.RetailerId');  
         
        $this -> db -> where(array(
            'ps.SpecialId' => $splId
        ));
        
        $cond1 = 'ROUND((6371 * acos( cos( radians(' . $lat . ') ) * cos( radians( s.Latitude ) ) * cos( radians( s.Longitude ) - radians(' . $long . ') ) + sin( radians(' . $lat . ') ) * sin( radians( s.Latitude ) ) ) ),2) <= '.$prefDistance;
        $this -> db -> where($cond1);        
        //$this -> db -> order_by('ps.Id', 'DESC');
        $this -> db -> order_by('distance', 'ASC');
        $this -> db -> group_by('ps.StoreId');
        $this -> db -> limit(1);
        
        $query = $this -> db -> get('productspecials as ps');
        //echo $this->db->last_query();exit;
        return $query -> row_array();
    }
    
    /* Get the combo products for the particular special product */
    public function get_combo_products($product_id, $special_id) {
        $noProducts = array();
        
        $this -> db -> select('cp.Id, cp.RetailerId, cp.SpecialId, cp.SpecialProductId, cp.ProductId, cp.Quantity,p.ProductName as ComboProductName');
        $this -> db -> from('special_combo_products as cp');
        $this -> db -> join('products as p','p.Id = cp.ProductId');
        $this -> db -> where('SpecialId', $special_id);    
        $this -> db -> where('SpecialProductId', $product_id);               
        $query = $this -> db -> get();
        
        if ($query -> num_rows() > 0) {
            return $query -> result_array();
        }
        else {
            return $noProducts;
        }
    }
	
	public function savespecialbrowse($retailer_id,$store_id, $special_id) {
		$this -> db -> select('Id,totalVisit');
        $this -> db -> from('specialBrowse');
		$this -> db -> where(array(
            'RetailerId' => $retailer_id,
			'StoreId' => $store_id,
			'specialId' => $special_id
        ));
		$query = $this -> db -> get();
		if ($query -> num_rows() > 0){
			$row=$query->row_array();
			$totalVisit=($row['totalVisit']+1);		
			$data = array(              
                'totalVisit' => $totalVisit
            );
			$this->db->where('Id',$row['Id']);
            $this -> db -> update('specialBrowse', $data);
			
		} 
		else {
			$totalVisit=1;
			$data = array(
				'RetailerId'=>$retailer_id,
				'StoreId'=>$store_id,
                'SpecialId' => $special_id,
                'totalVisit' => $totalVisit
            );
			 $this -> db -> insert('specialBrowse', $data);
		}
		
			return 1;
    }
}