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

    $('#mReporte').addClass("treeview active");
    $('#lReporteE').addClass("active");
}

function listar() {
    let param1 = "";
    let param2 = "";
    let param3 = "";
    let param4 = "";
    let param5 = "";

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
                url: '../ajax/reporte.php?op=listarEntradas',
                type: "get",
                data: { param1: param1, param2: param2, param3: param3, param4: param4, param5: param5 },
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
    const selects = ["fecha_inicio", "fecha_fin", "documentoBuscar", "localBuscar", "usuarioBuscar"];

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

    // Obtener los selectores
    const fecha_inicio = document.getElementById("fecha_inicio");
    const fecha_fin = document.getElementById("fecha_fin");
    const documentoBuscar = document.getElementById("documentoBuscar");
    const localBuscar = document.getElementById("localBuscar");
    const usuarioBuscar = document.getElementById("usuarioBuscar");

    if (fecha_inicio.value == "" && fecha_fin.value == "" && documentoBuscar.value == "" && localBuscar.value == "" && usuarioBuscar.value == "") {
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
                url: '../ajax/reporte.php?op=listarEntradas',
                type: "get",
                data: { param1: param1, param2: param2, param3: param3, param4: param4, param5: param5 },
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