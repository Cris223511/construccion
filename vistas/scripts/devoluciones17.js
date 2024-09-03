var tabla;

//Función que se ejecuta al inicio
function init() {
	limpiar();
	listar();

	$("#formulario2").on("submit", function (e) {
		guardaryeditar2(e);
	});
	$("#formulario3").on("submit", function (e) {
		guardaryeditar3(e);
	});
	$("#formulario4").on("submit", function (e) {
		guardaryeditar4(e);
	});

	$('[data-toggle="popover"]').popover();

	//Cargamos los items al select cliente
	$.post("../ajax/devoluciones.php?op=selectAlmacenero", function (r) {
		$("#idalmacenero").html(r);
		$('#idalmacenero').selectpicker('refresh');
		$("#idalmacenero2").html(r);
		$('#idalmacenero2').selectpicker('refresh');
		$("#idalmacenero3").html(r);
		$('#idalmacenero3').selectpicker('refresh');
	});

	$('#mDevolucion').addClass("treeview active");
	$('#lDevolucion').addClass("active");
}

//Función limpiar
function limpiar() {
	$("#idalmacenero").val($("#idalmacenero option:first").val());
	$("#idalmacenero").selectpicker('refresh');

	$("#codigo_pedido").val("");
	$("#telefono").val("");
	$("#empresa").val("");

	$("#idalmacenero2").val($("#idalmacenero2 option:first").val());
	$("#idalmacenero2").selectpicker('refresh');

	$("#codigo_pedido2").val("");
	$("#telefono2").val("");
	$("#empresa2").val("");

	$(".filas").remove();
	$("input[name='opcion']").prop("checked", false);

	$('#tblarticulos button').removeAttr('disabled');
}

function ocultarModal() {
	$('#myModal2').modal('hide');
	$('#myModal3').modal('hide');
	$('#myModal4').modal('hide');
	$('#myModal5').modal('hide');
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
				url: '../ajax/devoluciones.php?op=listar',
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

function guardaryeditar2(e) {
	e.preventDefault();
	var formData = new FormData($("#formulario2")[0]);
	$("#btnGuardar2").prop("disabled", true);
	$.ajax({
		url: "../ajax/devoluciones.php?op=guardaryeditarcomentario",
		type: "POST",
		data: formData,
		contentType: false,
		processData: false,
		success: function (datos) {
			datos = limpiarCadena(datos);
			if (!datos) {
				console.log("No se recibieron datos del servidor.");
				$("#btnGuardar2").prop("disabled", false);
				return;
			} else {
				bootbox.alert(datos);
				$("#btnGuardar2").prop("disabled", false);
				$("#iddevolucion").val("");
				$("#comentario").val("");
				ocultarModal();
				setTimeout(() => {
					location.reload();
				}, 1500);
			}
		}
	});
}

function guardaryeditar3(e) {
	e.preventDefault();
	var formData = new FormData($("#formulario3")[0]);
	$("#btnGuardar3").prop("disabled", true);
	$.ajax({
		url: "../ajax/devoluciones.php?op=actualizarDevolucion",
		type: "POST",
		data: formData,
		contentType: false,
		processData: false,

		success: function (datos) {
			datos = limpiarCadena(datos);
			if (!datos) {
				console.log("No se recibieron datos del servidor.");
				$("#btnGuardar3").prop("disabled", false);
				return;
			} else if (datos == "Una de las cantidades a devolver superan a la cantidad prestada del artículo.") {
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

function guardaryeditar4(e) {
	e.preventDefault();
	$('input[name="cantidad_devuelta[]"]').prop('disabled', false);

	var formData = new FormData($("#formulario4")[0]);
	var opcionSeleccionada = $('input[name="opcion"]:checked').val();
	formData.append('opcion', opcionSeleccionada);

	$('input[name="cantidad_devuelta[]"]').prop('disabled', true);
	$("#btnGuardar4").prop("disabled", true);

	$.ajax({
		url: "../ajax/devoluciones.php?op=actualizarDevolucion2",
		type: "POST",
		data: formData,
		contentType: false,
		processData: false,
		success: function (datos) {
			datos = limpiarCadena(datos);
			if (!datos) {
				console.log("No se recibieron datos del servidor.");
				$("#btnGuardar4").prop("disabled", false);
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


function verificarCantidades() {
	const filas = document.querySelectorAll('.filas');

	filas.forEach(fila => {
		const tdCantidadPrestada = fila.querySelector('td[data-cantidadprestada]');
		const tdCantidadDevuelta = fila.querySelector('td[data-cantidaddevuelta]');
		const campoInput = fila.querySelector('input[data-cantidaddevolver]');

		const cantidadPrestadaValor = tdCantidadPrestada.textContent.trim();
		const cantidadDevueltaValor = tdCantidadDevuelta.textContent.trim();

		if (cantidadPrestadaValor === cantidadDevueltaValor) {
			campoInput.readOnly = true;
			campoInput.value = '0';
			campoInput.style.backgroundColor = '#eee';
			campoInput.style.borderColor = '#d2d6de';

			$("#btnGuardar3").hide();
		} else {
			$("#btnGuardar3").show();
		}
	});
}

function mostrar(iddevolucion) {
	limpiar();
	$("#codigo_pedido").val("");
	$("#btnGuardar").hide();
	$('[data-toggle="popover"]').popover();

	$.post("../ajax/devoluciones.php?op=mostrar", { iddevolucion: iddevolucion }, function (data, status) {
		console.log(data);
		data = JSON.parse(data);
		console.log(data);

		$("#iddevolucion").val(data.iddevolucion);
		$("#idalmacenero").val(data.idalmacenero);
		$("#idalmacenero").selectpicker('refresh');

		$("#codigo_pedido").val(data.codigo_pedido);
		$("#telefono").val(data.telefono);
		$("#empresa").val(data.empresa);
		$('[data-toggle="popover"]').popover();
	});

	$.post("../ajax/devoluciones.php?op=listarDetalle&id=" + iddevolucion, function (r) {
		$("#detalles").html(r);
		$('[data-toggle="popover"]').popover();
	});
}

function mostrar2(iddevolucion) {
	limpiar();
	$("#btnGuardar3").hide();
	$("#codigo_pedido").val("");
	$.post("../ajax/devoluciones.php?op=mostrar", { iddevolucion: iddevolucion }, function (data, status) {
		console.log(data);
		data = JSON.parse(data);
		console.log(data);

		$("#iddevolucion2").val(data.iddevolucion);
		$("#idalmacenero2").val(data.idalmacenero);
		$("#idalmacenero2").selectpicker('refresh');

		$("#codigo_pedido2").val(data.codigo_pedido);
		$("#telefono2").val(data.telefono);
		$("#empresa2").val(data.empresa);
		$('[data-toggle="popover"]').popover();
	});

	$.post("../ajax/devoluciones.php?op=listarDetalle2&id=" + iddevolucion, function (r) {
		$("#btnGuardar3").show();
		$("#detalles2").html(r);
		verificarCantidades();
		$('[data-toggle="popover"]').popover();
	});
}

function mostrar3(iddevolucion) {
	limpiar();
	$("#btnGuardar4").hide();
	$("#codigo_pedido").val("");
	$('[data-toggle="popover"]').popover();

	$.post("../ajax/devoluciones.php?op=mostrar", { iddevolucion: iddevolucion }, function (data, status) {
		console.log(data);
		data = JSON.parse(data);
		console.log(data);

		$("#iddevolucion3").val(data.iddevolucion);
		$("#idalmacenero3").val(data.idalmacenero);
		$("#idalmacenero3").selectpicker('refresh');

		$("#codigo_pedido3").val(data.codigo_pedido);
		$("#telefono3").val(data.telefono);
		$("#empresa3").val(data.empresa);

		$("input[name='opcion'][value='" + data.opcion + "']").prop("checked", true);
		$('[data-toggle="popover"]').popover();

		$.post("../ajax/devoluciones.php?op=listarDetalle3&id=" + iddevolucion, function (r) {
			if (data.estado == "Finalizado")
				$("#btnGuardar4").hide();
			else
				$("#btnGuardar4").show();

			$("#detalles3").html(r);
			$('[data-toggle="popover"]').popover();
		});
	});

}

function mostrarComentario(iddevolucion) {
	$("#btnGuardar2").hide();
	$("#comentario").val("");
	$("#comentario").prop("disabled", true);
	$("#comentario").attr("placeholder", "Cargando...");
	$.post("../ajax/devoluciones.php?op=mostrarComentario", { iddevolucion: iddevolucion }, function (data, status) {
		console.log(data);
		data = JSON.parse(data);
		console.log(data);

		$("#iddevolucion").val(data.iddevolucion);
		$("#comentario").val(data.comentario);
		$("#script").empty().append(data.script);
	});
}

document.addEventListener('DOMContentLoaded', function () {
	init();
});