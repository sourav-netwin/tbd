$(document).ready(function()
{
	validate_email_template_form();
    // Email Template Datatables
    var base_url = $("#base_url").val();
    oTable = $('#email-template-table').dataTable({
        "bProcessing": true,
        "bServerSide": true,
        "sAjaxSource": base_url+'admin/emailtemplate/datatable',
        "bJQueryUI": true,
        "sPaginationType": "full_numbers",
        "iDisplayLength":25,
        "lengthMenu": [ 25, 50, 75, 100 ],"dom": '<"top"f>rt<"bottom row"<"col-md-3"l><"col-md-3"i><"col-md-6"p>>',
        "columns": [
                { data: "Title" },
                { data: "FromEmail" },
                { data : "ToEmail" },
                { data : "EmailTo" },
                { data: "Actions", "bSortable" : false, "aTargets" : [ "no-sort" ] }
            ],
        "oLanguage": {
            "sProcessing": "<img src='../assets/admin/img/ajax-loader_dark.gif'>"
        },
        "dom": '<"top"f>rt<"bottom row"<"col-md-3"l><"col-md-3"i><"col-md-6"p>>',
        'fnServerData': function (sSource, aoData, fnCallback) {
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
            $(nRow).attr('data-id',aData['Id']);
        },
        "fnRowCallback" : function(nRow, aData, iDisplayIndex){
            var html = status_html(aData["active"]);
            $("td:eq(4)", nRow).prepend(html);
        },
        fnDrawCallback: function( oSettings ) {
            $(".pagination li").removeClass("ui-button ui-state-default");
            $(".first.disabled, .previous.disabled, .next.disabled, .last.disabled, .fg-button.active").off( "click" );
            $(".first.disabled a, .previous.disabled a, .next.disabled a, .last.disabled a, .fg-button.active a").attr('href','javascript:void(0);');
        }
    });

    $('table#email-template-table tbody').on('click', '.active', function () {
        var data = $(this).parents('tr').data();

        var status = $(this).data('status');
        if (confirm("Are you sure you want to change the status?")) {
            window.location = "emailtemplate/change_status/" + data['id'] +"/"+status;
        }
    });
	
	$('table#email-template-table tbody').on('click', '.edit', function (e) {
        e.preventDefault();
			var url = $(this).attr('data-href');
			$.ajax({
				url: url,
				data: {},
				success: function(data){
					createModal('edit-email-template-modal', 'Edit Email Template', data,'wd-75'); 
					validate_email_template_form();
					init_tinymce();
				}
			});
    });

    //Validate Stores Form
	function validate_email_template_form(){
    $("#email_template_form").validate({
        ignore: [],
        errorElement: "div",
        rules: {
            email_from : {
                required: true,
                validateEmail: true
            },
            email_to : {
                required: true,
                validateEmail: true
            },
            title : {
                required: true
            },
            content : {
                validate_content: true
            }
        },
        messages: {
            email_from : {
                required: "Please enter from email"
            },
            email_to : {
                required: "Please enter to email"
            },
            title : {
                required: "Please enter title"
            },
            content : {
                validate_content: "Please enter content"
            }
        }
    });
	}
});

$('body').on('submit','.modalCustom #email_template_form', function(e){
	e.preventDefault();
	e.stopImmediatePropagation();
	var elm = $(this);
	var url = elm.attr('action');
	var email_is = $('input[name=email_to]').length;
	loading();
	var data = new FormData(this);
	data.append('email_is', email_is);
	$.ajax({
		url: url,
		type: 'POST',
		dataType: 'json',
		data: data, // Data sent to server, a set of key/value pairs (i.e. form fields and values)
		contentType: false,       // The content type used when sending data to the server.
		cache: false,             // To unable request pages to be cached
		processData:false,
		success: function(data){
			unloading();
			if(data.result == 1){
				location.reload();
			}
			else{
				placeError(data.result,data.message,'email_template_form')
			}
		},
		error: function(){
			unloading();
		}
	});
});