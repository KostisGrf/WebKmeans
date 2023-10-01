$(function(){

    if (sessionStorage.getItem("apikey")!==null) {
       $('#sign-in-btn').hide();
       $('#sign-up-btn').hide();
       $('#gear-dropdown').show();
       $('#user-name').text(sessionStorage.getItem("fname") + " " +sessionStorage.getItem("lname"));
    }else{
        $('#gear-dropdown').hide();
        $('#adv-exp').show();
    }

    $('#sign-out-btn').click(()=>{
        sessionStorage.clear();
        window.location.href ='./'
    })
})