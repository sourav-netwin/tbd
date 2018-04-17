<?php

/*
 * Author: Name:PHN
 * Purpose: Model for controlling database interactions regarding the content.
 * Date:26-08-2015
 * Dependency: None
 */

class WishListmodel extends CI_Model {
    /*
     * Purpose: Constructor.
     * Date: 26-08-2015
     * Input Parameter: None
     *  Output Parameter: None
     */

    public function __construct() {
        parent::__construct();
    }

    public function get_user_wishlist($user_id, $product_id = '') {

        $this->db->select("userwishlists.Id,userwishlists.WishlistDescription,COUNT(userwishlistproducts.ID) AS products_count");
        $this->db->select("DATE_FORMAT(userwishlists.CreatedOn, '%d/%m/%Y') AS CreatedOn, sum(case when userwishlistproducts.ProductId = $product_id then 1 else 0 end) as IsInList", FALSE);
        $this->db->join('userwishlistproducts', 'userwishlistproducts.UserWishlistId = userwishlists.Id', 'left');
        $this->db->where('userwishlists.UserId', $user_id);
        $this->db->where('userwishlists.IsActive', 1);
        $this->db->where('userwishlists.IsRemoved', 0);
        $this->db->group_by('userwishlists.Id');
        $query = $this->db->get('userwishlists');

        return $query->result_array();
    }

    public function add_wishlist($data) {

        $this->db->insert('userwishlists', $data);

        return $this->db->insert_id();
    }

    public function update_wishlist($id, $data) {

        $this->db->where('Id', $id);
        $this->db->update('userwishlists', $data);
        return $id;
    }

    public function delete_wishlist($id) {
        $data = array('IsRemoved' => '1');
        $this->db->where('Id', $id);
        $this->db->update('userwishlists', $data);

        //Delete the products related to wishlist
        $this->db->delete('userwishlistproducts', array('UserWishlistId' => $id));

        return $id;
    }
    
    public function delete_wishlists($ids) {
        $this -> db -> where_in('Id', $ids);
        //Delete the notifications
        $this -> db -> delete('userwishlists');
        
        $this -> db -> where_in('UserWishlistId', $ids);
        //Delete the notifications
        $this -> db -> delete('userwishlistproducts');
    }

    public function add_product_wishlist($data) {

        //Check if product already added to wishlist
        $this->db->select(array(
            'Id'
        ));
        $this->db->from('userwishlistproducts');
        $this->db->where($data);
        $this->db->limit(1);
        $query = $this->db->get();

        if (!($query->num_rows() == 1)) {

            //If product not added add to wishlist
            $this->db->insert('userwishlistproducts', $data);

            $data = array('CreatedOn' => date("Y-m-d H:i:s"));

            return $this->db->insert_id();

        } else {

            return FALSE;
        }
    }

    public function delete_product_wishlist($data) {

        $this->db->delete('userwishlistproducts', $data);

        return true;
    }

     public function get_user_wishlist_products($wishlistId) {

         /*
        $this->db->select('products.Id AS ProductId,
                           products.ProductName,
                           products.ProductImage,
                           products.RRP,
                           products.ProductDescription,
                           COUNT(productsreviews.ID) AS reviews_count,
                           AVG(productsreviews.rating) AS avg_rating,
                           categories.CategoryName,
                           retailers.Id as RetailerId,
                           retailers.CompanyName,
                           retailers.LogoImage,
                           stores.StoreName,
                           storeproducts.Price AS store_price,
                           productspecials.SpecialQty,
                           productspecials.SpecialPrice');

        $this->db->join('productsreviews', 'productsreviews.ProductId = userwishlistproducts.Id', 'left');
        $this->db->join('products', 'products.Id = userwishlistproducts.ProductId');
        $this->db->join('categories', 'categories.Id = products.CategoryId', 'left');
        $this->db->join('retailers', 'retailers.Id = userwishlistproducts.RetailerId', 'left');
        $this->db->join('stores', 'stores.Id = userwishlistproducts.StoreId', 'left');
        $this->db->join('storeproducts', 'storeproducts.ProductId = products.Id AND storeproducts.RetailerId = userwishlistproducts.RetailerId AND (storeproducts.StoreId=userwishlistproducts.StoreId OR (storeproducts.StoreId=0 AND storeproducts.PriceForAllStores=1)) AND storeproducts.IsActive=1');
        $this->db->join('productspecials', 'productspecials.ProductId = products.Id AND DATE(productspecials.PriceAppliedFrom) <= "' . date('Y-m-d') . '" AND DATE(productspecials.PriceAppliedTo) >= "' . date('Y-m-d') . '" AND productspecials.RetailerId =userwishlistproducts.RetailerId  AND (productspecials.StoreId=userwishlistproducts.StoreId OR (productspecials.StoreId=0 AND productspecials.PriceForAllStores=1)) AND productspecials.IsActive=1 AND productspecials.IsApproved =1', 'left');

        $this->db->where(array(
            'products.IsActive' => 1,
            'products.IsRemoved' => 0,
            'userwishlistproducts.UserWishlistId' => $wishlistId,
        ));

        $this->db->group_by('userwishlistproducts.RetailerId,products.CategoryId,products.Id');

        $query = $this->db->get('userwishlistproducts');
        */
         
         //products.ProductName,
         //case when products.HouseId is null then products.ProductDescription else concat(retailers.CompanyName,' ',products.ProductName) end as ProductName,
         
         $this->db->select("products.Id AS ProductId,
                           case when products.HouseId is null then products.ProductDescription else concat(retailers.CompanyName,' ',products.ProductName) end as ProductName,
                           products.ProductImage,
                           products.RRP,
                           products.ProductDescription,
                           COUNT(productsreviews.ID) AS reviews_count,
                           AVG(productsreviews.rating) AS avg_rating,
                           case when categories.CategoryName is null then '' else categories.CategoryName end as CategoryName,
                           retailers.Id as RetailerId,
                           retailers.CompanyName,
                           retailers.LogoImage,
                           stores.StoreName,
                           storeproducts.Price AS store_price,
                           productspecials.SpecialQty,
                           productspecials.SpecialPrice,
                           specials.IsStore,
                           specials.Id as special_id",FALSE);

        $this->db->join('productsreviews', 'productsreviews.ProductId = userwishlistproducts.Id', 'left');
        $this->db->join('products', 'products.Id = userwishlistproducts.ProductId');
        $this->db->join('categories', 'categories.Id = products.CategoryId', 'left');
        $this->db->join('retailers', 'retailers.Id = userwishlistproducts.RetailerId', 'left');
        $this->db->join('stores', 'stores.Id = userwishlistproducts.StoreId', 'left');
        $this->db->join('storeproducts', 'storeproducts.ProductId = products.Id AND storeproducts.RetailerId = userwishlistproducts.RetailerId AND (storeproducts.StoreId=userwishlistproducts.StoreId OR (storeproducts.StoreId=0 AND storeproducts.PriceForAllStores=1)) AND storeproducts.IsActive=1');
        $this->db->join('productspecials', 'productspecials.ProductId = products.Id AND DATE(productspecials.PriceAppliedFrom) <= "' . date('Y-m-d') . '" AND DATE(productspecials.PriceAppliedTo) >= "' . date('Y-m-d') . '" AND productspecials.RetailerId =userwishlistproducts.RetailerId  AND (productspecials.StoreId=userwishlistproducts.StoreId OR (productspecials.StoreId=0 AND productspecials.PriceForAllStores=1)) AND productspecials.IsActive=1 AND productspecials.IsApproved =1', 'left');
        $this -> db -> join('specials', 'specials.Id = productspecials.SpecialId and specials.IsActive = 1 and specials.IsRemoved = 0','left');
        
        $this->db->where(array(
            'products.IsActive' => 1,
            'products.IsRemoved' => 0,
            'userwishlistproducts.UserWishlistId' => $wishlistId,
        ));

        $this->db->group_by('userwishlistproducts.RetailerId,products.CategoryId,products.Id,userwishlistproducts.SpecialId');
        $this -> db -> order_by('products.ProductName', 'ASC');
        
        $query = $this->db->get('userwishlistproducts');
        //echo $this->db->last_query();exit;
        return $query->result_array();
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
            $emptyArr = array();
            return (object) $emptyArr;
            //return array();
        }
    }
}