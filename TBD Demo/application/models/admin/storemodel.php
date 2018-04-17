<?php

/*
 * Author: Name:PM
 * Purpose: Model for controlling database interactions regarding the content.
 * Date:04-09-2015
 * Dependency: None
 */

class Storemodel extends CI_Model {
    /*
     * Purpose: Constructor.
     * Date: 04-09-2015
     * Input Parameter: None
     * Output Parameter: None
     */

    public function __construct() {
        parent::__construct();
    }

    public function get_stores($retailer_id = 0, $storeformat_id = 0) {
        $this -> db -> select('stores.StoreName,retailers.Id as RetailerId,storestypes.Id as StoreTypeId,retailers.CompanyName,stores.Id,stores.StreetAddress,stores.City,state.Name,stores.IsActive,CONCAT_WS(" ",stores.StreetAddress,stores.City,state.Name) as address', FALSE);
        $this -> db -> from('stores');
        $this -> db -> join('retailers', 'retailers.Id = stores.RetailerId');
        $this -> db -> join('state', 'state.Id = stores.StateId');
        $this -> db -> join('storestypes', 'storestypes.Id = stores.StoreTypeId');
        $this -> db -> where(array('stores.IsRemoved' => 0));

        if ($retailer_id > 0) {
            $this -> db -> where(array('stores.IsActive' => 1));
            $this -> db -> where(array('stores.RetailerId' => $retailer_id));
        }
        if ($storeformat_id > 0) {
            $this -> db -> where(array('stores.IsActive' => 1));
            $this -> db -> where(array('stores.StoreTypeId' => $storeformat_id));
        }
        $this -> db -> order_by("stores.StoreName");
        $query = $this -> db -> get();

        return $query -> result_array();
    }

    public function get_store_details($store_id) {
        $this -> db -> select('retailers.CompanyName,stores.*,state.Name, storestypes.StoreType');
        $this -> db -> from('stores');
        $this -> db -> join('retailers', 'retailers.Id = stores.RetailerId');
        $this -> db -> join('state', 'state.Id = stores.StateId');
        $this -> db -> join('storestypes', 'storestypes.Id = stores.StoreTypeId');
        $this -> db -> where('stores.Id', $store_id);
        $this -> db -> limit(1);
        $query = $this -> db -> get();

        if ($query -> num_rows() == 1) {
            return $query -> row_array();
        }
        else {
            return FALSE;
        }
    }

    public function update_store($store_id, $data) {
        $this -> db -> where('Id', $store_id);
        $this -> db -> update('stores', $data);
    }

    public function add_store($insert_data) {
        $this -> db -> insert('stores', $insert_data);
        return $this -> db -> insert_id();
    }

    public function delete_store($store_id) {

        $data = array('IsRemoved' => "1");
        $this -> db -> where('Id', $store_id);
        $this -> db -> update('stores', $data);

        return TRUE;
    }

    public function change_status($store_id, $status) {

        $data = array('IsActive' => $status);
        $this -> db -> where('Id', $store_id);
        $this -> db -> update('stores', $data);

        return TRUE;
    }

    public function get_store_by_name($name) {
        $this -> db -> select('stores.Id, stores.StoreTypeId');
        $this -> db -> from('stores');
        $this -> db -> where("stores.StoreName", $name);
        $this -> db -> limit(1);
        $query = $this -> db -> get();

        if ($query -> num_rows() == 1) {
            return $query -> row_array();
        }
        else {
            return FALSE;
        }
    }

    public function save_store_timing($store_id, $data) {

        //Del the previous records
        $this -> db -> where('StoreId', $store_id);
        $this -> db -> delete('storetimings');

        $open_hour = $data['OpenHours'];
        if (isset($data['Opendays'])) {
            if (!empty($data['Opendays'])) {
                for ($i = 1; $i <= 7; $i++) {
                    $value = 0;
                    if ($data) {

                        if (in_array($i, $data['Opendays']))
                            $value = 1;

                        $insert_data = array(
                            'StoreId' => $store_id,
                            'OpenCloseDay' => $i,
                            'OpenCloseStatus' => $value,
                            'OpenCloseTimeFrom' => $open_hour[($i - 1) * 2] . "-" . $open_hour[($i * 2) - 1]
                        );

                        $this -> db -> insert('storetimings', $insert_data);
                    }
                }
            }
        }
        return true;
    }

    public function get_store_timing($store_id) {

        $this -> db -> select('OpenCloseDay,OpenCloseTimeFrom,OpenCloseStatus');
        $this -> db -> from('storetimings');
        $this -> db -> where('StoreId', $store_id);
        $query = $this -> db -> get();

        if ($query -> num_rows() >= 1) {
            return $query -> result_array();
        }
        else {
            return FALSE;
        }
    }

    public function get_stores_by_store_format($store_format_id) {
        $this -> db -> select('stores.StoreName,stores.Id', FALSE);
        $this -> db -> from('stores');
        $this -> db -> join('storestypes', 'storestypes.Id = stores.StoreTypeId');
        $this -> db -> where(array('stores.IsRemoved' => 0, 'stores.IsActive' => 1));
        $this -> db -> where_in('stores.StoreTypeId', $store_format_id);
        $this -> db -> order_by("stores.StoreName");
        $query = $this -> db -> get();

        return $query -> result_array();
    }

    public function get_store_format($store_id) {
        $this -> db -> select('StoreTypeId');

        $this -> db -> where(
            array(
                'Id' => $store_id
        ));

        $query = $this -> db -> get('stores');

        return $query -> row() -> StoreTypeId;
    }

    public function update_user_count($id) {
        $this -> db -> set('UserCount', '`UserCount`+1', FALSE);
        $this -> db -> where('id', $id);
        $this -> db -> update('stores');
    }

    public function add_store_wizard($insert_data) {
        $this -> db -> insert('store_wizard_steps', $insert_data);
    }

    public function delete_store_user($user_id, $store_id) {

        $data = array('Id' => $user_id, 'StoreId' => $store_id);

        $this -> db -> delete('storeadmin', $data);

        $this -> db -> set('UserCount', '`UserCount`-1', FALSE);
        $this -> db -> where('id', $store_id);
        $this -> db -> update('stores');
    }

    public function get_wizard_steps() {
        $this -> db -> select('Step1,Step2,Step3');

        $this -> db -> where(
            array(
                'UserId' => $this -> session -> userdata('user_id'),
                'StoreId' => $this -> session -> userdata('user_store_id')
        ));

        $query = $this -> db -> get('store_wizard_steps');

        return $query -> row_array();
    }

    public function update_wizard_step($data) {
        $this -> db -> where(
            array(
                'UserId' => $this -> session -> userdata('user_id'),
                'StoreId' => $this -> session -> userdata('user_store_id')
        ));

        $this -> db -> update('store_wizard_steps', $data);
    }

    public function check_product_added($product_id, $retailer_id, $store_format_id, $store) {
        $this -> db -> select('Id')
            -> from('storeproducts')
            -> where(
                array(
                    'RetailerId' => $retailer_id,
                    'StoreTypeId' => $store_format_id,
                    'StoreId' => $store,
                    'ProductId' => $product_id,
                    'IsActive' => 1,
                    'IsRemoved' => 0
                )
        );
        $query = $this -> db -> get();

        if ($query -> num_rows() > 0) {
            return TRUE;
        }
        return FALSE;
    }

    public function add_product_batch($insert_data) {
        if ($this -> db -> insert_batch('storeproducts', $insert_data)) {
            return TRUE;
        }
        else {
            return FALSE;
        }
    }

    public function get_retailer_email_id_if_special($retailer_id, $state_id) {
        $this -> db -> select('e.Email')
            -> from('specials as a ')
            -> join('special_state as b', 'b.SpecialId = a.Id')
            -> join('special_stores as c', 'c.SpecialId = a.Id')
            -> join('retailers as d', 'd.Id = c.RetailerId')
            -> join('users as e', 'e.Id = d.RetailerAdminId')
            -> where('now() between SpecialFrom and SpecialTo', NULL, FALSE)
            -> where(
                array(
                    'd.IsActive' => 1,
                    'd.IsRemoved' => 0,
                    'e.IsActive' => 1,
                    'e.IsRemoved' => 0,
                    'c.RetailerId' => $retailer_id,
                    'b.StateId' => $state_id
                )
        );
        $query = $this -> db -> get();
        if ($query -> num_rows() > 0) {
            return $query -> row_array();
        }
        return FALSE;
    }

    public function get_retailer_new_stores($retailer_id) {
        $this -> db -> select('Id, StoreName, RetailerId, StoreTypeId')
            -> from('stores')
            -> where(
                array(
                    'RetailerId' => $retailer_id,
                    'IsActive' => 1,
                    'IsRemoved' => 0,
                    'IsNew' => 1
                )
        );
        $query = $this -> db -> get();
        if ($query -> num_rows() > 0) {
            return $query -> result_array();
        }
        return FALSE;
    }

    public function get_already_added_products_by_store($retailer_id, $store_id, $store_type_id) {
        $this -> db -> select('a.ProductId')
            -> from('storeproducts as a')
            -> join('stores as b', 'b.Id = a.StoreId')
            -> where(
                array(
                    'a.RetailerId' => $retailer_id,
                    'a.StoreTypeId' => $store_type_id,
                    'a.StoreId' => $store_id,
                    'b.IsActive' => 1,
                    'b.IsRemoved' => 0,
                    'b.IsNew' => 1
                )
        );
        $query = $this -> db -> get();
        if ($query -> num_rows() > 0) {
            return $query -> result_array();
        }
        return FALSE;
    }

    public function get_products_to_add_store($added_products) {
        $this -> db -> select('a.Id, a.RRP')
            -> from('products as a')
            -> join('categories as b', 'b.Id = a.MainCategoryId')
            -> join('categories as c', 'c.Id = a.ParentCategoryId')
            -> join('categories as d', 'd.Id = a.CategoryId and d.IsActive = 1 and d.IsRemoved = 0 ', 'left')
            -> where(
                array(
                    'a.IsActive' => 1,
                    'a.IsRemoved' => 0,
                    'b.IsActive' => 1,
                    'b.IsRemoved' => 0,
                    'c.IsActive' => 1,
                    'c.IsRemoved' => 0
                )
            )
            -> where('a.HouseId is null or a.HouseId = '.$this -> session -> userdata('user_retailer_id'), NULL, FALSE)
            -> where_not_in('a.Id', $added_products);
        $query = $this -> db -> get();
//        echo $this -> db -> last_query();die;
        if ($query -> num_rows() > 0) {
            return $query -> result_array();
        }
        return FALSE;
    }

    public function insert_update_promo($promo_array, $store_id = '', $retailer_id = '') {
        if ($retailer_id != '' || $store_id != '') {
            $this -> db -> select('*')
                -> from('promotions');
            if ($store_id) {
                $this -> db -> where('StoreId', $store_id);
            }
            elseif ($retailer_id) {
                $this -> db -> where('RetialerId', $retailer_id);
            }
            $this -> db -> limit(1);
            $query = $this -> db -> get();
            if ($query -> num_rows() > 0) {
                $res_arr = $query -> row_array();
                $this -> db -> where('Id', $res_arr['Id']);
                if ($this -> db -> update('promotions', $promo_array)) {
                    return TRUE;
                }
                return FALSE;
            }
            else {
                if ($this -> db -> insert('promotions', $promo_array)) {
                    return TRUE;
                }
                return FALSE;
            }
        }
        return FALSE;
    }

    public function get_store_promos($id) {
        $this -> db -> select('*')
            -> from('promotions')
            -> where(
                array(
                    'StoreId' => $id
                )
        );
        $query = $this -> db -> get();
        if ($query -> num_rows() > 0) {
            return $query -> row_array();
        }
        return array(
            'Id' => 0,
            'RetailerId' => 0,
            'StoreId' => $id,
            'Standard' => 1,
            'Premium' => 0,
            'Concierge' => 0,
            'Messenger' => 0,
            'AdMessenger' => 0
        );
    }
    
    /* 
     * Function to remove groups  and set new groups
     */
    public function set_storeGroups($storeId, $groupIds) {          
        if($groupIds)
        {
            # Delete previous store groups
            $this -> db -> where('StoreId', $storeId);        
            $this -> db -> delete('stores_storegroups');
        
            # Insert store Groups 
            foreach ($groupIds as $groupId) {
                $data = array(                
                    'StoreId' => $storeId,                
                    'StoreGroupId' => $groupId
                );

                $this -> db -> insert('stores_storegroups', $data);
                $ids =  $this -> db -> insert_id();
            }
        
        }
    }
    
    
    /*
     * Function to get store groups for the particular store
     */
    public function get_stores_storegroups($storeId) {
        $this->db->select('StoreGroupId');

        $this->db->where(
                array(
                    'StoreId' => $storeId
        ));
        $query = $this->db->get('stores_storegroups');
        //echo $this -> db -> last_query();die;
        
        if ($query->num_rows() > 0) {
            $results =  $query->result_array();
            $storeGroupIds = array();
            foreach ($results as $result)
            {
                $storeGroupIds[]=$result['StoreGroupId'];
            }
            return $storeGroupIds;
            
        } else {
            return FALSE;
        }
        
    }
}
