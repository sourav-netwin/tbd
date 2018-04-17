$(document).ready(function(e){
	validate_categories_form();
        
	var base_url = $("#base_url").val();
	//Validate Category Form
	

	// Category listing (collapse/expand)
	$(document).on("click","#category_list ul.nav li.parent > a > span.sign", function(){
		$(this).find('i:first').toggleClass("fa-minus-circle");
	});

	//Code to expand all categories on page load
	// $("#category_list ul.nav li.parent > a > span.sign").find('i:first').addClass("fa-minus-circle");
	// $("#category_list ul.nav li").parents('ul.children').addClass("in");

	//Actions for category listing
	$('#category_list .actions').on('click', '.fa-trash-o', function () {
		var data = $(this).parents('span').attr('data-id');

		if (confirm("Are you sure you want to delete this category? \n\nNote:Deleting a parent category will delete its sub categories")) {
			window.location = "categories/delete/" + data;
		}
	});

	$('#category_list .actions').on('click', '.active', function () {
		var data = $(this).parents('span').attr('data-id');

		var status = $(this).data('status');
		if (confirm("Are you sure you want to change the status?")) {
			window.location = "categories/change_status/" + data +"/"+status;
		}
	});

	$('body').on('click', '#category_list .actions .fa-edit', function (e) {
		
                e.preventDefault();
		var elm = $(this);
		var parent = elm.parent().parent().parent();
		var category_name = parent.find('.lbl').html();
                
                var data = $(this).parents('span').attr('data-id');
                
		//window.location = "categories/edit/" + data;
		e.preventDefault();
		var url = base_url+"admin/categories/edit/" + data;
		$.ajax({
			url: url,
			type: 'POST',
			data: {},
			success: function(data){
				createModal('edit-categories-modal', 'Edit Category : ' + category_name , data,'large');
                               
                               
                               //alert($("#parent_category").val());
                               if( $("#parent_category").val() == 0 )
                               {
                                        $('#edit-categories-modal').find('.modal-dialog').css({
                                            width:'830'
                                        });
                               }else{
                                        $('#edit-categories-modal').find('.modal-dialog').css({
                                            width:'830'
                                        });
                               }
                               
                               
				validate_categories_form();
                                
                                check_store_grouping();
                                
                                $("#category_icon_container").show();
                                
                                /*
				if( $("#parent_category").val() == 0 )
					$("#category_icon_container").show();

				$("#categories_form #parent_category").change(function(e){
					if( $("#parent_category").val() == 0 )
						$("#category_icon_container").show();
					else
						$("#category_icon_container").hide();
				});
                                */
			}
		});
	});

	$('body').on('change',"#parent_category_search",function(e){
		e.preventDefault();
		var id = $(this).val();

		if( id == '' )
			$(".parent.category").show();
		else
		{
			$(".parent.category").hide();
			$("li.item-"+id).show();
		}
	});

	$('body').on('change',"#parent_category",function(e){
		/*
                e.preventDefault();
		var id = $(this).text();
                
                if( id == '' )
			$(".parent.category").show();
		else
		{
			$(".parent.category").hide();
			$("li.item-"+id).show();
		}
                */
               
               /*
                var parentId = $('#parent_category').val();
                
                if(parentId == 0)
                {
                    $('#groupArea').show();
                    $('#groupId_1').prop('checked', false);
                }else{
                    $('#groupArea').hide();                     
                    $('#groupId_1').prop('checked', true);
                }
                */
               
                check_store_grouping();
                        
                
	});
        
        function check_store_grouping()
        {
            var parentId = $('#parent_category').val();

            if(parentId == 0)
            {
                $('#groupArea').show();
                //$('#groupId_1').prop('checked', false);
            }else{
                $('#groupArea').hide();                     
                $('#groupId_1').prop('checked', true);
            }  
        }

	$('body').on('keyup',"#category_search",function (e) {
		$("#category_list li").show();
		var string = $("#category_search").val().toLowerCase();
		if(string != '')
		{
			$("#category_list li").filter(function() {
				return ($(this).text().toLowerCase().indexOf(string) == -1)
			}).hide();
		}
	});

	$(document).on('click', '.add_to_category', function () {
		var data = $(this).parents('span').attr('data-id');

		window.location = "categories/add/" + data;
	});

    

	$(document).on('click', '.fa-arrow-circle-up, .fa-arrow-circle-down', function () {
		var data = $(this).parents('span').attr('data-id');
		var type = $(this).hasClass("fa-arrow-circle-up") ? 'up' : 'down';

		$.ajax({
			url : base_url+'admin/categories/update_category_sequence',
			data : {
				id: data, 
				type: type
			},
			method : 'POST',
			success : function(data)
			{
				if( data == "success" )
					window.location = "categories";
			}
		});
	});

	//Code for up down sequence
	$(".main_category:first-child").each(function(index, item){
		$(item).find(".fa-arrow-circle-up:first").hide();
	})

	$(".main_category:last-child").each(function(index, item){
		$(item).find(".fa-arrow-circle-down:first").hide();
	})

	$(".main_category .parent_category:first-child").each(function(index, item){
		$(item).find(".fa-arrow-circle-up:first").hide();
	})

	$(".main_category .parent_category:last-child").each(function(index, item){
		$(item).find(".fa-arrow-circle-down:first").hide();
	})

	$(".main_category .parent_category .sub_category:first-child").each(function(index, item){
		$(item).find(".fa-arrow-circle-up:first").hide();
	})

	$(".main_category .parent_category .sub_category:last-child").each(function(index, item){
		$(item).find(".fa-arrow-circle-down:first").hide();
	})

	//Code for expand all
	$(document).on('click','#expand_all',function(e){
		$("#category_list ul.nav li.parent > a > span.sign").find('i:first').removeClass("fa-plus-circle").addClass("fa-minus-circle");
		$("#category_list ul.nav li").parents('ul.children').addClass("in");
		$(this).replaceWith('<a class="btn btn-primary btn-xs pull-right" href="javascript:void(0);" id="collapse_all"> Collapse All</a>');
	});

	//Code for expand all
	$(document).on('click','#collapse_all',function(){
		$("#category_list ul.nav li.parent > a > span.sign").find('i:first').removeClass("fa-minus-circle").addClass("fa-plus-circle");
		$("#category_list ul.nav li").parents('ul.children').removeClass("in");
		$(this).replaceWith('<a class="btn btn-primary btn-xs pull-right" href="javascript:void(0);" id="expand_all"> Expand All</a>');
	});
});

$('body').on('submit','.modalCustom #categories_form', function(e){
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
				placeError(data.result,data.message,'categories_form')
			}
		},
		error: function(){
			unloading();
		}
	});
});

$.validator.addMethod("one_required", function() {
    return $("#categories_form").find(".one_required:checked").length > 0;
}, 'Please select at least one group.');

function validate_categories_form(){
	$("#categories_form").validate({
		ignore: [],
		rules: {
                        'groupId':{
                                one_required :true
			},
			category_title : {
				required: true
			}/*,
				category_icon :{
					required: {
						depends: function(element) {
							return ( $("#old_icon").length == 1 ? false : true );
						}
					},
					checkEmpty:true,
					checkFile:true
				}*/
		},
		messages: {
			category_title : {
				required: "Please enter category name"
			}/*,
				category_icon :{
					required: "Please upload category icon",
					checkEmpty:"Please upload category icon"
				}*/
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