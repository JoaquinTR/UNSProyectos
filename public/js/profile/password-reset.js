jQuery(function(){
    $( "#password_eye" ).click(function() {
        password_show_hide("#password","#password_eye")
    });
    $( "#passwordr_eye" ).click(function() {
        password_show_hide("#passwordr","#passwordr_eye")
    });
});

function password_show_hide(id,id_container) {
    var x = $(id); console.log(x);
    var show_eye = $(`${id_container} #show_eye`); console.log(show_eye);
    var hide_eye = $(`${id_container} #hide_eye`); console.log(hide_eye);
    hide_eye.removeClass("d-none");
    if (x.attr('type') === "password") {
        x.attr('type', 'text');
        hide_eye.removeClass("d-none");
        show_eye.addClass("d-none");
    } else {
        x.attr('type', 'password');
        show_eye.removeClass("d-none");
        hide_eye.addClass("d-none");
    }
}