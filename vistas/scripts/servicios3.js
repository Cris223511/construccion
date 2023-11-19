var tabla;
var siguienteCorrelativo = "";

function actualizarCorrelativo() {
	$.post("../ajax/servicios.php?op=getLastCodigo", function (num) {
		console.log(num);
		siguienteCorrelativo = generarSiguienteCorrelativo(num);
		$("#codigo").val(siguienteCorrelativo);
	});
}

function generarSiguienteCorrelativo(correlativoActual) {
	const numeroActual = parseInt(correlativoActual, 10);
	const siguienteNumero = numeroActual + 1;
	const longitud = correlativoActual.length;
	const siguienteCorrelativo = String(siguienteNumero).padStart(longitud, '0');
	return siguienteCorrelativo;
}

function init() {
	mostrarform(false);
	listar();

	$("#formulario").on("submit", function (e) {
		guardaryeditar(e);
	});
	$('#mServicios').addClass("treeview active");

	actualizarCorrelativo();
}

function limpiar() {
	$("#idservicio").val("");
	$("#titulo").val("");
	$("#codigo").val(siguienteCorrelativo);
	$("#descripcion").val("");
	$("#costo").val("");
}

function mostrarform(flag) {
	limpiar();
	if (flag) {
		$("#listadoregistros").hide();
		$("#formularioregistros").show();
		$("#btnGuardar").prop("disabled", false);
		$("#btnagregar").hide();
	}
	else {
		$("#listadoregistros").show();
		$("#formularioregistros").hide();
		$("#btnagregar").show();
	}
}

function cancelarform() {
	limpiar();
	mostrarform(false);
}

function listar() {
	tabla = $('#tbllistado').dataTable(
		{
			"lengthMenu": [5, 10, 25, 75, 100],
			"aProcessing": true,
			"aServerSide": true,
			dom: '<Bl<f>rtip>',
			buttons: [
				'copyHtml5',
				'excelHtml5',
				'csvHtml5',
			],
			"ajax":
			{
				url: '../ajax/servicios.php?op=listar',
				type: "get",
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
			"iDisplayLength": 5,
			"order": [],
			"createdRow": function (row, data, dataIndex) {
				$(row).find('td:eq(0), td:eq(1), td:eq(2), td:eq(4), td:eq(5), td:eq(6), td:eq(7), td:eq(8)').addClass('nowrap-cell');
			}
		}).DataTable();
}

function guardaryeditar(e) {
	e.preventDefault();
	$("#btnGuardar").prop("disabled", true);
	var formData = new FormData($("#formulario")[0]);

	$.ajax({
		url: "../ajax/servicios.php?op=guardaryeditar",
		type: "POST",
		data: formData,
		contentType: false,
		processData: false,

		success: function (datos) {
			if (datos == "El nombre del servicio ya existe." || datos == "El código del servicio ya existe.") {
				bootbox.alert(datos);
				$("#btnGuardar").prop("disabled", false);
				return;
			}
			limpiar();
			bootbox.alert(datos);
			mostrarform(false);
			tabla.ajax.reload();
			actualizarCorrelativo();
		}
	});
}

function mostrar(idservicio) {
	$.post("../ajax/servicios.php?op=mostrar", { idservicio: idservicio }, function (data, status) {
		data = JSON.parse(data);
		mostrarform(true);

		console.log(data);

		$("#titulo").val(data.titulo);
		$("#codigo").val(data.codigo);
		$("#descripcion").val(data.descripcion);
		$("#costo").val(data.costo);
		$("#idservicio").val(data.idservicio);
	})
}

function desactivar(idservicio) {
	bootbox.confirm("¿Está seguro de desactivar el servicio?", function (result) {
		if (result) {
			$.post("../ajax/servicios.php?op=desactivar", { idservicio: idservicio }, function (e) {
				bootbox.alert(e);
				tabla.ajax.reload();
			});
		}
	})
}

function activar(idservicio) {
	bootbox.confirm("¿Está seguro de activar el servicio?", function (result) {
		if (result) {
			$.post("../ajax/servicios.php?op=activar", { idservicio: idservicio }, function (e) {
				bootbox.alert(e);
				tabla.ajax.reload();
			});
		}
	})
}

function eliminar(idservicio) {
	bootbox.confirm("¿Estás seguro de eliminar el servicio?", function (result) {
		if (result) {
			$.post("../ajax/servicios.php?op=eliminar", { idservicio: idservicio }, function (e) {
				bootbox.alert(e);
				tabla.ajax.reload();
				actualizarCorrelativo();
			});
		}
	})
}

init();