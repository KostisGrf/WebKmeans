$(function(){
    if (sessionStorage.getItem("apikey") !== null) {
        window.location.href = "./";
    }

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



$('#register-form').click(()=>{
    $('.error-message').hide();
    $('.error-message-big').hide();

    var firstName=($('#first-name').val().trim());
    var lastName=($('#last-name').val().trim());
    var email=($('#email').val().trim());
    var pwd=($('#pwd').val().trim());
    var confPwd=($('#conf-pwd').val().trim());

    if((firstName.length==0)){
        $('#error-text-firstname').text("Please enter your first name");
        $('.error-firstname').show();
        return ;
    }else{
        $('#error-text-firstname').text("");
        $('.error-firstname').hide();
    }

    if((lastName.length==0)){
        $('#error-text-lastname').text("Please enter your last name");
        $('.error-lastname').show();
        return ;
    }else{
        $('#error-text-lastname').text("");
        $('.error-lastname').hide();
    }

    const re=/^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;

    if((email.length==0)){
        $('#error-text-email').text("Please enter your email");
        $('.error-email').show();
        return ;
    }else{
        if(!(($('#email').val().trim()).match(re))){
            $('#error-text-email').text("Invalid email");
            $('.error-email').show();
            return ;
        }else{
            $('#error-text-email').text("");
            $('.error-email').hide();
        }
       
    }

    if((pwd.length!==0)){
        $('#error-text-password').text("");
        $('.error-password').hide();

<<<<<<< HEAD
        const passval=/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d@$!%*?&]{8,}$/;
=======
        const passval=/^(?=.*\d)(?=.*[a-zA-Z])[0-9a-zA-Z]{8,}$/;
>>>>>>> 549c676594f3b31b1fbceaece92a9e19c635fc8f
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
        }
    }else{
        $('#error-text-password').text("Please enter your password");
        $('.error-password').show();
        return ;
    }
    $('#register-form').prop('disabled', true);
    $('#loading-spinner').show();
    $.ajax({url: "./server/api/register.php", 
    method: 'POST',
    data:JSON.stringify({email:email,password:pwd,fname:firstName,lname:lastName}),
    dataType:"json",
    contentType: 'application/json',
    success:function(){
        $('#email-to').text(email)
        $('#EmailModalCenter').modal('show');
        $('#loading-spinner').hide();
    },
    error:function(xhr, status, error) {
        const response=JSON.parse(xhr.responseText)
        $('#error-text-big').text(response.errormesg);
        $('.error-message-big').show();
        $('#register-form').prop('disabled', false);
        $('#loading-spinner').hide();
      }
})

})

$('#resend-link').click(()=>{
    $('.error-resendemail').hide();
     email=($('#email').val().trim());

    $('#resend-email-text').hide();
    $('#loading-spinner-small').show();
     $.ajax({url: `./server/resend-email.php?email=${email}&type=email-verification`, 
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

})


