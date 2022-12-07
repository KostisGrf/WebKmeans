
$(function(){



$('#submit-form').on('click',function(){

    
   var new_password=$("#passwd-input").val();
   var conf_passwd=$("#passwd-confirm-input").val();

   function getUrlParams(k){
    var p={};
    location.search.replace(/[?&]+([^=&]+)=([^&]*)/gi,function(s,k,v){p[k]=v})
    return k?p[k]:p;
   }

   if(new_password!==conf_passwd){
    $('#result-form').html('<div class="alert alert-danger" role="alert">Passwords must match!</div>');
    return;
   }

   var token=getUrlParams("token");

   function password_saved(data){
    $('#result-form').html('<div class="alert alert-success" role="alert">'+data.message+'!</div>');
    console.log(data);
   }
   
   function print_error(data){
    console.log(data);
   }

    $.ajax({url: "../backend/password_reset_verify.php", 
    method: 'POST',
    data:JSON.stringify({token:token,password:new_password}),
    dataType:"json",
    contentType: 'application/json',
    success: password_saved});
})})
