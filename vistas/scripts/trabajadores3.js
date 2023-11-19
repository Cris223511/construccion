var tabla;

function init() {
	mostrarform(false);
	listar();

	$("#formulario").on("submit", function (e) {
		guardaryeditar(e);
	});

	$.post("../ajax/locales.php?op=selectLocalesUsuario", function (r) {
		console.log(r);
		$("#idlocal").html(r);
		$('#idlocal').selectpicker('refresh');
	});

	$('#mPersonas').addClass("treeview active");
	$('#lTrabajadores').addClass("active");
}

function limpiar() {
	$("#idtrabajador").val("");
	$("#nombre").val("");
	$("#idlocal").val(0);
	$('#idlocal').selectpicker('refresh');
	$("#tipo_documento").val("");
	$("#num_documento").val("");
	$("#telefono").val("");
	$("#email").val("");
	$("#fecha_nac").val("");
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
				url: '../ajax/trabajadores.php?op=listar',
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
				$(row).find('td:eq(0), td:eq(1), td:eq(2), td:eq(3), td:eq(5), td:eq(6), td:eq(7), td:eq(8)').addClass('nowrap-cell');
			}
		}).DataTable();
}

function guardaryeditar(e) {
	e.preventDefault();
	$("#btnGuardar").prop("disabled", true);
	var formData = new FormData($("#formulario")[0]);

	$.ajax({
		url: "../ajax/trabajadores.php?op=guardaryeditar",
		type: "POST",
		data: formData,
		contentType: false,
		processData: false,

		success: function (datos) {
			if (datos == "El número de documento que ha ingresado ya existe.") {
				bootbox.alert(datos);
				$("#btnGuardar").prop("disabled", false);
				return;
			}
			limpiar();
			bootbox.alert(datos);
			mostrarform(false);
			tabla.ajax.reload();
		}
	});
}

function mostrar(idtrabajador) {
	$.post("../ajax/trabajadores.php?op=mostrar", { idtrabajador: idtrabajador }, function (data, status) {
		data = JSON.parse(data);
		mostrarform(true);

		console.log(data);

		$("#nombre").val(data.nombre);
		$("#idlocal").val(data.idlocal);
		$('#idlocal').selectpicker('refresh');
		$("#tipo_documento").val(data.tipo_documento);
		$("#num_documento").val(data.num_documento);
		$("#telefono").val(data.telefono);
		$("#email").val(data.email);
		$("#fecha_nac").val(data.fecha_nac);
		$("#idtrabajador").val(data.idtrabajador);
	})
}

function desactivar(idtrabajador) {
	bootbox.confirm("¿Está seguro de desactivar al trabajador?", function (result) {
		if (result) {
			$.post("../ajax/trabajadores.php?op=desactivar", { idtrabajador: idtrabajador }, function (e) {
				bootbox.alert(e);
				tabla.ajax.reload();
			});
		}
	})
}

function activar(idtrabajador) {
	bootbox.confirm("¿Está seguro de activar al trabajador?", function (result) {
		if (result) {
			$.post("../ajax/trabajadores.php?op=activar", { idtrabajador: idtrabajador }, function (e) {
				bootbox.alert(e);
				tabla.ajax.reload();
			});
		}
	})
}

function eliminar(idtrabajador) {
	bootbox.confirm("¿Estás seguro de eliminar al trabajador?", function (result) {
		if (result) {
			$.post("../ajax/trabajadores.php?op=eliminar", { idtrabajador: idtrabajador }, function (e) {
				bootbox.alert(e);
				tabla.ajax.reload();
			});
		}
	})
}

init();