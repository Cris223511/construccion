var tabla;

//Función que se ejecuta al inicio
function init() {
	mostrarform(false);
	listar();

	$("#formulario").on("submit", function (e) {
		guardaryeditar(e);
	})

	$("#imagenmuestra").hide();
	$('#mAlmacen').addClass("treeview active");
	$('#lArticulos').addClass("active");

	$.post("../ajax/articulo.php?op=listarTodosActivos", function (data) {
		// console.log(data)
		const obj = JSON.parse(data);
		console.log(obj);

		const selects = {
			"idmarca": $("#idmarca, #idmarcaBuscar"),
			"idcategoria": $("#idcategoria, #idcategoriaBuscar"),
			"idlocal": $("#idlocal"),
			"idmedida": $("#idmedida"),
			"idubicacion": $("#idubicacion"),
		};

		for (const selectId in selects) {
			if (selects.hasOwnProperty(selectId)) {
				const select = selects[selectId];
				const atributo = selectId.replace('id', '');

				if (obj.hasOwnProperty(atributo)) {
					select.empty();
					select.html('<option value="">- Seleccione -</option>');
					obj[atributo].forEach(function (opcion) {
						if (atributo != "local") {
							select.append('<option value="' + opcion.id + '">' + opcion.titulo + '</option>');
						} else {
							select.append('<option value="' + opcion.id + '" data-local-ruc="' + opcion.ruc + '">' + opcion.titulo + '</option>');
						}
					});
					select.selectpicker('refresh');
				}
			}
		}

		$("#idlocal").val($("#idlocal option:first").val());
		$("#idlocal").selectpicker('refresh');

		$('#idcategoria').closest('.form-group').find('input[type="text"]').attr('onkeydown', 'agregarCategoria(event)');
		$('#idcategoria').closest('.form-group').find('input[type="text"]').attr('maxlength', '40');

		$('#idmarca').closest('.form-group').find('input[type="text"]').attr('onkeydown', 'agregarMarca(event)');
		$('#idmarca').closest('.form-group').find('input[type="text"]').attr('maxlength', '40');

		$('#idmedida').closest('.form-group').find('input[type="text"]').attr('onkeydown', 'agregarMedida(event)');
		$('#idmedida').closest('.form-group').find('input[type="text"]').attr('maxlength', '40');

		$('#idubicacion').closest('.form-group').find('input[type="text"]').attr('onkeydown', 'agregarUbicacion(event)');
		$('#idubicacion').closest('.form-group').find('input[type="text"]').attr('maxlength', '40');

		actualizarRUC();
	});
}

function listarTodosActivos(selectId) {
	$.post("../ajax/articulo.php?op=listarTodosActivos", function (data) {
		const obj = JSON.parse(data);
		console.log("esto traigo =) =>", obj);

		const select = $("#" + selectId);
		const atributo = selectId.replace('id', '');

		if (obj.hasOwnProperty(atributo)) {
			select.empty();
			select.html('<option value="">- Seleccione -</option>');
			obj[atributo].forEach(function (opcion) {
				if (atributo !== "local") {
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

function agregarUbicacion(e) {
	let inputValue = $('#idubicacion').closest('.form-group').find('input[type="text"]');

	if (e.key === "Enter") {
		if ($('.no-results').is(':visible')) {
			e.preventDefault();
			$("#titulo4").val(inputValue.val());

			var formData = new FormData($("#formularioUbicacion")[0]);

			$.ajax({
				url: "../ajax/ubicaciones.php?op=guardaryeditar",
				type: "POST",
				data: formData,
				contentType: false,
				processData: false,

				success: function (datos) {
					datos = limpiarCadena(datos);
					if (!datos) {
						console.log("No se recibieron datos del servidor.");
						return;
					} else if (datos == "El nombre de la ubicación ya existe.") {
						bootbox.alert(datos);
						return;
					} else {
						// bootbox.alert(datos);
						listarTodosActivos("idubicacion");
						$("#idubicacion2").val("");
						$("#titulo4").val("");
					}
				}
			});
		}
	}
}

function agregarCategoria(e) {
	let inputValue = $('#idcategoria').closest('.form-group').find('input[type="text"]');

	if (e.key === "Enter") {
		if ($('.no-results').is(':visible')) {
			e.preventDefault();
			$("#titulo2").val(inputValue.val());

			var formData = new FormData($("#formularioCategoria")[0]);

			$.ajax({
				url: "../ajax/categoria.php?op=guardaryeditar",
				type: "POST",
				data: formData,
				contentType: false,
				processData: false,

				success: function (datos) {
					datos = limpiarCadena(datos);
					if (!datos) {
						console.log("No se recibieron datos del servidor.");
						return;
					} else if (datos == "El nombre de la categoría que ha ingresado ya existe.") {
						bootbox.alert(datos);
						return;
					} else {
						// bootbox.alert(datos);
						listarTodosActivos("idcategoria");
						$("#idcategoria2").val("");
						$("#titulo2").val("");
						$("#descripcion2").val("");
					}
				}
			});
		}
	}
}

function agregarMarca(e) {
	let inputValue = $('#idmarca').closest('.form-group').find('input[type="text"]');

	if (e.key === "Enter") {
		if ($('.no-results').is(':visible')) {
			e.preventDefault();
			$("#titulo3").val(inputValue.val());

			var formData = new FormData($("#formularioMarcas")[0]);

			$.ajax({
				url: "../ajax/marcas.php?op=guardaryeditar",
				type: "POST",
				data: formData,
				contentType: false,
				processData: false,

				success: function (datos) {
					datos = limpiarCadena(datos);
					if (!datos) {
						console.log("No se recibieron datos del servidor.");
						return;
					} else if (datos == "El nombre de la marca que ha ingresado ya existe.") {
						bootbox.alert(datos);
						return;
					} else {
						// bootbox.alert(datos);
						listarTodosActivos("idmarca");
						$("#idmarca3").val("");
						$("#titulo3").val("");
						$("#descripcion3").val("");
					}
				}
			});
		}
	}
}

function agregarMedida(e) {
	let inputValue = $('#idmedida').closest('.form-group').find('input[type="text"]');

	if (e.key === "Enter") {
		if ($('.no-results').is(':visible')) {
			e.preventDefault();
			$("#titulo4").val(inputValue.val());

			var formData = new FormData($("#formularioMedidas")[0]);

			$.ajax({
				url: "../ajax/medidas.php?op=guardaryeditar",
				type: "POST",
				data: formData,
				contentType: false,
				processData: false,

				success: function (datos) {
					datos = limpiarCadena(datos);
					if (!datos) {
						console.log("No se recibieron datos del servidor.");
						return;
					} else if (datos == "El nombre de la medida que ha ingresado ya existe.") {
						bootbox.alert(datos);
						return;
					} else {
						// bootbox.alert(datos);
						listarTodosActivos("idmedida");
						$("#idmedida4").val("");
						$("#titulo4").val("");
						$("#descripcion4").val("");
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

//Función limpiar
function limpiar() {
	$("#codigo").val("");
	$("#codigo_producto").val("");
	$("#nombre").val("");
	$("#local_ruc").val("");
	$("#descripcion").val("");
	$("#stock").val("");
	$("#stock_minimo").val("");
	$("#precio_compra").val("");
	// $("#precio_compra_mayor").val("");
	$("#imagenmuestra").attr("src", "");
	$("#imagenmuestra").hide();
	$("#imagenactual").val("");
	$("#imagen").val("");
	$("#talla").val("");
	$("#color").val("");
	$("#peso").val("");
	$("#fecha_emision").val("");
	$("#fecha_vencimiento").val("");
	$("#nota_1").val("");
	$("#nota_2").val("");
	$("#imei").val("");
	$("#serial").val("");
	$("#print").hide();
	$("#idarticulo").val("");

	$("#idcategoria").val($("#idcategoria option:first").val());
	$("#idcategoria").selectpicker('refresh');
	$("#idlocal").val($("#idlocal option:first").val());
	$("#idlocal").selectpicker('refresh');
	$("#idmarca").val($("#idmarca option:first").val());
	$("#idmarca").selectpicker('refresh');
	$("#idmedida").val($("#idmedida option:first").val());
	$("#idmedida").selectpicker('refresh');
	$("#idubicacion").val($("#idubicacion option:first").val());
	$("#idubicacion").selectpicker('refresh');
	actualizarRUC();

	$(".btn1").show();
	$(".btn2").hide();
}

//Función mostrar formulario
function mostrarform(flag) {
	limpiar();
	detenerEscaneo();
	if (flag) {
		$(".listadoregistros").hide();
		$("#formularioregistros").show();
		$("#btnGuardar").prop("disabled", false);
		$("#btnagregar").hide();
		$("#btnDetalles1").show();
		$("#btnDetalles2").hide();
		$("#frmDetalles").hide();
	}
	else {
		$(".listadoregistros").show();
		$("#formularioregistros").hide();
		$("#btnagregar").show();
		$("#btnDetalles1").show();
		$("#btnDetalles2").hide();
		$("#frmDetalles").hide();
	}
}

function frmDetalles(bool) {
	if (bool == true) { $("#frmDetalles").show(); $("#btnDetalles1").hide(); $("#btnDetalles2").show(); }
	if (bool == false) { $("#frmDetalles").hide(); $("#btnDetalles1").show(); $("#btnDetalles2").hide(); }
	// $('html, body').animate({ scrollTop: $(document).height() }, 10);
}

//Función cancelarform
function cancelarform() {
	limpiar();
	mostrarform(false);
}

//Función Listar
function listar() {
	let param = 1;

	let param1 = "";
	let param2 = "";
	let param3 = "";

	var fecha_inicio = $("#fecha_inicio").val();
	var fecha_fin = $("#fecha_fin").val();

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
				url: '../ajax/articulo.php?op=listar',
				type: "get",
				data: { param: param, fecha_inicio: fecha_inicio, fecha_fin: fecha_fin, param1: param1, param2: param2, param3: param3 },
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
				// $(row).find('td:eq(0), td:eq(1), td:eq(2), td:eq(3), td:eq(4), td:eq(5), td:eq(6), td:eq(7), td:eq(8), td:eq(9), td:eq(10), td:eq(11, td:eq(12), td:eq(13), td:eq(14), td:eq(15), td:eq(16), td:eq(17), td:eq(18), td:eq(19), td:eq(20), td:eq(21), td:eq(22)').addClass('nowrap-cell');
			},
			"initComplete": function () {
				agregarBuscadorColumna(this.api(), 11, "Buscar por código.");
			},
		}).DataTable();
}
//Función para guardar o editar

function guardaryeditar(e) {
	e.preventDefault(); //No se activará la acción predeterminada del evento

	var codigoBarra = $("#codigo").val();

	// var formatoValido = /^[0-9]{1} [0-9]{2} [0-9]{4} [0-9]{1} [0-9]{4} [0-9]{1}$/.test(codigoBarra);

	// if (!formatoValido && codigoBarra != "") {
	// 	bootbox.alert("El formato del código de barra no es válido. El formato correcto es: X XX XXXX X XXXX X");
	// 	$("#btnGuardar").prop("disabled", false);
	// 	return;
	// }

	// var stock = parseFloat($("#stock").val());
	// var stock_minimo = parseFloat($("#stock_minimo").val());

	// if (stock_minimo > stock) {
	// 	bootbox.alert("El stock mínimo no puede ser mayor que el stock normal.");
	// 	return;
	// }

	$("#btnGuardar").prop("disabled", true);
	var formData = new FormData($("#formulario")[0]);

	let detalles = frmDetallesVisible() ? obtenerDetalles() : { talla: '', color: '', peso: '0.00', idubicacion: '', fecha_emision: '', fecha_vencimiento: '', nota_1: '', nota_2: '', codigo: '' };

	for (let key in detalles) {
		formData.append(key, detalles[key]);
	}

	$.ajax({
		url: "../ajax/articulo.php?op=guardaryeditar",
		type: "POST",
		data: formData,
		contentType: false,
		processData: false,

		success: function (datos) {
			datos = limpiarCadena(datos);
			if (datos == "El código de barra del producto que ha ingresado ya existe." || datos == "El código del producto que ha ingresado ya existe.") {
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

function obtenerDetalles() {
	let detalles = {
		talla: $("#talla").val(),
		color: $("#color").val(),
		peso: $("#peso").val(),
		idubicacion: $("#idubicacion").val(),
		fecha_emision: $("#fecha_emision").val(),
		fecha_vencimiento: $("#fecha_vencimiento").val(),
		nota_1: $("#nota_1").val(),
		nota_2: $("#nota_2").val(),
		imei: $("#imei").val(),
		serial: $("#serial").val(),
		codigo: $("#codigo").val()
	};

	if (!detalles.talla) detalles.talla = '';
	if (!detalles.color) detalles.color = '';
	if (!detalles.peso) detalles.peso = '0.00';
	if (!detalles.idubicacion) detalles.idubicacion = '';
	if (!detalles.fecha_emision) detalles.fecha_emision = '';
	if (!detalles.fecha_vencimiento) detalles.fecha_vencimiento = '';
	if (!detalles.nota_1) detalles.nota_1 = '';
	if (!detalles.nota_2) detalles.nota_2 = '';
	if (!detalles.imei) detalles.imei = '';
	if (!detalles.serial) detalles.serial = '';
	if (!detalles.codigo) detalles.codigo = '';

	$("#idubicacion").selectpicker("refresh");

	return detalles;
}

function frmDetallesVisible() {
	return $("#frmDetalles").is(":visible");
}

function mostrar(idarticulo) {
	mostrarform(true);
	frmDetalles(true);

	$(".btn1").show();
	$(".btn2").hide();

	$.post("../ajax/articulo.php?op=mostrar", { idarticulo: idarticulo }, function (data, status) {
		data = JSON.parse(data);
		console.log(data);

		$("#idcategoria").val(data.idcategoria);
		$('#idcategoria').selectpicker('refresh');
		$("#idlocal").val(data.idlocal);
		$('#idlocal').selectpicker('refresh');
		$("#idmarca").val(data.idmarca);
		$('#idmarca').selectpicker('refresh');
		$("#idmedida").val(data.idmedida);
		$('#idmedida').selectpicker('refresh');
		$("#idubicacion").val(data.idubicacion);
		$('#idubicacion').selectpicker('refresh');
		$("#codigo").val(data.codigo);
		$("#codigo_producto").val(data.codigo_producto);
		$("#nombre").val(data.nombre);
		$("#stock").val(data.stock);
		$("#stock_minimo").val(data.stock_minimo);
		$("#precio_compra").val(data.precio_compra);
		// $("#precio_compra_mayor").val(data.precio_compra_mayor);
		$("#talla").val(data.talla);
		$("#color").val(data.color);
		$("#peso").val(data.peso);
		$("#descripcion").val(data.descripcion);
		$("#imagenmuestra").show();
		$("#imagenmuestra").attr("src", "../files/articulos/" + data.imagen);
		$("#imagenactual").val(data.imagen);
		$("#imei").val(data.imei);
		$("#serial").val(data.serial);
		data.fecha_emision_formateada != "0000-00-00" ? $("#fecha_emision").val(data.fecha_emision_formateada) : null;
		data.fecha_vencimiento_formateada != "0000-00-00" ? $("#fecha_vencimiento").val(data.fecha_vencimiento_formateada) : null;
		$("#nota_1").val(data.nota_1);
		$("#nota_2").val(data.nota_2);
		$("#idarticulo").val(data.idarticulo);
		generarbarcode(0);
		actualizarRUC();
	})
}

//Función para desactivar registros
function desactivar(idarticulo) {
	bootbox.confirm("¿Está Seguro de desactivar el producto?", function (result) {
		if (result) {
			$.post("../ajax/articulo.php?op=desactivar", { idarticulo: idarticulo }, function (e) {
				bootbox.alert(e);
				tabla.ajax.reload();
			});
		}
	})
}

//Función para activar registros
function activar(idarticulo) {
	bootbox.confirm("¿Está Seguro de activar el producto?", function (result) {
		if (result) {
			$.post("../ajax/articulo.php?op=activar", { idarticulo: idarticulo }, function (e) {
				bootbox.alert(e);
				tabla.ajax.reload();
			});
		}
	})
}

//Función para eliminar los registros
function eliminar(idarticulo) {
	bootbox.confirm("¿Estás seguro de eliminar el producto?", function (result) {
		if (result) {
			$.post("../ajax/articulo.php?op=eliminar", { idarticulo: idarticulo }, function (e) {
				bootbox.alert(e);
				tabla.ajax.reload();
			});
		}
	})
}

function convertirMayus(element) {
	element.value = element.value.toUpperCase();
}

function resetear() {
	const selects = ["idmarcaBuscar", "idcategoriaBuscar", "estadoBuscar", "fecha_inicio", "fecha_fin"];

	for (const selectId of selects) {
		$("#" + selectId).val("");
		$("#" + selectId).selectpicker('refresh');
	}

	listar();
}

//Función buscar
function buscar(param) {
	let param1 = "";
	let param2 = "";
	let param3 = "";

	var fecha_inicio = $("#fecha_inicio").val();
	var fecha_fin = $("#fecha_fin").val();

	if (param == 0) {
		if (fecha_inicio == "" || fecha_fin == "") {
			alert("Los campos de fecha inicial y fecha final son obligatorios.");
			return;
		} else if (fecha_inicio > fecha_fin) {
			alert("La fecha inicial no puede ser mayor que la fecha final.");
			return;
		}
	}

	if (param == 1) {
		// Obtener los selectores
		const selectMarca = document.getElementById("idmarcaBuscar");
		const selectCategoria = document.getElementById("idcategoriaBuscar");
		const selectEstado = document.getElementById("estadoBuscar");

		if (selectMarca.value == "" && selectCategoria.value == "" && selectEstado.value == "") {
			bootbox.alert("Debe seleccionar al menos un campo para realizar la búsqueda.");
			return;
		}

		param1 = selectMarca.value;
		param2 = selectCategoria.value;
		param3 = selectEstado.value;
	}

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
				url: '../ajax/articulo.php?op=listar',
				data: { param: param, fecha_inicio: fecha_inicio, fecha_fin: fecha_fin, param1: param1, param2: param2, param3: param3 },
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
				// $(row).find('td:eq(0), td:eq(1), td:eq(2), td:eq(3), td:eq(4), td:eq(5), td:eq(6), td:eq(7), td:eq(8), td:eq(9), td:eq(10), td:eq(11, td:eq(12), td:eq(13), td:eq(14), td:eq(15), td:eq(16), td:eq(17), td:eq(18), td:eq(19), td:eq(20), td:eq(21), td:eq(22)').addClass('nowrap-cell');
			},
			"initComplete": function () {
				agregarBuscadorColumna(this.api(), 11, "Buscar por código.");
			},
		}).DataTable();
}

var quaggaIniciado = false;

function escanear() {
	$(".btn1").hide();
	$(".btn2").show();
	$("#camera").show();

	Quagga.init({
		inputStream: {
			name: "Live",
			type: "LiveStream",
			target: document.querySelector('#camera')
		},
		decoder: {
			readers: ["code_128_reader"]
		}
	}, function (err) {
		if (err) {
			console.log(err);
			return
		}
		console.log("Initialization finished. Ready to start");
		Quagga.start();
		quaggaIniciado = true;
	});

	Quagga.onDetected(function (data) {
		console.log(data.codeResult.code);
		var codigoBarra = data.codeResult.code;
		document.getElementById('codigo').value = codigoBarra;
	});
}

function detenerEscaneo() {
	if (quaggaIniciado) {
		Quagga.stop();
		$(".btn1").show();
		$(".btn2").hide();
		$("#camera").hide();
		formatearNumero();
		quaggaIniciado = false;
	}
}

$("#codigo").on("input", function () {
	formatearNumero();
});

function formatearNumero() {
	var codigo = $("#codigo").val().replace(/\s/g, '').replace(/\D/g, '');
	var formattedCode = '';

	// for (var i = 0; i < codigo.length; i++) {
	// 	if (i === 1 || i === 3 || i === 7 || i === 8 || i === 12 || i === 13) {
	// 		formattedCode += ' ';
	// 	}

	// 	formattedCode += codigo[i];
	// }

	// var maxLength = parseInt($("#codigo").attr("maxlength"));
	// if (formattedCode.length > maxLength) {
	// 	formattedCode = formattedCode.substring(0, maxLength);
	// }

	$("#codigo").val(codigo);
	generarbarcode(0);
}

function borrar() {
	$("#codigo").val("");
	$("#codigo").focus();
	$("#print").hide();
}

//función para generar el número aleatorio del código de barra
function generar() {
	var codigo = "775";
	codigo += generarNumero(10000, 999) + "";
	codigo += Math.floor(Math.random() * 10) + "";
	codigo += generarNumero(100, 9) + "";
	codigo += Math.floor(Math.random() * 10);
	$("#codigo").val(codigo);
	generarbarcode(1);
}

function generarNumero(max, min) {
	var numero = Math.floor(Math.random() * (max - min + 1)) + min;
	var numeroFormateado = ("0000" + numero).slice(-4);
	return numeroFormateado;
}

// Función para generar el código de barras
function generarbarcode(param) {

	// if (param == 1) {
	// 	var codigo = $("#codigo").val().replace(/\s/g, '');
	// 	console.log(codigo.length);

	// 	if (!/^\d+$/.test(codigo)) {
	// 		bootbox.alert("El código de barra debe contener solo números.");
	// 		return;
	// 	} else if (codigo.length !== 13) {
	// 		bootbox.alert("El código de barra debe tener 13 dígitos.");
	// 		return;
	// 	} else {
	// 		codigo = codigo.slice(0, 1) + " " + codigo.slice(1, 3) + " " + codigo.slice(3, 7) + " " + codigo.slice(7, 8) + " " + codigo.slice(8, 12) + " " + codigo.slice(12, 13);
	// 	}
	// } else {
	// 	var codigo = $("#codigo").val()
	// }

	var codigo = $("#codigo").val().replace(/\s/g, '');

	if (codigo != "") {
		JsBarcode("#barcode", codigo);
		$("#codigo").val(codigo);
		$("#print").show();
	} else {
		$("#print").hide();
	}
}

//Función para imprimir el código de barras
function imprimir() {
	$("#print").printArea();
}

init();