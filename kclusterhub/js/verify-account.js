
 
    




$(function(){

    function getUrlParams(k){
        var p={};
        location.search.replace(/[?&]+([^=&]+)=([^&]*)/gi,function(s,k,v){p[k]=v})
        return k?p[k]:p;
       }

    
    var token=getUrlParams('token');
    $.ajax({url: `./server/verify_email.php?token=${token}`,
    method: 'GET',
    success: function(data) {
        let successMessage=`<img src="http://goactionstations.co.uk/wp-content/uploads/2017/03/Green-Round-Tick.png" class="w-25" alt="">
    <p class="fs-3 mt-4 mb-3">Your account has been created successfully</p>`;
        $('.card>.card-body').append(successMessage);
        sessionStorage.clear();
    },
    error:function(xhr, status, error) {
       let errormesg=JSON.parse(xhr.responseText);
        let errorMessage=`<div class="text-danger">
        <i class="fa-solid fa-triangle-exclamation fs-2 triangle"></i>
        <span class="fs-3">${errormesg.errormesg}</span>
    </div>`
    let resend=`<hr>
<div class="error-message error-resendemail">
    <i class="fa-solid fa-triangle-exclamation"></i>
    <span id="error-text-resendemail"></span>
</div>
<div id="resend-email-text"><a href="#" id="resend-link">Resend confirmation email</a>
</div>
<div class="text-center mt-3 text-primary" id="loading-spinner">
    <div class="spinner-border" role="status">
      <span class="sr-only">Loading...</span>
    </div>
  </div>`
    $('.card>.card-body').append(errorMessage);
    $('.card>.card-body').append(resend);
      }
    })  

    $(document).on('click', '#resend-link', function(){
        $('.text-danger').hide();
        $('#resend-email-text').hide();
        $('#loading-spinner').show();
        $('#token-text').hide();
        

         $.ajax({url: `./server/resend-email.php?token=${token}&type=regenerate-token`, 
        method: 'GET',
        dataType: 'json',
        success:function(data){
            $('#email-to').text(data['email'])
            $('#email-dialog').show();
            $('#loading-spinner').hide();
        },
        error:function(xhr, status, error) {
            const response=JSON.parse(xhr.responseText);
            $('#error-text-resendemail').text(response.errormesg);
            $('.error-resendemail').show();
            $('#resend-email-text').show();
            $('#loading-spinner').hide();
          }
    })
    })

   

    $('#resend-link2').click(()=>{
        $('.error-resendemail2').hide();
         let email=$('#email-to').text();
        $('#resend-email-text2').hide();
        $('#loading-spinner-small').show();
         $.ajax({url: `./server/resend-email.php?email=${email}&type=email-verification`, 
        method: 'GET',
        success:function(){
            $('#resend-email-text2').show();
            $('#loading-spinner-small').hide();
        },
        error:function(xhr, status, error) {
            const response=JSON.parse(xhr.responseText);
            $('#error-text-resendemail2').text(response.errormesg);
            $('.error-resendemail2').show();
            $('#resend-email-text2').show();
            $('#loading-spinner-small').hide();
          }
    })
    }) 
    
    

})

