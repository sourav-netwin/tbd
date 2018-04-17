<?php

/*
 * Author:PM
 * Purpose:Store Controller
 * Date:04-09-2015
 * Dependency: storemodel.php
 */
//error_reporting(E_ALL);
//ini_set('display_errors', 1);

class Stores extends My_Controller {

    private $result;
    private $message;

    function __construct() {
        parent::__construct();

        $this -> load -> model('admin/storemodel', '', TRUE);
        $this -> load -> model('admin/retailermodel', '', TRUE);
        $this -> load -> model('admin/statemodel', '', TRUE);
        $this -> load -> model('admin/storeformatmodel', '', TRUE);
        $this -> load -> model('admin/storegroupmodel', '', TRUE);

        $this -> page_title = "Stores";
        $this -> breadcrumbs[] = array('label' => 'Stores', 'url' => '/stores');
    }

    public function index($retailer_id = 0, $store_format_id = 0) {
        $data['title'] = $this -> page_title;

        $this -> breadcrumbs[0] = array('label' => 'Stores Management', 'url' => '');
        $this -> breadcrumbs[1] = array('label' => 'Stores', 'url' => '/stores');
        $data['breadcrumbs'] = $this -> breadcrumbs;

        //Add filters according to the login user.
        //Admin Users
        if ($this -> session -> userdata('user_level') < 3)
            $data['retailers'] = $this -> retailermodel -> get_retailers();

        //Retailers Users
        if ($this -> session -> userdata('user_type') == 3)
            $data['store_formats'] = $this -> storeformatmodel -> get_store_formats($this -> session -> userdata('user_retailer_id'));
        if ($this -> session -> userdata('user_type') == 1)
            $data['store_formats'] = $this -> storeformatmodel -> get_store_formats();

        if ($retailer_id != 0)
            $data['store_formats'] = $this -> storeformatmodel -> get_store_formats($retailer_id);

        $data['store_format_id'] = $store_format_id;
        $data['retailer_id'] = $retailer_id;
        $data['states'] = $this -> statemodel -> get_states();
        
        $this -> template -> view('admin/stores/index', $data);
    }

    public function datatable($retailer_id = 0, $store_format_id = 0, $region = 0) {

        $subquery1 ='( 
                    SELECT count(storetimings.Id) as timing_count 
                    FROM storetimings                       
                    WHERE `storetimings`.`StoreId` = stores.Id                           
                ) as timing_count'; 
            
            $this->datatables->select($subquery1);
            
        $this -> datatables -> select("stores.StoreName,retailers.CompanyName as CompanyName, storestypes.StoreType as StoreType,stores.Id as s_id, stores.UserCount as UserCount, CONCAT_WS( ', ', stores.StreetAddress,stores.City,state.Name ) as Address,state.Name as state_name,stores.IsActive as active, stores.Latitude, stores.Longitude", FALSE)
            -> unset_column('s_id')
            -> unset_column('active')
            -> from('stores')
            -> join('retailers', 'retailers.Id = stores.RetailerId')
            -> join('storestypes', 'storestypes.Id = stores.StoreTypeId AND storestypes.IsActive = 1 AND storestypes.IsRemoved=0', 'left')
            -> join('state', 'state.Id = stores.StateId')
            -> add_column('Users', get_user_count('$1', '$2', '$3'), 'UserCount,stores,s_id')
            -> add_column('Actions', get_action_buttons('$1', 'stores'), 's_id');

        $array_where = array('stores.IsRemoved' => '0');

        //Retailers Users
        if ($this -> session -> userdata('user_type') == 3) {
            $array_where['retailers.Id'] = $this -> session -> userdata('user_retailer_id');
        }

        //StoreFormat Users
        if ($this -> session -> userdata('user_type') == 5) {
            $array_where['stores.StoreTypeId'] = $this -> session -> userdata('user_store_format_id');
        }

        if ($retailer_id > 0) {
            $array_where['stores.RetailerId'] = $retailer_id;
        }

        if ($store_format_id > 0) {
            $array_where['stores.StoreTypeId'] = $store_format_id;
        }
        
        if($region > 0){
            $array_where['state.Id'] = $region;
        }

        $this -> datatables -> where($array_where);

        echo $this -> datatables -> generate();
    }

    public function add() {

        if ($this -> input -> server('REQUEST_METHOD') == 'POST') {
            //Retailers Users
            $retailer_id = ( $this -> session -> userdata('user_type') != 3 ) ? $this -> input -> post('retailers') : $this -> session -> userdata('user_retailer_id');

            //StoreFormat Users
            $store_format_id = ( $this -> session -> userdata('user_type') != 5 ) ? $this -> input -> post('store_format') : $this -> session -> userdata('user_store_format_id');

            if ($this -> session -> userdata('user_type') == 5) {
                $retailer_id = $this -> session -> userdata('user_retailer_id');
            }

            //Add Retailer
            $this -> form_validation -> set_rules('store_name', 'store name', 'trim|required|xss_clean');
            if ($this -> session -> userdata('user_level') <= 3)
                $this -> form_validation -> set_rules('store_format', 'store format', 'trim|required|xss_clean');
            if ($this -> session -> userdata('user_level') < 3)
                $this -> form_validation -> set_rules('retailers', 'retailers', 'trim|required|xss_clean');
            $this -> form_validation -> set_rules('building', 'building name', 'trim|validate_streetaddress|xss_clean');
            $this -> form_validation -> set_rules('street_address', 'street address', 'trim|required|validate_streetaddress|xss_clean');
            $this -> form_validation -> set_rules('zip', 'zip code', 'trim|numeric|xss_clean');
            $this -> form_validation -> set_rules('city', 'city', 'trim|required|validate_city|xss_clean');
            $this -> form_validation -> set_rules('state', 'state', 'trim|required|xss_clean');
            

            if (!$this -> form_validation -> run() == FALSE) {

                $insert_data = array(
                    'RetailerId' => $retailer_id,
                    'StoreId' => $this -> input -> post('store_id'),
                    'StoreName' => $this -> input -> post('store_name'),
                    'StoreTypeId' => $store_format_id,
                    'StreetAddress' => $this -> input -> post('street_address'),
                    'Building' => $this -> input -> post('building') ? $this -> input -> post('building') : NULL,
                    'Zip' => $this -> input -> post('zip'),
                    'City' => $this -> input -> post('city'),
                    'StateId' => $this -> input -> post('state'),
                    'CountryId' => 1,
                    'Latitude' => $this -> input -> post('latitude'),
                    'Longitude' => $this -> input -> post('longitude'),
                    'ContactPerson' => $this -> input -> post('store_contact_person'),
                    'ContactPersonNumber' => $this -> input -> post('store_contact_tel'),
                    'CreatedBy' => $this -> session -> userdata('user_id'),
                    'CreatedOn' => date('Y-m-d H:i:s'),
                    'IsActive' => 1,
                    'IsNew' => 1
                );

                $result = $this -> storemodel -> add_store($insert_data);
                if ($result > 0) {
                    $this -> send_mail_special_present_in_region($this -> session -> userdata('user_retailer_id'), $this -> input -> post('state'), $this -> input -> post('store_name'));
                    $insert_data = array(
                        'Opendays' => $this -> input -> post('open_days'),
                        'OpenHours' => $this -> input -> post('open_hours')
                        );

                    //Save Store Timimg
                    $this -> storemodel -> save_store_timing($result, $insert_data);

                    # Set the store groups 
                    $groupIds = $this->input->post('groupId');
                    if(count($groupIds)> 0 )
                    {
                        $newStoreId = $result;
                        $result_setGroups = $this->storemodel->set_storeGroups($newStoreId, $groupIds); 
                    }
                    
                    $this -> session -> set_userdata('success_message', "Store added successfully");
                }
                else
                    $this -> session -> set_userdata('success_message', "Error while adding store");
                redirect('stores', 'refresh');
            }
        }


        $data['title'] = $this -> page_title;

        $this -> breadcrumbs[0] = array('label' => 'Stores Management', 'url' => '');
        $this -> breadcrumbs[1] = array('label' => 'Stores', 'url' => '/stores');
        $this -> breadcrumbs[2] = array('label' => 'Add Store', 'url' => 'stores/add');
        $data['breadcrumbs'] = $this -> breadcrumbs;

        $data['retailers'] = $this -> retailermodel -> get_retailers();

        $data['states'] = $this -> statemodel -> get_states();
        
        $data['store_groups'] = $this -> storegroupmodel -> get_store_groups();

        if ($this -> session -> userdata('user_type') == 3)
            $data['store_formats'] = $this -> storeformatmodel -> get_store_formats($this -> session -> userdata('user_retailer_id'));

        $this -> template -> view('admin/stores/add', $data);
    }

    public function edit($id) {
        $data = $this -> storemodel -> get_store_details($id);

        $data['store_timings'] = $this -> storemodel -> get_store_timing($id);
        $data['store_promos'] = $this -> storemodel -> get_store_promos($id);

        $this -> breadcrumbs[] = array('label' => 'Edit Store', 'url' => 'stores/edit/' . $id);

        $data['title'] = $this -> page_title;

        $data['breadcrumbs'] = $this -> breadcrumbs;

        $data['retailers'] = $this -> retailermodel -> get_retailers();

        $data['store_formats'] = $this -> storeformatmodel -> get_store_formats($data['RetailerId']);

        $data['states'] = $this -> statemodel -> get_states();
        
        $data['store_groups'] = $this -> storegroupmodel -> get_store_groups();
        $data['retailers_storegroups'] = $this -> storemodel -> get_stores_storegroups($id);
        
        $this -> load -> view('admin/stores/edit', $data);
    }

    public function edit_post($id) {
        if ($this -> input -> server('REQUEST_METHOD') == 'POST') {
            
            //Retailers Users
            $retailer_id = ( $this -> session -> userdata('user_type') != 3 ) ? $this -> input -> post('retailers') : $this -> session -> userdata('user_retailer_id');

            //StoreFormat Users
            $store_format_id = ( $this -> session -> userdata('user_type') != 5 ) ? $this -> input -> post('store_format') : $this -> session -> userdata('user_store_format_id');

            if ($this -> session -> userdata('user_type') == 5) {
                $retailer_id = $this -> session -> userdata('user_retailer_id');
            }

            $this -> form_validation -> set_rules('store_name', 'store name', 'trim|required|xss_clean');
            //Edit Retailer
            if ($this -> session -> userdata('user_level') <= 3)
                $this -> form_validation -> set_rules('store_format', 'store format', 'trim|required|xss_clean');
            if ($this -> session -> userdata('user_level') < 3)
                $this -> form_validation -> set_rules('retailers', 'retailers', 'trim|required|xss_clean');
            $this -> form_validation -> set_rules('building', 'building name', 'trim|xss_clean');
            $this -> form_validation -> set_rules('street_address', 'street address', 'trim|required|xss_clean');
            $this -> form_validation -> set_rules('zip', 'zip code', 'trim|numeric|xss_clean');
            $this -> form_validation -> set_rules('city', 'city', 'trim|required|xss_clean');
            $this -> form_validation -> set_rules('state', 'state', 'trim|required|xss_clean');
            
            $promo_premium = $this -> input -> post('promo_premium') === '1' ? $this -> input -> post('promo_premium') : '0';
            $promo_concierge = $this -> input -> post('promo_concierge') === '1' ? $this -> input -> post('promo_concierge') : '0';
            $promo_messenger = $this -> input -> post('promo_messenger') === '1' ? $this -> input -> post('promo_messenger') : '0';
            $promo_admanager = $this -> input -> post('promo_admanager') === '1' ? $this -> input -> post('promo_admanager') : '0';


            if (!$this -> form_validation -> run() == FALSE) {
                $edit_data = array(
                    'RetailerId' => $retailer_id,
                    'StoreId' => $this -> input -> post('store_id'),
                    'StoreName' => $this -> input -> post('store_name'),
                    'StoreTypeId' => $store_format_id,
                    'StreetAddress' => $this -> input -> post('street_address'),
                    'Building' => $this -> input -> post('building') ? $this -> input -> post('building') : NULL,
                    'Zip' => $this -> input -> post('zip'),
                    'City' => $this -> input -> post('city'),
                    'StateId' => $this -> input -> post('state'),
                    'CountryId' => 1,
                    'Latitude' => $this -> input -> post('latitude'),
                    'Longitude' => $this -> input -> post('longitude'),
                    'ContactPerson' => $this -> input -> post('store_contact_person'),
                    'ContactPersonNumber' => $this -> input -> post('store_contact_tel'),
                    'ModifiedBy' => $this -> session -> userdata('user_id'),
                    'ModifiedOn' => date('Y-m-d H:i:s'),
                    'IsActive' => 1
                );

                $result = $this -> storemodel -> update_store($id, $edit_data);

                $insert_data = array(
                    'Opendays' => $this -> input -> post('open_days'),
                    'OpenHours' => $this -> input -> post('open_hours')
                );
                
                $promo_array = array(
                    'RetailerId' => 0,
                    'StoreId' => $id,
                    'Standard' => '1',
                    'Premium' => $promo_premium,
                    'Concierge' => $promo_concierge,
                    'Messenger' => $promo_messenger,
                    'AdManager' => $promo_admanager
                );
                
                $this -> storemodel -> insert_update_promo($promo_array, $id);

                //Save Store Timimg
                $this -> storemodel -> save_store_timing($id, $insert_data);

                # Set the store groups 
                $groupIds = $this->input->post('groupId');
                if(count($groupIds)> 0 )
                {
                    $result_setGroups = $this->storemodel->set_storeGroups($id, $groupIds); 
                }
                    
                //$this -> session -> set_userdata('success_message', "Store updated successfully");
                $this -> result = 1;
                $this -> message = 'Store Updated Successfully';
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

    public function delete($id) {

        $this -> storemodel -> delete_store($id);
        $this -> session -> set_userdata('success_message', "Store deleted successfully");
        redirect('stores', 'refresh');
    }

    public function change_status($id, $status) {

        $this -> storemodel -> change_status($id, $status);
        $this -> session -> set_userdata('success_message', "Store status updated successfully");
        redirect('stores', 'refresh');
    }

    function get_latitude_longitude($address = '', $is_json = 1) {
        $address = ( $this -> input -> post('address') != '' ) ? $this -> input -> post('address') : $address;
        // Get lat and long by address
        $prepAddr = str_replace(' ', '+', $address);
        $geocode = $this -> file_get_contents_curl('http://maps.google.com/maps/api/geocode/json?address=' . $prepAddr . '&sensor=false');
        $output = json_decode($geocode);
        if ($output -> status == 'ZERO_RESULTS') {
            $latitude = $longitude = 0;
        }
        else {
            $latitude = $output -> results[0] -> geometry -> location -> lat;
            $longitude = $output -> results[0] -> geometry -> location -> lng;
        }

        if ($is_json)
            echo json_encode(array('latitude' => $latitude, 'longitude' => $longitude));
        else
            return(array('latitude' => $latitude, 'longitude' => $longitude));
    }

    public function import() {
        if ($this -> input -> server('REQUEST_METHOD') == 'POST') {

            //Retailers Users
            $retailer_id = ( $this -> session -> userdata('user_type') != 3 ) ? $this -> input -> post('retailers') : $this -> session -> userdata('user_retailer_id');

            //StoreFormat Users
            $store_format_id = ( $this -> session -> userdata('user_type') != 5 ) ? $this -> input -> post('store_format') : $this -> session -> userdata('user_store_format_id');

            if ($this -> session -> userdata('user_type') == 5) {
                $retailer_id = $this -> session -> userdata('user_retailer_id');
            }

            if (!empty($_FILES['import_file']['name']) && $retailer_id != '') {
                $result = $this -> do_upload_file('import_file');
                if (!isset($result['error'])) {
                    //load the excel library
                    $this -> load -> library('excel');

                    $file_path = IMPORT_FILE_PATH . $result['upload_data']['file_name'];
                    //read file from path
                    $objPHPExcel = PHPExcel_IOFactory::load($file_path);

                    $wrong_stores = array();
                    foreach ($objPHPExcel -> getWorksheetIterator() as $worksheet) {
                        $worksheetTitle = $worksheet -> getTitle();
                        $highestRow = $worksheet -> getHighestRow(); // e.g. 10
                        $highestColumn = $worksheet -> getHighestColumn(); // e.g 'F'
                        $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
                        $nrColumns = ord($highestColumn) - 64;

                        if ($nrColumns > 1 && $highestRow > 1) {
                            $array_states = array();
                            for ($row = 2; $row <= $highestRow; ++$row) {
                                array_push($array_states, trim($worksheet -> getCell("E$row") -> getValue()));
                            }


                            $state_check_result = $this -> statemodel -> validate_state(array_unique($array_states));

                            if (count(array_unique($array_states)) != count($state_check_result)) {
                                $this -> session -> set_userdata("error_message", "Please enter a valid state in the import file");
                                redirect('stores', 'refresh');
                            }
                            else {
                                $res_arr = array();
                                for ($row = 2; $row <= $highestRow; ++$row) {
                                    //Calculate latitude and longitude from address
                                    $state_data = $this -> statemodel -> get_state_details($worksheet -> getCell("E$row") -> getValue());
                                    $state = ( $state_data !== FALSE ) ? $state_data[0]['Name'] : '';

                                    $address = $worksheet -> getCell("B$row") -> getValue() . " " . $worksheet -> getCell("D$row") -> getValue() . " " . $worksheet -> getCell("F$row") -> getValue() . " " . $worksheet -> getCell("E$row") -> getValue() . " South Africa " . $worksheet -> getCell("G$row") -> getValue();

                                    $coordinates_data = ($this -> get_latitude_longitude($address, '0'));

                                    if ($coordinates_data['latitude'] == 0 && $coordinates_data['longitude'] == 0) {
                                        array_push($wrong_stores, $worksheet -> getCell("B$row") -> getValue());
                                    }
                                    $insert_data = array(
                                        'RetailerId' => $retailer_id,
                                        'StoreTypeId' => $store_format_id,
                                        'StoreId' => $worksheet -> getCell("A$row") -> getValue(),
                                        'StoreName' => $worksheet -> getCell("B$row") -> getValue(),
                                        'Building' => $worksheet -> getCell("C$row") -> getValue(),
                                        'StreetAddress' => $worksheet -> getCell("D$row") -> getValue(),
                                        'Zip' => $worksheet -> getCell("G$row") -> getValue(),
                                        'City' => $worksheet -> getCell("F$row") -> getValue(),
                                        'StateId' => $state_check_result[$worksheet -> getCell("E$row") -> getValue()],
                                        'CountryId' => 1,
                                        'ContactPerson' => $worksheet -> getCell("H$row") -> getValue(),
                                        'ContactPersonNumber' => $worksheet -> getCell("I$row") -> getValue(),
                                        'Latitude' => $coordinates_data['latitude'],
                                        'Longitude' => $coordinates_data['longitude'],
                                        'CreatedBy' => $this -> session -> userdata('user_id'),
                                        'CreatedOn' => date('Y-m-d H:i:s'),
                                        'IsActive' => 1
                                    );

                                    $result = $this -> storemodel -> add_store($insert_data);
                                    array_push($res_arr, $result);

                                    //Store the timing for a store
                                    $open_days = array();
                                    $open_hours = array();
                                    $i = 1;
                                    foreach (range('J', $highestColumn) as $column_key) {

                                        if ($worksheet -> getCell($column_key . $row) -> getValue()) {
                                            $open_days[] = $i;
                                        }
                                        $open_hours[] = $worksheet -> getCell($column_key . $row) -> getValue();
                                        $i++;
                                    }

                                    $insert_data = array('Opendays' => $open_days,
                                        'OpenHours' => $open_hours);

                                    //Save Store Timimg
                                    $this -> storemodel -> save_store_timing($result, $insert_data);
                                }
                                if (!empty($res_arr) && !in_array(0, $res_arr)) {
                                    $message = "Stores imported successfully." . ((!empty($wrong_stores) ) ? "'" . implode(',', $wrong_stores) . "'" . ' are imported with no latitude & longitude' : '' );
                                    $this -> session -> set_userdata('success_message', $message);
                                }
                                else {
                                    $message = "Error while importing store(s)." . ((!empty($wrong_stores) ) ? "'" . implode(',', $wrong_stores) . "'" . ' are imported with no latitude & longitude' : '' );
                                    $this -> session -> set_userdata('success_message', $message);
                                }
                                redirect('stores', 'refresh');
                            }
                        }
                    }
                }
                else {
                    // code to display error while image upload
                    $this -> session -> set_userdata('error_message', $result['error']);
                    redirect('stores', 'refresh');
                }
            }
        }
    }

    public function add_store_user($store_id = '') {
        if ($this -> input -> server('REQUEST_METHOD') == 'POST') {

            //Add Retailer
            $this -> form_validation -> set_rules('email', 'email', 'trim|required|callback_check_uniqueness_email');
            $this -> form_validation -> set_rules('first_name', 'first_name', 'trim|required|max_length[50]|xss_clean');
            $this -> form_validation -> set_rules('last_name', 'last_name', 'trim|required|max_length[50]|xss_clean');
            $this -> form_validation -> set_rules('password', 'password', 'trim|required|matches[confirm_password]');

            if (!$this -> form_validation -> run() == FALSE) {

                $this -> load -> model('admin/usermodel', '', TRUE);
                //Add Retailer User Initially
                $role = $this -> usermodel -> get_user_role('Store User');

                $user_data = array(
                    'FirstName' => $this -> input -> post('first_name'),
                    'LastName' => $this -> input -> post('last_name'),
                    'Email' => $this -> input -> post('email'),
                    'Password' => MD5($this -> input -> post('password')),
                    'UserRole' => $role['Id'],
                    'TelephoneFixed' => $this -> input -> post('contact_tel'),
                );

                $user_id = $this -> usermodel -> add_user($user_data);

                //Save the user to the store admin.

                $store_data = array(
                    'UserId' => $user_id,
                    'StoreId' => $this -> input -> post('store'),
                );

                $this -> load -> model('admin/storeadminmodel', '', TRUE);
                $result = $this -> storeadminmodel -> add_admin($store_data);


                $this -> storemodel -> update_user_count($this -> input -> post('store'));


                //Get store name
                $store_detail_data = $this -> storemodel -> get_store_details($this -> input -> post('store'));
                $store_name = $store_detail_data['StoreName'];

                // Send email notification to admin.
                $this -> load -> model('admin/emailtemplatemodel');

                $email_template_details = $this -> emailtemplatemodel -> get_email_template_details(5);

                $emailBody = $email_template_details['Content'];
                $emailBody = str_replace("{USER_TYPE}", "Store User", $emailBody);
                $emailBody = str_replace("{COMPANY}", $store_name, $emailBody);


                //---- LOAD EMAIL LIBRARY ----//
                $this -> load -> library('email');
                $config['mailtype'] = 'html';

                $this -> email -> initialize($config);
                $this -> email -> from($email_template_details['FromEmail']);
                $this -> email -> to($email_template_details['ToEmail']);
                $this -> email -> subject("The Best Deals: Store User added");
                $this -> email -> message($emailBody);
                $this -> email -> send();

                //Add default wizard status
                $this -> storemodel -> add_store_wizard($store_data);

                if ($result > 0)
                    $this -> session -> set_userdata('success_message', "Store User added successfully");
                else
                    $this -> session -> set_userdata('success_message', "Error while adding Store User");

                redirect('stores', 'refresh');
                exit(0);
            }
        }

        if ($this -> session -> userdata('user_type') != 3) {
            $this -> breadcrumbs[] = array('label' => 'Retailers', 'url' => '/retailers');
        }

//        $this->breadcrumbs[] = array('label' => 'Store Formats', 'url' => '/storeformat');
//
//        $this->breadcrumbs[] = array('label' => 'Add User', 'url' => '#');

        $data['store_id'] = $store_id;
        $data['title'] = $this -> page_title;

        $this -> breadcrumbs[0] = array('label' => 'Stores Management', 'url' => '');
        $this -> breadcrumbs[1] = array('label' => 'Stores', 'url' => '/stores');
        $this -> breadcrumbs[2] = array('label' => 'Add User', 'url' => 'stores/add_store_user');
        $data['breadcrumbs'] = $this -> breadcrumbs;

        //Retailers Users
        $retailer_id = ( $this -> session -> userdata('user_type') < 3 ) ? 0 : $this -> session -> userdata('user_retailer_id');

        //StoreFormat Users
        $store_format_id = ( $this -> session -> userdata('user_type') < 5 ) ? 0 : $this -> session -> userdata('user_store_format_id');


        $data['stores'] = $this -> storemodel -> get_stores($retailer_id, $store_format_id);

        $this -> template -> view('admin/stores/add_user', $data);
    }

    public function get_store_formats($id) {

        $store_formats = $this -> storeformatmodel -> get_store_formats($id);

        $store_format_result = "";

        $store_format_result .= '<option value="">Select Store Format</option>';
        foreach ($store_formats as $store_formats) {
            $store_format_result .= '<option value="' . $store_formats['Id'] . '">' . $store_formats['StoreType'] . '</option>';
        }

        echo json_encode(array('store_formats' => $store_format_result));
    }

    function validate_name($name) {
        $this -> form_validation -> set_message('validate_name', 'Name must contain contain only letters, apostrophe, spaces or dashes.');
        if (preg_match('/^[a-zA-Z\'\-\s]+$/', $name)) {
            return true;
        }
        else {
            return false;
        }
    }

    function validate_city($city) {
        $this -> form_validation -> set_message('validate_city', 'City must contain only letters and spaces.');
        if (preg_match('/^[a-zA-Z\s]+$/', $city)) {
            return true;
        }
        else {
            return false;
        }
    }

    function validate_streetaddress($street_address) {
        $this -> form_validation -> set_message('validate_streetaddress', 'Street Address must not contain special characters.');
        if (preg_match('/^[a-zA-Z0-9\'",\\\/\-\s]+$/', $street_address)) {
            return true;
        }
        else {
            return false;
        }
    }

    function file_get_contents_curl($url) {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);

        $data = curl_exec($ch);
        curl_close($ch);

        return $data;
    }

    public function validate_store($id) {

        if ($this -> input -> server('REQUEST_METHOD') == 'POST') {

            //Retailers Users
            $retailer_id = ( $this -> session -> userdata('user_type') != 3 ) ? $this -> input -> post('retailers') : $this -> session -> userdata('user_retailer_id');

            //StoreFormat Users
            $store_format_id = ( $this -> session -> userdata('user_type') != 5 ) ? $this -> input -> post('store_format') : $this -> session -> userdata('user_store_format_id');

            if ($this -> session -> userdata('user_type') == 5) {
                $retailer_id = $this -> session -> userdata('user_retailer_id');
            }


            //Edit Retailer

            $this -> form_validation -> set_rules('street_address', 'street address', 'trim|required|xss_clean');
            $this -> form_validation -> set_rules('zip', 'zip code', 'trim|required|numeric|xss_clean');
            $this -> form_validation -> set_rules('city', 'city', 'trim|required|xss_clean');
            $this -> form_validation -> set_rules('state', 'state', 'trim|required|xss_clean');


            if (!$this -> form_validation -> run() == FALSE) {
                $edit_data = array(
                    'StoreId' => $this -> input -> post('store_id'),
                    'StoreName' => $this -> input -> post('store_name'),
                    'StreetAddress' => $this -> input -> post('street_address'),
                    'Zip' => $this -> input -> post('zip'),
                    'City' => $this -> input -> post('city'),
                    'StateId' => $this -> input -> post('state'),
                    'CountryId' => 1,
                    'Latitude' => $this -> input -> post('latitude'),
                    'Longitude' => $this -> input -> post('longitude'),
                    'ContactPerson' => $this -> input -> post('store_contact_person'),
                    'ContactPersonNumber' => $this -> input -> post('store_contact_tel'),
                    'ModifiedBy' => $this -> session -> userdata('user_id'),
                    'ModifiedOn' => date('Y-m-d H:i:s'),
                    'IsActive' => 1);

                $result = $this -> storemodel -> update_store($id, $edit_data);

                $insert_data = array('Opendays' => $this -> input -> post('open_days'),
                    'OpenHours' => $this -> input -> post('open_hours'));

                //Save Store Timimg
                $this -> storemodel -> save_store_timing($id, $insert_data);

                $this -> session -> set_userdata('success_message', "Store updated successfully");
                redirect('stores', 'refresh');
            }
        }

        $data = $this -> storemodel -> get_store_details($id);

        $data['store_timings'] = $this -> storemodel -> get_store_timing($id);

        $data['title'] = 'Store details';

        $data['breadcrumbs'] = $this -> breadcrumbs;


        $data['states'] = $this -> statemodel -> get_states();

        $this -> template -> view('admin/stores/validate_store', $data);
    }

    /**
     * Function to send email to the retailer if the store region already have any specials added
     * 
     * @param int $retailer_id
     * @param int $state_id
     * @param string $store_name
     */
    public function send_mail_special_present_in_region($retailer_id, $state_id, $store_name) {
        $special_region_details = $this -> storemodel -> get_retailer_email_id_if_special($retailer_id, $state_id);
        if ($special_region_details) {
            $this -> load -> library('email');
            $config['mailtype'] = 'html';
            $email = $special_region_details['Email'];

            $mymessage = "<!DOCTYPE HTML PUBLIC =22-//W3C//DTD HTML 4.01 Transitional//EN=22 =22http://www.w3.org/TR/html4/loose.dtd=22>";
            $mymessage .= "<html><head><meta http-equiv=\"content-type\" content=\"text/html; charset=ISO-8859-1\" /></head><body>";
            $mymessage .= "<p>Dear Retailer,</p><br /><p>A new store named $store_name added and it is found that specials already added under the store region. Please add specials to this store too.</p><br /><br /><p>Regards</p><br /><p>The Best Deals</p>";
            $mymessage .= "</body></html>";


            $this -> email -> initialize($config);
            $this -> email -> from('admin@thebestdeals.co.za', 'Adminidtrator');
            $this -> email -> to($email);
            //$this -> email -> to('genknooztester1@gmail.com');
            $this -> email -> reply_to('admin@thebestdeals.co.za');
            $this -> email -> subject('Add specials in newly added store - ' . $store_name);
            $this -> email -> message($mymessage);
            $this -> email -> send();
        }
    }

    /**
     * Function to add the general product catalogue to the newly added stores. The authority will be the retailer
     */
    public function add_catalogue_to_new_stores() {
        set_time_limit(0);
        ini_set('memory_limit', '512M');
        if ($this -> input -> server('REQUEST_METHOD') == 'POST' && $this -> session -> userdata('user_type') == 3) {
            $retailer_id = $this -> session -> userdata('user_retailer_id');
            $new_stores = $this -> storemodel -> get_retailer_new_stores($retailer_id);
            $added_count = 0;
            if ($new_stores) {
                $store_ids = [];
                foreach ($new_stores as $store) {
                    $store_ids[] = $store['Id'];
                    $added_products = '';
                    $already_added_products = $this -> storemodel -> get_already_added_products_by_store($retailer_id, $store['Id'], $store['StoreTypeId']);
                    if ($already_added_products) {
                        foreach ($already_added_products as $product) {
                            $added_products[] = $product['ProductId'];
                        }
                    }
                    $products_to_add = $this -> storemodel -> get_products_to_add_store($added_products);
                    if ($products_to_add) {
                        $product_add_array = [];
                        foreach ($products_to_add as $products) {
                            $product_add_array[] = array(
                                'ProductId' => $products['Id'],
                                'RetailerId' => $store['RetailerId'],
                                'StoreId' => $store['Id'],
                                'StoreTypeId' => $store['StoreTypeId'],
                                'PriceForAllStores' => 0,
                                'Price' => $products['RRP'],
                                'CreatedBy' => $this -> session -> userdata('user_id'),
                                'CreatedOn' => date('Y-m-d H:i:s'),
                                'IsNew' => 1,
                                'IsActive' => 1
                            );
                        }
                        if (!empty($product_add_array)) {
                            $is_added = $this -> storemodel -> add_product_batch($product_add_array);
                            if ($is_added) {
                                $added_count++;
                            }
                        }
                    }
                    $this -> storemodel -> update_store($store['Id'], array('IsNew' => 0));
                }
                if ($added_count > 0) {
                    $this -> result = 1;
                    $this -> message = 'Completed adding catalogue to the new stores. Now start adding specials';
                }
                else {
                    $this -> result = 0;
                    $this -> message = 'No products added to the catalogue';
                }
            }
            else {
                $this -> result = 0;
                $this -> message = 'No new stores available';
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
    
    /* Get stores based on StoreTypeId  */
    public function get_stores($storeTypeId) {

        $stores = $this -> storemodel -> get_stores(0,$storeTypeId);

        $store_result = "";

        $store_result .= '<option value="">Select Store</option>';
        foreach ($stores as $store) {
            $store_result .= '<option value="' . $store['Id'] . '">' . $store['StoreName'] . '</option>';
        }

        echo json_encode(array('stores' => $store_result));
    }
    
}
