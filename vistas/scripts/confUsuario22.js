objSelects = {};

//Función que se ejecuta al inicio
function init() {
	$("#formulario").on("submit", function (e) {
		guardaryeditar(e);
	});
	$('#mPerfilUsuario').addClass("treeview active");
	$('#lConfUsuario').addClass("active");

	// Cargamos los items al select "local principal"
	$.post("../ajax/locales.php?op=selectLocalASC", function (data) {
		// console.log(data);
		objSelects = JSON.parse(data);
		console.log(objSelects)
		if (objSelects.length != 0) {
			const select = $("#idlocal");

			select.empty();
			select.html('<option value="">- Seleccione -</option>');

			objSelects.locales.forEach(function (opcion) {
				select.append('<option value="' + opcion.idlocal + '" data-local-ruc="' + opcion.local_ruc + '">' + opcion.titulo + '</option>');
			});
			select.selectpicker('refresh');
			mostrar();
		} else {
			console.log("no hay datos =)")
			mostrar();
		}
	});
}

function actualizarRUC() {
	const select = document.getElementById("idlocal");
	const localRUCInput = document.getElementById("local_ruc");
	const selectedOption = select.options[select.selectedIndex];

	if (selectedOption.value !== "") {
		const localRUC = selectedOption.getAttribute('data-local-ruc');
		localRUCInput.value = localRUC;
	} else {
		localRUCInput.value = "";
	}
}

function guardaryeditar(e) {
	e.preventDefault(); //No se activará la acción predeterminada del evento
	$("#btnGuardar").prop("disabled", true);
	var formData = new FormData($("#formulario")[0]);

	$.ajax({
		url: "../ajax/confUsuario.php?op=guardaryeditar",
		type: "POST",
		data: formData,
		contentType: false,
		processData: false,
		success: function (datas) {
			if (datas == "El nombre que ha ingresado ya existe." || datas == "El número de documento que ha ingresado ya existe." || datas == "El email que ha ingresado ya existe." || datas == "El nombre del usuario que ha ingresado ya existe.") {
				bootbox.alert(datas);
				$("#btnGuardar").prop("disabled", false);
				return;
			}
			bootbox.alert(datas);
			actualizarInfoUsuario();
			$("#btnGuardar").prop("disabled", false);
		}
	});
}

// función para actualizar la información del usuario en sesión en tiempo real
function actualizarInfoUsuario() {
	$.ajax({
		url: "../ajax/confUsuario.php?op=actualizarSession",
		dataType: 'json',
		success: function (data) {
			console.log(data)
			// actualizar la imagen y el nombre del usuario en la cabecera
			$('.user-image, .img-circle').attr('src', '../files/usuarios/' + data.imagen);
			$('.user-menu .local').html('<strong> Local: ' + data.local + '</strong>');
			$('.user-menu .user').html(data.nombre + ' - ' + '<strong> Rol: ' + data.cargo + '</strong>');
			$("#imagenmuestra").attr("src", "../files/usuarios/" + data.imagen);
		}
	});
}

function mostrar() {
	$.post("../ajax/confUsuario.php?op=mostrar", function (data, status) {
		data = JSON.parse(data);
		console.log(data);
		$("#nombre").val(data.nombre);
		$("#tipo_documento").val(data.tipo_documento);
		$("#tipo_documento").selectpicker('refresh');
		$("#num_documento").val(data.num_documento);
		$("#direccion").val(data.direccion);
		$("#telefono").val(data.telefono);
		$("#email").val(data.email);
		$("#idlocal").val(data.idlocal);
		$("#idlocal").selectpicker('refresh');
		$("#local_ruc").val(data.local_ruc);
		$("#login").val(data.login);
		$("#clave").val(data.clave);
		$("#imagenmuestra").attr("src", "../files/usuarios/" + data.imagen);
		$("#imagenactual").val(data.imagen);
	});
}

init();