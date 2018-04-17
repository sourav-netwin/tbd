<?php

/*
 * Author:  PM
 * Purpose: Email Template related functions
 * Date:    14-10-2015
 */

class Emailtemplatemodel extends CI_Model {
    
    public function __construct() 
    {
        parent::__construct();
    }

    /* Function to get email template details
     * Param: id - Email template id
     * Return: id - Array of details
     */
    public function get_email_template_details( $email_template_id )
    {
        $this->db->select("*, title as heading, if( EmailTo = 1, 'User', 'Admin' ) as EmailTo", FALSE);
        $this->db->from('emailtemplate');
        $this->db->where( 'emailtemplate.Id', $email_template_id );
        $this->db->limit(1);
        $query = $this->db->get();
     
        if ($query->num_rows() == 1) 
        {
            return $query->row_array();
        } 
        else
        {
            return FALSE;
        }
    }
}
?>