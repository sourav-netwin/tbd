//Validate Stores Form
function validate_content_form(){
	$("#content_form").validate({
		ignore: [],
		rules: {
			browser_title : {
				required: true
			},
			page_title : {
				required: true
			},
			meta_description : {
				required: true
			},
			keywords : {
				required: true
			},
			content:{
				validate_content: true
			}
		},
		messages: {
			browser_title : {
				required: "Please enter browser title"
			},
			page_title : {
				required: "Please enter page title"
			},
			meta_description : {
				required: "Please enter meta description"
			},
			keywords : {
				required: "Please enter keywords"
			},
			content:{
				validate_content: "Please enter content"
			}
		}
	});
}


$('body').on('click','.edit', function(e) {
	e.preventDefault();
	var url = $(this).attr('data-href');
	$.ajax({
		url: url,
		data: {},
		success: function(data){
			createModal('edit-content-modal', 'Edit Content', data);
			validate_content_form();
			init_tinymce();
		}
	});
			
});
		
$('body').on('submit','.modalCustom #content_form', function(e){
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
				placeError(data.result,data.message,'content_form')
			}
		},
		error: function(){
			unloading();
		}
	});
});