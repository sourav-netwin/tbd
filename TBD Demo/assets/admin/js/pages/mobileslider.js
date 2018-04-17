var base_url = '';
$(function(){
	base_url = $('#base_url').val();
	init_colorpicker();
	validate_slider_form();
	
	
	var start,stop;
	$("#slider-table").children('tbody')
	.sortable({
		revert: true,
		axis: "Y",
		start: function(event, ui) {
			start = ui.item.index();
		},
		stop: function(event, ui) {
			stop = ui.item.index();
			var moved_to = '';
			if(start < stop){
				moved_to = 'down';
			}
			else if(start > stop){
				moved_to = 'up';
			}
			var update_array = [];
			$("#slider-table tbody tr").each(function(){
				update_array.push($(this).attr('data-id')+':'+($(this).index()+1));
			});
			if(moved_to == 'up' || moved_to == 'down'){
				var data = ui.item.attr('data-id');
				var type = moved_to;
        
				$.ajax({
					url : base_url+'admin/mobileslider/update_slider_sequence',
					data : {
						id: data, 
						type: type,
						update_data: JSON.stringify(update_array)
					},
					method : 'POST',
					success : function(data)
					{
						if( data == "success" )
							window.location = "mobileslider";
					}
				});
			}
		}
	});
	
});

function readURL(input) {

	if (input.files && input.files[0]) {
		var reader = new FileReader();

		reader.onload = function (e) {
			$('.mobile_slider_image').removeAttr('style');
			$('.mobile_slider_image').attr('style','background-image: url('+e.target.result+')');
		}

		reader.readAsDataURL(input.files[0]);
	}
}

$('body').on('change','#inputImage',function(){
	readURL(this);
});

function validate_slider_form(){
	$("#mobile_slider_form").validate({
		errorElement: "div",
		ignore: [],
		rules: {
			mobile_slider_image :{
				required:{
					depends: function(element) {
						return ( $("#old_photo").length == 1 ? false : true );
					}
				},
				checkEmpty:"Please upload image file.",
				checkFile:true
			}
		},
		messages: {
			mobile_slider_image :{
				required: "Please upload slider image",
				checkEmpty:true
			}
		}
	});
}

$('table#slider-table tbody').on('click', '.active', function () {
	var data = $(this).parents('tr').data();

	var status = $(this).data('status');
	if (confirm("Are you sure you want to change the status?")) {
		$.ajax({
			url : base_url + "admin/mobileslider/change_status/" + data['id'] +"/"+status,
			method : 'POST',
			success : function(data)
			{
				location.reload();
			}
		});
	}
});
	
$('table#slider-table tbody').on('click', '.edit', function () {
	var url = $(this).attr('data-href');
	$.ajax({
		url: url,
		type: 'POST',
		data: {},
		success: function(data){
			createModal('edit-slider-modal', 'Edit Slider', data);
			validate_slider_form();
			init_colorpicker();
		}
	});
});

$('table#slider-table tbody').on('click', '.delete', function () {
	var data = $(this).parents('tr').data();

	if (confirm("Are you sure you want to delete this slider?")) {
		$.ajax({
			url : base_url + "admin/mobileslider/delete/" + data['id'],
			method : 'POST',
			success : function(data)
			{
				location.reload();
			}
		});
	}
});
	
$('body').on('submit','.modalCustom #mobile_slider_form', function(e){
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
				placeError(data.result,data.message,'mobile_slider_form')
			}
		},
		error: function(){
			unloading();
		}
	});
});


