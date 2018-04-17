<?php

/*
 * Author:MK
 * Purpose:Cards Controller
 * Date:23-06-2017
 * Dependency: cardmodel.php
 */

class Cards extends My_Controller {

    function __construct() {
        parent::__construct();
        $this -> load -> model('admin/cardmodel', '', TRUE);
        
        $this -> page_title = "Cards";
        $this -> breadcrumbs[] = array('label' => 'Cards', 'url' => '/cards');
    }

    public function index() {
        $data['title'] = $this -> page_title;

        $this -> breadcrumbs[0] = array('label' => 'System Settings', 'url' => '');
        $this -> breadcrumbs[1] = array('label' => 'Cards', 'url' => '/cards');
        $data['breadcrumbs'] = $this -> breadcrumbs;

        $data['cards'] = $this -> cardmodel -> get_cards();
        $data['sequence_data'] = $this -> cardmodel -> get_max_min_sequence();
        
        $this -> template -> view('admin/cards/index', $data);
    }

    public function add() {

        if ($this -> input -> server('REQUEST_METHOD') == 'POST') {

           if (!empty($_FILES['card_image']['name'])) {
               $result = $this -> do_upload_slider('card_image', 'cards', '', '', '220', '360');
               if (!isset($result['error'])) { 
                    $data = array(
                            'CardImage' => $result['upload_data']['file_name'],
                            'CardTitle' => sanitize($this -> input -> post('CardTitle')),
                            'CardDescription' => sanitize($this -> input -> post('CardDescription')),
                            'Sequence' => $this -> cardmodel -> get_sequence(),
                            'CreatedBy' => $this -> session -> userdata('user_id')
                    ); 
               }else {
                    // code to display error while image upload
                    $this -> session -> set_userdata('error_message', $result['error']);
                    redirect('cards', 'refresh');
               }
           }else{
               $data = array(
                    'CardImage' => "",
                    'CardTitle' => sanitize($this -> input -> post('CardTitle')),
                    'CardDescription' => sanitize($this -> input -> post('CardDescription')),
                    'Sequence' => $this -> cardmodel -> get_sequence(),
                    'CreatedBy' => $this -> session -> userdata('user_id')
               ); 
           }
           
           $result = $this -> cardmodel -> add_card($data);
            if ($result > 0)
                $this -> session -> set_userdata('success_message', "Card added successfully");
            else
                $this -> session -> set_userdata('success_message', "Error while adding card");

            redirect('cards', 'refresh');
            
        }

        $this -> breadcrumbs[0] = array('label' => 'System Settings', 'url' => '');
        $this -> breadcrumbs[1] = array('label' => 'Cards', 'url' => '/cards');
        $this -> breadcrumbs[2] = array('label' => 'Add Card', 'url' => 'cards/add');

        $data['title'] = $this -> page_title;

        $data['breadcrumbs'] = $this -> breadcrumbs;

        $this -> template -> view('admin/cards/add', $data);
    }

    public function delete($id) {

        $this -> cardmodel -> delete_card($id);
        $this -> session -> set_userdata('success_message', "Card deleted successfully");
        redirect('cards', 'refresh');
    }

    public function change_status($id, $status) {
        
       
        $this -> cardmodel -> change_status($id, $status);
        $this -> session -> set_userdata('success_message', "Card status updated successfully");
        redirect('cards', 'refresh');
    }

    function update_card_sequence() {
        $id = $this -> input -> post('id');
        $type = $this -> input -> post('type');
        $update_array = json_decode($this -> input -> post('update_data'));

        $this -> cardmodel -> update_card_sequence($id, $type, $update_array);

        echo "success";
    }

    public function edit($id) {

        $data = $this -> cardmodel -> get_card_details($id);
        $this -> breadcrumbs[] = array('label' => 'Edit Card', 'url' => 'cards/edit/' . $id);

        $data['title'] = $this -> page_title;

        $data['breadcrumbs'] = $this -> breadcrumbs;

        $this -> load -> view('admin/cards/edit', $data);
    }

    public function edit_post($id) {
        if ($this -> input -> server('REQUEST_METHOD') == 'POST') {
            //Edit User

            $old_photo = sanitize($this -> input -> post('old_photo'));

            $image_name = $old_photo;
            if (!empty($_FILES['card_image']['name'])) {
                //$result = $this -> do_upload_slider('card_image', 'cards', '', '', '540', '882');
                $result = $this -> do_upload_slider('card_image', 'cards', '', '', '220', '360');
                if (!isset($result['error'])) {
                    $image_name = $result['upload_data']['file_name'];
                }
            }
            if (!isset($result['error'])) {
                $edit_data = array(
                    'CardImage' => $image_name,
                    'CardTitle' => sanitize($this -> input -> post('CardTitle')),
                    'CardDescription' => sanitize($this -> input -> post('CardDescription')),
                    'ModifiedBy' => $this -> session -> userdata('user_id'),
                    'ModifiedOn' => date('Y-m-d H:i:s')
                );
                $this -> cardmodel -> update_card_image($id, $edit_data);
                $this -> session -> set_userdata('success_message', "Card updated successfully");

                $this -> result = 1;
            }
            else {
                // code to display error while image upload
                //$this->session->set_userdata('error_message', $result['error']);
                $this -> result = 0;
                $this -> message = $result['error'];
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

    function file_selected_check() {

        $this -> form_validation -> set_message('file_selected_check', 'Please upload card image.');
        if (empty($_FILES['card_image']['name'])) {
            return false;
        }
        else {
            return true;
        }
    }
}
