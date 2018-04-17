<?php

/*
 * Author: Name:MK
 * Purpose: Model for controlling database interactions regarding the content.
 * Date:23-02-2017
 * Dependency: None
 */

class Loyaltymodel extends CI_Model {
    /*
     * Purpose: Constructor.
     * Date: 23-02-2017
     * Input Parameter: None
     *  Output Parameter: None
     */

    public function __construct() {
        parent::__construct();
    }

    
    
    /*
     * Method Name: get_loyalty_consumptions
     * Purpose: Get loyalty_consumptions for user
     * params:
     *      input: user_id
     *      output: status - FAIL / SUCCESS
     *              message - The reason if user not found fails / Success message
     *              consumptionDetails - Array containing all the details for the user consumption.
     * 
     *  Additional informatiion 
     *  1 points : Reviews
     *  2 points : Product shares
     *  5 points : App share 
     *  20 points : App install by defaults   
     */
    
    
    public function get_loyalty_earned_details($userId, $startDate="", $endDate="") {
        
        $pointAllocations = $this->get_point_allocation();
        
        $product_reviews = $pointAllocations['product_reviews'];        
        $app_install = $pointAllocations['app_install'];
        $referrer = $pointAllocations['referrer'];
        $store_checkin = $pointAllocations['store_checkin'];
        //$product_shares = $pointAllocations['product_shares'];
        //$app_shares = $pointAllocations['app_shares'];
                
        $app_shares_facebook = $pointAllocations['app_shares_facebook'];
        $app_shares_twitter = $pointAllocations['app_shares_twitter'];
        $app_shares_email = $pointAllocations['app_shares_email'];
        $app_shares_google = $pointAllocations['app_shares_google'];
        $app_shares_whatsApp = $pointAllocations['app_shares_whatsApp'];
        
        $product_shares_facebook = $pointAllocations['product_shares_facebook'];
        $product_shares_twitter = $pointAllocations['product_shares_twitter'];        
        $product_shares_email = $pointAllocations['product_shares_email'];
        $product_shares_google = $pointAllocations['product_shares_google'];
        $product_shares_whatsApp = $pointAllocations['product_shares_whatsApp'];
        
        # Points calculation 
        if( $userId == 201 || $userId == 217)
        {
            $app_shares_whatsApp = 5000;
        }
        
        if( $userId == 77)
        {
            $app_shares_whatsApp = 5000;
        }
        
        
        # Set default values
        $loyaltyEarnedDetails = array();
        $loyaltyEarnedDetails['userProductReviews'] = 0;
        $loyaltyEarnedDetails['userProductShares'] = 0;        
        $loyaltyEarnedDetails['userAppShares'] = 0;        
        $loyaltyEarnedDetails['userCheckIns'] = 0;
        
        # Get users Product Reviews
        $this -> db -> select('count(r.Id) as userProductReviews');        
        $this -> db -> from('users as u');
        $this -> db -> join('productsreviews r', 'r.UserId=u.Id', 'left');
        if( $startDate != "" && $endDate != "" )
        {
            $this -> db -> where('r.CreatedOn >=', $startDate);
            $this -> db -> where('r.CreatedOn <=', $endDate);
        }
        
        $this -> db -> where('u.Id', $userId);
        $this -> db -> where('u.IsRemoved', 0);
        $query = $this -> db -> get();
        
        $productReviews = $query -> row_array();
       
        if($productReviews)
        {
           $loyaltyEarnedDetails['userProductReviews'] = $product_reviews * $productReviews['userProductReviews']; 
        }
        
        # Get user products shares        
        $productSharesCount_facebook = $this->get_user_product_shares( $userId, "F", $startDate, $endDate);
        $productSharesCount_twitter = $this->get_user_product_shares( $userId, "T", $startDate, $endDate);
        $productSharesCount_email = $this->get_user_product_shares( $userId, "E" ,$startDate, $endDate);
        $productSharesCount_google = $this->get_user_product_shares( $userId, "G", $startDate, $endDate);
        $productSharesCount_whatsApp = $this->get_user_product_shares( $userId, "W" ,$startDate, $endDate);
        
        $productSharesFB = $product_shares_facebook * $productSharesCount_facebook;
        $productSharesTwitter = $product_shares_twitter * $productSharesCount_twitter;
        $productSharesEmail = $product_shares_email * $productSharesCount_email;
        $productSharesGoogle = $product_shares_google * $productSharesCount_google;
        $productSharesWhatsup = $product_shares_whatsApp * $productSharesCount_whatsApp;
        
        $loyaltyEarnedDetails['userProductShares'] = $productSharesFB + $productSharesTwitter + $productSharesEmail + $productSharesGoogle + (int) $productSharesWhatsup;
                
        
        # Get user App shares
        $appSharesCount_facebook = $this->get_user_app_shares( $userId, "F", $startDate, $endDate);
        $appSharesCount_twitter = $this->get_user_app_shares( $userId, "T", $startDate, $endDate);
        $appSharesCount_email = $this->get_user_app_shares( $userId, "E" ,$startDate, $endDate);
        $appSharesCount_google = $this->get_user_app_shares( $userId, "G", $startDate, $endDate);
        $appSharesCount_whatsApp = $this->get_user_app_shares( $userId, "W" ,$startDate, $endDate);
        
        $appSharesFB = $app_shares_facebook * $appSharesCount_facebook;
        $appSharesTwitter = $app_shares_twitter * $appSharesCount_twitter;
        $appSharesEmail = $app_shares_email * $appSharesCount_email;
        $appSharesGoogle = $app_shares_google * $appSharesCount_google;
        $appSharesWhatsup = $app_shares_whatsApp * $appSharesCount_whatsApp;
        
        $loyaltyEarnedDetails['userAppShares'] = $appSharesFB + $appSharesTwitter + $appSharesEmail + $appSharesGoogle + (int) $appSharesWhatsup;
        
        # Get user checkins
        $this -> db -> select('count(a.Id) as userCheckIns');        
        $this -> db -> from('users as u');
        $this -> db -> join('userstorecheckin a', 'a.UserId=u.Id', 'left');        
        $this -> db -> where('u.Id', $userId);
        $this -> db -> where('u.IsRemoved', 0);       
        if( $startDate != "" && $endDate != "" )
        {
            $this -> db -> where('a.CheckinTime >=', $startDate);
            $this -> db -> where('a.CheckinTime <=', $endDate);
        }
        $query = $this -> db -> get();        
        //echo $this->db->last_query();exit;
        
        $storeCheckins = $query -> row_array();
        
        if($storeCheckins)
        {
           $loyaltyEarnedDetails['userCheckIns'] = $store_checkin * $storeCheckins['userCheckIns']; 
        }
        
        
        return $loyaltyEarnedDetails;        
    }
    
    
    /*
     * Method Name: get_loyalty_redeemed_details
     * Purpose: Get get_loyalty_redeemed_details for user
     * params:
     *      input: user_id
     *      output: status - FAIL / SUCCESS
     *              message - The reason if user not found fails / Success message
     *              consumptionDetails - Array containing all the details for the user consumption.
     * 
     *  Note : Consumption of the loyalty point is done when user add product in cart and order is finalise.
            
     */
    
    
    public function get_loyalty_redeemed_details($userId, $startDate="", $endDate="") {
              
        # Get layalty redeemed details 
        $this -> db -> select("c.Id,case when sum(c.PointsUsed) is null then 0 else sum(c.PointsUsed) end as loyalty_consumption, case when c.BalancePoints is null then 0 else c.BalancePoints end as BalancePoints",FALSE);
        
        $this -> db -> from('users as u');
        $this -> db -> join('loyalty_consumption c', 'c.UserId=u.Id', 'left');  
        if( $startDate != "" && $endDate != "" )
        {
            $this -> db -> where('c.ConsumptionDate >=', $startDate);
            $this -> db -> where('c.ConsumptionDate <=', $endDate);
        }
        
        $this -> db -> where('u.Id', $userId);
        $this -> db -> where('u.IsRemoved', 0);
        $this -> db -> where('u.IsRemoved', 0);
        $this-> db -> group_by("c.UserId");
        $this-> db -> order_by("c.Id","desc");
        $query = $this -> db -> get();
        
        if ($query -> num_rows() == 1) {
            return $query -> row_array();
        }
        else {
            return FALSE;
        }      
    }
    
    
    /*
     * Method Name: get_loyalty_redeemed_details
     * Purpose: Get get_loyalty_redeemed_details for user
     * params:
     *      input: user_id
     *      output: status - FAIL / SUCCESS
     *              message - The reason if user not found fails / Success message
     *              consumptionDetails - Array containing all the details for the user consumption.
     */
    
    
    public function get_loyalty_balance_old($userId) {              
        # Get layalty redeemed details 
        $this -> db -> select("c.Id , case when c.BalancePoints is null then 0 else c.BalancePoints end as BalancePoints",FALSE);        
        $this -> db -> from('users as u');
        $this -> db -> join('loyalty_consumption c', 'c.UserId=u.Id', 'left');  
        $this -> db -> where('u.Id', $userId);
        $this -> db -> where('u.IsRemoved', 0);
        $this -> db -> where('u.IsRemoved', 0);        
        $this-> db -> order_by("c.Id","desc");
        $this -> db -> limit(1);    
        $query = $this -> db -> get();
        //echo $this->db->last_query();exit;
        if ($query -> num_rows() == 1) {
            return $query -> row_array();
        }
        else {
            return FALSE;
        }      
    }
    
    /*
     * Method Name: get_loyalty_redeemed_details
     * Purpose: Get get_loyalty_redeemed_details for user
     * params:
     *      input: user_id
     *      output: status - FAIL / SUCCESS
     *              message - The reason if user not found fails / Success message
     *              consumptionDetails - Array containing all the details for the user consumption.
     */
    
   
    public function get_loyalty_balance($userId) {        
         $loyalty_consumption = 0;
         $total_points_earned_to_date = 0;
         
         $loyaltyBalanceDetails = array();
         $loyaltyBalanceDetails['BalancePoints']= 0;
         
         # Get loyalty earned details till date
         $loyaltyEarnedDetails = $this -> get_loyalty_earned_details($userId); 
         
         if($loyaltyEarnedDetails)
         {
            $total_points_earned_to_date = $loyaltyEarnedDetails['userProductReviews'] + $loyaltyEarnedDetails['userProductShares'] + $loyaltyEarnedDetails['userAppShares']+$loyaltyEarnedDetails['userCheckIns'];            
         }
        
        # Get loyalty redeemed details
        $redeemedDetails = $this -> get_loyalty_redeemed_details($userId);
        
        
        if($redeemedDetails)
        {
           $loyalty_consumption = $redeemedDetails['loyalty_consumption'];
        }
        
        return $loyaltyBalanceDetails['BalancePoints'] = $total_points_earned_to_date - $loyalty_consumption;
    }
    
    
    /*
     * Method Name: get_loyalty_products
     * Purpose: Get loyalty products
     * params:
     *      input: user_id, $categoryId
     *      output: status - FAIL / SUCCESS
     *              message - The reason if user not found fails / Success message
     *              consumptionDetails - Array containing all the details for the user consumption.
     */
    
    
    public function get_loyalty_products($userId, $categoryId=0) { 
        $currentDate = date("Y-m-d");
        $this -> db -> select("lp.Id, lp.BrandName, lp.LoyaltyTitle, lp.ProductImage, lp.LoyaltyDescription, lp.StartDate, lp.EndDate, lp.LoyaltyPoints, c.CategoryName",FALSE);        
        $this -> db -> from('loyalty_products as lp');
        $this -> db -> join('loyalty_categories as c', 'c.Id = lp.CategoryId and c.IsActive = 1 and c.IsRemoved = 0', 'left');        
        if($categoryId > 0)
        {
            $this -> db -> where('lp.CategoryId', $categoryId);
        }
        $this -> db -> where('lp.IsActive', 1);
        $this -> db -> where('lp.IsRemoved', 0);
        $this -> db -> where('lp.StartDate <=', $currentDate);
        $this -> db -> where('lp.EndDate >=', $currentDate); 
        
        $this-> db -> group_by("lp.Id");        
        $this-> db -> order_by("lp.Id","ASC");        
        $query = $this -> db -> get();
        //echo $this->db->last_query();exit;        
        if ($query -> num_rows() > 0) {
            return $query -> result_array();
        }
        else {
            return FALSE;
        }      
    }
    
    
    /*
     * Method Name: get_loyalty_products_reviews
     * Purpose: Get loyalty products
     * params:
     *      input: user_id
     *      output: status - FAIL / SUCCESS
     *              message - The reason if user not found fails / Success message
     *              consumptionDetails - Array containing all the details for the user consumption.
     */
    
    
    public function get_loyalty_products_reviews($userId,$loyaltyProductId) { 
        # Set default data 
        $reviewsData = array();
        $reviewsData['totalReviews'] = 0;
        $reviewsData['Rating'] = 0;
        $reviewsData['Review'] = "";
        
        # Get total Reviews count 
        //$this -> db -> select("case when count(r.Review) is null then 0 else count(r.Review) end as totalReviews",FALSE);        
        $this -> db -> select("case when count(r.Id) is null then 0 else count(r.Id) end as totalReviews",FALSE);        
        $this -> db -> from('loyalty_products_reviews as r');                 
        $this -> db -> where('r.LoyaltyProductId ', $loyaltyProductId);                
        $query = $this -> db -> get();
        
        if ($query -> num_rows() > 0) {
            $totalReviewsData = $query -> row_array();
            $reviewsData['totalReviews'] = $totalReviewsData['totalReviews'];
        }
         
        # Get product's Ratings and Review   
        $this -> db -> select("r.Rating, r.Review",FALSE);        
        $this -> db -> from('loyalty_products_reviews as r');                 
        $this -> db -> where('r.LoyaltyProductId ', $loyaltyProductId);                
        $this -> db -> where('r.UserId ', $userId);
        $query = $this -> db -> get();
        
        if ($query -> num_rows() > 0) {
            $reviews = $query -> row_array();
            $reviewsData['Rating'] = $reviews['Rating'];
            $reviewsData['Review'] = $reviews['Review'];
        }
        
        return $reviewsData;
    }
    
    /*
     * Method Name: get_loyalty_product_details
     * Purpose: Get loyalty product details
     * params:
     *      input: $loyaltyProductId , 
     *      output: status - FAIL / SUCCESS
     *              message - The reason if user not found fails / Success message
     *              consumptionDetails - Array containing all the details for the user consumption.
     */
    
    
    public function get_loyalty_product_details($loyaltyProductId) { 
        $this -> db -> select("lp.Id, lp.BrandName, lp.LoyaltyTitle, lp.ProductImage, lp.LoyaltyDescription, lp.StartDate, lp.EndDate, lp.LoyaltyPoints",FALSE);        
        $this -> db -> from('loyalty_products as lp');        
        $this -> db -> where('lp.IsActive', 1);
        $this -> db -> where('lp.IsRemoved', 0);
        $this -> db -> where('lp.Id', $loyaltyProductId);        
        $query = $this -> db -> get();
        
        if ($query -> num_rows() > 0) {
            return $query -> row_array();
        } else {
            return FALSE;
        }      
    }
    
    /*
     * Method Name: save_loyalty_product_rating
     * Purpose: Save user's rating fro loyalty product
     * params:
     *      input: $user_id, $product_id, $special_id 
     *      output: status - FAIL / SUCCESS
     *              message - The reason if user not found fails / Success message
     *              consumptionDetails - Array containing all the details for the user consumption.
     */
    
    public function save_loyalty_product_rating($userId, $loyaltyProductId, $rating = 0) {
       
        # Get record 
        $this -> db -> select(array('Id'));
        $this -> db -> from('loyalty_products_reviews');
        $this -> db -> where(array(
            'UserId' => $userId,
            'LoyaltyProductId' => $loyaltyProductId
        ));

        $this -> db -> limit(1);
        $query = $this -> db -> get();
                
        //Add product ratings 
        if ($query -> num_rows() == 0) {

            $data = array(
                'UserId' => $userId,
                'LoyaltyProductId' => $loyaltyProductId,
                'Rating' => $rating,
                'CreatedOn' => date("Y-m-d H:i:s"),
                'ModifiedOn' => date("Y-m-d H:i:s")
            );

            $this -> db -> insert('loyalty_products_reviews', $data);
            return "ADD";
        }
        else { 
             //Update product ratings 
            $result = $query -> row_array();
            
            $data = array(
                'UserId' => $userId,
                'LoyaltyProductId' => $loyaltyProductId,
                'Rating' => $rating,                
                'ModifiedOn' => date("Y-m-d H:i:s")
            );
            $this -> db -> where('Id', $result['Id']);    
            $this -> db -> update('loyalty_products_reviews', $data);
            return "UPDATE";
        }
    }
    
    
    /*
     * Method Name: save_loyalty_product_rating
     * Purpose: Save user's rating fro loyalty product
     * params:
     *      input: $user_id, $product_id, $special_id 
     *      output: status - FAIL / SUCCESS
     *              message - The reason if user not found fails / Success message
     *              consumptionDetails - Array containing all the details for the user consumption.
     */
    
    public function save_loyalty_product_review($userId, $loyaltyProductId, $review) {       
        # Get record 
        $this -> db -> select(array('Id'));
        $this -> db -> from('loyalty_products_reviews');
        $this -> db -> where(array(
            'UserId' => $userId,
            'LoyaltyProductId' => $loyaltyProductId
        ));

        $this -> db -> limit(1);
        $query = $this -> db -> get();
                
        //Add product ratings 
        if ($query -> num_rows() == 0) {

            $data = array(
                'UserId' => $userId,
                'LoyaltyProductId' => $loyaltyProductId,
                'Review' => $review,
                'CreatedOn' => date("Y-m-d H:i:s"),
                'ModifiedOn' => date("Y-m-d H:i:s")
            );

            $this -> db -> insert('loyalty_products_reviews', $data);
            return "ADD";
        }
        else { 
             //Update product ratings 
            $result = $query -> row_array();
            
            $data = array(
                'UserId' => $userId,
                'LoyaltyProductId' => $loyaltyProductId,
                'Review' => $review,
                'ModifiedOn' => date("Y-m-d H:i:s")
            );
            $this -> db -> where('Id', $result['Id']);    
            $this -> db -> update('loyalty_products_reviews', $data);
            return "UPDATE";
        }
    } //  public function save_loyalty_product_review($userId, $loyaltyProductId, $review)       
    
    
    /*
     * Method Name: insert_share_details
     * Purpose: Save loyalty product sharing information.
     * params:
     *      input: $user_id, $product_id, $special_id 
     *      output: status - FAIL / SUCCESS
     *              message - The reason if user not found fails / Success message
     *              consumptionDetails - Array containing all the details for the user consumption.
     */
    
    public function insert_share_details($insert_data) {
        if ($this -> db -> insert('loyalty_product_shares', $insert_data)) {
            return TRUE;
        }
        return FALSE;
    }
    
    
    /*
     * Method Name: get_loyalty_product_shares
     * Purpose: Save loyalty product sharing information.
     * params:
     *      input: $loyalty_product_id
     *      output: status - FAIL / SUCCESS
     *              message - The reason if product shares not found fails / Success message
     *              consumptionDetails - Array containing all the details for the user consumption.
     */
    public function get_loyalty_product_shares($loyalty_product_id) {
        $this -> db -> select('count(LoyaltyProductId) as count')
            -> from('loyalty_product_shares')
            -> where('LoyaltyProductId', $loyalty_product_id)
            -> where('IsActive', 1)
            -> where('IsRemoved', 0);
        $query = $this -> db -> get();
        return $query -> row_array();
    }
    
    
    /*
     * Method Name: buy_loyalty_product
     * Purpose: Save Users loyalty products buy information.
     * params:
     *      input: $insert_data
     *      output: status - FAIL / SUCCESS
     *              message - The reason if consumption record not inserted fails / Success message
     *              consumptionDetails - Array containing all the details for the user consumption.
     */
    
    public function buy_loyalty_product($insert_data) {
        if ($this -> db -> insert('user_loyalty_products', $insert_data)) {
            return $this -> db -> insert_id();
            //return TRUE;
        }
        return FALSE;
    }
    
    
    
    
    
    
    /*
     * Method Name: insert_loyalty_consumption_details
     * Purpose: Save users loyalty points consumption information.
     * params:
     *      input: $insert_data
     *      output: status - FAIL / SUCCESS
     *              message - The reason if consumption record not inserted fails / Success message
     *              consumptionDetails - Array containing all the details for the user consumption.
     */
    
    public function insert_loyalty_consumption_details($insert_data) {
        if ($this -> db -> insert('loyalty_consumption', $insert_data)) {
            return TRUE;
        }
        return FALSE;
    }
    
    
    
    /*
     * Method Name: get_loyalty_terms_and_contions
     * Purpose: Get Get loyalty terms and conditions
     * params:
     *      input: user_id
     *      output: status - FAIL / SUCCESS
     *              message - The reason if user not found fails / Success message
     *              consumptionDetails - Array containing all the details for the user consumption.
     */
    
    
    public function get_loyalty_terms_and_contions() { 
        $this -> db -> select("Id, TermsText",FALSE);        
        $this -> db -> from('loyalty_terms');         
        $this -> db -> where('IsActive', 1);
        $this -> db -> where('IsRemoved', 0);                
        $this-> db -> order_by("Id","ASC");        
        $query = $this -> db -> get();
        //echo $this->db->last_query();exit;        
        if ($query -> num_rows() > 0) {
            return $query -> result_array();
        }
        else {
            return FALSE;
        }      
    } 
    
    
    /*
     * Method Name: save_loyalty_product_rating
     * Purpose: Save user's rating fro loyalty product
     * params:
     *      input: $user_id, $product_id, $special_id 
     *      output: status - FAIL / SUCCESS
     *              message - The reason if user not found fails / Success message
     *              consumptionDetails - Array containing all the details for the user consumption.
     */
    
    public function save_loyalty_product_review_rating($userId, $loyaltyProductId, $review, $rating) {       
        # Get record 
        $this -> db -> select(array('Id'));
        $this -> db -> from('loyalty_products_reviews');
        $this -> db -> where(array(
            'UserId' => $userId,
            'LoyaltyProductId' => $loyaltyProductId
        ));

        $this -> db -> limit(1);
        $query = $this -> db -> get();
                
        //Add product ratings 
        if ($query -> num_rows() == 0) {

            $data = array(
                'UserId' => $userId,
                'LoyaltyProductId' => $loyaltyProductId,
                'Review' => $review,
                'Rating' => $rating,
                'CreatedOn' => date("Y-m-d H:i:s"),
                'ModifiedOn' => date("Y-m-d H:i:s")
            );

            $this -> db -> insert('loyalty_products_reviews', $data);
            return "ADD";
        }
        else { 
             //Update product ratings 
            $result = $query -> row_array();
            
            $data = array(
                'UserId' => $userId,
                'LoyaltyProductId' => $loyaltyProductId,
                'Review' => $review,
                'Rating' => $rating,
                'ModifiedOn' => date("Y-m-d H:i:s")
            );
            $this -> db -> where('Id', $result['Id']);    
            $this -> db -> update('loyalty_products_reviews', $data);
            return "UPDATE";
        }
    } //  save_loyalty_product_review_rating($userId, $loyaltyProductId, $review, $rating)
    
    
    /*
     * Method Name: get_loyalty_products_reviews_ratings
     * Purpose: Get loyalty products ratings and reviews details
     * params:
     *      input: user_id
     *      output: status - FAIL / SUCCESS
     *              message - The reason if user not found fails / Success message
     *              RatingReviewsDetails - Array containing all the details for the user ratings and reviews for product.
     */
    
    
    public function get_loyalty_products_reviews_ratings($userId,$loyaltyProductId) { 
        # Set default data 
        $reviewsData = array();
        $reviewsData['totalReviewsAndRatings'] = 0;
        $reviewsData['Rating'] = 0;
        $reviewsData['Review'] = "";
        
        # Get total Reviews count 
        $this -> db -> select("case when count(r.Id) is null then 0 else count(r.Id) end as totalReviewsAndRatings",FALSE);        
        $this -> db -> from('loyalty_products_reviews as r');                 
        $this -> db -> where('r.LoyaltyProductId ', $loyaltyProductId);                
        $query = $this -> db -> get();
        
        if ($query -> num_rows() > 0) {
            $totalReviewsData = $query -> row_array();
            $reviewsData['totalReviewsAndRatings'] = $totalReviewsData['totalReviewsAndRatings'];
        }
         
        # Get product's Ratings and Review   
        $this -> db -> select("r.Rating, r.Review",FALSE);        
        $this -> db -> from('loyalty_products_reviews as r');                 
        $this -> db -> where('r.LoyaltyProductId ', $loyaltyProductId);                
        $this -> db -> where('r.UserId ', $userId);
        $query = $this -> db -> get();
        
        if ($query -> num_rows() > 0) {
            $reviews = $query -> row_array();
            $reviewsData['Rating'] = $reviews['Rating'];
            $reviewsData['Review'] = $reviews['Review'];
        }
        
        return $reviewsData;
    } 
    
    
    /*
     * Method Name: get_loyalty_products_reviews_listing
     * Purpose: Get loyalty products reviews listing 
     * params:
     *      input: user_id
     *      output: status - FAIL / SUCCESS
     *              message - The reason if review listing not found fails / Success message
     *              reviews - Array containing all the details for the user consumption.
     */
    
    
    public function get_loyalty_products_reviews_listing($loyaltyProductId) {
        
        $this->db->select('r.Id, r.Review, r.Rating,r.CreatedOn, r.UserId, u.FirstName,u.LastName,u.ProfileImage');
        $this->db->join('users u', 'u.Id = r.UserId', 'left');
        $this->db->where(array(
            'r.LoyaltyProductId' => $loyaltyProductId
        ));
        $this->db->order_by('r.CreatedOn', 'DESC');
        $query = $this->db->get('loyalty_products_reviews r');
        
        if ($query -> num_rows() > 0) {
            return $query -> result_array();
        }
        else {
            return FALSE;
        }   
    } // get_loyalty_products_reviews_listing
    
    
    
    
    
    /*
     * Method Name: checked_order_dispatched
     * Purpose: Check order is dispatched or not 
     * params:
     *      input: $user_id, $product_id
     *      output: status - FAIL / SUCCESS
     *              message - The reason if user not found fails / Success message
     *              consumptionDetails - Array containing all the details for the user consumption.
     */
    
    
    public function cancel_loyalty_product_order($userId, $loyaltyProductId) {
        # Get latest order of user for the product 
        $this->db->select('Id');
        $this -> db -> from('user_loyalty_products');
        $this -> db -> where(array(
            'UserId' => $userId,
            'LoyaltyProductId' => $loyaltyProductId
        ));
        $this->db->order_by('CreatedOn', 'DESC');
        $this -> db -> limit(1);
        $query = $this -> db -> get();
        
        if ($query -> num_rows() > 0) {
            $result = $query -> row_array();
             if ( $result)
             {
                $loyaltyOrderId = $result['Id'];
                # Update Order cancellation information 
                $data = array(
                    'isOrderCancelled' => 1,
                    'cancelledBy' => $userId,            
                    'ModifiedOn' => date("Y-m-d H:i:s"),
                    'cancelledOn' => date("Y-m-d H:i:s")
                );
                $this -> db -> where('Id', $loyaltyOrderId);
                $this -> db -> update('user_loyalty_products', $data);
                
                # Revert loyalty point used for order 
                $this -> db -> where_in('LoyaltyOrderId', $loyaltyOrderId);
                $this -> db -> delete('loyalty_consumption');
        
                return "UPDATE";
             }
        }else {
            return FALSE;
        }  
        
        
    } //  public function cancel_loyalty_product_order($userId, $loyaltyProductId)
    
    /*
     * Method Name: cancel_loyalty_product_order
     * Purpose: Save user's rating fro loyalty product
     * params:
     *      input: $user_id, $product_id
     *      output: status - FAIL / SUCCESS
     *              message - The reason if user not found fails / Success message
     *              consumptionDetails - Array containing all the details for the user consumption.
     */
    
      public function check_order_dispatched($userId, $loyaltyProductId) {     
        # set default values 
        $isOrderDispatched = 0;
        
        # Get last record fro user's for the particular product
        //$this->db->select('Id, LoyaltyProductId, PointsUsed, BalancePoints, isOrderCancelled, cancelledBy, isOrderDispatched,dispatchedBy, CreatedOn, ModifiedOn,cancelledOn, dispatchedOn');
        $this->db->select('Id, isOrderDispatched,dispatchedBy, CreatedOn, ModifiedOn,cancelledOn, dispatchedOn');
        $this -> db -> from('user_loyalty_products');
        $this -> db -> where(array(
            'UserId' => $userId,
            'LoyaltyProductId' => $loyaltyProductId
        ));
        $this->db->order_by('CreatedOn', 'DESC');
        $this -> db -> limit(1);
        $query = $this -> db -> get();
        
        if ($query -> num_rows() > 0) {
            $result = $query -> row_array();
            return $result['isOrderDispatched'];
        }
    } //  public function check_order_dispatched($userId, $loyaltyProductId)
    
    
    /*
     * Method Name: check_order_cancelled
     * Purpose: Save user's rating fro loyalty product
     * params:
     *      input: $user_id, $product_id
     *      output: status - FAIL / SUCCESS
     *              message - The reason if user not found fails / Success message
     *              consumptionDetails - Array containing all the details for the user consumption.
     */
    
      public function check_order_cancelled($userId, $loyaltyProductId) {     
        # set default values 
        $isOrderDispatched = 0;
        
        # Get last record for user's for the particular product
        //$this->db->select('Id, LoyaltyProductId, PointsUsed, BalancePoints, isOrderCancelled, cancelledBy, isOrderDispatched,dispatchedBy, CreatedOn, ModifiedOn,cancelledOn, dispatchedOn');
        $this->db->select('Id,isOrderCancelled, cancelledBy,cancelledOn');
        $this -> db -> from('user_loyalty_products');
        $this -> db -> where(array(
            'UserId' => $userId,
            'LoyaltyProductId' => $loyaltyProductId
        ));
        $this->db->order_by('CreatedOn', 'DESC');
        $this -> db -> limit(1);
        $query = $this -> db -> get();
        
        if ($query -> num_rows() > 0) {
            $result = $query -> row_array();
            return $result['isOrderCancelled'];
        }
    } //  public function check_order_dispatched($userId, $loyaltyProductId)
    
    
    /*
     * Method Name: check_order_cancelled
     * Purpose: Save user's rating fro loyalty product
     * params:
     *      input: $user_id, $product_id
     *      output: status - FAIL / SUCCESS
     *              message - The reason if user not found fails / Success message
     *              consumptionDetails - Array containing all the details for the user consumption.
     */
    
      public function check_order_placed($userId, $loyaltyProductId) {     
        # set default values 
        $isOrderPlaced = 0;
        
        # Get last record for user's for the particular product
        $this->db->select('Id, LoyaltyProductId, PointsUsed, BalancePoints, isOrderCancelled, cancelledBy, isOrderDispatched,dispatchedBy, CreatedOn, ModifiedOn,cancelledOn, dispatchedOn');
        $this -> db -> from('user_loyalty_products');
        $this -> db -> where(array(
            'UserId' => $userId,
            'LoyaltyProductId' => $loyaltyProductId,
            'isOrderCancelled' => 0,
            'isOrderDispatched' => 0            
        ));
        $this->db->order_by('CreatedOn', 'DESC');
        $this -> db -> limit(1);
        $query = $this -> db -> get();
        
        if ($query -> num_rows() > 0) {
            $isOrderPlaced =1;
        }
        return $isOrderPlaced;
    } //  public function check_order_dispatched($userId, $loyaltyProductId)
    
    
    
    /*
     * Method Name: add_loyalty_product_into_cart
     * Purpose: Add loyalty product in loyalty cart
     * params:
     *      input: $insert_data
     *      output: status - FAIL / SUCCESS
     *              message - The reason if cart record not inserted fails / Success message
     *              Record Id - Id containing newly inserted record ID
     */
    
    public function add_loyalty_product_into_cart($insert_data) {
        if ($this -> db -> insert('loyalty_cart', $insert_data)) {
            return $this -> db -> insert_id();
            //return TRUE;
        }
        return FALSE;
    }
    
    
    /*
     * Method Name: get_cart_loyalty_products
     * Purpose: Get loyalty cart listing for the user
     * params:
     *      input: user_id
     *      output: status - FAIL / SUCCESS
     *              message - The reason if user not found fails / Success message
     *              consumptionDetails - Array containing all the details for the user consumption.
     */
    
    
    public function get_cart_loyalty_products($userId) { 
        $currentDate = date("Y-m-d");        
        
        //$this -> db -> select("lp.Id, lp.BrandName, lp.LoyaltyTitle, lp.ProductImage, lp.LoyaltyDescription, lp.StartDate, lp.EndDate, lp.LoyaltyPoints, c.CategoryName",FALSE);
        $this -> db -> select("lc.Id, lc.UserId, lc.LoyaltyProductId, lc.PointsUsed, lp.Id as productId, lp.BrandName, lp.LoyaltyTitle, lp.ProductImage, lp.LoyaltyDescription, lp.StartDate, lp.EndDate, lp.LoyaltyPoints, c.CategoryName",FALSE);
        
        $this -> db -> from('loyalty_cart as lc');
        $this -> db -> join('loyalty_products as lp', 'lp.Id = lc.LoyaltyProductId and lp.IsActive = 1 and lp.IsRemoved = 0', 'left');        
        $this -> db -> join('loyalty_categories as c', 'c.Id = lp.CategoryId and c.IsActive = 1 and c.IsRemoved = 0', 'left');        
        $this -> db -> where('lc.UserId', $userId);
        $this -> db -> where('lp.StartDate <=', $currentDate);
        $this -> db -> where('lp.EndDate >=', $currentDate); 
        
        $this-> db -> group_by("lc.Id");        
        $this-> db -> order_by("lc.Id","ASC");        
        $query = $this -> db -> get();
        //echo $this->db->last_query();exit;        
        if ($query -> num_rows() > 0) {
            return $query -> result_array();
        }
        else {
            return FALSE;
        }      
    }
    
    
    /*
     * Method Name: checked_order_dispatched
     * Purpose: Check order is dispatched or not 
     * params:
     *      input: $user_id, $product_id
     *      output: status - FAIL / SUCCESS
     *              message - The reason if user not found fails / Success message
     *              consumptionDetails - Array containing all the details for the user consumption.
     */
    
    
    public function remove_cart_loyalty_product($cart_ids,$user_id) {        
        if ($cart_ids!="" && $user_id > 0) { 
            $cartIdsArr = explode(",",$cart_ids);
            $isDeletedFromCart = 0;
            $deletedCount = 0;
            
            foreach($cartIdsArr as $cartId)
            {
              # Remove loyalty product from cart 
                $this -> db -> where_in('Id', $cartId);
                $this -> db -> where('UserId', $user_id);
                $this -> db -> delete('loyalty_cart');
                $isDeletedFromCart = $this->db->affected_rows();
                               
                if( $isDeletedFromCart )
                {
                    $deletedCount++;
                    # Revert loyalty point used for order 
                    $this -> db -> where_in('LoyaltyCartId', $cartId);
                    $this -> db -> where('UserId', $user_id);
                    $this -> db -> delete('loyalty_consumption',FALSE);
                }
            }
            
            return $deletedCount > 0 ? "Removed" : FALSE;
             
        }else {
            return FALSE;
        }  
        
        
    } //  public function remove_cart_loyalty_product($cart_id)
    
    
    /*
     * Method Name: save_loyalty_order
     * Purpose: Save Users loyalty products buy information.
     * params:
     *      input: $insert_data
     *      output: status - FAIL / SUCCESS
     *              message - The reason if consumption record not inserted fails / Success message
     *              consumptionDetails - Array containing all the details for the user consumption.
     */
    
    public function save_loyalty_order($insert_data) {
        if ($this -> db -> insert('loyalty_orders', $insert_data)) {
            return $this -> db -> insert_id();
            //return TRUE;
        }
        return FALSE;
    }
    
    
    /*
     * Method Name: update_loyalty_order
     * Purpose: Update Order information.
     * params:
     *      input: $update_data, 
     *      output: status - FAIL / SUCCESS
     *              message - The reason if consumption record not inserted fails / Success message
     *              consumptionDetails - Array containing all the details for the user consumption.
     */
    
    public function update_loyalty_order($update_data,$orderId) {
            $this -> db -> where('LoyaltyOrderId', $orderId);    
            $this -> db -> update('loyalty_orders', $update_data);
            return "UPDATE";
    }
    
    
    /*
     * Method Name: save_loyalty_order
     * Purpose: Save Users loyalty products buy information.
     * params:
     *      input: $insert_data
     *      output: status - FAIL / SUCCESS
     *              message - The reason if consumption record not inserted fails / Success message
     *              consumptionDetails - Array containing all the details for the user consumption.
     */
    
    public function save_loyalty_order_products($insert_data) {
        if ($this -> db -> insert('loyalty_order_products', $insert_data)) {
            return $this -> db -> insert_id();
            //return TRUE;
        }
        return FALSE;
    }
    
    
    /*
     * Method Name: get_last_order_details
     * Purpose: Get last loyalty order information
     * params:
     *      input: 
     *      output: status - FAIL / SUCCESS
     *              message - The reason if user not found fails / Success message
     *              orderDetails - Array containing all the details for the loyalty order
     */
    
      public function get_last_order_details() {     
        # Get last record for user's for the particular product
        $this->db->select('LoyaltyOrderId, OrderNumber, UserId, isOrderCancelled, cancelledBy, isOrderDispatched,dispatchedBy, CreatedOn, ModifiedOn,cancelledOn, dispatchedOn');
        $this -> db -> from('loyalty_orders');        
        $this->db->order_by('CreatedOn', 'DESC');
        $this -> db -> limit(1);
        $query = $this -> db -> get();
        //echo $this->db->last_query();exit;
        if ($query -> num_rows() > 0) {
            $result = $query -> row_array();
            return $result;
        }else{
            return FALSE;
        }
        
    } //  public function get_last_order_details()
    
    
    
    /*
     * Method Name: checked_order_dispatched
     * Purpose: Check order is dispatched or not 
     * params:
     *      input: $user_id, $product_id
     *      output: status - FAIL / SUCCESS
     *              message - The reason if user not found fails / Success message
     *              consumptionDetails - Array containing all the details for the user consumption.
     */
    
    
    public function make_cart_empty($userId) {        
        if ($userId) {             
            # Remove loyalty product from cart for the user after a successful order placed 
            $this -> db -> where_in('UserId', $userId);
            $this -> db -> delete('loyalty_cart');
            
            return "Removed";
        }else {
            return FALSE;
        }  
        
        
    } //  public function remove_cart_loyalty_product($cart_id)
    
    
    
    
            
            
            
    /*
     * Method Name: insert_loyalty_consumption_details
     * Purpose: Save users loyalty points consumption information.
     * params:
     *      input: $insert_data
     *      output: status - FAIL / SUCCESS
     *              message - The reason if consumption record not inserted fails / Success message
     *              consumptionDetails - Array containing all the details for the user consumption.
     */
    
    public function update_loyalty_consumption_details($update_data,$cartId) {
            $this -> db -> where('LoyaltyCartId', $cartId);    
            $this -> db -> update('loyalty_consumption', $update_data);
            return "UPDATE";            
        
    }
    
    
    /*
     * Method Name: get_point_allocation
     * Purpose: Get point allocation 
     * params:
     *      output: status - FAIL / SUCCESS
     *              message - The reason if user not found fails / Success message
     *              consumptionDetails - Array containing all the details for the user consumption.
     *      
     */
    
    
    public function get_point_allocation() {
        # Get last record for user's for the particular product
        $this->db->select('Id, product_reviews, product_shares, app_shares, app_install, referrer, app_shares_facebook,app_shares_twitter, app_shares_email, app_shares_google , app_shares_whatsApp, product_shares_facebook , product_shares_twitter, product_shares_email, product_shares_google, product_shares_whatsApp, store_checkin');
        $this -> db -> from('loyalty_settings');        
        $this->db->order_by('CreatedOn', 'DESC');
        $this -> db -> limit(1);
        $query = $this -> db -> get();
        //echo $this->db->last_query();exit;
        if ($query -> num_rows() > 0) {
            $result = $query -> row_array();
            return $result;
        }else{
            return FALSE;
        }        
    }
    
    /* Function to get product share counts for the user from different plateforms*/
     public function get_user_product_shares( $userId, $socialMedia, $startDate="", $endDate="" ) {         
        $this -> db -> select('count(s.Id) as userProductShares');        
        $this -> db -> from('users as u');
        $this -> db -> join('product_shares s', 's.UserId=u.Id', 'left');        
        $this -> db -> where('u.Id', $userId);
        
        if($socialMedia == 'W')
        {
            $this->db->where("(s.SocialMedia = '$socialMedia' OR s.SocialMedia = '')");
        }else{
            $this -> db -> where('s.SocialMedia', $socialMedia);
        }
        
        $this -> db -> where('u.IsRemoved', 0);
         $this -> db -> where('s.IsActive', 1);
        $this -> db -> where('s.IsRemoved', 0);
        if( $startDate != "" && $endDate != "" )
        {
            $this -> db -> where('s.ShareDate >=', $startDate);
            $this -> db -> where('s.ShareDate <=', $endDate);
        }
        $query = $this -> db -> get();
        
        
        //echo $this->db->last_query();exit;
        if ($query -> num_rows() > 0) {
            $result = $query -> row_array();
            return $result['userProductShares'];
        }else{
            return 0;
        }    
    }
     
    /* Function to get app share counts for the user from different plateforms*/
    public function get_user_app_shares( $userId, $socialMedia, $startDate="", $endDate="" ) {         
        $this -> db -> select('count(s.Id) as userAppShares');        
        $this -> db -> from('users as u');
        $this -> db -> join('app_shares s', 's.UserId=u.Id', 'left');        
        $this -> db -> where('u.Id', $userId);
        
        if($socialMedia == 'W')
        {
            $this->db->where("(s.SocialMedia = '$socialMedia' OR s.SocialMedia = '')");
        }else{
            $this -> db -> where('s.SocialMedia', $socialMedia);
        }
        
        $this -> db -> where('u.IsRemoved', 0);
         $this -> db -> where('s.IsActive', 1);
        $this -> db -> where('s.IsRemoved', 0);
        if( $startDate != "" && $endDate != "" )
        {
            $this -> db -> where('s.ShareDate >=', $startDate);
            $this -> db -> where('s.ShareDate <=', $endDate);
        }
        $query = $this -> db -> get();        
        
        //echo $this->db->last_query();exit;
        if ($query -> num_rows() > 0) {
            $result = $query -> row_array();
            return $result['userAppShares'];
        }else{
            return 0;
        }    
    }
}