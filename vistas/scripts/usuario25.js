var tabla;
let select = $("#idlocal"); // select

//Función que se ejecuta al inicio
function init() {
	mostrarform(false);
	listar();

	$("#formulario").on("submit", function (e) {
		guardaryeditar(e);
	})

	$("#imagenmuestra").hide();
	//Mostramos los permisos
	$.post("../ajax/usuario.php?op=permisos&id=", function (r) {
		$("#permisos").html(r);
	});

	$('#mAcceso').addClass("treeview active");
	$('#lUsuarios').addClass("active");
}

function cargarLocalDisponible() {
	$("#locales").empty().append("Locales disponibles(*):");
	select.empty();
	// Cargamos los items al select "local principal"
	$.post("../ajax/locales.php?op=selectLocalDisponible", function (data) {
		// console.log(data);
		objSelects = JSON.parse(data);
		console.log(objSelects);
		if (objSelects.length != 0) {
			select.html('<option value="">- Seleccione -</option>');

			objSelects.locales.forEach(function (opcion) {
				select.append('<option value="' + opcion.idlocal + '" data-local-ruc="' + opcion.local_ruc + '">' + opcion.titulo + '</option>');
			});
			select.selectpicker('refresh');
		} else {
			console.log("no hay datos =)")
		}
		limpiar();
	});
}

function actualizarRUC() {
	const selectLocal = document.getElementById("idlocal");
	const localRUCInput = document.getElementById("local_ruc");
	const selectedOption = selectLocal.options[selectLocal.selectedIndex];

	if (selectedOption.value !== "") {
		const localRUC = selectedOption.getAttribute('data-local-ruc');
		localRUCInput.value = localRUC;
	} else {
		localRUCInput.value = "";
	}
}

//Función limpiar
function limpiar() {
	$("#nombre").val("");
	$("#idlocal").val($("#idlocal option:first").val());
	$("#idlocal").selectpicker('refresh');
	$("tipo_documento").val("");
	$("#num_documento").val("");
	$("#direccion").val("");
	$("#telefono").val("");
	$("#email").val("");
	$("#local_ruc").val("");
	$("#cargo").val("administrador");
	$("#cargo").selectpicker('refresh');
	$("#login").val("");
	$("#clave").val("");
	$("#imagen").val("");
	$("#imagenmuestra").attr("src", "");
	$("#imagenmuestra").hide();
	$("#imagenactual").val("");
	$("#idusuario").val("");
}

//Función mostrar formulario
function mostrarform(flag) {
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
		cargarLocalDisponible();

		// desenmarcamos los selects
		$("#permisos input[type='checkbox']").each(function () {
			$(this).prop('checked', false);
		});
	}
}

//Función cancelarform
function cancelarform() {
	limpiar();
	mostrarform(false);
}

//Función Listar
function listar() {
	tabla = $('#tbllistado').dataTable(
		{
			"lengthMenu": [5, 10, 25, 75, 100],//mostramos el menú de registros a revisar
			"aProcessing": true,//Activamos el procesamiento del datatables
			"aServerSide": true,//Paginación y filtrado realizados por el servidor
			dom: '<Bl<f>rtip>',//Definimos los elementos del control de tabla
			buttons: [
				'copyHtml5',
				'excelHtml5',
				'csvHtml5',
			],
			"ajax":
			{
				url: '../ajax/usuario.php?op=listar',
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
			"iDisplayLength": 5,//Paginación
			"order": [],
			"createdRow": function (row, data, dataIndex) {
				$(row).find('td:eq(1), td:eq(2), td:eq(3), td:eq(4), td:eq(5), td:eq(6), td:eq(7), td:eq(8), td:eq(9), td:eq(10), td:eq(11)').addClass('nowrap-cell');
			},
		}).DataTable();
}
//Función para guardar o editar

function guardaryeditar(e) {
	e.preventDefault(); //No se activará la acción predeterminada del evento
	$("#btnGuardar").prop("disabled", true);
	var formData = new FormData($("#formulario")[0]);

	$.ajax({
		url: "../ajax/usuario.php?op=guardaryeditar",
		type: "POST",
		data: formData,
		contentType: false,
		processData: false,

		success: function (datos) {
			if (datos == "El nombre del usuario que ha ingresado ya existe." || datos == "El número de documento que ha ingresado ya existe.") {
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

function mostrar(idusuario) {
	select.empty();
	$("#locales").empty().append("Local principal(*):");
	$.post("../ajax/locales.php?op=selectLocalUsuario", { idusuariolocal: idusuario }, function (r) {
		console.log(r);
		$("#idlocal").html(r);
		$('#idlocal').selectpicker('refresh');

		$.post("../ajax/usuario.php?op=mostrar", { idusuario: idusuario }, function (data, status) {
			// console.log(data);
			data = JSON.parse(data);
			console.log(data);
			mostrarform(true);

			$("#nombre").val(data.nombre);
			$("#tipo_documento").val(data.tipo_documento);
			$("#tipo_documento").trigger("change");
			$("#tipo_documento").selectpicker('refresh');
			$("#num_documento").val(data.num_documento);
			$("#direccion").val(data.direccion);
			$("#telefono").val(data.telefono);
			$("#email").val(data.email);
			$("#idlocal").val(data.idlocal);
			$("#idlocal").selectpicker('refresh');
			$("#local_ruc").val(data.local_ruc);
			$("#cargo").val(data.cargo);
			$("#cargo").selectpicker('refresh');
			$("#login").val(data.login);
			$("#clave").val(data.clave);
			$("#imagenmuestra").show();
			$("#imagenmuestra").attr("src", "../files/usuarios/" + data.imagen);
			$("#imagenactual").val(data.imagen);
			$("#idusuario").val(data.idusuario);
		});

		$.post("../ajax/usuario.php?op=permisos&id=" + idusuario, function (r) {
			$("#permisos").html(r);
		});
	});
}

//Función para desactivar registros
function desactivar(idusuario) {
	bootbox.confirm("¿Está seguro de desactivar el usuario?", function (result) {
		if (result) {
			$.post("../ajax/usuario.php?op=desactivar", { idusuario: idusuario }, function (e) {
				bootbox.alert(e);
				tabla.ajax.reload();
			});
		}
	})
}

//Función para activar registros
function activar(idusuario) {
	bootbox.confirm("¿Está seguro de activar el usuario?", function (result) {
		if (result) {
			$.post("../ajax/usuario.php?op=activar", { idusuario: idusuario }, function (e) {
				bootbox.alert(e);
				tabla.ajax.reload();
			});
		}
	})
}

//Función para eliminar los registros
function eliminar(idusuario) {
	bootbox.confirm("¿Estás seguro de eliminar el usuario?", function (result) {
		if (result) {
			$.post("../ajax/usuario.php?op=eliminar", { idusuario: idusuario }, function (e) {
				bootbox.alert(e);
				tabla.ajax.reload();
				cargarLocalDisponible();
			});
		}
	})
}

init();