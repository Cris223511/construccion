var tabla;

function init() {
	mostrarform(false);
	mostrarform2(false);
	listar();

	$("#formulario").on("submit", function (e) {
		guardaryeditar(e);
	});

	$("#formulario2").on("submit", function (e) {
		guardaryeditar2(e);
	});

	$('#mPerfilUsuario').addClass("treeview active");
	$('#lLocalesDisponibles').addClass("active");

	cargarLocalesDisponibles();

	// $.post("../ajax/usuario.php?op=listarUsuariosActivos", function (r) {
	// 	console.log(r);
	// 	$("#idusuario_asignar").html(r);
	// 	$('#idusuario_asignar').selectpicker('refresh');
	// });
}

function cargarLocalesDisponibles() {
	// $.post("../ajax/localesDisponibles.php?op=selectLocalDisponible", function (r) {
	// 	console.log(r);
	// 	$("#idlocal_asignar").html(r);
	// 	$('#idlocal_asignar').selectpicker('refresh');
	// });
}

function limpiar() {
	$("#idlocal").val("");
	$("#titulo").val("");
	$("#local_ruc").val("");
	$("#descripcion").val("");

	$("#idlocal_asignar").val($("#idlocal_asignar option:first").val());
	$("#idlocal_asignar").selectpicker('refresh');

	$("#idusuario_asignar").val($("#idusuario_asignar option:first").val());
	$("#idusuario_asignar").selectpicker('refresh');
}

function mostrarform(flag) {
	limpiar();
	if (flag) {
		$("#listadoregistros").hide();
		$("#formularioregistros").show();
		$("#btnGuardar").prop("disabled", false);
		$("#btnagregar").hide();
		$("#btnasignar").hide();
	}
	else {
		$("#listadoregistros").show();
		$("#formularioregistros").hide();
		$("#btnagregar").show();
		$("#btnasignar").show();
	}
}

function mostrarform2(flag) {
	limpiar();
	if (flag) {
		$("#listadoregistros").hide();
		$("#formularioasignacion").show();
		$("#btnGuardar2").prop("disabled", false);
		$("#btnagregar").hide();
		$("#btnasignar").hide();
	}
	else {
		$("#listadoregistros").show();
		$("#formularioasignacion").hide();
		$("#btnagregar").show();
		$("#btnasignar").show();
	}
}

function cancelarform() {
	limpiar();
	mostrarform(false);
}

function cancelarform2() {
	limpiar();
	mostrarform2(false);
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
				url: '../ajax/localesDisponibles.php?op=listar',
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
				$(row).find('td:eq(0), td:eq(1), td:eq(2), td:eq(4), td:eq(5)').addClass('nowrap-cell');
			}
		}).DataTable();
}

function guardaryeditar(e) {
	e.preventDefault();
	$("#btnGuardar").prop("disabled", true);
	var formData = new FormData($("#formulario")[0]);

	if (formData.get("local_ruc").length < 11) {
		bootbox.alert("El RUC del local debe ser de 11 dígitos.");
		$("#btnGuardar").prop("disabled", false);
		return;
	}

	$.ajax({
		url: "../ajax/localesDisponibles.php?op=guardaryeditar",
		type: "POST",
		data: formData,
		contentType: false,
		processData: false,

		success: function (datos) {
			if (datos == "El nombre del local ya existe.") {
				bootbox.alert(datos);
				$("#btnGuardar").prop("disabled", false);
				return;
			}
			limpiar();
			bootbox.alert(datos);
			mostrarform(false);
			tabla.ajax.reload();
			cargarLocalesDisponibles();
		}
	});
}

function guardaryeditar2(e) {
	e.preventDefault();
	$("#btnGuardar2").prop("disabled", true);
	var formData = new FormData($("#formulario2")[0]);

	$.ajax({
		url: "../ajax/localesDisponibles.php?op=guardaryeditar2",
		type: "POST",
		data: formData,
		contentType: false,
		processData: false,

		success: function (datos) {
			limpiar();
			bootbox.alert(datos);
			mostrarform2(false);
			tabla.ajax.reload();
			cargarLocalesDisponibles();
		}
	});
}

function mostrar(idlocal) {
	$.post("../ajax/localesDisponibles.php?op=mostrar", { idlocal: idlocal }, function (data, status) {
		// console.log(data);
		data = JSON.parse(data);
		mostrarform(true);

		console.log(data);

		$("#titulo").val(data.titulo);
		$("#local_ruc").val(data.local_ruc);
		$("#descripcion").val(data.descripcion);
		$("#idlocal").val(data.idlocal);
	})
}

function desactivar(idlocal) {
	bootbox.confirm("¿Está seguro de desactivar el local?", function (result) {
		if (result) {
			$.post("../ajax/localesDisponibles.php?op=desactivar", { idlocal: idlocal }, function (e) {
				bootbox.alert(e);
				tabla.ajax.reload();
				cargarLocalesDisponibles();
			});
		}
	})
}

function activar(idlocal) {
	bootbox.confirm("¿Está seguro de activar el local?", function (result) {
		if (result) {
			$.post("../ajax/localesDisponibles.php?op=activar", { idlocal: idlocal }, function (e) {
				bootbox.alert(e);
				tabla.ajax.reload();
				cargarLocalesDisponibles();
			});
		}
	})
}

function eliminar(idlocal) {
	bootbox.confirm("¿Estás seguro de eliminar el local?", function (result) {
		if (result) {
			$.post("../ajax/localesDisponibles.php?op=eliminar", { idlocal: idlocal }, function (e) {
				bootbox.alert(e);
				tabla.ajax.reload();
				cargarLocalesDisponibles();
			});
		}
	})
}

init();