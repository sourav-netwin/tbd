<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Custom messages for status and replies from webservices.
|--------------------------------------------------------------------------
|
| These messages are used whenever an operation is executed and status messages are to be returned.
|
*/

// WEBSERVICE MESSAGES
define('SUCCESS','Success');
define('FAIL','Fail');

//-----------MESSAGES RELATED TO TOKEN-------------
define('INVALID_TOKEN_MESSAGE','Invalid API token.');
define('NO_TOKEN_MESSAGE','No API token received.');
define('VALID_TOKEN_MESSAGE','API Token validated.');

//-----------MESSAGES RELATED TO LOGIN, REGISTRATION AND EDIT PROFILE-------------
define('INVALID_USER_CREDENTIALS','Invalid email or password.');
define('VALID_USER_CREDENTIALS','Login success.');
define('INVALID_USER','Invalid user.');
define('INVALID_USER_ID','Invalid user id.');
define('SAME_USER_ID','Both user Id cannot be same.');
define('VALID_USER','Valid user details found.');
define('INVALID_EMAIL','Please enter a valid email.');
define('IMAGE_UPLOAD_FAIL','Unable to upload image.');

define('REGISTRATION_FAILED','Registration failed.');
define('REGISTRATION_SUCCESS','Registration successful.');

define('PROFILE_UPDATE_FAILED','Failed to update profile.');
define('PROFILE_UPDATE_SUCCESS','Profile updated successfully.');

define('FB_EMAIL_UPDATE_SUCCESS','Email updated successfully.');
define('FB_EMAIL_UPDATE_FAILED','Failed to update email.');
define('TW_EMAIL_UPDATE_SUCCESS','Email updated successfully.');
define('TW_EMAIL_UPDATE_FAILED','Failed to update email.');

define('USER_EXISTS','The user already exists.');
define('EMAIL_EXISTS','The email already exists.');
define('PREF_LOCATION_EXISTS','Preferred location is set.');
define('PREF_LOCATION_NOT_EXISTS','Preferred location is not set.');
define('PREF_LOCATION_SUCCESS','Preferred location updated successfully.');
define('PREF_LOCATION_FAIL','Failed to update preferred location.');
define('USERNAME_EXISTS','The username already exists.');
define('USERNAME_AND_EMAIL_EXISTS','The username and email already exists.');
define('FB_ACCOUNT_EXISTS','The facebook account already exists.');
define('FB_ACCOUNT_NOT_EXISTS','The facebook account is not available.');
define('TW_ACCOUNT_EXISTS','The twitter account already exists.');
define('TW_ACCOUNT_NOT_EXISTS','The twitter account is not available.');
define('GP_ACCOUNT_EXISTS','The google account already exists.');
define('GP_ACCOUNT_NOT_EXISTS','The google account is not available.');
define('SEARCH_PRODUCT_NOT_FOUND','The searched product is not found.');

//-----------MESSAGES RELATED TO CHANGE PASSWORD-------------
define('PASSWORD_MISMATCH','Old password is invalid.');
define('PASSWORD_MATCH','Old password is correct.');
define('PASSWORD_UPDATE_FAILED','Failed to update password.');
define('PASSWORD_UPDATE_SUCCESS','Profile updated password.');

//-----------MESSAGES RELATED TO FORGET PASSWORD-------------
define('EMAIL_NOT_FOUND','Email not found.');
define('ACCOUNT_INACTIVE','Sorry, your account awaits admin approval.');
define('EMAIL_SEND_FAILED','Something went wrong, could not send mail.');
define('EMAIL_SENT','Email sent successfully.');

/* End of file messages.php */
/* Location: ./application/config/messages.php */