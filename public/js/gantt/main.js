/* SYNC vars */
var voto_emitido = false;
var tablero_voto_visible = false;

/* Layout custom */
gantt.config.layout = {
    css: "gantt_container",
    cols: [
        {
            width:225,
            min_width:225,
            rows:[
                {view: "grid", scrollX: "gridScroll", scrollable: true, scrollY: "scrollVer"},
                {view: "scrollbar", id: "gridScroll", group:"horizontal"}
            ]
        },
        {
            rows:[
                {view: "timeline", scrollX: "scrollHor", scrollY: "scrollVer"},
                {view: "scrollbar", id: "scrollHor", group:"horizontal"}
            ]
        },
        {view: "scrollbar", id: "scrollVer"}
    ]
};

/* Agrega una tarea utilizando el lightbox */
function custom_add(){
    let maxId = Math.max.apply(Math, gantt.getChildren(0));
    var res = gantt.createTask({
        id: maxId+1,
        text:"Nueva tarea",
        start_date: gantt.getState().min_date.toISOString().split('T')[0],
        duration:5
    }, 0, gantt.getTaskCount());

    return true;
}


gantt.config.columns = [
	{name: "start_date", align: "center", width: '*'},
	{name: "duration", align: "center", width: '75'},
];

/* Botones */
/* idioma */
gantt.i18n.setLocale("Es");

gantt.locale.labels["column_duration"] = "Duración";
gantt.locale.labels["column_start_date"] = "Comienzo";
gantt.locale.labels["column_text"] = "Nombre";
gantt.locale.labels["confirm_deleting"] = "La tarea será eliminada permanentemente, ¿Está seguro?";
gantt.locale.labels["gantt_cancel_btn"] = "Cancelar";
gantt.locale.labels["icon_cancel"] = "Cancelar";
gantt.locale.labels["gantt_delete_btn"] = "Eliminar";
gantt.locale.labels["icon_delete"] = "Eliminar";
gantt.locale.labels["gantt_save_btn"] = "Guardar";
gantt.locale.labels["icon_save"] = "Guardar";
gantt.locale.labels["new_task"] = "Nueva tarea";
gantt.locale.labels["complete_button"] = "Completar";
gantt.locale.labels["days"] = "Días";
gantt.locale.labels["months"] = "Meses";
gantt.locale.labels["weeks"] = "Semanas";
gantt.locale.labels["years"] = "Años";

/* Eventos */
gantt.attachEvent("onGanttReady", function(){
    gantt.config.buttons_left = [
        "gantt_save_btn","gantt_cancel_btn",
        "complete_button"
    ];
    return true;
});

gantt.attachEvent("onLightboxButton", function(button_id, node, e){
    if(button_id == "complete_button"){
        var id = gantt.getState().lightbox;
        gantt.getTask(id).progress = 1;
        gantt.updateTask(id);
        gantt.hideLightbox();
        /* gantt.message({type:"info", text:"Tarea completada"}); */
        success("Tarea completada");
    }
    return true;
});


gantt.attachEvent("onBeforeTaskMove", function(id, parent, tindex){
    var task = gantt.getTask(id);
    if(task.parent != parent)
        return false;
    return true;
});

/* Eventos de resize del gantt */
gantt.attachEvent("onAfterTaskDrag", function(id, mode, e){
    if(mode == "resize" || mode == "move"){
        gantt.init("gantt_here");
    }
});
gantt.attachEvent("onAfterTaskAdd", function(id, mode, e){
    gantt.init("gantt_here");
});
gantt.attachEvent("onAfterTaskDelete", function(id, mode, e){
    gantt.init("gantt_here");
});

/* chart configuration and initialization */
gantt.plugins({ /* Debo implementar el save on demand para que esto funcione */
    /* undo: true, */
    quick_info:true
});
/* gantt.config.undo = false;
gantt.config.redo = true; */
gantt.config.readonly = (noEditable) ? true : false;
gantt.config.order_branch = true;// order tasks only inside a branch
gantt.config.scale_height = 30*2;
gantt.config.min_column_width = 50;
gantt.config.date_format = "%Y-%m-%d"; 
gantt.config.order_branch = true;
gantt.config.order_branch_free = true;
gantt.config.reorder_grid_columns = true;
gantt.config.cascade_delete = true;
gantt.init("gantt_here");

/* Load manual con datos de sprint y comisión */
hard_reload_gantt()

var dp = new gantt.dataProcessor("/");
gantt.config.scales = [
    {unit: "month", step: 1, format: "%F, %Y"},
    {unit: "day", step: 1, format: "%j, %D"}
];
dp.init(gantt);

//Agrego un header custom para una autenticación ultra-básica y datos de spint y comisión
dp.setTransactionMode({
    mode:"REST",
    headers:{
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    payload:{
        "sprintId":sprint,
        "comisionId":comision
    }
});

/* Retoques de envío de datos */
// Cargo el id del sprint antes de que se envíe la data
dp.attachEvent("onBeforeUpdate", function(id, state, data){
    data.sprintId = sprint;
    data.comisionId = comision;
    return true;
});

dp.attachEvent("onBeforeDataSending", function(id, state, data){
    data.sprintId = sprint;
    data.comisionId = comision;
    return true;
});

//error handler
dp.attachEvent("onAfterUpdate", function(id, action, tid, response){
    if(action == "error"){
        hard_reload_gantt();
        var box = gantt.alert({
            title:"Alert",
            type:"alert-error",
            text: response.msg
        });
    }
});


/* Funcionalidad custom */                                              
//gant.init("gantt_here"); //esto me refresca el gantt visualmente
//var tasks = gantt.eachTask(function(task){console.log(task);}) console.log(tasks) evaluar agregar hora y fecha y recorrer las tareas para detectar posible colisión

//Variables del timer
var progressBar = document.querySelector('.e-c-progress'); //Barra de progreso
var pointer = document.getElementById('e-pointer'); //extremo de barra
var length = Math.PI * 2 * 100; //radio
var intervalTimer; //Clock de muestreo en pantalla (tics de reloj)
var timeLeft;      //Tiempo restante
var wholeTime = 15; //Tiempo total
var isStarted = false; //flag de inicio de cuenta atrás
progressBar.style.strokeDasharray = length; //Seteo la longitud
var modal = null;

//Variables de control de tokens
var token_owner = null;
var success_timeout = null;
var error_timeout = null;

/* Funcionalidad de tokens, inicio de la página */
jQuery(function(){
    /* Autenticación básica via cookies, ideal sanctum en /api */
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    if (noEditable){
        hide($('#nueva-tarea'));
    }

    if(token_owner != 0 && !isProfesor){
        keepalive();
        setInterval(keepalive, 3000);
    }

    $('[data-bs-toggle="profile-info"]').popover({
        placement : 'bottom',
        trigger : 'hover',
        html : true
    });

    $('#nueva-tarea').click(custom_add);

    $('#refrescar-gantt').click(function(){
        hard_reload_gantt();
    });

    $('#soltar-token').click(function(){
        soltarToken();
    });

    $('#pedir-token').click(function(){
        pedirToken();
    });

    $('#aceptar-pedido').click(function(){
        votoPositivo();
    });

    $('#rechazar-pedido').click(function(){
        votoNegativo();
    });

    $('#modal-voto').on('hide.bs.modal', function (event) {
        kill_timer();
    });
});

function update(value, timePercent) {
    var offset = - length - length * value / (timePercent);
    progressBar.style.strokeDashoffset = offset; 
    pointer.style.transform = `rotate(${360 * value / (timePercent)}deg)`; 
};

function timer (seconds){ //counts time, takes seconds
    let remainTime = Date.now() + (seconds * 1000);
    update(seconds, wholeTime);
    
    intervalTimer = setInterval(function(){
        timeLeft = Math.round((remainTime - Date.now()) / 1000);
        if(timeLeft < 0){
            kill_timer();
            return ;
        }
        update(timeLeft, wholeTime);
    }, 1000);
}

function kill_timer(){
    clearInterval(intervalTimer);
    isStarted = false;
    update(wholeTime, wholeTime);
}

function kickstart_timer(time){
    //circle start
    wholeTime = time;
    update(wholeTime,wholeTime); //refreshes progress bar

    // Disparo la cuenta regresiva
    timer(wholeTime);
    isStarted = true;
}

/**
 * Interacción con el backend, sincronización de datos y manejo de tokens.
 */
function keepalive(){
    jQuery.ajax({
        type: "POST",
        url: "/token/keepalive",
        dataType: "text",
        timeout: 2500,
        success: function (response) {
          crawl(JSON.parse(response));
        },
        error: function (xhr, ajaxOptions, thrownError) {
          console.error(thrownError);
        }
    });
}

/**
 * Interpreta el objeto de respuesta de un keepalive.
 */
function crawl(status_comision){
    console.log(status_comision);

    if('token_owner' in status_comision){
        token_owner = status_comision.token_owner;
    }
    
    /* Actualizo el estado de cada compañero */
    if('data_compañeros' in status_comision){
        status_comision.data_compañeros.forEach(compa => {
            if(compa.last_seen != "gone"){
                $('#status-'+compa.id).removeClass("gone");
                $('#imagen-'+compa.id).addClass("border border-3 border-success");
            }else{
                $('#status-'+compa.id).addClass("gone");
                $('#imagen-'+compa.id).removeClass("border border-3 border-success");
            }
            if(token_owner == compa.id){
                $('#token-'+compa.id).removeClass("d-none");
                $('#token-msg-'+compa.id).removeClass("d-none");
            }else{
                $('#token-'+compa.id).addClass("d-none");
                $('#token-msg-'+compa.id).addClass("d-none");
            }
        });
    }

    /* Recarga de datos disparada por backend, el metodo /data limpia la caché */
    if('datos_dirty' in status_comision && status_comision.datos_dirty == 1){
        hard_reload_gantt();
    }

    /* Manejo de readonly en base al token_owner */
    //La seguridad la proponen los endpoint de actualización de datos, 
    //cocinar un current_user_id solo rompe el front end.
    if('token_owner' in status_comision && status_comision.token_owner == current_user_id && gantt.config.readonly == true){
        hide($('#pedir-token'));
        show($('#soltar-token'));
        hide($('#token-libre'));
        show($('#nueva-tarea'));
        gantt.config.readonly = false;
        noEditable = false;

        /* Fuerzo la recarga del gantt desde raíz */
        hard_reload_gantt();
    }else if('token_owner' in status_comision && status_comision.token_owner != current_user_id && gantt.config.readonly == false){ //Edit revoke
        show($('#pedir-token'));
        hide($('#soltar-token'));
        hide($('#token-libre'));
        hide($('#nueva-tarea'));
        gantt.config.readonly = true;
        noEditable = true;

        /* Fuerzo la recarga del gantt desde raíz */
        hard_reload_gantt();
    }else if(status_comision.token_owner == 0){
        hide($('#soltar-token'));
        show($('#token-libre'));
        
    }else if(status_comision.token_owner > 0){
        hide($('#token-libre'));
    }

    /* Manejo de votación */
    if(status_comision.votacion_en_curso == 1 && tablero_voto_visible == false && voto_emitido == false){
        //muestro el sistema de voto
        show_poll();
        hide_token_control();
        tablero_voto_visible = true;
    }else if(status_comision.votacion_en_curso == 0 || (status_comision.votacion_en_curso == 0 && tablero_voto_visible == false && voto_emitido == true)){
        hide_poll();
        show_token_control();
        tablero_voto_visible = false;
        if('resultado_votacion' in status_comision && voto_emitido == true){
            if(status_comision.resultado_votacion == 1){
                success("La votación ha terminado positiva y se actualizó el editor.");
            }else{
                error("La votación ha terminado negativa, se mantiene el editor.");
            }
        }
        voto_emitido = false;
    }
}

/* Fuerza una recarga de datos y repintado del gantt */
function hard_reload_gantt(){
    gantt.ajax.get({
        url: "/data",
        headers: {
            "X-Header-Sprint-Id": sprint,
            "X-Header-Comision-Id": comision
        }
    }).then(function (xhr) {
        if(noEditable) gantt.clearAll(); //hotfix para tareas eliminadas en alumnos en un gantt no editable
        gantt.parse(xhr.responseText);
        gantt.init("gantt_here");
    });
}

/* Suelta el token en caso de tenerlo */
function soltarToken(){
    jQuery.ajax({
        type: "POST",
        url: "/token/soltar",
        dataType: "text",
        timeout: 2500,
        success: function (response) {
          prescindirToken(JSON.parse(response));
        },
        error: function (xhr, ajaxOptions, thrownError) {
            console.error(thrownError);
            error(thrownError);
        }
    });
}

/* actualiza la interfaz gráfica para obtener el token */
function prescindirToken(res){
    if(res.cod != 1){
        error(res.action);
    }else{
        success(res.action);
        show($('#pedir-token'));
        hide($('#soltar-token'));
        show($('#token-libre'));
        hide($('#nueva-tarea'));
        gantt.config.readonly = true;
        noEditable = true;

        /* Fuerzo la recarga del gantt desde raíz */
        hard_reload_gantt();
    }
}

/* Suelta el token en caso de tenerlo */
function pedirToken(){
    jQuery.ajax({
        type: "POST",
        url: "/token/pedir",
        dataType: "text",
        timeout: 2500,
        success: function (response) {
          tomarToken(JSON.parse(response));
        },
        error: function (xhr, ajaxOptions, thrownError) {
          console.error(thrownError);
          error(thrownError);
        }
    });
}

function tomarToken(res){
    voto_emitido == false;
    if(res.cod == 0){
        error(res.action);
    }else if(res.cod == 1){
        hide($('#pedir-token'));
        show($('#soltar-token'));
        hide($('#token-libre'));
        show($('#nueva-tarea'));
        gantt.config.readonly = false;
        noEditable = false;

        /* Fuerzo la recarga del gantt desde raíz */
        hard_reload_gantt();
    }else{
        show_poll();
        hide_token_control();
        success(res.action);
    }
}

/* Suelta el token en caso de tenerlo */
function votoPositivo(){
    jQuery.ajax({
        type: "POST",
        url: "/token/aceptar",
        dataType: "text",
        timeout: 2500,
        success: function (response) {
            let res = JSON.parse(response);
            console.log(res);
            if(res.cod == 1){
                error(res.action);
            }else{
                hide($('#grupo-voto'));
                voto_emitido = true;
            }
        },
        error: function (xhr, ajaxOptions, thrownError) {
          console.error(thrownError);
          error(thrownError);
        }
    });
}

/* Suelta el token en caso de tenerlo */
function votoNegativo(){
    jQuery.ajax({
        type: "POST",
        url: "/token/rechazar",
        dataType: "text",
        timeout: 2500,
        success: function (response) {
            let res = JSON.parse(response);
            console.log(res);
            if(res.cod == 1){
                error(res.action);
            }else{
                hide($('#grupo-voto'));
                voto_emitido = true;
            }
        },
        error: function (xhr, ajaxOptions, thrownError) {
          console.error(thrownError);
          error(thrownError);
        }
    });
}

/* Muestra el manejador de voto */
function show_poll(){
    tablero_voto_visible = true;
    show($('#grupo-voto'));
    modal = new bootstrap.Modal(document.getElementById('modal-voto'), {
        keyboard: false
    });
    modal.show();
    kickstart_timer(15);
}

/* Esconde el manejador de voto */
function hide_poll(){
    tablero_voto_visible = false;
    if(modal!=null) modal.hide();
    hide($('#grupo-voto'));
};

function show_token_control(){
    show($('#grupo-token'));
}

function hide_token_control(){
    hide($('#grupo-token'));
}

/* Auxiliar, esconde un componente HTML */
function hide(el){
    el.addClass("d-none");
}

/* Auxiliar, muestra un componente HTML */
function show(el){
    el.removeClass("d-none");
}

/* Auxiliar, muestra alerta en pantalla */
function success(msg){
    $('#toast-error').hide();
    $('#toast-success').hide();
    $("#toast-success-body").html(msg);
    $("#toast-success").fadeIn( 200, function() {
        if(success_timeout != null) clearTimeout(success_timeout);
        if(error_timeout != null) clearTimeout(error_timeout);
        success_timeout = setTimeout(function(){
            $('#toast-success').fadeOut(200);
        },3500);
    });
}

/* Auxiliar, muestra error en pantalla */
function error(msg){
    $('#toast-error').hide();
    $('#toast-success').hide();
    $("#toast-error-body").html(msg);
    $("#toast-error").fadeIn( 200, function() {
        if(error_timeout != null) clearTimeout(error_timeout);
        if(success_timeout != null) clearTimeout(success_timeout);
        error_timeout = setTimeout(function(){
            $('#toast-error').fadeOut(200);
        },3500);
    });
}