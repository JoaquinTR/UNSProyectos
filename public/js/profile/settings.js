jQuery(function(){
    console.log("hello there");
    /* agarrar el checked, y modificar el link css */

});

/* TODO fixme, hacer un visor de tema con el js de dhtmlx gantt y los skins, esta función los intercambia y luego se llama a un gantt.init */
function switchTheme(e) {
    let themeLink = document.getElementById("theme-link");
    let theme = document.getElementById("theme-selector").value;
    if (theme === "1") {
        themeLink.href = "https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css";
    } else if (theme === "2") {
        themeLink.href = "https://cdnjs.cloudflare.com/ajax/libs/bootstrap-material-design/4.0.2/bootstrap-material-design.umd.min.js";
    }
}