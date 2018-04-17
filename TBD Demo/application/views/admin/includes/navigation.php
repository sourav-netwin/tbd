<body class="hold-transition skin-blue sidebar-mini">
    <div class="wrapper">
        <header class="main-header">
            <input type="hidden" name="base_url" id="base_url" value="<?php echo $this -> config -> item('front_url') ?>">


            <!-- Header Navbar: style can be found in header.less -->
            <nav class="navbar navbar-static-top" role="navigation">
                <!-- Sidebar toggle button-->
                <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button"> <span class="sr-only">Toggle navigation</span> </a>
                <!--                <div class="navbar-logo-part">
                <?php
                //$LogoImage = $this -> session -> userdata('user_logo');
                //if ($LogoImage != '') {
                ?>
                                        <img src="<?php //echo $LogoImage  ?>" border="0" />
                <?php //} ?>
                                </div>-->
                <!-- Navbar Right Menu -->
                <div class="navbar-custom-menu">
                    <ul class="nav navbar-nav">
                        <!-- Messages: style can be found in dropdown.less-->
                        <li class="dropdown messages-menu"> <a href="#" class="dropdown-toggle" data-toggle="dropdown"> <i class="fa fa-envelope-o"></i> <span class="label label-success">4</span> </a>

                        </li>
                        <!-- Notifications: style can be found in dropdown.less -->
                        <li class="dropdown notifications-menu"> <a href="#" class="dropdown-toggle" data-toggle="dropdown"> <i class="fa fa-bell-o"></i> <span class="label label-warning">10</span> </a>

                        </li>
                        <!-- Tasks: style can be found in dropdown.less -->
                        <li class="dropdown tasks-menu"> <a href="#" class="dropdown-toggle" data-toggle="dropdown"> <i class="fa fa-flag-o"></i> <span class="label label-danger">9</span> </a>

                        </li>
                        <!-- User Account: style can be found in dropdown.less -->
                        <?php
                        $image_path = ( $this -> session -> userdata('user_image') == null ? 'default.gif' : $this -> session -> userdata('user_image') );
                        $user_image_path = $this -> config -> item('front_url') . USER_IMAGE_PATH . 'small/' . $image_path;
                        ?>
                        <li class="dropdown user user-menu"> <a href="#" class="dropdown-toggle" data-toggle="dropdown"> 
                                <img src="<?php echo $user_image_path; ?>" class="user-image" alt="User Image"> 
                                <span class="hidden-xs"><?php
                        if ($this -> session -> userdata('user_type') == 3) {
                            echo substr($this -> session -> userdata('user_company_name'), 0, 20);
                            if (strlen($this -> session -> userdata('user_company_name')) > 20) {
                                echo "...";
                            }
                        }
                        else {
                            echo substr($this -> session -> userdata('user_full_name'), 0, 20);
                            if (strlen($this -> session -> userdata('user_full_name')) > 20) {
                                echo "...";
                            }
                        }
                        ?> </span> </a>
                            <ul class="dropdown-menu">
                                <!-- User image -->
                                <li class="user-header">
                                    <a href="<?php echo site_url('account/profile'); ?>">
                                        <img src="<?php echo $user_image_path ?>" alt="User Image">
                                        <p><?php
                                    if ($this -> session -> userdata('user_type') == 3) {
                                        echo substr($this -> session -> userdata('user_company_name'), 0, 20);
                                        if (strlen($this -> session -> userdata('user_company_name')) > 20) {
                                            echo "...";
                                        }
                                    }
                                    else {
                                        echo substr($this -> session -> userdata('user_full_name'), 0, 20);
                                        if (strlen($this -> session -> userdata('user_full_name')) > 20) {
                                            echo "...";
                                        }
                                    }
                        ?> </p>

                                    </a>
                                </li>

                                <!-- Menu Footer-->
                                <li class="user-footer">
                                    <div class="pull-left"> <a href="<?php echo site_url('account/change_password'); ?>" class="btn btn-default">Change Password</a> </div>
                                    <div class="pull-right"> <a href="<?php echo site_url('home/logout'); ?>" class="btn btn-default">Sign out</a> </div>
                                </li>
                            </ul>
                        </li>

                    </ul>
                </div>
            </nav>
        </header>

        <!-- Left Menu Starts Here.......>
        <!-- Left side column. contains the logo and sidebar -->
        <aside class="main-sidebar">

            <!-- Logo -->
            <a href="<?php echo base_url(); ?>" class="logo">
                <!-- mini logo for sidebar mini 50x50 pixels -->
                <!--<span class="logo-mini"><b>TBD</b></span>-->
                <span class="logo-mini"><img src="<?php echo $this -> config -> item('admin_assets'); ?>img/logo.png" border="0"></span>


                <!-- logo for regular state and mobile devices -->
                <span class="logo-lg"> <img src="<?php echo $this -> config -> item('admin_assets'); ?>img/logo.png" border="0">
                    <img src="<?php echo $this -> config -> item('admin_assets'); ?>img/TBD-Name-Logo.png" border="0">
                </span>


            </a>

            <!-- sidebar: style can be found in sidebar.less -->
            <section class="sidebar">
                <!-- Sidebar user panel -->
                <div class="user-panel">
                    <div class="pull-left image"> <img src="<?php echo $user_image_path; ?>"  alt="User Image"> </div>
                    <div class="pull-left info">
                        <p><?php
                                            if ($this -> session -> userdata('user_type') == 3) {
                                                echo substr($this -> session -> userdata('user_company_name'), 0, 20);
                                                if (strlen($this -> session -> userdata('user_company_name')) > 20) {
                                                    echo "...";
                                                }
                                            }
                                            else {
                                                echo substr($this -> session -> userdata('user_full_name'), 0, 20);
                                                if (strlen($this -> session -> userdata('user_full_name')) > 20) {
                                                    echo "...";
                                                }
                                            }
                        ?>  </p>
                        <a href="#"></i> <?php echo $this -> session -> userdata('user_role') ?></a> </div>
                </div>

                <!--  Left Navigation Menu -->
                <ul class="sidebar-menu">
                    <?php
                    
                    $data['parent_menu'] = $data['child_menu'] = array();
//if ($this->session->userdata('user_type') == 1) {

                    foreach ($nav_menus as $menu) {
                        if ($menu['ParentId'] == 0) {
                            $data['parent_menu'][] = array('Id' => $menu['Id'], 'OptionName' => $menu['OptionName'], 'Pagename' => $menu['Pagename'], 'SequenceNo' => $menu['SequenceNo'], 'Icon' => $menu['Icon'], 'ParentId' => $menu['ParentId']);
                        }
                        else {
                            $data['child_menu'][$menu['ParentId']][] = array('Id' => $menu['Id'], 'OptionName' => $menu['OptionName'], 'Pagename' => $menu['Pagename'], 'SequenceNo' => $menu['SequenceNo'], 'Icon' => $menu['Icon'], 'ParentId' => $menu['ParentId']);
                        }
                    }

                    $arrMnu = $data['parent_menu'];
                    $childMenu = $data['child_menu'];
//                    } else {
//                        $arrMnu = $nav_menus;
//                    }
                    if (!empty($arrMnu)) {
                        foreach ($arrMnu as $row => $val):
                            //Check with the current url the menu url.
                            $class = "";
                            $current_url = base_url(uri_string());
                            if (strpos($current_url, $val['Pagename']) !== false) {
                                $class = 'active';
                            }
                            else if (strpos($current_url, 'storeformat/index') !== false && strpos($val['Pagename'], 'storeformat/index') !== false) {
                                $class = 'active';
                            }
                            if ($this -> session -> userdata('user_type') == 3 && strpos($val['Pagename'], '{RetailerId}') !== false) {
                                echo $val['Pagename'] = str_replace('{RetailerId}', $this -> session -> userdata('user_retailer_id'), $val['Pagename']);
                            }
                            else {
                                //echo $val['Pagename'] = '#';
                            }
                            ?>

                            <li class="treeview <?php echo $class ?>">
                                <a  class="disabled" href="<?php echo $val['Pagename'] == '#' ? '#' : site_url($val['Pagename']); ?>"><i class="fa fa-fw <?php echo $val['Icon'] ?>"></i> <span><?php echo $val['OptionName']; ?></span>
                                    <?php if (!empty($childMenu[$val['Id']])) { ?>
                                        <i class="fa fa-angle-left pull-right"></i>
                                    <?php } ?>
                                </a>
                                <?php
                                if($val['OptionName'] == 'Dashboard' && $this -> session -> userdata('store_promotion_active') == 'true'){
                                    ?>
                            <li><a href="<?php echo site_url('insights'); ?>"><i class="fa fa-fw fa-dashboard"></i> <span>Insights</span></a></li>
                                <?php
                                }
                                if (!empty($childMenu[$val['Id']])) {
                                    $sub_array = array();
                                    $sub_array = $childMenu[$val['Id']];
									
                                    ?>

                                    <?php
                                    $sub_count = 1;
                                    foreach ($sub_array as $row => $sub_val):
									
                                        if ($sub_count == 1) {
                                            ?>
                                            <ul class="treeview-menu">
                                                <?php
                                            }
                                            $sub_class = "";

                                            if (strpos($current_url, $sub_val['Pagename']) !== false) {
                                                $sub_class = 'active';
                                            }
                                            ?>
                                            <li class="treeview<?php echo $sub_class ?>"><a href="<?php echo site_url(str_replace('{RetailerId}', '', $sub_val['Pagename'])); ?>"><i class="fa <?php echo $sub_val['Icon'] ?>"></i> <?php echo $sub_val['OptionName']; ?>
											<?php if($sub_val['Id']==33 && $this -> session -> userdata('unReadOrders')!=0){
												?>
											<span style="background-color:red !important;" data-toggle="tooltip" title="<?php echo $this -> session -> userdata('unReadOrders'); ?> unviewed orders" class="badge bg-red unReadOrders"><?php echo $this -> session -> userdata('unReadOrders'); ?></span>
											<?php }
												?>
												
											</a></li>
                                            <?php
                                            if ($sub_count == count($sub_array)) {
                                                ?>
                                            </ul>
                                            <?php
                                        }
                                        $sub_count++;
                                    endforeach;
                                    ?>

                                <?php } ?>
                            </li>

                        <?php endforeach; ?>
                    <?php } ?>
                </ul>
            </section>
            <!-- /.sidebar -->
        </aside>

        <!-- Toastr Messages -->
        <div id="success_message" class="hide"><?php echo $this -> session -> userdata('success_message'); ?></div>
        <div id="error_message" class="hide"><?php echo $this -> session -> userdata('error_message'); ?></div>
        <?php
        $this -> session -> unset_userdata('success_message');
        $this -> session -> unset_userdata('error_message');
        ?>
        <!-- End toastr messages -->