<?php
if ($this -> session -> userdata('user_id')) {
    ?>
    <footer class="main-footer text-center"> <strong>Copyright &copy; 2014-2015 <a href="#">The Best Deals</a>.</strong> All rights reserved. </footer>
    <?php
}
?>
</div>


<!-- Bootstrap Core JavaScript -->
<script src="<?php echo $this -> config -> item('admin_assets'); ?>js/bootstrap.min.js"></script>
<script src="<?php echo $this -> config -> item('admin_assets'); ?>js/app.min.js"></script>


<!-- Datatables JavaScript -->
<script src="<?php echo $this -> config -> item('admin_assets'); ?>js/jquery.dataTables.min.js"></script>
<script src="<?php echo $this -> config -> item('admin_assets'); ?>js/plugins/dataTables/dataTables.bootstrap.js"></script>
<script src="<?php echo $this -> config -> item('admin_assets'); ?>js/plugins/dataTables/dataTables.responsive.js"></script>
<script src="<?php echo $this -> config -> item('admin_assets'); ?>js/plugins/dataTables/dataTables.tableTools.min.js"></script>

<!-- Select2 -->
<script src="<?php echo $this -> config -> item('admin_assets'); ?>js/plugins/select2/select2.full.min.js"></script>

<!-- Toastr Javascript -->
<script src="<?php echo $this -> config -> item('admin_assets'); ?>js/plugins/toastr/toastr.min.js"></script>

<!-- Validate JavaScript -->
<script src="<?php echo $this -> config -> item('admin_assets'); ?>js/plugins/validate/jquery.validate.min.js"></script>

<!-- Datepicker JavaScript -->
<script src="<?php echo $this -> config -> item('admin_assets'); ?>js/plugins/datepicker/datepicker.js"></script>

<!-- Timepicker JavaScript -->
<script src="<?php echo $this -> config -> item('admin_assets'); ?>js/plugins/timepicker/bootstrap-timepicker.min.js"></script>
<script src="<?php echo $this -> config -> item('admin_assets'); ?>js/plugins/colorpicker/bootstrap-colorpicker.min.js"></script>

<script src="<?php echo $this -> config -> item('admin_assets'); ?>js/plugins/icheck/icheck.min.js"></script>
<!-- Custom JavaScript -->
<script src="<?php echo $this -> config -> item('admin_assets'); ?>js/custom.js"></script>

<!-- JavaScript loaded per page -->
<?php if ($this -> router -> fetch_class() == 'home' && $this -> session -> userdata('user_id') && ($this -> session -> userdata('user_type') == 1 || $this -> session -> userdata('user_type') == 2 || $this -> session -> userdata('user_type') == 3 || $this -> session -> userdata('user_type') == 5)) { ?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/proj4js/2.3.6/proj4.js"></script>
    <script src="<?php echo $this -> config -> item('admin_assets'); ?>js/plugins/highmaps/highmaps.js"></script>
    <script src="http://code.highcharts.com/maps/modules/exporting.js"></script>
    <script src="http://code.highcharts.com/mapdata/countries/za/za-all.js"></script>
    <script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.9.2/jquery-ui.js"></script>
    <script src="http://www.highcharts.com/samples/maps/demo/all-maps/jquery.combobox.js"></script>

    <script src="<?php echo $this -> config -> item('admin_assets'); ?>js/plugins/highcharts/highcharts.js"></script>
    <script src="<?php echo $this -> config -> item('admin_assets'); ?>js/plugins/highcharts/modules/data.js"></script>
    <script src="<?php echo $this -> config -> item('admin_assets'); ?>js/plugins/highcharts/modules/drilldown.js"></script>

<?php } ?>
<script src="<?php echo $this -> config -> item('admin_assets'); ?>js/pages/<?php echo $this -> router -> fetch_class(); ?>.js"></script>



<!-- Image cropper -->
<script src="<?php echo $this -> config -> item('admin_assets'); ?>js/plugins/cropper/cropper.min.js"></script>

<?php if ($this -> router -> fetch_class() == 'content' || $this -> router -> fetch_class() == 'emailtemplate' || $this -> router -> fetch_class() == 'mailbox') { ?>
    <!-- Tinymice Javascript -->
    <script src="<?php echo $this -> config -> item('admin_assets'); ?>js/plugins/tinymce/tinymce.min.js"></script>
    <script type="text/javascript">
        function init_tinymce(){
            tinyMCE.editors = [];
            tinymce.init({
                plugins: "autoresize",
                selector: "textarea#content",
                font_formats: 'Andale Mono=andale mono,times;'+ 'Arial=arial,helvetica,sans-serif;'+ 'Arial Black=arial black,avant garde;'+ 'Book Antiqua=book antiqua,palatino;'+ 'Comic Sans MS=comic sans ms,sans-serif;'+ 'Courier New=courier new,courier;'+ 'Georgia=georgia,palatino;'+ 'Helvetica=helvetica;'+ 'Impact=impact,chicago;'+ 'Raleway=raleway,zapf dingbats'+ 'Symbol=symbol;'+ 'Tahoma=tahoma,arial,helvetica,sans-serif;'+ 'Terminal=terminal,monaco;'+ 'Times New Roman=times new roman,times;'+ 'Trebuchet MS=trebuchet ms,geneva;'+ 'Verdana=verdana,geneva;'+ 'Webdings=webdings;'+ 'Wingdings=wingdings,zapf dingbats',
                fontsize_formats: "8pt 10pt 12pt 14pt 16pt 18pt 20pt 22pt 24pt 26pt 28pt 30pt 32pt 34pt 36pt 38pt 40pt 42pt 44pt 46pt 48pt 50pt 52pt 54pt 56pt 58pt 60pt 62pt 64pt 66pt 68pt 70pt 72pt 74pt 76pt 78pt 80pt",
                theme: "modern",
                plugins: [
                    "advlist autolink colorpicker link image lists charmap print preview hr anchor pagebreak",
                    "searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
                    "save table contextmenu directionality emoticons template paste textcolor responsivefilemanager"
                ],
                toolbar1: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | print preview media fullpage | forecolor backcolor emoticons",
                toolbar2: "fontsizeselect | fontselect ",
                theme_advanced_buttons: "forecolor backcolor",
                theme_advanced_fonts: "Michroma=Michroma, sans-serif;",
                directionality : 'ltr',
                relative_urls: false,
                remove_script_host: false,
                force_br_newlines : true,
                force_p_newlines : false,
                browser_spellcheck : true,
                height : 600,
                forced_root_block : false,
                external_filemanager_path:"<?php echo $this -> config -> item('admin_assets'); ?>js/plugins/tinymce/filemanager/",
                filemanager_title:"Responsive Filemanager" ,
                external_plugins: { "filemanager" : "<?php echo $this -> config -> item('admin_assets'); ?>js/plugins/tinymce/filemanager/plugin.js",
                    "nanospell": "<?php echo $this -> config -> item('admin_assets'); ?>js/plugins/tinymce/nanospell/plugin.js"
                },
                plugin_preview_height:550,
                plugin_preview_width:800,
                nanospell_server: "php",
                nanospell_dictionary: "en",
                resize: false,
                init_instance_callback : function(editor) {
                    $("#"+editor.id+"_ifr").attr('title','');
                }
            });
        }
        init_tinymce();
    </script>
<?php } ?>
<!-- Spinner -->
<script src="<?php echo $this -> config -> item('admin_assets'); ?>js/jquery-ui.js"></script>

<?php if ($this -> router -> fetch_class() == 'stores' || $this -> router -> fetch_class() == 'home' || $this -> router -> fetch_class() == 'insights') { ?>
    <script src="<?php echo $this -> config -> item('admin_assets'); ?>js/plugins/canvas/canvasjs.min.js"></script>
    <!--<script src="<?php //echo $this -> config -> item('admin_assets');         ?>js/plugins/canvas/Chart.min.js"></script>-->
    <!-- Map -->
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCRXOgcXC1MO7dyCZwTt9IuW0Fz8upWpDM" ></script>
    <script src="<?php echo $this -> config -> item('admin_assets'); ?>js/pages/stores.js"></script>

<?php } ?>
    
<?php if ($this -> router -> fetch_class() == 'insights') { ?>
    <script src="//cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.min.js"></script>
    <script src="<?php echo $this -> config -> item('admin_assets'); ?>js/plugins/knob/jquery.knob.min.js"></script>
    
    <script src="<?php echo $this -> config -> item('admin_assets'); ?>js/plugins/chartjs/ChartJS.js"></script>
    <script src="<?php echo $this -> config -> item('admin_assets'); ?>js/plugins/sparkline/jquery.sparkline.min.js"></script>
    <script src="<?php echo $this -> config -> item('admin_assets'); ?>js/plugins/jvectormap/jquery-jvectormap-1.2.2.min.js"></script>
    <script src="<?php echo $this -> config -> item('admin_assets'); ?>js/plugins/jvectormap/jquery-jvectormap-world-mill-en.js"></script>
    <script src="<?php echo $this -> config -> item('admin_assets'); ?>js/plugins/jvectormap/jquery-jvectormap-za-mill.js"></script>
<?php } ?>
    
<?php if ($this -> router -> fetch_class() == 'users') { ?>
    <!-- Map -->
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCRXOgcXC1MO7dyCZwTt9IuW0Fz8upWpDM&libraries=places" ></script>

<?php } ?>

</body>
</html>
