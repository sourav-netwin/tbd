$(document).ready(function(){
    var base_url = $("#base_url").val();
    
    //Validate edit profile Form
    $("#edit_profile_form").validate({
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
                required: "Please enter postal code"
            }
        },
        errorPlacement: function(error, element) {
            error.insertAfter(element);
        }
    });
});