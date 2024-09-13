var tabla;

//Función que se ejecuta al inicio
function init() {
    listar();

    $.post("../ajax/locales.php?op=selectLocal", function (r) {
        console.log(r);
        $("#localBuscar").html(r);
        $('#localBuscar').selectpicker('refresh');
    });

    $.post("../ajax/usuario.php?op=selectUsuarios", function (r) {
        console.log(r);
        $("#usuarioBuscar").html(r);
        $('#usuarioBuscar').selectpicker('refresh');
    })

    $.post("../ajax/tipos.php?op=selectTipos", function (r) {
        console.log(r);
        $("#tiposBuscar").html(r);
        $('#tiposBuscar').selectpicker('refresh');
    })

    $.post("../ajax/personales.php?op=selectPersonales", function (r) {
        console.log(r);
        $("#personalAutorizadoBuscar").html(r);
        $('#personalAutorizadoBuscar').selectpicker('refresh');
        $("#personalRecibidoBuscar").html(r);
        $('#personalRecibidoBuscar').selectpicker('refresh');
    })

    $.post("../ajax/maquinarias.php?op=selectMaquinarias", function (r) {
        console.log(r);
        $("#maquinariaBuscar").html(r);
        $('#maquinariaBuscar').selectpicker('refresh');
    })

    $('#mReporte').addClass("treeview active");
    $('#lReporteS').addClass("active");
}

function listar() {
    let param1 = "";
    let param2 = "";
    let param3 = "";
    let param4 = "";
    let param5 = "";
    let param6 = "";
    let param7 = "";
    let param8 = "";
    let param9 = "";
    let param10 = "";
    let param11 = "";

    tabla = $('#tbllistado').dataTable(
        {
            "lengthMenu": [10, 25, 75, 100],
            "aProcessing": true,
            "aServerSide": true,
            dom: '<Bl<f>rtip>',
            buttons: [
                'copyHtml5',
                'excelHtml5',
                'csvHtml5',
                {
                    'extend': 'pdfHtml5',
                    'orientation': 'landscape',
                    'exportOptions': {
                        'columns': ':not(:first-child)'
                    },
                    'customize': function (doc) {
                        doc.defaultStyle.fontSize = 8;
                        doc.styles.tableHeader.fontSize = 8;
                    },
                },
            ],
            "ajax":
            {
                url: '../ajax/reporte.php?op=listarSalidas',
                type: "get",
                data: { param1: param1, param2: param2, param3: param3, param4: param4, param5: param5, param6: param6, param7: param7, param8: param8, param9: param9, param10: param10, param11: param11 },
                dataType: "json",
                error: function (e) {
                    console.log(e.responseText);
                }
            },
            "language": {
                "lengthMenu": "Mostrar : _MENU_ registros",
                "buttons": {
                    "copyTitle": "Tabla Copiada",
                    "copySuccess": {
                        _: '%d líneas copiadas',
                        1: '1 línea copiada'
                    }
                }
            },
            "bDestroy": true,
            "iDisplayLength": 10,
            "order": [],
            "createdRow": function (row, data, dataIndex) {
                // $(row).find('td:eq(0), td:eq(1), td:eq(2), td:eq(3), td:eq(4), td:eq(5), td:eq(6), td:eq(7), td:eq(8), td:eq(9)').addClass('nowrap-cell');
            }
        }).DataTable();
}

function resetear() {
    const selects = ["fecha_inicio", "fecha_fin", "documentoBuscar", "localBuscar", "usuarioBuscar", "estadoBuscar", "tiposBuscar", "tipoMovimientoBuscar", "maquinariaBuscar", "personalAutorizadoBuscar", "personalRecibidoBuscar"];

    for (const selectId of selects) {
        $("#" + selectId).val("");
        $("#" + selectId).selectpicker('refresh');
    }

    listar();
}

function buscar() {
    let param1 = "";
    let param2 = "";
    let param3 = "";
    let param4 = "";
    let param5 = "";
    let param6 = "";
    let param7 = "";
    let param8 = "";
    let param9 = "";
    let param10 = "";
    let param11 = "";

    // Obtener los selectores
    const fecha_inicio = document.getElementById("fecha_inicio");
    const fecha_fin = document.getElementById("fecha_fin");
    const documentoBuscar = document.getElementById("documentoBuscar");
    const localBuscar = document.getElementById("localBuscar");
    const usuarioBuscar = document.getElementById("usuarioBuscar");
    const estadoBuscar = document.getElementById("estadoBuscar");
    const tiposBuscar = document.getElementById("tiposBuscar");
    const tipoMovimientoBuscar = document.getElementById("tipoMovimientoBuscar");
    const maquinariaBuscar = document.getElementById("maquinariaBuscar");
    const personalAutorizadoBuscar = document.getElementById("personalAutorizadoBuscar");
    const personalRecibidoBuscar = document.getElementById("personalRecibidoBuscar");

    if (fecha_inicio.value == "" && fecha_fin.value == "" && documentoBuscar.value == "" && localBuscar.value == "" && usuarioBuscar.value == "" && estadoBuscar.value == "" && tiposBuscar.value == "" && tipoMovimientoBuscar.value == "" && maquinariaBuscar.value == "" && personalAutorizadoBuscar.value == "" && personalRecibidoBuscar.value == "") {
        bootbox.alert("Debe seleccionar al menos un campo para realizar la búsqueda.");
        return;
    }

    if (fecha_inicio.value > fecha_fin.value) {
        bootbox.alert("La fecha inicial no puede ser mayor que la fecha final.");
        return;
    }

    param1 = fecha_inicio.value;
    param2 = fecha_fin.value;
    param3 = documentoBuscar.value;
    param4 = localBuscar.value;
    param5 = usuarioBuscar.value;
    param6 = estadoBuscar.value;
    param7 = tiposBuscar.value;
    param8 = tipoMovimientoBuscar.value;
    param9 = maquinariaBuscar.value;
    param10 = personalAutorizadoBuscar.value;
    param11 = personalRecibidoBuscar.value;

    tabla = $('#tbllistado').dataTable(
        {
            "lengthMenu": [10, 25, 75, 100],
            "aProcessing": true,
            "aServerSide": true,
            dom: '<Bl<f>rtip>',
            buttons: [
                'copyHtml5',
                'excelHtml5',
                'csvHtml5',
                {
                    'extend': 'pdfHtml5',
                    'orientation': 'landscape',
                    'exportOptions': {
                        'columns': ':not(:first-child)'
                    },
                    'customize': function (doc) {
                        doc.defaultStyle.fontSize = 8;
                        doc.styles.tableHeader.fontSize = 8;
                    },
                },
            ],
            "ajax":
            {
                url: '../ajax/reporte.php?op=listarSalidas',
                type: "get",
                data: { param1: param1, param2: param2, param3: param3, param4: param4, param5: param5, param6: param6, param7: param7, param8: param8, param9: param9, param10: param10, param11: param11 },
                dataType: "json",
                error: function (e) {
                    console.log(e.responseText);
                }
            },
            "language": {
                "lengthMenu": "Mostrar : _MENU_ registros",
                "buttons": {
                    "copyTitle": "Tabla Copiada",
                    "copySuccess": {
                        _: '%d líneas copiadas',
                        1: '1 línea copiada'
                    }
                }
            },
            "bDestroy": true,
            "iDisplayLength": 10,
            "order": [],
            "createdRow": function (row, data, dataIndex) {
                // $(row).find('td:eq(0), td:eq(1), td:eq(2), td:eq(3), td:eq(4), td:eq(5), td:eq(6), td:eq(7), td:eq(8), td:eq(9)').addClass('nowrap-cell');
            }
        }).DataTable();
}

init();