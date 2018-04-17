<?php

/*
 * Author:  PN
 * Purpose: Filter products
 * Date:    07-10-2015
 */

class Filter extends My_Front_Controller {

    function __construct() {

        parent::__construct();
        $this -> load -> model('front/usermodel', '', TRUE);
        if (!$this -> usermodel -> check_email_entered($this -> session -> userdata('userid'))) {
            redirect(front_url() . 'registration/set_email');
            exit(0);
        }
    }

    public function quickshopping_list() {
        $this->load->model('front/quickshoppinglistmodel', '', TRUE);
        $this->load->model('front/productmodel', '', TRUE);

        $shopping_list = $this->input->post('shopping_list') ? $this->input->post('shopping_list') : "";

        if ((!empty($shopping_list)) || (!empty($this->session->userdata('shopping_list')))) {

            if (!empty($shopping_list)) {
                $shopping_list = preg_split('/\s+/', trim($shopping_list));
            } else {
                $shopping_list = explode(',',$this->session->userdata('shopping_list'));
            }
           
            $shopping_list_string = implode(",", $shopping_list);
            $data = array(
                'UserId' => $this->session->userdata('userid'),
                'ShoppingList' => $shopping_list_string,
                'CreatedOn' => date('Y-m-d H:i:s'),
            );

            //Save the user shopping list
            $this->quickshoppinglistmodel->save_list($this->session->userdata('userid'), $data);

            //Set Shopping List in Session
            $this->session->set_userdata('shopping_list', $shopping_list_string);

            // Search the products for the shopping list
            $products_to_search = $this->quickshoppinglistmodel->get_products_by_shopping_list($shopping_list);

            foreach ($products_to_search as $key => $products) {
                $product_string = array();

                foreach ($products as $products_list) {
                    $product_string[] = $products_list['Id'];
                }
                $products = array();

                // Get the product details
                if ($product_string)
                    $products = $this->productmodel->get_products("", "", $price_range = array(), 0, 0, $product_string);

                $i = 0; //Encode image of products
                foreach ($products as $product) {

                    if ($product['is_favorite'] != NULL) {
                        $products[$i]['is_favorite'] = "1";
                    } else {
                        $products[$i]['is_favorite'] = "0";
                    }

                    if ($product['avg_rating'] == NULL) {
                        $products[$i]['avg_rating'] = "0";
                    }
                    $i++;
                }

                $products_to_search[$key] = $products;
            }

            //Set data for view
            // Get product container data from common controller
            $data = $this->get_product_container_data();

            $data['products'] = $products_to_search;
            $data['is_top_offer'] = 0;
            
            $data['breadcrumbs'] = array(
            array(
                'name' => 'Quick Shopping List',
                'url' => ''
            )
        );

            $this->template->front_view('front/quickshoppinglist', $data, 1);
        }
    }

}

?>
