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
    $('.card>.card-body').append(errorMessage);
      }
    })  

})