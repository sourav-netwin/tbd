<?php

/*
 * Author:AS
 * Purpose:Mailbox Controller
 * Date:24-11-2016
 * Dependency: mailbox.php
 */

class Mailbox extends My_Controller {

    private $result;
    private $message;

    function __construct() {
        parent::__construct();
        $this -> load -> model('admin/mailboxmodel', '', TRUE);

        $this -> page_title = "Mailbox";
        $this -> breadcrumbs[] = array('label' => 'Mailbox', 'url' => '');
    }

    public function index() {
        $this -> inbox();
    }

    public function inbox() {
        $data['title'] = $this -> page_title;

        $this -> breadcrumbs[0] = array('label' => 'Mailbox', 'url' => '');
        $this -> breadcrumbs[1] = array('label' => 'Inbox', 'url' => '/inbox');
        $data['breadcrumbs'] = $this -> breadcrumbs;
        $this -> template -> view('admin/mailbox/inbox', $data);
    }
    
    public function compose() {
        $data['title'] = 'Compose';

        $this -> breadcrumbs[0] = array('label' => 'Mailbox', 'url' => '');
        $this -> breadcrumbs[1] = array('label' => 'Compose', 'url' => '/compose');
        $data['breadcrumbs'] = $this -> breadcrumbs;
        $this -> template -> view('admin/mailbox/compose', $data);
    }
}

