<?php

/*
 * Author: Name:AS
 * Purpose: Model for controlling database interactions regarding the content.
 * Date:30-01-2017
 * Dependency: None
 */

class Insightmodel extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function get_store_user_count() {
        $this -> db -> select('count(distinct a.UserId) as users_count', FALSE)
            -> from('userpreferredbrands as a')
            -> join('stores as b', 'a.StoreId = b.Id')
            -> join('users as c', 'c.Id = a.UserId')
            -> where(
                array(
                    'c.IsActive' => 1,
                    'c.IsRemoved' => 0,
                    'b.IsActive' => 1,
                    'b.IsRemoved' => 0,
                    'b.Id' => $this -> session -> userdata('user_store_id')
                )
        );
        $query = $this -> db -> get();
        if ($query -> num_rows() > 0) {
            return $query -> row_array();
        }
        return ['users_count' => 0];
    }

    public function get_store_product_count() {
        $this -> db -> select('count(distinct a.ProductId) as products_count', FALSE)
            -> from('storeproducts as a')
            -> join('products as b', 'b.Id = a.ProductId')
            -> where(
                array(
                    'a.StoreId' => $this -> session -> userdata('user_store_id'),
                    'a.IsActive' => 1,
                    'a.IsRemoved' => 0,
                    'a.IsActive' => 1,
                    'a.IsRemoved' => 0
                )
        );
        $query = $this -> db -> get();
        if ($query -> num_rows() > 0) {
            return $query -> row_array();
        }
        return ['products_count' => 0];
    }

    public function get_store_category_count() {
        $this -> db -> select('count(distinct d.Id) as categories_count', FALSE)
            -> from('storeproducts as a')
            -> join('stores as b', 'b.Id = a.StoreId')
            -> join('products as c', 'c.Id = a.ProductId')
            -> join('categories as d', 'd.Id = c.MainCategoryId')
            -> where(
                array(
                    'a.StoreId' => $this -> session -> userdata('user_store_id'),
                    'a.IsActive' => 1,
                    'a.IsRemoved' => 0,
                    'b.IsActive' => 1,
                    'b.IsRemoved' => 0,
                    'c.IsActive' => 1,
                    'c.IsRemoved' => 0,
                    'd.IsActive' => 1,
                    'd.IsRemoved' => 0
                )
        );
        $query = $this -> db -> get();
        if ($query -> num_rows() > 0) {
            return $query -> row_array();
        }
        return array('categories_count' => 0);
    }

    public function get_region_consumer_count() {
        $this -> db -> select('count(b.Id) as count, a.Name as state_name, a.Id', FALSE)
            -> from('state as a')
            -> join('users as b', 'b.State = a.Id and b.IsActive = 1 and b.IsRemoved = 0')
            -> join('userpreferredbrands as c', 'c.UserId = b.Id and c.StoreId = ' . $this -> session -> userdata('user_store_id'))
            -> where(
                array(
                    'a.IsActive' => 1,
                    'a.IsRemoved' => 0
                )
            )
            -> where('(b.UserRole = 0 or b.UserRole = 4)', NULL, FALSE)
            -> group_by('a.Id')
            -> order_by('count', 'desc');
        $query = $this -> db -> get();
        if ($query -> num_rows() > 0) {
            return $query -> result_array();
        }
        return FALSE;
    }

    public function get_gender_consumer_count() {
        $this -> db -> select("count(a.Id) as count, case when a.Gender = 'M' then 'Male' when a.Gender = 'F' then 'Female' else 'Not Mentioned' end as gender_exp, a.Gender", FALSE)
            -> from('users as a')
            -> join('userpreferredbrands as b', 'b.UserId = a.Id and b.StoreId = ' . $this -> session -> userdata('user_store_id'))
            -> where(
                array(
                    'a.IsActive' => 1,
                    'a.IsRemoved' => 0
                )
            )
            -> where('(a.UserRole = 0 or a.UserRole = 4)', NULL, FALSE)
            -> group_by('a.Gender')
            -> order_by('count', 'desc');
        $query = $this -> db -> get();
        if ($query -> num_rows() > 0) {
            return $query -> result_array();
        }
        return FALSE;
    }

    public function get_user_device_count() {
        $this -> db -> select("sum(case when b.DeviceType = 'A' then 1 else 0 end) as android_count,sum(case when b.DeviceType = 'I' then 1 else 0 end) as ios_count,sum(case when b.Id is null then 1 else 0 end) as web_count", FALSE)
            -> from('users as a')
            -> join('userdevices as b', 'a.Id = b.UserId', 'left')
            -> join('userpreferredbrands as c', 'c.UserId = a.Id and c.StoreId = ' . $this -> session -> userdata('user_store_id'))
            -> where(
                array(
                    'a.IsActive' => 1,
                    'a.IsRemoved' => 0
                )
            )
            -> where('(a.UserRole = 0 or a.UserRole = 4)', NULL, FALSE)
            -> group_by('b.DeviceType');
        $query = $this -> db -> get();
        if ($query -> num_rows() > 0) {
            $result_array = $query -> result_array();
            $android_count = 0;
            $ios_count = 0;
            $web_count = 0;
            foreach ($result_array as $res) {
                $android_count += $res['android_count'];
                $ios_count += $res['ios_count'];
                $web_count += $res['web_count'];
            }
            return array(
                array(
                    'label' => 'Android',
                    'y' => $android_count,
                    'id' => 'A'
                ),
                array(
                    'label' => 'iOS',
                    'y' => $ios_count,
                    'id' => 'I'
                ),
                array(
                    'label' => 'Web',
                    'y' => $web_count,
                    'id' => 'W'
                ),
            );
        }
        return FALSE;
    }

    public function get_category_sub_total() {
        $this -> db -> select('count(distinct b.Id) as count, a.Id as main_cat_id,a.CategoryName as main_cat, c.Id as parent_cat_id,c.CategoryName as parent_cat', FALSE)
            -> from('categories as a')
            -> join('products as b', 'b.MainCategoryId = a.Id')
            -> join('categories as c', 'c.Id = b.ParentCategoryId')
            -> join('storeproducts as d', 'd.ProductId = b.Id and d.StoreId = ' . $this -> session -> userdata('user_store_id'))
            -> where(
                array(
                    'a.IsActive' => 1,
                    'a.IsRemoved' => 0,
                    'b.IsActive' => 1,
                    'b.IsRemoved' => 0,
                    'd.IsActive' => 1,
                    'd.IsRemoved' => 0
                )
            )
            -> group_by('c.Id')
            -> order_by('count', 'desc');
        $query = $this -> db -> get();
        if ($query -> num_rows() > 0) {
            return $query -> result_array();
        }
        return FALSE;
    }

    public function get_product_expansion_details($main_cat, $parent_cat) {
        $this -> db -> select('a.ProductName, a.Brand, b.CategoryName, a.RRP')
            -> from('products as a')
            -> join('categories as b', 'b.Id = a.CategoryId and b.IsActive = 1 and b.IsRemoved = 0', 'left')
            -> join('storeproducts as c', 'c.ProductId = a.Id and c.IsActive = 1 and c.IsRemoved = 0 and c.StoreId = ' . $this -> session -> userdata('user_store_id'))
            -> where(
                array(
                    'a.IsActive' => 1,
                    'a.IsRemoved' => 0,
                    'a.MainCategoryId' => $main_cat,
                    'a.ParentCategoryId' => $parent_cat
                )
        );
        $query = $this -> db -> get();
//        echo $this -> db -> last_query();die;
        if ($query -> num_rows() > 0) {
            return $query -> result_array();
        }
        return FALSE;
    }

    public function check_premium() {
        $this -> db -> select('Premium')
            -> from('promotions')
            -> where(
                array(
                    'StoreId' => $this -> session -> userdata('user_store_id')
                )
        );
        $query = $this -> db -> get();
//        echo $this -> db -> last_query();die;
        if ($query -> num_rows() > 0) {
            return $query -> row_array();
        }
        return FALSE;
    }

    public function get_user_age_count() {
        $query = $this -> db -> query("SELECT
                    CASE 
                    WHEN TIMESTAMPDIFF (YEAR, a.DateOfBirth, CURDATE()) < 18 THEN '< 18'
                    WHEN TIMESTAMPDIFF (YEAR, a.DateOfBirth, CURDATE()) BETWEEN 18 AND 25 THEN '18 to 25'
                    WHEN TIMESTAMPDIFF (YEAR, a.DateOfBirth, CURDATE()) BETWEEN 26 and 30 THEN '26 to 30'
                    WHEN TIMESTAMPDIFF (YEAR, a.DateOfBirth, CURDATE()) BETWEEN 31 and 40 THEN '31 to 40'
                    WHEN TIMESTAMPDIFF (YEAR, a.DateOfBirth, CURDATE()) BETWEEN 41 and 50 THEN '41 to 50'
                    WHEN TIMESTAMPDIFF (YEAR, a.DateOfBirth, CURDATE()) >= 50 THEN '50 +' END AS agegroup, 
                    sum( case when TIMESTAMPDIFF (YEAR, a.DateOfBirth, CURDATE()) is not null then 1 else 0 end) AS total
                    FROM users as a
                    join userpreferredbrands as b on b.UserId = a.Id
                    where a.IsActive = 1
                    and a.IsRemoved = 0
                    and b.StoreId = " . $this -> session -> userdata('user_store_id') . "
                    GROUP BY agegroup
                    having agegroup is not null");
        if ($query -> num_rows() > 0) {
            return $query -> result_array();
        }
        return [];
    }

    public function get_user_view_chart() {
        $this -> db -> select("count(Id) as views, substr(DAYNAME(ViewDate),1,3) as day,
            case when substr(DAYNAME(ViewDate),1,3) = 'Sun' then 1
            when substr(DAYNAME(ViewDate),1,3) = 'Mon' then 2
            when substr(DAYNAME(ViewDate),1,3) = 'Tue' then 3
            when substr(DAYNAME(ViewDate),1,3) = 'Wed' then 4
            when substr(DAYNAME(ViewDate),1,3) = 'Thu' then 5
            when substr(DAYNAME(ViewDate),1,3) = 'Fri' then 6
            when substr(DAYNAME(ViewDate),1,3) = 'Sat' then 7
            end as day_number", FALSE)
            -> from('productviews')
            -> where('ViewDate > DATE_ADD(Now(), INTERVAL - 6 MONTH)', NULL, FALSE)
            -> where('UserId > 0', NULL, FALSE)
            -> group_by('day')
            -> order_by('day_number');
        $query = $this -> db -> get();
        if ($query -> num_rows() > 0) {
            return $query -> result_array();
        }
        return FALSE;
    }
    
   /*
   * Function to get popular categories based on product views
   */ 
    
    public function get_popular_category_view() {
        $this -> db -> select('count(v.id) as views, c.CategoryName')
            -> from('productviews as v')
            -> join('products as p', 'p.Id = v.ProductId and p.IsActive = 1 and p.IsRemoved = 0')
            -> join('categories as c', 'c.Id = p.MainCategoryId and c.IsActive = 1 and c.IsRemoved = 0')            
            -> group_by('c.id')
            -> order_by('views','desc');
        $query = $this -> db -> get();
        if ($query -> num_rows() > 0) {
            return $query -> result_array();
        }
        return FALSE;
    }
    
    /*
     * Function to get last year product view data
     */  
    public function get_yearly_view_chart() {
        $this -> db -> select("count(productviews.Id) as views, MONTHNAME(ViewDate) as month, YEAR(ViewDate) as year,
            case when substr(MONTHNAME(ViewDate),1,3) = 'Jan' then 1
            when substr(MONTHNAME(ViewDate), 1, 3) = 'Feb' then 2
            when substr(MONTHNAME(ViewDate), 1, 3) = 'Mar' then 3
            when substr(MONTHNAME(ViewDate), 1, 3) = 'Apr' then 4
            when substr(MONTHNAME(ViewDate), 1, 3) = 'May' then 5
            when substr(MONTHNAME(ViewDate), 1, 3) = 'Jun' then 6
            when substr(MONTHNAME(ViewDate), 1, 3) = 'Jul' then 7
            when substr(MONTHNAME(ViewDate), 1, 3) = 'Aug' then 8
            when substr(MONTHNAME(ViewDate), 1, 3) = 'Sep' then 9
            when substr(MONTHNAME(ViewDate), 1, 3) = 'Oct' then 10
            when substr(MONTHNAME(ViewDate), 1, 3) = 'Nov' then 11
            when substr(MONTHNAME(ViewDate), 1, 3) = 'Dec' then 12
            end as month_number", FALSE)
            -> from('productviews')
            -> join('products as p', 'p.Id = productviews.ProductId and p.IsActive = 1 and p.IsRemoved = 0')    
            -> where('ViewDate > DATE_ADD(Now(), INTERVAL - 12 MONTH)', NULL, FALSE)
            -> where('UserId > 0', NULL, FALSE)
            -> group_by('month')
            -> order_by('month_number');
        $query = $this -> db -> get();        
        if ($query -> num_rows() > 0) {
            return $query -> result_array();
        }
        return FALSE;
    }
    
    
    /*
     * Function to get last year product view data
     */  
    public function get_specials() {
        $storeId = $this -> session -> userdata('user_store_id');
        
        $this -> db -> select("count(s.Id) as specials_count, MONTHNAME(s.SpecialTo) as month, YEAR(s.SpecialTo) as year,
            case when substr(MONTHNAME(s.SpecialTo),1,3) = 'Jan' then 1
            when substr(MONTHNAME(s.SpecialTo), 1, 3) = 'Feb' then 2
            when substr(MONTHNAME(s.SpecialTo), 1, 3) = 'Mar' then 3
            when substr(MONTHNAME(s.SpecialTo), 1, 3) = 'Apr' then 4
            when substr(MONTHNAME(s.SpecialTo), 1, 3) = 'May' then 5
            when substr(MONTHNAME(s.SpecialTo), 1, 3) = 'Jun' then 6
            when substr(MONTHNAME(s.SpecialTo), 1, 3) = 'Jul' then 7
            when substr(MONTHNAME(s.SpecialTo), 1, 3) = 'Aug' then 8
            when substr(MONTHNAME(s.SpecialTo), 1, 3) = 'Sep' then 9
            when substr(MONTHNAME(s.SpecialTo), 1, 3) = 'Oct' then 10
            when substr(MONTHNAME(s.SpecialTo), 1, 3) = 'Nov' then 11
            when substr(MONTHNAME(s.SpecialTo), 1, 3) = 'Dec' then 12
            end as month_number", FALSE)
            -> from('specials as s')
            -> join('productspecials as ps', 'ps.SpecialId = s.Id and ps.IsActive = 1 and ps.IsApproved  = 1')    
            -> where('s.SpecialTo > DATE_ADD(Now(), INTERVAL - 6 MONTH)', NULL, FALSE)
            -> where('ps.StoreId', $storeId)
            -> group_by('month')
            -> order_by('month_number');
            
            $query = $this -> db -> get();  
            
             //echo $this->db->last_query(); exit;
            
            if ($query -> num_rows() > 0) {
                return $query -> result_array();
            }
            return FALSE;
    }
    
    
    /*
     * Function to get last year product view data
     */  
    public function get_visitors() {
        $this -> db -> select("count(u.Id) as users_count,s.name as statename, UPPER(s.StateCode) as stateCode", FALSE)
            -> from('users as u')
            -> join('state as s', 's.Id = u.State')                
            -> where('u.IsActive', 1)
            -> where('u.IsRemoved', 0)    
            -> group_by('s.Id');
            
            $query = $this -> db -> get();
            
            if ($query -> num_rows() > 0) {
                return $query -> result_array();
            }
            return FALSE;
    }
    
    /*
     * Function to get product view details for the given month 
     */  
    public function get_product_view_details($startdate, $endDate) {        
        $this -> db -> select('count(v.id) as views, c.CategoryName as MainCategoryName, `s`.`CategoryName` as SubCategoryName, `ss`.`CategoryName` as SubSubCategoryName')
            -> from('productviews as v')
            -> join('products as p', 'p.Id = v.ProductId and p.IsActive = 1 and p.IsRemoved = 0')
            -> join('categories as c', 'c.Id = p.MainCategoryId')
            -> join('categories as s', 's.Id = p.ParentCategoryId')
            -> join('categories as ss', 'ss.Id = p.CategoryId','left')
            -> where('ViewDate >=', $startdate)    
            -> where('ViewDate <=', $endDate) 
            -> where('UserId > 0', NULL, FALSE)    
            -> group_by('c.id')
            -> order_by('views','desc');
        $query = $this -> db -> get();
        //echo $this->db->last_query();exit;
        if ($query -> num_rows() > 0) {
            return $query -> result_array();
        }
        
    }
    
}