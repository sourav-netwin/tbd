var base_url = '';
$(function(){
	base_url = $('#base_url').val();
	init_icheck();
	validate_notification_form();
	initSelect2();
});

$('body').on('submit', '#notification_form', function(e){
	e.preventDefault();
	e.stopImmediatePropagation();
	var subject = $('#notif_subject').val();
	var content = $('#notif_conent').val();
	var c = confirm('Are you sure to send the notification? This action can\'t redo');
	if(c){
		loading();
		$.ajax({
			url: base_url+'admin/sendnotifications/send_notification',
			type: 'POST',
			dataType: 'json',
			data: $('#notification_form').serialize(),
			success: function(data){
				unloading();
				if(data.result == 1){
					$('#notification_form')[0].reset();
					var html = '<option value="">Select Store Format</option>';
					$('#sel_storetype').html(html);
					$('select').select2();
					init_icheck();
					Command: toastr["success"](data.message);
				}
				else{
					Command: toastr["error"](data.message);
				}
			},
			error: function(){
				Command: toastr["error"]('Something went wrong. Please try again');
				unloading();
			}
		});
	}
});



function validate_notification_form(){
	$("#notification_form").validate({
		ignore: [],
		rules: {
			notif_subject : {
				required: true,
				maxlength: 50
			},
			notif_content : {
				required: true,
				maxlength: 200
			}
		},
		messages: {
			notif_subject : {
				required: "Please enter the subject"
			},
			notif_content : {
				required: "Please enter the content"
			}
		}
	});
}

$('body').on('change','#sel_region',function(e){
	e.preventDefault();
	e.stopImmediatePropagation();
	var elm = $(this);
	var state_id = elm.val();
	if(state_id == '' || typeof state_id == 'undefined'){
		var html = '<option value="">Select Store Format</option>';
		$('#sel_storetype').html(html);
		$('#sel_storetype').select2();
	}
	else{
		loading();
		$.ajax({
			url: base_url+'admin/sendnotifications/get_state_storetype',
			type: 'POST',
			dataType: 'json',
			data: {
				state_id: state_id
			},
			success: function(data){
				unloading();
				if(data.result == 1){
					$('#sel_storetype').html(data.message);
					$('#sel_storetype').select2();
				}
			},
			error: function(){
				unloading();
			}
		});
	}
});