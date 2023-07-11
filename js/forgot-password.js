$(function(){
    
        $('#email-form').click(()=>{
            $('.error-message').hide();
            $('.error-message-big').hide();
    
        
            var email=($('#email').val().trim());
            console.log
        
            if((email.length==0)){
                $('#error-text-email').text("Please enter your email");
                $('.error-email').show();
                return ;
            }else{
                $('#error-text-email').text("");
                $('.error-email').hide();
            }
            $('#email-form').prop('disabled', true);
            $('#loading-spinner').show();
        $.ajax({url: "./server/api/forgot-password.php", 
        method: 'POST',
        data:JSON.stringify({email:email}),
        dataType:"json",
        contentType: 'application/json',
        success: function(data) {
            $('#email-to').text(email)
            $('#EmailModalCenter').modal('show');
            $('#loading-spinner').hide();},
        error:function(xhr, status, error) {
            const response=JSON.parse(xhr.responseText)
            $('#error-text-big').text(response.errormesg);
            $('.error-message-big').show();
            $('#email-form').prop('disabled', false);
            $('#loading-spinner').hide();
          }
        })  
      })

    $('#resend-link').click(()=>{
        $('.error-resendemail').hide();
        email=($('#email').val().trim());
    
        $('#resend-email-text').hide();
        $('#loading-spinner-small').show();
        $.ajax({url: `./server/resend-email.php?email=${email}&type=forgot-password`, 
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
    
    
    