var tabla;
let lastCodigoPedido = "";
var idSession;

function nowrapCell() {
	var detallesTables = document.querySelectorAll("#detalles, #detalles2");

	detallesTables.forEach(function (detallesTable) {
		var tdList = detallesTable.querySelectorAll("td");
		var thList = detallesTable.querySelectorAll("th");

		tdList.forEach(function (td) {
			td.classList.add("nowrap-cell");
		});

		thList.forEach(function (th) {
			th.classList.add("nowrap-cell");
		});
	});
}

//Función que se ejecuta al inicio
function init() {
	limpiar();
	listar();
	listarArticulos();

	$(".despachador").hide();

	$("#formulario").on("submit", function (e) {
		guardaryeditar(e);
	});
	$("#formulario2").on("submit", function (e) {
		guardaryeditar2(e);
	});
	$("#formulario3").on("submit", function (e) {
		guardaryeditar3(e);
	});

	$('[data-toggle="popover"]').popover();

	//Cargamos los items al select usuario
	$.post("../ajax/usuario.php?op=selectUsuarios", function (data) {
		$("#emisor").html(data);
		$('#emisor').selectpicker('refresh');
		$("#receptor").html(data);
		$('#receptor').selectpicker('refresh');
		$("#idalmacenero").html(data);
		$('#idalmacenero').selectpicker('refresh');
		$("#idalmacenero2").html(data);
		$('#idalmacenero2').selectpicker('refresh');
		$("#idencargado").html(data);
		$('#idencargado').selectpicker('refresh');
	});

	// obtenemos el último número del código de pedido
	$.post("../ajax/solicitudes.php?op=getLastCodigoPedido", function (e) {
		console.log(e);
		lastCodigoPedido = generarSiguienteCorrelativo(e);
		$("#codigo_pedido").val(lastCodigoPedido);
	});

	$.post("../ajax/usuario.php?op=getSessionId", function (r) {
		// console.log(r);
		data = JSON.parse(r);
		idSession = data.idusuario;
	})

	$('#mPrestamo').addClass("treeview active");
	$('#lSolicitud').addClass("active");
}

// function generarSiguienteCorrelativo(correlativoActual) {
// 	const siguienteNumero = Number(correlativoActual) + 1;
// 	const siguienteCorrelativo = siguienteNumero.toString().padStart(4, "0");
// 	return siguienteCorrelativo;
// }

//Función limpiar
function limpiar() {
	$(".despachador").hide();

	$("#idalmacenero").val($("#idalmacenero option:first").val());
	$("#idalmacenero").selectpicker('refresh');
	$("#emisor").val($("#emisor option:first").val());
	$("#emisor").selectpicker('refresh');

	$("#codigo_pedido").val(lastCodigoPedido);
	$("#telefono").val("");
	$("#empresa").val("");
	$("#destino").val("");

	$("#emisor").val("");
	$("#receptor").val("");

	$("#idalmacenero2").val($("#idalmacenero2 option:first").val());
	$("#idalmacenero2").selectpicker('refresh');

	$("#codigo_pedido2").val(lastCodigoPedido);
	$("#telefono2").val("");
	$("#empresa2").val("");
	$("#destino2").val("");

	$("#btnGuardar").hide();
	$("#btnAgregarArt").show();
	$(".filas").remove();

	$('#tblarticulos button').removeAttr('disabled');

	$("#detalles").html(`
		<thead style="background-color:#A9D0F5">
			<th>Opciones</th>
			<th>
				Artículo
			</th>
			<th>
				Categoría
			</th>
			<th>
				Marca
			</th>
			<th>
				Local
			</th>
			<th>
				Precio compra
			</th>
			<th>
				Cantidad <a href="#" data-toggle="popover" data-placement="top" title="Cantidad" data-content="Es la cantidad o stock del producto." style="color: #418bb7"><i class="fa fa-question-circle"></i></a>
			</th>
			<th>
				Cantidad a Solicitar <a href="#" data-toggle="popover" data-placement="top" title="Cantidad a Solicitar" data-content="Digita la cantidad a solicitar prestado (no debe superar a la cantidad o stock del producto)." style="color: #418bb7"><i class="fa fa-question-circle"></i></a>
			</th>
			<th>
				Estado
			</th>
		</thead>
		<tbody>
		</tbody>
	`);

	$('[data-toggle="popover"]').popover();

	nowrapCell();
}

function ocultarModal() {
	$('#myModal2').modal('hide');
	$('#myModal3').modal('hide');
	$('#myModal4').modal('hide');
}

//Función cancelarform
function cancelarform() {
	limpiar();
	ocultarModal();
}

//Función Listar
function listar() {
	tabla = $('#tbllistado').dataTable(
		{
			"lengthMenu": [15, 25, 50, 100],//mostramos el menú de registros a revisar
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
				url: '../ajax/solicitudes.php?op=listar',
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
			"iDisplayLength": 15,//Paginación
			"order": [],
			"createdRow": function (row, data, dataIndex) {
				$(row).find('td:eq(1)').css({
					"white-space": "nowrap"
				});
				$(row).find('td:eq(2)').css({
					"white-space": "nowrap"
				});
				$(row).find('td:eq(3)').css({
					"white-space": "nowrap"
				});
				$(row).find('td:eq(4)').css({
					"white-space": "nowrap"
				});
				$(row).find('td:eq(3)').css({
					"white-space": "nowrap"
				});
			}
		}).DataTable();

	tabla.on('init.dt', function () {
		$('[data-toggle="popover"]').popover();
	});
}


//Función ListarArticulos
function listarArticulos() {
	tabla = $('#tblarticulos').DataTable({
		"aProcessing": true,
		"aServerSide": true,
		"dom": 'Bfrtip',
		"buttons": [],
		"ajax": {
			url: '../ajax/solicitudes.php?op=listarArticulosSolicitud',
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
			cantidad: $(this).find("input[name='cantidad[]']").val()
		};
		detalles.push(detalle);
	});
	return detalles;
}

function disableButton(button) {
	button.disabled = true;
}

//Función para guardar o editar
function guardaryeditar(e) {
	e.preventDefault();
	formatearNumero();
	var formData = new FormData($("#formulario")[0]);
	$("#btnGuardar").prop("disabled", true);
	$.ajax({
		url: "../ajax/solicitudes.php?op=guardaryeditar",
		type: "POST",
		data: formData,
		contentType: false,
		processData: false,

		success: function (datos) {
			datos = limpiarCadena(datos);
			if (datos == "El número correlativo que ha ingresado ya existe." || datos == "Una de las cantidades superan a la cantidad o stock del artículo.") {
				bootbox.alert(datos);
				$("#btnGuardar").prop("disabled", false);
				return;
			} else {
				bootbox.alert(datos);
				limpiar();
				ocultarModal();
				setTimeout(() => {
					location.reload();
				}, 1500);
			}
		}
	});
}

function guardaryeditar2(e) {
	e.preventDefault();
	formatearNumero();
	var formData = new FormData($("#formulario2")[0]);
	$("#btnGuardar2").prop("disabled", true);
	$.ajax({
		url: "../ajax/solicitudes.php?op=guardaryeditarcomentario",
		type: "POST",
		data: formData,
		contentType: false,
		processData: false,
		success: function (datos) {
			datos = limpiarCadena(datos);
			bootbox.alert(datos);
			$("#btnGuardar2").prop("disabled", false);
			$("#idsolicitud").val("");
			$("#comentario").val("");
			ocultarModal();
			setTimeout(() => {
				location.reload();
			}, 1500);
		}
	});
}

function guardaryeditar3(e) {
	e.preventDefault();
	formatearNumero();
	var formData = new FormData($("#formulario3")[0]);
	$("#btnGuardar3").prop("disabled", true);
	$.ajax({
		url: "../ajax/solicitudes.php?op=actualizarSolicitud",
		type: "POST",
		data: formData,
		contentType: false,
		processData: false,

		success: function (datos) {
			datos = limpiarCadena(datos);
			if (datos == "El número correlativo que ha ingresado ya existe." || datos == "Una de las cantidades a prestar superan a la cantidad solicitada del artículo.") {
				bootbox.alert(datos);
				$("#btnGuardar3").prop("disabled", false);
				return;
			} else {
				bootbox.alert(datos);
				limpiar();
				ocultarModal();
				setTimeout(() => {
					location.reload();
				}, 1500);
			}
		}
	});
}

function probarDatos() {
	// Obtenemos los datos del formulario
	var formData = new FormData($("#formulario3")[0]);

	$.ajax({
		url: "../ajax/solicitudes.php?op=probarDatos",
		type: "POST",
		data: formData,
		contentType: false,
		processData: false,
		success: function (response) {
			// Muestra el resultado en la consola
			console.log(response);
			var data = JSON.parse(response);
			console.log('Datos obtenidos:', data);
		},
		error: function (error) {
			console.error('Error al obtener datos:', error);
		}
	});
}

function verificarCantidades() {
	const filas = document.querySelectorAll('.filas');

	filas.forEach(fila => {
		const tdCantidadPrestada = fila.querySelector('td[data-cantidadsolicitada]');
		const tdCantidadDevuelta = fila.querySelector('td[data-cantidadprestada]');
		const campoInput = fila.querySelector('input[data-cantidadprestar]');

		const cantidadPrestadaValor = tdCantidadPrestada.textContent.trim();
		const cantidadDevueltaValor = tdCantidadDevuelta.textContent.trim();

		if (cantidadPrestadaValor === cantidadDevueltaValor) {
			campoInput.readOnly = true;
			campoInput.value = '0';
			campoInput.style.backgroundColor = '#eee';
			campoInput.style.borderColor = '#d2d6de';
		}
	});
}

function mostrar(idsolicitud) {
	limpiar();
	$(".despachador").show();
	$("#codigo_pedido").val("");
	$("#btnAgregarArt").hide();
	$("#btnGuardar").hide();

	$('[data-toggle="popover"]').popover();

	$.post("../ajax/solicitudes.php?op=mostrar", { idsolicitud: idsolicitud }, function (data, status) {
		console.log(data);
		data = JSON.parse(data);
		console.log(data);

		$("#idsolicitud").val(data.idsolicitud);
		$("#idalmacenero").val(data.idalmacenero);
		$("#idalmacenero").selectpicker('refresh');
		$("#emisor").val(data.idencargado);
		$("#emisor").selectpicker('refresh');

		$("#codigo_pedido").val(data.codigo_pedido);
		$("#telefono").val(data.telefono);
		$("#empresa").val(data.empresa);
	});

	$("#detalles").html(`
		<thead style="background-color:#A9D0F5">
			<th>
				Opciones
			</th>
			<th>
				Artículo
			</th>
			<th>
				Categoría
			</th>
			<th>
				Marca
			</th>
			<th>
				Local
			</th>
			<th>
				Cantidad Solicitada <a href="#" data-toggle="popover" data-placement="top" title="Cantidad Solicitada" data-content="Es la cantidad solicitada a prestar." style="color: #002a8e"><i class="fa fa-question-circle"></i></a>
			</th>
			<th>
				Cantidad Prestada <a href="#" data-toggle="popover" data-placement="top" title="Cantidad Prestada" data-content="Es la cantidad que el almacenero prestó." style="color: #002a8e"><i class="fa fa-question-circle"></i></a>
			</th>
			<th>
				Estado
			</th>
		</thead>
	`);

	$.post("../ajax/solicitudes.php?op=listarDetalle&id=" + idsolicitud, function (r) {
		// console.log(r);
		$("#detalles").html(r);
		$('[data-toggle="popover"]').popover();
		nowrapCell();
	});
}

function mostrar2(idsolicitud) {
	limpiar();
	$(".despachador").show();
	$("#btnGuardar3").hide();
	$("#codigo_pedido2").val("");

	$('[data-toggle="popover"]').popover();

	$.post("../ajax/solicitudes.php?op=mostrar", { idsolicitud: idsolicitud }, function (data, status) {
		console.log(data);
		data = JSON.parse(data);
		console.log(data);

		idalmacenero = (data.idalmacenero == null | data.idalmacenero == "null") ? idSession : data.idalmacenero;

		$("#idsolicitud2").val(data.idsolicitud);
		$("#idencargado").val(data.idencargado);
		$("#idencargado").selectpicker('refresh');
		$("#idalmacenero2").val(idalmacenero);
		$("#idalmacenero2").selectpicker('refresh');

		$("#codigo_pedido2").val(data.codigo_pedido);
		$("#telefono2").val(data.telefono);
		$("#empresa2").val(data.empresa);
		$("#destino2").val(data.destino);
	});

	$("#detalles2").html(`
		<thead style="background-color:#A9D0F5">
			<th>
				Opciones
			</th>
			<th>
				Artículo
			</th>
			<th>
				Categoría
			</th>
			<th>
				Marca
			</th>
			<th>
				Local
			</th>
			<th>
				Cantidad Solicitada <a href="#" data-toggle="popover" data-placement="top" title="Cantidad Solicitada" data-content="Es la cantidad solicitada a prestar." style="color: #418bb7"><i class="fa fa-question-circle"></i></a>
			</th>
			<th>
				Cantidad Prestada <a href="#" data-toggle="popover" data-placement="top" title="Cantidad Prestada" data-content="Es la cantidad que el receptor de pedido prestó." style="color: #418bb7"><i class="fa fa-question-circle"></i></a>
			</th>
			<th>
				Cantidad a Prestar <a href="#" data-toggle="popover" data-placement="top" title="Cantidad a Prestar" data-content="Digita la cantidad que deseas prestar al emisor de pedido (no debe superar la cantidad solicitada)." style="color: #418bb7"><i class="fa fa-question-circle"></i></a>
			</th>
			<th>
				Estado
			</th>
		</thead>
	`);

	$.post("../ajax/solicitudes.php?op=listarDetalle2&id=" + idsolicitud, function (r) {
		// console.log(r);
		$("#detalles2").html(r);
		$("#btnGuardar3").show();
		$('[data-toggle="popover"]').popover();
		verificarCantidades();
		nowrapCell();
	});
}

function mostrarComentario(idsolicitud) {
	$("#btnGuardar2").hide();
	$("#comentario").val("");
	$("#comentario").prop("disabled", true);
	$("#comentario").attr("placeholder", "Cargando...");
	$.post("../ajax/solicitudes.php?op=mostrarComentario", { idsolicitud: idsolicitud }, function (data, status) {
		console.log(data);
		data = JSON.parse(data);
		console.log(data);

		$("#idsolicitud").val(data.idsolicitud);
		$("#comentario").val(data.comentario);
		$("#script").empty().append(data.script);
	});
}

//Función para anular registros
function anular(idsolicitud) {
	bootbox.confirm("¿Está seguro de anular la solicitud?", function (result) {
		if (result) {
			$.post("../ajax/solicitudes.php?op=anular", { idsolicitud: idsolicitud }, function (e) {
				bootbox.alert(e);
				setTimeout(() => {
					location.reload();
				}, 1500);
			});
		}
	})
}

//Función para activar registros
function activar(idsolicitud) {
	bootbox.confirm("¿Está seguro de activar la solicitud?", function (result) {
		if (result) {
			$.post("../ajax/solicitudes.php?op=activar", { idsolicitud: idsolicitud }, function (e) {
				bootbox.alert(e);
				setTimeout(() => {
					location.reload();
				}, 1500);
			});
		}
	})
}

//Función para rechazar registros
function rechazar(idsolicitud) {
	bootbox.confirm("¿Está seguro de rechazar la solicitud?", function (result) {
		if (result) {
			$.post("../ajax/solicitudes.php?op=rechazar", { idsolicitud: idsolicitud }, function (e) {
				bootbox.alert(e);
				setTimeout(() => {
					location.reload();
				}, 1500);
			});
		}
	})
}

//Función para eliminar los registros
function eliminar(idsolicitud) {
	bootbox.confirm("¿Estás seguro de eliminar la solicitud?", function (result) {
		if (result) {
			$.post("../ajax/solicitudes.php?op=eliminar", { idsolicitud: idsolicitud }, function (e) {
				bootbox.alert(e);
				setTimeout(() => {
					location.reload();
				}, 1500);
			});
		}
	})
}

var cont = 0;
var detalles = 0;
$("#btnGuardar").hide();

function agregarDetalle(marca, local, precio_compra, categoria, idarticulo, stock, articulo) {
	var cantidad = 0;

	if (idarticulo != "") {
		var fila = '<tr class="filas" id="fila' + cont + '">' +
			'<td class="nowrap-cell"><button type="button" class="btn btn-danger" onclick="eliminarDetalle(' + cont + ', ' + idarticulo + ')">X</button></td>' +
			'<td class="nowrap-cell"><input type="hidden" name="idarticulo[]" value="' + idarticulo + '">' + articulo + '</td>' +
			'<td>' + categoria + '</td>' +
			'<td>' + marca + '</td>' +
			'<td>' + local + '</td>' +
			'<td><input type="hidden" name="precio_compra[]" value="' + precio_compra + '">' + precio_compra + '</td>' +
			'<td>' + stock + '</td>' +
			'<td class="nowrap-cell"><input type="number" step="any" name="cantidad[]" id="cantidad[]" value="' + cantidad + '" min="0.1"></td>' +
			'<td><span class="label bg-orange">Incompleto</span></td>' +
			'</tr>';
		cont++;
		detalles = detalles + 1;
		$('#detalles').append(fila);
		console.log("Deshabilito a: " + idarticulo + " =)");
	}
	else {
		alert("Error al ingresar el detalle, revisar los datos del artículo");
	}
	evaluar();
	nowrapCell();
}

function verificar_stock(idarticulo, articulo) {
	var cantidad = document.querySelector('#cantidad\\[\\]').value;
	if (cantidad !== '') {
		console.log('El valor del input cantidad es: ' + cantidad);
		console.log('El nombre del artículo es: ' + articulo);
		console.log('El idarticulo que verificaremos es: ' + idarticulo);

		$.post("../ajax/solicitudes.php?op=verificarStockMinimo&id=" + idarticulo + "&nombre=" + articulo + "&cantidad=" + cantidad, function (data) {
			if (data !== '') {
				bootbox.alert(data);
			}
		});
	} else {
		console.log('El input "cantidad" está vacío');
	}
}

function evaluar() {
	console.log(detalles);
	if (detalles > 0) {
		$("#btnGuardar").show();
	}
	else {
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

document.addEventListener('DOMContentLoaded', function () {
	init();
});