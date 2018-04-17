<?php

/*
 * Author: Name:PM
 * Purpose: Model for controlling database interactions regarding the content.
 * Date:04-09-2015
 * Dependency: None
 */

class categorymodel extends CI_Model {
    /*
     * Purpose: Constructor.
     * Date: 04-09-2015
     * Input Parameter: None
     * Output Parameter: None
     */

    public function __construct() {
        parent::__construct();
    }

    public function get_all_categories($category_id,$group_id=0,$level=0) {
        
        
        //$this -> db -> select('c.Id,c.CategoryName,c.ParentCategory,c.CategoryIcon,c.IsActive');
        
        // Added on 18 May 2017
        $this -> db -> select('c.Id,c.CategoryName,c.ParentCategory,c.CategoryIcon,c.IsActive,ChildCategoryCount');

       /*
        if(!$category_id)
        {
          $this -> db -> join('categories_storegroups AS csg', 'c.Id = csg.CategoryId');  
        }
        */
        
        
        
         // Added on 20 April 2017 : Need to check this query.
        if($category_id)
        {
            if ($level == '2') {
                $this -> db -> join('categories_storegroups AS csg', 'c.ParentCategory = csg.CategoryId');  
            }
        }else{
          $this -> db -> join('categories_storegroups AS csg', 'c.Id = csg.CategoryId');    
        }
        
        
        
        $this -> db -> where(
            array(
                'c.IsRemoved' => 0,
                'c.IsActive' => 1
        ));

        if ($category_id) {
            $this -> db -> where('c.ParentCategory', $category_id);
            
            
            // Added on 20 April 2017 
            if ($level == '2') {
                if($group_id)
                {
                    $this -> db -> where('csg.StoreGroupId', $group_id);
                }
            }
             
            
        }
        else {
            $this -> db -> where('c.ParentCategory', 0);
            if($group_id)
            {
                $this -> db -> where('csg.StoreGroupId', $group_id);
            }
        }
        $this -> db -> order_by('c.CategoryName');

        $query = $this -> db -> get('categories as c');
        //echo $this->db->last_query();exit;
        
        return $query -> result_array();
    }
    
    
    public function get_all_categories_WIP($category_id,$group_id=0,$retailer_id, $store_id, $level) {
        
        $categoryImgPath = front_url() . CATEGORY_IMAGE_PATH . 'large/';
        
        if ($level == '3') {
            $category_field = 'products.CategoryId';
        }
        else {
            $category_field = 'products.ParentCategoryId';
        }
        
        $this -> db -> select('c.Id,c.CategoryName,c.ParentCategory,c.CategoryIcon,c.ChildCategoryCount as child_cat_count,c.IsActive',FALSE);
        
        //$this -> db -> select("c.Id,c.CategoryName,c.ParentCategory,case when c.CategoryIcon is null then \"\" else $categoryImgPath"."c.CategoryIcon end as CategoryIcon,ChildCategoryCount,c.IsActive",FALSE);
        if($category_id)
        {
            /*
            $subquery1 ='( 
                    SELECT count(storeproducts.ID) as product_count 
                    FROM products
                    JOIN storeproducts ON `storeproducts`.`ProductId` = `products`.`Id` 
                    AND storeproducts.RetailerId ='.$retailer_id.'
                    AND (storeproducts.StoreId='.$store_id.' OR (storeproducts.StoreId=0 AND storeproducts.PriceForAllStores=1)) 
                    AND storeproducts.IsActive=1    
                    WHERE `products`.`IsActive` =  1
                    AND `products`.`IsRemoved` =  0
                    AND '.$category_field.' =  `c`.`Id`        
                ) as product_count';  
            */
            
            /*
            $subquery1 ='( 
                    SELECT count(storeproducts.ID) as product_count 
                    FROM products, storeproducts                    
                    WHERE `products`.`IsActive` =  1
                    AND `products`.`IsRemoved` =  0
                    AND '.$category_field.' =  `c`.`Id`
                    AND `storeproducts`.`ProductId` = `products`.`Id` 
                    AND storeproducts.RetailerId ='.$retailer_id.'
                    AND (storeproducts.StoreId='.$store_id.' OR (storeproducts.StoreId=0 AND storeproducts.PriceForAllStores=1)) 
                    AND storeproducts.IsActive=1        
                ) as product_count'; 
            */
            
            // SELECT count(storeproducts.ID) as product_count 
            $subquery1 ='( 
                    SELECT COUNT(DISTINCT storeproducts.ProductId) as product_count 
                    FROM products, storeproducts                    
                    WHERE `products`.`IsActive` =  1
                    AND `products`.`IsRemoved` =  0
                    AND '.$category_field.' =  `c`.`Id`
                    AND `storeproducts`.`ProductId` = `products`.`Id` 
                    AND storeproducts.RetailerId ='.$retailer_id.'
                    AND storeproducts.StoreId='.$store_id.'                    
                    AND storeproducts.IsActive=1        
                ) as product_count'; 
            
            $this->db->select($subquery1);
        }
        
        $subquery2 ='( 
                SELECT Count(Id) as child_count
                FROM (`categories`)
                WHERE `IsRemoved` =  0
                AND `IsActive` =  1
                AND `ParentCategory` =  `c`.`Id`
            ) as child_cat_count';       
        //$this->db->select($subquery2);
        
        if(!$category_id)
        {
          $this -> db -> join('categories_storegroups AS csg', 'c.Id = csg.CategoryId');  
        }
        
        $this -> db -> where(
            array(
                'c.IsRemoved' => 0,
                'c.IsActive' => 1
        ));

        if ($category_id) {
            $this -> db -> where('c.ParentCategory', $category_id);
        }
        else {
            $this -> db -> where('c.ParentCategory', 0);
            if($group_id)
            {
                $this -> db -> where('csg.StoreGroupId', $group_id);
            }
        }
        $this -> db -> order_by('c.CategoryName');

        $query = $this -> db -> get('categories as c');
        //echo $this->db->last_query();exit;
        
        return $query -> result_array();
    }
    
    public function get_all_categories_old_31March2017($category_id) {
        $this -> db -> select('Id,CategoryName,ParentCategory,CategoryIcon,IsActive');

        $this -> db -> where(
            array(
                'IsRemoved' => 0,
                'IsActive' => 1
        ));

        if ($category_id) {
            $this -> db -> where('ParentCategory', $category_id);
        }
        else {
            $this -> db -> where('ParentCategory', 0);
        }
        $this -> db -> order_by('CategoryName');

        $query = $this -> db -> get('categories');

        return $query -> result_array();
    }

    public function get_category_with_product_count($category_id, $retailer_id, $store_id, $level) {

        $this -> db -> select('storeproducts.Id');
        $this -> db -> join('storeproducts', 'storeproducts.ProductId = products.Id AND storeproducts.RetailerId =' . $retailer_id . " AND (storeproducts.StoreId=" . $store_id . " OR (storeproducts.StoreId=0 AND storeproducts.PriceForAllStores=1)) AND storeproducts.IsActive=1");

        $this -> db -> where(array(
            'products.IsActive' => 1,
            'products.IsRemoved' => 0
        ));


        $this -> db -> group_by('products.Id');

        if ($level == '3') {
            $category_field = 'products.CategoryId';
        }
        else {
            $category_field = 'products.ParentCategoryId';
        }

        if ($category_id) {
            $this -> db -> where($category_field, $category_id);
        }


        $query = $this -> db -> get('products');
        
       // echo $this -> db -> last_query();exit;

        return $query -> num_rows();
    }
    
    public function get_category_with_product_count_WIP($category_id, $retailer_id, $store_id, $level) {

        $this -> db -> select('storeproducts.Id');
        $this->db->from('products, storeproducts');
        //$this -> db -> join('storeproducts', 'storeproducts.ProductId = products.Id AND storeproducts.RetailerId =' . $retailer_id . " AND (storeproducts.StoreId=" . $store_id . " OR (storeproducts.StoreId=0 AND storeproducts.PriceForAllStores=1)) AND storeproducts.IsActive=1");

        $this -> db -> where(array(
            'products.IsActive' => 1,
            'products.IsRemoved' => 0
        ));
        $cond1 ="storeproducts.ProductId = products.Id";
        $this -> db -> where($cond1);
        
        $this -> db -> where(array(
            'storeproducts.RetailerId' => $retailer_id,            
            'storeproducts.IsActive'=> 1
        ));
        
        $cond2 = "(storeproducts.StoreId=" . $store_id . " OR (storeproducts.StoreId=0 AND storeproducts.PriceForAllStores=1))";
        $this -> db -> where($cond2);

        $this -> db -> group_by('products.Id');

        if ($level == '3') {
            $category_field = 'products.CategoryId';
        }
        else {
            $category_field = 'products.ParentCategoryId';
        }

        if ($category_id) {
            $this -> db -> where($category_field, $category_id);
        }

        $query = $this -> db -> get();
        //$query = $this -> db -> get('products');
        
        //echo $this -> db -> last_query();exit;

        if ($query -> num_rows() > 0) {
            return $query -> num_rows();
        }else{
            return 0;
        }
        
    }
    
    
    public function get_category_and_product_count_WIP($category_id, $retailer_id, $store_id, $level) {

        $this -> db -> select('storeproducts.Id');
        //$this -> db -> select('count(*) as product_count');
        $this->db->from('products, storeproducts');
        //$this -> db -> join('storeproducts', 'storeproducts.ProductId = products.Id AND storeproducts.RetailerId =' . $retailer_id . " AND (storeproducts.StoreId=" . $store_id . " OR (storeproducts.StoreId=0 AND storeproducts.PriceForAllStores=1)) AND storeproducts.IsActive=1");

        $this -> db -> where(array(
            'products.IsActive' => 1,
            'products.IsRemoved' => 0
        ));
        $cond1 ="storeproducts.ProductId = products.Id";
        $this -> db -> where($cond1);
        
        $this -> db -> where(array(
            'storeproducts.RetailerId' => $retailer_id,            
            'storeproducts.IsActive'=> 1
        ));
        
        /*
        $cond2 = "(storeproducts.StoreId=" . $store_id . " OR (storeproducts.StoreId=0 AND storeproducts.PriceForAllStores=1))";
        $this -> db -> where($cond2);
        */
        
        $this -> db -> where("storeproducts.StoreId", $store_id);

        $this -> db -> group_by('products.Id');

        if ($level == '3') {
            $category_field = 'products.CategoryId';
        }
        else {
            $category_field = 'products.ParentCategoryId';
        }

        if ($category_id) {
            $this -> db -> where($category_field, $category_id);
        }

        $query = $this -> db -> get();
        //$query = $this -> db -> get('products');
        
        //echo $this -> db -> last_query();exit;

        if ($query -> num_rows() > 0) {
            return $query -> num_rows();
        }else{
            return 0;
        }
        
    }
    

    public function get_child_category_count($category_id) {

        $this -> db -> select('Count(Id) as child_count');

        $this -> db -> where(
            array(
                'IsRemoved' => 0,
                'IsActive' => 1
        ));

        if ($category_id) {
            $this -> db -> where('ParentCategory', $category_id);
        }


        $query = $this -> db -> get('categories');
        //echo $this->db->last_query();exit;
        $result = $query -> row_array();

        return $result['child_count'];
    }
}

?>