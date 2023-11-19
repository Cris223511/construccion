var tabla;
var idSession;
// var idLocal;

function init() {
	$('#listadoregistros').show();
	$('#formularioregistros').hide();
	$('#btnagregar').show();

	listar();

	$('#formulario').on('submit', function (e) {
		guardaryeditar(e);
	});

	$.post('../ajax/locales.php?op=selectLocalesUsuario', function (r) {
		console.log(r);
		$('#idlocal').html(r);
		$('#idlocal').selectpicker('refresh');

		// $.post('../ajax/usuario.php?op=getSessionId', function (r) {
		// 	console.log(r);
		// 	data = JSON.parse(r);
		// 	idLocal = data.idlocal;
		// 	$('#idlocal').val(idLocal);
		// 	$('#idlocal').selectpicker('refresh');
		// })
	});

	$('#mCajas').addClass('treeview active');
	$('#lCierres').addClass('active');
}

function listar() {
	let param1 = '';
	let param2 = '';
	let param3 = '';

	tabla = $('#tbllistado').dataTable(
		{
			'lengthMenu': [5, 10, 25, 75, 100],
			'aProcessing': true,
			'aServerSide': true,
			dom: '<Bl<f>rtip>',
			buttons: [
				'copyHtml5',
				'excelHtml5',
				'csvHtml5',
			],
			'ajax':
			{
				url: '../ajax/cajas.php?op=listar2',
				type: 'get',
				data: { param1: param1, param2: param2, param3: param3 },
				dataType: 'json',
				error: function (e) {
					console.log(e.responseText);
				}
			},
			'language': {
				'lengthMenu': 'Mostrar : _MENU_ registros',
				'buttons': {
					'copyTitle': 'Tabla Copiada',
					'copySuccess': {
						_: '%d líneas copiadas',
						1: '1 línea copiada'
					}
				}
			},
			'bDestroy': true,
			'iDisplayLength': 5,
			'order': [],
			'createdRow': function (row, data, dataIndex) {
				$(row).find('td:eq(0), td:eq(1), td:eq(3), td:eq(4), td:eq(5), td:eq(6), td:eq(7), td:eq(8)').addClass('nowrap-cell');
			}
		}).DataTable();
}

function resetear() {
	const selects = ["fecha_inicio", "fecha_fin", "idlocal"];

	for (const selectId of selects) {
		$("#" + selectId).val("");
		$("#" + selectId).selectpicker('refresh');
	}

	listar();
}

//Función buscar
function buscar() {
	let param1 = '';
	let param2 = '';
	let param3 = '';

	const selectFechaInicio = document.getElementById('fecha_inicio');
	const selectFechaFin = document.getElementById('fecha_fin');
	const selectLocal = document.getElementById('idlocal');

	if (selectFechaInicio.value === "" && selectFechaFin.value === "" && selectLocal.value === "") {
		alert("Debe seleccionar al menos un campo para realizar la búsqueda.");
		return;
	}

	if (selectFechaInicio.value !== "" || selectFechaFin.value !== "") {
		if (selectFechaInicio.value === "" || selectFechaFin.value === "") {
			alert("Los campos de fecha inicial y fecha final son obligatorios.");
			return;
		} else {
			const fechaInicio = new Date(selectFechaInicio.value);
			const fechaFin = new Date(selectFechaFin.value);

			if (fechaInicio > fechaFin) {
				alert("La fecha inicial no puede ser mayor que la fecha final.");
				return;
			}
		}
	}

	param1 = selectFechaInicio.value;
	param2 = selectFechaFin.value;
	param3 = selectLocal.value;

	tabla = $('#tbllistado').dataTable(
		{
			'lengthMenu': [5, 10, 25, 75, 100],//mostramos el menú de registros a revisar
			'aProcessing': true,//Activamos el procesamiento del datatables
			'aServerSide': true,//Paginación y filtrado realizados por el servidor
			dom: '<Bl<f>rtip>',//Definimos los elementos del control de tabla
			buttons: [
				'copyHtml5',
				'excelHtml5',
				'csvHtml5',
			],
			'ajax':
			{
				url: '../ajax/cajas.php?op=listar2',
				data: { param1: param1, param2: param2, param3: param3 },
				type: 'get',
				dataType: 'json',
				error: function (e) {
					console.log(e.responseText);
				}
			},
			'language': {
				'lengthMenu': 'Mostrar : _MENU_ registros',
				'buttons': {
					'copyTitle': 'Tabla Copiada',
					'copySuccess': {
						_: '%d líneas copiadas',
						1: '1 línea copiada'
					}
				}
			},
			'bDestroy': true,
			'iDisplayLength': 5,//Paginación
			'order': [],
			'createdRow': function (row, data, dataIndex) {
				$(row).find('td:eq(0), td:eq(1), td:eq(2), td:eq(3), td:eq(5), td:eq(6), td:eq(7), td:eq(8), td:eq(9), td:eq(10)').addClass('nowrap-cell');
			}
		}).DataTable();
}

init();

function cerrar(idcaja) {
	bootbox.confirm("¿Está seguro de cerrar la caja?", function (result) {
		if (result) {
			$.post("../ajax/cajas.php?op=cerrar", { idcaja: idcaja }, function (e) {
				bootbox.alert(e);
				tabla.ajax.reload();
			});
		}
	})
}