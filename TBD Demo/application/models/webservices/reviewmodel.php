<?php

/*
 * Author: Name:PHN
 * Purpose: Model for controlling database interactions regarding the content.
 * Date:04-09-2015
 * Dependency: None
 */

class reviewmodel extends CI_Model {

    public function get_product_reviews($product_id) {
        $this->db->select('productsreviews.Id,
                           productsreviews.Review,
                           productsreviews.Rating,
                           productsreviews.CreatedOn,
                           productsreviews.UserId,
                           users.FirstName,
                           users.LastName,
                           users.ProfileImage');

        $this->db->join('users', 'users.Id = productsreviews.UserId', 'left');

        $this->db->where(array(
            'productsreviews.ProductId' => $product_id
        ));

        $this->db->order_by('productsreviews.CreatedOn', 'DESC');

        $query = $this->db->get('productsreviews');
        return $query->result_array();
    }

    public function add_review_rating($user_id, $product_id, $review_comment, $review_rating) {
        $this->db->select(array('Id'));
        $this->db->from('productsreviews');
        $this->db->where(array(
            'UserId' => $user_id,
            'ProductId' => $product_id
        ));

        $this->db->limit(1);
        $query = $this->db->get();

        if ($query->num_rows() == 0) {

            $data = array(
                'UserId' => $user_id,
                'ProductId' => $product_id,
                'Review' => $review_comment,
                'Rating' => $review_rating,
                'CreatedOn' => date("Y-m-d H:i:s")
            );

            $this->db->insert('productsreviews', $data);

            return $this->db->insert_id();
        } else {
            return FALSE;
        }
    }


    public function update_review_rating($user_id, $product_id, $review_comment, $review_rating) {

        $this->db->where(array(
            'UserId' => $user_id,
            'ProductId'  => $product_id
        ));

        $this->db->update('productsreviews', array(
            'Review' =>     $review_comment,
            'Rating' =>    $review_rating
        ));

        return TRUE;
    }

    public function delete_review_rating($user_id, $product_id) {

        //Delete the user review

        $this->db->delete('productsreviews',array(
            'UserId' => $user_id,
            'ProductId'  => $product_id
        ));

       return TRUE;
    }
    
    
    public function get_products_all_reviews($product_id) {
        
        $review1 ="This product is amazing";
        $review2 ="I like this product";
        $review3 ="Not really sure about this product";
        $review4 ="I dont like this product";
        
        
        # Get reviews count for each type of reviews
        $this->db->select('count(productsreviews.Id) as reviewCount');
        $this->db->where(array(
            'productsreviews.ProductId' => $product_id,
            'productsreviews.Review' => $review1
        ));
        $query1 = $this->db->get('productsreviews');  
        $result1 = $query1 -> row_array();
        $reviewCount1 = $result1['reviewCount'];
        
        
        # Get reviews count for each type of reviews
        $this->db->select('count(productsreviews.Id) as reviewCount');
        $this->db->where(array(
            'productsreviews.ProductId' => $product_id,
            'productsreviews.Review' => $review2
        ));
        $query2 = $this->db->get('productsreviews');         
        $result2 = $query2 -> row_array();
        $reviewCount2 = $result2['reviewCount'];
        
        
        # Get reviews count for each type of reviews
        $this->db->select('count(productsreviews.Id) as reviewCount');
        $this->db->where(array(
            'productsreviews.ProductId' => $product_id,
            'productsreviews.Review' => $review3
        ));
        $query3 = $this->db->get('productsreviews');         
        $result3 = $query3 -> row_array();
        $reviewCount3 = $result3['reviewCount'];
        
        
        # Get reviews count for each type of reviews
        $this->db->select('count(productsreviews.Id) as reviewCount');
        $this->db->where(array(
            'productsreviews.ProductId' => $product_id,
            'productsreviews.Review' => $review4
        ));
        $query4 = $this->db->get('productsreviews');         
        $result4 = $query4 -> row_array();
        $reviewCount4 = $result4['reviewCount'];
        
        
        $allReviews = array();
        
        $allReviews[0]['ReviewId'] = "1";
        $allReviews[0]['ReviewText'] = $review1;
        $allReviews[0]['ReviewCount'] = $reviewCount1;
                
        $allReviews[1]['ReviewId'] = "2";
        $allReviews[1]['ReviewText'] = $review2;
        $allReviews[1]['ReviewCount'] = $reviewCount2;
        
        $allReviews[2]['ReviewId'] = "3";
        $allReviews[2]['ReviewText'] = $review3;
        $allReviews[2]['ReviewCount'] = $reviewCount3;
        
        $allReviews[3]['ReviewId'] = "4";
        $allReviews[3]['ReviewText'] = $review4;
        $allReviews[3]['ReviewCount'] = $reviewCount4;
        
        return $allReviews;
        
    }
    
}
?>
