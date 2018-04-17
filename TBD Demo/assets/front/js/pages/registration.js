$(document).ready(function(){
	var base_url = $("#base_url").val();
	var called_from = '';
	//Validate Registration Form

	if(window.location.hash) {
		var hash = window.location.hash.substring(1); //Puts hash in variable, and removes the # character
		if(hash == 'n'){
			$('#registration-form').toggle();
		}
	}
	
	if(user_det_pg == 'true'){
		check_user_details();
	}

	$("#registration_form").validate({
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
				validatePhoneNumber: true
			},
			mobile_number : {
				required: true,
				validatePhoneNumber: true
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
			house_number : {
				required: true,
				nospecials: true
			},
			street_name :{
				required:true,
				nospecials: true
			},
			suburb:{
				required: true,
				nospecials: true
			},
			city:{
				required:true,
				Onlylettersandspaces: true
			},
			province : {
				required:true,
				Onlylettersandspaces: true
			},
			pin_code: {
				required:true,
				number: true
			},
			terms_conditions: {
				required: true
			}
		},
		messages: {
			first_name : {
				required: "Please enter first name"
			},
			last_name : {
				required: "Please enter last name"
			},
			telephone : {
				required: "Please enter telephone",
				validatePhoneNumber: "Please enter valid telephone number"
			},
			mobile_number : {
				required: "Please enter mobile number",
				validatePhoneNumber: "Please enter valid mobile number"
			},
			email : {
				required: "Please enter an email address"
			},
			password : {
				required: "Please enter password"
			},
			confirm_password : {
				required: "Please enter repeat password",
				equalTo: "Repeat password must be same as password"
			},
			house_number : {
				required: "Please enter house No."
			},
			street_name :{
				required:"Please enter street name"
			},
			suburb:{
				required: "Please enter suburb"
			},
			city:{
				required: "Please enter city"
			},
			province : {
				required: "Please enter province"
			},
			pin_code: {
				required: "Please enter pin code"
			},
			terms_conditions: {
				required: "Please accept terms and conditions"
			}
		},
		errorPlacement: function(error, element) {
			if($(element).is(':checkbox')) {
				error.insertAfter($(element).closest(".checkbox"));
			} else {
				error.insertAfter(element);
			}
		}
	});

	// Facebook Login Code

	// This is called with the results from from FB.getLoginStatus().
	function statusChangeCallback(response) {
		// console.log('statusChangeCallback');
		// console.log(response);
		// The response object is returned with a status field that lets the
		// app know the current login status of the person.
		// Full docs on the response object can be found in the documentation
		// for FB.getLoginStatus().
		if (response.status === 'connected') {
			// Logged into your app and Facebook.
			testAPI();
		} else if (response.status === 'not_authorized') {
			// The person is logged into Facebook, but not your app.
			alert('Please log into this app.');
		} else {
		// The person is not logged into Facebook, so we're not sure if
		// they are logged into this app or not.
		// alert('Please log into Facebook.');
		}
	}

	// This function is called when someone finishes with the Login
	// Button.  See the onlogin handler attached to it in the sample
	// code below.
	function checkLoginState() {
		FB.getLoginStatus(function(response) {
			statusChangeCallback(response);
		});
	}

	window.fbAsyncInit = function() {
		FB.init({
			appId      : '176514089453791', 
			xfbml      : true,
			version    : 'v2.7'
		});

		// Now that we've initialized the JavaScript SDK, we call
		// FB.getLoginStatus().  This function gets the state of the
		// person visiting this page and can return one of three states to
		// the callback you provide.  They can be:
		//
		// 1. Logged into your app ('connected')
		// 2. Logged into Facebook, but not your app ('not_authorized')
		// 3. Not logged into Facebook and can't tell if they are logged into
		//    your app or not.
		//
		// These three cases are handled in the callback function.

		FB.getLoginStatus(function(response) {
			$.ajax({
				url: $("#base_url").val()+'registration/check_is_logged_in',
				type: 'POST',
				dataType: 'json',
				data: {},
				success: function(data){
					if(data.success == '1'){
						statusChangeCallback(response); 
					}
					else{
						fbLogoutUser();
					}
				}
			});
            
		});
	};
	
	function fbLogoutUser() {
		FB.getLoginStatus(function(response) {
			if (response && response.status === 'connected') {
				FB.logout(function(response) {
					document.location.reload();
				});
			}
		});
	}

	// Load the SDK asynchronously
	(function(d, s, id){
		var js, fjs = d.getElementsByTagName(s)[0];
		if (d.getElementById(id)) {
			return;
		}
		js = d.createElement(s);
		js.id = id;
		js.src = "//connect.facebook.net/en_US/sdk.js";
		fjs.parentNode.insertBefore(js, fjs);
	}(document, 'script', 'facebook-jssdk'));

	// Here we run a very simple test of the Graph API after login is
	// successful.  See statusChangeCallback() for when this call is made.
	function testAPI() {
		// console.log('Welcome!  Fetching your information.... ');
		FB.api('/me?fields=id,first_name,last_name,email,picture,location,hometown', function(response) {
			$.ajax({
				url : $("#base_url").val()+'registration/register',
				data : {
					fb_response: response,
					called_from : called_from
				},
				method : 'POST',
				dataType: 'JSON',
				success : function(data)
				{
					if( data.success != '' ){
						var fullUrl = $(location).attr('href');
						if(fullUrl != data.success){
							window.location.href = data.success;
						}
						
					}
                        
					else if ( data.error != '' )
						Command: toastr["error"](data.error);
				}
			});
		});
	}

	// Code to trigger facebook login on click of the facebook button when we want to have our custom button and not the default facebook button
	$(".login-fb").on("click", function () {
		called_from = $(this).attr('data-called-from');
		FB.login(function(response) {
			if (response.authResponse) {
				checkLoginState();
			}
		},{
			scope: 'user_location,user_hometown,email,user_likes'
		});
	});

	// Code to trigger twitter login on click of the twitter button
	$(".login-twitter").on("click", function () {
		called_from = $(this).attr('data-called-from');
		window.location.href = base_url+'registration/twitter_redirect/'+called_from;
	});

//Show registration form

	


//Select nearest store

	

	
	
});

$(document).on("click","#registration-email", function(){
	if($('#registration-form').hasClass('hide')){
		$('#registration-form').removeClass('hide');
	}
	else{
		$('#registration-form').addClass('hide');
	}
});
$(document).on("click",".social-registration", function(){
	if(!$('#registration-form').hasClass('hide')){
		$('#registration-form').addClass('hide');
	}
});

$(document).on("click","#pref_retailer_select a", function(){

	$("#pref_retailer_select_btn").html( $(this).html()+'<span class="caret"></span>' );
	var retailer_id = $(this).attr("data-retailer-id");
	$("#pref_retailer_select_btn").attr("data-retailer-id", retailer_id);
	$("#pref_retailers").val(retailer_id);
	$.LoadingOverlay("show");

	$.ajax({
		url : $("#base_url").val()+'registration/get_stores/'+ retailer_id,
		method : 'POST',
		success : function(data)
		{
			$.LoadingOverlay("hide");
			$("#pref_store_select .dropdown-menu").html(data);

		}
	});
});

//Select nearest store
$(document).on("click","#pref_store_select a", function(){

	$("#pref_store_select_btn").html( $(this).html()+'<span class="caret"></span>' );
	var store_id = $(this).attr("data-store-id");

	$("#pref_store_select_btn").attr("data-store-id", store_id);
	$("#pref_stores").val(store_id);

});

function validate_preference_form(){
	$("#preference_form").validate({
		ignore: [],
		rules: {
			pref_retailers : {
				required: true
			},
			pref_stores : {
				required: true
			}
		},
		messages: {
			pref_retailers : {
				required: "Please select retailer"
			},
			pref_stores : {
				required: "Please select store"
			}
		}

	});
}

function validate_set_email_form(){
	$("#set_email_form").validate({
		ignore: [],
		rules: {
			email : {
				required: true,
				email: true
			}
		},
		messages: {
			email : {
				required: "Please enter Email Id"
			}
		}

	});
}
function validate_set_location_form(){
	$("#set_location_form").validate({
		ignore: [],
		rules: {
			us_latitude : {
				required: true,
				number: true
			},
			us_longitude : {
				required: true,
				number: true
			}
		},
		messages: {
			us_latitude : {
				required: 'Please enter latitude'
			},
			us_longitude : {
				required: 'Please enter longitude'
			}
		}

	});
}


function check_user_details(){
	$.LoadingOverlay("show");
	$('#user_details_fill').html('');
	$.ajax({
		url: base_url+'registration/check_details',
		method: 'POST',
		dataType: 'json',
		data: {},
		success: function(data){
			$.LoadingOverlay("hide");
			if(data.message == 'success'){
				window.location.href = base_url+'topoffers';
			}
			else if(data.message != ''){
				$('#user_details_fill').html(data.message);
				if(data.page == 'preference'){
					$('body').attr('style','background:url(\''+base_url+'assets/images/Shopping.jpg\') no-repeat scroll 0% 0% / 1366px 726px');
					$('.social.front-pg').hide();
				}
				else{
					$('body').removeAttr('style');
					$('.social.front-pg').show();
				}
				validate_set_email_form();
				validate_set_location_form();
				validate_preference_form();
				init_location_pick();
				init_slider();
			}
			else{
				window.location.href = base_url;
			}
		},
		error: function(){
			window.location.href = base_url;
		}
	});
}

function init_location_pick(){
	if($('#location-div').length > 0){
		$('#location-div').locationpicker({
			location: {
				latitude: -29.85590820250414,
				longitude: 31.0203857421875
			},
			radius: 0,
			inputBinding: {
				latitudeInput: $('#us_latitude'),
				longitudeInput: $('#us_longitude'),
				radiusInput: $('#us_radius_hd'),
				locationNameInput: $('#us_address')
			},
			enableAutocomplete: true
		});
	}
}
var set = 0;
function init_slider(){
	if($('#slider').length > 0){
		var range = document.getElementById('slider');

		range.style.width = '600px';
		range.style.margin = '0 auto 30px';

		noUiSlider.create(range, {
			start: [ 0 ], // 4 handles, starting at...
			//margin: 300, // Handles must be at least 300 apart
			//limit: 600, // ... but no more than 600
			connect: true, // Display a colored bar between the handles
			direction: 'ltr', // Put '0' at the bottom of the slider
			orientation: 'horizontal', // Orient the slider vertically
			behaviour: 'tap-drag', // Move handle on tap, bar is draggable
			step: 1,
			tooltips: true,
			range: {
				'min': 0,
				'max': 100
			},
			format: wNumb({
				decimals: 0,
				//thousand: '.',
				postfix: 'KM'
			})
		/*,
	pips: { // Show a scale with the slider
						mode: 'steps',
						stepped: true,
						density: 4
					}*/
		});
		
		
		marginSlider = document.getElementById('slider');
		var mapDist = document.getElementById('us_radius_hd');
		marginSlider.noUiSlider.on('update', function ( values, handle ) {
			if(set != 0){
				$('#us_radius_hd').val(((values[handle].split('KM')[0])*1000));
				$('#location-div').locationpicker({
					radius: ((values[handle].split('KM')[0])*1000),
					location: {
						latitude: $('#us_latitude').val(),
						longitude: $('#us_longitude').val()
					}
				});
			}
			set = 1;
		});
		
			
	}
}

$('body').on('submit', '#set_email_form', function(e){
	e.preventDefault();
	$.LoadingOverlay("show");
	$.ajax({
		url: base_url+'registration/set_email',
		type: 'POST',
		dataType: 'json',
		data: {
			email: $('#email').val()
		},
		success: function(data){
			$.LoadingOverlay("hide");
			if(data.result == 1){
				Command: toastr["success"](data.message);
			check_user_details();
			}
			else{
				Command: toastr["error"](data.message);
			}
		},
		error: function(){
		//window.location.href = base_url;
		}
	});
});

$('body').on('submit', '#set_location_form', function(e){
	e.preventDefault();
	$.LoadingOverlay("show");
	$.ajax({
		url: base_url+'registration/set_preference',
		type: 'POST',
		dataType: 'json',
		data: $('#set_location_form').serialize(),
		success: function(data){
			$.LoadingOverlay("hide");
				Command: toastr["success"]('Location preferences updated successfully');
			$('#user_details_fill').html(data.message);
		},
		error: function(){
		//window.location.href = base_url;
		}
	});
});

$('body').on('submit', '#preference_form', function(e){
	var ret = $('#pref_retailers').val();
	var str = $('#pref_stores').val();
	if(ret == '' || typeof ret == 'undefined'){
		Command: toastr["error"]('Please select a retialer');
	e.preventDefault();
		return false;
	}
	else if(ret == '' || typeof ret == 'undefined'){
		Command: toastr["error"]('Please select a store');
	e.preventDefault();
		return false;
	}
	else{
		$('#preference_form').unbind().submit();
	}
});