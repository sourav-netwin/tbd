<?php

/*
 * Author:AS
 * Purpose:Insight Controller
 * Description: If a store is premium category, then only this page will be visible.
 * Date:30-01-2017
 * Dependency: insightmodel.php
 */

//error_reporting(E_ALL);
//ini_set('display_errors', 1);

class Insights extends My_Controller {

    private $result;
    private $message;

    function __construct() {
        parent::__construct();

        $this -> load -> model('admin/insightmodel', '', TRUE);

        $premium = $this -> insightmodel -> check_premium();
        if (isset($premium['Premium'])) {
            if ($premium['Premium'] != 1) {
                redirect('/', TRUE);
            }
        }
        else {
            redirect('/', TRUE);
        }

        $this -> page_title = "Insights";
        $this -> breadcrumbs[] = array('label' => 'Insights', 'url' => '/insights');
    }

    public function index() {
        //echo "Shri Gajanan, Jay Gajanan";exit;
        $data['title'] = $this -> page_title;
        $data['breadcrumbs'] = $this -> breadcrumbs;
        $data['users'] = $this -> insightmodel -> get_store_user_count();
        $data['products'] = $this -> insightmodel -> get_store_product_count();
        $data['categories'] = $this -> insightmodel -> get_store_category_count();

        $this -> template -> view('admin/insights/dashboard', $data);
    }

    public function get_consumers_count_expansion() {
        if ($this -> input -> server('REQUEST_METHOD') == 'POST') {
            $region_user_count_details = $this -> insightmodel -> get_region_consumer_count();
            $gender_user_count_details = $this -> insightmodel -> get_gender_consumer_count();
            $device_count_array = $this -> insightmodel -> get_user_device_count();
            $age_count_details = $this -> insightmodel -> get_user_age_count();
            $special_count_array = [];
            $region_count_array = [];
            $gender_count_array = [];
            $age_count_array = [];
            if ($region_user_count_details) {
                foreach ($region_user_count_details as $region) {
                    $region_count_array[] = array(
                        'label' => $region['state_name'],
                        'y' => (int) $region['count'],
                        'id' => $region['Id']
                    );
                }
            }
            if ($gender_user_count_details) {
                foreach ($gender_user_count_details as $gender) {
                    $gender_count_array[] = array(
                        'label' => $gender['gender_exp'],
                        'y' => (int) $gender['count'],
                        'id' => $gender['Gender']
                    );
                }
            }
//            if ($age_count_details) {
//                foreach ($age_count_details as $age) {
//                    $age_count_array[] = array(
//                        'label' => $age['gender_exp'],
//                        'y' => (int)$age['count'],
//                        'id' => $age['Gender']
//                    );
//                }
//            }
            $this -> result = 1;
            $this -> message['region_users_count'] = $region_count_array;
            $this -> message['gender_users_count'] = $gender_count_array;
            $this -> message['device_users_count'] = $device_count_array;
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

    public function get_products_count_expansion() {
        if ($this -> input -> server('REQUEST_METHOD') == 'POST') {
            $category_sub_total_list = $this -> insightmodel -> get_category_sub_total();
            if ($category_sub_total_list) {
                $category_array = [];
                foreach ($category_sub_total_list as $category) {
                    $category_array[$category['main_cat_id'] . '::' . $category['main_cat']][] = array(
                        'label' => $category['parent_cat'],
                        'y' => (int) $category['count'],
                        'id' => $category['parent_cat_id'],
                        'main_cat' => $category['main_cat_id']
                    );
                }
                $this -> result = 1;
                $this -> message = $category_array;
            }
            else {
                $this -> result = 0;
                $this -> message = 'No Details Found';
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

    public function get_category_sub_count_expansion() {
        set_time_limit(0);
        ini_set('memory_limit', '512M');
        if ($this -> input -> server('REQUEST_METHOD') == 'POST') {
            $main_cat = sanitize($this -> input -> post('main_cat'));
            $parent_cat = sanitize($this -> input -> post('parent_cat'));
            $product_details = $this -> insightmodel -> get_product_expansion_details($main_cat, $parent_cat);
            if ($product_details) {
                $html = '<table class="table table-bordered" id="cat_sub_table">
                    <thead>
                        <tr>
                            <th>Product Name</th>
                            <th>Category</th>
                            <th>Brand</th>
                            <th>Price</th>
                        </tr>
                    </thead><tbody>';
                foreach ($product_details as $product) {
                    $html .= '<tr>
                        <td>' . $product['ProductName'] . '</td>
                        <td>' . $product['CategoryName'] . '</td>
                        <td>' . $product['Brand'] . '</td>
                        <td>' . $product['RRP'] . '</td>
                        </tr>';
                }
                $html .= '</tbody></table>';
                $this -> result = 1;
                $this -> message = $html;
            }
            else {
                $this -> result = 0;
                $this -> message = 'No Products Found';
            }
        }
        else {
            $this -> result = 0;
            $this -> message = 'Invalid data';
        }
        echo json_encode(
            array(
                'result' => $this -> result,
                'message' => $this -> message
            )
        );
    }

    public function get_user_view_chart() {
        $chart_details = $this -> insightmodel -> get_user_view_chart();
        if ($chart_details) {
            $this -> result = 1;
            $this -> message = $chart_details;
        }
        else {
            $this -> result = FALSE;
            $this -> message = 'No records found';
        }
        echo json_encode(
            array(
                'result' => $this -> result,
                'message' => $this -> message
            )
        );
    }
    
    /*
     * Get the Goal Completion data
     * Show top 5 categories fro product view with percentage
     */
    public function get_goal_completion_view() {
        # Get all product view data
        $goal_details = $this -> insightmodel -> get_goal_completion_view();
        
        # Set default values 
        $index = $totalViews = 0;
        $maxRecord = 5;
        $goals = array();
        
        if ($goal_details) {
            foreach($goal_details as $singleRow)
            {
                $totalViews = $totalViews + $singleRow['views'];
            }
            
            # Show only required data and do total view calculation
            foreach($goal_details as $singleRow)
            {
                if($index < 5 )
                {
                    $goals[$index]['CategoryName'] =  $singleRow['CategoryName']; 
                    $goals[$index]['views']        =  $singleRow['views'];
                    $goals[$index]['viewsPercentage'] =  round(( $singleRow['views'] * 100) / $totalViews);
                }
                $index++;
            }
        
            $this -> result = 1;
            $this -> totalViews = $totalViews;
            $this -> message = $goals;
        }
        else {
            $this -> result = FALSE;
            $this -> totalViews = 0;
            $this -> message = 'No records found';
        }
        echo json_encode(
            array(
                'result' => $this -> result,
                'totalViews' => $this -> totalViews,
                'message' => $this -> message
            )
        );
    }
    
    
    
    public function get_yearly_view() {
        /*
         * Show last 12 months from current date
         * Put data in array according to month , maintain sequence
         * 
         */
        
        $chart_details = $this -> insightmodel -> get_yearly_view_chart();
        
        $allMonths = array();
        
        
        for( $monthIndex=0; $monthIndex < 12; $monthIndex++)
        {
           $monthNumber =  date('m', strtotime(date('Y-m')." -$monthIndex month")); 
           $monthName =  date('M', strtotime(date('Y-m')." -$monthIndex month"));
           $yearNumber =  date('Y', strtotime(date('Y-m')." -$monthIndex month")); 
           
           $allMonths[$monthIndex]['monthNumber'] = $monthNumber; 
           $allMonths[$monthIndex]['yearNumber'] = $yearNumber; 
           $allMonths[$monthIndex]['monthName'] = $monthName;
           $allMonths[$monthIndex]['monthYear'] = $monthName."-".$yearNumber;
           
        }
        $allMonths = array_reverse($allMonths);
        
        # Compare with view data is available for the month then set it otherwise make it o for the month 
        foreach($allMonths as $allMonthsKey=>$allMonthsValue)
        {
            $found = false;
            foreach($chart_details as $viewKey=>$viewValue)
            {
               if($allMonthsValue['monthNumber'] == $viewValue['month_number'] && $allMonthsValue['yearNumber'] == $viewValue['year'])
               {
                   $found = true;
                   break; 
               }
            }
            
            if($found == true)
            {
                $allMonths[$allMonthsKey]['views'] = $viewValue['views'];
            }else{
                $allMonths[$allMonthsKey]['views']  = 0;
            }
        }
        
        
        
        echo "<pre>";
        print_r($allMonths);
        exit;
        
        //echo date('Y-m-d');
        //echo "<br>".date('Y', strtotime(date('Y-m')." -1 month"));
        //echo "<br>".date('m', strtotime(date('Y-m')." -1 month"));
        //echo "<br>".date('Y-m-d', strtotime(date('Y-m')." -2 month"));
        //echo "<br>".date('Y-m-d', strtotime(date('Y-m')." -3 month"));
        print_r($chart_details);
        exit;
        
        # Set default values 
        $index = 0;        
        $monthData = $availableMonths =  array();
        $allMonths = array(1,2,3,4,5,6,7,8,9,10,11,12);
        
        if ($chart_details) {
            
            # Get available months from data
            foreach($chart_details as $singleRow)
            {
               $availableMonths[ $singleRow['month_number'] ] =  $singleRow['views'];
            }
            
            # Check if data available fro all months
            foreach($allMonths as $oneMonth)
            {
                if (array_key_exists($oneMonth, $availableMonths)) {
                   $monthData[$index]['month_number'] =  $oneMonth;      
                   $monthData[$index]['views']        =  $availableMonths[$oneMonth];
                }else{
                   $monthData[$index]['month_number'] =  $index + 1;  
                   $monthData[$index]['views']        =  0;
                }
                $index++;
            }
            
            $this -> result = 1;
            $this -> message = $monthData;
        }
        else {
            $this -> result = FALSE;
            $this -> message = 'No records found';
        }
        echo json_encode(
            array(
                'result' => $this -> result,
                'message' => $this -> message
            )
        );
    }
    
}