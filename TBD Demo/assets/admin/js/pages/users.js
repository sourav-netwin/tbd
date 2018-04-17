// User Datatables
$(document).ready(function(){
	initAutocomplete();
	validate_user_form();
	var base_url = $("#base_url").val();
	if($('#users-table').length > 0){
		oTable = $('#users-table').dataTable({
			"bProcessing": true,
			"bServerSide": true,
			"sAjaxSource": base_url+'admin/users/datatable/',
			"bJQueryUI": true,
			"sPaginationType": "full_numbers",
			"iDisplayLength":25,
			"lengthMenu": [ 25, 50, 75, 100 ],
			"dom": '<"top"f>rt<"bottom row"<"col-md-3"l><"col-md-3"i><"col-md-6"p>>',
			"columns": [
			{
				data: "Name"
			},
			{
				data : "Email"
			},
			{
				data : "CompanyName"
			},
			{
				data: "Type"
			},
			{
				data: "Actions",
				"bSortable" : false,
				"aTargets" : [ "no-sort" ]
			}
			],
			"oLanguage": {
				"sProcessing": "<img src='../assets/admin/img/ajax-loader_dark.gif'>"
			},
			"dom": '<"top"f>rt<"bottom row"<"col-md-3"l><"col-md-3"i><"col-md-6"p>>',
			'fnServerData': function (sSource, aoData, fnCallback) {
				var role_search = $("#role_search").val();
				sSource = sSource+''+role_search;
				aoData.push({name: 'csrf_tbd_token', value: tbd_csrf});
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
			"fnRowCallback" : function(nRow, aData, iDisplayIndex){
                                
                                
				var html = status_html(aData["active"]);
				$("td:eq(4)", nRow).prepend(html);
				if( aData['Type'] == 'Retailers' )
				{
					var row_html = $("td:eq(4)", nRow).html().replace('<a href="#" class="delete"><i class="fa fa-fw fa-med fa-trash-o" title="Delete user"></i></a>','<i class="fa fa-fw fa-med fa-trash-o disabled_icon"></i>');

					$("td:eq(4)", nRow).html(row_html);
				}

			},
			fnDrawCallback: function( oSettings ) {
				$('div.dataTables_filter input').addClass("form-control");
				$(".pagination li").removeClass("ui-button ui-state-default");
				$(".first.disabled, .previous.disabled, .next.disabled, .last.disabled, .fg-button.active").off( "click" );
				$(".first.disabled a, .previous.disabled a, .next.disabled a, .last.disabled a, .fg-button.active a").attr('href','javascript:void(0);');
			}
		});
	}
	
	if($('#social-users-table').length > 0){
		oTable = $('#social-users-table').dataTable({
			"bProcessing": true,
			"bServerSide": true,
			"sAjaxSource": base_url+'admin/users/web_datatable/',
			"bJQueryUI": true,
			"sPaginationType": "full_numbers",
			"iDisplayLength":25,
			"lengthMenu": [ 25, 50, 75, 100 ],
			"dom": '<"top"f>rt<"bottom row"<"col-md-3"l><"col-md-3"i><"col-md-6"p>>',
			"columns": [
			{
				data: "Name"
			},
			{
				data : "Email"
			},			
                        {
				data : "SocialMedia"
			},
                        {
				data : "RegistrationDate"
			},
                        /*
                        {
				data : "loyalty_points"
			},
                        */
			{
				data: "Actions",
				"bSortable" : false,
				"aTargets" : [ "no-sort" ]
			}
			],
			"oLanguage": {
				"sProcessing": "<img src='../../assets/admin/img/ajax-loader_dark.gif'>"
			},
			"dom": '<"top"f>rt<"bottom row"<"col-md-3"l><"col-md-3"i><"col-md-6"p>>',
			'fnServerData': function (sSource, aoData, fnCallback) {
				sSource = sSource;
				aoData.push({name: 'csrf_tbd_token', value: tbd_csrf});
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
			},
			"fnRowCallback" : function(nRow, aData, iDisplayIndex){
				/*
                                var userId = aData['u_id'];
                                var baseUrl = $('#baseUrl').attr('data-id');
                                    url = baseUrl + "/users/get_loyalty_balance/"+userId;
                                
                                $.ajax({
                                        url: url,
                                        data: {},
                                        success: function(data){
                                               
                                        }
                                });
                                */
                               
                                var html = status_html(aData["active"]);
                                //$("td:eq(4)", nRow).prepend(html);
                                                         
				$("td:eq(4)", nRow).prepend(html);                                
                                     
			},
			fnDrawCallback: function( oSettings ) {
				$('div.dataTables_filter input').addClass("form-control");
				$(".pagination li").removeClass("ui-button ui-state-default");
				$(".first.disabled, .previous.disabled, .next.disabled, .last.disabled, .fg-button.active").off( "click" );
				$(".first.disabled a, .previous.disabled a, .next.disabled a, .last.disabled a, .fg-button.active a").attr('href','javascript:void(0);');
			}
		});
	}
		



	//add a table for store/storeformat admins
	if( $('#admins-table').length ) {
		oTable = $('#admins-table').dataTable({
			"bProcessing": true,
			"bServerSide": true,
			"sAjaxSource": base_url+'admin/users/store_user_datatable/',
			"bJQueryUI": true,
			"sPaginationType": "full_numbers",
			"iDisplayLength":25,
			"lengthMenu": [ 25, 50, 75, 100 ],
			"dom": '<"top"f>rt<"bottom row"<"col-md-3"l><"col-md-3"i><"col-md-6"p>>',
			"columns": [
			{
				data: "Name"
			},
			{
				data : "Email"
			},
			{
				data: "Type"
			},
			{
				data : "CompanyName"
			},
			{
				data: "Actions",
				"bSortable" : false,
				"aTargets" : [ "no-sort" ]
			}
			],
			"oLanguage": {
				"sProcessing": "<img src='../assets/admin/img/ajax-loader_dark.gif'>"
			},
			"dom": '<"top"f>rt<"bottom row"<"col-md-3"l><"col-md-3"i><"col-md-6"p>>',
			'fnServerData': function (sSource, aoData, fnCallback) {

				var admin_id = $("#admin_id").val();
				var admin_type = $("#admin_type").val();

				sSource = sSource+''+admin_type+"/"+admin_id;

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
			"fnRowCallback" : function(nRow, aData, iDisplayIndex){
				if( aData['StoreType'])
				{
					var row_html = aData['StoreType'];

				} else {

					row_html = aData['StoreName'];
				}
				$("td:eq(3)", nRow).html(row_html);
			},
			fnDrawCallback: function( oSettings ) {
				$('div.dataTables_filter input').addClass("form-control");
				$(".pagination li").removeClass("ui-button ui-state-default");
				$(".first.disabled, .previous.disabled, .next.disabled, .last.disabled, .fg-button.active").off( "click" );
				$(".first.disabled a, .previous.disabled a, .next.disabled a, .last.disabled a, .fg-button.active a").attr('href','javascript:void(0);');
			}
		});
	}


	$('table#users-table tbody').on('click', '.delete', function () {
		var data = $(this).parents('tr').data();

		if (confirm("Are you sure you want to delete this user?")) {
			window.location = "users/delete/" + data['id'];
		}
	});
	
	$('table#social-users-table tbody').on('click', '.delete', function () {
		var data = $(this).parents('tr').data();

		if (confirm("Are you sure you want to delete this user?")) {
			window.location = "web_delete/" + data['id'];
		}
	});
	
	
	
	

  


	// impostiamo gli attributi da aggiungere all'iframe es: data-src andrà ad impostare l'url dell'iframe
	$('table#users-table tbody').on('click','.edit', function(e) {
		e.preventDefault();
		var url = $(this).attr('data-href');
		$.ajax({
			url: url,
			type: 'POST',
			dataType: 'json',
			data: {},
			success: function(data){
				createModal('edit-user-modal', 'Edit User - '+data.name, data.html,'wd-75');
				validate_user_form();
				initAutocomplete();
				initSelect2();
			}
		});
			
	});
	$('table#social-users-table tbody').on('click','.edit', function(e) {
		e.preventDefault();
		var url = $(this).attr('data-href');
		$.ajax({
			url: url,
			type: 'POST',
			dataType: 'json',
			data: {},
			success: function(data){
				createModal('edit-user-modal', '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Edit User - '+data.name, data.html,'wd-75');
                                $('#edit-user-modal').find('.modal-dialog').css({
                                    width:'600'
                                });
                    
				validate_user_form();
				initAutocomplete();
				initSelect2();
			}
		});
			
	});

	// se si chiude la modale resettiamo i dati dell'iframe per impedire ad un video di continuare a riprodursi anche quando la modale è chiusa
	$('#myModal').on('hidden.bs.modal', function(){
		$(this).find('iframe').html("");
		$(this).find('iframe').attr("src", "");
	});

	$('table#users-table tbody').on('click', '.active', function () {
		var data = $(this).parents('tr').data();

		var status = $(this).data('status');
		if (confirm("Are you sure you want to change the status?")) {
			window.location = "users/change_status/" + data['id'] +"/"+status;
		}
	});
	$('table#social-users-table tbody').on('click', '.active', function () {
		var data = $(this).parents('tr').data();

		var status = $(this).data('status');
		if (confirm("Are you sure you want to change the status?")) {
			window.location = "web_change_status/" + data['id'] +"/"+status;
		}
	});

	$('table#admins-table tbody').on('click', '.delete', function () {
		var data = $(this).parents('tr').data();

		if (confirm("Are you sure you want to delete this user?")) {
			window.location = "users/store_user_delete/" + data['id'];
		}
	});



	

	$("#change_password").validate({
		ignore: [],
		errorElement: "div",
		rules: {
			password : {
				required: true
			},
			confirm_password : {
				required: true,
				equalTo: "#password"
			}
		},
		messages: {
			password : {
				required: "Please enter password"
			},
			confirm_password : {
				required: "Please enter confirm password"
			}
		}

	});

	$("#role_search").change(function(){
		oTable.fnDraw();
	});
   
	$("#store_format_selector").click(function(){
		$("#store_format_div").removeClass('hide');
		$("#store_div").addClass('hide');
	});

	$("#store_selector").click(function(){
		$("#store_div").removeClass('hide');
		$("#store_format_div").addClass('hide');
	});
});

$('body').on('submit','.modalCustom #user_form', function(e){
	e.preventDefault();
	e.stopImmediatePropagation();
	var elm = $(this);
	var url = elm.attr('action');
	loading();
	$.ajax({
		url: url,
		type: 'POST',
		dataType: 'json',
		data: new FormData(this), // Data sent to server, a set of key/value pairs (i.e. form fields and values)
		contentType: false,       // The content type used when sending data to the server.
		cache: false,             // To unable request pages to be cached
		processData:false,
		success: function(data){
			unloading();
			if(data.result == 1){
				location.reload();
			}
			else{
				placeError(data.result,data.message,'user_form')
			}
		},
		error: function(){
			unloading();
		}
	});
});


var mapAuto;
function initAutocomplete() {
	if($('#street_address').length > 0){
// Create the autocomplete object, restricting the search to geographical
		// location types.
		mapAuto = new google.maps.places.Autocomplete(
			/** @type {!HTMLInputElement} */(document.getElementById('street_address')),
			{
				types: ['geocode']
				});

		// When the user selects an address from the dropdown, populate the address
		// fields in the form.
		mapAuto.addListener('place_changed', fillInAddress);
	}
		
}

function fillInAddress() {
	// Get the place details from the autocomplete object.
	var place = mapAuto.getPlace();

	
}

//Validate User Form
	function validate_user_form(){
		$("#user_form").validate({
			ignore: [],
			rules: {
				first_name : {
					required: true,
					validateName: true,
					maxlength: 50
				},
				last_name : {
					required: true,
					validateName: true,
					maxlength: 50
				},
				telephone : {
					required: true,
					maxlength: 15,
					number: true
				},
				mobile : {
					required: true,
					maxlength: 15,
					number: true
				},
				email : {
					required: true,
					validateEmail: true
				},
				password : {
					required: true
				},
				confirm_password : {
					required: true,
					equalTo: "#password"
				},
				user_role : {
					required: true
				},
				city:{
					validateCity: true
				},
				street_address : {
					validateStreetAddress: true
				},
				pin_code:{
					number: true
				}
			},
			messages: {
				first_name : {
					required: "Please enter first name",
					validateName: "First name must contain only letters, apostrophe, spaces or dashes."
				},
				last_name : {
					required: "Please enter last name",
					validateName: "Last name must contain only letters, apostrophe, spaces or dashes."
				},
				telephone : {
					required: "Please enter Telephone(Home)"
				},
				mobile : {
					required: "Please enter Mobile No"
				},
				email : {
					required: "Please enter email"
				},
				password : {
					required: "Please enter password"
				},
				confirm_password : {
					required: "Please enter confirm password"
				},
				user_role : {
					required: "Please select user role"
				}
			}
		});
	}


        $('table#social-users-table tbody').on('click','.loyalty', function(e) {
		e.preventDefault();
		var url = $(this).attr('data-href');
		$.ajax({
			url: url,
			type: 'POST',
			dataType: 'json',
			data: {},
			success: function(data){
				createModal('show-loyalty-modal', 'Show Loyalty - '+data.name, data.html,'wd-10');                                
			}
		});
			
	});