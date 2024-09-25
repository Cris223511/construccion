var tabla;
var tabla2;
var idSession;

function nowrapCell() {
	var detallesTable = document.getElementById("detalles");
	var tdList = detallesTable.querySelectorAll("td");
	var thList = detallesTable.querySelectorAll("th");

	tdList.forEach(function (td) {
		td.classList.add("nowrap-cell");
	});

	thList.forEach(function (th) {
		th.classList.add("nowrap-cell");
	});
}

function bloquearCampos() {
	$("input, select, textarea").prop("disabled", true);
}

function desbloquearCampos() {
	$("input, select, textarea").prop("disabled", false);
}

function convertirMayus() {
	var inputCodigo = document.getElementById("codigo");
	inputCodigo.value = inputCodigo.value.toUpperCase();
}

function init() {
	mostrarform(false);
	limpiar();
	listar();

	$("#formulario").on("submit", function (e) {
		guardaryeditar(e);
	});

	$('#mTransferencias').addClass("treeview active");
	$("#btnGuardar").hide();

	$('[data-toggle="popover"]').popover();

	$.post("../ajax/transferencias.php?op=listarTodosActivos", function (data) {
		console.log(data)
		const obj = JSON.parse(data);
		console.log(obj);

		const selects = {
			"origen": $("#origen"),
			"destino": $("#destino"),
		};

		for (const selectId in selects) {
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

		$.post("../ajax/usuario.php?op=getSessionId", function (r) {
			console.log(r);
			data = JSON.parse(r);
			idSession = data.idusuario;
		})
	});
}

function listarTodosActivos(selectId) {
	$.post("../ajax/transferencias.php?op=listarTodosActivos", function (data) {
		const obj = JSON.parse(data);

		const select = $("#" + selectId);
		const atributo = selectId.replace('id', '');

		if (obj.hasOwnProperty(atributo)) {
			select.empty();
			select.html('<option value="">- Seleccione -</option>');
			obj[atributo].forEach(function (opcion) {
				select.append('<option value="' + opcion.id + '">' + opcion.titulo + '</option>');
			});
			select.selectpicker('refresh');
		}
	});
}

function validarLugarDestino() {
	const origen = $("#origen").val();
	const destino = $("#destino").val();

	if (origen === destino && (origen != "" || destino != "")) {
		$("#destino_input").show();
		$("#lugar_destino").attr("required", true);
	} else {
		$("#destino_input").hide();
		$("#lugar_destino").removeAttr("required");
	}
}

function validarListarArticulos(idlocal) {
	console.log(idlocal);
	if (idlocal != "") {
		listarArticulos(idlocal);
		listarArticulosCodigoBarra(idlocal);
		$("#idproducto").prop("disabled", false);
		$("#idproducto").selectpicker('refresh');
		$("#btnAgregarArt").prop("disabled", false);
	} else {
		$("#idproducto").prop("disabled", true);
		$("#idproducto").html("");
		$('#idproducto').selectpicker('refresh');
		$("#btnAgregarArt").prop("disabled", true);
	}
}

function listarArticulosCodigoBarra(idlocal) {
	$.get("../ajax/transferencias.php?op=selectProducto", { idlocal: idlocal }, function (r) {
		$("#idproducto").html(r);
		$('#idproducto').selectpicker('refresh');
	});
}

function limpiar() {
	desbloquearCampos();

	$("#codigo").val("");
	$("#comentario").val("");

	$("#origen").val(idSession);
	$("#origen").selectpicker('refresh');
	$("#destino").val($("#destino option:first").val());
	$("#destino").selectpicker('refresh');

	$("#lugar_destino").val("");
	$("#lugar_destino").removeAttr("required");
	$("#destino_input").hide();

	$(".filas").remove();
	$('#myModal').modal('hide');
	$("#btnGuardar").hide();
	$("#botonArt").show();
	$('#tblarticulos button').removeAttr('disabled');

	$("#form_codigo_barra").show();
	$("#idproducto").prop("disabled", true);

	$("#btnAgregarArt").show();
	$("#btnAgregarArt").prop("disabled", true);

	$("#origen").trigger('onchange');
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
				url: '../ajax/transferencias.php?op=listar',
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
				// $(row).find('td:eq(0), td:eq(1), td:eq(2), td:eq(3), td:eq(4), td:eq(5), td:eq(6), td:eq(7), td:eq(8)').addClass('nowrap-cell');
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
				url: '../ajax/transferencias.php?op=listar',
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
				// $(row).find('td:eq(0), td:eq(1), td:eq(2), td:eq(3), td:eq(4), td:eq(5), td:eq(6), td:eq(7), td:eq(8)').addClass('nowrap-cell');
			}
		}).DataTable();
}

function disableButton(button) {
	button.disabled = true;
}

function guardaryeditar(e) {
	e.preventDefault();
	$("#btnGuardar").prop("disabled", true);
	var formData = new FormData($("#formulario")[0]);

	$.ajax({
		url: "../ajax/transferencias.php?op=guardaryeditar",
		type: "POST",
		data: formData,
		contentType: false,
		processData: false,

		success: function (datos) {
			if (datos == "Una de las cantidades superan al stock normal del artículo.") {
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

function mostrar(idtransferencia) {
	$("#form_codigo_barra").hide();
	$("#btnAgregarArt").hide();
	$("#btnCrearArt").hide();
	$("#btnGuardar").hide();

	$.post("../ajax/transferencias.php?op=mostrar", { idtransferencia: idtransferencia }, function (data, status) {
		data = JSON.parse(data);
		mostrarform(true);
		bloquearCampos();
		console.log(data);

		$("#origen").val(data.origen);
		$('#origen').selectpicker('refresh');
		$("#destino").val(data.destino);
		$('#destino').selectpicker('refresh');

		$("#lugar_destino").val(data.lugar_destino);
		$("#lugar_destino").removeAttr("required");
		$("#destino_input").show();

		$("#codigo").val(data.codigo);
		$("#comentario").val(data.comentario);
		$("#print").hide();

		$("#botonArt").hide();
		$("#form_codigo_barra").hide();

		$.post("../ajax/transferencias.php?op=listarDetalle&id=" + idtransferencia, function (r) {
			// console.log(r);
			$("#detalles").html(r);
			inicializeGLightbox();
			nowrapCell();
		})
	})
}

function desactivar(idtransferencia) {
	bootbox.confirm("¿Está seguro de desactivar la transferencia?", function (result) {
		if (result) {
			$.post("../ajax/transferencias.php?op=desactivar", { idtransferencia: idtransferencia }, function (e) {
				bootbox.alert(e);
				tabla.ajax.reload();
			});
		}
	})
}

function activar(idtransferencia) {
	bootbox.confirm("¿Está seguro de activar la transferencia?", function (result) {
		if (result) {
			$.post("../ajax/transferencias.php?op=activar", { idtransferencia: idtransferencia }, function (e) {
				bootbox.alert(e);
				tabla.ajax.reload();
			});
		}
	})
}

function eliminar(idtransferencia) {
	bootbox.confirm("¿Estás seguro de eliminar la transferencia y devolver el stock del producto del local destino al local de origen?, esta acción <strong>hará que los productos transferidos al local destino vuelvan al local de origen, sumando la cantidad transferida con su stock actual del producto del local de origen y eliminará los productos transferidos en el local destino.</strong>", function (result) {
		if (result) {
			$.post("../ajax/transferencias.php?op=eliminar", { idtransferencia: idtransferencia }, function (e) {
				bootbox.alert(e);
				tabla.ajax.reload();
			});
		}
	})
}

function limpiarTransferencia(idtransferencia) {
	bootbox.confirm("¿Estás seguro de eliminar la transferencia? esta acción <strong>solo removerá la transferencia de la grilla, no afectará el stock de los productos en el local de origen y destino.</strong>", function (result) {
		if (result) {
			$.post("../ajax/transferencias.php?op=limpiar", { idtransferencia: idtransferencia }, function (e) {
				bootbox.alert(e);
				tabla.ajax.reload();
			});
		}
	})
}

function listarArticulos(idlocal) {
	tabla2 = $('#tblarticulos').DataTable({
		"aProcessing": true,
		"aServerSide": true,
		"dom": 'Bfrtip',
		"buttons": [],
		"ajax": {
			url: '../ajax/transferencias.php?op=listarArticulos',
			data: { idlocal: idlocal },
			type: "GET",
			dataType: "json",
			error: function (e) {
				console.log(e.responseText);
			}
		},
		"bDestroy": true,
		"iDisplayLength": 5,
		"order": [],
		"drawCallback": function (settings) {
			$('#tblarticulos button[data-idarticulo]').removeAttr('disabled');

			var detalles = getDetalles();

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
		var fila = '<tr class="filas" id="fila' + cont + '">' +
			'<td><button type="button" class="btn btn-danger" onclick="eliminarDetalle(' + cont + ', ' + idarticulo + ')">X</button></td>' +
			'<td><a href="../files/articulos/' + imagen + '" class="galleria-lightbox" style="z-index: 10000 !important;"><img src="../files/articulos/' + imagen + '" height="50px" width="50px" class="img-fluid"></a>' +
			'<td><input type="hidden" name="idarticulo[]" value="' + idarticulo + '">' + articulo + '</td>' +
			'<td>' + categoria + '</td>' +
			'<td>' + marca + '</td>' +
			'<td>' + codigo_producto + '</td>' +
			'<td>' + codigo + '</td>' +
			'<td>' + stock + '</td>' +
			'<td>' + stock_minimo + '</td>' +
			'<td><input type="number" name="cantidad[]" id="cantidad[]" lang="en-US" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" maxlength="6" onkeydown="evitarNegativo(event)" onpaste="return false;" onDrop="return false;" step="any" min="0.1" required value="' + cantidad + '"></td>' +
			'<td> S/. ' + precio_compra + '</td>' +
			'<td>' + medida + '</td>' +
			'</tr>';
		cont++;
		detalles = detalles + 1;
		$('#detalles').append(fila);
		evaluar();
		inicializeGLightbox();
		evitarCaracteresEspecialesCamposNumericos();
		aplicarRestrictATodosLosInputs();
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

function eliminarDetalle(indice, idarticulo) {
	$("#fila" + indice).remove();
	$('#tblarticulos button[data-idarticulo="' + idarticulo + '"]').removeAttr('disabled');
	console.log("Habilito a: " + idarticulo + " =)");
	detalles = detalles - 1;
	evaluar();
}

init();