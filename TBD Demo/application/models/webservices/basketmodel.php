<?php

/*
 * Author: Name:PHN
 * Purpose: Model for controlling database interactions regarding the content.
 * Date:04-09-2015
 * Dependency: None
 */

class basketmodel extends CI_Model {
    /*
     * Purpose: Constructor.
     * Date: 04-09-2015
     * Input Parameter: None
     * Output Parameter: None
     */


    /* Add to basket
     *  param - product_id : Id of product to add to basket
     *  return - Id of inserted record
     */

    public function add_to_basket($special_id, $product_id, $user_id, $product_count) {

        $ins_arr = array(
            'SpecialId' => $special_id,
            'ProductId' => $product_id,
            'UserId' => $user_id,
            'productCount' => $product_count
        );

        $this -> db -> select("Id");
        $this -> db -> from("userbasket");
        $this -> db -> where(
            array(
                'SpecialId' => $special_id,
                'ProductId' => $product_id,
                'UserId' => $user_id
            )
        );
        $query = $this -> db -> get();

        if ($query -> num_rows() > 0) {
            $where = array(
                'SpecialId' => $special_id,
                'ProductId' => $product_id,
                'UserId' => $user_id
            );
            $update_data = array(
                'productCount' => $product_count
            );
            $this -> db -> where($where);
            $this -> db -> update('userbasket', $update_data);
            return '1';
            //return 'duplicate';
        }
        else {
            $ins_arr = array(
                'SpecialId' => $special_id,
                'ProductId' => $product_id,
                'productCount' => $product_count,
                'UserId' => $user_id,
                'CreatedBy' => $user_id,
                'CreatedOn' => date("Y/m/d H:i:s")
            );
            $this -> db -> insert('userbasket', $ins_arr);
            return $this -> db -> insert_id();
        }
    }
    /* Function to remove product from basket
     *  Param - product_id of product to be removed
     *  return - success/failure
     */

    public function remove_from_basket($product_id, $user_id) {
        $arr = array('ProductId' => $product_id, 'UserId' => $user_id);

        if ($this -> db -> delete('userbasket', $arr))
            return true;
        else
            return false;
    }
    /* Function to total count of basket
     *  Param - product_id of product to be removed
     *  return - count
     */

    public function get_basket_count($user_id) {
      if($user_id > 0 )
      {
            $user_preference = $this -> get_user_preferred_retailer($user_id);
            
            $retailer_id = $user_preference->Id;
            $store_id = $user_preference->StoreId;

            $query = $this -> db -> query("select sum(count) as count from (
                        SELECT userbasket.ProductCount as count
                        FROM (userbasket)
                        JOIN products ON products.Id = userbasket.ProductId
                        JOIN storeproducts ON products.Id = storeproducts.ProductId
                        WHERE `userbasket`.`UserId` =  $user_id
                        AND `products`.`IsActive` =  1
                        AND `products`.`IsRemoved` =  0
                        AND `storeproducts`.`IsActive` = 1 
                        AND `storeproducts`.`IsRemoved` = 0
                        AND `storeproducts`.`Price` > 0                    
                        AND `storeproducts`.`RetailerId` =  $retailer_id
                        AND `storeproducts`.`StoreId` =  $store_id
                        group by products.Id )  as abc");
            
            if ($query -> num_rows() > 0) {
                $result = $query -> row_array();
                $basketCount = $result['count'];
                if($basketCount != NULL || $basketCount > 0 )
                {
                    return $basketCount;
                }else{
                   return 0; 
                }
            }
            else {
                return 0;
            }
         }else{
             return 0;
         }
    }
    
    public function get_user_basket_count($user_id,$retailer_id,$store_id) {
      if($user_id > 0)
      {
            if($retailer_id == 0 && $store_id == 0)
            {
                $user_preference = $this -> get_user_preferred_retailer($user_id);

                $retailer_id = $user_preference->Id;
                $store_id = $user_preference->StoreId;
            }
                   
            $query = $this -> db -> query("select sum(count) as count from (
                        SELECT userbasket.ProductCount as count
                        FROM (userbasket)
                        JOIN products ON products.Id = userbasket.ProductId
                        JOIN storeproducts ON products.Id = storeproducts.ProductId
                        WHERE `userbasket`.`UserId` =  $user_id
                        AND `userbasket`.`RetailerId` =  $retailer_id
                        AND `userbasket`.`StoreId` =  $store_id
                        AND `products`.`IsActive` =  1
                        AND `products`.`IsRemoved` =  0
                        AND `storeproducts`.`IsActive` = 1 
                        AND `storeproducts`.`IsRemoved` = 0
                        AND `storeproducts`.`Price` > 0                    
                        AND `storeproducts`.`RetailerId` =  $retailer_id
                        AND `storeproducts`.`StoreId` =  $store_id
                        group by products.Id )  as abc");
            
            //echo $this -> db -> last_query();exit;
            
            if ($query -> num_rows() > 0) {
                $result = $query -> row_array();
                $basketCount = $result['count'];
                if($basketCount != NULL || $basketCount > 0 )
                {
                    return $basketCount;
                }else{
                   return 0; 
                }
            }
            else {
                return 0;
            }
      }else{
         return 0;
      }
    }
    
    
    /* Function to get user basket data
     * return - array of user basket details
     */

    public function get_user_basket($limit = 1, $user_id, $retailer_id, $store_type_id, $store_id) {
        $user_preference = $this -> get_user_preferred_retailer($user_id);

        $this -> db -> select("b.Id, case when b.HouseId is null then b.ProductDescription else concat(d.CompanyName,' ',b.ProductName) end as ProductName, b.ProductImage, round(sum(case when e.SpecialPrice > 0 and e.SpecialQty > 0 then ((e.SpecialPrice/e.SpecialQty)*a.ProductCount) when e.SpecialPrice > 0 then e. SpecialPrice*a.ProductCount else c.Price*a.ProductCount end),2) as Price, a.ProductCount, a.Id as BasketId", false);
        $this -> db -> from('userbasket as a');
        $this -> db -> join('products as b', 'b.Id = a.ProductId');
        $this -> db -> join('storeproducts as c', 'b.Id = c.ProductId');
        $this -> db -> join('retailers as d', 'd.Id = c.RetailerId');
        $this -> db -> join('productspecials as e', 'e.ProductId = c.ProductId and e.RetailerId = d.Id and e.StoreTypeId = c.StoreTypeId and e.StoreId = c.StoreId AND DATE(e.PriceAppliedFrom) <= \'' . date('Y-m-d') . '\' AND DATE(e.PriceAppliedTo) >= \'' . date('Y-m-d') . '\' and e.IsActive=1 and e.IsApproved=1', 'left');
        $this -> db -> where(array(
            'a.UserId' => $user_id,
            'a.RetailerId' => $retailer_id,
            'a.StoreId' => $store_id,
            'c.RetailerId' => $retailer_id,
            'c.StoreId' => $store_id
        ));
        $this -> db -> group_by("c.ProductId");

        if ($limit == 1)
            $this -> db -> limit($this -> config -> item('my_basket_limit'));

        $this -> db -> order_by('a.Id', 'Desc');
        $query = $this -> db -> get();
        $result_array = $query -> result_array();
        
        # Show only those products whose price is greater than zero.
        if ($result_array) {
            $i = 0;
            $finalResults = array();
            foreach ($result_array as $result_data) {
                if($result_data['Price'] > 0)
                {
                    $finalResults[$i]= $result_data;
                    
                    if ($result_data['ProductImage']) {
                        $finalResults[$i]['ProductImage'] = front_url() . PRODUCT_IMAGE_PATH . "medium/" . $result_data['ProductImage'];
                    }
                    else {
                        $finalResults[$i]['ProductImage'] = DEFAULT_PRODUCT_IMAGE_PATH;
                    }
                
                    $i++;
                }
            }
            return $finalResults;
        } 
        return FALSE;
    }
    
    
    public function get_user_basket_old($limit = 1, $user_id, $retailer_id, $store_type_id, $store_id) {
        $user_preference = $this -> get_user_preferred_retailer($user_id);

        $this -> db -> select("b.Id, b.ProductName, b.ProductImage, round(sum(case when e.SpecialPrice > 0 and e.SpecialQty > 0 then ((e.SpecialPrice/e.SpecialQty)*a.ProductCount) when e.SpecialPrice > 0 then e. SpecialPrice*a.ProductCount else c.Price*a.ProductCount end),2) as Price, a.ProductCount, a.Id as BasketId", false);
        $this -> db -> from('userbasket as a');
        $this -> db -> join('products as b', 'b.Id = a.ProductId');
        $this -> db -> join('storeproducts as c', 'b.Id = c.ProductId');
        $this -> db -> join('retailers as d', 'd.Id = c.RetailerId');
        $this -> db -> join('productspecials as e', 'e.ProductId = c.ProductId and e.RetailerId = d.Id and e.StoreTypeId = c.StoreTypeId and e.StoreId = c.StoreId AND DATE(e.PriceAppliedFrom) <= \'' . date('Y-m-d') . '\' AND DATE(e.PriceAppliedTo) >= \'' . date('Y-m-d') . '\' and e.IsActive=1 and e.IsApproved=1', 'left');
        $this -> db -> where(array(
            'a.UserId' => $user_id,
            'c.RetailerId' => $retailer_id,
            'c.StoreId' => $store_id
        ));
        $this -> db -> group_by("c.ProductId");

        if ($limit == 1)
            $this -> db -> limit($this -> config -> item('my_basket_limit'));

        $this -> db -> order_by('a.Id', 'Desc');
        $query = $this -> db -> get();
        $result_array = $query -> result_array();
        
        # Show only those products whose price is greater than zero.
        if ($result_array) {
            $i = 0;
            $finalResults = array();
            foreach ($result_array as $result_data) {
                if($result_data['Price'] > 0)
                {
                    $finalResults[$i]= $result_data;
                    
                    if ($result_data['ProductImage']) {
                        $finalResults[$i]['ProductImage'] = front_url() . PRODUCT_IMAGE_PATH . "medium/" . $result_data['ProductImage'];
                    }
                    else {
                        $finalResults[$i]['ProductImage'] = DEFAULT_PRODUCT_IMAGE_PATH;
                    }
                
                    $i++;
                }
            }
            return $finalResults;
        } 
        return FALSE;
    }

    public function get_user_basket_single($limit = 1, $user_id, $retailer_id, $store_type_id, $store_id, $product_id) {
        $user_preference = $this -> get_user_preferred_retailer($user_id);

        $this -> db -> select("b.Id, b.ProductName, b.ProductImage, round(sum(case when e.SpecialPrice > 0 and e.SpecialQty > 0 then ((e.SpecialPrice/e.SpecialQty)*a.ProductCount) when e.SpecialPrice > 0 then e. SpecialPrice*a.ProductCount else c.Price*a.ProductCount end),2) as Price, a.ProductCount, a.Id as BasketId", false);
        $this -> db -> from('userbasket as a');
        $this -> db -> join('products as b', 'b.Id = a.ProductId');
        $this -> db -> join('storeproducts as c', 'b.Id = c.ProductId');
        $this -> db -> join('retailers as d', 'd.Id = c.RetailerId');
        $this -> db -> join('productspecials as e', 'e.ProductId = c.ProductId and e.RetailerId = d.Id and e.StoreTypeId = c.StoreTypeId and e.StoreId = c.StoreId AND DATE(e.PriceAppliedFrom) <= \'' . date('Y-m-d') . '\' AND DATE(e.PriceAppliedTo) >= \'' . date('Y-m-d') . '\' and e.IsActive=1 and e.IsApproved=1', 'left');
        $this -> db -> where(array(
            'a.UserId' => $user_id,
            'c.RetailerId' => $retailer_id,
            'c.StoreId' => $store_id,
            'a.ProductId' => $product_id
        ));
        $this -> db -> group_by("c.ProductId");
        $this -> db -> order_by('a.Id', 'Desc');
        $query = $this -> db -> get();
        $result_array = $query -> result_array();
        
        if ($result_array) {
            $i = 0;
            foreach ($result_array as $result_data) {
                if ($result_data['ProductImage']) {
                    $result_array[$i]['ProductImage'] = front_url() . PRODUCT_IMAGE_PATH . "medium/" . $result_data['ProductImage'];
                }
                else {
                    $result_array[$i]['ProductImage'] = DEFAULT_PRODUCT_IMAGE_PATH;
                }
                $i++;
            }
            return $result_array;
        }
        return FALSE;
    }

    public function get_user_preferred_retailer($user_id) {
        $this -> db -> select('retailers.CompanyName, retailers.Id, retailers.LogoImage, userpreferredbrands.StoreId, stores.StoreName,stores.StoreTypeId');
        $this -> db -> from('userpreferredbrands');
        $this -> db -> join('retailers', ' retailers.Id = userpreferredbrands.RetailerId');
        $this -> db -> join('stores', ' stores.Id = userpreferredbrands.StoreId');

        $this -> db -> where(array(
            'UserId' => $user_id
        ));
        $this -> db -> limit(1);
        $query = $this -> db -> get();

        return $query -> row();
    }

    public function get_basket_min_details($user_id, $retailer_id, $store_type_id, $store_id) {
        $query = $this -> db -> query("
SELECT a.UserId,a.ProductId,case when a.ProductCount is not null and a.ProductCount > 0 then a.ProductCount else 0 end as total_count, 
case when f.SpecialPrice > 0 and f.SpecialQty > 0 then ((f.SpecialPrice/f.SpecialQty)*a.ProductCount) when f.SpecialPrice > 0 then f. SpecialPrice*a.ProductCount else c.Price*a.ProductCount end as price_sum 
FROM (`userbasket` as a) 
JOIN `products` as b ON `b`.`Id` = `a`.`ProductId` 
JOIN `storeproducts` as c ON `c`.`ProductId` = `b`.`Id` 
JOIN `retailers` as d ON `d`.`Id` = `c`.`RetailerId` 
JOIN `storestypes` as e ON `e`.`Id` = `c`.`StoreTypeId` 
LEFT JOIN `productspecials` as f ON `f`.`ProductId` = `a`.`ProductId` and f.RetailerId = c.RetailerId and f.StoreTypeId = c.StoreTypeId and f.StoreId = c.StoreId AND DATE(f.PriceAppliedFrom) <= '".date('Y-m-d')."' AND DATE(f.PriceAppliedTo) >= '".date('Y-m-d')."' and f.IsActive=1 and f.IsApproved=1 
WHERE `a`.`UserId` = $user_id AND `c`.`RetailerId` = $retailer_id AND `c`.`StoreId` = $store_id
AND `a`.`RetailerId` = $retailer_id AND `a`.`StoreId` = $store_id              
AND `a`.`IsActive` = 1 AND `a`.`IsRemoved` = 0
AND `b`.`IsActive` = 1 AND `b`.`IsRemoved` = 0
AND `c`.`IsActive` = 1 AND `c`.`IsRemoved` = 0
AND `d`.`IsActive` = 1 AND `d`.`IsRemoved` = 0
AND `e`.`IsActive` = 1 AND `e`.`IsRemoved` = 0
group by b.Id");
        
        if ($query -> num_rows() > 0) {
            $results = $query -> result_array();
            
            
            
            
            $res = array();
            $res['total_count'] =  0;
            $res['price_sum'] =  0;
            
            $index = 0; 
            foreach($results as $singleRow)
            {
                if($singleRow['price_sum'] > 0 )
                {
                    $res['total_count'] = $res['total_count'] + $singleRow['total_count'];
                    $res['price_sum']   = $res['price_sum'] + $singleRow['price_sum'];
                }
            }
            return $res;            
        }
        else {
            return FALSE;
        }
       
    }
    /* Function to get user basket data total for other retailers
     * return - array of user basket for other retailers details
     */

    public function get_user_basket_other_retailers($user_id, $retailer_id, $limit = 0, $price_order = '') {

        $this -> db -> _protect_identifiers = false;
        $this -> db -> select("d.LogoImage, sum(case when f.SpecialPrice > 0 and f.SpecialQty > 0 then ((f.SpecialPrice/f.SpecialQty)*a.ProductCount) when f.SpecialPrice > 0 then f. SpecialPrice*a.ProductCount else c.Price*a.ProductCount end) as price_sum,g.StoreName,d.CompanyName,(6371 * acos( cos( radians(h.PrefLatitude) ) * cos( radians( g.Latitude ) ) * cos( radians( g.Longitude ) - radians(h.PrefLongitude) ) + sin( radians(h.PrefLatitude) ) * sin( radians( g.Latitude ) ) ) ) as store_distance,h.PrefDistance", FALSE)
            -> from('userbasket as a')
            -> join('products as b', 'b.Id = a.ProductId')
            -> join('storeproducts as c', 'c.ProductId = b.Id')
            -> join('retailers as d', 'd.Id = c.RetailerId')
            -> join('storestypes as e', 'e.Id = c.StoreTypeId and e.IsActive = 1 and e.IsRemoved = 0')
            -> join('productspecials as f', 'f.ProductId = a.ProductId and f.RetailerId = c.RetailerId and f.StoreTypeId = c.StoreTypeId and f.StoreId = c.StoreId AND DATE(f.PriceAppliedFrom) <= "' . date('Y-m-d') . '" AND DATE(f.PriceAppliedTo) >= "' . date('Y-m-d') . '" and f.IsActive=1 and f.IsApproved=1', 'left')
            -> join('stores as g', 'g.Id = c.StoreId')
            -> join('users as h', 'h.Id = a.UserId')
            -> where('a.UserId', (int) $user_id)
            -> where('c.RetailerId != ' . $retailer_id)
            -> where('a.IsActive', 1)
            -> where('a.IsRemoved', 0)
            -> group_by("c.RetailerId, c.StoreId")
            -> having('price_sum > 0  and store_distance <= h.PrefDistance')
            -> limit(ALTERNATE_PRICE_LIMIT, $limit);

        if (trim($price_order == 'l-h')) {
            $this -> db -> order_by('price_sum', 'asc');
        }
        elseif (trim($price_order == 'h-l')) {
            $this -> db -> order_by('price_sum', 'desc');
        }
        $query = $this -> db -> get();

        //echo $this -> db -> last_query();die;

        return $query -> result_array();
    }

    public function get_user_basket_other_retailers_count($user_id, $retailer_id) {

        $this -> db -> _protect_identifiers = false;
        $this -> db -> select("d.LogoImage, sum(case when f.SpecialPrice > 0 and f.SpecialQty > 0 then ((f.SpecialPrice/f.SpecialQty)*a.ProductCount) when f.SpecialPrice > 0 then f. SpecialPrice*a.ProductCount else c.Price*a.ProductCount end) as price_sum,g.StoreName,d.CompanyName,(6371 * acos( cos( radians(h.PrefLatitude) ) * cos( radians( g.Latitude ) ) * cos( radians( g.Longitude ) - radians(h.PrefLongitude) ) + sin( radians(h.PrefLatitude) ) * sin( radians( g.Latitude ) ) ) ) as store_distance,h.PrefDistance", FALSE)
            -> from('userbasket as a')
            -> join('products as b', 'b.Id = a.ProductId')
            -> join('storeproducts as c', 'c.ProductId = b.Id')
            -> join('retailers as d', 'd.Id = c.RetailerId')
            -> join('storestypes as e', 'e.Id = c.StoreTypeId')
            -> join('productspecials as f', 'f.ProductId = a.ProductId and f.RetailerId = c.RetailerId and f.StoreTypeId = c.StoreTypeId and f.StoreId = c.StoreId AND DATE(f.PriceAppliedFrom) <= "' . date('Y-m-d') . '" AND DATE(f.PriceAppliedTo) >= "' . date('Y-m-d') . '" and f.IsActive=1 and f.IsApproved=1', 'left')
            -> join('stores as g', 'g.Id = c.StoreId')
            -> join('users as h', 'h.Id = a.UserId')
            -> where('a.UserId', $user_id)
            -> where('c.RetailerId != ' . $retailer_id)
            -> where('a.IsActive', 1)
            -> where('a.IsRemoved', 0)
            -> group_by("c.RetailerId, c.StoreId")
            -> having('price_sum > 0  and store_distance <= h.PrefDistance');


        $query = $this -> db -> get();



        return $query -> num_rows();
    }

    public function get_brand_list($user_id, $retailer_id, $storetype_id, $store_id, $category_id, $search_string = '', $parent_category) {

        $on_condition = '';
        $on_field = '';
        if ($parent_category === 3) {
            $on_condition = 'a.CategoryId = h.Id';
            $on_field = 'a.CategoryId';
        }
        if ($parent_category === 2) {
            $on_condition = 'a.ParentCategoryId = h.Id';
            $on_field = 'a.ParentCategoryId';
        }

        $this -> db -> select('a.Brand,count(case when a.Id is null then 0 else 1 end) as product_count,6371 * acos( cos( radians(g.PrefLatitude) ) * cos( radians( c.Latitude ) ) * cos( radians( c.Longitude ) - radians(g.PrefLongitude) ) + sin( radians(g.PrefLatitude) ) * sin( radians( c.Latitude ) ) ) as available_distance,g.PrefDistance as PrefDistance', FALSE)
            -> from('products as a')
            -> join('storeproducts as b', 'b.ProductId = a.Id')
            -> join('stores as c', 'c.Id = b.StoreId')
            -> join('storestypes as d', 'd.Id = b.StoreTypeId')
            -> join('retailers as e', 'e.Id = b.RetailerId')
            -> join('userpreferredbrands as f', 'f.RetailerId = b.RetailerId and f.StoreId = b.StoreId')
            -> join('users as g', 'g.Id = f.UserId')
            -> join('categories as h', $on_condition)
            -> where(
                array(
                    'a.IsActive' => 1,
                    'a.IsRemoved' => 0,
                    'b.RetailerId' => $retailer_id,
                    //'b.StoreTypeId' => $storetype_id,
                    'b.StoreId' => $store_id,
                    'g.Id' => $user_id,
                    $on_field => $category_id,
                    'c.IsActive' => 1,
                    'c.IsRemoved' => 0,
                    'd.IsActive' => 1,
                    'd.IsRemoved' => 0,
                    'e.IsActive' => 1,
                    'e.IsRemoved' => 0,
                    'g.IsActive' => 1,
                    'g.IsRemoved' => 0,
                    'h.IsActive' => 1,
                    'h.IsRemoved' => 0,
                )
            )
            -> group_by('a.Brand');
        //-> having('PrefDistance >= available_distance');
        if ($search_string != '') {
            $this -> db -> like('a.Brand', $search_string);
        }
        $query = $this -> db -> get();
//        echo $this -> db -> last_query();die;
        if ($query -> num_rows() > 0) {
            return $query -> result_array();
        }
        return FALSE;
    }

    public function get_parent_cat_id($category_id) {
        $this -> db -> select('a.ParentCategory as child, b.ParentCategory as Parent, c.ParentCategory as main')
            -> from('categories as a')
            -> join('categories as b', 'a.ParentCategory = b.Id')
            -> join('categories as c', 'b.ParentCategory = c.Id', 'left')
            -> where(array(
                'a.Id' => $category_id,
                'a.IsActive' => 1,
                'a.IsRemoved' => 0
            ));
        $query = $this -> db -> get();
        if ($query -> num_rows() > 0) {
            $res_array = $query -> row_array();
            if ($res_array['main'] === '0' && $res_array['main'] !== NULL && $res_array['main'] !== '') {
                return 3;
            }
            else {
                return 2;
            }
        }
        return FALSE;
    }

    public function get_special_terms($user_id, $retailer_id, $category_id) {
        $this -> db -> _protect_identifiers = false;
        $store_id = $this -> get_nearest_or_prefered_store($retailer_id);
        $this -> db -> select("case when group_concat(distinct i.TermsText) = '' then '' else group_concat(distinct i.TermsText SEPARATOR '. ') end as special_terms", FALSE)
            -> from('products as a')
            -> join('productsreviews as b', 'b.ProductId = a.Id', 'left')
            -> join('usersfavorite as c', 'c.ProductId = a.Id AND c.UserId=' . $user_id, 'left')
            -> join('userbasket as d', 'd.ProductId = a.Id AND d.UserId =' . $user_id, 'left')
            -> join('userspricealerts as e', 'e.ProductId = a.Id AND e.UserId =' . $user_id, 'left')
            -> join('storeproducts as f', 'f.ProductId = a.Id AND f.RetailerId =' . $retailer_id . ' AND (f.StoreId=' . $store_id . ' OR (f.StoreId=0 AND f.PriceForAllStores=1)) AND f.IsActive=1')
            -> join('productspecials as g', 'g.ProductId = a.Id AND DATE(g.PriceAppliedFrom) <= "' . date('Y-m-d') . '" AND DATE(g.PriceAppliedTo) >= "' . date('Y-m-d') . '" AND g.RetailerId =' . $retailer_id . ' AND (g.StoreId=' . $store_id . ' OR (g.StoreId=0 AND g.PriceForAllStores=1)) AND g.IsActive=1 AND g.IsApproved =1')
            -> join('specials as h', 'h.Id = g.SpecialId')
            -> join('special_terms as i', 'FIND_IN_SET (i.Id,h.TermsAndConditions)')
            -> where('a.IsActive', 1)
            //-> where('(a.CategoryId ='.$category_id.' or a.ParentCategoryId ='.$category_id.')', NULL, FALSE)
            -> group_by('a.Id')
            -> order_by('g.Id', 'DESC')
            -> limit(1);
        if ($category_id) {
            $this -> db -> where("(a.CategoryId =$category_id or a.ParentCategoryId =$category_id)", NULL, FALSE);
        }
        $query = $this -> db -> get();
        //echo $this->db->last_query();exit;
        if ($query -> num_rows() > 0) {
            $res_array = $query -> row_array();
            return $res_array['special_terms'];
        }
        else {
            return "";
        }
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

        $this -> db -> last_query();

        $result = $query -> row_array();

        $store_id = $result['Id'];

        return $store_id;
    }

    public function update_basket($user_id, $basket_id, $product_count) {
        $this -> db -> where('Id', $basket_id);
        $update_data = array(
            'ProductCount' => $product_count,
            'ModifiedOn' => date('Y-m-d H:i:s'),
            'ModifiedBy' => $user_id
        );
        if ($this -> db -> update('userbasket', $update_data)) {
            return TRUE;
        }
        return FALSE;
    }

    public function get_one_product_count($user_id, $product_id, $special_id) {
        $this -> db -> select('ProductCount')
            -> from('userbasket')
            -> where(
                array(
                    'SpecialId' => $special_id,
                    'ProductId' => $product_id,
                    'UserId' => $user_id
                )
        );
        $query = $this -> db -> get();
        if ($query -> num_rows() > 0) {
            $result_array = $query -> row_array();
            return $result_array['ProductCount'];
        }
        return '0';
    }

    public function remove_user_basket($user_id) {
        $where = array(
            'UserId' => $user_id
        );
        if ($this -> db -> delete('userbasket', $where)) {
            return TRUE;
        }
        return FALSE;
    }
    
    
    public function get_special_terms_and_conditions($user_id, $retailer_id, $category_id,$store_id) {
        $this -> db -> _protect_identifiers = false;
        //$store_id = $this -> get_nearest_or_prefered_store($retailer_id);
        $this -> db -> select("case when group_concat(distinct i.TermsText) = '' then '' else group_concat(distinct i.TermsText SEPARATOR '. ') end as special_terms", FALSE)
            -> from('products as a')            
            -> join('productspecials as g', 'g.ProductId = a.Id AND DATE(g.PriceAppliedFrom) <= "' . date('Y-m-d') . '" AND DATE(g.PriceAppliedTo) >= "' . date('Y-m-d') . '" AND g.RetailerId =' . $retailer_id . ' AND (g.StoreId=' . $store_id . ' OR (g.StoreId=0 AND g.PriceForAllStores=1)) AND g.IsActive=1 AND g.IsApproved =1')
            -> join('specials as h', 'h.Id = g.SpecialId')
            -> join('special_terms as i', 'FIND_IN_SET (i.Id,h.TermsAndConditions)')
            -> where('a.IsActive', 1)
            //-> where('(a.CategoryId ='.$category_id.' or a.ParentCategoryId ='.$category_id.')', NULL, FALSE)
            -> group_by('a.Id')
            -> order_by('g.Id', 'DESC')
            -> limit(1);
        if ($category_id) {
            $this -> db -> where("(a.CategoryId =$category_id or a.ParentCategoryId =$category_id)", NULL, FALSE);
        }
        $query = $this -> db -> get();
        
        //echo $this->db->last_query();exit;
        
        if ($query -> num_rows() > 0) {
            $res_array = $query -> row_array();
            return $res_array['special_terms'];
        }
        else {
            return "";
        }
    }
    
    /* Get Terms and Condition for specials */
    public function get_special_tandc($special_id) {
        $this -> db -> _protect_identifiers = false;
        $this -> db -> select("case when group_concat(distinct b.TermsText) = '' then '' else group_concat(distinct b.TermsText SEPARATOR '. ') end as special_terms", FALSE)
            -> from('specials as a')
            -> join('special_terms as b', 'FIND_IN_SET (b.Id,a.TermsAndConditions)')
            -> where('a.IsActive', 1)
            -> group_by('a.Id')
            -> order_by('b.Id', 'DESC')
            -> limit(1);
        $this -> db -> where("a.id", $special_id, FALSE);
        $query = $this -> db -> get();
        
        if ($query -> num_rows() > 0) {
            $res_array = $query -> row_array();
            return $res_array['special_terms'];
        }else {
            return "";
        }
    }
    
    
    /* Function to get all products count from another Retailer*/
    public function get_basket_product_count_from_another_retailer($user_id,$retailer_id,$store_id) {
        $this -> db -> select('case when sum(ProductCount) is not null then sum(ProductCount) else 0 end as ProductCounts',FALSE)
            -> from('userbasket')
            -> where(
                array(                                 
                    'UserId' => $user_id
                )
        );
        //$this -> db -> where("RetailerId <> ", $retailer_id, FALSE);
        $this -> db -> where("StoreId <> ", $store_id, FALSE);
        $query = $this -> db -> get();
        
        //echo $this -> db -> last_query();die;
        if ($query -> num_rows() > 0) {
            $result_array = $query -> row_array();
            return $result_array['ProductCounts'];
        }
        return '0';
    }
    
    
    public function get_basket_product_count($user_id, $product_id,$special_id,$retailer_id,$store_id) {
        $this -> db -> select('ProductCount')
            -> from('userbasket')
            -> where(
                array(
                    'SpecialId' => $special_id,
                    'ProductId' => $product_id,
                    'UserId' => $user_id,
                    'RetailerId' => $retailer_id
                )
        );
        $query = $this -> db -> get();
        if ($query -> num_rows() > 0) {
            $result_array = $query -> row_array();
            return $result_array['ProductCount'];
        }
        return '0';
    }
    
    
    /* Function to get Store information */
    public function get_store_details($store_id) {
        $this -> db -> select('a.Id,a.StoreTypeId,a.StoreName', FALSE);
        $this -> db -> where('a.Id', $store_id);
        $query = $this -> db -> get('stores as a');
        
        //echo $this -> db -> last_query();die;
        return $query -> row_array();
    }
    
    
    /* Add to basket
     *  param - product_id : Id of product to add to basket
     *  return - Id of inserted record
     */

    public function add_product_to_basket($special_id, $product_id, $user_id, $product_count,$retailer_id,$store_id) {

        $ins_arr = array(
            'SpecialId' => $special_id,
            'ProductId' => $product_id,
            'UserId' => $user_id,
            'productCount' => $product_count,
            'RetailerId' => $retailer_id,
            'StoreId' => $store_id
        );

        $this -> db -> select("Id");
        $this -> db -> from("userbasket");
        $this -> db -> where(
            array(
                'SpecialId' => $special_id,
                'ProductId' => $product_id,
                'UserId' => $user_id,
                'RetailerId' => $retailer_id,
                'StoreId' => $store_id
            )
        );
        $query = $this -> db -> get();

        if ($query -> num_rows() > 0) {
            $where = array(
                'SpecialId' => $special_id,
                'ProductId' => $product_id,
                'UserId' => $user_id,
                'RetailerId' => $retailer_id,
                'StoreId' => $store_id
            );
            $update_data = array(
                'productCount' => $product_count
            );
            $this -> db -> where($where);
            $this -> db -> update('userbasket', $update_data);
            return '1';
            //return 'duplicate';
        }
        else {
            $ins_arr = array(
                'SpecialId' => $special_id,
                'ProductId' => $product_id,
                'productCount' => $product_count,
                'RetailerId' => $retailer_id,
                'StoreId' => $store_id,
                'UserId' => $user_id,
                'CreatedBy' => $user_id,
                'CreatedOn' => date("Y/m/d H:i:s")
            );
            $this -> db -> insert('userbasket', $ins_arr);
            return $this -> db -> insert_id();
        }
    }
    
    
    /* Function to get Store information */
    public function get_store_retailer_details($store_id) {
        
        $this -> db -> select('a.Id,a.StoreTypeId,a.StoreName,b.CompanyName as retailerName', FALSE);
        $this -> db -> join('retailers as b', 'b.Id = a.RetailerId');
        $this -> db -> where('a.Id', $store_id);
        $query = $this -> db -> get('stores as a');
        return $query -> row_array();
		
    }
    
}