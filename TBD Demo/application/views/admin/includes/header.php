<!DOCTYPE html>
<html lang="en">

    <head>

        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="">
        <meta name="author" content="">

        <title>The Best Deals</title>

        <!-- Bootstrap Core CSS -->
        <link href="<?php echo $this -> config -> item('admin_assets'); ?>css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=Raleway|Varela+Round|Baloo+Bhai|Oswald" />




        <!-- Custom Fonts -->
        <link href="<?php echo $this -> config -> item('admin_assets'); ?>font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">

        <!-- Datatables CSS -->
        <link href="<?php echo $this -> config -> item('admin_assets'); ?>css/plugins/dataTables/dataTables.bootstrap.css" rel="stylesheet" type="text/css">
        <link href="<?php echo $this -> config -> item('admin_assets'); ?>css/plugins/dataTables/dataTables.responsive.css" rel="stylesheet" type="text/css">
        <link href="<?php echo $this -> config -> item('admin_assets'); ?>css/plugins/dataTables/dataTables.responsive.css" rel="stylesheet" type="text/css">

        <!--  Bootstrap datepicker CSS -->
        <link href="<?php echo $this -> config -> item('admin_assets'); ?>css/plugins/datepicker/datepicker.css" rel="stylesheet" type="text/css">

        <!-- Toastr CSS -->
        <link href="<?php echo $this -> config -> item('admin_assets'); ?>css/plugins/toastr/toastr.min.css" rel="stylesheet" type="text/css">

        <!-- Bootstrap time Picker -->
        <link rel="stylesheet" href="<?php echo $this -> config -> item('admin_assets'); ?>css/plugins/timepicker/bootstrap-timepicker.min.css">

        <!-- Select2 -->
        <link rel="stylesheet" href="<?php echo $this -> config -> item('admin_assets'); ?>css/plugins/select2/select2.min.css">

        <!-- Spinner
        <link href="<?php echo $this -> config -> item('admin_assets'); ?>css/jquery-ui.min.css" rel="stylesheet">
		comment on 11th sept
 -->
        <!-- Cropper -->
        <link href="<?php echo $this -> config -> item('admin_assets'); ?>css/plugins/cropper/cropper.min.css" rel="stylesheet">

        <link href="<?php echo $this -> config -> item('admin_assets'); ?>css/plugins/highmaps/highmaps.css" rel="stylesheet">
        <link href="<?php echo $this -> config -> item('admin_assets'); ?>css/plugins/icheck/all.css" rel="stylesheet">

        <!-- Bootstrap Color Picker -->
        <link rel="stylesheet" href="<?php echo $this -> config -> item('admin_assets'); ?>css/plugins/colorpicker/bootstrap-colorpicker.css">
        <link rel="stylesheet" href="<?php echo $this -> config -> item('admin_assets'); ?>css/plugins/jvectormap/jquery-jvectormap-1.2.2.css">
        
        <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.css">

        <!-- Custom Theme CSS -->
        <link href="<?php echo $this -> config -> item('admin_assets'); ?>css/admin-style.min.css" rel="stylesheet">
        <link href="<?php echo $this -> config -> item('admin_assets'); ?>css/_all-skins.min.css" rel="stylesheet">
        <link href="<?php echo $this -> config -> item('admin_assets'); ?>css/custom.css" rel="stylesheet">
		<link href="<?php echo $this -> config -> item('admin_assets'); ?>css/percircle.css" rel="stylesheet">
		<link href="<?php echo $this -> config -> item('admin_assets'); ?>css/chartist.min.css" rel="stylesheet">

        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
            <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
            <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
        <![endif]-->

        <!-- jQuery -->
        <script src="<?php echo $this -> config -> item('admin_assets'); ?>js/jquery.js"></script>
        <script src="<?php echo $this -> config -> item('admin_assets'); ?>js/js.cookie.js"></script>
		<link href="<?php echo $this -> config -> item('admin_assets'); ?>css/morris-0.4.3.min.css"></script>
		
        <script type="text/javascript">
            var tbd_csrf = "<?php echo $this -> security -> get_csrf_hash(); ?>";
        </script>
    </head>