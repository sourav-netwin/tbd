<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| File and Directory Modes
|--------------------------------------------------------------------------
|
| These prefs are used when checking and setting modes when working
| with the file system.  The defaults are fine on servers with proper
| security, but you may wish (or even need) to change the values in
| certain environments (Apache running a separate process for each
| user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
| always be used to set the mode correctly.
|
*/
define('FILE_READ_MODE', 0644);
define('FILE_WRITE_MODE', 0666);
define('DIR_READ_MODE', 0755);
define('DIR_WRITE_MODE', 0777);

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/

define('FOPEN_READ',							'rb');
define('FOPEN_READ_WRITE',						'r+b');
define('FOPEN_WRITE_CREATE_DESTRUCTIVE',		'wb'); // truncates existing file data, use with care
define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE',	'w+b'); // truncates existing file data, use with care
define('FOPEN_WRITE_CREATE',					'ab');
define('FOPEN_READ_WRITE_CREATE',				'a+b');
define('FOPEN_WRITE_CREATE_STRICT',				'xb');
define('FOPEN_READ_WRITE_CREATE_STRICT',		'x+b');


// Define paths for images

define('API_TOKEN',"TBD_SYSTEM_CONFIGURATION"); // Max image upload size 2MB

//define('MAX_UPOAD_IMAGE_SIZE',4096); // Max image upload size 2MB
define('RESULT_SET_LIMIT',50); // Max limit number of records in a result set
define('USER_IMAGE_PATH','assets/images/users/');
define('DEFAULT_USER_IMAGE_PATH','assets/images/users/default.jpg');

define('RETAILER_IMAGE_PATH','assets/images/retailers/');
define('CATEGORY_IMAGE_PATH', 'assets/images/categories/');
define('PRODUCT_IMAGE_PATH', 'assets/images/products/');
define('SLIDER_IMAGE_PATH', 'assets/images/sliders/');
define('MOBILE_SLIDER_IMAGE_PATH', 'assets/images/mobile_sliders/');
define('STORE_FORMAT_IMAGE_PATH', 'assets/images/storeformats/');
define('SPECIAL_IMAGE_PATH', 'assets/images/specials/');
define('LOYALTY_PRODUCT_IMAGE_PATH', 'assets/images/loyaltyproducts/');
define('ADVERTISEMENT_IMAGE_PATH', 'assets/images/advertisements/');
define('DEFAULT_ADVERTISEMENT_IMAGE_PATH', 'assets/images/advertisements/default.jpg');
define('DEFAULT_ADVERTISEMENT_IMAGE_NAME', 'default.jpg');
define('CARDS_IMAGE_PATH', 'assets/images/cards/');


define('RETAILERS_WIDTH',255);
define('RETAILERS_HEIGHT',90);

define('CATEGORIES_WIDTH',100);
define('CATEGORIES_HEIGHT',100);

define('PRODUCTS_WIDTH',500);
define('PRODUCTS_HEIGHT',500);

define('USERS_WIDTH','');
define('USERS_HEIGHT','');

define('SLIDERS_WIDTH',1530);
define('SLIDERS_HEIGHT',649);

define('IMPORT_FILE_PATH', 'assets/admin/import_files/');
define('SAMPLE_IMPORT_FILE_PATH', 'assets/admin/sample_import_files/');

define('DEFAULT_PRODUCT_IMAGE_PATH', 'assets/images/products/default.jpg');
define('DEFAULT_PRODUCT_IMAGE_NAME', 'default.jpg');

define('DEFAULT_LOYALTY_PRODUCT_IMAGE_PATH', 'assets/images/loyaltyproducts/default.jpg');
define('DEFAULT_LOYALTY_PRODUCT_IMAGE_NAME', 'default.jpg');

define('DEFAULT_ADVERTISEMENT_IMAGE_PATH', 'assets/images/advertisements/default.jpg');
define('DEFAULT_ADVERTISEMENT_IMAGE_NAME', 'default.jpg');

//Webservice Setting
define('API_PAGE_LIMIT', 25);

define('PEM_CERTIFICATE',FCPATH.'/assets/settings/TBD_NW_Dev.pem');
define('PASSPHRASE','tbd123');

define('USER_ROLE', 4);
define('COUNTRY', 1);

// Id from database for terms and conditions
define('TERMS_CONDITIONS', 1);

// Id from database for terms and conditions
define('FAQ', 4);

// Forgot Password email template id
define('FORGOT_PASSWORD', 1);

//Location preference notification interval in hour
define('LOCATION_NOTIFICATION_DELAY', 1);//hour
//Near by store notification interval in minutes
define('NEARBY_NOTIFICATION_DELAY', 15);//mins
//Nearby store distance for notification in kilometers
define('NEARBY_NOTIFICATION_DISTANCE', 2);//KM

//maximum number of device token able to send in one FCM request
define('FCM_MAX_LIMIT', 1000);//1000 is the maximum allowed by FCM. Give numbers <= 1000 only

//Months limit for price chart in product details page
define('PRODUCT_CHART', 12);

//limit of alternate pricing in basket page
define('ALTERNATE_PRICE_LIMIT', 20); 

//android FCM url
define('FCM_ANDROID_URL', 'https://fcm.googleapis.com/fcm/send'); 

//Google play store URL
define('PLAY_STORE_URL', 'https://play.google.com/store/apps/details?id=com.thebestdeals'); 
//define('GOOGLE_FCM_KEY', 'AIzaSyBOND37d4v7orU3XkUQBBmQvoWaR5CXf7Q'); //Demo
define('GOOGLE_FCM_KEY', 'AIzaSyAjkgfg-afHdNlhkI-Uc0UqjEStj1aD_as'); //TBD account

//Apple App store URL
define('APP_STORE_URL', 'https://itunes.apple.com/us/app/the-best-deals/id1206490613?ls=1&mt=8'); 

/* End of file constants.php */
/* Location: ./application/config/constants.php */