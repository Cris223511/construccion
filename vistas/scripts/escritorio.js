//Función que se ejecuta al inicio
function init() {
    $('#mEscritorio').addClass("treeview active");
}

function listar() {
    let param1 = "";
    let param2 = "";

    const fecha_inicio = document.getElementById("fecha_inicio").value;
    const fecha_fin = document.getElementById("fecha_fin").value;

    param1 = fecha_inicio || '';
    param2 = fecha_fin || '';

    $.ajax({
        url: '../ajax/consultas.php?op=listarEscritorio',
        type: 'POST',
        data: { param1: param1, param2: param2 },
        success: function (data) {
            console.log(data);
            const resultados = JSON.parse(data);
            console.log(resultados);

            $("#total_entradas").text(' ' + (resultados.total_entradas || 0));
            $("#cantidad_total_entradas").text(' ' + (resultados.cantidad_total_entradas || 0));
            $("#total_salidas").text(' ' + (resultados.total_salidas || 0));
            $("#cantidad_total_salidas").text(' ' + (resultados.cantidad_total_salidas || 0));
        },
        error: function (xhr, status, error) {
            console.error("Error en la petición AJAX:", error);
        }
    });
}

function resetear() {
    const selects = ["fecha_inicio", "fecha_fin"];

    for (const selectId of selects) {
        $("#" + selectId).val("");
        $("#" + selectId).selectpicker('refresh');
    }

    listar();
}

function buscar() {
    let param1 = "";
    let param2 = "";

    const fecha_inicio = document.getElementById("fecha_inicio").value;
    const fecha_fin = document.getElementById("fecha_fin").value;

    if (!fecha_inicio && !fecha_fin) {
        bootbox.alert("Debe seleccionar al menos un campo para realizar la búsqueda.");
        return;
    }

    if (fecha_inicio > fecha_fin) {
        bootbox.alert("La fecha inicial no puede ser mayor que la fecha final.");
        return;
    }

    param1 = fecha_inicio;
    param2 = fecha_fin;

    listar();
}

init();
