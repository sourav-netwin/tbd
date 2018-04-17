<?php
    $this->load->view('front/includes/header');

	// If user is logged in display category header below main header
    if( $sub_header == 1 )
    	$this->load->view('front/includes/category_sub_header');

    // Contains individual page content
    $this->load->view($child_view_to_load);

    $this->load->view('front/includes/footer');
?>