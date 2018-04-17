var base_url = '';
$(function(){
	base_url = $('#base_url').val();
	show_tandc_table();
	validate_tandc_form();
});

function show_tandc_table(){
	if($('#tandc-table').length > 0){
		oTable = $('#tandc-table').dataTable({
			"bProcessing": true,
			"bServerSide": true,
			"sAjaxSource": base_url+'admin/loyaltyterms/datatable/',
			"bJQueryUI": true,
			"sPaginationType": "full_numbers",
			"iDisplayLength":25,
			"lengthMenu": [ 25, 50, 75, 100 ],
			"dom": '<"top"f>rt<"bottom row"<"col-md-3"l><"col-md-3"i><"col-md-6"p>>',
			"columns": [
                        {
				data: "t_id",
                                "bVisible" : false
			},    
			{
				data: "TermsText"
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
				$(nRow).attr('data-id',aData['t_id']);
			},
			"fnRowCallback" : function(nRow, aData, iDisplayIndex){
                                // Replacing \' with single quote
                                var TermsText = aData["TermsText"];
                                var newTermsText = TermsText.replace(/\\'/g, "'");                                
				$("td:eq(0)", nRow).html(newTermsText);
                        
			},
			fnDrawCallback: function( oSettings ) {
				$('div.dataTables_filter input').addClass("form-control");
				$(".pagination li").removeClass("ui-button ui-state-default");
				$(".first.disabled, .previous.disabled, .next.disabled, .last.disabled, .fg-button.active").off( "click" );
				$(".first.disabled a, .previous.disabled a, .next.disabled a, .last.disabled a, .fg-button.active a").attr('href','javascript:void(0);');
			}
		});
	}
}

function validate_tandc_form(){
	if($('#tandc_form').length > 0){
		$("#tandc_form").validate({
			ignore: [],
			rules: {
				terms_text : {
					required: true,
					maxlength: 300,
					minlength: 10
				}
			},
			messages: {
				terms_text : {
					required: "Please enter Terms and Conditions"
				}
			}
		});
	}
		
}

$('table#tandc-table tbody').on('click','.edit', function(e) {
	e.preventDefault();
	var url = $(this).attr('data-href');
	$.ajax({
		url: url,
		type: 'POST',
		dataType: 'json',
		data: {},
		success: function(data){
			createModal('edit-tandc-modal', 'Edit T & C', data.html,'small');
			validate_tandc_form();
		}
	});
			
});

$('body').on('submit','.modalCustom #tandc_form', function(e){
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
                           Command: toastr["success"](data.message);
			   removeModal('edit-tandc-modal');
			   oTable.fnStandingRedraw();  
			  //location.reload();
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

$('table#tandc-table tbody').on('click','.delete', function(e) {
	e.preventDefault();
	e.stopImmediatePropagation();
	var parent = $(this).parent().parent().parent();
	var c = confirm('Are you sure to delete the T & C ?');
	if(c){
		loading();
		var id = parent.attr('data-id');
		$.ajax({
			url: base_url+'admin/loyaltyterms/delete_tandc',
			type: 'POST',
			dataType: 'json',
			data: {
				id: id
			},
			success: function(data){
				unloading();
				if(data.result == 1){
					parent.slideUp('slow');
					Command: toastr["success"](data.message);
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
});

