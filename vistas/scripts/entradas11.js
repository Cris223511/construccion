var tabla;
var tabla2;

function bloquearCampos() {
	$("input, select, textarea").prop("disabled", true);
}

function desbloquearCampos() {
	$("input, select, textarea").prop("disabled", false);
}

function init() {
	mostrarform(false);
	limpiar();
	listar();
	listarArticulos();

	$("#formulario").on("submit", function (e) {
		guardaryeditar(e);
	});
	$('#mEntradas').addClass("treeview active");
	$('#lEntradas').addClass("active");
	$("#btnGuardar").hide();

	$('[data-toggle="popover"]').popover();

	$.post("../ajax/entradas.php?op=listarTodosActivos", function (data) {
		// console.log(data)
		const obj = JSON.parse(data);
		console.log(obj);

		const selects = {
			"idmarca": $("#idmarca"),
			"idcategoria": $("#idcategoria"),
			"idmedida": $("#idmedida"),
			"idtipo": $("#idtipo"),
			"idproveedor": $("#idproveedor"),
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
	});

	$.post("../ajax/entradas.php?op=selectProducto", function (r) {
		$("#idproducto").html(r);
		$('#idproducto').selectpicker('refresh');
	});
}

function limpiar() {
	desbloquearCampos();

	$("#codigo").val("");
	$("#codigo_producto").val("");
	$("#nombre").val("");
	$("#descripcion").val("");
	$("#peso").val("");
	$("#ubicacion").val("");
	$("#print").hide();
	$("#identrada").val("");

	$("#idcategoria").val($("#idcategoria option:first").val());
	$("#idcategoria").selectpicker('refresh');
	$("#idmarca").val($("#idmarca option:first").val());
	$("#idmarca").selectpicker('refresh');
	$("#idmedida").val($("#idmedida option:first").val());
	$("#idmedida").selectpicker('refresh');
	$("#idtipo").val($("#idtipo option:first").val());
	$("#idtipo").selectpicker('refresh');
	$("#idproveedor").val($("#idproveedor option:first").val());
	$("#idproveedor").selectpicker('refresh');

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
	}
	else {
		$(".listadoregistros").show();
		$("#formularioregistros").hide();
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
				url: '../ajax/entradas.php?op=listar',
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
				$(row).find('td:eq(0), td:eq(1), td:eq(2), td:eq(3), td:eq(4), td:eq(5), td:eq(6), td:eq(7), td:eq(8), td:eq(9)').addClass('nowrap-cell');
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
				url: '../ajax/entradas.php?op=listar',
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
				$(row).find('td:eq(0), td:eq(1), td:eq(2), td:eq(3), td:eq(4), td:eq(5), td:eq(6), td:eq(7), td:eq(8), td:eq(9)').addClass('nowrap-cell');
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
		url: "../ajax/entradas.php?op=guardaryeditar",
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

function mostrar(identrada) {
	$.post("../ajax/entradas.php?op=mostrar", { identrada: identrada }, function (data, status) {
		data = JSON.parse(data);
		mostrarform(true);
		bloquearCampos();
		console.log(data);

		$("#idcategoria").val(data.idcategoria);
		$('#idcategoria').selectpicker('refresh');
		$("#idmarca").val(data.idmarca);
		$('#idmarca').selectpicker('refresh');
		$("#idmedida").val(data.idmedida);
		$('#idmedida').selectpicker('refresh');
		$("#idtipo").val(data.idtipo);
		$('#idtipo').selectpicker('refresh');
		$("#idproveedor").val(data.idproveedor);
		$('#idproveedor').selectpicker('refresh');
		$("#codigo").val(data.codigo);
		$("#codigo_producto").val(data.codigo_producto);
		$("#nombre").val(data.nombre);
		$("#descripcion").val(data.descripcion);
		$("#peso").val(data.peso);
		$("#ubicacion").val(data.ubicacion);
		$("#print").hide();
		$("#identrada").val(data.identrada);

		$("#botonArt").hide();
		$("#form_codigo_barra").hide();

		$.post("../ajax/entradas.php?op=listarDetalle&id=" + identrada, function (r) {
			// console.log(r);
			$("#detalles").html(r);
		})
	})

}

function desactivar(identrada) {
	bootbox.confirm("¿Está seguro de desactivar la entrada?", function (result) {
		if (result) {
			$.post("../ajax/entradas.php?op=desactivar", { identrada: identrada }, function (e) {
				bootbox.alert(e);
				tabla.ajax.reload();
			});
		}
	})
}

function activar(identrada) {
	bootbox.confirm("¿Está seguro de activar la entrada?", function (result) {
		if (result) {
			$.post("../ajax/entradas.php?op=activar", { identrada: identrada }, function (e) {
				bootbox.alert(e);
				tabla.ajax.reload();
			});
		}
	})
}

function eliminar(identrada) {
	bootbox.confirm("¿Estás seguro de eliminar la entrada?", function (result) {
		if (result) {
			$.post("../ajax/entradas.php?op=eliminar", { identrada: identrada }, function (e) {
				bootbox.alert(e);
				tabla.ajax.reload();
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
			url: '../ajax/entradas.php?op=listarArticulos',
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

function agregarDetalle(idarticulo, articulo, codigo) {
	var cantidad = 1;

	if (idarticulo != "") {
		var fila = '<tr class="filas" id="fila' + cont + '">' +
			'<td><button type="button" class="btn btn-danger" onclick="eliminarDetalle(' + cont + ', ' + idarticulo + ')">X</button></td>' +
			'<td><input type="hidden" name="idarticulo[]" value="' + idarticulo + '">' + articulo + '</td>' +
			'<td><input type="number" name="cantidad[]" id="cantidad[]" value="' + cantidad + '"></td>' +
			'<td>' + codigo + '</td>' +
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
			url: '../ajax/entradas.php?op=listarProductos',
			type: 'GET',
			dataType: 'json',
			data: { idarticulo: idarticulo },
			success: function (e) {
				console.log(e);
				$('#idproducto').prop("disabled", false);
				console.log("Envío esto al servidor =>", e[0].idarticulo, e[0].articulo, e[0].codigo);

				// Resetear el valor del select
				$('#idproducto').val($("#idproducto option:first").val());
				$("#idproducto").selectpicker('refresh');

				agregarDetalle(e[0].idarticulo, e[0].articulo, e[0].codigo);

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
		$("#btnGuardar").hide();
		$("#btnGuardar").prop("disabled", false);

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