<?php

/*
 * Author: Name:PM
 * Purpose: Model for controlling database interactions regarding the content.
 * Date:04-09-2015
 * Dependency: None
 */

class Categorymodel extends CI_Model {
    /*
     * Purpose: Constructor.
     * Date: 04-09-2015
     * Input Parameter: None
     * Output Parameter: None
     */

    public function __construct() {
        parent::__construct();
    }

    public function get_all_categories( $show_deleted = 0 ) {
        $this->db->select('Id,CategoryName,ParentCategory,IsActive,Sequence');

        if( $show_deleted == 0 )
        {
            $this->db->where(
                array(
                    'IsRemoved' => 0,
//                    'IsActive' => 1
            ));
        }
        $this->db->order_by('CategoryName');
        //$this->db->order_by('Sequence');
        $query = $this->db->get('categories');

        return $query->result_array();
    }

    public function get_category_details($category_id) {
        $this->db->from('categories');
        $this->db->where('Id', $category_id);
        $this->db->limit(1);
        $query = $this->db->get();

        if ($query->num_rows() == 1) {
            return $query->row_array();
        } else {
            return FALSE;
        }
    }

    public function update_category($category_id,$data) {

        $this->db->where('Id', $category_id);
        $this->db->update('categories', $data);
    }

    public function add_category( $data ) {
        $this->db->insert('categories', $data);
        return $this->db->insert_id();
    }

    public function delete_category($category_id) {

        $data = array('IsRemoved' => "1");
        $this->db->where('Id', $category_id);
        $this->db->update('categories', $data);

        $this->db->where('ParentCategory', $category_id );
        $this->db->update('categories', $data);

        return TRUE;
    }

    public function change_status($category_id, $status) {

        $data = array('IsActive' => $status);
        $this->db->where('Id', $category_id);
        $this->db->update('categories', $data);

        $this->db->where('ParentCategory', $category_id );
        $this->db->update('categories', $data);

        return TRUE;
    }

    public function validate_categories( $categories )
    {
        $res_arr = array();

        $this->db->select('Id,CategoryName');
        $this->db->from('categories');
        $this->db->where_in('CategoryName', $categories);
        $query = $this->db->get();

        foreach ($query->result_array() as $res)
        {
            $res_arr[$res['CategoryName']] = $res['Id'];
        }

        return $res_arr;
    }

    public function get_main_categories() {
        $this->db->select('Id,CategoryName');

        $this->db->where(
            array(
                'IsRemoved' => 0,
                'IsActive' => 1,
                'ParentCategory' => 0
        ));

        $this->db->order_by("CategoryName");
        $query = $this->db->get('categories');

        return $query->result_array();
    }

    public function get_parent_categories( $parent_category_id )
    {
        $this->db->select('Id,CategoryName');

        $this->db->where(
            array(
                'IsRemoved' => 0,
//                'IsActive' => 1,
                'ParentCategory' => $parent_category_id
        ));

        $this->db->order_by("CategoryName");
        $query = $this->db->get('categories');

        return $query->result_array();
    }

    public function get_sequence( $parent_category_id )
    {
        $this->db->select("IF(ISNULL( Max(Sequence) ) , 0 , Max(Sequence)) as Sequence",false);
        $this->db->where( array('ParentCategory' => $parent_category_id) );
        $this->db->limit(1);
        $query = $this->db->get('categories');

        return ( $query->row()->Sequence + 1 );
    }

    public function update_category_sequence( $id, $type )
    {
        $this->db->select("c1.Id as newID , c1.Sequence as newSeq, c.Id, c.Sequence");
        $this->db->from("categories c");
        $this->db->join("categories c1", "c1.ParentCategory = c.ParentCategory");
        if( $type == "up" )
        {
            $this->db->where(array( "c.Id" => $id, "c1.Sequence <" => "c.Sequence" ), '', FALSE);
            $this->db->order_by("c1.Sequence DESC");
        }
        else
        {
            $this->db->where(array( "c.Id" => $id, "c1.Sequence >" => "c.Sequence" ), '', FALSE);
            $this->db->order_by("c1.Sequence ASC");
        }

        $this->db->limit(1);
        $query = $this->db->get();

        $newID = $query->row()->newID;
        $updated_new_sequence = $query->row()->Sequence;

        $currentID = $query->row()->Id;
        $updated_current_sequence = $query->row()->newSeq;

        $this->db->update("categories", array( "Sequence" => $updated_new_sequence ), array("ID" => $newID ) );
        $this->db->update("categories", array( "Sequence" => $updated_current_sequence ), array("ID" => $currentID ) );
    }


    public function get_retailer_categories($retailer_id) {
        $this->db->select('categories.Id,categories.CategoryName');
        $this->db->join("categories", "categories.Id = retailercategories.CategoryID");
        $this->db->where(
            array(
                'categories.IsRemoved' => 0,
                'categories.IsActive' => 1,
                'retailercategories.IsActive' => 1,
                'retailercategories.RetailerId' => $retailer_id
        ));

        $query = $this->db->get('retailercategories');

        return $query->result_array();
    }
    public function get_retailer_image($retailer_id) {
        $this->db->select('LogoImage');
        $this->db->from('retailers');
        $this->db->where(
            array(
                'IsRemoved' => 0,
                'IsActive' => 1,
                'Id' => $retailer_id
        ));

        $query = $this->db->get();

        return $query->result_array();
    }
    
    
    /* 
     * Function to remove groups  set new groups
     */
    public function set_storeGroups($categoryId, $groupIds) {          
        if($groupIds)
        {
            # Delete previous store groups
            $this -> db -> where('CategoryId', $categoryId);        
            $this -> db -> delete('categories_storegroups');
        
            # Insert store Groups 
            foreach ($groupIds as $groupId) {
                $data = array(                
                    'CategoryId' => $categoryId,                
                    'StoreGroupId' => $groupId
                );

                $this -> db -> insert('categories_storegroups', $data);
                $ids =  $this -> db -> insert_id();
            }
        
        }
    }
    
    
    /*
     * Function to get store groups for the particular category
     */
    public function get_category_storegroups($categoryId) {
        $this->db->select('StoreGroupId');

        $this->db->where(
                array(
                    'CategoryId' => $categoryId
        ));
        $query = $this->db->get('categories_storegroups');
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
?>