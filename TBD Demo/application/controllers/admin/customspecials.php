<?php

/*
 * Author:AS
 * Purpose:Store Catalogue Controller - Store format user login
 * Date:27-10-2015
 * Dependency: specialmanagement.php
 */
//error_reporting(E_ALL);
//ini_set('display_errors', 1);

class Customspecials extends My_Controller {

    private $result;
    private $message;

    function __construct() {
        parent::__construct();

        $this -> load -> model('admin/storemodel', '', TRUE);
        $this -> load -> model('admin/retailermodel', '', TRUE);

        $this -> load -> model('admin/storeformatmodel', '', TRUE);
        $this -> load -> model('admin/specialproductmodel', '', TRUE);

        $this -> page_title = "Add New Special";
        if ($this -> session -> userdata('user_type') == 6) {
            $this -> check_wizard_navigation();
        }
    }

    public function index($id = '') {        
        $data['title'] = $this -> page_title;
        $this -> breadcrumbs[0] = array('label' => 'Manage Your Specials', 'url' => '');
        $this -> breadcrumbs[1] = array('label' => $this -> page_title, 'url' => '');
        $data['breadcrumbs'] = $this -> breadcrumbs;
        $data['specials'] = $this -> specialproductmodel -> get_special_details();
        $this -> template -> view('admin/custom_special/index', $data);
    }

    public function add_special_new() {
        if ($this -> input -> server('REQUEST_METHOD') == 'POST') {
            if (!$this -> input -> post('special_name_sel')) {
                $this -> form_validation -> set_rules('special_name', 'Special Name', 'trim|required|xss_clean');
                $this -> form_validation -> set_rules('price_from', 'price from', 'trim|required|valid_serverdate|xss_clean');
                $this -> form_validation -> set_rules('price_to', 'price to', 'trim|required|valid_serverdate|xss_clean');
            }
            else {
                $this -> form_validation -> set_rules('special_name_sel', 'Special', 'trim|required|numeric|xss_clean');
            }


            if (!$this -> form_validation -> run() == FALSE) {
                $special_sel = $this -> input -> post('special_name_sel');
                $special_name = $this -> input -> post('special_name');
                $special_from = $this -> input -> post('price_from');
                $special_to = $this -> input -> post('price_to');
                $special_terms = $this -> input -> post('special_terms');

                if (!$this -> input -> post('special_name_sel')) {
                    $file_uploaded = TRUE;
                    $image_name = '';
                    if (!$_FILES['spbanner_image']['size'] == 0) {
                        $result = $this -> do_upload('spbanner_image', 'specials', $this -> input -> post('image-x'), $this -> input -> post('image-y'), $this -> input -> post('image-width'), $this -> input -> post('image-height'));
                        if (isset($result['error'])) {
                            $file_uploaded = FALSE;
                        }
                        else {
                            $image_name = $result['upload_data']['file_name'];
                        }
                    }
                    if ($file_uploaded) {
                        $insert_data = array(
                            'SpecialName' => $special_name,
                            'SpecialBanner' => $image_name,
                            'SpecialFrom' => $special_from,
                            'SpecialTo' => $special_to,
                            'TermsAndConditions' => $special_terms
                        );
                        $isInsert = $this -> specialproductmodel -> insert_special_data($insert_data);
                        if ($isInsert) {
                            $this -> session -> set_userdata('special_sel', $isInsert);
                            // $this -> session -> set_userdata('success_message', 'Special added successfully');
                            $this -> result = 1;
                            $this -> message = 'Special added successfully';
                        }
                        else {
                            $this -> session -> set_userdata('error_message', 'Failed to add special');
                            $this -> result = 0;
                            $this -> message = 'Failed to add special';
                        }
                    }
                    else {
                        $this -> session -> set_userdata('error_message', 'Failed to upload the banner');
                        $this -> result = 0;
                        $this -> message = 'Failed to upload the banner';
                    }
                }
                else {
                    $this -> session -> set_userdata('special_sel', $special_sel);
                    $this -> result = 1;
                }
//                $this -> session -> set_userdata('special_sel', $special_sel);
//                $this -> result = 1;
            }
            else {
                $this -> result = 0;
                $this -> message = 'There are errors in the form. Please clear them and submit again';
            }
        }
        else {
            $this -> result = 0;
            $this -> message = 'Invalid request';
        }
        echo json_encode(array(
            'result' => $this -> result,
            'message' => $this -> message
        ));
    }

    function validate_name($name) {
        $this -> form_validation -> set_message('validate_name', 'Name must contain contain only letters, apostrophe, spaces or dashes.');
        if (preg_match("/^[a-zA-Z0-9'\-\s]+$/", $name)) {
            return true;
        }
        else {
            return false;
        }
    }
    /* public function datatable() {

      $selected_states = $this -> specialproductmodel -> get_selected_states($this -> session -> userdata('special_sel'));
      $selected_stores = $this -> specialproductmodel -> get_selected_stores($this -> session -> userdata('special_sel'));

      $all_states = FALSE;
      $all_stores = FALSE;
      $selected_state_list = '';
      $selected_store_list = '';
      if ($selected_states) {
      if (sizeof($selected_states) == 1) {
      if ($selected_states[0]['AllStates'] == 1) {
      $all_states = TRUE;
      }
      else {
      $selected_state_list[] = $selected_states[0]['StateId'];
      }
      }
      else {
      foreach ($selected_states as $sel_state) {
      $selected_state_list[] = $sel_state['StateId'];
      }
      }
      }
      if ($selected_stores) {
      if (sizeof($selected_stores) == 1) {
      if ($selected_stores[0]['AllStores'] == 1) {
      $all_stores = TRUE;
      }
      else {
      $selected_store_list[] = $selected_stores[0]['StoreId'];
      }
      }
      else {
      foreach ($selected_stores as $sel_store) {
      $selected_store_list[] = $sel_store['StoreId'];
      }
      }
      }

      $retailer_id = '';
      if ($this -> session -> userdata('user_type') >= 3) {
      $retailer_id = $this -> session -> userdata('user_retailer_id');
      }

      $this -> datatables -> select("a.Id as p_id,g.CategoryName as MainCategory,h.CategoryName as ParentCategory,i.CategoryName as Category,b.ProductName,d.StoreName,a.price as RRP,case when f.SpecialPrice IS NULL then concat('<input class=\"prod_prc\" style=\"width:65px\" type=\"text\" id=\"product_price_',a.Id, '\" value=\"0.00\" />') else concat('<input class=\"prod_prc\" style=\"width:65px\" type=\"text\" id=\"product_price_',a.Id,'\" value=\"',f.SpecialPrice,'\" />') end as SpecialPrice,
      case
      when f.SpecialQty IS NULL then " . specials_get_select(0) . "
      when f.SpecialQty = 1 then " . specials_get_select(1) . "
      when f.SpecialQty = 2 then " . specials_get_select(2) . "
      when f.SpecialQty = 3 then " . specials_get_select(3) . "
      when f.SpecialQty = 4 then " . specials_get_select(4) . "
      when f.SpecialQty = 5 then " . specials_get_select(5) . "
      when f.SpecialQty = 6 then " . specials_get_select(6) . "
      when f.SpecialQty = 7 then " . specials_get_select(7) . "
      when f.SpecialQty = 8 then " . specials_get_select(8) . "
      when f.SpecialQty = 9 then " . specials_get_select(9) . "
      when f.SpecialQty = 10 then " . specials_get_select(10) . "
      when f.SpecialQty > 10 then " . specials_get_select(11) . " end
      as SpecialQty,
      DATE_FORMAT(f.PriceAppliedFrom,'%d/%c/%Y') as PriceAppliedFrom,DATE_FORMAT(f.PriceAppliedTo,'%d/%c/%Y') as PriceAppliedTo", false)
      -> from('storeproducts as a')
      -> join('products as b', 'b.Id = a.ProductId')
      -> join('retailers as c', 'c.Id = a.RetailerId')
      -> join('stores as d', 'd.Id = a.StoreId')
      -> join('storestypes as e', 'e.Id = a.StoreTypeId')
      -> join('productspecials as f', 'f.ProductId = b.Id and f.RetailerId = c.Id and f.StoreId = d.Id and f.StoreTypeId = e.Id and (now() between f.PriceAppliedFrom and f.PriceAppliedTo) and f.SpecialId !='.$this -> session -> userdata('special_sel').' and f.ProductId is null', 'left')
      -> join('categories as g', 'g.Id = b.MainCategoryId', 'left')
      -> join('categories as h', 'h.Id = b.ParentCategoryId', 'left')
      -> join('categories as i', 'i.Id = b.CategoryId', 'left')
      -> where('(b.HouseId is null or b.HouseId = ' . $retailer_id . ' )')
      //-> where('a.StoreTypeId', $this -> session -> userdata('user_store_format_id'))
      -> add_column('selectVal', '<input type="checkbox"  name="store_products[]" value="$1" />', 'p_id');

      if ($this -> session -> userdata('user_type') == 3) {
      $this -> datatables -> where('a.RetailerId', $this -> session -> userdata('user_retailer_id'));
      }
      if ($this -> session -> userdata('user_type') == 5) {
      $this -> datatables -> where('a.StoreTypeId', $this -> session -> userdata('user_store_format_id'));
      }
      if ($this -> session -> userdata('user_type') == 6) {
      $this -> datatables -> where('a.StoreId', $this -> session -> userdata('user_store_id'));
      }
      $state_where = '';
      if(!$all_states && !empty($selected_state_list)){
      $state_where .= '(';
      $ct_cnt = 0;
      foreach ($selected_state_list as $state){
      if($ct_cnt == 0){
      $state_where .= ' d.StateId = '.$state.' ';
      }
      else{
      $state_where .= ' OR d.StateId = '.$state.' ';
      }
      $ct_cnt++;
      }
      $state_where .= ')';
      }
      $store_where = '';
      if(!$all_stores && !empty($selected_store_list)){
      $store_where .= '(';
      $ct_cnt = 0;
      foreach ($selected_store_list as $store){
      if($ct_cnt == 0){
      $store_where .= ' d.StoreId='.$store.' ';
      }
      else{
      $store_where .= ' OR d.StoreId='.$store.' ';
      }
      $ct_cnt++;
      }
      $store_where .= ')';
      }
      if($state_where){
      $this -> datatables -> where($state_where);
      }
      if($store_where){
      if ($this -> session -> userdata('user_type') == 6) {
      $this -> datatables -> or_where($store_where);
      }
      else{
      $this -> datatables -> where($store_where);
      }

      }
      //        if(!empty($selected_state_list)){
      //            $qr_str = implode(' StateId= ',$selected_state_list);
      //            echo $qr_str;die;
      //            $this -> datatables -> where('StateId', $selected_state_list);
      //        }
      //        if(!empty($selected_store_list)){
      //            $this -> datatables -> where_in('Id', $selected_store_list);
      //        }

      echo $this -> datatables -> generate();
      } */

    public function datatable($homebrand=0) {

        # Set default values 
        $productNameCond = $categoryNameCond = $parentCategoryNameCond = $mainCategoryNameCond = $act_RRP_Cond = "";
        
        # Get the text to be searched 
        $requestData        = $_REQUEST;
        $searchText         = $requestData['sSearch'];
        
        # Separate each word and exclude spaces 
        $tempSearchTextArr  = explode(' ',$searchText);
        $searchTextArr = array();
        foreach ($tempSearchTextArr as $row) {
            if (trim($row) != "")
            {
                $searchTextArr[] = $row;
            }
        }
        $searchWordsCounter = count($searchTextArr); 
        
        /*
        # Search by productName, categoryName, parentCategoryName and mainCategoryName        
        if($searchText)
        {
            $index=1;
            $searchCond ="(";
            foreach($searchTextArr as $single)
            {
                # Product Name search 
                $productNameCond = $productNameCond . " a.ProductName LIKE '%".$single."%' ";
                if($index < $searchWordsCounter){
                    $productNameCond = $productNameCond. " or";
                }

                # Category Name search 
                if($index  == 1 ){
                    $categoryNameCond = $categoryNameCond. " or";
                }
                $categoryNameCond = $categoryNameCond . " d.CategoryName LIKE '%".$single."%' ";
                if($index < $searchWordsCounter){
                    $categoryNameCond = $categoryNameCond. " or";
                }

                # Parent Category Name search 
                if($index  == 1 ){
                    $parentCategoryNameCond = $parentCategoryNameCond. " or";
                }
                $parentCategoryNameCond = $parentCategoryNameCond . " c.CategoryName LIKE '%".$single."%' ";
                if($index < $searchWordsCounter){
                    $parentCategoryNameCond = $parentCategoryNameCond. " or";
                }

                # Main Category Name search 
                if($index  == 1 ){
                    $mainCategoryNameCond = $mainCategoryNameCond. " or";
                }
                $mainCategoryNameCond = $mainCategoryNameCond . " b.CategoryName LIKE '%".$single."%' ";
                if($index < $searchWordsCounter){
                    $mainCategoryNameCond = $mainCategoryNameCond. " or";
                }
                
                
                # $act_RRP_Cond
                if($index  == 1 ){
                    $act_RRP_Cond = $act_RRP_Cond. " or";
                }
                $act_RRP_Cond = $act_RRP_Cond . " a.RRP LIKE '%".$single."%' ";
                if($index < $searchWordsCounter){
                    $act_RRP_Cond = $act_RRP_Cond. " or";
                }
                
                
                $index++;
            }
            $searchCond = $searchCond.$productNameCond.$categoryNameCond.$parentCategoryNameCond.$mainCategoryNameCond.$act_RRP_Cond.")";        
        }
        */
        
        # Search by productName, categoryName, parentCategoryName and mainCategoryName        
        if($searchText)
        {
           $searchCond ="("; 
           //$productNameCond = $productNameCond . " a.ProductName LIKE '%".$searchText."%' ";
           
           $productNameCond = $productNameCond . " a.ProductName LIKE '%".$searchText."%' ";
           $productNameCond = $productNameCond . " or a.ProductDescription LIKE '%".$searchText."%' ";
           
           $categoryNameCond = $categoryNameCond . " or d.CategoryName LIKE '%".$searchText."%' ";
           $parentCategoryNameCond = $parentCategoryNameCond . " or c.CategoryName LIKE '%".$searchText."%' ";
           $mainCategoryNameCond = $mainCategoryNameCond . " or b.CategoryName LIKE '%".$searchText."%' ";
           $act_RRP_Cond = $act_RRP_Cond . " or a.RRP LIKE '%".$searchText."%' ";
           $searchCond = $searchCond.$productNameCond.")";  
           //$searchCond = $searchCond.$productNameCond.$categoryNameCond.$parentCategoryNameCond.$mainCategoryNameCond.$act_RRP_Cond.")";  
        }
                
        //$this -> datatables -> select("a.Id as p_id,b.CategoryName as MainCategory,c.CategoryName as ParentCategory,d.CategoryName as Category,case when a.HouseId is not null then concat(e.CompanyName,' ',a.ProductName) else a.ProductName end as ProductName,a.RRP as act_RRP,'0.00' as RRP, concat('<input class=\"prod_prc click_sel\" style=\"width:65px\" type=\"text\" id=\"product_price_',a.Id, '\" value=\"0.00\" />') as SpecialPrice, " . specials_get_select(0) . " as SpecialQty,", false)
        $this -> datatables -> select("a.Id as p_id,b.CategoryName as MainCategory,c.CategoryName as ParentCategory,d.CategoryName as Category,case when a.HouseId is not null then concat(e.CompanyName,' ',a.ProductName) else a.ProductDescription end as ProductName,a.RRP as act_RRP,'0.00' as RRP, concat('<input class=\"prod_prc click_sel\" style=\"width:65px\" type=\"text\" text-align=\"right\" id=\"product_price_',a.Id, '\" value=\"0.00\" />') as SpecialPrice, concat('<input class=\"coupon_amount click_sel\" style=\"width:65px\" type=\"text\" id=\"coupon_amount_',a.Id, '\" value=\"0.00\" />') as CouponAmount," . specials_get_select(0) . " as SpecialQty,case when scp.ComboProductsData is null or scp.ComboProductsData ='{}' then 0 else 1 end as ComboProducts,", false)
            -> from('products as a')
            -> join('categories as b', 'a.MainCategoryId = b.Id and b.IsActive = 1 and b.IsRemoved = 0')
            -> join('categories as c', 'a.ParentCategoryId = c.Id and c.IsActive = 1 and c.IsRemoved = 0')
            -> join('categories as d', 'a.CategoryId = d.Id and d.IsActive = 1 and d.IsRemoved = 0', 'left')
            -> join('retailers as e', 'e.Id = a.HouseId', 'left')
            -> join('special_combo_products_backup as scp', 'scp.SpecialProductId = a.Id and scp.RetailerId = '.$this -> session -> userdata('user_retailer_id').' and scp.SpecialId = '.$this -> session -> userdata('special_sel'), 'left')    
            -> where('(a.HouseId is null or a.HouseId = ' . $this -> session -> userdata('user_retailer_id') . ' )')
            -> where('a.IsActive', 1)
            -> where('a.IsRemoved', 0)
            //-> where('a.StoreTypeId', $this -> session -> userdata('user_store_format_id'))
            -> add_column('selectVal', '<input type="checkbox"  name="store_products[]" value="$1" />', 'p_id')
            -> add_column('Actions', get_customspecials_action_buttons('$1', 'customspecials'), 'p_id');
            
            if($homebrand > 0 )
            {
                $this -> datatables -> where('a.HouseId > 0');
            }
            
        # Added adanced search condition
        if($searchText)
        {
            $this -> datatables -> where($searchCond);
        }
        
        $this -> datatables -> group_by('a.Id');

        echo $this -> datatables -> generate();
    }

    public function datatable_selected() {
        $selected_arr = json_decode($this -> input -> post('selected'));
        //$this -> datatables -> select("a.Id as p_id,b.CategoryName as MainCategory,c.CategoryName as ParentCategory,d.CategoryName as Category,case when a.HouseId is not null then concat(e.CompanyName,' ',a.ProductName) else a.ProductName end as ProductName,a.RRP as act_RRP,'0.00' as RRP, concat('<input class=\"prod_prc  click_sel\" style=\"width:65px\" type=\"text\" id=\"product_price_',a.Id, '\" value=\"0.00\" />') as SpecialPrice, concat('<input class=\"coupon_amount click_sel\" style=\"width:65px\" type=\"text\" id=\"coupon_amount_',a.Id, '\" value=\"0.00\" />') as CouponAmount," . specials_get_select(0) . " as SpecialQty,", false)

		
		$this -> datatables -> select("a.Id as p_id,b.CategoryName as MainCategory,c.CategoryName as ParentCategory,d.CategoryName as Category,case when a.HouseId is not null then concat(e.CompanyName,' ',a.ProductName) else a.ProductDescription end as ProductName,a.RRP as act_RRP,'0.00' as RRP, concat('<input class=\"prod_prc click_sel\" style=\"width:65px\" type=\"text\" text-align=\"right\" id=\"product_price_',a.Id, '\" value=\"0.00\" />') as SpecialPrice, concat('<input class=\"coupon_amount click_sel\" style=\"width:65px\" type=\"text\" id=\"coupon_amount_',a.Id, '\" value=\"0.00\" />') as CouponAmount," . specials_get_select(0) . " as SpecialQty,case when scp.ComboProductsData is null or scp.ComboProductsData ='{}' then 0 else 1 end as ComboProducts", false)
            -> from('products as a')
            -> join('categories as b', 'a.MainCategoryId = b.Id and b.IsActive = 1 and b.IsRemoved = 0')
            -> join('categories as c', 'a.ParentCategoryId = c.Id and c.IsActive = 1 and c.IsRemoved = 0')
            -> join('categories as d', 'a.CategoryId = d.Id and d.IsActive = 1 and d.IsRemoved = 0', 'left')
            -> join('retailers as e', 'e.Id = a.HouseId', 'left')
            -> join('special_combo_products_backup as scp', 'scp.SpecialProductId = a.Id and scp.RetailerId = '.$this -> session -> userdata('user_retailer_id').' and scp.SpecialId = '.$this -> session -> userdata('special_sel'), 'left')    
            -> where('(a.HouseId is null or a.HouseId = ' . $this -> session -> userdata('user_retailer_id') . ' )')
            -> add_column('selectVal', '<input type="checkbox"  name="store_products[]" value="$1" />', 'p_id')
            -> add_column('Actions', get_customspecials_action_buttons('$1', 'customspecials'), 'p_id');
             
        if ($selected_arr) {
            $cnt = 0;
            $qry_apnd = '';
            foreach ($selected_arr as $sel_id => $val) {
                if ($cnt == 0) {
                    $qry_apnd .= '( a.Id = ' . $sel_id . ' ';
                }
                else {
                    $qry_apnd .= ' OR a.Id = ' . $sel_id . ' ';
                }
                $cnt++;
            }
            $qry_apnd .= ')';
            if ($cnt > 0) {
                $this -> datatables -> where($qry_apnd);
            }
        }
        $this -> datatables -> group_by('a.Id');

        echo $this -> datatables -> generate();
    }
    /* public function datatable_selected() {

      $selected_arr = json_decode($this -> input -> post('selected'));
      $this -> datatables -> select("a.Id as p_id,g.CategoryName as MainCategory,h.CategoryName as ParentCategory,i.CategoryName as Category,b.ProductName,c.CompanyName,d.StoreName,a.price as RRP,case when f.SpecialPrice IS NULL then concat('<input class=\"prod_prc\" style=\"width:65px\" type=\"text\" id=\"product_price_',a.Id, '\" value=\"0.00\" />') else concat('<input class=\"prod_prc\" style=\"width:65px\" type=\"text\" id=\"product_price_',a.Id,'\" value=\"',f.SpecialPrice,'\" />') end as SpecialPrice,
      case
      when f.SpecialQty IS NULL then " . specials_get_select(0) . "
      when f.SpecialQty = 1 then " . specials_get_select(1) . "
      when f.SpecialQty = 2 then " . specials_get_select(2) . "
      when f.SpecialQty = 3 then " . specials_get_select(3) . "
      when f.SpecialQty = 4 then " . specials_get_select(4) . "
      when f.SpecialQty = 5 then " . specials_get_select(5) . "
      when f.SpecialQty = 6 then " . specials_get_select(6) . "
      when f.SpecialQty = 7 then " . specials_get_select(7) . "
      when f.SpecialQty = 8 then " . specials_get_select(8) . "
      when f.SpecialQty = 9 then " . specials_get_select(9) . "
      when f.SpecialQty = 10 then " . specials_get_select(10) . "
      when f.SpecialQty > 10 then " . specials_get_select(11) . " end
      as SpecialQty,
      DATE_FORMAT(f.PriceAppliedFrom,'%d/%c/%Y') as PriceAppliedFrom,DATE_FORMAT(f.PriceAppliedTo,'%d/%c/%Y') as PriceAppliedTo", false)
      -> from('storeproducts as a')
      -> join('products as b', 'b.Id = a.ProductId')
      -> join('retailers as c', 'c.Id = a.RetailerId')
      -> join('stores as d', 'd.Id = a.StoreId')
      -> join('storestypes as e', 'e.Id = a.StoreTypeId')
      -> join('productspecials as f', 'f.ProductId = b.Id and f.RetailerId = c.Id and f.StoreId = d.Id and f.StoreTypeId = e.Id and (now() between f.PriceAppliedFrom and f.PriceAppliedTo)', 'left')
      -> join('categories as g', 'g.Id = b.MainCategoryId', 'left')
      -> join('categories as h', 'h.Id = b.ParentCategoryId', 'left')
      -> join('categories as i', 'i.Id = b.CategoryId', 'left')
      //-> where('a.StoreTypeId', $this -> session -> userdata('user_store_format_id'))
      -> add_column('selectVal', '<input type="checkbox"  name="store_products[]" value="$1" />', 'p_id');
      if ($this -> session -> userdata('user_type') == 3) {
      $this -> datatables -> where('a.RetailerId', $this -> session -> userdata('user_retailer_id'));
      }
      if ($this -> session -> userdata('user_type') == 5) {
      $this -> datatables -> where('a.StoreTypeId', $this -> session -> userdata('user_store_format_id'));
      }
      if ($this -> session -> userdata('user_type') == 6) {
      $this -> datatables -> where('a.StoreId', $this -> session -> userdata('user_store_id'));
      }
      if ($selected_arr) {
      $cnt = 0;
      $qry_apnd = '';
      foreach ($selected_arr as $sel_id => $val) {
      if ($cnt == 0) {
      $qry_apnd .= '( a.Id = ' . $sel_id . ' ';
      }
      else {
      $qry_apnd .= ' OR a.Id = ' . $sel_id . ' ';
      }
      $cnt++;
      }
      $qry_apnd .= ')';
      if ($cnt > 0) {
      $this -> datatables -> where($qry_apnd);
      }
      }
      $this -> datatables -> group_by('a.Id');

      echo $this -> datatables -> generate();
      } */

    public function get_special_details() {
        if ($this -> input -> server('REQUEST_METHOD') == 'POST') {
            $special_sel = $this -> input -> post('special_sel');
            $name = '';
            $from = '';
            $to = '';
            $image = '';
            if ($special_sel) {
                $special_details = $this -> specialproductmodel -> get_special_data($special_sel);
                if ($special_details) {
                    $this -> result = 1;
                    $this -> message = 'Got data';
                    $name = $special_details['SpecialName'];
                    $from = $special_details['SpecialFrom'];
                    $to = $special_details['SpecialTo'];
                    $terms = $special_details['TermsAndConditions'];
                    $image = $special_details['SpecialBanner'];

                    $terms_all = $this -> specialproductmodel -> get_terms_details();
                    if ($terms) {
                        $terms_added = [];
                        $terms_arr = explode(',', $terms);
                        if ($terms_all) {
                            foreach ($terms_all as $term) {
                                if (in_array($term['Id'], $terms_arr)) {
                                    $terms_added[] = $term['TermsText'];
                                }
                            }
                        }
                        $terms = implode('<br />', $terms_added);
                    }
                    else {
                        $terms = '';
                    }
                }
                else {
                    $this -> result = 0;
                    $this -> message = 'No records found';
                }
            }
            else {
                $this -> result = 0;
                $this -> message = 'Invalid data';
            }
        }
        else {
            $this -> result = 0;
            $this -> message = 'Invalid request';
        }
        echo json_encode(array(
            'result' => $this -> result,
            'message' => $this -> message,
            'name' => $name,
            'from' => $from,
            'to' => $to,
            'terms' => $terms,
            'image' => $image
        ));
    }

    public function get_main_categories() {
        $main_categories = $this -> specialproductmodel -> get_main_categories();
        if ($main_categories) {
            $html = '';
            foreach ($main_categories as $category) {
                $html .= '<option value="' . $category['Id'] . '">' . $category['CategoryName'] . '</option>';
            }
            echo $html;
        }
        else {
            echo '';
        }
    }

    public function get_parent_categories() {
        $main_cat = $this -> input -> post('sel_val');
        $main_categories = $this -> specialproductmodel -> get_parent_categories($main_cat);
        if ($main_categories) {
            $html = '<option value="">Select Parent Category</option>';
            foreach ($main_categories as $category) {
                $html .= '<option value="' . $category['Id'] . '">' . $category['CategoryName'] . '</option>';
            }
            echo $html;
        }
        else {
            echo '';
        }
    }
    /* public function add_special_product_new() {
      set_time_limit(0);
      ini_set('memory_limit', '512M');
      $insert_data = [];
      if ($this -> input -> server('REQUEST_METHOD') == 'POST') {
      $product_details = json_decode($this -> input -> post('product_details'));
      $store_detail_list = $this -> specialproductmodel -> get_special_group_stores($this -> session -> userdata('special_sel'));
      if (!empty($product_details) && $store_detail_list) {
      $success = 0;
      $fail = 0;
      //$validate_price = [];
      $special_data = $this -> specialproductmodel -> get_specials_details($this -> session -> userdata('special_sel'));
      foreach ($store_detail_list as $store_detail) {
      foreach ($product_details as $product_id => $product) {
      $one_item_price = $product -> price;
      //                        if(!isset($validate_price[$product_id])){
      //                             $validate_price[$product_id] = $this -> specialproductmodel -> validate_default_price($product_id, $store_detail['RetailerId'], $store_detail['StoreTypeId'], $store_detail['Id'], $one_item_price);
      //                        }
      //$product_data = $this -> specialproductmodel -> get_store_product_details($product_id);
      //if ($product_data && $special_data) {
      if ($special_data) {
      $validate_data = array(
      'RetailerId' => $store_detail['RetailerId'],
      'StoreTypeId' => $store_detail['StoreTypeId'],
      'StoreId' => $store_detail['Id'],
      'ProductId' => $product_id,
      'SpecialId' => $this -> session -> userdata('special_sel'),
      'PriceAppliedFrom' => $special_data['SpecialFrom'],
      'PriceAppliedTo' => $special_data['SpecialTo'],
      'IsActive' => 1
      );
      $validate_offer = $this -> specialproductmodel -> validate_offer_any($validate_data);
      if ($product -> qty > 1) {
      $one_item_price = ($product -> price) / ($product -> qty);
      }
      if ($product -> def > 0) {
      $up_dat = array(
      'Price' => $product -> def,
      'ModifiedBy' => $this -> session -> userdata('user_id'),
      'ModifiedOn' => date('Y-m-d H:i:s')
      );
      $where = array(
      'RetailerId' => $store_detail['RetailerId'],
      'StoreTypeId' => $store_detail['StoreTypeId'],
      'StoreId' => $store_detail['Id'],
      'ProductId' => $product_id
      );
      $this -> specialproductmodel -> update_store_price($where, $up_dat);
      }
      if (!$validate_offer) {
      $validate_price = $this -> specialproductmodel -> validate_default_price($product_id, $store_detail['RetailerId'], $store_detail['StoreTypeId'], $store_detail['Id'], $one_item_price);
      if ($validate_price && $product -> price > 0) {

      $this -> specialproductmodel -> validate_store_product($product_id, $store_detail['RetailerId'], $store_detail['Id']);
      $insert_data[] = array(
      'ProductId' => $product_id,
      'RetailerId' => $store_detail['RetailerId'],
      'StoreId' => $store_detail['Id'],
      'StoreTypeId' => $store_detail['StoreTypeId'],
      'PriceForAllStores' => '0',
      'ActualPrice' => '0.00',
      'SpecialQty' => $product -> qty,
      'SpecialPrice' => $product -> price,
      'PriceAppliedFrom' => $special_data['SpecialFrom'],
      'PriceAppliedTo' => $special_data['SpecialTo'],
      'CreatedBy' => $this -> session -> userdata('user_id'),
      'CreatedOn' => date('Y-m-d H:i:s'),
      'SpecialId' => $special_data['Id'],
      'IsActive' => 1,
      'IsApproved' => 0,
      'ApprovedBy' => 0);

      //$result = $this -> specialproductmodel -> add_special_product($insert_data);
      //                                    if ($result) {
      //                                        $success++;
      //                                    }
      //                                    else {
      //                                        $fail++;
      //                                    }
      }
      else {
      $fail++;
      }
      }
      else {
      $validate_price = $this -> specialproductmodel -> validate_default_price($product_id, $store_detail['RetailerId'], $store_detail['StoreTypeId'], $store_detail['Id'], $one_item_price);
      if ($validate_price && $product -> price > 0) {

      $this -> specialproductmodel -> validate_store_product($product_id, $store_detail['RetailerId'], $store_detail['Id']);
      $update_data = array(
      'ProductId' => $product_id,
      'RetailerId' => $store_detail['RetailerId'],
      'StoreId' => $store_detail['Id'],
      'StoreTypeId' => $store_detail['StoreTypeId'],
      'PriceForAllStores' => '0',
      'ActualPrice' => '0.00',
      'SpecialQty' => $product -> qty,
      'SpecialPrice' => $product -> price,
      'PriceAppliedFrom' => $special_data['SpecialFrom'],
      'PriceAppliedTo' => $special_data['SpecialTo'],
      'SpecialId' => $special_data['Id'],
      'IsApproved' => 0);
      $result = $this -> specialproductmodel -> update_special_product($validate_offer['Id'], $update_data);
      if ($result) {
      $success++;
      }
      else {
      $fail++;
      }
      }
      else {
      $fail++;
      }
      }
      }
      else {
      $fail++;
      }
      }
      }

      if(!empty($insert_data)){
      if($this -> specialproductmodel -> insert_special_batch($insert_data)){
      $success++;
      }
      }
      if ($success == 0 && $fail > 0) {
      $this -> result = 0;
      $this -> message = 'Failed to add specials';
      }
      elseif ($success > 0 && $fail > 0) {
      $this -> result = 1;
      $this -> message = 'Successfully added some products, failed to add the others.';
      }
      elseif ($success > 0) {
      $this -> result = 1;
      $this -> message = 'All specials added successfully';
      }
      }
      else {
      $this -> result = 0;
      $this -> message = 'No specials found to add.';
      }
      }
      else {
      $this -> result = 0;
      $this -> message = 'Invalid request';
      }
      echo json_encode(array(
      'result' => $this -> result,
      'message' => $this -> message
      ));
      } */

    public function get_special_stores() {
        if ($this -> input -> server('REQUEST_METHOD') == 'POST') {
            $store_detail_list = $this -> specialproductmodel -> get_special_group_stores($this -> session -> userdata('special_sel'));
            if ($store_detail_list) {
                $this -> result = 1;
                $this -> message = $store_detail_list;
            }
            else {
                $this -> result = 0;
                $this -> message = 'No stores found to add specials';
            }
        }
        else {
            $this -> result = 0;
            $this -> message = 'Invalid data';
        }
        echo json_encode(array(
            'result' => $this -> result,
            'message' => $this -> message
        ));
    }

    public function add_special_product_new() {
        set_time_limit(0);
        ini_set('memory_limit', '512M');
        $insert_data = [];
        if ($this -> input -> server('REQUEST_METHOD') == 'POST') {
            
            
            $AllStates = sanitize($this -> input -> post('AllStates'));
            $AllStores = sanitize($this -> input -> post('AllStores'));
            $Id = sanitize($this -> input -> post('Id'));
            $RetailerId = sanitize($this -> input -> post('RetailerId'));
            $SpecialName = sanitize($this -> input -> post('SpecialName'));
            $StateId = sanitize($this -> input -> post('StateId'));
            $StoreTypeId = sanitize($this -> input -> post('StoreTypeId'));
            $product_details = json_decode($this -> input -> post('product_details'));
            
            /*
            echo "<pre>";
            print_r($this -> input -> post());
            exit;
            */
            
            //$store_detail_list = $this -> specialproductmodel -> get_special_group_stores($this -> session -> userdata('special_sel'));
            //if (!empty($product_details) && $store_detail_list) {
            if (!empty($product_details)) {
                $success = 0;
                $fail = 0;
                //$validate_price = [];
                $special_data = $this -> specialproductmodel -> get_specials_details($this -> session -> userdata('special_sel'));
                
                
            
                foreach ($product_details as $product_id => $product) {
                    
                    
                    $one_item_price = $product -> price;
//                        if(!isset($validate_price[$product_id])){
//                             $validate_price[$product_id] = $this -> specialproductmodel -> validate_default_price($product_id, $store_detail['RetailerId'], $store_detail['StoreTypeId'], $store_detail['Id'], $one_item_price);
//                        }
                    //$product_data = $this -> specialproductmodel -> get_store_product_details($product_id);
                    //if ($product_data && $special_data) {
                    if ($special_data) {
                        $validate_data = array(
                            'RetailerId' => $RetailerId,
                            'StoreTypeId' => $StoreTypeId,
                            'StoreId' => $Id,
                            'ProductId' => $product_id,
                            'SpecialId' => $this -> session -> userdata('special_sel'),
                            'PriceAppliedFrom' => date('Y-m-d H:i:s',  strtotime($special_data['SpecialFrom'])),
                            'PriceAppliedTo' => date('Y-m-d H:i:s',  strtotime($special_data['SpecialTo'])),
                            'SpecialId' => $special_data['Id'],
                            'IsActive' => 1
                        );
                        $validate_offer = $this -> specialproductmodel -> validate_offer_any($validate_data);
                        
                        
                        if ($product -> qty > 1) {
                            $one_item_price = ($product -> price) / ($product -> qty);
                        }
                        if ($product -> def > 0) {
                            $up_dat = array(
                                'Price' => $product -> def,
                                'ModifiedBy' => $this -> session -> userdata('user_id'),
                                'ModifiedOn' => date('Y-m-d H:i:s')
                            );
                            $where = array(
                                'RetailerId' => $RetailerId,
                                'StoreTypeId' => $StoreTypeId,
                                'StoreId' => $Id,
                                'ProductId' => $product_id
                            );
                            $this -> specialproductmodel -> update_store_price($where, $up_dat);
                        }
                        if (!$validate_offer) {
                            
                            $validate_price = $this -> specialproductmodel -> validate_default_price($product_id, $RetailerId, $StoreTypeId, $Id, $one_item_price);
                            
                           
                            
                            
                            if ($validate_price && $product -> price > 0) {
                                //$this -> specialproductmodel -> validate_store_product($product_id, $RetailerId, $Id);
                                $insert_data[] = array(
                                    'ProductId' => $product_id,
                                    'RetailerId' => $RetailerId,
                                    'StoreId' => $Id,
                                    'StoreTypeId' => $StoreTypeId,
                                    'PriceForAllStores' => '0',
                                    'ActualPrice' => '0.00',
                                    'SpecialQty' => $product -> qty,
                                    'SpecialPrice' => $product -> price,
                                    'CouponAmount' => $product -> coupon_amount,
                                    'PriceAppliedFrom' => $special_data['SpecialFrom'],
                                    'PriceAppliedTo' => $special_data['SpecialTo'],
                                    'CreatedBy' => $this -> session -> userdata('user_id'),
                                    'CreatedOn' => date('Y-m-d H:i:s'),
                                    'SpecialId' => $special_data['Id'],
                                    'IsActive' => 1,
                                    'IsApproved' => 0,
                                    'ApprovedBy' => 0);

                                //$result = $this -> specialproductmodel -> add_special_product($insert_data);
//                                    if ($result) {
//                                        $success++;
//                                    }
//                                    else {
//                                        $fail++;
//                                    }
                            }
                            else {
                                $fail++;
                            }
                        }
                        else {
                            
                            $validate_price = $this -> specialproductmodel -> validate_default_price($product_id, $RetailerId, $StoreTypeId, $Id, $one_item_price);
                            if ($validate_price && $product -> price > 0) {

                                //$this -> specialproductmodel -> validate_store_product($product_id, $RetailerId, $Id);
                                $update_data = array(
                                    'ProductId' => $product_id,
                                    'RetailerId' => $RetailerId,
                                    'StoreId' => $Id,
                                    'StoreTypeId' => $StoreTypeId,
                                    'PriceForAllStores' => '0',
                                    'ActualPrice' => '0.00',
                                    'SpecialQty' => $product -> qty,
                                    'SpecialPrice' => $product -> price,
                                    'CouponAmount' => $product -> coupon_amount,
                                    'PriceAppliedFrom' => $special_data['SpecialFrom'],
                                    'PriceAppliedTo' => $special_data['SpecialTo'],
                                    'SpecialId' => $special_data['Id'],
                                    'IsApproved' => 0);
                                $result = $this -> specialproductmodel -> update_special_product($validate_offer['Id'], $update_data);
                                //$result = 1;
                                if ($result) {
                                    $success++;
                                }
                                else {
                                    $fail++;
                                }
                            }
                            else {
                                $fail++;
                            }
                        }
                    }
                    else {
                        $fail++;
                    }
                }

                /*
                echo "<pre>";
                print_r($insert_data);
                exit;
                */
                
                if (!empty($insert_data)) {
                    if ($this -> specialproductmodel -> insert_special_batch($insert_data)) {
                    //if (1 == 1) {
                        $success++;
                    }
                }
                if ($success == 0 && $fail > 0) {
                    $this -> result = 0;
                    $this -> message = 'Failed to add specials';
                }
                elseif ($success > 0 && $fail > 0) {
                    $this -> result = 1;
                    $this -> message = 'Successfully added some products, failed to add the others.';
                    $this -> specialproductmodel -> update_added_store($Id,$RetailerId);
                }
                elseif ($success > 0) {
                    $this -> result = 1;
                    $this -> message = 'All specials added successfully';
                    $this -> specialproductmodel -> update_added_store($Id,$RetailerId);
                }
                
               
                // Set combo Products 
                $specialId = $this -> session -> userdata('special_sel');
                if($specialId > 0 ){
                  $this -> specialproductmodel -> set_combo_products($specialId);
                }
                
                
            }
            else {
                $this -> result = 0;
                $this -> message = 'No specials found to add.';
            }
        }
        else {
            $this -> result = 0;
            $this -> message = 'Invalid request';
        }
        
        
        echo json_encode(array(
            'result' => $this -> result,
            'message' => $this -> message
        ));
    }
    /* public function add_special_product_new() {
      set_time_limit(0);
      if ($this -> input -> server('REQUEST_METHOD') == 'POST') {
      $product_details = json_decode($this -> input -> post('product_details'));
      if (!empty($product_details)) {
      $success = 0;
      $fail = 0;
      foreach ($product_details as $product_id => $product) {
      $product_data = $this -> specialproductmodel -> get_store_product_details($product_id);
      $special_data = $this -> specialproductmodel -> get_specials_details($this -> session -> userdata('special_sel'));
      if ($product_data && $special_data) {
      $validate_data = array(
      'ProductId' => $product_data['ProductId'],
      'RetailerId' => $product_data['RetailerId'],
      'StoreId' => $product_data['StoreId'],
      'StoreTypeId' => $product_data['StoreTypeId'],
      'SpecialId' => $this -> session -> userdata('special_sel'),
      'PriceAppliedFrom' => $special_data['SpecialFrom'],
      'PriceAppliedTo' => $special_data['SpecialTo'],
      'IsActive' => 1
      );
      $validate_offer = $this -> specialproductmodel -> validate_offer_any($validate_data);
      if (!$validate_offer) {
      $one_item_price = $product -> price;
      if ($product -> qty > 1) {
      $one_item_price = ($product -> price) / ($product -> qty);
      }
      $validate_price = $this -> specialproductmodel -> validate_default_price($product_data['ProductId'], $product_data['RetailerId'], $product_data['StoreTypeId'], $product_data['StoreId'], $one_item_price);
      if ($validate_price && $product -> price > 0) {
      $this -> specialproductmodel -> validate_store_product($product_data['ProductId'], $product_data['RetailerId'], $product_data['StoreId']);
      $insert_data = array(
      'ProductId' => $product_data['ProductId'],
      'RetailerId' => $product_data['RetailerId'],
      'StoreId' => $product_data['StoreId'],
      'StoreTypeId' => $product_data['StoreTypeId'],
      'PriceForAllStores' => '0',
      'ActualPrice' => '0.00',
      'SpecialQty' => $product -> qty,
      'SpecialPrice' => $product -> price,
      'PriceAppliedFrom' => $special_data['SpecialFrom'],
      'PriceAppliedTo' => $special_data['SpecialTo'],
      'CreatedBy' => $this -> session -> userdata('user_id'),
      'CreatedOn' => date('Y-m-d H:i:s'),
      'SpecialId' => $special_data['Id'],
      'IsActive' => 1,
      'IsApproved' => 0,
      'ApprovedBy' => 0);


      $result = $this -> specialproductmodel -> add_special_product($insert_data);
      if ($result) {
      $success++;
      }
      else {
      $fail++;
      }
      }
      else {
      $fail++;
      }
      }
      else {
      $one_item_price = $product -> price;
      if ($product -> qty > 1) {
      $one_item_price = ($product -> price) / ($product -> qty);
      }
      $validate_price = $this -> specialproductmodel -> validate_default_price($product_data['ProductId'], $product_data['RetailerId'], $product_data['StoreTypeId'], $product_data['StoreId'], $one_item_price);
      if ($validate_price && $product -> price > 0) {
      $this -> specialproductmodel -> validate_store_product($product_data['ProductId'], $product_data['RetailerId'], $product_data['StoreId']);
      $update_data = array(
      'ProductId' => $product_data['ProductId'],
      'RetailerId' => $product_data['RetailerId'],
      'StoreId' => $product_data['StoreId'],
      'StoreTypeId' => $product_data['StoreTypeId'],
      'PriceForAllStores' => '0',
      'ActualPrice' => '0.00',
      'SpecialQty' => $product -> qty,
      'SpecialPrice' => $product -> price,
      'PriceAppliedFrom' => $special_data['SpecialFrom'],
      'PriceAppliedTo' => $special_data['SpecialTo'],
      'SpecialId' => $special_data['Id'],
      'IsApproved' => 0);
      $result = $this -> specialproductmodel -> update_special_product($validate_offer['Id'], $update_data);
      if ($result) {
      $success++;
      }
      else {
      $fail++;
      }
      }
      else {
      $fail++;
      }
      }
      }
      else {
      $fail++;
      }
      }
      if ($success == 0 && $fail > 0) {
      $this -> result = 0;
      $this -> message = 'Failed to add specials';
      }
      elseif ($success > 0 && $fail > 0) {
      $this -> result = 1;
      $this -> message = 'Successfully added some products, failed to add the others.';
      }
      elseif ($success > 0) {
      $this -> result = 1;
      $this -> message = 'All specials added successfully';
      }
      }
      else {
      $this -> result = 0;
      $this -> message = 'No specials found to add.';
      }
      }
      else {
      $this -> result = 0;
      $this -> message = 'Invalid request';
      }
      echo json_encode(array(
      'result' => $this -> result,
      'message' => $this -> message
      ));
      } */

    public function get_special_list() {
        $specials = $this -> specialproductmodel -> get_special_details();
        if ($specials) {
            $html = '';
            foreach ($specials as $special) {
                $html .= '<option value="' . $special['Id'] . '">' . $special['SpecialName'] . '</option>';
            }
            $this -> result = 1;
            $this -> message = $html;
        }
        else {
            $this -> result = 0;
            $this -> message = 'No specials found';
        }
        echo json_encode(array(
            'result' => $this -> result,
            'message' => $this -> message,
            'sel' => $this -> session -> userdata('special_sel')
        ));
    }

    public function add_to_special_backup() {
        if ($this -> input -> server('REQUEST_METHOD') == 'POST') {
            $special_array = $this -> input -> post('special_array');
            $isAdded = $this -> specialproductmodel -> add_special_backup($special_array);
        }
        else {
            $this -> result = 0;
            $this -> message = 'Invalid data';
        }
        echo json_encode(array(
            'result' => $this -> result,
            'message' => $this -> message
        ));
    }

    public function get_special_backup() {
        $added_stores_arr = [];
        if ($this -> input -> server('REQUEST_METHOD') == 'POST') {
            $special_backup = $this -> specialproductmodel -> get_special_backup();
            $this -> result = 1;
            $this -> message = $special_backup['SpecialData'];
            $added_stores_arr = explode(',',$special_backup['AddedStores']);
        }
        else {
            $this -> result = 0;
            $this -> message = 'Invalid data';
        }
        echo json_encode(array(
            'result' => $this -> result,
            'message' => $this -> message,
            'added' => $added_stores_arr
        ));
    }

    public function remove_special_backup() {
        if ($this -> input -> server('REQUEST_METHOD') == 'POST') {
            $isAdded = $this -> specialproductmodel -> remove_special_backup();
        }
        else {
            $this -> result = 0;
            $this -> message = 'Invalid data';
        }
        echo json_encode(array(
            'result' => $this -> result,
            'message' => $this -> message
        ));
    }
    
    public function get_store_name(){
        $id = $this -> input -> post('id');
        $store_details = $this -> specialproductmodel -> get_store_details($id);
        if(isset($store_details['StoreName'])){
            $this -> result = 1;
            $this -> message = $store_details['StoreName'];
        }
        else{
            $this -> result = 0;
            $this -> message = 'No Stores';
        }
        echo json_encode(array(
            'result' => $this -> result,
            'message' => $this -> message
        ));
    }
//    public function create_push_message($product_id = '', $retailer_id = '', $store_type_id = '', $store_id = '', $product_name = '', $retailer_name = '', $store_name = '', $store_type_name = '', $special_count = 0, $special_price = 0, $special_name = '', $success_count = 0, $store_array = array(), $product_array = array(), $store_detail_array = array()) {
//        $multiple_insert = [];
//        $multiple_single_insert = [];
//        $message = $special_name . ' created for ';
//        if ($success_count > 1) {
//            $product_id = '';
//            $retailer_id = '';
//            $store_type_id = '';
//            $store_id = '';
//            $message .= $success_count . ' products.';
//        }
//        else {
//            $message .= $product_name . ' from retailer: ' . $retailer_name . ', store: ' . $store_name . '. Now available at a rate ';
//            if ($special_count > 1) {
//                $message .= $special_price . ' for ' . $special_count . '.';
//            }
//            else {
//                $message .= $special_price . '.';
//            }
//        }
//        $message .= ' Don\'t miss it!';
//
//
//        $user_ids = $this -> specialproductmodel -> get_device_user_ids();
//        $users_have_store = [];
//        $user_have_price_alert = [];
//        if ($user_ids) {
//            foreach ($user_ids as $user_id) {
//                if ($user_id['PrefLatitude'] && $user_id['PrefLongitude'] && $user_id['PrefDistance']) {
//                    $store_list = $this -> specialproductmodel -> get_device_user_stores($user_id['PrefLatitude'], $user_id['PrefLongitude'], $user_id['PrefDistance'], $store_array);
//                    if ($store_list) {
//                        $users_have_store[] = $user_id['UserId'];
//                    }
//                }
//            }
//            if (!empty($users_have_store)) {
//                $notification_array = array(
//                    'title' => 'Hurry! Specials Added',
//                    'message' => $message,
//                    'product_id' => $product_id,
//                    'retailer_id' => $retailer_id,
//                    'store_type_id' => $store_type_id,
//                    'store_id' => $store_id
//                );
//                if (!empty($product_array)) {
//                    foreach ($product_array as $index => $product) {
//                        foreach ($users_have_store as $user_get) {
//                            $is_alert_available = $this -> specialproductmodel -> check_price_alert($product['id'], $user_get);
//                            if ($is_alert_available) {
//                                $notification_array1 = array(
//                                    'title' => 'Price change alert',
//                                    'message' => 'A price change is made for ' . $product['name'] . ' by store ' . $store_detail_array[$index]['name'],
//                                    'product_id' => $product['id'],
//                                    'retailer_id' => $store_detail_array[$index]['retailer'],
//                                    'store_type_id' => $store_detail_array[$index]['storeType'],
//                                    'store_id' => $store_detail_array[$index]['id']
//                                );
//                                $multiple_single_insert[] = array(
//                                    'Title' => $notification_array1['title'],
//                                    'Message' => $notification_array1['message'],
//                                    'UserId' => $user_get
//                                );
//                                send_push_notification($notification_array1, array($user_get),$multiple_single_insert);
//                            }
//                        }
//                    }
//                }
//                foreach ($users_have_store as $us_st) {
//                    $multiple_insert[] = array(
//                        'Title' => $notification_array['title'],
//                        'Message' => $notification_array['message'],
//                        'UserId' => $us_st
//                    );
//                }
//                send_push_notification($notification_array, $users_have_store, $multiple_insert);
//                die('done');
//            }
//        }
//    }
    
    public function show_combo_products()    
    {
        $data = array();
        $this -> load -> view('admin/custom_special/combo_products', $data);
    }
    
     public function combo_products_datatable() {
        $selected_combo_products = $this -> input -> post('selected_combo_products');	
        $selected_arr = json_decode($this -> input -> post('selected_combo_products')); 
        $homebrand = 0;
        
        $this -> datatables -> select("a.Id as p_id,case when a.HouseId is not null then concat(e.CompanyName,' ',a.ProductName) else a.ProductDescription end as ProductName," . combo_products_get_select(0) . " as SpecialQty,", false)
            -> from('products as a')
            -> join('retailers as e', 'e.Id = a.HouseId', 'left')
            -> where('(a.HouseId is null or a.HouseId = ' . $this -> session -> userdata('user_retailer_id') . ' )')
            -> where('a.IsActive', 1)
            -> where('a.IsRemoved', 0)
            -> add_column('selectVal', '<input type="checkbox"  name="store_combo_products[]" value="$1" id="comboProd_$1"/>', 'p_id')
            -> add_column('Actions', get_customspecials_action_buttons('$1', 'customspecials'), 'p_id');
            
            if($homebrand > 0 )
            {
                $this -> datatables -> where('a.HouseId > 0');
            }
            
            if ($selected_arr) {
                $cnt = 0;
                $qry_apnd = '';
                foreach ($selected_arr as $sel_id => $val) {
                    if ($cnt == 0) {
                        $qry_apnd .= '( a.Id = ' . $sel_id . ' ';
                    }
                    else {
                        $qry_apnd .= ' OR a.Id = ' . $sel_id . ' ';
                    }
                    $cnt++;
                }
                $qry_apnd .= ')';
                if ($cnt > 0) {
                    $this -> datatables -> where($qry_apnd);
                }
            }
            
            /* This condition is added if particular product is not having any combo products */
            if ($selected_combo_products == "{}") {
               $no_id = '-1'; 
               $this -> datatables -> where('a.Id',$no_id);
            }

            $this -> datatables -> group_by('a.Id');

            echo $this -> datatables -> generate();
    }
    
    
    public function combo_products_datatable_working() {
        $homebrand = 0;
        //$this -> datatables -> select("a.Id as p_id,b.CategoryName as MainCategory,c.CategoryName as ParentCategory,d.CategoryName as Category,case when a.HouseId is not null then concat(e.CompanyName,' ',a.ProductName) else a.ProductDescription end as ProductName,a.RRP as act_RRP,'0.00' as RRP, concat('<input class=\"prod_prc click_sel\" style=\"width:65px\" type=\"text\" id=\"product_price_',a.Id, '\" value=\"0.00\" />') as SpecialPrice, concat('<input class=\"coupon_amount click_sel\" style=\"width:65px\" type=\"text\" id=\"coupon_amount_',a.Id, '\" value=\"0.00\" />') as CouponAmount," . specials_get_select(0) . " as SpecialQty,", false)
        
        $this -> datatables -> select("a.Id as p_id,case when a.HouseId is not null then concat(e.CompanyName,' ',a.ProductName) else a.ProductDescription end as ProductName," . combo_products_get_select(0) . " as SpecialQty,", false)
            -> from('products as a')
            /*    
            -> join('categories as b', 'a.MainCategoryId = b.Id and b.IsActive = 1 and b.IsRemoved = 0')
            -> join('categories as c', 'a.ParentCategoryId = c.Id and c.IsActive = 1 and c.IsRemoved = 0')
            -> join('categories as d', 'a.CategoryId = d.Id and d.IsActive = 1 and d.IsRemoved = 0', 'left')             
             */
            -> join('retailers as e', 'e.Id = a.HouseId', 'left')
            -> where('(a.HouseId is null or a.HouseId = ' . $this -> session -> userdata('user_retailer_id') . ' )')
            -> where('a.IsActive', 1)
            -> where('a.IsRemoved', 0)
            //-> where('a.StoreTypeId', $this -> session -> userdata('user_store_format_id'))
            -> add_column('selectVal', '<input type="checkbox"  name="store_combo_products[]" value="$1" id="comboProd_$1"/>', 'p_id')
            -> add_column('Actions', get_customspecials_action_buttons('$1', 'customspecials'), 'p_id');
            
            if($homebrand > 0 )
            {
                $this -> datatables -> where('a.HouseId > 0');
            }
            
        # Added adanced search condition
        if($searchText)
        {
            $this -> datatables -> where($searchCond);
        }
        
        $this -> datatables -> group_by('a.Id');

        echo $this -> datatables -> generate();
    }
    
    
    /* Function used to save  special_combo_products for particular special product */
    public function add_to_special_combo_products_backup() {
        if ($this -> input -> server('REQUEST_METHOD') == 'POST') {
            $special_id = $this -> input -> post('special_id');
            $special_product_id = $this -> input -> post('special_product_id');
            $special_combo_products = $this -> input -> post('special_combo_products');
            
            $isAdded = $this -> specialproductmodel -> add_special_combo_products_backup($special_id,$special_product_id,$special_combo_products);
            
            $comboProductCount = $this -> specialproductmodel -> get_comboProductCount($special_id,$special_product_id);
            
            $this -> comboProductCount = $comboProductCount;
            $this -> result = 1;
            $this -> message = 'Combo Products added successfully.';
        }
        else {
            $this -> comboProductCount = 0;
            $this -> result = 0;
            $this -> message = 'Invalid data';
        }
        
        echo json_encode(array(
            'comboProductCount' => $this -> comboProductCount,
            'result' => $this -> result,
            'message' => $this -> message
        ));
    }
    
    public function get_special_combo_products_backup() {
        
        if ($this -> input -> server('REQUEST_METHOD') == 'POST') {
            $special_id = $this -> input -> post('special_id');
            $special_product_id = $this -> input -> post('special_product_id');
            
            $combo_products = $this -> specialproductmodel -> get_special_combo_products_backup($special_id,$special_product_id);
            
            if( $combo_products == "{}")
            {
                $this -> result = 0;
                $this -> message = 'No data'; 
            }else{
                $this -> result = 1;
                $this -> message = $combo_products['ComboProductsData'];
            }
        }
        else {
            $this -> result = 0;
            $this -> message = 'Invalid data';
        }
        echo json_encode(array(
            'result' => $this -> result,
            'message' => $this -> message
        ));
    }
}