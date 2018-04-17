<?php

/*
 * Author:  PM
 * Purpose: Login Controller
 * Date:    07-10-2015
 */

class Login extends My_Front_Controller 
{
	public $data = array();

	function __construct() {
        parent::__construct();
        $this->load->model('front/usermodel','',TRUE);
        $this->load->helper('cookie');

        //Get active parent categories to be displayed that contain active not deleted products
        $this->data['categories'] = $this->categories;

        //Get all active categories to be displayed that contain active not deleted products
        $this->data['all_categories'] = $this->all_categories;
        
        $this -> load -> library('instagram_api');
    }

    /*
     * Function to load the login page
     */
    /*public function index() 
    {
        $this -> load -> library('instagram_api');
        $this -> session -> set_userdata('called_from', 'login');
        $this->template->front_view('front/login', $this->data, 1);
    }*/

    /*
     * Function called after user clicks Sign up button on login page
     */
    public function signup()
    {
    	if ($this->input->server('REQUEST_METHOD') == 'POST') 
    	{
    		$this->form_validation->set_rules('email', 'email', 'trim|required|valid_email');    		
            $this->form_validation->set_rules('password', 'password', 'trim|required|xss_clean|callback_check_database');
            
            if (!$this->form_validation->run() == FALSE) 
            {
            	$this->session->set_userdata('success_message','You have logged in successfully');
    			redirect(front_url().'topoffers', 'refresh');
    		}
    		else
    		{
                $this->session->set_userdata('error_message','Invalid login credentials');
    			//$this->template->front_view('front/login', $this->data, 1);
                redirect(front_url(), 'refresh');
    		}
    	}
    }

    /*
     * Check user login in database
     */
    public function check_database( $password ) {
        $email = $this->input->post('email');
        $remember = $this->input->post('remember');
        $result = $this->usermodel->login( $email, $password );

        if ( $result ) 
        {
            foreach ($result as $row) 
            {
            	$this->session->set_userdata('userid',$result['Id']);
				$this->session->set_userdata('name',$result['FirstName']." ".$result['LastName']);
				$this->session->set_userdata('email',$result['Email']);
				$this->session->set_userdata('image',$result['ProfileImage']);

				// If user has checked 'Remember Me' set data in cookie for 90 days
				if( $remember != NULL )
				{
					$this->input->set_cookie( array('name' => 'email', 'value' => $result['Email'], 'expire' => 7776000 ) );
					$this->input->set_cookie( array('name' => 'password', 'value' => $password, 'expire' => 7776000 ) );
				}
            }
            return TRUE;
        }
        else
        {
        	$this->form_validation->set_message('check_database', 'Invalid email or password');
            return FALSE;
        }
    }

	/*
     * Forgot Password
     */
    public function forgot_password()
    {
    	if ($this->input->server('REQUEST_METHOD') == 'POST') 
    	{
    		$this->form_validation->set_rules('forgot_pwd_email', 'email', 'trim|required|xss_clean|callback_check_email');

    		if (!$this->form_validation->run() == FALSE) 
            {
            	//Set token for the user
	            $token = $this->usermodel->set_password_reset_token( $this->session->userdata('reset_user_id') );

	            $reset_password_link = front_url() . 'reset_password/' . $token;

	            $this->load->model('front/emailtemplatemodel');

	            $email_template_details = $this->emailtemplatemodel->get_email_template_details( FORGOT_PASSWORD );

	            $emailBody = $email_template_details['Content'];
	            $emailBody = str_replace("{LINK}",$reset_password_link,$emailBody);

	            //---- LOAD EMAIL LIBRARY ----//
	            $this->load->library('email');
	            $config['mailtype'] = 'html';
	            $this->email->initialize($config);

	            $this->email->from($email_template_details['FromEmail']);
	            $this->email->to($email_template_details['ToEmail']);
	            $this->email->subject("The Best Deals: Password Reset");

	            $this->email->message($emailBody);
	            $this->email->send();

	            $this->session->set_userdata('success_message','Your password reset link has been mailed to your email address');
    			redirect(front_url().'login', 'refresh');
    		}
    		else
    		{
    			$this->session->set_userdata('forgot_pwd_error','1');
    			$this->template->front_view('front/login', $this->data, 1);
    		}
    	}
    }

    /*
     * Check if user email address exists
     */
    public function check_email( $email ) {
        $result = $this->usermodel->check_email_exists( $email );

        if ($result) 
        {
            $this->session->set_userdata('reset_user_id', $result['Id']);
            return TRUE;
        }
        else 
        {
            $this->form_validation->set_message('check_email', 'Email Address does not exists');
            return FALSE;
        }
    }

    /*
     * Function for reset password
     */
    public function reset_password( $token = '' )
    {
    	$this->data['token'] = $token;
    	if ($this->input->server('REQUEST_METHOD') == 'POST') 
    	{
    		$this->form_validation->set_rules('password', 'password', 'trim|required|matches[confirm_password]');
            $this->form_validation->set_rules('confirm_password', 'confirm password', 'trim|required');
            $res = $this->form_validation->run();
            
            if ($res == TRUE && $token != "")
            {
                $new_password = $this->input->post('password');

                $result = $this->usermodel->reset_password( $token, $new_password );

                if ($result) 
                {
                	$this->session->set_userdata('success_message','Password changed successfully');
                }
                else
                {
                    $this->session->set_userdata('error_message','The password link is expired');
                }
                redirect(front_url(), 'refresh');
            }
    	}
    	else{
            $this->template->front_view('front/reset_password', $this->data, 1);
        }
    		
    }

    /*
     * Function for logout
     */
    public function logout()
    {
        $this->session->sess_destroy();
        redirect(front_url(), 'refresh');
    }
}

?>