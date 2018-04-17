var oTable;
$(function () {
	initToastr();
	var fullUrl = $(location).attr('href');
	var urlArray = fullUrl.split('/');
	var urlLength = urlArray.length;
	var currentMenu = fullUrl;
	$('.sidebar-menu a').each(function(){
		if($(this).attr('href') == currentMenu){
			var parent = $(this).parent().parent();
			if(parent.is('ul') && !parent.hasClass('sidebar-menu')){
				$(this).parent().addClass('active')
				parent.parent().addClass('active');
				parent.show();
			}
			else if($(this).parent().is('li')){
				$(this).parent().addClass('active');
			}
		}
	});
	
	
	
	var success_message = $("#success_message").text();
	var error_message = $("#error_message").text();
	function initToastr(){
		toastr.options = {
			//"progressBar": true,
			"positionClass": "toast-top-center",
			"showDuration": "300",
			"hideDuration": "1000",
			"timeOut": "5000",
			"extendedTimeOut": "1000",
			"showEasing": "swing",
			"preventDuplicates": true,
			//"hideEasing": "linear",
			"showMethod": "fadeIn",
			"hideMethod": "fadeOut"
		}
	}
		

	if( success_message != '' )
		Command: toastr["success"](success_message)
	else if( error_message != '' )
		Command: toastr["error"](error_message)
	
	//Select2 Initailization.

	$(".select-filter").select2();

	//Datatables search
	$('#search_filter').keyup(function(){
		oTable.fnFilter($(this).val());
	});

	//Datable change on search filters
	$("#product_main_category, #product_parent_category, #product_sub_category,#retailers,#store_format").change(function(){
		oTable.fnDraw();
	});

	$.validator.addMethod("checkFile", function(value, element) {
		if(element.value !='')
		{
			if(!/(\.gif|\.jpg|\.jpeg|\.GIF|\.JPG|\.JPEG|\.PNG|\.png)$/i.test(element.value)) {
				//alert("Invalid image file type.");
				element.focus();
				return false;
			}
			return true;
		}
		return true;
	}, "Please upload .jpg, .jpeg, .png, or .gif image.");


	$.validator.addMethod("checkEmpty", function(value, element) {
		if($("#old_icon").val() =='')
		{
			if(element.value =='')
				return false;
		}
		return true;
	});

	$.validator.addMethod("checkFileExcel", function(value, element) {
		if(element.value !='')
		{
			if(!/(\.xls|\.xlsx)$/i.test(element.value)) {
				//alert("Invalid image file type.");
				element.focus();
				return false;
			}
			return true;
		}
		return true;
	}, "Please upload .xls, .xlsx file.");

	$.validator.addMethod("validateName", function(value, element) {
		return this.optional(element) || /^[a-z'\-\s]+$/i.test(value);
	});

	$.validator.addMethod("validateCity", function(value, element) {
		return this.optional(element) || /^[a-z\s]+$/i.test(value);
	},"City must contain only letters and spaces.");

	$.validator.addMethod("validateStreetAddress", function(value, element) {
		return this.optional(element) || /^[a-z0-9'",\\\/\-\s]+$/i.test(value);
	},"Street Address must not contain special characters.");

	$.validator.addMethod("validate_content", function(value, element) {
		if( tinyMCE.get('content').getContent() == '' )
			return false;
		else
			return true;
	});

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
	}, "Please enter a valid phone number.");

	$.validator.addMethod("checkZipFile", function(value, element) {
		if(element.value !='')
		{
			if(!/(\.zip|\.zipx)$/i.test(element.value)) {
				element.focus();
				return false;
			}
			return true;
		}
		return true;
	}, "Please upload .zip, .zipx file.");

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

	$.validator.addMethod("validateTime", function(value, element) {
		if((element.value).trim() !='')
		{
			var dateReg = /^(0?[1-9]|1[012])(:[0-5]\d) [APap][mM](\s)[-](\s)([01]?[0-9]|2[0-3]):[0-5][0-9] [APap][mM]/;
			if (dateReg.test(element.value))
			{

				return true;
			}
			else
			{
				console.log(element.value);
				return false;
			}
		}
		return true;
	}, "Please enter a valid 12 hour format time.");

	$("#special_quantity").spinner({
		min: 1
	});


	//Remove title attribute for select2 drop downs
	$(".select2-selection__rendered").removeAttr('title');

	//Remove title attribute on change of select option
	$(document).on('change','select',function(e){
		$(".select2-selection__rendered").removeAttr('title');
	});
	$(document).on('mouseover','.select2',function(e){
		$(this).find(".select2-selection__rendered").removeAttr('title');
	});

	$.validator.setDefaults({
		errorPlacement: function(error, element) {
			if($(element).attr('id') == 'inputImage') {
				error.insertAfter($("#image_text")).css('line-height','0px').css('line-height','0px');
			} else if($(element).is('select')) {
				error.insertAfter($(element).next(".select2"));
			} else if(element.parent('.input-group').length) {
				error.insertAfter(element.parent());
			} else if( $(element).is(':radio') ) {
				error.insertAfter($(".radio"));
			} else if($(element).attr('id') == 'import_zip_file') {
				error.insertAfter($("#image_folder_text")).css('line-height','0px');
			} else if($(element).attr('name') == 'store_products[]') {
				error.insertAfter($("#list_product").closest('.form-group'));
			} else if($(element).attr('name') == 'open_hours[]') {
				error.insertAfter($("#store_time_errors"));
			} else if($(element).is(':checkbox')) {
				error.insertAfter($(element).closest(".checkbox_lists"));
			} else {
				error.insertAfter(element);
			}
		}
	});



	//Selection of Store Format
	$(document).on('click','#all_store_formats,#all_stores,#select_products,#select_products_search,#all_category',function(event){
		var name = '';
		if( $(this).attr('id') == 'select_products' )
			name = 'store_products[]';
		else if( $(this).attr('id') == 'select_products_search' )
			name = 'store_products_search[]';
		else
			name = $(this).attr('name');

		if(this.checked) {
			// Iterate each checkbox
			$("input[name='"+name+"']:checkbox").each(function() {
				this.checked = true;
			});
		}
		else {
			$("input[name='"+name+"']:checkbox").each(function() {
				this.checked = false;
			});
		}
	});

	//Unselect or select
	$(document).on('click','.checkbox input:checkbox',function(event){
		var arr = ['all_store_formats','all_stores','select_products','all_category'];
		var name = '';
		if ($.inArray($(this).attr('id'), arr) !== -1)
		{
			if( $(this).attr('id') == 'select_products' )
				name = 'store_products[]';
			else
				name = $(this).attr('name');

			if(this.checked) {
				// Iterate each checkbox
				$("input[name='"+name+"']:checkbox").each(function() {
					this.checked = true;
				});
			}
			else {
				$("input[name='"+name+"']:checkbox").each(function() {
					this.checked = false;
				});
			}
		}
	//        else {
	//            name = $(this).attr('name');
	//
	//            if( name == 'store_products[]' )
	//            {
	//                var total_checkbox = ($("input[name='"+name+"']").length);
	//                if($("input[name='"+name+"']:checked").length ==  total_checkbox){
	//                    $("#select_products").prop('checked',true);
	//                } else {
	//                    $("#select_products").prop('checked',false);
	//                }
	//            }
	//            else
	//            {
	//                var total_checkbox = ($("input[name='"+name+"']:not(:first)").length);
	//
	//                if($("input[name='"+name+"']:not(:first):checked").length ==  total_checkbox){
	//                    $("input[name='"+name+"']:first").prop('checked',true);
	//                } else {
	//                    $("input[name='"+name+"']:first").prop('checked',false);
	//                }
	//            }
	//        }

	});

	//Set aspect ratio

	var ratio = 1/1;
	if($('#aspect_ratio').length) {
		if($('#aspect_ratio').val() == 'any'){
			ratio = '';
		}
		else{
			ratio = $('#aspect_ratio').val();
			ratio = $('#aspect_ratio').val();
		}
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

	$('body').on('hidden.bs.modal','#imageModal', function () {

		var  getData = $image.cropper('getData');

		$('#image-x').val(getData['x']);
		$('#image-y').val(getData['y']);
		$('#image-width').val(getData['width']);
		$('#image-height').val(getData['height']);

		$(".profile_image_group").find('.btn-primary').css("background-image", "url("+$image.cropper("getDataURL")+")");
		
		$('.modal').each(function(){
			var cnt = 0;
			if($(this).is(':visible')){
				cnt++;
			}
			if(cnt == 0){
				$('body').removeClass('modal-open');
			}
			else{
				if(!$('body').hasClass('modal-open')){
					$('body').addClass('modal-open');
				}
			}
		});

	});

	//Block for Image preview

	$(document).on("change","#inputImage", function()
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
	
	
	
	
	$.fn.dataTableExt.oApi.fnStandingRedraw = function(oSettings) {
		//redraw to account for filtering and sorting
		// concept here is that (for client side) there is a row got inserted at the end (for an add)
		// or when a record was modified it could be in the middle of the table
		// that is probably not supposed to be there - due to filtering / sorting
		// so we need to re process filtering and sorting
		// BUT - if it is server side - then this should be handled by the server - so skip this step
		if(oSettings.oFeatures.bServerSide === false){
			var before = oSettings._iDisplayStart;
			oSettings.oApi._fnReDraw(oSettings);
			//iDisplayStart has been reset to zero - so lets change it back
			oSettings._iDisplayStart = before;
			oSettings.oApi._fnCalculateEnd(oSettings);
		}
      
		//draw the 'current' page
		oSettings.oApi._fnDraw(oSettings);
	};
	
	
});


function status_html( active_status )
{
	var html ='';
	if(parseInt(active_status) == 1)
		html = '<a class="active" data-status="0" href="#" title="Change status"><i class="fa fa-fw fa-med fa-check"></i></a>';
	else
		html = '<a class="active" data-status="1" href="#" title="Change status"><i class="fa fa-fw fa-med fa-close"></i></a>';

	return html;
}

function approve_html( approved_status )
{
	var html ='';
	if(parseInt(approved_status) == 1)
		html = '<a class="disapprove" data-status="0" href="#" style="cursor:default;"><i class="fa fa-fw fa-med fa-thumbs-o-up" style="color:#3c763d"></i></a>';
	else
		html = '<a class="approve" data-status="1" href="#" title="Approve"><i class="fa fa-fw fa-med fa-thumbs-o-down" style="color:#FF0000"></i></a>';

	return html;
}

function goBack() {
	window.history.back();
}



function removeModal(id){
	$('#'+id).fadeOut('slow', function(){
		$('#'+id).remove();
		$('.modal-backdrop').first().remove();
		if($('.modalCustom').length > 0){
			$('body').addClass('modal-open');
		}
		else{
			$('body').removeClass('modal-open');
		}
	});
}
$("body").on('hidden.bs.modal','.modal', function () {

	$('.modal').each(function(){
		var cnt = 0;
		if($(this).is(':visible')){
			cnt++;
		}
		if(cnt == 0){
			$('body').removeClass('modal-open');
		}
		else{
			if(!$('body').hasClass('modal-open')){
				$('body').addClass('modal-open');
			}
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
		$('.modal').each(function(){
			var cnt = 0;
			if($(this).is(':visible')){
				cnt++;
			}
			if(cnt == 0){
				$('body').removeClass('modal-open');
			}
			else{
				if(!$('body').hasClass('modal-open')){
					$('body').addClass('modal-open');
				}
			}
		});
		$('.modal-backdrop').first().remove();
	});
	
});

function placeError(result,error,formId){        
	if(typeof error =='object'){
		$.each(error, function (key, data) {
			if($("#"+formId+" #"+key).is(":checkbox")){
				var parent = $('#'+key).parent().parent().parent();	
				$("#"+formId+" #"+key).focus();
			}
			else if($('#'+formId+' input[name='+key+']').is(":radio")){
				var parent = $('#'+formId+' input[name='+key+']').parent().parent().parent();
				if(parent.is('ul')){
					parent = parent.parent();
				}
				$('#'+formId+' input[name='+key+']').focus();
			}
			else{
				var parent = $('#'+formId+' #'+key).parent();
			}
			if($('#'+formId+' select[name='+key+']').is("select")){
                            
                               // alert(" key :" + key );
				$('#'+formId+' select[name='+key+']').attr('aria-describedby',key+'-error');
				$('#'+formId+' select[name='+key+']').attr('aria-invalid','true');
                                
                                $('#'+formId+' select[name='+key+']').after('<label class="error" id="'+key+'-error">'+data+'</label>');
                                
                                //error.insertAfter(element.next('.btn-group'));
                                //$('#'+formId+' select[name='+key+']').next('span').after('<label class="error" id="'+key+'-error">'+data+'</label>');
                                
                                 
				$('#'+formId+' select[name='+key+']').focus();
			}
			else{
                               // alert(" key :" + key );
				$('#'+formId+' input[name='+key+']').attr('aria-describedby',key+'-error');
				$('#'+formId+' input[name='+key+']').attr('aria-invalid','true');
				$('#'+formId+' input[name='+key+']').after('<label class="error" id="'+key+'-error'+'">'+data+'</label>');
				$('#'+formId+' input[name='+key+']').focus();
			}
				
		//var errorDiv = parent.find('.with-errors');
		//parent.addClass('has-feedback has-error has-danger');
		//var errorHtml = data;
		//errorDiv.html(errorHtml);

		});
	}
	else{
		var type = '';
		if(result == 1){
			type = 'success';
		}
		else{
			type = 'error';
		}
		show_message(type, error);
	}
	
		
}

/*function loading(message) {
	if(message == '' || typeof message == 'undefined'){
		message = '';
	}
	setTimeout(function(){
		$('body').append('<div class="overlay"><div class="fa fa-refresh fa-spin"></div><div class="over-text" id="loading-text-dyn"><div id="progress-color"></div>'+message+' <span></span></div></div>');
	},20);
	
}*/
function loading(message) {
	if(message == '' || typeof message == 'undefined'){
		message = 'Please Wait';
	}
	setTimeout(function(){
		$('body').append('<div class="overlay"><div class="overlay-inner-box"><div class="fa fa-refresh fa-spin"></div><div class="over-text" id="loading-text-dyn"><div id="progress-color"></div>'+message+' <span></span></div><div id="loader_sub_text"></div></div></div>');
	},20);
}

function unloading() {
	//Remove overlay and loading img
	$('body').find('.overlay').remove();
}

function show_message(type, message){
	Command: toastr[type](message);
}

function initSelect2(){
	if($('select').length > 0){
		$('select').select2();
	}
}
function activateMenu(currentMenu){
	$('.sidebar-menu a').each(function(){
		if($(this).attr('href') == $('#base_url').val()+currentMenu){
			var parent = $(this).parent().parent();
			if(parent.is('ul') && !parent.hasClass('sidebar-menu')){
				$(this).parent().addClass('active')
				parent.parent().addClass('active');
				parent.show();
			}
			else if($(this).parent().is('li')){
				$(this).parent().addClass('active');
			}
		}
	});
}
$.ajaxSetup({
	data: {
		csrf_tbd_token: tbd_csrf
	}
});

function init_cropper(){
	var ratio = 1/1;
	if($('#aspect_ratio').length) {
		if($('#aspect_ratio').val() == 'any'){
			ratio = '';
		}
		else{
			ratio = $('#aspect_ratio').val();
			ratio = $('#aspect_ratio').val();
		}
	}
	var $image = $(".image-crop > img");
	$($image).cropper({
		aspectRatio: ratio,
		zoomable: false,
		rotatable: false
	});
}


$(document).on('focusin', function(e) {
	if ($(e.target).closest(".mce-window").length || $(e.target).closest(".moxman-window").length) {
		e.stopImmediatePropagation();
	}
});

function init_icheck(){
	if($('.icheck-minimal-check').length > 0){
		$('.icheck-minimal-check').iCheck({
			checkboxClass: 'icheckbox_minimal-blue',
			radioClass: 'iradio_minimal-blue'
		});
	}
}
$('body').on('focus','.click_sel', function(){
	var elm = $(this);
	elm.select();
});

$('body').on('input', '[valid-name]', function (event) {
	var elm = $(this);
	var value = elm.val();
	var regex = /[^a-zA-Z0-9 \'\-]/g;
	elm.val(value.replace(regex, ''));
});

$('body').on('input', '[alpha-only]', function (event) {
	var elm = $(this);
	var value = elm.val();
	var regex = /[^a-zA-Z]/g;
	elm.val(value.replace(regex, ''));
});

$('body').on('input', '[alpha-numeric]', function (event) {
	var elm = $(this);
	var value = elm.val();
	var regex = /[^a-zA-Z0-9]/g;
	elm.val(value.replace(regex, ''));
});
$('body').on('input', '[numeric-decimal]', function (event) {
	var elm = $(this);
	var value = elm.val();
	var regex = /[^0-9\.]/g;
	elm.val(value.replace(regex, ''));
});

$('body').on('input', '[numeric-only]', function (event) {
	var elm = $(this);
	var value = elm.val();
	var regex = /[^0-9]/g;
	elm.val(value.replace(regex, ''));
});

function shuffle(array) {
	var currentIndex = array.length, temporaryValue, randomIndex;

	// While there remain elements to shuffle...
	while (0 !== currentIndex) {

		// Pick a remaining element...
		randomIndex = Math.floor(Math.random() * currentIndex);
		currentIndex -= 1;

		// And swap it with the current element.
		temporaryValue = array[currentIndex];
		array[currentIndex] = array[randomIndex];
		array[randomIndex] = temporaryValue;
	}

	return array;
}

function init_colorpicker(){
	if($('.color_pick').length > 0){
		$('.color_pick').colorpicker();
	}
}

function loading_new(message) {
	if(message == '' || typeof message == 'undefined'){
		message = 'Please Wait';
	}
	setTimeout(function(){
		$('body').append('<div class="overlay"><div class="overlay-inner-box"><div class="fa fa-refresh fa-spin"></div><div class="over-text" id="loading-text-dyn"><div id="progress-color"></div>'+message+' <span></span></div></div></div>');
	},20);
	
}

function loading_store_products(message) {
	if(message == '' || typeof message == 'undefined'){
		message = 'Calculating Store Products...';
	}
	setTimeout(function(){
		$('body').append('<div class="overlay-new"><div class="overlay-inner-box"><div class="fa fa-refresh fa-spin"></div><div class="over-text" id="loading-text-dyn"><div id="progress-color"></div>'+message+' <span></span></div><div id="loader_sub_text"></div></div></div>');
	},20);
}

function unloading_store_products() {
	//Remove overlay and loading img
	$('body').find('.overlay-new').remove();
}

function all_store_price_html()
{
    var html ='';
    //html = '<a class="all-store-price" data-status="0" href="javascript:void(0);" title="Update Price"><i style="color:#3c763d" class="fa fa-fw fa-med fa-thumbs-o-up"></i></a>';
    html = '<a class="all-store-price" data-status="0" href="javascript:void(0);" title="Update Price"><i class="fa fa-fw fa-med fa-refresh"></i></a>';
    return html;
}