$('.carousel').carousel({
	pause: "false"
});
var base_url = '';
$(document).ready(function(e) {
	
	validate_email_help_form();
	
	
	//	$("#popover-link").webuiPopover({
	//		url: '#pop-up-web-cust',
	//		closeable: true,
	//		animation: 'pop',
	//		placement: 'left-bottom',
	//		width: 600,
	//		height: 'auto'
	//	});
	//filter collapsable menu
	base_url = $("#base_url").val();
	$(".goo-collapsible > li > a").on("click", function(e){
		if($(this).hasClass("active")) {
			$(this).next("ul").slideUp(350);
			$(this).removeClass("active");
		}else{
			$(this).next("ul").slideDown(350);
			$(this).addClass("active");
		}
	});
	
	var ratio = 1/1;
	if($('#aspect_ratio').length) {
		ratio = $('#aspect_ratio').val();
	}
	//Image cropper
	var $image = $(".image-crop > img")
	$($image).cropper({
		aspectRatio: ratio,
		zoomable: false,
		rotatable: false
	});
	$('body').on('click',"#crop-button",function () {
		$("#imageModal").modal("hide");
	});

	$("#imageModal").on('hidden.bs.modal', function () {

		var  getData = $image.cropper('getData');

		$('#image-x').val(getData['x']);
		$('#image-y').val(getData['y']);
		$('#image-width').val(getData['width']);
		$('#image-height').val(getData['height']);

		$(".profile_image_group").find('.btn-primary').css("background-image", "url("+$image.cropper("getDataURL")+")");
		
		$('body').removeClass('modal-open');


	});

	//Block for Image preview

	$('body').on("change","#inputImage", function()
	{
		var files = !!this.files ? this.files : [];

		if (!files.length || !window.FileReader) return; // no file selected, or no FileReader support

		if (/^image/.test( files[0].type)){ // only image file

			var reader = new FileReader(); // instance of the FileReader
			reader.readAsDataURL(files[0]); // read the local file

			reader.onloadend = function(){ // set image data as background of div
				$("#imageModal").modal("show");
				$image.cropper("reset", true).cropper("replace", this.result);
			}
		}
	});

	$(".goo-collapsible > .dropdown > a").addClass("active");
	$(".goo-collapsible > .dropdown > ul").show();

	(function () {
		function parallaxInit() {
			$("#phone_wrap").parallax("1%", 0.3);
		}
		parallaxInit();
	}());

	// Custom validations for name field
	$.validator.addMethod("validateName", function(value, element) {
		return this.optional(element) || /^[a-z'\-\s]+$/i.test(value);
	},"This field cannot contain numbers and special characters");

	// Custom validations for field that can have only letters and spaces
	$.validator.addMethod("Onlylettersandspaces", function(value, element) {
		return this.optional(element) || /^[a-z\s]+$/i.test(value);
	},"This field can contain only letters and spaces");

	// Custom validations for field that can have no special characters ( Only numbers, alphabets, spaces, quotes )
	$.validator.addMethod("nospecials", function(value, element) {
		return this.optional(element) || /^[a-z0-9'",\\\/\-\s]+$/i.test(value);
	},"This field can not contain special characters");

	// Custom validations for phone number
	$.validator.addMethod("validatePhoneNumber", function(value, element) {
		if(element.value !='')
		{
			var filter = /^[0-9-+()\s]+$/;

			if (filter.test(element.value))
				return true;
			else
				return false;
		}
		return true;
	});

	// Custom validations for email
	$.validator.addMethod("validateEmail", function(value, element) {
		if(element.value !='')
		{
			var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
			if (regex.test(element.value))
				return true;
			else
				return false;
		}
		return true;
	}, "Please enter a valid email address.");

	// Display toastr messages
	var success_message = $("#success_message").text();
	var error_message = $("#error_message").text();

	toastr.options = {
		//"progressBar": true,
		"positionClass": "toast-top-center",
		"showDuration": "300",
		"hideDuration": "1000",
		"timeOut": "5000",
		"extendedTimeOut": "1000",
		"showEasing": "swing",
		//"hideEasing": "linear",
		"showMethod": "fadeIn",
		"hideMethod": "fadeOut"
	}

	if( success_message != '' )
		Command: toastr["success"](success_message)
	else if( error_message != '' )
		Command: toastr["error"](error_message)

	
	

	//    // tabs on profile page
	//    $('#myTab a').click(function (e) {
	//        e.preventDefault();
	//        $('a[href="' + $(this).attr('href') + '"]').tab('show');
	//    });


	// Facebook Login Code

	

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

	


});

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
	
// Here we run a very simple test of the Graph API after login is
// successful.  See statusChangeCallback() for when this call is made.
function testAPI() {
	removeModal('sign-in-modal');
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
			},
			error: function(){
				$.LoadingOverlay("hide");
			}
		});
	});
}
function fbLogoutUser() {
	FB.getLoginStatus(function(response) {
		if (response && response.status === 'connected') {
			FB.logout(function(response) {
				document.location.reload();
			});
		}
	});
}
$(window).load(function() {
	// Display retailers on home page with a slider
	$("#home_retailers").flexisel({
		visibleItems: 6,
		animationSpeed: 600,
		autoPlay: true,
		autoPlaySpeed: 3000,
		pauseOnHover: true,
		clone:false,
		enableResponsiveBreakpoints: true,
		responsiveBreakpoints: {
			mobilexs: {
				changePoint:430,
				visibleItems: 1
			},
			mobile: {
				changePoint:630,
				visibleItems: 2
			},
			portrait: {
				changePoint:768,
				visibleItems: 3
			},
			landscape: {
				changePoint:1040,
				visibleItems: 2
			},
			tablet: {
				changePoint:1200,
				visibleItems: 3
			}
		}
	});

	if($('#search_text').length && $('#search_text').val().length) {
		$('#search_text').show();

	}

	//Search Button on header
	$("#search_button").on("click", function()
	{
		if($('#search_text').length && $('#search_text').val().length) {
			$('#search_text').show();

			$("#search_form").submit();
		} else {
			$('#search_text').toggle();
		}
	});


	//Quick Search

	$(document).on('click',".quick_search_button", function (e) {
		if($("#quick_search_text").length && $("#quick_search_text").val().length) {
			$("#quick_search_form").submit();
			return true;
		} else {
			return false;
		}

	});
});

function createModal(id, title, message,size){
	if(id == '' || typeof id == 'undefined'){
		id = 'myModal';
	}
	var html = '<div class="modal modalCustom " id="'+id+'" role="dialog" aria-labelledby="myModalLabel">\n\
					<div class="modal-dialog '+size+'" role="document">\n\
						<div class="modal-content" style="">\n\
							<div class="modal-header">\n\
								<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button> \n\
								<h4 class="modal-title" id="myModalLabel">'+title+'</h4>\n\
							</div>\n\
							<div class="modal-body" style="display: inline"> '+message+'\n\
							</div>\n\
						</div>\n\
					</div>\n\
				</div>';
	$('body').append(html);
	$('#'+id).modal({
		backdrop: 'static',
		keyboard: true, 
		show: true
	});
}

$('body').on('click', '.modalCustom [aria-label=Close]', function(e){
	e.preventDefault();
	var parent = $(this).parent().parent().parent().parent();
	parent.fadeOut('slow', function(){
		parent.remove();
		if($('.modalCustom').length > 0){
			$('body').addClass('modal-open');
		}
		$('.modal-backdrop').first().remove();
	});
	
});

function removeModal(id){
	$('#'+id).fadeOut('slow', function(){
		$('#'+id).remove();
		$('.modal-backdrop').first().remove();
		if($('.modalCustom').length > 0){
			$('body').addClass('modal-open');
		}
	});
}
$(".modal").on('hidden.bs.modal', function () {

	if($('.modal').length > 0){
		$('body').addClass('modal-open');
	}


});

function validate_login_form(){
	$("#login_form").validate({
		ignore: [],
		rules: {
			email : {
				required: true,
				email: true
			},
			password : {
				required: true
			}
		},
		messages: {
			email : {
				required: "Please enter Email Id"
			},
			password : {
				required: "Please enter Password"
			}
		}

	});
}

$('body').on('click','#user_login',function(){
	signInModal();
	validate_login_form();
}); 

function signInModal(){
	var instPath = $('#instaPath').val();
	var un = $('#uval1').val();
	var up = $('#uval2').val();
	var rm = $('#uval3').val();
	var html = '<div class="modal modalCustom modal-login" id="sign-in-modal" role="dialog" aria-labelledby="myModalLabel">\n\
                <div class="modal-dialog modal-signin wd-38-per" role="document">\n\
                    <div class="modal-content" style="">\n\
                        <div class="modal-body" style="display: inline">\n\
                            <button type="button" class="close mr-rt-5" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>\n\
                            <form id="login_form" class="custom_form_login bg-cl-site mr-sd-20" action="'+$('#base_url').val()+'login/signup" method="POST">\n\
                                <div style="display:none">\n\
									<input type="hidden" name="csrf_tbd_token" value="'+tbd_csrf+'">\n\
								</div>\n\
								<div class="mr-si-cst">\n\
                                    <div class="row login-title">\n\
                                    SIGN IN\n\
									</div>\n\
									<div class="row">\n\
                                        <div class="col-xs-4 mr-tp-4-per" style="padding-left: 30px;">\n\
                                            <img src="'+$('#base_url').val()+'assets/front/img/TBD-logo-medium.png" class="img-responsive" alt="the best deal">\n\
                                        </div>\n\
                                        <div class="col-xs-8">\n\
                                            <div class="form-group">\n\
                                                <label></label>\n\
                                                <input type="email" class="form-control" id="email" name="email" placeholder="Email" value="'+un+'" />\n\
                                                <label></label>\n\
                                                <div class="row">\n\
													<div class="col-xs-7">\n\
														<input type="password" class="form-control" id="password" name="password" placeholder="Password" value="'+up+'" />\n\
													</div>\n\
													<div class="col-xs-5" style="padding-top: 2px">\n\
														<input type="submit" class="btn btn-login btn-xs pull-right" value="Sign in">\n\
													</div>\n\
												</div>\n\
											</div>\n\
                                            <div class="row">\n\
												<div class="form-group col-xs-6">\n\
													<div class="checkbox">\n\
														<label>\n\
															<input type="checkbox" '+rm+' name="remember" id="remember">  <span class="font-md">Remember me</span>\n\
														</label>\n\
													</div>\n\
												</div>\n\
												<div class="form-group col-xs-6 text-right">\n\
													<a href="javascript:void(0);" data-toggle="modal" data-target="#forgot_password_modal" class="font-md">Forgot Password?</a>\n\
												</div>\n\
											</div>\n\
											<div class="row">\n\
												<div class="col-xs-12 pd-bt-10">\n\
													<span class="font-small">Also sign in with</span>\n\
													<ul class="social_small_sign with-image">\n\
														<li><a href="javascript:void(0)" id="login_fb" data-called-from="login" style="background:url(\''+base_url+'assets/front/img/social/facebook_200.png\')no-repeat scroll 0% 0% / 32px 32px;"></a></li>\n\
														<li><a href="javascript:void(0)" id="login_tw" style="background:url(\''+base_url+'assets/front/img/social/twitter_200.png\')no-repeat scroll 0% 0% / 32px 32px;"></a></li>\n\
														<li><a href="'+instPath+'" class="social-registration"  title="Instagram Login" style="background:url(\''+base_url+'assets/front/img/social/instagram_200.png\')no-repeat scroll 0% 0% / 32px 32px;"></a></li>\n\
													</ul>\n\
												</div>\n\
											</div>\n\
                                        </div>\n\
                                    </div>\n\
                                </div>\n\
                            </form>\n\
                        </div>\n\
                    </div>\n\
                </div>\n\
            </div>';
	
	$('body').append(html);
	$('#sign-in-modal').modal({
		backdrop: 'static',
		keyboard: true, 
		show: true
	});
}


$("body").on("click","#login_fb", function () {
	called_from = $(this).attr('data-called-from');
	FB.login(function(response) {
		$.LoadingOverlay("show");
		if (response.authResponse) {
			checkLoginState();
		}
		else{
			$.LoadingOverlay("hide");
		}
	});
});

// Code to trigger twitter login on click of the twitter button
$('body').on("click","#login_tw", function () {
	called_from = $(this).attr('data-called-from');
	window.location.href = base_url+'registration/twitter_redirect/login';
});
$('body').on("click","#login_is", function () {
	var url = $('#instaPath').val();
	window.location.href = url;
});

$.ajaxSetup({
	data: {
		csrf_tbd_token: tbd_csrf
	}
});

$('body').on('click', '#retailer_login', function(){
	$.ajax({
		url: $("#base_url").val()+'webservices/users/update_fb_email',
		type: 'POST',
		dataType: 'json',
		data: {
			api_key: "7e53784e66dd87004a1d0e76ff195011",
			fb_uid: '1262039103819931',
			email: 'aliah.test@gmail.com'
		},
		success: function(data){
			console.log(data);
		}
	});
});

$('body').on('click', '#saving-btn', function(e){
	e.preventDefault();
	e.stopImmediatePropagation();
	$.ajax({
		url: $("#base_url").val()+'home/check_login',
		type: 'POST',
		data: {},
		success: function(data){
			if(data == '1'){
				window.location.href = $("#base_url").val()+'topoffers';
			}
			else{
				$('#user_login').click();
			}
		}
	});
});

$('body').on('keyup','#quick-list-search',function(){
	var elm = $(this);
	var search_val = elm.val();
	if(search_val != '' && typeof search_val != 'undefined'){
		search_val = escape(search_val);
		$.ajax({
			url: base_url+'productslist/search_quick',
			type: 'POST',
			dataType: 'json',
			data: {
				search_text: search_val
			},
			success: function(data){
				if(data.result == 1){
					$('#quick-auto-list').html(data.message);
					$('#quick-auto-list').show();
				}
				else{
					$('#quick-auto-list').html('');
					$('#quick-auto-list').hide();
				}
			},
			error: function(){
				$('#quick-auto-list').html('');
				$('#quick-auto-list').hide();	
			}
		});
	}
	else{
		$('#quick-auto-list').html('');
		$('#quick-auto-list').hide();
	}
});
$('body').on('click','#quick-auto-list ul li', function(e){
	e.preventDefault();
	e.stopImmediatePropagation();
	var elm = $(this);
	var pr_name = elm.html().replace(',','|||');
	var pr_id = elm.attr('data-pi');
	var pr_ri = elm.attr('data-ri');
	var pr_sti = elm.attr('data-sti');
	var pr_si = elm.attr('data-si');
	var pr_pr = elm.attr('data-pr');
	var pr_sp = elm.attr('data-sp');
	var sp_class = '';
	if(pr_sp == '1'){
		sp_class = 'text-danger';
	}
	var pr_count = 0;
	$('.search_quick_area tr').each(function(){
		var tr_elm = $(this);
		if(tr_elm.attr('data-pi') == pr_id){
			pr_count++;
		}
	});
	if(pr_count == 0){
		$.ajax({
			url: base_url+'productslist/add_single_quick',
			type: 'POST',
			dataType: 'json',
			async:false,
			data: {
				item: pr_name+':::'+pr_id+':::'+pr_ri+':::'+pr_sti+':::'+pr_si+':::1:::0'
			},
			success: function(data){
				if(data.result == 1){
					var html = '<tr data-pi="'+pr_id+'" data-ri="'+pr_ri+'" data-sti="'+pr_sti+'" data-si="'+pr_si+'"><td>'+pr_name.replace('|||',',')+'</td><td><input type="text" value="1" class="quick_count click_sel" /></td><td class="number-font '+sp_class+' quick_price">'+pr_pr+'</td><td><a href="javascript:void(0)" class="quick-remove"><i class="fa fa-close"></i></a></td></tr>';
					$('.search_quick_area table').append(html);
					update_total_quick();
				}
			},
			error: function(){
				
			}
		});
		
	}
	$('#quick-auto-list').html('');
	$('#quick-auto-list').hide();
	$('#quick-list-search').val('');
});

$('body').on('click','.quick-remove',function(e){
	e.preventDefault();
	e.stopImmediatePropagation();
	var elm = $(this);
	var parent = elm.parent().parent();
	var pr_id = parent.attr('data-pi');
	var pr_ri = parent.attr('data-ri');
	var pr_sti = parent.attr('data-sti');
	var pr_si = parent.attr('data-si');
	var pr_name = elm.parent().siblings(":first").html();
	var full_str = pr_name+':::'+pr_id+':::'+pr_ri+':::'+pr_sti+':::'+pr_si;
	$.ajax({
		url: base_url+'productslist/remove_quick',
		type: 'POST',
		dataType: 'json',
		data: {
			full_str: full_str
		},
		success: function(data){
			if(data.result == 1){
				parent.remove();
				update_total_quick();
			//Command: toastr["success"](data.message);
			}
			else{
				Command: toastr["error"](data.message);
			}
		},
		error: function(){
		}
	});
});
$('body').on('change','.quick_count',function(){
	var elm = $(this);
	var count = elm.val();
	var parent  = elm.parent().parent();
	var pro_id = parent.attr('data-pi');
	if(isNaN(count)){
		elm.val(1);
	}
	else if(count <= 0){
		elm.val(1);
	}
	else if(count > 10){
		elm.val(10);
	}
	count = elm.val();
	$.ajax({
		url: base_url+'productslist/get_quick_price_one',
		type: 'POST',
		dataType: 'json',
		data: {
			pro_id: pro_id,
			count: count
		},
		success: function(data){
			if(data.result == 1){
				parent.find('.quick_price').html(data.message);
				update_total_quick();
			}
		},
		error: function(){
			
		}
	});
});

function update_total_quick(){
	var total = 0;
	$('.quick_price').each(function(){
		var elm = $(this);
		var price = elm.html() * 1;
		if(!isNaN(price)){
			total += price;
		}
	});
	$('#quick_tot_price').html(total.toFixed(2));
}

$('body').on('click','#popover-link', function(){
	$('#real-web-mail').fadeToggle('slow', 'swing');
});



function validate_email_help_form(){
	if($('#email_help_form').length > 0){
		$.validator.setDefaults({
			errorPlacement: function (error, element) {
				if (element.parent('.input-group').length) {
					error.insertAfter(element.parent());
				} else {
					error.insertAfter(element);
				}
			}
		});
		$("#email_help_form").validate({
			ignore: [],
			rules: {
				user_name : {
					required: true
				},
				user_email : {
					required: true,
					email: true
				},
				email_subject : {
					required: true
				},
				email_body : {
					required: true
				}
			},
			messages: {
				user_name : {
					required: "Please enter User Name"
				},
				user_email : {
					required: "Please enter Email Id"
				},
				email_subject : {
					required: "Please enter Subject"
				},
				email_body : {
					required: "Please enter Email Content"
				}
			}

		});
	}
}
$('body').on('submit','#email_help_form', function(e){
	e.preventDefault();
	e.stopImmediatePropagation();
	$('#email-submit-btn').prop('disabled', true);
	$.ajax({
		url: base_url+'home/send_help_email',
		type: 'POST',
		dataType: 'json',
		data: $('#email_help_form').serialize(),
		success: function(data){
			$('#email-submit-btn').prop('disabled', false);
			if(data.result == 1){
				$('#real-web-mail').fadeToggle('slow', 'swing',function(){
					$('#email_help_form')[0].reset();
				});
					Command: toastr["success"](data.message);
			}
			else{
				Command: toastr["error"](data.message);
			}
		},
		error: function(){
			$('#email-submit-btn').prop('disabled', false);
		}
	});
});
$('body').on('input','#basket-pro-count',function(){
	var value = parseInt($(this).val());
	var elm = $(this);
	if(value != ''){
		if(isNaN(value)){
			elm.val('1');
		}
		else if(value <= 0){
			elm.val('1');
		}
		else if(value > 999){
			elm.val('999');
		}
		else{
			elm.val(Math.floor(value));
		}
	}
		
});

$('body').on('click','#minus-cart',function(e){
	e.preventDefault();
	e.stopImmediatePropagation();
	var elm = $(this);
	var count_elm = $('#basket-pro-count');
	var count = parseInt(count_elm.val());
	count -= 1;
	count_elm.val(count);
	if(isNaN(count)){
		count_elm.val('1');
	}
	else if(count <= 0){
		count_elm.val('1');
	}
	else if(count > 999){
		count_elm.val('999');
	}
});
$('body').on('click','#plus-cart',function(e){
	e.preventDefault();
	e.stopImmediatePropagation();
	var elm = $(this);
	var count_elm = $('#basket-pro-count');
	var count = parseInt(count_elm.val());
	count += 1;
	count_elm.val(count);
	if(isNaN(count)){
		count_elm.val('1');
	}
	else if(count <= 0){
		count_elm.val('1');
	}
	else if(count > 999){
		count_elm.val('999');
	}
});

// Add to basket button click
$(document).on('click', '#add_to_basket', function(e){        
	$('.pro-bs-cnt').fadeToggle('slow','swing');
});
$(document).on('click', '#bskt-cnt-sub input', function(e){
	var product = $('#add_to_basket').attr('data-product');
        var special_id = $('#add_to_basket').attr('data-special-id');
        
        //alert("Success : special_id :" + special_id);
        //return false;
        
	var count = $('#basket-pro-count').val();
	if(count == ''){
		count = 1;
		$('#basket-pro-count').val('1');
	}
	$.LoadingOverlay("show");
	$.ajax({
		url : $("#base_url").val()+'productdetails/add_to_basket',
		data : {
                        special_id : special_id,
			product: product,
			count: count
		},
		method : 'POST',
		dataType: 'JSON',
		success : function(data)
		{
			$.LoadingOverlay("hide");	
			if( data.success != '' )
			{
				$('.pro-bs-cnt').fadeToggle('slow', 'swing');
				$('#basket-pro-count').val('1');
				Command: toastr["success"](data.success);
				location.reload();
			}
			else if( data.error != '' )
			{
				Command: toastr["error"](data.error);
			}
		},
		error: function(){
			$.LoadingOverlay("hide");	
		}
	});
});

$(document).on('click', '.add_to_basket', function(e){
        alert(" Now in another fn");
        
	var product = $(this).attr('data-product');
	var count = 1;
	if(count == ''){
		count = 1;
	}
	$.LoadingOverlay("show");
	$.ajax({
		url : $("#base_url").val()+'productdetails/add_to_basket',
		data : {
			product: product,
			count: count
		},
		method : 'POST',
		dataType: 'JSON',
		success : function(data)
		{
			$.LoadingOverlay("hide");	
			if( data.success != '' )
			{
				Command: toastr["success"](data.success);
				location.reload();
			}
			else if( data.error != '' )
			{
				Command: toastr["error"](data.error);
			}
		},
		error: function(){
			$.LoadingOverlay("hide");	
		}
	});
});

$('body').on('focus','.click_sel', function(){
	var elm = $(this);
	elm.select();
});
	
