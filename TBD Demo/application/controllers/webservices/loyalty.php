<?php

//error_reporting(E_ALL);
//ini_set('display_errors', 1);

if (!defined('BASEPATH'))
    exit('No direct script access allowed'); 

require APPPATH . 'libraries/REST_Controller.php';
/*
 * Author:MK
 * Purpose: Loyalty Webservices
 * Date:23-02-2017
 * Dependency: loyaltymodel.php
 */

class Loyalty extends REST_Controller {

    function __construct() {        
        parent::__construct();
        $api_key = $this -> post('api_key');

        validateApiKey($api_key);

        $retArr = array();

        $this -> load -> model('webservices/loyaltymodel', '', TRUE);
         $this -> load -> model('webservices/usermodel', '', TRUE);
        
    }
    

    /*
     * Method Name: get_loyalty_consumptions
     * Purpose: Get loyalty_consumptions for user
     * params:
     *      input: user_id
     *      output: status - FAIL / SUCCESS
     *              message - The reason if user not found fails / Success message
     *              consumptionDetails - Array containing all the details for the user consumption.
     */
    
    
    public function get_loyalty_consumptions_post() {  
        
        # get post values 
       $userId = $this -> post('user_id');
               
        # Set defaults        
        $loyaltyDetails = array();
        $loyaltyDetails['current_date'] = date('d M Y');
       // $loyaltyDetails['points_earned_from_reviews'] = 0;
        $loyaltyDetails['points_earned_from_product_shares'] = 0;
        $loyaltyDetails['points_earned_from_app_shares'] = 0;
        $loyaltyDetails['points_earned_from_checkin'] = 0;
        $loyaltyDetails['total_points_earned_this_month'] = 0;
        $loyaltyDetails['total_points_earned_to_date'] = 0;
        $loyaltyDetails['total_points_redeemed_this_month'] = 0;
        $loyaltyDetails['total_points_redeemed_to_date'] = 0;
        $loyaltyDetails['current_point_balance'] = 0;
        $termsString = "";
                
        # Get loyalty earned details till date
        $loyaltyEarnedDetails = $this -> loyaltymodel -> get_loyalty_earned_details($userId); 
        
        if($loyaltyEarnedDetails)
        {
            $total_points_earned_to_date = $loyaltyEarnedDetails['userProductReviews'] + $loyaltyEarnedDetails['userProductShares'] + $loyaltyEarnedDetails['userAppShares']+$loyaltyEarnedDetails['userCheckIns'];
           // $loyaltyDetails['points_earned_from_reviews']        = $loyaltyEarnedDetails['userProductReviews'];
            $loyaltyDetails['points_earned_from_product_shares'] = $loyaltyEarnedDetails['userProductShares'];
            $loyaltyDetails['points_earned_from_app_shares']     = $loyaltyEarnedDetails['userAppShares'];
            $loyaltyDetails['points_earned_from_checkin']     = $loyaltyEarnedDetails['userCheckIns'];
            $loyaltyDetails['total_points_earned_to_date']       = $total_points_earned_to_date;
        }
        
        
        # Get loyalty earned details for month
        $dt         = new DateTime( date("Y-m-d") ); 
        $startDate  = date('Y-m-01')." 00:00:00";
        $endDate    = $dt->format( 'Y-m-t' )." 23:59:59";
        
        $loyaltyEarnedDetailsForMoth = $this -> loyaltymodel -> get_loyalty_earned_details($userId, $startDate, $endDate);
        
        if($loyaltyEarnedDetailsForMoth )
        {
            $total_points_earned_this_month = $loyaltyEarnedDetailsForMoth['userProductReviews'] + $loyaltyEarnedDetailsForMoth['userProductShares'] + $loyaltyEarnedDetailsForMoth['userAppShares']+$loyaltyEarnedDetails['userCheckIns'];
            $loyaltyDetails['total_points_earned_this_month'] = $total_points_earned_this_month;
        }
        
        # Get loyalty redeemed details
        $redeemedDetails = $this -> loyaltymodel -> get_loyalty_redeemed_details($userId);
        $redeemedDetailsForMonth = $this -> loyaltymodel -> get_loyalty_redeemed_details($userId, $startDate, $endDate);
        //$loyaltyBalanceDetails = $this -> loyaltymodel -> get_loyalty_balance($userId);
                
        if($redeemedDetails )
        {
            $loyaltyDetails['total_points_redeemed_to_date'] = $redeemedDetails['loyalty_consumption'];
        }
        
         if($redeemedDetailsForMonth )
        {
            $loyaltyDetails['total_points_redeemed_this_month'] = $redeemedDetailsForMonth['loyalty_consumption'];
        }        
       
        # Get User's balance loyalty points 
        $loyaltyDetails['current_point_balance'] = $this -> loyaltymodel -> get_loyalty_balance($userId);
        
        if ($loyaltyDetails) {
            # Get loyalty terms and conditions
            $loyaltyTermsAndContions = $this -> loyaltymodel -> get_loyalty_terms_and_contions();
            
            if($loyaltyTermsAndContions)
            {
                foreach($loyaltyTermsAndContions as $terms)
                {
                   if( $termsString )
                   {
                       $termsString = $termsString .", ".$terms['TermsText'];
                   }else{
                       $termsString = $terms['TermsText'];
                   }
                }
            }
            
            $retArr['status'] = SUCCESS;
            $retArr['termsAndConditions'] = $termsString;
            $retArr['loyaltyDetails'] = $loyaltyDetails;
            $this -> response($retArr, 200); // 200 being the HTTP response code
            die;
        }
        else {
            $retArr['status'] = FAIL;
            $retArr['termsAndConditions'] = $termsString;
            $retArr['message'] = 'No details found';
            $retArr['loyaltyDetails'] = array();
            $this -> response($retArr, 200); // 200 being the HTTP response code
            die;
        } // if ($loyaltyDetails)          
         
    } // public function get_loyalty_consumptions_post()
    
    
    /*
     * Method Name: get_loyalty_products_post
     * Purpose: Get listing for the active loyalty products
     * params:
     *      input: $userId
     *      output: status - FAIL / SUCCESS
     *              message - The reason if loyalty products not found fails / Success message
     *              loyaltyProducts - Array containing all active loyalty products.
     */
    
    
    public function get_loyalty_products_post() {   
        # get post values 
        $userId = $this -> post('user_id');
        $categoryId = $this -> post('category_id');
        $termsString = "";
        $device_type = $this -> post('device_type') ? $this -> post('device_type') : ""; //W - Web, A - Android, I - I phone
        $device_type = $device_type != '' ? $device_type : "A";
            
        # Get loyalty products
        $loyaltyProducts = $this -> loyaltymodel -> get_loyalty_products($userId,$categoryId);
                
        if($loyaltyProducts)
        {
            $i = 0;

            //Encode image of loyalty products
            foreach ($loyaltyProducts as $loyaltyProduct) {
                if ($loyaltyProduct['ProductImage'])
                    $loyaltyProducts[$i]['ProductImage'] = (front_url() . LOYALTY_PRODUCT_IMAGE_PATH . "medium/" . $loyaltyProduct['ProductImage']);
                else
                    $loyaltyProducts[$i]['ProductImage'] = (front_url() . DEFAULT_LOYALTY_PRODUCT_IMAGE_PATH);

                # Need to change this URL as we don't have frontend for loyalty module
                
                switch ($device_type) {
                    case "A":
                            $pageUrl = PLAY_STORE_URL;
                            break;
                    case "I":
                            $pageUrl = APP_STORE_URL;
                            break;
                    case "W":
                            $pageUrl = front_url() . 'loyaltyproductdetails/' . urlencode(encode_per($loyaltyProduct['LoyaltyTitle'])) . '/' . $this -> encrypt -> encode($loyaltyProduct['Id']);
                            break;
                    default:
                            $pageUrl = PLAY_STORE_URL;
                }
                
                //$loyaltyProducts[$i]['PageUrl'] = $pageUrl;                
                $loyaltyProducts[$i]['PageUrl'] = front_url(); // As currently loyalty module is not implemented on front end will redirect to home page
                $loyaltyProducts[$i]['AppStoreUrl']  = APP_STORE_URL;
                $loyaltyProducts[$i]['PlayStoreUrl'] = PLAY_STORE_URL;    
                
                # Get order placed information
                $isOrderPlaced = $this -> loyaltymodel -> check_order_placed($userId,$loyaltyProduct['Id']);
                $loyaltyProducts[$i]['isOrderPlaced'] = $isOrderPlaced;
                
                # Get product reviews and map with listing
                $loyaltyProductsReviews = $this -> loyaltymodel -> get_loyalty_products_reviews($userId,$loyaltyProduct['Id']);
                
                $loyaltyProducts[$i]['totalReviews'] = $loyaltyProductsReviews['totalReviews'];
                $loyaltyProducts[$i]['Rating'] = $loyaltyProductsReviews['Rating'];
                $loyaltyProducts[$i]['Review'] = $loyaltyProductsReviews['Review'];
                $i++;
            }
            
            # Get loyalty terms and conditions
            $loyaltyTermsAndContions = $this -> loyaltymodel -> get_loyalty_terms_and_contions();
            
            if($loyaltyTermsAndContions)
            {
                foreach($loyaltyTermsAndContions as $terms)
                {
                   if( $termsString )
                   {
                       $termsString = $termsString .", ".$terms['TermsText'];
                   }else{
                       $termsString = $terms['TermsText'];
                   }
                }
            }            
        
            $retArr['status'] = SUCCESS;            
            $retArr['balanceLoyaltyPoints'] = $this -> loyaltymodel -> get_loyalty_balance($userId);        
            $retArr['termsAndConditions'] = $termsString;
            $retArr['loyaltyProducts'] = $loyaltyProducts;
            $this -> response($retArr, 200); // 200 being the HTTP response code
            die;
        }else {
            $retArr['status'] = FAIL;
            $retArr['message'] = "No product found";
            $this -> response($retArr, 200); // 200 being the HTTP response code
            die;
        }
    } // public function get_loyalty_products_post()
    
    /*
     * Method Name: get_loyalty_product_details_post
     * Purpose: Get Loyalty product details.
     * params:
     *      input: NIL
     *      output: status - FAIL / SUCCESS
     *              message - The reason if loyalty products not found fails / Success message
     *              loyaltyProducts - Array containing all active loyalty products.
     */
    public function get_loyalty_product_details_post() {   
        # get post values 
        $userId = $this -> post('user_id');
        $loyaltyProductId = $this -> post('loyalty_product_id');
        
        # Get loyalty products
        $loyaltyProductDetails = $this -> loyaltymodel -> get_loyalty_product_details($loyaltyProductId);
        
        if($loyaltyProductDetails)
        {   
            # Get product reviews and map with listing
            $loyaltyProductsReviews = $this -> loyaltymodel -> get_loyalty_products_reviews($userId,$loyaltyProductId);
        
            if($loyaltyProductsReviews)
            {
                $loyaltyProductDetails['totalReviews'] = $loyaltyProductsReviews['totalReviews'];
                $loyaltyProductDetails['Rating'] = $loyaltyProductsReviews['Rating'];
                $loyaltyProductDetails['Review'] = $loyaltyProductsReviews['Review'];
            }
                
            $retArr['status'] = SUCCESS;
            $retArr['loyaltyProductDetails'] = $loyaltyProductDetails;
            $this -> response($retArr, 200); // 200 being the HTTP response code
            die;
        }else {
            $retArr['status'] = FAIL;
            $retArr['message'] = "No product found";
            $this -> response($retArr, 200); // 200 being the HTTP response code
            die;
        }
    } // public function get_loyalty_products_post()
    
    
    /*
     * Method Name: save_loyalty_product_ratings_post
     * Purpose: Save user's rating for the loyalty product.
     * params:
     *      input: user_id , loyalty_product_id, rating
     *      output: status - FAIL / SUCCESS
     *              message - The reason if loyalty products not found fails / Success message
     *              loyaltyProducts - Array containing all active loyalty products.
     */
    public function save_loyalty_product_rating_post() {   
        # get post values 
        $userId = $this -> post('user_id');
        $loyaltyProductId = $this -> post('loyalty_product_id');
        $rating = $this -> post('rating');
        
        # Save rating for loyalty product
        $result = $this -> loyaltymodel -> save_loyalty_product_rating($userId, $loyaltyProductId, $rating);
        
        if ($result == 'ADD') {
            $message = "Rating saved successfully.";
        } else if ($result == 'UPDATE') {
            $message = "Rating saved successfully.";
        }

        if ($result) {
            # Get loyalty products
            $loyaltyProductDetails = $this -> loyaltymodel -> get_loyalty_product_details($loyaltyProductId);
            
            # Get product reviews and map with listing
            $loyaltyProductsReviews = $this -> loyaltymodel -> get_loyalty_products_reviews($userId,$loyaltyProductId);
        
            $retArr['status'] = SUCCESS;            
            $retArr['message'] = $message;
            $retArr['loyaltyProductDetails'] = $loyaltyProductDetails;
            $retArr['loyaltyProductsReviews'] = $loyaltyProductsReviews;
            
            $this -> response($retArr, 200); // 200 being the HTTP response code
            die;
        }
        else {
            $retArr['status'] = FAIL;
            $this -> response($retArr, 200); // 200 being the HTTP response code
            die;
        }
    } // public function save_loyalty_product_rating_post()
    
    
    /*
     * Method Name: save_loyalty_product_review_post
     * Purpose: Save users reviews for the loyalty product.
     * params:
     *      input: user_id , loyalty_product_id, review
     *      output: status - FAIL / SUCCESS
     *              message - The reason if loyalty products not found fails / Success message
     *              loyaltyProductDetails - Array containing product details.
     *              loyaltyProductsReviews - Array containing product reviews.
     */
    public function save_loyalty_product_review_post() {   
        # get post values 
        $userId = $this -> post('user_id');
        $loyaltyProductId = $this -> post('loyalty_product_id');
        $review = $this -> post('review');
        
        # Save review for loyalty product
        $result = $this -> loyaltymodel -> save_loyalty_product_review($userId, $loyaltyProductId, $review);
        
        if ($result == 'ADD') {
            $message = "Review saved successfully.";
        } else if ($result == 'UPDATE') {
            $message = "Review saved successfully.";
        }

        if ($result) {
            # Get loyalty product details
            $loyaltyProductDetails = $this -> loyaltymodel -> get_loyalty_product_details($loyaltyProductId);
            
            # Get product reviews and map with listing
            $loyaltyProductsReviews = $this -> loyaltymodel -> get_loyalty_products_reviews($userId,$loyaltyProductId);
        
            $retArr['status'] = SUCCESS;            
            $retArr['message'] = $message;
            $retArr['loyaltyProductDetails'] = $loyaltyProductDetails;
            $retArr['loyaltyProductsReviews'] = $loyaltyProductsReviews;
            
            $this -> response($retArr, 200); // 200 being the HTTP response code
            die;
        }
        else {
            $retArr['status'] = FAIL;
            $this -> response($retArr, 200); // 200 being the HTTP response code
            die;
        }
    } // public function save_loyalty_product_review_post()
    
    
    /*
     * Method Name: save_loyalty_product_share_post
     * Purpose: Save loyalty product sharing information.
     * params:
     *      input: user_id , loyalty_product_id, $share_from [ W - Web, A - Android, I - I phone  ]
     *      output: status - FAIL / SUCCESS
     *              message - The reason if loyalty products not found fails / Success message     
     */
    
    public function save_loyalty_product_share_post() {        
        $loyalty_product_id = $this -> post('loyalty_product_id') ? $this -> post('loyalty_product_id') : "";
        $user_id = $this -> post('user_id') ? $this -> post('user_id') : "";
        $share_from = $this -> post('share_from') ? $this -> post('share_from') : ""; //W - Web, A - Android, I - I phone 

        $insert_data = array(
            'LoyaltyProductId' => $loyalty_product_id,            
            'UserId' => $user_id,
            'ShareFrom' => $share_from
        );
        $isInsert = $this -> loyaltymodel -> insert_share_details($insert_data);
        if ($isInsert) {
            $share_count = $this -> loyaltymodel -> get_loyalty_product_shares($loyalty_product_id);
            $share_count = $share_count['count'] ? $share_count['count'] : 0;
            $retArr['status'] = SUCCESS;
            $retArr['count'] = $share_count;
            $retArr['message'] = 'Share count updated successfully';
            $this -> response($retArr, 200); // 200 being the HTTP response code
            die;
        }
        else {
            $retArr['status'] = FAIL;
            $retArr['message'] = 'Failed to update share count';
            $this -> response($retArr, 200); // 200 being the HTTP response code
            die;
        }
    }
    
    
    /*
     * Method Name: buy_loyalty_product_post
     * Purpose: Submit Request to get the product by using user's loyalty points.	
     * params:
     *      input: user_id , loyalty_product_id, $share_from [ W - Web, A - Android, I - I phone  ]
     *      output: status - FAIL / SUCCESS
     *              message - The reason if loyalty products not found fails / Success message     
     */
    
    public function buy_loyalty_product_post() {        
        # Get Post Values 
        $loyalty_product_id = $this -> post('loyalty_product_id') ? $this -> post('loyalty_product_id') : "";
        $user_id = $this -> post('user_id') ? $this -> post('user_id') : "";
        $points_used = $this -> post('points_used') ? $this -> post('points_used') : 0;

        # Set default values 
        $balancePoints = 0;
        
        # Check user has enough balance points         
        $balancePoints = $this -> loyaltymodel -> get_loyalty_balance($user_id);        
        
        if($points_used < $balancePoints)
        {
            $currentBalancePoints = $balancePoints - $points_used;
            
            /*
            # Insert Record to buy the product 
            $insert_data = array(
                'LoyaltyProductId' => $loyalty_product_id,            
                'UserId' => $user_id,
                'PointsUsed' => $points_used,
                'BalancePoints'=> $currentBalancePoints,
                'CreatedOn' => date("Y-m-d H:i:s"),
                'ModifiedOn' => date("Y-m-d H:i:s")
            );
            $isInsert = $this -> loyaltymodel -> buy_loyalty_product($insert_data);
            */
            
            # Insert product into loyalty_cart  
            $insert_data = array(
                'LoyaltyProductId' => $loyalty_product_id,            
                'UserId' => $user_id,
                'PointsUsed' => $points_used,
                'BalancePoints'=> $currentBalancePoints,
                'CreatedOn' => date("Y-m-d H:i:s")
            );
            $isInsert = $this -> loyaltymodel -> add_loyalty_product_into_cart($insert_data);
            
            if ($isInsert) { 
                # Update loyalty Comsumption information 
                $insert_consumption_data = array(                    
                    'UserId' => $user_id,
                    'LoyaltyCartId'=> $isInsert,
                    //'LoyaltyOrderId'=> $isInsert,
                    'PointsUsed' => $points_used,
                    'BalancePoints'=> $currentBalancePoints,
                    'ConsumptionDate' => date("Y-m-d H:i:s")
                );
                $isConsumptionInsert = $this -> loyaltymodel -> insert_loyalty_consumption_details($insert_consumption_data);
                
                $retArr['status'] = SUCCESS;                
                //$retArr['message'] = 'you will get this product within 48/72 hours at the address specified.';
                //$retArr['message'] ="This item will be delivered to you as per the address specified in Your Profile. If your address has changed, please update it before you redeem this item. Thank you";
                
                $retArr['message'] = "Product successfully added into cart.";
                
                $retArr['balanceLoyaltyPoints'] = $this -> loyaltymodel -> get_loyalty_balance($user_id);        
                $this -> response($retArr, 200); // 200 being the HTTP response code
                die;
            }
            else {
                $retArr['status'] = FAIL;
                $retArr['message'] = 'Failed to save record.';
                $this -> response($retArr, 200); // 200 being the HTTP response code
                die;
            }
            
        }else{
            $retArr['status'] = FAIL;
            $retArr['message'] = "Sorry, you don't have enough loyalty points to get this product.";
            $this -> response($retArr, 200); // 200 being the HTTP response code
            die; 
        }
    } 
    
    
    public function buy_loyalty_product_post_old() {        
        # Get Post Values 
        $loyalty_product_id = $this -> post('loyalty_product_id') ? $this -> post('loyalty_product_id') : "";
        $user_id = $this -> post('user_id') ? $this -> post('user_id') : "";
        $points_used = $this -> post('points_used') ? $this -> post('points_used') : 0;

        # Set default values 
        $balancePoints = 0;
        
        # Check user has enough balance points         
        $balancePoints = $this -> loyaltymodel -> get_loyalty_balance($user_id);
        
        
        if($points_used < $balancePoints)
        {
            $currentBalancePoints = $balancePoints - $points_used;
            
            # Insert Record to buy the product 
            $insert_data = array(
                'LoyaltyProductId' => $loyalty_product_id,            
                'UserId' => $user_id,
                'PointsUsed' => $points_used,
                'BalancePoints'=> $currentBalancePoints,
                'CreatedOn' => date("Y-m-d H:i:s"),
                'ModifiedOn' => date("Y-m-d H:i:s")
            );
            $isInsert = $this -> loyaltymodel -> buy_loyalty_product($insert_data);
            if ($isInsert) { 
                # Update loyalty Comsumption information 
                $insert_consumption_data = array(                    
                    'UserId' => $user_id,
                    'LoyaltyOrderId'=> $isInsert,
                    'PointsUsed' => $points_used,
                    'BalancePoints'=> $currentBalancePoints,
                    'ConsumptionDate' => date("Y-m-d H:i:s")
                );
                $isConsumptionInsert = $this -> loyaltymodel -> insert_loyalty_consumption_details($insert_consumption_data);
                
                $retArr['status'] = SUCCESS;                
                //$retArr['message'] = 'you will get this product within 48/72 hours at the address specified.';
                $retArr['message'] ="This item will be delivered to you as per the address specified in Your Profile. If your address has changed, please update it before you redeem this item. Thank you";
                        
                $retArr['balanceLoyaltyPoints'] = $this -> loyaltymodel -> get_loyalty_balance($user_id);        
                $this -> response($retArr, 200); // 200 being the HTTP response code
                die;
            }
            else {
                $retArr['status'] = FAIL;
                $retArr['message'] = 'Failed to save record.';
                $this -> response($retArr, 200); // 200 being the HTTP response code
                die;
            }
            
        }else{
            $retArr['status'] = FAIL;
            $retArr['message'] = "Sorry, you don't have enough loyalty points to get this product.";
            $this -> response($retArr, 200); // 200 being the HTTP response code
            die; 
        }
    } 
    
    /*
     * Method Name: save_loyalty_product_review_post
     * Purpose: Save users reviews for the loyalty product.
     * params:
     *      input: user_id , loyalty_product_id, review
     *      output: status - FAIL / SUCCESS
     *              message - The reason if loyalty products not found fails / Success message
     *              loyaltyProductDetails - Array containing product details.
     *              loyaltyProductsReviews - Array containing product reviews.
     */
    public function save_loyalty_product_review_rating_post() {   
        # get post values 
        $userId = $this -> post('user_id');
        $loyaltyProductId = $this -> post('loyalty_product_id');
        $review = $this -> post('review');
        $rating = $this -> post('rating');
        
        # Save review for loyalty product
        $result = $this -> loyaltymodel -> save_loyalty_product_review_rating($userId, $loyaltyProductId, $review,$rating);
        
        if ($result == 'ADD') {
            $message = "Review and Rating saved successfully.";
        } else if ($result == 'UPDATE') {
            $message = "Review and Rating saved successfully.";
        }

        if ($result) {
            # Get loyalty product details
            $loyaltyProductDetails = $this -> loyaltymodel -> get_loyalty_product_details($loyaltyProductId);
            
            # Get product reviews and map with listing
            $reviewsAndRatings = $this -> loyaltymodel -> get_loyalty_products_reviews_ratings($userId,$loyaltyProductId);
           
            $retArr['status'] = SUCCESS;            
            $retArr['message'] = $message;
            $retArr['loyaltyProductDetails'] = $loyaltyProductDetails;
            $retArr['reviewsAndRatings'] = $reviewsAndRatings;
            
            $this -> response($retArr, 200); // 200 being the HTTP response code
            die;
        }
        else {
            $retArr['status'] = FAIL;
            $this -> response($retArr, 200); // 200 being the HTTP response code
            die;
        }
    } // public function save_loyalty_product_review_post()
    
    
    /*
     * Method Name: get_loyalty_products_reviews
     * Purpose: Get listing for the loyalty products reviews
     * params:
     *      input: $userId
     *      output: status - FAIL / SUCCESS
     *              message - The reason if loyalty product reviews not found fails / Success message
     *              $reviews - Array containing all reviews about loyalty product
     */
    
    
    public function get_loyalty_products_reviews_post() {   
        # get post values 
        $userId = $this -> post('user_id');
        $productId = $this -> post('product_id');
        
        # Get loyalty products
        $reviews = $this -> loyaltymodel -> get_loyalty_products_reviews_listing($productId);
        
        //Convert the time to time ago for reviews
            $i = 0;
            $is_review_added = 0;
            foreach ($reviews as $review) {

                if ($review['CreatedOn'])
                    $reviews[$i]['CreatedOn'] = humanTiming(strtotime($review['CreatedOn'])) . " ago";

                if ($review['ProfileImage'])
                    $reviews[$i]['ProfileImage'] = (front_url() . USER_IMAGE_PATH . 'medium/' . $review['ProfileImage']);
                else
                    $reviews[$i]['ProfileImage'] = (front_url() . DEFAULT_USER_IMAGE_PATH);

                //To check if review added by user.

                if ($review['UserId'] == $user_id)
                    $is_review_added = 1;

                $i++;
            }
        
        if($reviews)
        {
            $retArr['status'] = SUCCESS;            
            $retArr['reviews'] = $reviews;                    
            $this -> response($retArr, 200); // 200 being the HTTP response code
            die;
        }else {
            $retArr['status'] = FAIL;
            $retArr['message'] = "No reviews found";
            $this -> response($retArr, 200); // 200 being the HTTP response code
            die;
        }
    } // public function get_loyalty_products_reviews_post()
    
    
    /*
     * Method Name: cancel_loyalty_product_order_post
     * Purpose: Cancel previously created order.	
     * params:
     *      input: user_id , loyalty_product_id
     *      output: status - FAIL / SUCCESS
     *              message - The reason if loyalty products not found fails / Success message     
     */
    
    
    public function cancel_loyalty_product_order_post() {   
        # get post values 
        $loyalty_product_id = $this -> post('loyalty_product_id') ? $this -> post('loyalty_product_id') : "";
        $user_id = $this -> post('user_id') ? $this -> post('user_id') : "";
        
        # Check order is dispatched Or not 
        $isOrderDispatched = $this -> loyaltymodel -> check_order_dispatched($user_id, $loyalty_product_id);
        
        if( $isOrderDispatched == 0 )
        {
            # Check order is already cancelled Or not 
            $isOrderCancelled = $this -> loyaltymodel -> check_order_cancelled($user_id, $loyalty_product_id);
           
            if( $isOrderCancelled == 0 )
            {
                # Proceed to cancel order                
                $result = $this -> loyaltymodel -> cancel_loyalty_product_order($user_id, $loyalty_product_id);

                if ($result) {
                    $retArr['status'] = SUCCESS;            
                    $retArr['message'] = "Order cancelled successfully.";
                    $retArr['balanceLoyaltyPoints'] = $this -> loyaltymodel -> get_loyalty_balance($user_id);        
                    $this -> response($retArr, 200); // 200 being the HTTP response code
                    die;
                }else {
                    $retArr['status'] = FAIL;
                    $this -> response($retArr, 200); // 200 being the HTTP response code
                    die;
                }
           }else{
                $retArr['status'] = FAIL;            
                $retArr['message'] = "Sorry, You have already cancelled this order.";
                $this -> response($retArr, 200); // 200 being the HTTP response code
                die;
           }     
                
        }else{
            $retArr['status'] = FAIL;            
            $retArr['message'] = "Sorry, You can't cancell this order as order is already dispatched.";
            $this -> response($retArr, 200); // 200 being the HTTP response code
            die;
        }                
        
    } // public function cancel_loyalty_product_order_post()
    
    
    
    /*
     * Method Name: add_to_loyalty_cart_post
     * Purpose: Add loyalty product into cart.	
     * params:
     *      input: user_id , loyalty_product_id, $share_from [ W - Web, A - Android, I - I phone  ]
     *      output: status - FAIL / SUCCESS
     *              message - The reason if loyalty products not found fails / Success message     
     */
    
    public function add_to_loyalty_cart_post() {        
        # Get Post Values 
        $loyalty_product_id = $this -> post('loyalty_product_id') ? $this -> post('loyalty_product_id') : "";
        $user_id = $this -> post('user_id') ? $this -> post('user_id') : "";
        $points_used = $this -> post('points_used') ? $this -> post('points_used') : 0;

        # Set default values 
        $balancePoints = 0;
        
        # Check user has enough balance points         
        $balancePoints = $this -> loyaltymodel -> get_loyalty_balance($user_id);        
        
        if($points_used < $balancePoints)
        {
            $currentBalancePoints = $balancePoints - $points_used;
            
            /*
            # Insert Record to buy the product 
            $insert_data = array(
                'LoyaltyProductId' => $loyalty_product_id,            
                'UserId' => $user_id,
                'PointsUsed' => $points_used,
                'BalancePoints'=> $currentBalancePoints,
                'CreatedOn' => date("Y-m-d H:i:s"),
                'ModifiedOn' => date("Y-m-d H:i:s")
            );
            $isInsert = $this -> loyaltymodel -> buy_loyalty_product($insert_data);
            */
            
            # Insert product into loyalty_cart  
            $insert_data = array(
                'LoyaltyProductId' => $loyalty_product_id,            
                'UserId' => $user_id,
                'PointsUsed' => $points_used,
                'BalancePoints'=> $currentBalancePoints,
                'CreatedOn' => date("Y-m-d H:i:s")
            );
            $isInsert = $this -> loyaltymodel -> add_loyalty_product_into_cart($insert_data);
            
            if ($isInsert) { 
                # Update loyalty Comsumption information 
                $insert_consumption_data = array(                    
                    'UserId' => $user_id,
                    'LoyaltyCartId'=> $isInsert,
                    //'LoyaltyOrderId'=> $isInsert,
                    'PointsUsed' => $points_used,
                    'BalancePoints'=> $currentBalancePoints,
                    'ConsumptionDate' => date("Y-m-d H:i:s")
                );
                $isConsumptionInsert = $this -> loyaltymodel -> insert_loyalty_consumption_details($insert_consumption_data);
                
                $retArr['status'] = SUCCESS;                
                //$retArr['message'] = 'you will get this product within 48/72 hours at the address specified.';
                //$retArr['message'] ="This item will be delivered to you as per the address specified in Your Profile. If your address has changed, please update it before you redeem this item. Thank you";
                
                $retArr['message'] = "Product successfully added into cart.";
                
                $retArr['balanceLoyaltyPoints'] = $this -> loyaltymodel -> get_loyalty_balance($user_id);        
                $this -> response($retArr, 200); // 200 being the HTTP response code
                die;
            }
            else {
                $retArr['status'] = FAIL;
                $retArr['message'] = 'Failed to save record.';
                $this -> response($retArr, 200); // 200 being the HTTP response code
                die;
            }
            
        }else{
            $retArr['status'] = FAIL;
            $retArr['message'] = "Sorry, you don't have enough loyalty points to get this product.";
            $this -> response($retArr, 200); // 200 being the HTTP response code
            die; 
        }
    } 
    
    /*
     * Method Name: get_loyalty_products_post
     * Purpose: Get listing for the active loyalty products
     * params:
     *      input: $userId
     *      output: status - FAIL / SUCCESS
     *              message - The reason if loyalty products not found fails / Success message
     *              loyaltyProducts - Array containing all active loyalty products.
     */
    
    
    public function get_cart_loyalty_products_post() {   
        # get post values 
        $userId = $this -> post('user_id');
        $device_type = $this -> post('device_type') ? $this -> post('device_type') : ""; //W - Web, A - Android, I - I phone
        $device_type = $device_type != '' ? $device_type : "A";
    
        $totalPoints =0;
        
        # Get loyalty products
        $loyaltyProducts = $this -> loyaltymodel -> get_cart_loyalty_products($userId);
        
        if($loyaltyProducts)
        {
            $i = 0;

            //Encode image of loyalty products
            foreach ($loyaltyProducts as $loyaltyProduct) {
                if ($loyaltyProduct['ProductImage'])
                    $loyaltyProducts[$i]['ProductImage'] = (front_url() . LOYALTY_PRODUCT_IMAGE_PATH . "medium/" . $loyaltyProduct['ProductImage']);
                else
                    $loyaltyProducts[$i]['ProductImage'] = (front_url() . DEFAULT_LOYALTY_PRODUCT_IMAGE_PATH);
               
                # Need to change this URL as we don't have frontend for loyalty module  
                # Currently we set home page url
                
                switch ($device_type) {
                    case "A":
                            $pageUrl = PLAY_STORE_URL;
                            break;
                    case "I":
                            $pageUrl = APP_STORE_URL;
                            break;
                    case "W":
                            $pageUrl = front_url() . 'loyaltyproductdetails/' . urlencode(encode_per($loyaltyProduct['LoyaltyTitle'])) . '/' . $this -> encrypt -> encode($loyaltyProduct['Id']);
                            break;
                    default:
                            $pageUrl = PLAY_STORE_URL;
                }
                
                //$loyaltyProducts[$i]['PageUrl'] = $pageUrl;
                
                $loyaltyProducts[$i]['PageUrl'] = front_url();
                
                $loyaltyProducts[$i]['AppStoreUrl']  = APP_STORE_URL;
                $loyaltyProducts[$i]['PlayStoreUrl'] = PLAY_STORE_URL;
                
                $totalPoints = $totalPoints + $loyaltyProduct['PointsUsed'];
                
                $i++;
            }
            
            # Get loyalty terms and conditions
            $loyaltyTermsAndContions = $this -> loyaltymodel -> get_loyalty_terms_and_contions();
            
            if($loyaltyTermsAndContions)
            {
                foreach($loyaltyTermsAndContions as $terms)
                {
                   if( $termsString )
                   {
                       $termsString = $termsString .", ".$terms['TermsText'];
                   }else{
                       $termsString = $terms['TermsText'];
                   }
                }
            }  
            
            # Get user details 
            $user_details = $this -> usermodel -> get_user_details_by_id($userId);

            if ($user_details) {
                //replace null values
                foreach ($user_details as $key => $value) {
                    if (is_null($value)) {
                        $user_details[$key] = "";
                    }
                }
                
                //Set path for image
                if ($user_details['ProfileImage'])
                    $user_details['ProfileImage'] = (front_url() . USER_IMAGE_PATH . "medium/" . $user_details['ProfileImage']);
                else
                    $user_details['ProfileImage'] = (front_url() . DEFAULT_USER_IMAGE_PATH);            
            }
        
            $retArr['status'] = SUCCESS;            
            $retArr['balanceLoyaltyPoints'] = $this -> loyaltymodel -> get_loyalty_balance($userId);  
            $retArr['totalPoints'] = $totalPoints;            
            $retArr['termsAndConditions'] = $termsString;
            $retArr['cartProducts'] = $loyaltyProducts;
            $retArr['user_details'] = ($user_details);
            $this -> response($retArr, 200); // 200 being the HTTP response code
            die;
        }else {
            $retArr['status'] = FAIL;
            $retArr['message'] = "Sorry, there are no items in your Shopping Cart";
            $this -> response($retArr, 200); // 200 being the HTTP response code
            die;
        }
    } // public function get_loyalty_products_post()
    
    
    
    /*
     * Method Name: remove_cart_loyalty_product_post
     * Purpose: Remove product(s) from cart
     * params:
     *      input: user_id , $cart_ids
     *      output: status - FAIL / SUCCESS
     *              message - The reason if loyalty products remove fails / Success message     
     */
    
    
    public function remove_cart_loyalty_product_post() {         
        # get post values 
        $cart_ids = $this -> post('cart_ids') ? $this -> post('cart_ids') : "";        
        $user_id = $this -> post('user_id') ? $this -> post('user_id') : "";
        $totalPoints =0;
        
        # Remove product(s) from cart                       
        $result = $this -> loyaltymodel -> remove_cart_loyalty_product($cart_ids, $user_id);
        
        if ($result) {
            
            # Get loyalty products
            $loyaltyProducts = $this -> loyaltymodel -> get_cart_loyalty_products($user_id);
            
             if($loyaltyProducts)
             {
                foreach ($loyaltyProducts as $loyaltyProduct) {
                    $totalPoints = $totalPoints + $loyaltyProduct['PointsUsed'];
                }
             }
             
            $retArr['status'] = SUCCESS;            
            $retArr['message'] = "Product(s) successfully removed from cart.";
            $retArr['balanceLoyaltyPoints'] = $this -> loyaltymodel -> get_loyalty_balance($user_id);        
            $retArr['totalPoints'] = $totalPoints;
            $this -> response($retArr, 200); // 200 being the HTTP response code
            die;
        }else {
            $retArr['status'] = FAIL;
            $this -> response($retArr, 200); // 200 being the HTTP response code
            die;
        }
    } // public function remove_cart_loyalty_product_post()
    
    
    /*
     * Method Name: place_loyalty_order_post
     * Purpose: Get listing for the active loyalty products
     * params:
     *      input: $userId
     *      output: status - FAIL / SUCCESS
     *              message - The reason if loyalty products not found fails / Success message
     *              loyaltyProducts - Array containing all active loyalty products.
     */
    
    
    public function place_loyalty_order_post() {   
        # get post values 
        $userId  = $this -> post('user_id');        
        
        $houseNo = $this -> post('house_No') ? $this -> post('house_No') : "";
        $streetName = $this -> post('street_name') ? $this -> post('street_name') : "";
        $suburb = $this -> post('suburb') ? $this -> post('suburb') : "";
        $city = $this -> post('city') ? $this -> post('city') : "";
        $state = $this -> post('province') ? $this -> post('province') : "";
        $pincode = $this -> post('pincode') ? $this -> post('pincode') : "";
        $latitude = $this -> post('latitude') ? $this -> post('latitude') : "";
        $longitude = $this -> post('longitude') ? $this -> post('longitude') : "";
        $telephone_fixed = $this -> post('telephone_fixed') ? $this -> post('telephone_fixed') : "";
        $telephone_mob = $this -> post('telephone_mob') ? $this -> post('telephone_mob') : "";        
        
        $orderId = 0;
        $orderTotal = 0;
        
        # Get loyalty products
        $cartProducts = $this -> loyaltymodel -> get_cart_loyalty_products($userId);
        
        if($cartProducts)
        {            
            # Get last 
            $lastOrderDetails= $this -> loyaltymodel -> get_last_order_details();
            
            if($lastOrderDetails)
            {
                $loyaltyOrderId = (int)$lastOrderDetails['LoyaltyOrderId'] + 1;
                //$orderNumber = "Order".date("Y").$loyaltyOrderId;
                $orderNumber = date("Y").$loyaltyOrderId;
            }else{
                //$orderNumber = "Order".date("Y")."1";
                $orderNumber = date("Y")."1";
            }
            
            # Place a new order            
            $insert_order_data = array(                
                'OrderNumber' => $orderNumber,
                'UserId' => $userId,
                'HouseNumber' => $houseNo,
                'StreetAddress' => $streetName,
                'Suburb' => $suburb,
                'city' => $city,
                'State' => $state,
                'Country' => '1',
                'PinCode' => $pincode,
                'Latitude' => $latitude,
                'Longitude' => $longitude,                
                'TelephoneFixed' => $telephone_fixed,
                'Mobile' => $telephone_mob,
                'CreatedOn' => date("Y-m-d H:i:s"),
                'ModifiedOn' => date("Y-m-d H:i:s")
            );
            
            $orderId = $this -> loyaltymodel -> save_loyalty_order($insert_order_data);
                        
            if($orderId)
            {
                foreach ($cartProducts as $cartProduct) {
            
                    # insert cart products into "user_loyalty_products" table
                    $insert_data = array(
                        'LoyaltyOrderId' => $orderId,
                        'LoyaltyProductId' => $cartProduct['LoyaltyProductId'],            
                        'UserId' => $cartProduct['UserId'],
                        'PointsUsed' => $cartProduct['PointsUsed'],                        
                        'CreatedOn' => date("Y-m-d H:i:s"),
                        'ModifiedOn' => date("Y-m-d H:i:s")
                    );

                    $isInsert = $this -> loyaltymodel -> buy_loyalty_product($insert_data);
                    $isItemInsert = $this -> loyaltymodel -> save_loyalty_order_products($insert_data);

                    
                    # Update loyalty Comsumption information 
                    $update_consumption_data = array(
                          'LoyaltyOrderId'=> $orderId                            
                    );
                    $isConsumptionUpdate = $this -> loyaltymodel -> update_loyalty_consumption_details($update_consumption_data,$cartProduct['Id']);
                    
                    $orderTotal = $orderTotal + $cartProduct['PointsUsed'];
                }// foreach ($loyaltyProducts as $loyaltyProduct)
        
                # Remove Products from cart
                $isRemoved = $this -> loyaltymodel -> make_cart_empty($userId);
                
                # Update loyalty Comsumption information 
                $update_order_data = array(
                     'OrderTotal'=> $orderTotal                            
                );
                $isOrderUpdate = $this -> loyaltymodel -> update_loyalty_order($update_order_data,$orderId);
                    
            }
            
        }
       
        if ($orderId) {
            
            # Send Notification for the user about order placed
            $notification_array = array(
                    'title' => 'Order Placed',
                    'message' => 'This order will be delivered to you as per the address specified. Thank you.',
                                    'order_id' => $orderId,
                    'product_id' => '0',
                    'retailer_id' => '0',
                    'store_type_id' => '0',
                    'store_id' => '0',
                    'is_special' => '0',
                    'is_location_message' => '0',
                    'is_location_near_message' => '0'
                );

            $multiple_insert[] = array(
                            'Title' => 'Order Placed',
                            'Message' => 'This order will be delivered to you as per the address specified. Thank you.',
                            'UserId' => $userId,
                            'CreatedOn' => date('Y-m-d H:i:s')
                    );
            send_push_notification($notification_array, array($userId), $multiple_insert);	
        
            $retArr['status'] = SUCCESS;            
            $retArr['message'] = "Loyalty order placed successfully.";            
            //$retArr['message'] ="This item will be delivered to you as per the address specified in Your Profile. If your address has changed, please update it before you redeem this item. Thank you";
            $retArr['balanceLoyaltyPoints'] = $this -> loyaltymodel -> get_loyalty_balance($userId);        
            $this -> response($retArr, 200); // 200 being the HTTP response code
            die;
        }else {
            $retArr['status'] = FAIL;
            $this -> response($retArr, 200); // 200 being the HTTP response code
            die;
        }       
    } // public function place_loyalty_order_post()
}