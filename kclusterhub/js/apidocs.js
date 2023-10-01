$(function(){
    if (sessionStorage.getItem("apikey") === null) {
        window.location.href = "./";
        $('#gear-dropdown').hide();
        return;
    }else{
        $('#gear-dropdown').show();
       $('#user-name').text(sessionStorage.getItem("fname") + " " +sessionStorage.getItem("lname"));
       $('#apikey-input').val(sessionStorage.getItem("apikey"));
       
    }
    

    $('#sign-out-btn').click(()=>{
        sessionStorage.clear();
        window.location.href ='./'
    })
    

    $('#copy-apikey').click(()=>{
        navigator.clipboard.writeText($('#apikey-input').val());
    })

    $('#regenerate-key').click(()=>{
        $('#regenerate-key').prop('disabled',true);

        $.ajax({url: "./server/regenerate-apikey.php", 
            method: 'POST',
            data:JSON.stringify({apikey:sessionStorage.getItem("apikey")}),
            dataType: 'json',
            success: function(data) {
                sessionStorage.setItem("apikey",data.apikey);
                $('#apikey-input').val(data.apikey)
                $('#regenerate-key').prop('disabled',false);
    }
    })
    })

    
})