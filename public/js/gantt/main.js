/* SYNC vars */
var voto_emitido = false;
var tablero_voto_visible = false;

/* Layout custom */
gantt.config.layout = {
    css: "gantt_container",
    cols: [
        {
            width:400,
            min_width:400,
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

var colHeader;
if (!noEditable){
    colHeader = 
                `<div class="gantt_grid_head_cell gantt_grid_head_add" onclick="custom_add()">
                    <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor" class="bi bi-plus-circle-fill" viewBox="0 0 16 16">
                        <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM8.5 4.5a.5.5 0 0 0-1 0v3h-3a.5.5 0 0 0 0 1h3v3a.5.5 0 0 0 1 0v-3h3a.5.5 0 0 0 0-1h-3v-3z"/>
                    </svg>
                </div>`,colContent = "";    
}else{
    colHeader = "",colContent = "";
}
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
	{name: "text", tree: true, width: '*', resize: true},
	{name: "start_date", align: "center", resize: true},
	{name: "duration", align: "center"},
	{
		name: "buttons",
		label: colHeader,
		width: 75,
		template: colContent
	}
];
if (noEditable){
    gantt.config.columns.pop();
}

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
        gantt.message({type:"info", text:"Tarea completada"});
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
    if(mode == "resize"){
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
//var tasks = gantt.getTaskByTime(); console.log(tasks) evaluar agregar hora y fecha y recorrer las tareas para detectar posible colisión
// Implementar función de achicado o agrandado de task list jugando con la variable gantt.config.layout[cols][0][width] y forzar un refresh del gantt


/* Funcionalidad de tokens */
jQuery(function(){
    /* Autenticación básica via cookies */
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    if(token_owner != 0){
        keepalive();
        setInterval(keepalive, 3000);
    }

    $('[data-bs-toggle="profile-info"]').popover({
        placement : 'bottom',
        trigger : 'hover',
        html : true
    });

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
});

/**
 * Interacción con el backend, sincronización de datos y manejo de tokens.
 */
function keepalive(){
    // Agregar control de timeout (tornar gantt no editable o algo así)
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
        });
    }

    /* Recarga de datos disparada por backend, el metodo /data limpia la caché */
    if('datos_dirty' in status_comision && status_comision.datos_dirty == 1){
        console.debug("Recargo el gantt");
        hard_reload_gantt();
    }

    /* Manejo de readonly en base al token_owner */
    //La seguridad la proponen los endpoint de actualización de datos, 
    //cocinar un current_user_id solo rompe el front end.
    if('token_owner' in status_comision && status_comision.token_owner == current_user_id && gantt.config.readonly == true){
        hide($('#pedir-token'));
        show($('#soltar-token'));
        hide($('#token-libre'));
        console.log("Vuelvo a tornar readable el gantt");
        gantt.config.readonly = false;
        noEditable = false;

        /* Rearmo los headers */
        colHeader = 
                `<div class="gantt_grid_head_cell gantt_grid_head_add" onclick="custom_add()">
                    <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor" class="bi bi-plus-circle-fill" viewBox="0 0 16 16">
                        <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM8.5 4.5a.5.5 0 0 0-1 0v3h-3a.5.5 0 0 0 0 1h3v3a.5.5 0 0 0 1 0v-3h3a.5.5 0 0 0 0-1h-3v-3z"/>
                    </svg>
                </div>`,colContent = ""; 
        gantt.config.columns = [
            {name: "text", tree: true, width: '*', resize: true},
            {name: "start_date", align: "center", resize: true},
            {name: "duration", align: "center"},
            {
                name: "buttons",
                label: colHeader,
                width: 75,
                template: colContent
            }
        ];

        /* Fuerzo la recarga del gantt desde raíz */
        hard_reload_gantt();
    }else if('token_owner' in status_comision && status_comision.token_owner != current_user_id && gantt.config.readonly == false){ //Edit revoke
        show($('#pedir-token'));
        hide($('#soltar-token'));
        hide($('#token-libre'));
        gantt.config.readonly = true;
        noEditable = true;
        colHeader = "",colContent = "";
        gantt.config.columns = [
            {name: "text", tree: true, width: '*', resize: true},
            {name: "start_date", align: "center", resize: true},
            {name: "duration", align: "center"}
        ];

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
    }else if(status_comision.votacion_en_curso == 1 && tablero_voto_visible == true && voto_emitido == true){
        //se me pasó la votación
        hide_poll();
        show_token_control();
        tablero_voto_visible = false;
        voto_emitido = false;
    }else if(status_comision.votacion_en_curso == 0 || (status_comision.votacion_en_curso == 0 && tablero_voto_visible == false && voto_emitido == true)){
        hide_poll();
        show_token_control();
        tablero_voto_visible = false;
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
    console.log(res);
    if(res.cod != 1){
        error(res.action);
    }else{
        success(res.action);
        show($('#pedir-token'));
        hide($('#soltar-token'));
        show($('#token-libre'));
        gantt.config.readonly = true;
        noEditable = true;
        colHeader = "",colContent = "";
        gantt.config.columns = [
            {name: "text", tree: true, width: '*', resize: true},
            {name: "start_date", align: "center", resize: true},
            {name: "duration", align: "center"}
        ];

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
    console.log(res);
    if(res.cod == 0){
        error(res.action);
    }else if(res.cod == 1){
        success(res.action);
        hide($('#pedir-token'));
        show($('#soltar-token'));
        hide($('#token-libre'));
        console.log("Vuelvo a tornar readable el gantt");
        gantt.config.readonly = false;
        noEditable = false;

        /* Rearmo los headers */
        colHeader = 
                `<div class="gantt_grid_head_cell gantt_grid_head_add" onclick="custom_add()">
                    <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor" class="bi bi-plus-circle-fill" viewBox="0 0 16 16">
                        <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM8.5 4.5a.5.5 0 0 0-1 0v3h-3a.5.5 0 0 0 0 1h3v3a.5.5 0 0 0 1 0v-3h3a.5.5 0 0 0 0-1h-3v-3z"/>
                    </svg>
                </div>`,colContent = ""; 
        gantt.config.columns = [
            {name: "text", tree: true, width: '*', resize: true},
            {name: "start_date", align: "center", resize: true},
            {name: "duration", align: "center"},
            {
                name: "buttons",
                label: colHeader,
                width: 75,
                template: colContent
            }
        ];

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
          console.log(JSON.parse(response));
          hide_poll();
          voto_emitido = true;
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
          console.log(JSON.parse(response));
          hide_poll();
          voto_emitido = true;
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
}

/* Esconde el manejador de voto */
function hide_poll(){
    tablero_voto_visible = false;
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
        setTimeout(function(){
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
        setTimeout(function(){
            $('#toast-error').fadeOut(200);
        },3500);
    });
}