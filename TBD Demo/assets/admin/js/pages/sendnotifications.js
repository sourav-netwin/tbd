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


/*
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
*/

    $('body').on('change','#sel_region',function(e){
            e.preventDefault();
            e.stopImmediatePropagation();
            var elm = $(this);
            var state_id = elm.val();
            if(state_id == '' || typeof state_id == 'undefined'){
                    var html = '<option value="">Select Retailer</option>';
                    $('#sel_retailer').html(html);
                    $('#sel_retailer').select2();
            }
            else{
                    loading();
                    $.ajax({
                            url: base_url+'admin/sendnotifications/get_state_retailers',
                            type: 'POST',
                            dataType: 'json',
                            data: {
                                    state_id: state_id
                            },
                            success: function(data){
                                    unloading();
                                    if(data.result == 1){
                                        $('#sel_retailer').html(data.message);
                                        $('#sel_retailer').select2();

                                        // Get users according to filter criteria
                                        getUsers();
                                    }
                            },
                            error: function(){
                                    unloading();
                            }
                    });
            }
    });


    $('body').on('change','#sel_retailer',function(e){
            e.preventDefault();
            e.stopImmediatePropagation();
            var elm = $(this);
            var retailer_id = elm.val();
            if(retailer_id == '' || typeof retailer_id == 'undefined'){
                    var html = '<option value="">Select Store Format</option>';
                    $('#sel_storetype').html(html);
                    $('#sel_storetype').select2();
            }
            else{
                    loading();
                    $.ajax({
                            url: base_url+'admin/sendnotifications/get_retailer_storetype',
                            type: 'POST',
                            dataType: 'json',
                            data: {
                                    retailer_id: retailer_id
                            },
                            success: function(data){
                                    unloading();
                                    if(data.result == 1){
                                            $('#sel_storetype').html(data.message);
                                            $('#sel_storetype').select2();

                                        // Get users according to filter criteria
                                        getUsers();
                                    }
                            },
                            error: function(){
                                    unloading();
                            }
                    });
            }
    });


    $('body').on('change','#sel_storetype',function(e){
        // Get users according to filter criteria
        getUsers();
    });
    
    // Call getUsers on change of 
    $('input').on('ifChecked', function(event){
        $(this).closest("input").attr('checked', true); 
        getUsers();
    });
  
     $('input').on('ifUnchecked', function(event){
        $(this).closest("input").attr('checked', false); 
        getUsers();
    });

    /* Function to get users based on filter criteria */
    function getUsers()
    {
        var isMale    = $('#notif_male').prop("checked") == true ? 1 : 0;
        var isFemale  = $('#notif_female').prop("checked") == true ? 1 : 0;
        var isAndroid = $('#notif_android').prop("checked")== true ? 1 : 0;
        var isIphone  = $('#notif_iphone').prop("checked")== true ? 1 : 0;
        
        //alert("notif_male : " + notif_male + " , notif_female :"+ notif_female + ", notif_android : " +notif_android + ", notif_iphone : "+notif_iphone);
        
        var region_id    = $('#sel_region').val();
        var retailer_id  = $('#sel_retailer').val();
        var storetype_id = $('#sel_storetype').val();

        if(region_id > 0 || retailer_id > 0 || storetype_id > 0 || isMale > 0 || isFemale > 0 || isAndroid > 0 || isIphone > 0)
        {
            loading();
            $.ajax({
                    url: base_url+'admin/sendnotifications/get_users',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        region_id : region_id,
                        retailer_id: retailer_id,
                        storetype_id:storetype_id,
                        isMale : isMale,
                        isFemale: isFemale,
                        isAndroid:isAndroid,                            
                        isIphone:isIphone
                    },
                    success: function(data){
                        unloading();                        
                        if(data.result == 1){
                            $('#sel_user').html(data.message);
                            $('#sel_user').select2();
                        }
                    },
                    error: function(){
                        unloading();
                    }
            });
        }
    }