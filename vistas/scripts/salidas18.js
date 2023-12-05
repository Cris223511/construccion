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
	$("input, select, textarea").not("#fecha_hora").prop("disabled", true);
}

function desbloquearCampos() {
	$("input, select, textarea").not("#fecha_hora").prop("disabled", false);
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

function generarSiguienteCorrelativo(numeros) {
	const siguienteNumero = parseInt(numeros, 10) + 1;
	const longitud = numeros.length;
	const siguienteCorrelativo = String(siguienteNumero).padStart(longitud, '0');
	return siguienteCorrelativo;
}

function init() {
	mostrarform(false);
	limpiar();
	listar();
	listarArticulos();
	actualizarFecha();

	$("#formulario").on("submit", function (e) {
		guardaryeditar(e);
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
			"idpersonal": $("#idpersonal"),
			"idautorizado": $("#idautorizado"),
			"identregado": $("#identregado"),
			"idrecibido": $("#idrecibido"),
			"idmaquinaria": $("#idmaquinaria"),
		};

		for (const selectId in selects) {
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

			if (selects.hasOwnProperty(selectId)) {
				const select = selects[selectId];
				const atributo = selectId.replace('id', '');

				if (obj.hasOwnProperty(atributo)) {
					select.empty();
					select.html('<option value="">- Seleccione -</option>');
					obj[atributo].forEach(function (opcion) {
						select.append('<option value="' + opcion.id + '">' + opcion.titulo + '</option>');
					});
					select.selectpicker('refresh');
				}
			}
		}
	});

	$.post("../ajax/salidas.php?op=selectProducto", function (r) {
		$("#idproducto").html(r);
		$('#idproducto').selectpicker('refresh');
	});
}

function limpiar() {
	desbloquearCampos();
	actualizarFecha();

	$("#cod_1").val(letraCorrelativo);
	$("#cod_2").val(siguienteCorrelativo);
	$("#codigo_producto").val("");
	$("#nombre").val("");
	$("#descripcion").val("");
	$("#ubicacion").val("");
	$("#print").hide();
	$("#idsalida").val("");

	$(".selectPersonal").hide();
	$("#selectMaquinaria").hide();

	$("#idtipo").val($("#idtipo option:first").val());
	$("#idtipo").selectpicker('refresh');
	$("#tipo_movimiento").val($("#tipo_movimiento option:first").val());
	$("#tipo_movimiento").selectpicker('refresh');
	$("#idpersonal").val($("#idpersonal option:first").val());
	$("#idpersonal").selectpicker('refresh');
	$("#idautorizado").val($("#idautorizado option:first").val());
	$("#idautorizado").selectpicker('refresh');
	$("#identregado").val($("#identregado option:first").val());
	$("#identregado").selectpicker('refresh');
	$("#idrecibido").val($("#idrecibido option:first").val());
	$("#idrecibido").selectpicker('refresh');
	$("#idmaquinaria").val($("#idmaquinaria option:first").val());
	$("#idmaquinaria").selectpicker('refresh');

	$(".filas").remove();
	$('#myModal').modal('hide');
	$("#btnAgregarArt").show();
	$("#btnGuardar").hide();
	$("#botonArt").show();
	$("#form_codigo_barra").show();
	$('#tblarticulos button').removeAttr('disabled');
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
				$(row).find('td:eq(0), td:eq(1), td:eq(2), td:eq(3), td:eq(4), td:eq(5), td:eq(6), td:eq(7), td:eq(8), td:eq(9), td:eq(10), td:eq(11), td:eq(12)').addClass('nowrap-cell');
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
				$(row).find('td:eq(0), td:eq(1), td:eq(2), td:eq(3), td:eq(4), td:eq(5), td:eq(6), td:eq(7), td:eq(8), td:eq(9), td:eq(10), td:eq(11), td:eq(12)').addClass('nowrap-cell');
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
	$.post("../ajax/salidas.php?op=mostrar", { idsalida: idsalida }, function (data, status) {
		data = JSON.parse(data);
		mostrarform(true);
		bloquearCampos();
		console.log(data);

		$("#idtipo").val(data.idtipo);
		$('#idtipo').selectpicker('refresh');

		if (data.idpersonal != 0) {
			$("#tipo_movimiento").val('personal');
			$('#tipo_movimiento').selectpicker('refresh');
			$('#tipo_movimiento').trigger('onchange');
		} else {
			$("#tipo_movimiento").val('maquinaria');
			$('#tipo_movimiento').selectpicker('refresh');
			$('#tipo_movimiento').trigger('onchange');
		}

		$("#idpersonal").val(data.idpersonal);
		$('#idpersonal').selectpicker('refresh');
		$("#idautorizado").val(data.idautorizado);
		$('#idautorizado').selectpicker('refresh');
		$("#identregado").val(data.identregado);
		$('#identregado').selectpicker('refresh');
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
		$("#idsalida").val(data.idsalida);

		$("#botonArt").hide();
		$("#form_codigo_barra").hide();

		$.post("../ajax/salidas.php?op=listarDetalle&id=" + idsalida, function (r) {
			// console.log(r);
			$("#detalles").html(r);
			nowrapCell();
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
			$(row).find('td:eq(0), td:eq(1), td:eq(2), td:eq(3), td:eq(4), td:eq(6), td:eq(7), td:eq(8), td:eq(9), td:eq(10), td:eq(11, td:eq(12), td:eq(13)').addClass('nowrap-cell');
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

function agregarDetalle(idarticulo, articulo, categoria, marca, medida, codigo_producto, codigo, stock, stock_minimo, imagen) {
	var cantidad = 1;

	if (idarticulo != "") {
		var fila = '<tr class="filas" id="fila' + cont + '">' +
			'<td><button type="button" class="btn btn-danger" onclick="eliminarDetalle(' + cont + ', ' + idarticulo + ')">X</button></td>' +
			'<td><input type="hidden" name="idarticulo[]" value="' + idarticulo + '">' + articulo + '</td>' +
			'<td>' + categoria + '</td>' +
			'<td>' + marca + '</td>' +
			'<td><input type="text" name="cantidad[]" id="cantidad[]" step="any" onkeydown="evitarNegativo(event)" oninput="validarNumeroDecimal(this, 6)" value="' + cantidad + '"></td>' +
			'<td>' + medida + '</td>' +
			'<td>' + codigo_producto + '</td>' +
			'<td>' + codigo + '</td>' +
			'<td>' + stock + '</td>' +
			'<td>' + stock_minimo + '</td>' +
			'<td><img src="../files/articulos/' + imagen + '" height="50px" width="50px"></td>' +
			'</tr>';
		cont++;
		detalles = detalles + 1;
		$('#detalles').append(fila);
		evaluar();
		console.log("Deshabilito a: " + idarticulo + " =)");
	}
	else {
		alert("Error al ingresar el detalle, revisar los datos del artículo");
	}

	nowrapCell();
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
				console.log("Envío esto al servidor =>", e[0].idarticulo, e[0].articulo, e[0].categoria, e[0].marca, e[0].medida, e[0].codigo_producto, e[0].codigo, e[0].stock, e[0].stock_minimo, e[0].imagen);

				// Resetear el valor del select
				$('#idproducto').val($("#idproducto option:first").val());
				$("#idproducto").selectpicker('refresh');

				agregarDetalle(e[0].idarticulo, e[0].articulo, e[0].categoria, e[0].marca, e[0].medida, e[0].codigo_producto, e[0].codigo, e[0].stock, e[0].stock_minimo, e[0].imagen);

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

	$("#idpersonal").val($("#idpersonal option:first").val());
	$("#idpersonal").selectpicker('refresh');
	$("#idautorizado").val($("#idautorizado option:first").val());
	$("#idautorizado").selectpicker('refresh');
	$("#identregado").val($("#identregado option:first").val());
	$("#identregado").selectpicker('refresh');
	$("#idrecibido").val($("#idrecibido option:first").val());
	$("#idrecibido").selectpicker('refresh');
	$("#idmaquinaria").val($("#idmaquinaria option:first").val());
	$("#idmaquinaria").selectpicker('refresh');

	$("#idmaquinaria").attr("required", "required");
	$("#idpersonal").attr("required", "required");
	$("#idautorizado").attr("required", "required");
	$("#identregado").attr("required", "required");
	$("#idrecibido").attr("required", "required");

	if (tipoMovimiento === "personal") {
		$(".selectPersonal").show();
		$("#idmaquinaria").removeAttr("required");
	} else if (tipoMovimiento === "maquinaria") {
		$("#selectMaquinaria").show();
		$("#idpersonal").removeAttr("required");
		$("#idautorizado").removeAttr("required");
		$("#identregado").removeAttr("required");
		$("#idrecibido").removeAttr("required");
	}
	else {
		$("#idmaquinaria").attr("required", "required");
		$("#idpersonal").attr("required", "required");
		$("#idautorizado").attr("required", "required");
		$("#identregado").attr("required", "required");
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