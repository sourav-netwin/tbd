<?php

/*
 * Author: Name:PHN
 * Purpose: Model for controlling database interactions regarding the content.
 * Date:26-08-2015
 * Dependency: None
 */

class StoreAdminmodel extends CI_Model {
    /*
     * Purpose: Constructor.
     * Date: 26-08-2015
     * Input Parameter: None
     *  Output Parameter: None
     */

    public function __construct() {
        parent::__construct();
    }

    public function add_admin($data) {

        $this->db->insert('storeadmin', $data);

        $user_id = $this->db->insert_id();

        return $user_id;
    }

}

?>
