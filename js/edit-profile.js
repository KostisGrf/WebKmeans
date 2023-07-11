$(function(){
    if (sessionStorage.getItem("apikey") === null) {
        window.location.href = "./";
        return;
    }else{
        $('#email').val(sessionStorage.getItem("email"));
        $("#email").prop('disabled', true);
        $("#fname").val(sessionStorage.getItem("fname"));
        $("#lname").val(sessionStorage.getItem("lname"));
        $("#user-name").text(sessionStorage.getItem("fname")+" "+sessionStorage.getItem("lname"))
    }
    var apikey=sessionStorage.getItem("apikey")


    $('#sign-out-btn').click(()=>{
        sessionStorage.clear();
        window.location.href ='./'
    })

showHidePwd=$('.showHide-pwd');
pwdFields=document.querySelectorAll('.pwd');

showHidePwd.click(function(){    
    showHidePwd.toggleClass("fa-eye-slash fa-eye")
    pwdFields.forEach(pwdField => {
        if(pwdField.type==="password"){
           
            pwdField.type="text"
        }else{
            showHidePwd.toggleClass("fa-eye fa-eye-slash")
            pwdField.type="password"
        }
    });
   
})


    $('#save-form').click(()=>{
    $('#error-text-confPasswd').text("");
    $('.error-confpassword').hide();
    $('#error-text-password').text("");
    $('.error-password').hide();
    var firstName=($('#fname').val().trim());
    var lastName=($('#lname').val().trim());
    var pwd=($('#pwd').val().trim());
    var confPwd=($('#conf-pwd').val().trim());
    

    var dataChanged={}
    

    if((firstName.length==0)){
        $('#error-text-firstname').text("Please enter your first name");
        $('.error-firstname').show();
        return ;
    }else{
        $('#error-text-firstname').text("");
        $('.error-firstname').hide();
        if(firstName!==sessionStorage.getItem("fname")){
            dataChanged.fname=firstName;
        }
    }

    if((lastName.length==0)){
        $('#error-text-lastname').text("Please enter your last name");
        $('.error-lastname').show();
        return ;
    }else{
        $('#error-text-lastname').text("");
        $('.error-lastname').hide();
        if(lastName!==sessionStorage.getItem("lname")){
            dataChanged.lname=lastName;
        }
    }

    if((pwd.length!==0)){
        $('#error-text-password').text("");
        $('.error-password').hide();

        const passval=/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d@$!%*?&]{8,}$/;
        if(!(pwd.match(passval))){
            $('#error-text-password').text("Your password length must be at least 8,contain at least one uppercase and one digit");
            $('.error-password').show();
            return ;
        }

        if(pwd!==confPwd){
            $('#error-text-confPasswd').text("password doesn't match");
            $('.error-confpassword').show();
            return ;
        }else{
            $('#error-text-confPasswd').text("");
            $('.error-confpassword').hide();
            dataChanged.password=pwd;
        }
    }

    if(($('#lname').val()===sessionStorage.getItem("lname"))&&($('#fname').val()===sessionStorage.getItem("fname"))&&($('#pwd').val().length===0)&&($('#conf-pwd').val().length===0)){
        $('#error-text-big').text("You didn't change anything");
        $('.error-message-big').show();
        return;
    }else{
        $('#error-text-big').text("");
        $('.error-message-big').hide();
    }

    

    if(Object.keys(dataChanged).length!==0){
        dataChanged.apikey=apikey;

        $('#save-form').prop('disabled', true);
        $('#delete-acc-form').prop('disabled',true);
        $('#loading-spinner').show();
        
        $.ajax({url: "./server/api/edit-profile.php", 
    method: 'POST',
    data:JSON.stringify(dataChanged),
    contentType: 'application/json',
    success: function() {
        sessionStorage.setItem("fname", firstName);
        sessionStorage.setItem("lname", lastName);
        $("#user-name").text(firstName + " " +lastName)
        $('#save-form').prop('disabled', false);
        $('#delete-acc-form').prop('disabled',false);
        $('#loading-spinner').hide();
        $('#profile-modal').modal('show');
        },
    error:function(xhr, status, error) {
        $('#error-text-big').text(xhr.responseText);
        $('.error-message-big').show();
        $('#save-form').prop('disabled', false);
        $('#delete-acc-form').prop('disabled',false);
        $('#loading-spinner').hide();
      }
    })  
    }
    

    })

    $('#delete-acc-form').click(()=>{
        $('#delete-acc-quest').modal('show');
    });

    $('#yes-btn').click(()=>{
        $('#delete-acc-quest').modal('hide');
        $('#save-form').prop('disabled', true);
        $('#delete-acc-form').prop('disabled',true);
        $('#loading-spinner').show();
        let email=sessionStorage.getItem("email");
        $.ajax({url: "./server/api/delete-user.php", 
    method: 'DELETE',
    data:JSON.stringify({apikey:apikey}),
    contentType: 'application/json',
    success: function() {
        $('#loading-spinner').hide();
        $('#email-to').text(email);
        $('#delete-acc-modal').modal('show');
        },
    error:function(xhr, status, error) {
        let errormesg=JSON.parse(xhr.responseText);
        $('#error-text-big').text(errormesg.errormesg);
        $('.error-message-big').show();
        $('#loading-spinner').hide();
      }
    })  
    })


    $('#resend-link').click(()=>{
        $('.error-resendemail').hide();
        email=sessionStorage.getItem("email");
    
        $('#resend-email-text').hide();
        $('#loading-spinner-small').show();
        $.ajax({url: `./server/resend-email.php?email=${email}&type=delete-user`, 
        method: 'GET',
        success:function(){
            $('#resend-email-text').show();
            $('#loading-spinner-small').hide();
        },
        error:function(xhr, status, error) {
            const response=JSON.parse(xhr.responseText);
            $('#error-text-resendemail').text(response.errormesg);
            $('.error-resendemail').show();
            $('#resend-email-text').show();
            $('#loading-spinner-small').hide();
          }
    })
    })

});