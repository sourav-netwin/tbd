var oTable = '';
var oTable_select = '';
var search_array = {};
var already_added_array = {};
var special_f_sel = '';
var first_page_html = '';
var base_url = '';
var combo_products_array = {};
var special_product_id = '';
var show_selected_products = '';

var special_add_form_data = '';
var is_selected_products_shown = '';

$(function(){
	base_url = $('#base_url').val();
	validate_special_add_form();
	first_page_html = $('#specials_add_div').html();
	init_front_date();
	get_special_backup();
//	if(Cookies.getJSON('search_array') == '' || typeof Cookies.getJSON('search_array') == 'undefined'){
//		search_array = {};
//	}
//	else{
//		search_array = Cookies.getJSON('search_array');
//	}
	
});

//$('body').on('change','#special_name_sel', function(){
//	var elm = $(this);
//	var spl_nm = $('#special_name_cont');
//	if(elm == '' || typeof elm == 'undefined'){
//		spl_nm.show();
//	}
//	else{
//		spl_nm.hide();
//	}
//});
$('body').on('change','#special_name_sel', function(){
	var elm = $(this);
	var span_bg = 'background: url('+$('#base_url').val()+'assets/admin/img/temp-slider.jpg) no-repeat scroll 0% 0%;background-size: 100% 13vw;';
	var special_sel = elm.val();
	//	$('#special_name').val('');
	//	$('#price_from').val('');
	//	$('#price_to').val('');
	//	$('#special_terms').val('');
	//	$('.spbanner_image').attr('style',span_bg);
	if(special_sel != '' && typeof special_sel != 'undefined'){
		$('#special_name_cont').hide();
		$('#special_name_cont').attr('disabled','');
		$('#price_from').attr('disabled','');
		$('#price_to').attr('disabled','');
		//$('#special_terms').attr('disabled','');
		$('#inputImage').attr('disabled','');
		//$('.spbanner_image').toggleClass('spbanner_image spbanner_image_dis');
		special_f_sel = special_sel;
		$.ajax({
			url: $('#base_url').val()+'admin/customspecials/get_special_details',
			type: 'POST',
			dataType: 'json',
			data: {
				special_sel: special_sel
			},
			success: function(data){
				if(data.result == 1){
					$('#price_from').val(data.from);
					$('#price_to').val(data.to);
					$('#special_terms').html(data.terms);
					if(data.image){
						$('.spbanner_image').attr('style','background: url("'+$('#base_url').val()+'admin/../assets/images/specials/medium/'+data.image+'") no-repeat scroll 0% 0%;background-size: 100% 13vw;');
					}
					else{
						$('.spbanner_image').attr('style',span_bg);
					}
				}
				else{
					Command: toastr["error"](data.message)
				}
			},
			error: function(){
				
			}
		});
	}
	else{
		//		$('#special_name_cont').removeAttr('disabled','');
		//		$('#price_from').removeAttr('disabled','');
		//		$('#price_to').removeAttr('disabled','');
		//		$('#special_terms').removeAttr('disabled','');
		//		$('#inputImage').removeAttr('disabled','');
		//		$('.spbanner_image_dis').toggleClass('spbanner_image_dis spbanner_image');
		//		$('#special_name_cont').show();
		$('#special_name_cont').val('');
		$('#price_from').val('');
		$('#price_to').val('');
		$('#special_terms').html('');
		$('.spbanner_image').attr('style',span_bg);
		
	}
});

function validate_special_add_form(){
	$("#special_add_form").validate({
		ignore: [],
		rules: {
			special_name_sel : {
				required: function(e){
					if($('#special_name_cont').is(":visible")){
						return false;
					}
					else{
						return true;
					}
				}
			},
			price_from : {
				required: function(e){
					var spl_val = $('#special_name_sel').val();
					if(spl_val == '' || typeof spl_val == 'undefined'){
						return true;
					}
					else{
						return false;
					}
				}
			},
			price_to : {
				required: function(e){
					var spl_val = $('#special_name_sel').val();
					if(spl_val == '' || typeof spl_val == 'undefined'){
						return true;
					}
					else{
						return false;
					}
				}
			}
		},
		messages: {
			special_name_sel : {
				required: "Please select the special"
			},
			price_from : {
				required: "Please select price from"
			},
			price_to : {
				required: "Please select price to"
			}
		}
	});
}

$('body').on('submit','#special_add_form',function(e){
	e.preventDefault();
	e.stopImmediatePropagation();
	var data = new FormData(this);
        special_add_form_data = data;
	loading();
	$.ajax({
		url: $('#base_url').val()+'admin/customspecials/add_special_new',
		type: 'POST',
		dataType: 'json',
		data: data,
		contentType: false,
		cache: false,
		processData: false,
		success: function (data) {
			unloading();
			if(data.result == 1){
				append_table_start();
				show_table();
				$('.search_filter').select2();
				$('#lio').removeClass('current');
				$('#lio').addClass('visited');
				$('#lit').addClass('current');
			}
			else{
				Command: toastr["error"](data.message);
			}
		},
		error: function(){
			unloading();
		}
	});
});

function append_table_start(){
	var base_url = $("#base_url").val();
	var sel = '';
	$.ajax({
		url: base_url+'admin/customspecials/get_main_categories',
		type: 'POST',
		data: {},
		async: false,
		success: function(data){
			if(data != ''){
				sel = data;
			}
		},
		error: function(){
			
		}
	});
        /*
	var html = '<div class="search_filter_container">\n\
    <div class="col-lg-12">\n\
        <div class="col-xs-3" style="width: 260px;">\n\
            <div class="form-group pull-left">\n\
                <label for="search">SEARCH:</label>\n\
                <input type="text" class="form-control" id="search_filter" name="search_filter">\n\
            </div>\n\
        </div>\n\
        <div class="col-xs-3" style="width: 245px;">\n\
            <div class="form-group pull-left" style="width:200px !important">\n\
                <select class="search_filter" id="sel_main_cat" >\n\
				<option value="">Select Main Category</option>'+sel+'\n\
				</select>\n\
            </div>\n\
        </div>\n\
        <div class="col-xs-3">\n\
            <div class="form-group pull-left" style="width:200px !important">\n\
                <select class="search_filter" id="sel_par_cat">\n\
				<option value="">Select Parent Category</option>\n\
				</select>\n\
            </div>\n\
        </div>\n\
        <div class="col-xs-3">\n\
            <div class="form-group pull-right">\n\
                <label>Count: <span id="sel_prd_cnt">0</span><span id="clear-search-array" title="Clear" style="font-weight: bold; cursor: pointer">&nbsp;x</span></label>\n\
            </div>\n\
        </div>\n\
    </div>\n\
</div>\n\
<div class="">\n\
    <div class="col-lg-12">\n\
        <div class="table-responsive" id="special-product-listing">\n\
            <table id="special-management-table" class="table table-bordered table-hover table-striped dataTables">\n\
                <thead>\n\
                    <tr>\n\
                        <th width="1%" class="no-sort"><input type="checkbox" id="select_products" /></th>\n\
                        <th>Main Category</th>\n\
                        <th>Parent Category</th>\n\
                        <th width="45%">Product Name</th>\n\
                        <!--<th width="30%">Store</th>-->\n\
			<th width="5%">RRP</th>\n\
                        <th width="5%">Default Price</th>\n\
                        <th width="5%">Offer Price</th>\n\
                        <th width="7%" class="alignCenter">Quantity</th>\n\
                    </tr>\n\
                </thead>\n\
                <tbody></tbody>\n\
            </table>\n\
        </div>\n\
<div class="form-group">\n\
            <div class="row">\n\
                <div class="col-md-12">\n\
                    <button type="button" class="btn btn-primary btn-xs block full-width m-b" id="add_special_new">Confirm Pricing</button>\n\
                    <a class="btn btn-danger btn-xs block full-width m-b" href="javascript:void(0)" id="back_1">Back</a>\n\
                </div>\n\
            </div>\n\
        </div>\n\
</div>\n\
</div>';           
      */
     
        var html = '<div class="search_filter_container">\n\
    <div class="col-lg-12">\n\
        <div class="col-xs-10 col-sm-10 col-md-10">\n\
            <div class="form-group pull-left">\n\
                <label for="search">SEARCH</label>\n\
                <input type="text" class="form-control" id="search_filter" name="search_filter">\n\
            </div>\n\
            <div class="form-group pull-left" style="width:200px !important">\n\
                <select class="search_filter" id="sel_main_cat" >\n\
				<option value="">Select Main Category</option>'+sel+'\n\
				</select>\n\
            </div>\n\
            <div class="form-group pull-left" style="width:200px !important; margin-left:5px">\n\
                <select class="search_filter" id="sel_par_cat">\n\
				<option value="">Select Parent Category</option>\n\
				</select>\n\
            </div>\n\
            <div class="form-group pull-left" style="margin-left:10px">\n\
                <span style="margin-left:5px;margin-right:5px"><label>Home Brand </label></span> <span style="margin-top:5px;"><input type="checkbox" id="home_brand"  /><span>\n\
            </div>\n\
        </div>\n\
        <div class="col-xs-2 col-sm-2 col-md-2 text-right">\n\
            <div class="form-group pull-right">\n\
                <label>Count: <span id="sel_prd_cnt">0</span><span id="clear-search-array" title="Clear" style="font-weight: bold; cursor: pointer">&nbsp;x</span></label>\n\
            </div>\n\
        </div>\n\
    </div>\n\
</div>\n\
<div class="">\n\
    <div class="col-lg-12">\n\
        <div class="table-responsive" id="special-product-listing">\n\
            <table id="special-management-table" class="table table-bordered table-hover table-striped dataTables">\n\
                <thead>\n\
                    <tr>\n\
                        <th width="1%" class="no-sort"><input type="checkbox" id="select_products" /></th>\n\
                        <!--<th>Main Category</th>-->                        <!--<th>Parent Category</th>-->\n\
                        <th width="45%">Product Name</th>\n\
                        <!--<th width="30%">Store</th>-->\n\
			<th width="5%">RRP</th>\n\
                        <th width="5%">Default Price</th>\n\
                        <th width="5%">Offer Price</th>\n\
                        <th width="7%" class="alignCenter">Qty</th>\n\
                        <th width="5%">Coupon</th>\n\
                        <th width="2%" class="alignCenter">Combo</th>\n\
                    </tr>\n\
                </thead>\n\
                <tbody></tbody>\n\
            </table>\n\
        </div>\n\
<div class="form-group">\n\
            <div class="row">\n\
                <div class="col-md-12">\n\
                    <button type="button" class="btn btn-primary btn-xs block full-width m-b" id="add_special_new">Confirm Pricing</button>\n\
                    <a class="btn btn-danger btn-xs block full-width m-b" href="javascript:void(0)" id="back_1">Back</a>\n\
                </div>\n\
            </div>\n\
        </div>\n\
</div>\n\
</div>';
            
            
	$('#specials_add_div').html(html);
}
function append_table_second(){
	var base_url = $("#base_url").val();
	var sel = '';
	//	$.ajax({
	//		url: base_url+'admin/customspecials/get_main_categories',
	//		type: 'POST',
	//		data: {},
	//		async: false,
	//		success: function(data){
	//			if(data != ''){
	//				sel = data;
	//			}
	//		},
	//		error: function(){
	//			
	//		}
	//	});
	var html = '<div class="search_filter_container">\n\
    <div class="col-lg-12">\n\
        <div class="col-xs-6">\n\
            <div class="form-group pull-left">\n\
                <label for="search">Search:</label>\n\
                <input type="text" class="form-control" id="search_filter" name="search_filter">\n\
            </div>\n\
        </div>\n\
    </div>\n\
</div>\n\
<div class="">\n\
    <div class="col-lg-12">\n\
        <div class="table-responsive" id="special-product-listing-selected">\n\
            <table id="special-management-table-selected" class="table table-bordered table-hover table-striped dataTables">\n\
                <thead>\n\
                    <tr>\n\
                        <th width="1%" class="no-sort"><input type="checkbox" id="select_products" /></th>\n\
                        <!--<th>Main Category</th>-->\n\
                        <!--<th>Parent Category</th>-->\n\
                        <th width="45%">Product Name</th>\n\
                        <!--<th width="30%">Store</th>-->\n\
                        <th width="5%">RRP</th>\n\
                        <th width="5%">Default Price</th>\n\
                        <th width="5%">Offer Price</th>\n\
                        <th width="7%" class="alignCenter">Qty</th>\n\
                        <th width="5%">Coupon</th>\n\
                        <th width="2%" class="alignCenter">Combo</th>\n\
                    </tr>\n\
                </thead>\n\
                <tbody></tbody>\n\
            </table>\n\
        </div>\n\
<div class="form-group">\n\
            <div class="row">\n\
                <div class="col-md-12">\n\
                    <button type="button" class="btn btn-primary btn-xs block full-width m-b" id="add_special_all">Finish</button>\n\
                    <a class="btn btn-danger btn-xs block full-width m-b" href="javascript:void(0)" id="back_2">Back</a>\n\
                </div>\n\
            </div>\n\
        </div>\n\
</div>\n\
</div>';
	$('#specials_add_div').html(html);
}

function show_table(){
    
        is_selected_products_shown = '';
	var base_url = $("#base_url").val();
	if(typeof oTable == 'object'){
		oTable.fnDestroy();
	}
        /*
        if($('#home_brand').is(':checked'))
            alert('checked');
        else
            alert('unchecked');
        */
       
        show_selected_products = '';
        
	oTable = $('#special-management-table').dataTable({
		"paging": true,
		"bSortable": true,
		"bProcessing": true,
		"bServerSide": true,
		"sAjaxSource": base_url+'admin/customspecials/datatable/',
		"bJQueryUI": true,
		"sPaginationType": "full_numbers",
		"iDisplayLength":100,
		"lengthMenu": [ 25, 50, 75, 100 ],
		"dom": '<"top"f>rt<"bottom row"<"col-md-3"l><"col-md-3"i><"col-md-6"p>>',
		"columns": [
                    {
                            data: "selectVal","bSearchable" : false
                    }
                    /*
                    ,
                    {
                            data: "MainCategory","bSearchable" : false
                    },
                    {
                            data: "ParentCategory","bSearchable" : false
                    }
                    */
                    ,
                    {
                            data: "ProductName","bSearchable" : false,className: "productName"
                    },
                    //		{
                    //			data : "StoreName"
                    //		},
                    {
                            data : "act_RRP","bSearchable" : false,"className": "right-aligned-column"
                    },
                    {
                            data : "RRP","bSearchable" : false,"className": "right-aligned-column"
                    },
                    {
                            data: "SpecialPrice","bSearchable" : false
                    },
                    {
                            data: "SpecialQty","bSearchable" : false
                    },
                    {
                            data: "CouponAmount","bSearchable" : false, "className": "coupon-column-background"
                    },
                    {
                            data: "Actions", 
                            "bSortable" : false,
                            "bSearchable" : false,
                            "aTargets" : [ "no-sort" ],
                            "className": "center-aligned-column"
                    } 
		],
                "order": [[ 1, "ASC" ]],
		"oLanguage": {
			"sProcessing": "<img src='../assets/admin/img/ajax-loader_dark.gif'>"
		},
                /*
		"aoColumnDefs": [
		{
			"targets": [0],
			"bSortable" : false
		},
		{
			"targets": [1],
			"bSortable" : false,
			"visible": false
		},
		{
			"targets": [2],
			"bSortable" : false,
			"visible": false
		},
		{
			"targets": [3],
			"bSortable" : false
		},
		{
			"targets": [4],
			"bSortable" : true
		},
		{
			"targets": [5],
			"bSortable" : false
		},
		{
			"targets": [6],
			"bSortable" : false
		},
		{
			"targets": [7],
			"bSortable" : false
		},
		//		{
		//			"targets": [7],
		//			"bSortable" : false
		//		}
		],
                */
		"dom": '<"top"f>rt<"bottom row"<"col-md-3"l><"col-md-3"i><"col-md-6"p>>',
		'fnServerData': function (sSource, aoData, fnCallback) {
			sSource = sSource;
                        var home_brand = $('#home_brand').is(':checked') ? 1 : 0;
                        
                        sSource = sSource+''+home_brand;
			aoData.push({
				name: 'csrf_tbd_token', 
				value: tbd_csrf
			});
			$.ajax
			({
				'dataType': 'json',
				'type': 'POST',
				'url': sSource,
				'data': aoData,
				'success': fnCallback
			});
		},
		'fnCreatedRow': function( nRow, aData, iDataIndex ) {
                    $(nRow).attr('data-id',aData['u_id']);
                    $(nRow).attr('data-user-role',aData['RoleId']);

                    if(aData['ComboProducts'] > 0)
                    {
                        $('td', nRow).find('.fa-plus-circle').addClass('fa fa-fw fa-med fa-plus-circle highlight-red');
                    }
		},
		fnDrawCallback: function( oSettings ) {
			$('div.dataTables_filter input').addClass("form-control");
			if($(".pagination li").length > 0){
				$(".pagination li").removeClass("ui-button ui-state-default");
				$(".first.disabled, .previous.disabled, .next.disabled, .last.disabled, .fg-button.active").off( "click" );
				$(".first.disabled a, .previous.disabled a, .next.disabled a, .last.disabled a, .fg-button.active a").attr('href','javascript:void(0);');
			}
			if($('input[name="store_products[]"]').length > 0){
				var count = 0;
				var i;
				for (i in search_array) {
					if (search_array.hasOwnProperty(i)) {
						count++;
					}
				}
				if(count > 0){
					fill_selected_price();
					show_sel_count();
				}
			}
		}
	});
}
function show_table_selected(){
	$('#lio').removeAttr('class');
	$('#lio').attr('class','visited');
	$('#lit').removeAttr('class');
	$('#lit').attr('class','visited');
	$('#lir').removeAttr('class');
	$('#lir').attr('class','current');
	$('#lif').removeAttr('class');
	append_table_second();
	var base_url = $("#base_url").val();
        
        show_selected_products = 'yes';
        
	oTable_select = '';
	oTable_select = $('#special-management-table-selected').dataTable({
		"paging": true,
		"bSortable": true,
		"bProcessing": true,
		"bServerSide": true,
		"sAjaxSource": base_url+'admin/customspecials/datatable_selected/',
		"bJQueryUI": true,
		"sPaginationType": "full_numbers",
		"iDisplayLength":999999,
		"lengthMenu": [ 25, 50, 75, 100 ],
		"dom": '<"top"f>rt<"bottom row"<"col-md-3"l><"col-md-3"i><"col-md-6"p>>',
		"columns": [
                    {
                            data: "selectVal"
                    },
                    /*
                    {
                            data: "MainCategory"
                    },
                    {
                            data: "ParentCategory"
                    },
                    */
                    {
                            data: "ProductName",className: "productName"
                    },
                    //		{
                    //			data : "StoreName"
                    //		},
                    {
                            data : "act_RRP","className": "right-aligned-column"
                    },
                    {
                            data : "RRP","className": "right-aligned-column"
                    },
                    {
                            data: "SpecialPrice"
                    },                    
                    {
                            data: "SpecialQty"
                    },
                    {
                            data: "CouponAmount","bSearchable" : false, "className": "coupon-column-background" 
                    },
                    {
                            data: "Actions", 
                            "bSortable" : false,
                            "bSearchable" : false,
                            "aTargets" : [ "no-sort" ],
                            "className": "center-aligned-column"
                    } 
		],
                "order": [[ 1, "asc" ]],
		"oLanguage": {
			"sProcessing": "<img src='../assets/admin/img/ajax-loader_dark.gif'>"
		},
                /*
		"aoColumnDefs": [
		{
			"targets": [0],
			"bSortable" : false
		},
		{
			"targets": [1],
			"bSortable" : false,
			"visible": false
		},
		{
			"targets": [2],
			"bSortable" : false,
			"visible": false
		},
		{
			"targets": [3],
			"bSortable" : false
		},
		{
			"targets": [4],
			"bSortable" : false
		},
		{
			"targets": [5],
			"bSortable" : false
		},
		{
			"targets": [6],
			"bSortable" : false
		}
		//		{
		//			"targets": [7],
		//			"bSortable" : false
		//		}
		],
                */
		"dom": '<"top"f>rt<"bottom row"<"col-md-3"l><"col-md-3"i><"col-md-6"p>>',
		'fnServerData': function (sSource, aoData, fnCallback) {
			sSource = sSource;
			aoData.push({
				name: 'csrf_tbd_token', 
				value: tbd_csrf
			},
			{
				name: 'selected',
				value: JSON.stringify(search_array)
			}
			);
			$.ajax
			({
				'dataType': 'json',
				'type': 'POST',
				'url': sSource,
				'data': aoData,
				'success': fnCallback
			});
		},
		'fnCreatedRow': function( nRow, aData, iDataIndex ) {
			$(nRow).attr('data-id',aData['u_id']);
			$(nRow).attr('data-user-role',aData['RoleId']);
                        
                        if(aData['ComboProducts'] > 0)
                        {
                            $('td', nRow).find('.fa-plus-circle').addClass('fa fa-fw fa-med fa-plus-circle highlight-red');
                        }
		},
		fnDrawCallback: function( oSettings ) {
			$('div.dataTables_filter input').addClass("form-control");
			if($(".pagination li").length > 0){
				$(".pagination li").removeClass("ui-button ui-state-default");
				$(".first.disabled, .previous.disabled, .next.disabled, .last.disabled, .fg-button.active").off( "click" );
				$(".first.disabled a, .previous.disabled a, .next.disabled a, .last.disabled a, .fg-button.active a").attr('href','javascript:void(0);');
			}
			if($('input[name="store_products[]"]').length > 0){
				var count = 0;
				var i;

				for (i in search_array) {
					if (search_array.hasOwnProperty(i)) {
						count++;
					}
				}
				if(count > 0){
					fill_selected_price();
				}
			}
		}
	});
}
$('body').on('keyup','#search_filter',function(){
	oTable.fnFilter($(this).val()) ;
});
$('body').on('change','#sel_main_cat',function(){
	var elm = $(this);
	var base_url = $("#base_url").val();
	var sel = '';
	var sel_val = elm.val();
	$('#sel_par_cat').html('<option value="">Select Parent Category</option>');
	$('#sel_par_cat').select2();
	if(sel_val != '' && typeof sel_val != 'undefined'){
		$.ajax({
			url: base_url+'admin/customspecials/get_parent_categories',
			type: 'POST',
			data: {
				sel_val: sel_val
			},
			async: false,
			success: function(data){
				if(data != ''){
					$('#sel_par_cat').html(data);
					$('#sel_par_cat').select2();
				}
			},
			error: function(){

			}
		});
	}
		
	if(elm.val() == ''){
		oTable.fnFilter('',2) ;
		oTable.fnFilter('',1) ;
	}
	else{
		oTable.fnFilter($("#sel_main_cat option:selected").text(),1) ;
	}
	
});
$('body').on('change','#sel_par_cat',function(){
	var elm = $(this);
	if(elm.val() == ''){
		oTable.fnFilter('',2) ;
	}
	else{
		oTable.fnFilter($("#sel_par_cat option:selected").text(),2) ;
	}
	
});

$('#home_brand').change(function(){
    var main_cat = $('#sel_main_cat').val();
    var par_cat = $('#sel_par_cat').val();
    
    if(main_cat > 0 )
    {
            oTable.fnFilter($("#sel_main_cat option:selected").text(),1) ;
    }
    
    if(par_cat > 0 )
    {
       oTable.fnFilter($("#sel_par_cat option:selected").text(),2) ;     
    }
});


$('body').on('click','#home_brand',function(){
    var main_cat = $('#sel_main_cat').val();
    var par_cat = $('#sel_par_cat').val();
    
    if(main_cat > 0 )
    {
            oTable.fnFilter($("#sel_main_cat option:selected").text(),1) ;
    }
    
    if(par_cat > 0 )
    {
       oTable.fnFilter($("#sel_par_cat option:selected").text(),2) ;     
    }

    if(main_cat == 0 && par_cat == 0)
    {
            oTable.fnFilter('',2) ;
            oTable.fnFilter('',1) ;  
    }
    
});


function add_to_search(id){
	setTimeout(function(){
		if(id == '' || typeof id == 'undefined'){
			$('input[name="store_products[]"]:checked').each(function(){
				var elm = $(this);
				var parent = elm.parent().parent();
				var def_elm_td = parent.find('td:nth-child(4)');
				var sel_id = elm.val();
				if(def_elm_td.find('.prod_def').length == 0){
					def_elm_td.html('<input class="prod_def click_sel" style="width:65px" id="price_def_'+sel_id+'" value="'+def_elm_td.text()+'" type="text">');
				}
				
				//elm.change();
				
				var price_elm = elm.parent().parent().find('input.prod_prc');
				var qty_elm = elm.parent().parent().find('select');
                                
				//16June2017
                                var coupon_amount_elm = elm_sel.parent().parent().find('input.coupon_amount');
                                
				search_array[sel_id] = {
					"price": price_elm.val() == '' ? "0.00" : price_elm.val(), 
					"qty": qty_elm.val(),
					"def": $('#price_def_'+sel_id).val(),
                                        "coupon_amount": coupon_amount_elm.val() == '' ? "0.00" : coupon_amount_elm.val()
				}
			});
		}
		else{
			if($('input[name="store_products[]"][value="'+id+'"]').prop('checked')){
				var elm_sel = $('input[name="store_products[]"][value="'+id+'"]');
				var price_elm = elm_sel.parent().parent().find('input.prod_prc');
				var qty_elm = elm_sel.parent().parent().find('select');
                                
                                //16June2017
                                var coupon_amount_elm = elm_sel.parent().parent().find('input.coupon_amount');
				search_array[id] = {
					"price": price_elm.val() == '' ? "0.00" : price_elm.val(), 
					"qty": qty_elm.val(),
					"def": $('#price_def_'+id).val(),
                                        "coupon_amount": coupon_amount_elm.val() == '' ? "0.00" : coupon_amount_elm.val()
				};
			}
			else{
				remove_search_value(id);
			}

		}
		console.log(search_array); 
		add_to_special_backup(search_array);
		show_sel_count();
	},20);
	
		
}

function remove_search_value(id){
	if(id == '' || typeof id == 'undefined'){
		$('input[name="store_products[]"]').each(function(){
			var elm = $(this);
			var val = elm.val();
			var parent = elm.parent().parent();
			var def_elm_td = parent.find('td:nth-child(4)');
			def_elm_td.text($('#price_def_'+val).val());
			if(!elm.prop('checked')){
				delete search_array[val];
			}
		});
	}
	else{
		delete search_array[id];
	}
	console.log(search_array);
	add_to_special_backup(search_array);
	show_sel_count();
}
$('body').on('change','input[name="store_products[]"]', function(){
	var elm = $(this);
	var val = elm.val();
	var parent = elm.parent().parent();
	var def_elm_td = parent.find('td:nth-child(4)');
	if(elm.prop('checked')){
		def_elm_td.html('<input class="prod_def click_sel" style="width:65px" id="price_def_'+val+'" value="'+def_elm_td.text()+'" type="text">');
		add_to_search(val);
	}
	else{
		def_elm_td.text($('#price_def_'+val).val());
		remove_search_value(val);
	}
});
$('body').on('change','#select_products', function(e){
	e.preventDefault();
	e.stopImmediatePropagation();
	var elm = $(this);
	if(elm.prop('checked')){
		add_to_search();
	}
	else{
		remove_search_value();
	}
});

$('body').on('change', '.prod_prc', function(){
	var elm = $(this);
	var id = elm.attr('id').replace('product_price_','');
	add_to_search(id);
});
$('body').on('change', '.prod_def', function(){
	var elm = $(this);
	var id = elm.attr('id').replace('price_def_','');
	add_to_search(id);
});
$('body').on('change', '.prd_qty', function(){
	var elm = $(this);
	var elm_cb = elm.parent().parent().find('input[type="checkbox"]');
	var id = elm_cb.val();
	add_to_search(id);
});
$('body').on('change', '.coupon_amount', function(){
	var elm = $(this);
	var id = elm.attr('id').replace('coupon_amount_','');
	add_to_search(id);
});

function show_sel_count(){
	var count = 0;
	var i;
	for (i in search_array) {
		if (search_array.hasOwnProperty(i)) {
			count++;
		}
	}
	$('#sel_prd_cnt').html(count);
}
$('body').on('click','#add_special_new', function(e){
	e.preventDefault();
	e.stopImmediatePropagation();
	var error = 0;
	var count = 0;
	var base_url = $("#base_url").val();
	$.each(search_array, function( index, value ) {
		if(value.price <= 0){
			error++;
		}
		else if(!$.isNumeric(value.price)){
			error++;
		}
		count++;
	});
	//if(error == 0){
	if(count == 0){
		alert('Please select atleast one product before continue');
	}
	else{
                is_selected_products_shown = 'Yes';
		show_table_selected();
	}
	
/*$.ajax({
			url: base_url+'admin/customspecials/add_special_product_new',
			type: 'POST',
			dataType: 'json',
			data: {
				product_details: JSON.stringify(search_array)
			},
			success: function(data){
				if(data.result == 1){
					window.location.reload();
				}
				else{
					Command: toastr["error"](data.message);
				}
			},
			error: function(){
				alert('Something went wrong. Please try again');
			}
		});*/
//	}
//	else{
//		alert('Please enter valid price for all selected products');
//	}
});

$('body').on('click','#add_special_all', function(e){
	e.preventDefault();
	e.stopImmediatePropagation();
	var error = 0;
	var base_url = $("#base_url").val();
	$.each(search_array, function( index, value ) {
		if(value.price <= 0){
			$('#product_price_'+index).parent().parent().attr('style', 'background-color:#fba3a3');
			error++;
		}
		else if(!$.isNumeric(value.price) || !$.isNumeric(value.def)){
			$('#product_price_'+index).parent().parent().attr('style', 'background-color:#fba3a3');
			error++;
		}
		else{
			$('#product_price_'+index).parent().parent().removeAttr('style'); 
		}
	});
	if(error == 0){
		var c = confirm('Please verify the prices before continue. Specials with equal/higher price than the default will be rejected!');
		if(c){
			loading();
			var send_products = JSON.stringify(search_array);
			console.log(search_array);
			$.ajax({
				url: base_url+'admin/customspecials/get_special_stores',
				type: 'POST',
				dataType: 'json',
				data: {},
				success: function(data){
					if(data.result == 1){
						var total_count_get = data.message.length;
						var processed_count = 1;
						var success_count = 0;
						$.each(data.message,function(index, value){
							
							var complete_per = (processed_count/total_count_get)*100;
							//$('#loading-text-dyn').html('<div id="progress-color" style="width: '+complete_per+'%"></div>Inserting to stores: <span>'+processed_count+'/'+total_count_get+'</span>');
							$('#loading-text-dyn').html('<div class="new-loader">'+
									'<div class="left-text"><h3>This Promotion is been added to</h3>'+
									'<div id="loading-text-dyn" style="position:relative;" class="outer-progress"> <div id="progress-colorr" style="width: '+complete_per+'%"></div></div>'+
										'<h2 id="loader_sub_text"></h2>	'+
									'</div><div class="numbers">'+
										'<span class="plain-text">'+processed_count+'</span>'+
										'<span class="number-bg">'+total_count_get+'</span>'+
									'</div></div>');
							$.ajax({
								url: base_url+'admin/customspecials/get_store_name',
								type: 'POST',
								async: false,
								dataType: 'json',
								data: {
									id: value.Id
								},
								success: function(data){
										
									if(data.result == 1){
										$('#loader_sub_text').text(data.message);
										if($.inArray(value.Id, already_added_array) == -1){
											$.ajax({
												url: base_url+'admin/customspecials/add_special_product_new',
												type: 'POST',
												async: false,
												dataType: 'json',
												data: {
													product_details: send_products,
													AllStates: value.AllStates,
													AllStores: value.AllStores,
													Id: value.Id,
													RetailerId: value.RetailerId,
													SpecialName: value.SpecialName,
													StateId: value.StateId,
													StoreTypeId: value.StoreTypeId
												},
												success: function(data){
													//unloading();
													if(data.result == 1){
														success_count++;
													//special_final();
													//Cookies.remove('search_array');
													}
													else{
													//Command: toastr["error"](data.message);
													}
												},
												error: function(){
												//unloading();
												//alert('Something went wrong. Please try again');
												}
											});
										}
									}
								}
							});
							processed_count++;
						});
						
						if(success_count > 0){
							remove_special_backup();
							special_final();
							//Cookies.remove('search_array');
							unloading();
						}
						else{
							unloading();
								Command: toastr["error"]('Failed to add specials'); 
						}
					}
				},
				error: function(){
					unloading();
				}
			});
		}
	}
	else{
		alert('Please enter valid price for all selected products');
	}
});
$('body').on('click','#cancel_special_new', function(e){
	e.preventDefault();
	var c = confirm('Are you sure to cancel? All saved data will lost');
	if(c){
		window.location.reload();
	}
	
});
function first_page(){
	var specials_list = '';
	$.ajax({
		url: $('#base_url').val()+'admin/customspecials/get_special_list',
		type: 'POST',
		dataType: 'json',
		async: false,
		data: {},
		success: function(data){
			if(data.result == 1){
				specials_list = data.message;
				special_f_sel = data.sel;
			}
		}
	});
	var html = '<form action="" method="post" id="special_add_form" class="form-horizontal" autocomplete="off" enctype="multipart/form-data">\n\
<div style="display:none">\n\
<input name="csrf_tbd_token" value="'+tbd_csrf+'" type="hidden">\n\
</div>	\n\
<div class="col-xs-5 col-xs-offset-1">\n\
            <div class="form-group">\n\
                <div class="row">\n\
                    <div class="col-md-12">\n\
                        <label for="special_name_sel">Special <span>*</span></label>\n\
                        <select name="special_name_sel" id="special_name_sel" class="form-control select-filter">\n\
                            <option value=""> New Special </option>'+specials_list+'\n\
                        </select>\n\
                    </div>\n\
                </div>\n\
            </div>\n\
            <div class="form-group">\n\
                <div class="btn-group profile_image_group" style="max-width: unset">\n\
                    <label for="inputImage" class="btn btn-primary btn-xs spbanner_image">\n\
                    </label>\n\
                </div>\n\
                <div class="img-preview img-preview-sm"></div>\n\
            </div>\n\
            <div class="form-group">\n\
                <div class="row">\n\
                    <div class="col-md-6">\n\
                        <label for="price_from">Special From <span>*</span></label>\n\
                        <div class="input-group">\n\
                            <div class="input-group-addon">\n\
                                <i class="fa fa-calendar"></i>\n\
                            </div>\n\
                            <input type="text" class="form-control" name="price_from" id="price_from" placeholder="Price From" value="" disabled="">\n\
                        </div>\n\
                        <div class="error">\n\
                        </div>\n\
                    </div>\n\
                    <div class="col-md-6">\n\
                        <label for="price_to" >Special To <span>*</span></label>\n\
                        <div class="input-group">\n\
                            <div class="input-group-addon">\n\
                                <i class="fa fa-calendar"></i>\n\
                            </div>\n\
                            <input type="text" class="form-control" name="price_to" id="price_to" placeholder="Price To" value="" disabled="">\n\
                        </div>\n\
                        <div class="error">\n\
                        </div>\n\
                    </div>\n\
                </div>\n\
            </div>\n\
            <div class="form-group">\n\
                <div class="row">\n\
                    <div class="col-md-12">\n\
                        <label for="special_name" >Terms and conditions for this specials</label>\n\
                        <div id="special_terms"></div>\n\
                    </div>\n\
                </div>\n\
            </div>\n\
            <div class="form-group">\n\
                <div class="row">\n\
                    <div class="col-md-12">\n\
                        <button type="submit" class="btn btn-primary btn-xs block full-width m-b">Select Products</button>\n\
                    </div>\n\
                </div>\n\
            </div>\n\
        </div>\n\
        <div class="modal fade" id="imageModal" data-keyboard="false" data-backdrop="static">\n\
            <div class="modal-dialog">\n\
                <div class="modal-content">\n\
                    <div class="modal-header">\n\
                        <h4 class="modal-title" id="myModalLabel"> <span id="form-action">Select</span></h4>\n\
                    </div>\n\
                    <div class="modal-body" >\n\
                        <div class="image-crop">\n\
                            <img src="' + $('#base_url').val() + 'assets/admin/img/default.gif">\n\
                        </div>\n\
                        <div><a id="crop-button" class="btn btn-primary btn-xs block full-width m-b">Select</a></div>\n\
                        <input type="hidden" name="image-x" id="image-x">\n\
                        <input type="hidden" name="image-y" id="image-y">\n\
                        <input type="hidden" name="image-width" id="image-width">\n\
                        <input type="hidden" name="image-height" id="image-height">\n\
                        <input type="hidden" name="aspect_ratio" id="aspect_ratio" value="2.80">\n\
                    </div>\n\
                </div>\n\
            </div>\n\
        </div>\n\
</form>';
	//var html = first_page_html;
	$('#specials_add_div').html(html);
	validate_special_add_form();
	if(special_f_sel != '' && typeof special_f_sel != 'undefined'){
		$('.select2').remove();
		$('#special_name_sel').val(special_f_sel);
		$('#special_name_sel').select2();
		$('#special_name_sel').change();
		$('#lit').attr('class','');
		$('#lio').removeAttr('class');
		$('#lio').attr('class','current');
		$('#lir').removeAttr('class');
		$('#lif').removeAttr('class');
	}
	init_front_date();
//init_cropper();
	
}

$('body').on('click', '#back_1', function(){
	first_page();
});
$('body').on('click', '#back_2', function(){
	$('#lio').removeAttr('class');
	$('#lio').attr('class','visited');
	$('#lit').removeAttr('class');
	$('#lit').attr('class','current');
	$('#lir').removeAttr('class');
	$('#lif').removeAttr('class');
	append_table_start();
	show_table();
	$('.search_filter').select2();
});

function fill_selected_price(){
	$.each(search_array, function( index, value ) {
		var check_elm = $('input[type="checkbox"][value="'+index+'"]');
		var parent = check_elm.parent().parent();
		var def_elm_td = parent.find('td:nth-child(4)');
		var price_elm = check_elm.parent().parent().find('input.prod_prc');
		var def_elm = check_elm.parent().parent().find('input.prod_def');
		var qty_elm = check_elm.parent().parent().find('select').val(value.qty);
                var coupon_amount_elm = check_elm.parent().parent().find('input.coupon_amount');
                
		def_elm_td.html('<input class="prod_def click_sel" style="width:65px" id="price_def_'+ index +'" value="'+value.def+'" type="text">');
		check_elm.prop('checked', true);
		price_elm.val(value.price);
		qty_elm.val(value.qty);
                coupon_amount_elm.val(value.coupon_amount);
	});
}

function init_front_date(){
	$("#price_from").datepicker({
		format: 'yyyy-mm-dd',
		todayHighlight: true
	});

	$("#price_to").datepicker({
		format: 'yyyy-mm-dd',
		todayHighlight: true
	});

	$('#price_from').datepicker({
		format: 'yyyy-mm-dd',
		autoclose: true
	}).on('changeDate', function(selected){
		startDate = new Date(selected.date.valueOf());
		startDate.setDate(startDate.getDate(new Date(selected.date.valueOf())));
		$('#price_to').datepicker('setStartDate', startDate);
	});

	$('#price_to').datepicker({
		format: 'yyyy-mm-dd',
		autoclose: true
	}).on('changeDate', function(selected){
		FromEndDate = new Date(selected.date.valueOf());
		FromEndDate.setDate(FromEndDate.getDate(new Date(selected.date.valueOf())));
		$('#price_from').datepicker('setEndDate', FromEndDate);
	});
}

function special_final(){
	$('#lio').removeAttr('class');
	$('#lio').attr('class','visited');
	$('#lit').removeAttr('class');
	$('#lit').attr('class','visited');
	$('#lir').removeAttr('class');
	$('#lir').attr('class','visited');
	$('#lif').removeAttr('class');
	$('#lif').attr('class','current');
	var html = '<div class="row">\n\
	<div class="col-xs-10 col-xs-offset-1 ">\n\
		<div class="special-final-1">\n\
			You successfully completed adding the specials. The below buttons will help you for further actions\n\
		</div>\n\
		<div class="text-center">\n\
			<a href="'+$('#base_url').val()+'admin/specialmanagement"><input class="btn btn-primary" value="Approve the specials" type="button"></a>&nbsp;&nbsp;<a href="'+$('#base_url').val()+'admin/customspecials"><input class="btn btn-primary" value="Add new specials" type="button"></a>\n\
		</div>\n\
	</div>\n\
</div>';
	$('#specials_add_div').html(html);
}

$('body').on('click', '#clear-search-array', function(){
	var c = confirm('Are you sure to delete the saved special details?');
	if(c){
		remove_special_backup();
		show_table();
	}
	
});

function add_to_special_backup(special_array_content){
	$.ajax({
		url: base_url+'admin/customspecials/add_to_special_backup',
		type: 'POST',
		dataType: 'json',
		data: {
			special_array: JSON.stringify(special_array_content)
		}
	});
}

function get_special_backup(){
	$.ajax({
		url: base_url+'admin/customspecials/get_special_backup',
		type: 'POST',
		dataType: 'json',
		data: {},
		async: false,
		success: function(data){
			if(data.result == 1){
				search_array = JSON.parse(data.message);
				already_added_array = data.added;
				console.log(search_array);
				console.log(already_added_array);
			}
		},
		error: function(){
			
		}
	});
}

function remove_special_backup(){
	$.ajax({
		url: base_url+'admin/customspecials/remove_special_backup',
		type: 'POST',
		dataType: 'json',
		data: {},
		async: false
	});
	get_special_backup();
	show_sel_count();
}


    /* Function use to open */    
    $('body').on('click', '.combo_offer', function(e){
        e.preventDefault();
        var elm = $(this);
        var productId = $(this).attr('id');
        var parent = elm.parent().parent().parent();
        var product_name = parent.find('.productName').html();
        
        //alert("product_name : "+product_name);
        
        special_product_id = productId;
        
        var url = elm.attr('data-href');
        $.ajax({
            url: url,
            data: {},
            success: function(data){
                combo_products_array = {};
                
                //createModal('combo_products-catalogue-modal', 'Select Combo Products for : ' + product_name, data,'large');
                createModal('combo_products-catalogue-modal', 'Select Combo Products for : ' + product_name, data,'wd-85');
                
                get_special_combo_prodcuts_backup();
                show_combo_product_table();
                //$('select').select2();
            }
        });
    });
    
    
    
    function show_combo_product_table()
    {
        base_url = $("#base_url").val();
        
        oTable = $('#combo-products-table').dataTable({
		"paging": true,
		"bSortable": true,
		"bProcessing": true,
		"bServerSide": true,
		"sAjaxSource": base_url+'admin/customspecials/combo_products_datatable/',
		"bJQueryUI": true,
		"sPaginationType": "full_numbers",
		"iDisplayLength":50,
		"lengthMenu": [ 25, 50, 75, 100 ],
		"dom": '<"top"f>rt<"bottom row"<"col-md-3"l><"col-md-3"i><"col-md-6"p>>',
		"columns": [
                    {
                            data: "selectVal","bSearchable" : false
                    },
                    {
                            data: "ProductName","bSearchable" : true
                    },                    
                    {
                            data: "SpecialQty","bSearchable" : false
                    }
		],
                "order": [[ 1, "ASC" ]],
		"oLanguage": {
			"sProcessing": "<img src='../assets/admin/img/ajax-loader_dark.gif'>"
		},
		"dom": '<"top"f>rt<"bottom row"<"col-md-3"l><"col-md-3"i><"col-md-6"p>>',
		'fnServerData': function (sSource, aoData, fnCallback) {
			sSource = sSource;
                        var home_brand = $('#home_brand').is(':checked') ? 1 : 0;
                        
                        sSource = sSource+''+home_brand;
                        
                        if(show_selected_products == 'yes')
                        {
                            aoData.push({	
				name: 'csrf_tbd_token', 
				value: tbd_csrf
                            },
                            {
                               name: 'selected_combo_products',
                               value: JSON.stringify(combo_products_array)
                            }
                            );  
                        }else{
                            aoData.push({
				name: 'csrf_tbd_token', 
				value: tbd_csrf
                            });
                        }
                        
			$.ajax
			({
				'dataType': 'json',
				'type': 'POST',
				'url': sSource,
				'data': aoData,
				'success': fnCallback
			});
		},
		'fnCreatedRow': function( nRow, aData, iDataIndex ) {
			$(nRow).attr('data-id',aData['u_id']);
			$(nRow).attr('data-user-role',aData['RoleId']);
		},
		fnDrawCallback: function( oSettings ) {
			$('div.dataTables_filter input').addClass("form-control");
			if($(".pagination li").length > 0){
				$(".pagination li").removeClass("ui-button ui-state-default");
				$(".first.disabled, .previous.disabled, .next.disabled, .last.disabled, .fg-button.active").off( "click" );
				$(".first.disabled a, .previous.disabled a, .next.disabled a, .last.disabled a, .fg-button.active a").attr('href','javascript:void(0);');
			}
                        
			if($('input[name="store_combo_products[]"]').length > 0){
                            var count = 0;
                            var i;
                            for (i in combo_products_array) {
                                if (combo_products_array.hasOwnProperty(i)) {
                                    count++;
                                    }
				}
				if(count > 0){
                                    fill_selected_combo_products();
                                    //show_sel_count();
				}
			}
                        
		}
	});
    }
    
    
    function fill_selected_combo_products(){
	$.each(combo_products_array, function( index, value ) {
                var check_elm = $('input[type="checkbox"][id="comboProd_'+index+'"]');		
		var qty_elm = check_elm.parent().parent().find('select').val(value.qty);
		check_elm.prop('checked', true);
		qty_elm.val(value.qty);
	});
    }

    
    /* Add combo products  */
    $('body').on('change','input[name="store_combo_products[]"]', function(){
        var elm             = $(this);
        var val             = elm.val();
        var parent          = elm.parent().parent();
        var qty_elm         = elm.parent().parent().find('select');
        var comboProductQty = qty_elm.val();

        if(elm.prop('checked')){
            add_to_selected_combo_products(val);
        }
        else{
            remove_products_from_selected_combo_products(val);
        }
    });
    
    /* Function to add products into combo_products_array  */
    function add_to_selected_combo_products(id){
	setTimeout(function(){
		if(id == '' || typeof id == 'undefined'){
			$('input[name="store_combo_products[]"]:checked').each(function(){
				var elm = $(this);
				var parent = elm.parent().parent();
				var sel_id = elm.val();
				var qty_elm = elm.parent().parent().find('select');
                                
                                //alert("If qty_elm.val() : " + qty_elm.val() );
                                
				combo_products_array[sel_id] = {
                                    "product_id": sel_id,
                                    "qty": qty_elm.val()
				}
			});
		}
		else{
                    if($('input[name="store_combo_products[]"][value="'+id+'"]').prop('checked')){
                        var elm_sel = $('input[name="store_combo_products[]"][value="'+id+'"]');
                        var qty_elm = elm_sel.parent().parent().find('select');

                        //alert("Else qty_elm.val() : " + qty_elm.val() );
                        combo_products_array[id] = {
                            "product_id": id,
                            "qty": qty_elm.val()
                        };
                    }
                    else{
                        remove_products_from_selected_combo_products(id);
                    }
		}
		console.log(combo_products_array);                 
		add_to_special_combo_products_backup(combo_products_array);
                
                //
		//show_sel_count();
	},20);
    }

    /* Function to remove products from combo_products_array */
    function remove_products_from_selected_combo_products(id){
        if(id == '' || typeof id == 'undefined'){
            $('input[name="combo_products_array[]"]').each(function(){
                    var elm = $(this);
                    var val = elm.val();
                    var parent = elm.parent().parent();
                    if(!elm.prop('checked')){
                        delete combo_products_array[val];
                    }
            });
        }
        else{
            delete combo_products_array[id];
        }
        console.log(combo_products_array);
        add_to_special_combo_products_backup(combo_products_array);
        
        //show_sel_count();
    }
    
    $('body').on('change', '.comboprod_qty', function(){
            var elm = $(this);
            var elm_cb = elm.parent().parent().find('input[type="checkbox"]');
            var id = elm_cb.val();
            add_to_selected_combo_products(id);
    });
    
    /* Function to add prodcuts into special_combo_products_backup table */
    function add_to_special_combo_products_backup(combo_products_array_content){
        var special_id = special_f_sel;
        var spl_product_id = special_product_id;
        
	$.ajax({
		url: base_url+'admin/customspecials/add_to_special_combo_products_backup',
		type: 'POST',
		dataType: 'json',
		data: {
                    special_id : special_id,
                    special_product_id : spl_product_id, 
                    special_combo_products: JSON.stringify(combo_products_array_content)
		},
                success: function(data){
                    if(data.comboProductCount == 1){
                      $('#'+spl_product_id).find('.fa-plus-circle').addClass('fa fa-fw fa-med fa-plus-circle highlight-red');  
                    }else{
                      $('#'+spl_product_id).find('.fa-plus-circle').removeClass('highlight-red');    
                    }
                },
                error: function(){

                }
	});
        
        
         
    }

    /* Function to get selected combo products */
    function get_special_combo_prodcuts_backup(){
        var special_id = special_f_sel;
        var spl_product_id = special_product_id;
        
	$.ajax({
            url: base_url+'admin/customspecials/get_special_combo_products_backup',
            type: 'POST',
            dataType: 'json',
            data: {
                    special_id : special_id,
                    special_product_id : spl_product_id
		},
            async: false,
            success: function(data){
                if(data.result == 1){
                    combo_products_array = JSON.parse(data.message);
                    console.log(combo_products_array);
                }
            },
            error: function(){

            }
	});
    }
    
    
    $('body').on('click', '#combo_products-catalogue-modal .close', function(){
        loadProducts();        
    });
    
    $('body').on('click', '#comboproduct_back', function(){
        loadProducts();
        $("#combo_products-catalogue-modal .close").click();
        
    });
    
    $('body').on('click', '#comboproduct_add', function(){
        //append_table_start();
        loadProducts();
        $("#combo_products-catalogue-modal .close").click();
        
    });
    
    
    
    function loadProducts()
    {
        if(is_selected_products_shown == '')
        {
            // Here special_add_form_data is formdata when we are selecting any special
            data = special_add_form_data;

            loading();
            $.ajax({
                    url: $('#base_url').val()+'admin/customspecials/add_special_new',
                    type: 'POST',
                    dataType: 'json',
                    data: data,
                    contentType: false,
                    cache: false,
                    processData: false,
                    success: function (data) {
                            unloading();
                            if(data.result == 1){
                                    append_table_start();
                                    show_table();
                                    $('.search_filter').select2();
                                    $('#lio').removeClass('current');
                                    $('#lio').addClass('visited');
                                    $('#lit').addClass('current');
                            }
                            else{
                                    Command: toastr["error"](data.message);
                            }
                    },
                    error: function(){
                            unloading();
                    }
            });
                
        }
        
    }