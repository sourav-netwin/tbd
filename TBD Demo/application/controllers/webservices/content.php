<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';
/*
 * Author:PHN
 * Purpose: Content Webservices
 * Date:02-09-2015
 * Dependency: usermodel.php
 */

class Content extends REST_Controller {

    function __construct() {
        parent::__construct();

        $api_key = $this->post('api_key');

        validateApiKey($api_key);

        $retArr = array();

        $this->load->model('webservices/contentmodel', '', TRUE);
    }

    public function get_content_post() {

        $menu_id = $this->post('menu_id') ? $this->post('menu_id') : "";

        $content = $this->contentmodel->get_menu_content($menu_id);

        $retArr['status'] = SUCCESS;
        $retArr['content'] = ($content);
        $this->response($retArr, 200); // 200 being the HTTP response code
        die;
    }
    
    public function get_front_slider_post(){
        $slider_details = $this -> contentmodel -> get_sliders();
        
        if(!empty($slider_details)){
            foreach($slider_details as $key => $val){
                $slider_details[$key]['Image'] = base_url().'../'.MOBILE_SLIDER_IMAGE_PATH.$slider_details[$key]['Image'];
            }
        }
        
        $retArr['status'] = SUCCESS;
        $retArr['slider'] = $slider_details;
        $this->response($retArr, 200); // 200 being the HTTP response code
        die;
    }
    
    /* Function to get cards information */
    public function get_cards_post(){
        $cards = $this -> contentmodel -> get_cards();
        
        if(!empty($cards)){
            foreach($cards as $key => $val){
                if($cards[$key]['CardImage'] !="" ){
                    $cards[$key]['CardImage'] = base_url().'../'.CARDS_IMAGE_PATH.$cards[$key]['CardImage'];
                }
                
                $CardDescription = str_replace("\'", "'", $cards[$key]['CardDescription']);

                $cards[$key]['CardDescription'] = $CardDescription;
            }
        }
        
        $retArr['status'] = SUCCESS;
        $retArr['cards'] = $cards;
        $this->response($retArr, 200); // 200 being the HTTP response code
        die;
    }

}
