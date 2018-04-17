<?php

/*
 * Author:MK
 * Purpose:Loyalty Products Controller
 * Date:01-03-2017
 * Dependency: loyaltyproductmodel.php
 */
//error_reporting(E_ALL);
//ini_set('display_errors', 1);
class Loyaltyorders extends My_Controller {

    private $message;
    private $result;

    function __construct() {
        parent::__construct();
        
        # Load required models
        
        $this -> load -> model('admin/loyaltyordermodel', '', TRUE);
        $this -> load -> model('admin/loyaltybrandmodel', '', TRUE);
        $this -> load -> model('admin/loyaltycategorymodel', '', TRUE);        
        $this -> load -> model('admin/loyaltyproductmodel', '', TRUE);

        # Set default values
        $this -> page_title = "Loyalty Orders";
        $this -> breadcrumbs[] = array('label' => 'Loyalty Orders', 'url' => '/loyaltyorders');

        if ($this -> session -> userdata('user_type') == 6) {
            $this -> check_wizard_navigation();
        }
    }

    /*
     * Method Name: index
     * Purpose: Shows all loyalty products 
     * params:
     *      input: 
     *      output: status - FAIL / SUCCESS
     *              message - 
     */
    
    
    public function index() {
        # Set page title
        $data['title'] = $this -> page_title;

        # Set breadcrumbs
        $this -> breadcrumbs[0] = array('label' => 'Loyalty Management', 'url' => '');
        $this -> breadcrumbs[1] = array('label' => 'Loyalty Orders', 'url' => '/loyaltyorders');
        $data['breadcrumbs'] = $this -> breadcrumbs;
         $unReadOrders = $this -> loyaltyordermodel -> getunReadOrders();
		 $this -> session -> set_userdata('unReadOrders', 0);
		 $this -> session -> set_userdata('unReadOrders', $unReadOrders);
        $this -> template -> view('admin/loyaltyorders/index', $data);
    }

    /*
     * Method Name: datatable
     * Purpose: Get loyalty orders
     * params:
     *      input: 
     *      output: status - FAIL / SUCCESS
     *              message - 
     */
    
    public function datatable() {
        $this -> datatables -> select("o.LoyaltyOrderId as Id,o.OrderNumber, CONCAT_WS( ' ', u.FirstName, u.LastName ) as Name,u.Email,u.Mobile,o.OrderTotal,  case when o.OrderStatus = 1 then 'Cancelled' when o.OrderStatus = 2 then 'Dispatched' else 'Received' end as OrderStatus, o.CreatedOn,o.isAdminReviewed", FALSE)         
            -> from('loyalty_orders as o')            
            -> join('users as u', 'u.Id = o.UserId and u.IsActive = 1 and u.IsRemoved = 0', 'left')    
            -> add_column('Actions', get_loyalty_order_action_buttons('$1', 'loyaltyorders'), 'Id');
			
        $cond = array(
           'u.IsRemoved' => '0'
        );   
       
        $this -> datatables -> where($cond);

        echo $this -> datatables -> generate();
    }
    
    /*
     * Method Name: edit
     * Purpose: Get loyalty order information for edit
     * params:
     *      input: $id
     *      output: status - FAIL / SUCCESS
     *              message - The reason if loyalty products not found fails / Success message
     *              loyaltyProduct - Array containing all active loyalty products.
     */
    
    public function edit($id) {
        # Get Order deatils 
		
        $data = $this -> loyaltyordermodel -> get_order_details($id);
		 $upData['isAdminReviewed']='1';
         $this->db->where('LoyaltyOrderId', $id);
         $this->db->update('loyalty_orders', $upData);
		
        $shippingAddress = "";
        if($data)
        {
           //$shippingAddress = $data['HouseNumber']!='' ? $data['HouseNumber'].", " : "";
           $shippingAddress = $data['StreetAddress']!='' ? $shippingAddress.$data['StreetAddress']: "";
           //$shippingAddress = $data['city']!='' ? $shippingAddress.$data['city'].", " : "";
           //$shippingAddress = $data['stateName']!='' ? $shippingAddress.$data['stateName'].", " : "";
           //$shippingAddress = $data['countryName']!='' ? $shippingAddress.$data['countryName'].", " : "";
        }
        
        $data['shippingAddress'] = $shippingAddress;
                
        # Get Order deatils 
        $orderProducts = $this -> loyaltyordermodel -> get_order_products($id);
        
        $data['orderProducts'] = $orderProducts;
        
       /*
        echo "<pre>";
        print_r($orderProducts);
        exit;
         */
        
        #Set values 
        $this -> breadcrumbs[] = array('label' => 'Edit Order', 'url' => 'loyaltyorders/edit/' . $id);

        $data['title'] = $this -> page_title;
        $data['breadcrumbs'] = $this -> breadcrumbs;       
        
        $this -> load -> view('admin/loyaltyorders/edit', $data);
    }

    /*
     * Method Name: edit_post
     * Purpose: Edit loyalty product information.
     * params:
     *      input: $id
     *      output: status - FAIL / SUCCESS
     *              message - The reason if loyalty products not found fails / Success message
     *              loyaltyProduct - Array containing all active loyalty products.
     *      Note :
     *      Order status. 0:Received, 1:Cancelled, 2:Dispatched 
     */
    
    public function edit_post($id) {
        if ($this -> input -> server('REQUEST_METHOD') == 'POST') {
            //Edit Product            
            $this -> form_validation -> set_rules('OrderStatus', 'Order Status', 'trim|required|xss_clean');            

            if (!$this -> form_validation -> run() == FALSE) {                    
                
                $orderStatus = $this -> input -> post('OrderStatus');
                $voucherCode = $this -> input -> post('voucherCode');
                
                //Order status. 0:Received, 1:Cancelled, 2:Dispatched 
                switch ($orderStatus) {
                    case 1: // Cancelled
                        $edit_data = array(
                             'OrderStatus' => $orderStatus, 
                             'VoucherCode' => '',
                             'ModifiedOn' => date('Y-m-d H:i:s'),   
                             'isOrderCancelled' => 1,                                                                                   
                             'cancelledBy' => $this -> session -> userdata('user_id'),
                             'cancelledOn' => date('Y-m-d H:i:s'),
                             'isOrderDispatched' => 0,                                                                                   
                             'dispatchedBy' => 0,
                             'dispatchedOn' => "0000-00-00 00:00:00"
                        );                                
                        break;
                    case 2: // Dispatched
                        $edit_data = array(
                            'OrderStatus' => $orderStatus,
                            'VoucherCode' => $voucherCode,
                            'ModifiedOn' => date('Y-m-d H:i:s'),
                            'isOrderCancelled' => 0,                                                                                   
                            'cancelledBy' => 0,
                            'cancelledOn' => "0000-00-00 00:00:00",
                            'isOrderDispatched' => 1,                                                                                   
                            'dispatchedBy' => $this -> session -> userdata('user_id'),
                            'dispatchedOn' => date('Y-m-d H:i:s')
                        );
                        break;                            
                    default: // Received
                        $edit_data = array(
                            'OrderStatus' => $orderStatus, 
                            'VoucherCode' => '',
                            'ModifiedOn' => date('Y-m-d H:i:s'),
                            'isOrderCancelled' => 0,                                                                                   
                            'cancelledBy' => 0,
                            'cancelledOn' => "0000-00-00 00:00:00", 
                            'isOrderDispatched' => 0,                                                                                   
                            'dispatchedBy' => 0,
                            'dispatchedOn' => "0000-00-00 00:00:00"
                        );
                }

                $result = $this -> loyaltyordermodel -> update_order($id, $edit_data);
                
                if($result!="" && $orderStatus==2)
                {
                    # Get Order deatils 
                    $orderDetails = $this -> loyaltyordermodel -> get_order_details($id);
                    
                    if( $orderDetails )
                    {
                        $message = "Your order (". $orderDetails['OrderNumber'] .") has been dispatched.";
                        if($orderDetails['VoucherCode'])
                        {
                             $message = $message." You can use voucher code (". $orderDetails['VoucherCode'] .") to redeem it."; 
                        }
                        $message = $message." Thank you.";

                        # Send Notification for the user about order is dispatched
                        $notification_array = array(
                                'title' => 'Order Dispatched',
                                'message' => $message,
                                'order_id' => $orderDetails['Id'],
                                'product_id' => '0',
                                'retailer_id' => '0',
                                'store_type_id' => '0',
                                'store_id' => '0',
                                'is_special' => '0',
                                'is_location_message' => '0',
                                'is_location_near_message' => '0'
                         );

                        $multiple_insert[] = array(
                            'Title' => 'Order Dispatched',
                            'Message' => $message,
                            'UserId' => $orderDetails['UserId'],
                            'CreatedOn' => date('Y-m-d H:i:s')
                        );
                        send_push_notification($notification_array, array($orderDetails['UserId']), $multiple_insert);                        
                    }
                }
                
                $this -> session -> set_userdata('success_message', "Order status updated successfully");                    
                $this -> result = 1;
                $this -> message = 'Order status updated successfully';
               
            }
            else {
                $this -> result = 0;
                $this -> message = $this -> form_validation -> error_array();
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

    /*
     * Method Name: delete
     * Purpose: Delete loyalty product
     * params:
     *      input: $id
     *      output: status - FAIL / SUCCESS
     *              message - The reason if loyalty products not found fails / Success message
     *              loyaltyProduct - Array containing all active loyalty products.
     */
    
    public function delete($id) {        
        $this -> loyaltyproductmodel -> delete_product($id);
        $this -> session -> set_userdata('success_message', "Product deleted successfully");
        redirect('loyaltyproducts', 'refresh');
    }

    /*
     * Method Name: change_status
     * Purpose: Update loyalty product status
     * params:
     *      input: $product_id, $status
     *      output: status - FAIL / SUCCESS
     *              message - TRUE     
     */
    public function change_status($id, $status) {
        $this -> loyaltyproductmodel -> change_status($id, $status);
        $this -> session -> set_userdata('success_message', "Product status updated successfully");
        redirect('loyaltyproducts', 'refresh');
    }

    function file_selected_check() {
        $this -> form_validation -> set_message('file_selected_check', 'Please upload product image.');
        if (empty($_FILES['ProductImage']['name'])) {
            return false;
        }
        else {
            return true;
        }
    }

    /*
     * Method Name: check_product_by_name
     * Purpose: Check product name exist
     * params:
     *      input: $name
     *      output: status - FAIL / SUCCESS
     *              message - TRUE     
     */
    function check_uniqueness_by_product_name($name) {
        $this -> form_validation -> set_message('check_uniqueness_by_product_name', 'Product already exists');
        return $this -> loyaltyproductmodel -> check_product_by_name($name);
    }

    /*
     * Method Name: check_product_by_name_edit
     * Purpose: Check product name exist excluding current record
     * params:
     *      input: $name,$id
     *      output: status - FAIL / SUCCESS
     *              message - TRUE     
     */    
    function check_uniqueness_by_product_name_edit($name, $id) {
        $this -> form_validation -> set_message('check_uniqueness_by_product_name_edit', 'Product already exists');

        return $this -> loyaltyproductmodel -> check_product_by_name_edit($name, $id);
    }
    
    
    
    //showImage
    
    
    /*
     * Method Name: edit
     * Purpose: Get loyalty order information for edit
     * params:
     *      input: $id
     *      output: status - FAIL / SUCCESS
     *              message - The reason if loyalty products not found fails / Success message
     *              loyaltyProduct - Array containing all active loyalty products.
     */
    
    public function showImage($loyaltyProductId) {
        # Get Order deatils 
        $data = $this -> loyaltyproductmodel -> get_product_details($loyaltyProductId);
        
        #Set values 
        $this -> breadcrumbs[] = array('label' => 'Show Image', 'url' => 'loyaltyorders/showImage/' . $loyaltyProductId);

        $data['title'] = $this -> page_title;
        $data['breadcrumbs'] = $this -> breadcrumbs;       
        
        $this -> load -> view('admin/loyaltyorders/showImage', $data);
    }
    
}
