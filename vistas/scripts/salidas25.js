var tabla;
var tabla2;
var letraCorrelativo = "";
var siguienteCorrelativo = "";

function nowrapCell() {
	var detallesTable = document.getElementById("detalles");
	var tdList = detallesTable.querySelectorAll("td");

	tdList.forEach(function (td) {
		td.classList.add("nowrap-cell");
	});
}

function bloquearCampos() {
	$("input, select, textarea").not("#fecha_hora, #local_ruc").prop("disabled", true);
}

function desbloquearCampos() {
	$("input, select, textarea").not("#fecha_hora, #local_ruc").prop("disabled", false);
}

function habilitarPersonales() {
	$("#idautorizado").prop("disabled", false);
	$("#idrecibido").prop("disabled", false);
}

function deshabilitarPersonales() {
	$("#idautorizado").prop("disabled", true);
	$("#idrecibido").prop("disabled", true);

	$("#idautorizado").empty().append('<option value="0">- Seleccione -</option>');
	$("#idrecibido").empty().append('<option value="0">- Seleccione -</option>');
}

function convertirMayus() {
	var inputCodigo = document.getElementById("cod_1");
	inputCodigo.value = inputCodigo.value.toUpperCase();
}

function onlyLetters() {
	var inputCodigo = document.getElementById("cod_1");
	inputCodigo.value = inputCodigo.value.replace(/[^A-Za-z]/g, '');
}

function actualizarCorrelativo() {
	$.post("../ajax/salidas.php?op=getLastNumCodigo", function (num) {
		console.log(num);
		const { letras, numeros } = separarLetrasYNumeros(num);
		letraCorrelativo = letras;
		siguienteCorrelativo = generarSiguienteCorrelativo(numeros);
		$("#cod_1").val(letraCorrelativo);
		$("#cod_2").val(siguienteCorrelativo);
	});
}

function separarLetrasYNumeros(correlativoActual) {
	const matches = correlativoActual.match(/([A-Za-z]+)([0-9]+)/);

	if (matches && matches.length === 3) {
		const letras = matches[1];  // Grupo 1: Letras
		const numeros = matches[2]; // Grupo 2: Números
		return { letras, numeros };
	} else {
		console.error("Formato de correlativo no válido");
		return { letras: "", numeros: "" };
	}
}

function init() {
	mostrarform(false);
	limpiar();
	listar();
	listarArticulos();
	actualizarFecha();
	deshabilitarPersonales();

	$("#formulario").on("submit", function (e) {
		guardaryeditar(e);
	});

	$("#formulario2").on("submit", function (e) {
		guardaryeditar2(e);
	});

	$("#formulario3").on("submit", function (e) {
		guardaryeditar3(e);
	});

	$("#formSunat").on("submit", function (e) {
		buscarSunat(e);
	});

	$('#mSalidas').addClass("treeview active");
	$('#lSalidas').addClass("active");
	$("#btnGuardar").hide();

	$('[data-toggle="popover"]').popover();

	$.post("../ajax/salidas.php?op=listarTodosActivos", function (data) {
		// console.log(data);
		const obj = JSON.parse(data);
		console.log(obj);

		const selects = {
			"idtipo": $("#idtipo"),
			"idmaquinaria": $("#idmaquinaria"),
			"idlocal": $("#idlocal"),
			"idlocal2": $("#idlocal2"),
			"idlocal3": $("#idlocal3"),
		};

		if (obj.hasOwnProperty('correlativo') && obj.correlativo.length > 0) {
			const correlativoActual = obj.correlativo[0].titulo || "PRO00000";
			const { letras, numeros } = separarLetrasYNumeros(correlativoActual);

			letraCorrelativo = letras;
			siguienteCorrelativo = generarSiguienteCorrelativo(numeros);

			$("#cod_1").val(letraCorrelativo);
			$("#cod_2").val(siguienteCorrelativo);
		} else {
			console.error("No se encontró el correlativo en el objeto");
		}

		for (const selectId in selects) {

			if (selects.hasOwnProperty(selectId)) {
				const select = selects[selectId];
				const atributo = selectId.replace('id', '');

				if (obj.hasOwnProperty(atributo)) {
					select.empty();
					select.html('<option value="">- Seleccione -</option>');

					obj[atributo].forEach(function (opcion) {
						if (atributo != "local" && atributo != "local2" && atributo != "local3") {
							select.append('<option value="' + opcion.id + '">' + opcion.titulo + '</option>');
						} else {
							select.append('<option value="' + opcion.id + '" data-local-ruc="' + opcion.ruc + '">' + opcion.titulo + '</option>');
						}
					});
					select.selectpicker('refresh');
				}
			}
		}

		$('#idautorizado').closest('.form-group').find('input[type="text"]').attr('onkeydown', 'checkEnter(event)');
		$('#idrecibido').closest('.form-group').find('input[type="text"]').attr('onkeydown', 'checkEnter(event)');

		$('#idmaquinaria').closest('.form-group').find('input[type="text"]').attr('onkeydown', 'agregarMaquinaria(event)');
		$('#idmaquinaria').closest('.form-group').find('input[type="text"]').attr('maxlength', '40');

		$('#idtipo').closest('.form-group').find('input[type="text"]').attr('onkeydown', 'agregarTipo(event)');
		$('#idtipo').closest('.form-group').find('input[type="text"]').attr('maxlength', '40');
	});

	$.post("../ajax/salidas.php?op=selectProducto", function (r) {
		$("#idproducto").html(r);
		$('#idproducto').selectpicker('refresh');
		actualizarRUC();
		actualizarRUC2();
		actualizarRUC3();
	});
}

function listarTodosActivos(selectId) {
	$.post("../ajax/salidas.php?op=listarTodosActivos", function (data) {
		const obj = JSON.parse(data);

		const select = $("#" + selectId);
		const atributo = selectId.replace('id', '');

		if (obj.hasOwnProperty(atributo)) {
			select.empty();
			select.html('<option value="">- Seleccione -</option>');
			obj[atributo].forEach(function (opcion) {
				if (atributo !== "almacen") {
					select.append('<option value="' + opcion.id + '">' + opcion.titulo + '</option>');
				}
			});
			select.selectpicker('refresh');
		}

		select.closest('.form-group').find('input[type="text"]').attr('onkeydown', 'agregar' + atributo.charAt(0).toUpperCase() + atributo.slice(1) + '(event)');
		select.closest('.form-group').find('input[type="text"]').attr('maxlength', '40');
		$("#" + selectId + ' option:last').prop("selected", true);
		select.selectpicker('refresh');
		select.selectpicker('toggle');
	});
}

function agregarMaquinaria(e) {
	let inputValue = $('#idmaquinaria').closest('.form-group').find('input[type="text"]');

	if (e.key === "Enter") {
		if ($('.no-results').is(':visible')) {
			e.preventDefault();
			$("#titulo2").val(inputValue.val());

			var formData = new FormData($("#formularioMaquinaria")[0]);

			$.ajax({
				url: "../ajax/maquinarias.php?op=guardaryeditar",
				type: "POST",
				data: formData,
				contentType: false,
				processData: false,

				success: function (datos) {
					datos = limpiarCadena(datos);
					if (!datos) {
						console.log("No se recibieron datos del servidor.");
						return;
					} else if (datos == "El nombre de la maquinaria ya existe.") {
						bootbox.alert(datos);
						return;
					} else {
						// bootbox.alert(datos);
						listarTodosActivos("idmaquinaria");
						$("#idmaquinaria2").val("");
						$("#titulo2").val("");
						$("#descripcion2").val("");
					}
				}
			});
		}
	}
}

function agregarTipo(e) {
	let inputValue = $('#idtipo').closest('.form-group').find('input[type="text"]');

	if (e.key === "Enter") {
		if ($('.no-results').is(':visible')) {
			e.preventDefault();
			$("#titulo3").val(inputValue.val());

			var formData = new FormData($("#formularioTipo")[0]);

			$.ajax({
				url: "../ajax/tipos.php?op=guardaryeditar",
				type: "POST",
				data: formData,
				contentType: false,
				processData: false,

				success: function (datos) {
					datos = limpiarCadena(datos);
					if (!datos) {
						console.log("No se recibieron datos del servidor.");
						return;
					} else if (datos == "El nombre del tipo ya existe.") {
						bootbox.alert(datos);
						return;
					} else {
						// bootbox.alert(datos);
						listarTodosActivos("idtipo");
						$("#idtipo2").val("");
						$("#titulo3").val("");
						$("#descripcion2").val("");
					}
				}
			});
		}
	}
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

function actualizarRUC2() {
	const selectLocal = document.getElementById("idlocal2");
	const localRUCInput = document.getElementById("local_ruc2");
	const selectedOption = selectLocal.options[selectLocal.selectedIndex];

	if (selectedOption.value !== "") {
		const localRUC = selectedOption.getAttribute('data-local-ruc');
		localRUCInput.value = localRUC;
	} else {
		localRUCInput.value = "";
	}
}

function actualizarRUC3() {
	const selectLocal = document.getElementById("idlocal3");
	const localRUCInput = document.getElementById("local_ruc3");
	const selectedOption = selectLocal.options[selectLocal.selectedIndex];

	if (selectedOption.value !== "") {
		const localRUC = selectedOption.getAttribute('data-local-ruc');
		localRUCInput.value = localRUC;
	} else {
		localRUCInput.value = "";
	}
}

function checkEnter(event) {
	if (event.key === "Enter") {
		if ($('.no-results').is(':visible')) {
			$('#myModal3').modal('show');
			limpiarModalClientes();
			$("#sunat").val("");
			console.log("di enter en idpersonal =)");
		}
	}
}

// CLIENTES NUEVOS (POR SUNAT)

function limpiarModalClientes() {
	$("#idpersonal2").val("");
	$("#nombre").val("");
	$("#tipo_documento").val("");
	$("#num_documento").val("");
	$("#direccion").val("");
	$("#telefono").val("");
	$("#email").val("");
	$("#descripcion2").val("");

	habilitarTodoModalCliente();

	$("#idlocal2").val($("#idlocal2 option:first").val());
	$("#idlocal2").selectpicker('refresh');

	$("#btnSunat").prop("disabled", false);
	$("#btnGuardarCliente").prop("disabled", true);

	actualizarRUC2();
}

function guardaryeditar2(e) {
	e.preventDefault();
	$("#btnGuardarCliente").prop("disabled", true);

	deshabilitarTodoModalCliente();
	var formData = new FormData($("#formulario2")[0]);
	habilitarTodoModalCliente();

	$.ajax({
		url: "../ajax/personales.php?op=guardaryeditar",
		type: "POST",
		data: formData,
		contentType: false,
		processData: false,

		success: function (datos) {
			datos = limpiarCadena(datos);
			if (!datos) {
				console.log("No se recibieron datos del servidor.");
				$("#btnGuardarCliente").prop("disabled", false);
				return;
			} else if (datos == "El número de documento que ha ingresado ya existe.") {
				bootbox.alert(datos);
				$("#btnGuardarCliente").prop("disabled", false);
				return;
			} else {
				bootbox.alert(datos);
				$('#myModal3').modal('hide');
				let idlocal = $("#idlocal").val();
				actualizarPersonales(idlocal);
				limpiarModalClientes();
				$("#sunat").val("");
			}
		}
	});
}

function buscarSunat(e) {
	e.preventDefault();
	var formData = new FormData($("#formSunat")[0]);
	limpiarModalClientes();
	$("#btnSunat").prop("disabled", true);

	$.ajax({
		url: "../ajax/salidas.php?op=consultaSunat",
		type: "POST",
		data: formData,
		contentType: false,
		processData: false,

		success: function (datos) {
			datos = limpiarCadena(datos);
			if (!datos) {
				console.log("No se recibieron datos del servidor.");
				limpiarModalClientes();
				return;
			} else if (datos == "DNI no encontrado" || datos == "RUC no encontrado") {
				limpiarModalClientes();
				bootbox.confirm({
					message: datos + ", ¿deseas crear un personal manualmente?",
					buttons: {
						cancel: {
							label: 'Cancelar',
						},
						confirm: {
							label: 'Aceptar',
						}
					},
					callback: function (result) {
						if (result) {
							(datos == "DNI no encontrado") ? $("#tipo_documento2").val("DNI") : $("#tipo_documento2").val("RUC");

							$("#tipo_documento2").trigger("change");

							let inputValue = $('#sunat').val();
							$("#num_documento2").val(inputValue);

							$('#myModal3').modal('hide');
							$('#myModal4').modal('show');
							$("#local_ruc3").prop("disabled", true);
						}
					}
				});
			} else if (datos == "El DNI debe tener 8 caracteres." || datos == "El RUC debe tener 11 caracteres.") {
				bootbox.alert(datos);
				limpiarModalClientes();
			} else {
				// console.log(datos);
				const obj = JSON.parse(datos);
				console.log(obj);

				$("#nombre").val(obj.nombre);
				$("#tipo_documento").val(obj.tipoDocumento == "1" ? "DNI" : "RUC");
				$("#num_documento").val(obj.numeroDocumento);
				$("#direccion").val(obj.direccion);
				$("#telefono").val(obj.telefono);
				$("#email").val(obj.email);

				// Deshabilitar los campos solo si están vacíos
				$("#nombre").prop("disabled", obj.hasOwnProperty("nombre") && obj.nombre !== "" ? true : false);
				$("#direccion").prop("disabled", obj.hasOwnProperty("direccion") && obj.direccion !== "" ? true : false);
				$("#telefono").prop("disabled", obj.hasOwnProperty("telefono") && obj.telefono !== "" ? true : false);
				$("#email").prop("disabled", obj.hasOwnProperty("email") && obj.email !== "" ? true : false);

				$("#idlocal2").prop("disabled", false);
				$("#descripcion2").prop("disabled", false);

				$("#idlocal2").val($("#idlocal2 option:first").val());
				$("#idlocal2").selectpicker('refresh');

				$("#sunat").val("");

				$("#btnSunat").prop("disabled", false);
				$("#btnGuardarCliente").prop("disabled", false);
			}
		}
	});
}

function agregarClienteManual() {
	limpiarModalClientes();
	$("#sunat").val("");
	$('#myModal3').modal('hide');
	$('#myModal4').modal('show');
	$("#local_ruc3").prop("disabled", true);
}

function habilitarTodoModalCliente() {
	$("#tipo_documento").prop("disabled", true);
	$("#num_documento").prop("disabled", true);
	$("#nombre").prop("disabled", true);
	$("#direccion").prop("disabled", true);
	$("#telefono").prop("disabled", true);
	$("#email").prop("disabled", true);
	$("#idlocal2").prop("disabled", true);
	$("#local_ruc2").prop("disabled", true);
	$("#descripcion2").prop("disabled", true);
}

function deshabilitarTodoModalCliente() {
	$("#tipo_documento").prop("disabled", false);
	$("#num_documento").prop("disabled", false);
	$("#nombre").prop("disabled", false);
	$("#direccion").prop("disabled", false);
	$("#telefono").prop("disabled", false);
	$("#email").prop("disabled", false);
	$("#idlocal2").prop("disabled", false);
	$("#local_ruc2").prop("disabled", false);
	$("#descripcion2").prop("disabled", false);
}

// CLIENTES NUEVOS (POR SI NO ENCUENTRA LA SUNAT)

function limpiarModalClientes2() {
	$("#idpersonal3").val("");
	$("#nombre2").val("");
	$("#tipo_documento2").val("");
	$("#num_documento2").val("");
	$("#direccion2").val("");
	$("#telefono2").val("");
	$("#email2").val("");
	$("#descripcion3").val("");

	$("#idlocal3").val($("#idlocal3 option:first").val());
	$("#idlocal3").selectpicker('refresh');

	$("#btnGuardarCliente2").prop("disabled", false);

	actualizarRUC3();
}

function guardaryeditar3(e) {
	e.preventDefault();
	$("#btnGuardarCliente2").prop("disabled", true);
	var formData = new FormData($("#formulario3")[0]);

	$.ajax({
		url: "../ajax/personales.php?op=guardaryeditar",
		type: "POST",
		data: formData,
		contentType: false,
		processData: false,

		success: function (datos) {
			datos = limpiarCadena(datos);
			if (!datos) {
				console.log("No se recibieron datos del servidor.");
				$("#btnGuardarCliente2").prop("disabled", false);
				return;
			} else if (datos == "El número de documento que ha ingresado ya existe.") {
				bootbox.alert(datos);
				$("#btnGuardarCliente2").prop("disabled", false);
				return;
			} else {
				bootbox.alert(datos);
				$('#myModal4').modal('hide');
				let idlocal = $("#idlocal").val();
				actualizarPersonales(idlocal);
				limpiarModalClientes2();
			}
		}
	});
}

function actualizarPersonales(idlocal) {
	return new Promise((resolve, reject) => {
		habilitarPersonales();
		$.post("../ajax/salidas.php?op=listarTodosLocalActivosPorUsuario", { idlocal: idlocal }, function (data) {
			console.log(data);
			const obj = JSON.parse(data);
			console.log(obj);

			const selects = {
				"idautorizado": $("#idautorizado"),
				"idrecibido": $("#idrecibido"),
			};

			for (const selectId in selects) {
				const select = selects[selectId];
				const atributo = selectId.replace('id', '');

				if (selects.hasOwnProperty(selectId)) {
					if (obj.hasOwnProperty(atributo)) {
						select.empty();
						select.html('<option value="0">- Seleccione -</option>');
						obj[atributo].forEach(function (opcion) {
							select.append('<option value="' + opcion.id + '">' + opcion.nombre + '</option>');
						});
						select.selectpicker('refresh');
						select.closest('.form-group').find('input[type="text"]').attr('onkeydown', 'checkEnter(event)');
					} else if (idlocal == 0) {
						select.empty();
						select.html('<option value="0">- Seleccione -</option>');
						select.selectpicker('refresh');
						deshabilitarPersonales();
						select.selectpicker('refresh');
						select.closest('.form-group').find('input[type="text"]').attr('onkeydown', 'checkEnter(event)');
					} else {
						select.empty();
						select.html('<option value="0">- Seleccione -</option>');
						select.selectpicker('refresh');
						select.closest('.form-group').find('input[type="text"]').attr('onkeydown', 'checkEnter(event)');
					}
				}
			}

			resolve();  // Resuelve la promesa una vez completado
		});
	});
}

function limpiar() {
	desbloquearCampos();
	actualizarFecha();
	deshabilitarPersonales();

	$("#cod_1").val(letraCorrelativo);
	$("#cod_2").val(siguienteCorrelativo);
	$("#codigo_producto").val("");
	$("#nombre").val("");
	$("#descripcion").val("");
	$("#ubicacion").val("");
	$("#print").hide();
	$("#idsalida").val("");
	$("#impuesto").val("0");
	$("#impuesto").selectpicker('refresh');

	$(".selectPersonal").hide();
	$("#selectMaquinaria").hide();

	$("#idtipo").val($("#idtipo option:first").val());
	$("#idtipo").selectpicker('refresh');
	$("#idlocal").val($("#idlocal option:first").val());
	$("#idlocal").selectpicker('refresh');
	$("#tipo_movimiento").val($("#tipo_movimiento option:first").val());
	$("#tipo_movimiento").selectpicker('refresh');
	$("#idautorizado").val($("#idautorizado option:first").val());
	$("#idautorizado").selectpicker('refresh');
	$("#idrecibido").val($("#idrecibido option:first").val());
	$("#idrecibido").selectpicker('refresh');
	$("#idmaquinaria").val($("#idmaquinaria option:first").val());
	$("#idmaquinaria").selectpicker('refresh');

	$(".filas").remove();
	$('#myModal').modal('hide');

	$("#total_compra").val("");
	$("#btnAgregarArt").show();
	$("#igv").html("S/. 0.00");
	$("#total").html("S/. 0.00");

	$("#btnGuardar").hide();
	$("#botonArt").show();
	$("#form_codigo_barra").show();
	$('#tblarticulos button').removeAttr('disabled');
	actualizarRUC();
	actualizarRUC2();
	actualizarRUC3();
}

function mostrarform(flag) {
	limpiar();
	if (flag) {
		$(".listadoregistros").hide();
		$("#formularioregistros").show();
		$("#btnGuardar").prop("disabled", false);
		$("#btnagregar").hide();
	}
	else {
		$(".listadoregistros").show();
		$("#formularioregistros").hide();
		$("#btnagregar").show();
	}
}

function cancelarform() {
	limpiar();
	mostrarform(false);
}

function listar() {
	$("#fecha_inicio").val("");
	$("#fecha_fin").val("");

	var fecha_inicio = $("#fecha_inicio").val();
	var fecha_fin = $("#fecha_fin").val();

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
				url: '../ajax/salidas.php?op=listar',
				data: { fecha_inicio: fecha_inicio, fecha_fin: fecha_fin },
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
				$(row).find('td:eq(0), td:eq(1), td:eq(3), td:eq(4), td:eq(5), td:eq(6), td:eq(7), td:eq(8), td:eq(9), td:eq(10)').addClass('nowrap-cell');
			}
		}).DataTable();
}

function buscar() {
	var fecha_inicio = $("#fecha_inicio").val();
	var fecha_fin = $("#fecha_fin").val();

	if (fecha_inicio == "" || fecha_fin == "") {
		alert("Los campos de fecha inicial y fecha final son obligatorios.");
		return;
	} else if (fecha_inicio > fecha_fin) {
		alert("La fecha inicial no puede ser mayor que la fecha final.");
		return;
	}

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
				url: '../ajax/salidas.php?op=listar',
				data: { fecha_inicio: fecha_inicio, fecha_fin: fecha_fin },
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
				$(row).find('td:eq(0), td:eq(1), td:eq(3), td:eq(4), td:eq(5), td:eq(6), td:eq(7), td:eq(8), td:eq(9), td:eq(10)').addClass('nowrap-cell');
			}
		}).DataTable();
}

function disableButton(button) {
	button.disabled = true;
}

function guardaryeditar(e) {
	e.preventDefault();
	$("#btnGuardar").prop("disabled", true);

	var letras = $("#cod_1").val();
	var numeros = $("#cod_2").val();
	var codigo = letras + numeros;

	var formData = new FormData($("#formulario")[0]);

	formData.append('codigo', codigo);

	$.ajax({
		url: "../ajax/salidas.php?op=guardaryeditar",
		type: "POST",
		data: formData,
		contentType: false,
		processData: false,

		success: function (datos) {
			if (datos == "Una de las cantidades superan al stock normal del artículo." || datos == "El N° de documento de la salida ya existe.") {
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

function mostrar(idsalida) {
	$("#form_codigo_barra").hide();
	$("#btnAgregarArt").hide();
	$("#btnCrearArt").hide();
	$("#btnGuardar").hide();

	$("#serie_comprobante").val("");
	$("#num_proforma").val("");

	$.post("../ajax/salidas.php?op=mostrar", { idsalida: idsalida }, function (data, status) {
		data = JSON.parse(data);
		mostrarform(true);

		actualizarPersonales(data.idlocal).then(() => {
			bloquearCampos();
			console.log(data);

			$("#idtipo").val(data.idtipo);
			$('#idtipo').selectpicker('refresh');
			$("#idlocal").val(data.idlocal);
			$('#idlocal').selectpicker('refresh');

			if (data.tipo_movimiento == "personal") {
				$("#tipo_movimiento").val('personal');
				$('#tipo_movimiento').selectpicker('refresh');
				$('#tipo_movimiento').trigger('onchange');
			} else {
				$("#tipo_movimiento").val('maquinaria');
				$('#tipo_movimiento').selectpicker('refresh');
				$('#tipo_movimiento').trigger('onchange');
			}

			$("#idautorizado").val(data.idautorizado);
			$('#idautorizado').selectpicker('refresh');
			$("#idrecibido").val(data.idrecibido);
			$('#idrecibido').selectpicker('refresh');
			$("#idmaquinaria").val(data.idmaquinaria);
			$('#idmaquinaria').selectpicker('refresh');

			const { letras, numeros } = separarLetrasYNumeros(data.codigo);
			// Establecer valores en los campos correspondientes
			$("#cod_1").val(letras);
			$("#cod_2").val(numeros);

			$("#codigo_producto").val(data.codigo_producto);
			$("#nombre").val(data.nombre);
			$("#descripcion").val(data.descripcion);
			$("#ubicacion").val(data.ubicacion);
			$("#fecha_hora").val(data.fecha_hora);
			$("#print").hide();

			var impuesto = parseInt(data.impuesto);
			$("#impuesto").val(impuesto);
			$("#impuesto").selectpicker('refresh');

			$("#idsalida").val(data.idsalida);

			$("#botonArt").hide();
			$("#form_codigo_barra").hide();
			actualizarRUC();

			$.post("../ajax/salidas.php?op=listarDetalle&id=" + idsalida, function (r) {
				// console.log(r);
				$("#detalles").html(r);
				nowrapCell();
			})
		})
	})
}

function desactivar(idsalida) {
	bootbox.confirm("¿Está seguro de desactivar la salida?", function (result) {
		if (result) {
			$.post("../ajax/salidas.php?op=desactivar", { idsalida: idsalida }, function (e) {
				bootbox.alert(e);
				tabla.ajax.reload();
			});
		}
	})
}

function activar(idsalida) {
	bootbox.confirm("¿Está seguro de activar la salida?", function (result) {
		if (result) {
			$.post("../ajax/salidas.php?op=activar", { idsalida: idsalida }, function (e) {
				bootbox.alert(e);
				tabla.ajax.reload();
			});
		}
	})
}

function eliminar(idsalida) {
	bootbox.confirm("¿Estás seguro de eliminar la salida?", function (result) {
		if (result) {
			$.post("../ajax/salidas.php?op=eliminar", { idsalida: idsalida }, function (e) {
				bootbox.alert(e);
				tabla.ajax.reload();
				actualizarCorrelativo();
			});
		}
	})
}

//Función ListarArticulos
function listarArticulos() {
	tabla2 = $('#tblarticulos').DataTable({
		"aProcessing": true,
		"aServerSide": true,
		"dom": 'Bfrtip',
		"buttons": [],
		"ajax": {
			url: '../ajax/salidas.php?op=listarArticulos',
			type: "GET",
			dataType: "json",
			error: function (e) {
				console.log(e.responseText);
			}
		},
		"bDestroy": true,
		"iDisplayLength": 5,//Paginación
		"order": [],
		"createdRow": function (row, data, dataIndex) {
			$(row).find('td:eq(0), td:eq(1), td:eq(2), td:eq(3), td:eq(4), td:eq(5), td:eq(6), td:eq(7), td:eq(8), td:eq(9), td:eq(10), td:eq(11, td:eq(12), td:eq(13), td:eq(14)').addClass('nowrap-cell');
		},
		"drawCallback": function (settings) {
			// Vuelve a habilitar los botones de los artículos
			$('#tblarticulos button[data-idarticulo]').removeAttr('disabled');

			// Obtén los detalles actuales
			var detalles = getDetalles();

			// Itera sobre cada detalle y deshabilita el botón correspondiente
			for (var i = 0; i < detalles.length; i++) {
				var idarticulo = detalles[i].idarticulo;
				$('#tblarticulos button[data-idarticulo="' + idarticulo + '"]').attr('disabled', true);
			}
		}
	});
}

function getDetalles() {
	var detalles = [];
	$("#detalles tbody tr").each(function (index) {
		var detalle = {
			idarticulo: $(this).find("input[name='idarticulo[]']").val(),
			cantidad: $(this).find("input[name='cantidad[]']").val(),
			codigo: $(this).find("input[name='codigo[]']").val(),
		};
		detalles.push(detalle);
	});
	return detalles;
}

var cont = 0;
var detalles = 0;

//$("#guardar").hide();
$("#btnGuardar").hide();

function agregarDetalle(idarticulo, articulo, categoria, marca, medida, stock, stock_minimo, precio_compra, codigo_producto, codigo, imagen) {
	var cantidad = 1;

	if (idarticulo != "") {
		var subtotal = cantidad * precio_compra;
		var fila = '<tr class="filas" id="fila' + cont + '">' +
			'<td><button type="button" class="btn btn-danger" onclick="eliminarDetalle(' + cont + ', ' + idarticulo + ')">X</button></td>' +
			'<td><img src="../files/articulos/' + imagen + '" height="50px" width="50px"></td>' +
			'<td><input type="hidden" name="idarticulo[]" value="' + idarticulo + '">' + articulo + '</td>' +
			'<td>' + categoria + '</td>' +
			'<td>' + marca + '</td>' +
			'<td>' + codigo_producto + '</td>' +
			'<td>' + codigo + '</td>' +
			'<td><input type="number" name="cantidad[]" id="cantidad[]" lang="en-US" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength); modificarSubototales();" maxlength="6" onkeydown="evitarNegativo(event)" onpaste="return false;" onDrop="return false;" step="any" min="0.1" required value="' + cantidad + '"></td>' +
			'<td><input type="number" name="precio_compra[]" id="precio_compra[]" lang="en-US" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength); modificarSubototales();" maxlength="6" onkeydown="evitarNegativo(event)" onpaste="return false;" onDrop="return false;" step="any" min="0.1" required value="' + (precio_compra == '' ? parseFloat(0).toFixed(2) : precio_compra) + '"></td>' +
			'<td>' + medida + '</td>' +
			'<td>' + stock + '</td>' +
			'<td>' + stock_minimo + '</td>' +
			'<td class="nowrap-cell"><span name="subtotal" id="subtotal' + cont + '">' + subtotal + '</span></td>' +
			'</tr>';
		cont++;
		detalles = detalles + 1;
		$('#detalles').append(fila);
		modificarSubototales();
		evitarCaracteresEspecialesCamposNumericos();
		aplicarRestrictATodosLosInputs();
		console.log("Deshabilito a: " + idarticulo + " =)");
	}
	else {
		alert("Error al ingresar el detalle, revisar los datos del artículo");
	}

	nowrapCell();
}

function modificarSubototales() {
	var cant = document.getElementsByName("cantidad[]");
	var prec = document.getElementsByName("precio_compra[]");
	var sub = document.getElementsByName("subtotal");

	for (var i = 0; i < cant.length; i++) {
		var inpC = cant[i];
		var inpP = prec[i];
		var inpS = sub[i];

		inpS.value = (inpC.value * inpP.value);
		document.getElementsByName("subtotal")[i].innerHTML = inpS.value.toFixed(2);
	}
	calcularTotales();
}

function calcularTotales() {
	var sub = document.getElementsByName("subtotal");
	var total = 0.0;
	var igv = 0.0;

	for (var i = 0; i < sub.length; i++) {
		total += document.getElementsByName("subtotal")[i].value;
	}

	var impuesto = document.getElementById("impuesto").value;

	igv = impuesto == 18 ? total * 0.18 : total * 0;
	total = impuesto == 18 ? total + (total * 0.18) : total;

	$("#igv").html("S/. " + igv.toFixed(2));
	$("#total").html("S/. " + total.toFixed(2));
	$("#total_compra").val(total.toFixed(2));
	evaluar();
}

function llenarTabla() {
	var idarticulo = $('#idproducto').val();
	console.log(idarticulo);

	if (idarticulo == "") {
		console.log("no hago nada =)");
		return;
	}

	// Función para verificar si el idarticulo ya existe en el tbody
	const articuloExistente = () => {
		let tabla = document.querySelector("#detalles tbody");
		let inputs = tabla.querySelectorAll('input[name="idarticulo[]"]');
		return Array.from(inputs).some(input => input.value === idarticulo);
	};

	if (articuloExistente()) {
		alert("No puedes agregar el mismo artículo dos veces.");
		// Resetear el valor del select
		$('#idproducto').val($("#idproducto option:first").val());
		$("#idproducto").selectpicker('refresh');
	} else {
		$('#idproducto').prop("disabled", true);
		$.ajax({
			url: '../ajax/salidas.php?op=listarProductos',
			type: 'GET',
			dataType: 'json',
			data: { idarticulo: idarticulo },
			success: function (e) {
				console.log(e);
				$('#idproducto').prop("disabled", false);
				console.log("Envío esto al servidor =>", e[0].idarticulo, e[0].articulo, e[0].categoria, e[0].marca, e[0].medida, e[0].stock, e[0].stock_minimo, e[0].precio_compra, e[0].codigo_producto, e[0].codigo, e[0].imagen);

				// Resetear el valor del select
				$('#idproducto').val($("#idproducto option:first").val());
				$("#idproducto").selectpicker('refresh');

				agregarDetalle(e[0].idarticulo, e[0].articulo, e[0].categoria, e[0].marca, e[0].medida, e[0].stock, e[0].stock_minimo, e[0].precio_compra, e[0].codigo_producto, e[0].codigo, e[0].imagen);

				$('#tblarticulos button[data-idarticulo="' + idarticulo + '"]').attr('disabled', 'disabled');
				console.log("Deshabilito a: " + idarticulo + " =)");
			},
			error: function (e) {
				console.log(e.responseText);
			}
		});
	}
}

function evaluar() {
	if (detalles > 0) {
		$("#btnGuardar").show();
		$("#btnGuardar").prop("disabled", false);

	}
	else {
		$("#btnGuardar").prop("disabled", false);
		$("#btnGuardar").hide();

		cont = 0;
	}
}

function evaluarMetodo() {
	var tipoMovimiento = $("#tipo_movimiento").val();
	$(".selectPersonal").hide();
	$("#selectMaquinaria").hide();

	$("#idautorizado").val($("#idautorizado option:first").val());
	$("#idautorizado").selectpicker('refresh');
	$("#idrecibido").val($("#idrecibido option:first").val());
	$("#idrecibido").selectpicker('refresh');
	$("#idmaquinaria").val($("#idmaquinaria option:first").val());
	$("#idmaquinaria").selectpicker('refresh');

	$("#idmaquinaria").attr("required", "required");
	$("#idautorizado").attr("required", "required");
	$("#idrecibido").attr("required", "required");

	if (tipoMovimiento === "personal") {
		$(".selectPersonal").show();
		$("#idmaquinaria").removeAttr("required");
		$("#idautorizado").removeAttr("required");
	} else if (tipoMovimiento === "maquinaria") {
		$(".selectPersonal").show();
		$("#selectMaquinaria").show();
		$("#idautorizado").removeAttr("required");
		$("#idrecibido").removeAttr("required");
	}
	else {
		$("#idmaquinaria").attr("required", "required");
		$("#idautorizado").attr("required", "required");
		$("#idrecibido").attr("required", "required");
		$(".selectPersonal").hide();
		$("#selectMaquinaria").hide();
	}
}

function eliminarDetalle(indice, idarticulo) {
	$("#fila" + indice).remove();
	$('#tblarticulos button[data-idarticulo="' + idarticulo + '"]').removeAttr('disabled');
	console.log("Habilito a: " + idarticulo + " =)");
	detalles = detalles - 1;
	evaluar();
}

init();