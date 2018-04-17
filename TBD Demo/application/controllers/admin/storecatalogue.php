<?php

/*
 * Author:AS
 * Purpose:Store Catalogue Controller - Store format user login
 * Date:26-10-2015
 * Dependency: storecatalogue.php
 */

class Storecatalogue extends My_Controller {

    function __construct() {
        parent::__construct();

        $this -> load -> model('admin/storemodel', '', TRUE);
        $this -> load -> model('admin/retailermodel', '', TRUE);

        $this -> load -> model('admin/storeformatmodel', '', TRUE);

        $this -> page_title = "Store Catalogue";
    }

    public function index($id = '') {
        $data['title'] = $this -> page_title;
        $this -> breadcrumbs[0] = array('label' => 'Product Management', 'url' => '');
        $this -> breadcrumbs[1] = array('label' => 'Store Catalogue', 'url' => '/storecatalogue');
        $data['breadcrumbs'] = $this -> breadcrumbs;
        $this -> template -> view('admin/store_catalogue/index', $data);
    }

    public function datatable() {
        $this -> datatables -> select("a.Id,b.ProductName,d.StoreName,b.RRP,case when f.SpecialQty > 1 then concat(f.SpecialPrice,' (',f.SpecialQty,')') else f.SpecialPrice end as SpecialPrice,DATE_FORMAT(f.PriceAppliedFrom,'%d/%c/%Y') as PriceAppliedFrom,DATE_FORMAT(f.PriceAppliedTo,'%d/%c/%Y') as PriceAppliedTo", false)
            -> unset_column('Id')
            -> from('storeproducts as a')
            -> join('products as b', 'b.Id = a.ProductId')
            -> join('retailers as c', 'c.Id = a.RetailerId')
            -> join('stores as d', 'd.Id = a.StoreId')
            -> join('storestypes as e', 'e.Id = a.StoreTypeId')
            -> join('productspecials as f', 'f.ProductId = b.Id and f.RetailerId = c.Id and f.StoreId = d.Id and f.StoreTypeId = e.Id and (now() between f.PriceAppliedFrom and f.PriceAppliedTo)', 'left')
            -> where('a.StoreTypeId', $this -> session -> userdata('user_store_format_id'))
            -> add_column('selectVal', '<input type="checkbox" />');

        echo $this -> datatables -> generate();
    }
}