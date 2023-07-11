$(function(){
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

    function getUrlParams(k){
        var p={};
        location.search.replace(/[?&]+([^=&]+)=([^&]*)/gi,function(s,k,v){p[k]=v})
        return k?p[k]:p;
       }
    
    $('#reset-form').click(()=>{
        $('.error-message').hide();
        $('.error-message-big').hide();
        
    
        var pwd=($('#pwd').val().trim());
        var confPwd=($('#conf-pwd').val().trim());
        var token=getUrlParams('token');

         console.log(confPwd)
    
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
                $('.error-confPasswd').show();
                return ;
            }else{
                $('#error-text-confPasswd').text("");
                $('.error-confPasswd').hide();
            }
        }else{
            $('#error-text-password').text("Please enter your password");
            $('.error-password').show();
            return ;
        }
    
        $('#reset-form').prop('disabled', true);
        $('#loading-spinner').show();
        $.ajax({url: "./server/password_reset_verify.php", 
        method: 'POST',
        data:JSON.stringify({token:token,password:pwd}),
        dataType:"json",
        contentType: 'application/json',
        success:function(){
            $('#EmailModalCenter').modal('show');
            $('#loading-spinner').hide();
        },
        error:function(xhr, status, error) {
            const response=JSON.parse(xhr.responseText)
            $('#error-text-big').text(response.errormesg);
            $('.error-message-big').show();
            $('#loading-spinner').hide();
            $('#reset-form').prop('disabled', false);
          }
    })
    
    })

   
        
})