<?php

/*
 * Author:PM
 * Purpose:Slider Controller
 * Date:07-10-2015
 * Dependency: slidermodel.php
 */

class Slider extends My_Controller {

    function __construct() {
        parent::__construct();

        $this->load->model('admin/slidermodel', '', TRUE);

        $this->page_title = "Sliders";
        $this->breadcrumbs[] = array('label' => 'Sliders', 'url' => '/sliders');
    }

    public function index() {
        $data['title'] = $this->page_title;

        $this -> breadcrumbs[0] = array('label' => 'System Settings', 'url' => '');
        $this -> breadcrumbs[1] = array('label' => 'Slider Management', 'url' => '/slider');
        $data['breadcrumbs'] = $this->breadcrumbs;

        $data['sliders'] = $this->slidermodel->get_sliders();

        $data['sequence_data'] = $this->slidermodel->get_max_min_sequence();

        $this->template->view('admin/slider/index', $data);
    }

    public function add() {

        if ($this->input->server('REQUEST_METHOD') == 'POST') {

            // $this->form_validation->set_rules('slider_image', 'Document', 'callback_file_selected_check');

            // if (!$this->form_validation->run() == FALSE) {
            //     echo"In if";die();
                $result = $this->do_upload_slider('slider_image', 'sliders');
                if (!isset($result['error'])) {

                    $data = array(
                        'Image' => $result['upload_data']['file_name'],
                        'Sequence' => $this->slidermodel->get_sequence(),
                        'CreatedBy' => $this->session->userdata('user_id'),
                        'CreatedOn' => date('Y-m-d H:i:s'),
                        'IsActive' => 1
                    );

                    $result = $this->slidermodel->add_slider($data);
                    if( $result > 0 )
                        $this->session->set_userdata('success_message', "Slider added successfully");
                    else
                        $this->session->set_userdata('success_message', "Error while adding slider");

                    redirect('slider', 'refresh');
                } else {
                    // code to display error while image upload
                    $this->session->set_userdata('error_message', $result['error']);
                }
            // }
        }

        $this -> breadcrumbs[0] = array('label' => 'System Settings', 'url' => '');
        $this -> breadcrumbs[1] = array('label' => 'Slider Management', 'url' => '/slider');
        $this->breadcrumbs[2] = array('label' => 'Add Slider', 'url' => 'slider/add');

        $data['title'] = $this->page_title;

        $data['breadcrumbs'] = $this->breadcrumbs;

        $this->template->view('admin/slider/add', $data);
    }

    public function delete($id) {

        $this->slidermodel->delete_slider($id);
        $this->session->set_userdata('success_message', "Slider deleted successfully");
        redirect('slider', 'refresh');
    }

    public function change_status($id, $status) {

        $this->slidermodel->change_status($id, $status);
        $this->session->set_userdata('success_message', "Slider status updated successfully");
        redirect('slider', 'refresh');
    }

    // function file_selected_check() {

    //     echo "In fsdf"; die();
    //     $this->form_validation->set_message('file_selected_check1', 'Please upload slider image.');
    //     if (empty($_FILES['slider_image']['name'])) {
    //         echo"In 1213"; die();
    //         return false;
    //     } else {
    //         echo"In 124564613"; die();
    //         return true;
    //     }
    // }

    function update_slider_sequence()
    {
        $id = $this->input->post('id');
        $type = $this->input->post('type');
        $update_array = json_decode($this -> input -> post('update_data'));

        $this->slidermodel->update_slider_sequence($id, $type, $update_array);

        echo "success";
    }
    
    
    public function edit($id) {

        $data = $this -> slidermodel -> get_sider_details($id);
        $this -> breadcrumbs[] = array('label' => 'Edit Slider', 'url' => 'slider/edit/' . $id);

        $data['title'] = $this -> page_title;

        $data['breadcrumbs'] = $this -> breadcrumbs;

        $this -> load -> view('admin/slider/edit', $data);
    }

    public function edit_post($id) {
        if ($this -> input -> server('REQUEST_METHOD') == 'POST') {
            //Edit User

            if (empty($_FILES['slider_image']['name'])) {
                $this -> result = 0;
                $this -> message = 'Select a new slider image to update';
            }
            else {
                if (!empty($_FILES['slider_image']['name'])) {
                    $result = $this -> do_upload('slider_image', 'sliders','','','1530','649');
                    if (!isset($result['error'])) {
                        $edit_data = array(
                            'Image' => $result['file_name']
                        );
                        $this -> slidermodel -> update_slider_image($id, $edit_data);
                        $this -> session -> set_userdata('success_message', "Slider updated successfully");

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
                    $this -> message = 'No changes has been made';
                }
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

        $this -> form_validation -> set_message('file_selected_check', 'Please upload slider image.');
        if (empty($_FILES['slider_image']['name'])) {
            return false;
        }
        else {
            return true;
        }
    }
}

?>