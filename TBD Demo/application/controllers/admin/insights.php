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
     * Get the Popular Categories data based on product views
     * Show top 5 categories for product view with percentage.
     */
    public function get_popular_category_view() {
        # Get all product view data
        $category_details = $this -> insightmodel -> get_popular_category_view();
        
        # Set default values 
        $index = $totalViews = 0;
        $maxRecord = 5;
        $categories = array();
        
        if ($category_details) {
            foreach($category_details as $singleRow)
            {
                $totalViews = $totalViews + $singleRow['views'];
            }
            
            # Show only required data and do total view calculation
            foreach($category_details as $singleRow)
            {
                if($index < 5 )
                {
                    $categories[$index]['CategoryName'] =  $singleRow['CategoryName']; 
                    $categories[$index]['views']        =  $singleRow['views'];
                    $categories[$index]['viewsPercentage'] =  round(( $singleRow['views'] * 100) / $totalViews);
                }
                $index++;
            }
        
            $this -> result = 1;
            $this -> totalViews = $totalViews;
            $this -> message = $categories;
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
    
    /*
     * Function Name : get_yearly_view
     * Purpose       : Get yearly product views data.
     * Parameters    : No Parameters
     */
    
    public function get_yearly_view() {
        # Set default values 
        $allMonths = array();
        
        # Get product views data for previous year
        $view_details = $this -> insightmodel -> get_yearly_view_chart();
        
        if ($view_details) {
            # Set title for the map
            $startDate  =  date('d M,Y', strtotime(date('Y-m-d')." -11 month"));
            $endDate    =  date('d M,Y', strtotime(date('Y-m-d')));
            $duration   = "Product Views: ".$startDate." - ".$endDate;
            
            # Get last 12 months from the current date , including current month 
            for( $monthIndex=0; $monthIndex < 12; $monthIndex++)
            {
                $monthNumber =  date('m', strtotime(date('Y-m')." -$monthIndex month")); 
                $monthName   =  date('M', strtotime(date('Y-m')." -$monthIndex month"));
                $yearNumber  =  date('Y', strtotime(date('Y-m')." -$monthIndex month")); 

                $allMonths[$monthIndex]['monthNumber'] = $monthNumber; 
                $allMonths[$monthIndex]['yearNumber'] = $yearNumber; 
                $allMonths[$monthIndex]['monthName'] = $monthName;
                $allMonths[$monthIndex]['monthYear'] = $monthName."-".$yearNumber;
            }
            
            # Make the reveserse array as we need from last 11th to current month 
            $allMonths = array_reverse($allMonths);

            # Compare with view data is available for the month then set it otherwise make it o for the month 
            foreach($allMonths as $allMonthsKey=>$allMonthsValue)
            {
                $found = false;
                foreach($view_details as $viewKey=>$viewValue)
                {
                    if($allMonthsValue['monthNumber'] == $viewValue['month_number'] && $allMonthsValue['yearNumber'] == $viewValue['year'])
                    {
                        $found = true;
                        break; 
                    }
                }
                $allMonths[$allMonthsKey]['views'] = $found == true ? $viewValue['views'] : 0;
            }
           
            $this -> result = 1;
            $this -> message = $allMonths;
            $this -> duration = $duration;
        }
        else {
            $this -> result = FALSE;
            $this -> message = 'No records found';
            $this -> duration = '';
        }
        echo json_encode(
            array(
                'result' => $this -> result,
                'message' => $this -> message,
                'duration' => $this -> duration
            )
        );
    }
    
    
    
    /*
     * Function Name : get_specials
     * Purpose       : Get specials count for last 6 months..
     * Parameters    : No Parameters
     */
    
    public function get_specials() {
        $this -> session -> userdata('user_store_id');
        
        # Set default values 
        $allMonths = array();
        
        # Get product views data for previous year
        $specials = $this -> insightmodel -> get_specials();
        
        if ($specials) {
            # Set title for the map
            $startDate  =  date('d M,Y', strtotime(date('Y-m-d')." -5 month"));
            $endDate    =  date('d M,Y', strtotime(date('Y-m-d')));
            $duration   = "Specials: ".$startDate." - ".$endDate;
            
            # Get last 6 months from the current date , including current month 
            for( $monthIndex=0; $monthIndex < 6; $monthIndex++)
            {
                $monthNumber =  date('m', strtotime(date('Y-m')." -$monthIndex month")); 
                $monthName   =  date('M', strtotime(date('Y-m')." -$monthIndex month"));
                $yearNumber  =  date('Y', strtotime(date('Y-m')." -$monthIndex month")); 

                $allMonths[$monthIndex]['monthNumber'] = $monthNumber; 
                $allMonths[$monthIndex]['yearNumber'] = $yearNumber; 
                $allMonths[$monthIndex]['monthName'] = $monthName;
                //$allMonths[$monthIndex]['monthYear'] = $monthName."-".$yearNumber;
                $allMonths[$monthIndex]['monthYear'] = $monthName;
            }
            
            # Make the reveserse array as we need from last 11th to current month 
            $allMonths = array_reverse($allMonths);

            # Compare with view data is available for the month then set it otherwise make it o for the month 
            foreach($allMonths as $allMonthsKey=>$allMonthsValue)
            {
                $found = false;
                foreach($specials as $specialKey=>$specialValue)
                {
                    if($allMonthsValue['monthNumber'] == $specialValue['month_number'] && $allMonthsValue['yearNumber'] == $specialValue['year'])
                    {
                        $found = true;
                        break; 
                    }
                }
                //$allMonths[$allMonthsKey]['monthYear'] = $allMonths[$allMonthsKey]['monthName']."-".date('y',strtotime($allMonths[$allMonthsKey]['yearNumber']));
                
                $allMonths[$allMonthsKey]['specials_count'] = $found == true ? $specialValue['specials_count'] : 0;
            }
           
            $this -> result = 1;
            $this -> message = $allMonths;
            $this -> duration = $duration;
        }
        else {
            $this -> result = FALSE;
            $this -> message = 'No records found';
            $this -> duration = '';
        }
        echo json_encode(
            array(
                'result' => $this -> result,
                'message' => $this -> message,
                'duration' => $this -> duration
            )
        );
    }
    
    
    
    /*
     * Function Name : get_specials
     * Purpose       : Get specials count for last 6 months..
     * Parameters    : No Parameters
     */
    
    public function get_visitors() {
        # Set default values 
        $visitorsData = array();
        $totalUsers = 0;
        $visitString = '';
        
        # Get visitors Data
        $visitors = $this -> insightmodel -> get_visitors();
        
        foreach($visitors as $visitor)
        {
           $totalUsers = $totalUsers + $visitor['users_count'];
           if($visitString)
           {
               $visitString = $visitString .",".$visitor['users_count'];
           }else{
               $visitString = $visitor['users_count'];
           }
           
        }
        
        if ($visitors) {
            $this -> result = 1;
            $this -> message = $visitors;
            $this -> totalUsers = $totalUsers;
            $this -> visitString = $visitString;
        }
        else {
            $this -> result = FALSE;
            $this -> message = 'No records found';
            $this -> totalUsers = 0;
            $this -> visitString = '';
        }
        
        echo json_encode(
            array(
                'result' => $this -> result,
                'message' => $this -> message,
                'totalUsers' => $this -> totalUsers,
                'visitString' => $this -> visitString
            )
        );
         
         
    }
    
     /*
     * Function Name : get_product_view_details
     * Purpose       : Get product view count with each category subcategory and subsubcategory
     * Parameters    : No Parameters
     */
    
    public function get_product_view_details() {
        #set Default values 
        $totalViews =  $index = 0;
        $categories = array();
        
        # Get post Values 
        $monthNameValue = sanitize($this -> input -> post('monthName'));
        $monthlyViewsCount = sanitize($this -> input -> post('monthlyViewsCount'));
        
        # Get Month and Year information 
        $monthData = explode('-',$monthNameValue);
        
        # Get start and end date of the month
        $nmonth = date("m", strtotime($monthNameValue));
        $startdate = $monthData[1]."-".$nmonth.'-01 00:00:00';
        
        $d = new DateTime( $startdate ); 
        $endDate = $d->format( 'Y-m-t' )." 23:59:59";
                
        # Get product views data for selected month Year
        $view_details = $this -> insightmodel -> get_product_view_details($startdate, $endDate);
        
        if ($view_details) {
            
            foreach($view_details as $singleRow)
            {
                $totalViews = $totalViews + $singleRow['views'];
            }
            
            # Show only required data and do total view calculation
            foreach($view_details as $singleRow)
            {
                if($singleRow['SubSubCategoryName'])
                {
                    $categories[$index]['CategoryName'] =  $singleRow['MainCategoryName']." - ".$singleRow['SubCategoryName']." - ".$singleRow['SubSubCategoryName']; 
                }else{
                   $categories[$index]['CategoryName'] =  $singleRow['MainCategoryName']." - ".$singleRow['SubCategoryName'];  
                }
                
                $categories[$index]['views']        =  $singleRow['views'];
                $categories[$index]['viewsPercentage'] =  round(( $singleRow['views'] * 100) / $totalViews);
                
                $index++;
            }
            
            $this -> result = 1;
            $this -> message = $categories;
            $this -> totalViews = $totalViews;
            $this -> monthName = $monthNameValue;
            
        }
        else {
            $this -> result = FALSE;
            $this -> message = 'No records found';
            $this -> totalViews = 0;
            $this -> monthName = $monthNameValue;
        }
        
        echo json_encode(
            array(
                'result' => $this -> result,
                'message' => $this -> message,
                'totalViews' => $this -> totalViews,
                'monthName' => $this -> monthName
            )
        );
        
        
    }
    
    
    
}