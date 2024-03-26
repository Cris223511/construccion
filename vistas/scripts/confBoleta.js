objSelects = {};

//Función que se ejecuta al inicio
function init() {
	$("#formulario").on("submit", function (e) {
		guardaryeditar(e);
	});
	$('#mPerfilUsuario').addClass("treeview active");
	$('#lConfBoleta').addClass("active");

	mostrar();
}

function guardaryeditar(e) {
	e.preventDefault(); //No se activará la acción predeterminada del evento
	$("#btnGuardar").prop("disabled", true);
	var formData = new FormData($("#formulario")[0]);

	$.ajax({
		url: "../ajax/confBoleta.php?op=guardaryeditar",
		type: "POST",
		data: formData,
		contentType: false,
		processData: false,
		success: function (datos) {
			if (!datos) {
				console.log("No se recibieron datos del servidor.");
				$("#btnGuardar").prop("disabled", false);
				return;
			} else {
				bootbox.alert(datos);
				$("#btnGuardar").prop("disabled", false);
				mostrar();
			}
		}
	});
}

function mostrar() {
	$.post("../ajax/confBoleta.php?op=mostrar", function (data, status) {
		data = JSON.parse(data);
		console.log(data);
		$("#idreporte").val(data.idreporte);
		$("#titulo").val(data.titulo);
		$("#ruc").val(data.ruc);
		$("#direccion").val(data.direccion);
		$("#telefono").val(data.telefono);
		$("#email").val(data.email);
		$("#imagenmuestra").attr("src", "../files/logo_reportes/" + data.imagen);
		$("#imagenactual").val(data.imagen);
	});
}

document.addEventListener('DOMContentLoaded', function () {
	init();
});