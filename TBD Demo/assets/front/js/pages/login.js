$(document).ready(function(){

    //Validate Login Form
    $("#login_form").validate({
        ignore: [],
        rules: {
            email : {
                required: true,
                validateEmail: true
            },
            password : {
                required: true
            }
        },
        messages: {
            email : {
                required: "Please enter an email address"
            },
            password : {
                required: "Please enter password"
            }
        }
    });

    //Validate Forgot Password Form
    $("#forgot_password_form").validate({
        rules: {
            forgot_pwd_email : {
                required: true,
                validateEmail: true
            }
        },
        messages: {
            forgot_pwd_email : {
                required: "Please enter an email address"
            }
        }
    });

    // Submit forgot password form on click of submit button in forgot password modal footer
    $(document).on('click','#forgot_pwd_submit', function(e){
        $("#forgot_password_form").submit();
    });

    // Load the modal when there is a server side error on forgot password form
    if( $("#forgot_pwd_error").val() == 1 )
        $('#forgot_password_modal').modal('show');


    //Validate Reset password Form
    $("#reset_password_form").validate({
        rules: {
            password : {
                required: true
            },
            confirm_password : {
                required: true,
                equalTo: "#password"
            }
        },
        messages: {
            password : {
                required: "Please enter password"
            },
            confirm_password : {
                required: "Please enter repeat password",
                equalTo: "Repeat password must be same as password"
            }
        }
    });
});