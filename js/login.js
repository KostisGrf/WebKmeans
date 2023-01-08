$(function(){

    if (sessionStorage.getItem("apikey") !== null) {
        window.location.href = "./";
    }

showHidePwd=$('.showHide-pwd');
pwdField=document.querySelector('#pwd');

showHidePwd.click(function(){
   
        if(pwdField.type==="password"){
            showHidePwd.toggleClass("fa-eye-slash fa-eye")
            pwdField.type="text"
        }else{
            showHidePwd.toggleClass("fa-eye fa-eye-slash")
            pwdField.type="password"
        }
   
})

    $('#login-form').click(()=>{
        $('.error-message').hide();
        $('.error-message-big').hide();

    
        var email=($('#email').val().trim());
        var pwd=($('#pwd').val().trim());
    
        if((email.length==0)){
            $('#error-text-email').text("Please enter your email");
            $('.error-email').show();
            return ;
        }else{
            $('#error-text-email').text("");
            $('.error-email').hide();
        }


        if((pwd.length==0)){
            $('#error-text-password').text("Please enter your password");
            $('.error-password').show();
            return ;
        }else{
            $('#error-text-password').text("");
            $('.error-password').hide();
        }

        
        
    $('#login-form').prop('disabled', true);
    $('#loading-spinner').show();
    $.ajax({url: "./server/api/login.php", 
    method: 'POST',
    data:JSON.stringify({email:email,password:pwd}),
    dataType:"json",
    contentType: 'application/json',
    success: function(data) {
        console.log(data);
        sessionStorage.setItem("apikey", data.apiKey);
        sessionStorage.setItem("email", data.email);
        sessionStorage.setItem("fname", data.fname);
        sessionStorage.setItem("lname", data.lname);
        $('#login-form').prop('disabled', false);
        $('#loading-spinner').hide();
        window.location.href = "./";
        },
    error:function(xhr, status, error) {
        const response=JSON.parse(xhr.responseText)
        $('#error-text-big').text(response.errormesg);
        $('.error-message-big').show();
        $('#login-form').prop('disabled', false);
        $('#loading-spinner').hide();
      }
    })  
  })
})


