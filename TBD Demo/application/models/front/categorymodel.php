<?php

/*
 * Author:  PM
 * Purpose: Category related functions
 * Date:    08-10-2015
 */

class Categorymodel extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    //Get active not deleted parent categories that contain active not deleted products
    public function get_category_having_products() {
        $this -> db -> select('main_parent_category.CategoryName, main_parent_category.Id as main_category_id, parent_category.CategoryName as parent_category, parent_category.Id as parent_category_id, sub_category.CategoryName as sub_category, sub_category.Id as sub_category_id');
        $this -> db -> from('products');
        $this -> db -> join('categories sub_category', 'sub_category.Id = products.CategoryId', 'left');
        $this -> db -> join('categories parent_category', 'parent_category.Id = products.ParentCategoryId', 'left');
        $this -> db -> join('categories main_parent_category', 'main_parent_category.Id = products.MainCategoryId', 'left');
        $this -> db -> where(array(
            'main_parent_category.ParentCategory' => 0,
            'main_parent_category.IsActive' => 1,
            'main_parent_category.IsRemoved' => 0,
            'products.IsActive' => 1,
            'products.IsRemoved' => 0,
        ));
        $this -> db -> group_by('main_parent_category.Id, parent_category.Id, sub_category.Id');
        $this -> db -> order_by('main_parent_category.CategoryName');
        $query = $this -> db -> get();

        return $query -> result_array();
    }
    /* Function to get category details
     * Param - int: category_id
     * Return - array containing category details
     */

    public function get_category_details($category_id, $category_type = '') {
        if ($category_type == 'sub') {
            $this -> db -> select("parent_cat.CategoryName as parent_cat_name, parent_cat.Id as parent_cat_id, main_cat.CategoryName as main_cat_name, main_cat.Id as main_cat_id, sub_cat.CategoryName as sub_cat_name,sub_cat.Id as sub_cat_id");
            $this -> db -> from("categories as main_cat");
            $this -> db -> join("categories as sub_cat","sub_cat.ParentCategory = main_cat.Id");
            $this -> db -> join("categories as parent_cat","main_cat.ParentCategory = parent_cat.Id");
            $this -> db -> where(array('sub_cat.Id' => $category_id));
            $this -> db -> limit('1');
            $query = $this -> db -> get();
        }
        else {
            $this -> db -> select("sub_cat.CategoryName,sub_cat.CategoryName as CategoryId, main_cat.CategoryName as main_cat, main_cat.Id as main_cat_id");
            $this -> db -> from("categories as main_cat");
            $this -> db -> join("categories as sub_cat","sub_cat.ParentCategory = main_cat.Id");
            $this -> db -> where(array('sub_cat.Id' => $category_id));
            $this -> db -> limit('1');
            $query = $this -> db -> get();
        }


        return $query -> row_array();
    }
}

?>