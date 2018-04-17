// Account Profile Datatables
$(document).ready(function(){
    //Validate Change password Form
    $("#user_change_password").validate({
        ignore: [],
        errorElement: "div",
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
            email : {
                required: true,
                validateEmail: true
            },
            profile_image :{
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
            first_name : {
                required: "Please enter first name",
                validateName: "First name must contain only letters, apostrophe, spaces or dashes."
            },
            last_name : {
                required: "Please enter last name",
                validateName: "Last name must contain only letters, apostrophe, spaces or dashes."
            },
            email : {
                required: "Please enter email"
            },
            profile_image :{
                required: "Please upload user image",
                checkEmpty:true
            }
        }
    });

    $("#change_password").validate({
        ignore: [],
        errorElement: "div",
        rules: {
            old_password:{
                required: true
            },
            password : {
                required: true
            },
            confirm_password : {
                required: true,
                equalTo: "#password"
            }
        },
        messages: {
             old_password : {
                required: "Please enter old password"
            },
            password : {
                required: "Please enter password"
            },
            confirm_password : {
                required: "Please enter confirm password"
            }
        }

    });
});