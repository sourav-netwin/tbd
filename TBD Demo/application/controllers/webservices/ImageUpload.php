<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/*
 * Author:PHN
 * Purpose: ImageUpload Webservices
 * Date:02-09-2015
 * Dependency: usermodel.php
 */

class ImageUpload extends CI_Controller {

    function __construct() {
        parent::__construct();

        ini_set('memory_limit', '400M');
        ini_set('upload_max_filesize', '400M');
        ini_set('post_max_size', '400M');
        ini_set('max_input_time', 7200);
        ini_set('max_execution_time', 7200);

        $this->load->model('webservices/usermodel', '', TRUE);
    }

    public function save_profile_image() {

        if (!empty($_FILES['profile_image']['name'])) {
            $profile_image = $this->uploadImage();
            $user_id = $this->input->post('user_id') ? $this->input->post('user_id') : "";

            // Check for user profile image
            if (array_key_exists('error', $profile_image)) {

                $retArr['status'] = FAIL;
                $retArr['message'] = IMAGE_UPLOAD_FAIL;
                $retArr['userDetails'] = array($profile_image);
                $this->response($retArr, 200); // 404 being the HTTP response code
                die;
            } else if (array_key_exists('filename', $profile_image)) {

                $update_data['ProfileImage'] = $profile_image['filename'];
            }
            $result = $this->usermodel->update_user($user_id, $update_data);

            $update_data['user_id'] = $user_id;

            if ($result) {
                if (isset($profile_image['filename']))
                    $update_data['profile_image'] = (front_url() . USER_IMAGE_PATH."medium/" . $profile_image['filename']);
                else
                    $update_data['profile_image'] = (front_url() . DEFAULT_USER_IMAGE_PATH);

                $retArr['status'] = SUCCESS;
                $retArr['message'] = PROFILE_UPDATE_SUCCESS;
                $retArr['userDetails'] = array($update_data);
                echo json_encode($retArr, 200); // 200 being the HTTP response code
            } else {
                $retArr['status'] = FAIL;
                $retArr['message'] = PROFILE_UPDATE_FAILED;
                $retArr['userDetails'] = IMAGE_UPLOAD_FAIL;
                echo json_encode($retArr, 200); // 404 being the HTTP response code
                die;
            }
        } else {
            $retArr['status'] = FAIL;
            $retArr['message'] = "mISSING PARAM";
            $retArr['userDetails'] = IMAGE_UPLOAD_FAIL;
            echo json_encode($retArr, 200); // 200 being the HTTP response code
            die;
        }
    }

    /**
     * uploadImage
     *
     * @return type
     */
    function uploadImage() {
        $data = array();

        if (!empty($_FILES['profile_image']['name'])) {
            if (!file_exists(USER_IMAGE_PATH)) {
                mkdir(USER_IMAGE_PATH, 0700, true);
            }
            $file_name = stripJunk($_FILES['profile_image']['name']); //preg_replace('/[^a-zA-Z0-9_.]/s', '', $_FILES['image']['name']);

            $medium_path = './assets/images/users/medium';
            $small_path = './assets/images/users/small';

            $medium_size = 500;
            $small_size = 200;

            $config = array(
                'allowed_types' => "*",
                'overwrite' => FALSE,
                'file_name' => $file_name
            );
            $config['upload_path'] = './assets/images/users/original';

            $config['max_size'] = '1000000';
            $config['max_width'] = '1024000';
            $config['max_height'] = '768000';

            $this->load->library('upload', $config);
            $this->upload->initialize($config);
            $this->load->library('image_lib');
            if ($this->upload->do_upload('profile_image')) {
                $upload_data = $this->upload->data();
                $data = array('filename' => $upload_data['file_name']);

                //Medium
                $config = array(
                    'source_image' => $upload_data['full_path'], //path to the uploaded image
                    'new_image' => $medium_path, //path to new medium image
                    'maintain_ratio' => true,
                    'width' => $medium_size,
                    'height' => $medium_size
                );

                $this->image_lib->initialize($config);
                $this->image_lib->resize();

                //Small
                $config = array(
                    'source_image' => $upload_data['full_path'], //path to the uploaded image
                    'new_image' => $small_path, //path to new thumb image
                    'maintain_ratio' => true,
                    'width' => $small_size,
                    'height' => $small_size
                );

                $this->image_lib->initialize($config);
                $this->image_lib->resize();
            } else {
                $data = array('error' => $this->upload->display_errors());
            }
        } else {
            $data = array('error' => " profile_image not found");
        }
        return $data;
    }

}

?>
