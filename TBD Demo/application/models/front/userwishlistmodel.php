<?php

/*
 * Author:  PM
 * Purpose: User wish list related functions
 * Date:    21-10-2015
 */

class Userwishlistmodel extends CI_Model {

    public function __construct()
    {
        parent::__construct();
    }

    /*  Function to get logged in user wishlist
     *  return - array - user wishlist names array
     */
    public function get_user_wish_list()
    {
        $this->db->select('Id,WishlistDescription');
        $this->db->from('userwishlists');
        $this->db->where( array( 'UserId' => $this->session->userdata('userid'), 'IsActive' => 1, 'IsRemoved' => 0 ) );
        $query = $this->db->get();

        return $query->result_array();
    }


    /*  Function to get logged in user wishlist
     *  return - array - user wishlist names array with details
     */
    public function get_user_wishlist_details($id=0) {
        $user_id = $this->session->userdata('userid');
        $this->db->select("userwishlists.Id,userwishlists.WishlistDescription,COUNT(userwishlistproducts.ID) AS products_count");
        $this->db->select("DATE_FORMAT(userwishlists.CreatedOn, '%d/%m/%Y') AS CreatedOn", FALSE);
        $this->db->join('userwishlistproducts', 'userwishlistproducts.UserWishlistId = userwishlists.Id', 'left');
        $this->db->where('userwishlists.UserId', $user_id);
        $this->db->where('userwishlists.IsActive', 1);
        $this->db->where('userwishlists.IsRemoved', 0);
        $this->db->group_by('userwishlists.Id');
         if($id>0) {
             $this->db->where('userwishlists.Id',$id);
        }

        $query = $this->db->get('userwishlists');



        return $query->result_array();
    }

    /*  Function to create user wishlist
     *  param - string : wishlist name
     *  return - id: id of inserted record
     */
    public function create_wishlist( $list )
    {
        $new_ins_data = array( 'UserId' => $this->session->userdata('userid'), 'WishlistDescription' => $list, 'CreatedBy' => $this->session->userdata('userid'), 'CreatedOn' => date('Y-m-d H:i:s'), 'IsActive' => 1 );

        $this->db->insert('userwishlists', $new_ins_data);
        return $this->db->insert_id();
    }

    /*  Function to create user wishlist
     *  param - string : wishlist name
     *  return - id: id of inserted record
     */
    public function add_to_wishlist( $ins_data )
    {
        // Check if product already added to same wishlist, do not add and show a message
        $this->db->select('Id');
        $this->db->from('userwishlistproducts');
        $this->db->where( array('UserId' => $ins_data['UserId'], 'SpecialId' => $ins_data['SpecialId'],'ProductId' => $ins_data['ProductId'], 'UserWishlistId' => $ins_data['UserWishlistId']) );

        $query = $this->db->get();
        if( $query->num_rows() > 0 )
        {
            return 'duplicate';
        }
        else
        {
            $this->db->insert('userwishlistproducts', $ins_data);
            return $this->db->insert_id();
        }
    }


     public function delete_wishlist($wishlist_id) {

        $data = array('Id' =>$wishlist_id );

        $this->db->delete('userwishlists', $data);

        return true;
    }


     public function delete_product_wishlist($wishlist_product_id) {

        $data = array('Id' =>$wishlist_product_id );

        $this->db->delete('userwishlistproducts', $data);

        return true;
    }

     public function get_user_wishlist_products($wishlistId) {

        $this->db->select('userwishlistproducts.Id,
                           userwishlistproducts.UserWishlistId,
                           products.Id AS ProductId,
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

        return $query->result_array();
    }
}

?>