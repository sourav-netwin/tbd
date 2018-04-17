<?php

/*
 * Author: Name:PM
 * Purpose: Model for controlling database interactions regarding the content.
 * Date:11-09-2015
 * Dependency: None
 */

class Emailtemplatemodel extends CI_Model {
    /*
     * Purpose: Constructor.
     * Date: 11-09-2015
     * Input Parameter: None
     * Output Parameter: None
     */

    public function __construct() {
        parent::__construct();
    }

    public function get_email_template_details($email_template_id) {
        $this->db->select("*, title as heading, if( EmailTo = 1, 'User', 'Admin' ) as EmailTo", FALSE);
        $this->db->from('emailtemplate');
        $this->db->where( 'emailtemplate.Id', $email_template_id );
        $this->db->limit(1);
        $query = $this->db->get();
     
        if ($query->num_rows() == 1) {
            return $query->row_array();
        } else {
            return FALSE;
        }
    }

    public function update_email_template($email_template_id,$data) {

        $this->db->where('Id', $email_template_id);
        $this->db->update('emailtemplate', $data);
    }

    public function change_status($email_template_id, $status) {

        $data = array('IsActive' => $status);
        $this->db->where('Id', $email_template_id);
        $this->db->update('emailtemplate', $data);

        return TRUE;
    }
}
?>