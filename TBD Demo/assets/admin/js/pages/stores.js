var base_url = '';
$(document).ready(function(){
	initTimepicker();
	validate_stores_form();
        
        
        
	// Retailers Datatables
	base_url = $("#base_url").val();
	if ( ! $.fn.DataTable.isDataTable( '#stores-table' ) ) {
		oTable = $('#stores-table').dataTable({
			"bProcessing": true,
			"bServerSide": true,
			"sAjaxSource": base_url+'admin/stores/datatable/',
			"bJQueryUI": true,
			"sPaginationType": "full_numbers",
			"iDisplayLength":25,
			"lengthMenu": [ 25, 50, 75, 100 ],
			"dom": '<"top"f>rt<"bottom row"<"col-md-3"l><"col-md-3"i><"col-md-6"p>>',
			"columns": [
			{
				data: "StoreName"
			},
			{
				data: "CompanyName"
			},
			{
				data: "StoreType"
			},
			{
				data : "state_name"
			},
			{
				data : "Address"
			},
			{
				data: "Users",
				"bSortable" : false,
				"aTargets" : [ "no-sort" ]
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
			"aoColumnDefs": [
			{
				"targets": [3],
				"bSortable" : false,
				"visible": false
			},
			],
			"dom": '<"top"f>rt<"bottom row"<"col-md-3"l><"col-md-3"i><"col-md-6"p>>',
			'fnServerData': function (sSource, aoData, fnCallback) {
				var retailer_id = $('#retailers').val() == '' ? 0 : $('#retailers').val();

				var store_format_id = $("#store_format").val() == '' ? 0 : $("#store_format").val();
				var region = $("#region").val() == '' ? 0 : $("#region").val();

				sSource = sSource+''+retailer_id+"/"+store_format_id+"/"+region;
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
				$(nRow).attr('data-id',aData['s_id']);
			},
			"fnRowCallback" : function(nRow, aData, iDisplayIndex){
				var html = status_html(aData["active"]);
				$("td:eq(6)", nRow).prepend(html);
                                
                                // Add class to make row highlighted if Latitude or Longitude or store timing is not set 
                                if(aData['Latitude']== null || aData['Latitude']== 0 || aData['Longitude']== null || aData['Longitude']== 0 || aData['timing_count'] < 7)
                                {
                                  $(nRow).addClass("label-warning");      
                                  //$(nRow).addClass("row-warning");      
                                }
			},
			fnDrawCallback: function( oSettings ) {
                                //setStoreformats();
				$(".pagination li").removeClass("ui-button ui-state-default");
				$(".first.disabled, .previous.disabled, .next.disabled, .last.disabled, .fg-button.active").off( "click" );
				$(".first.disabled a, .previous.disabled a, .next.disabled a, .last.disabled a, .fg-button.active a").attr('href','javascript:void(0);');
			}
		});
	}

        setStoreformats();
	$('table#stores-table tbody').on('click', '.delete', function () {
		var data = $(this).parents('tr').data();

		if (confirm("Are you sure you want to delete this store?")) {
			$.ajax({
				url : base_url + "admin/stores/delete/" + data['id'],
				method : 'POST',
				success : function(data)
				{
					location.reload();
				}
			});
		}
	});
	
	$('table#stores-table tbody').on('click','.Edit', function(e) {
		e.preventDefault();
		e.stopImmediatePropagation();
		var url = $(this).attr('data-href');
		var parent = $(this).parent().parent().parent();
		var pro_name = parent.find('td:eq(0)').text();
		$.ajax({
			url: url,
			type: 'POST',
			data: {},
			success: function(data){
				createModal('edit-stores-modal', 'Edit Store - '+ pro_name, data,'large');
				validate_stores_form();
                                $('select').select2();
				initMap();
				initTimepicker();
				init_icheck();
			}
		});
			
	});

	$('table#stores-table tbody').on('click', '.active', function () {
		var data = $(this).parents('tr').data();

		var status = $(this).data('status');
		if (confirm("Are you sure you want to change the status?")) {
			$.ajax({
				url : base_url + "admin/stores/change_status/" + data['id'] +"/"+status,
				method : 'POST',
				success : function(data)
				{
					location.reload();
				}
			});
		}
	});

    

	$("#import_stores_form").validate({
		errorElement: "div",
		ignore: [],
		rules: {
			retailers:{
				required: true
			},
			store_format: {
				required: true
			},
			import_file :{
				required: true,
				checkFileExcel:true
			}
		},
		messages: {
			retailers:{
				required: "Please select a retailer"
			},
			store_format: {
				required: "Please select the store format"
			},
			import_file :{
				required: "Please upload file to import"
			}
		}
	});

	$(document).on('click','#import_stores', function(e){
		$("#import_stores_form").submit();
	});

	$(document).on('change','#retailers', function(e){
                //alert("Success : Retailer Change");
		var element = $(this).attr("id");
                var val = $(this).val();
		if(val == '' || typeof val == 'undefined'){
			$("#store_format").html('<option value="">Select Store Format</option>');
			$("#store_format").select2();
		}
		else{
			$.ajax({
				url : base_url+'admin/stores/get_store_formats/'+val,
				data : {
					id: val,
					type: element
				},
				method : 'POST',
				dataType: 'json',
				success : function(data)
				{
					$("#store_format").html(data.store_formats);
					$("#store_format_import").html(data.store_formats);
					$("#store_format").select2();
					$("#store_format_import").select2();
				}
			});
		}

			
	});

	// For import section
	$(document).on('change','#retailers_import', function(e){

		var element = $(this).attr("id");
		var val = $(this).val();

		$.ajax({
			url : base_url+'admin/stores/get_store_formats/'+val,
			data : {
				id: val,
				type: element
			},
			method : 'POST',
			dataType: 'json',
			success : function(data)
			{

				$("#store_format_import").html(data.store_formats);
			}
		});
	});


	//Get google map - lat & long for address
	$('body').on('click',"#display_lat_long",function()
	{
		if($('#street_address').val()!='' && $('#state').val()!='' && $('#city').val()!='' && $('#zip').val()!='')
		{
			var address = $('#street_address').val() +" "+ $('#city').val() +" "+ $("#state option:selected" ).text()+" "+ "South Africa" +" "+$('#zip').val()+ " ";

			$.ajax({
				url: base_url+'admin/stores/get_latitude_longitude',
				data : {
					address: address
				},
				method : 'POST',
				dataType: 'json',
				success: function(data){
					if(data){
						$("#latitude").val(data['latitude']);
						$("#longitude").val(data['longitude']);

						var myLatLng = {
							lat: data['latitude'],
							lng: data['longitude']
						};

						var map = new google.maps.Map(document.getElementById('map_div'), {
							zoom: 18,
							center: myLatLng
						});

						var marker = new google.maps.Marker({
							position: myLatLng,
							map: map,
							draggable: true
						});
						google.maps.event.addListener(marker, 'dragend', function (event) {
							document.getElementById("latitude").value = this.getPosition().lat();
							document.getElementById("longitude").value = this.getPosition().lng();
						});

						$("#display_status").val('1');
						$("#diplay_lat").attr('disabled','disabled');
						$("#diplay_lat").next('input').next('div').html('');
					}
					else
						alert("Please enter correct address.");
				}
			});
		}
		else
		{
			alert("Please enter all the address fields");
		}
	});
	
	

	//Reset form on popupp close
	$('#myModal').on('hidden.bs.modal', function () {
		$('#import_stores_form').trigger('reset');
		$('#retailers_import').select2();
		$('#store_format_import').select2();
	});

	//Timepicker for store timings
	function initTimepicker(){
		$('.timepicker').timepicker({
			defaultTime: false
		});
	}

});

$('body').on('submit','.modalCustom #stores_form', function(e){
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
				//location.reload();
				Command: toastr["success"](data.message);
			removeModal('edit-stores-modal');
				oTable.fnStandingRedraw();
			}
			else{
				placeError(data.result,data.message,'stores_form')
			}
		},
		error: function(){
			unloading();
		}
	});
});

$('body').on('click','#copy_time',function(e){
	e.preventDefault();
	e.stopImmediatePropagation();
	var cnt = 1;
	var from_time = '';
	var to_time = '';
	var err = 0;
	if($('input[name="open_hours[]"]').length > 0){
		$('input[name="open_hours[]"]').each(function(){
			var elm = $(this);
			if(cnt == 1){
				from_time = elm.val();
				if(from_time == '' || typeof from_time == 'undefined'){
					alert('Please set from time for monday');
					err++;
					return false;
				}
			}
			else if(cnt == 2){
				to_time = elm.val();
				if(to_time == '' || typeof to_time == 'undefined'){
					alert('Please set to time for monday');
					err++;
					return false;
				}
			}
			else{
				if(cnt % 2 !== 0){
					elm.val(from_time);
				}
				else{
					elm.val(to_time);
				}
			}
			cnt ++;
		});
	}
	if($('input[name="From"]').length > 0){
		$('input[name="From"]').each(function(){
			var elm = $(this);
			if(cnt == 1){
				from_time = elm.val();
				to_time = $('input[name="To"]:eq(0)').val();
				if(from_time == '' || typeof from_time == 'undefined'){
					alert('Please set from time for monday');
					err++;
					return false;
				}
				if(to_time == '' || typeof to_time == 'undefined'){
					alert('Please set to time for monday');
					err++;
					return false;
				}
			}
			else{
				elm.val(from_time);
				$('input[name="To"]:eq('+(cnt-1)+')').val(to_time);
			}
			cnt ++;
		});
	}
	var cnt = 1;
	var checked = false;
	if(err == 0){
		$('input[name="open_days[]"]').each(function(){
			var elm = $(this);
			if(cnt == 1){
				checked = elm.prop('checked');
			}
			else{
				elm.prop('checked', checked);
			}
			cnt ++;
		});
	}
		
});

$('body').on('change','input[name="open_days[]"]', function(){
	var elm = $(this);
	var parent = elm.parent().parent().parent();
	if(!elm.prop('checked')){
		parent.find('.timepicker').each(function(){
			$(this).val('');
		});
	}
});

//Validate Stores Form
$.validator.addMethod("one_required", function() {
    return $("#stores_form").find(".one_required:checked").length > 0;
}, 'Please select at least one group.');

function validate_stores_form(){
	$("#stores_form").validate({
		rules: {
                        
                        'groupId':{
                                one_required :true
			},
                        
			store_id: {
				required: true
			},
			store_name: {
				required: true
			//                validateName: true
			},
			store_format: {
				required: true
			},
			retailers : {
				required: true
			},
			//			building : {
			//				required: true,
			//				validateStreetAddress: true
			//			},
			street_address : {
				required: true,
				validateStreetAddress: true
			},
			zip : {
				//required: true,
				number: true
			},
			city : {
				required: true,
				validateCity: true
			},
			state : {
				required: true
			},
			latitude : {
				required: true
			},
			longitude: {
				required: true
			},
			store_contact_person:{
				validateName: true
			},
			store_contact_tel:{
				validatePhoneNumber: true
			},
			'open_hours[]':{
		//                validateTime: true
		}
		},
		messages: {                        
			store_id: {
				required: "Please enter store id"
			},
			store_name: {
				required:"Please enter store name.",
				validateName: "Store name must contain only letters, apostrophe, spaces or dashes."
			},
			retailers : {
				required: "Please select retailers"
			},
			store_format: {
				required: "Please select the store format"
			},
			//			building : {
			//				required: "Please enter building name"
			//			},
			street_address : {
				required: "Please enter street address"
			},
			zip : {
			//required: "Please enter zip code"
			},
			city : {
				required: "Please enter city"
			},
			state : {
				required: "Please select state"
			},
			latitude : {
				required:  "Please enter latitude"
			},
			longitude: {
				required:  "Please select longitude"
			},
			store_contact_person:{
				validateName: "Contact person name must contain only letters, apostrophe, spaces or dashes."
			}
		},
                errorPlacement: function(error, element) {
                    if ($(element).hasClass("one_required")) {
                        //error.insertAfter($(element).closest("div"));
                        error.insertAfter(".showError");
                    } else {
                        error.insertAfter(element);
                    }
                }
	});
}

function initMap(){
	if( $("#latitude").val() != '' && $("#longitude").val() != '' )
	{
		var myLatlng = new google.maps.LatLng($("#latitude").val(), $("#longitude").val());
		var mapOptions = {
			zoom: 18,
			center: myLatlng
		}
		var map = new google.maps.Map(document.getElementById("map_div"), mapOptions);

		var marker = new google.maps.Marker({
			position: myLatlng,
			map: map,
			draggable: true
		});
		google.maps.event.addListener(marker, 'dragend', function (event) {
			document.getElementById("latitude").value = this.getPosition().lat();
			document.getElementById("longitude").value = this.getPosition().lng();
		});
	}
}
$('body').on('input', '#latitude,#longitude', function(){
	initMap();
});

$('body').on('click', '#add_product_to_new_stores', function(e){
	e.preventDefault();
	e.stopImmediatePropagation();
	var c = confirm('Are you sure to proceed? This will add the catalogue to all the new stores and may take a while to complete.');
	if(c){
		loading();
		$.ajax({
			url: base_url + 'admin/stores/add_catalogue_to_new_stores',
			type: 'POST',
			dataType: 'json',
			success: function(data){
				unloading();
				if(data.result == 1){
					Command: toastr['success'](data.message);
				}
				else{
					Command: toastr['error'](data.message);
				}
			},
			error: function(){
				unloading();
			}
		});
	}
		
});

$("body").on('change', '#region', function(){
	oTable.fnDraw();
});

function setStoreformats()
{
    //alert("Success : setStoreformats");
    var retailerId = $('#retailers').val();
    
    if(retailerId != '' || typeof retailerId != 'undefined'){
       $.ajax({
            url : base_url+'admin/stores/get_store_formats/'+retailerId,
            data : {
                    id: retailerId,
                    type: 'retailers'
            },
            method : 'POST',
            dataType: 'json',
            success : function(data)
            {
                    $("#store_format").html(data.store_formats);
                    $("#store_format_import").html(data.store_formats);
                    $("#store_format").select2();
                    $("#store_format_import").select2();
            }
        }); 
    }
    
}