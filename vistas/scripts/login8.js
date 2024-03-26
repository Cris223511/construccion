$("#frmAcceso").on('submit', function (e) {
    e.preventDefault();
    login = $("#login").val();
    clave = $("#clave").val();

    console.log("hace la validación =)");

    $.post("../ajax/usuario.php?op=verificar", { "logina": login, "clavea": clave },
        function (data) {
            console.log(data);
            if (data == 0) {
                $("#btnGuardar").prop("disabled", false);
                Swal.fire({
                    icon: 'error',
                    title: 'Sin acceso',
                    text: 'Su usuario está desactivado, comuníquese con el administrador.',
                })
            } else if (data == 1) {
                Swal.fire({
                    icon: 'error',
                    title: 'Sin acceso',
                    text: 'El usuario no se encuentra disponible, comuníquese con el administrador.',
                })
            } else if (data == 2) {
                Swal.fire({
                    icon: 'error',
                    title: 'Sin acceso',
                    text: 'El local en donde usted está trabajando está desactivado, comuníquese con el administrador.',
                })
            } else if (data == 3) {
                Swal.fire({
                    icon: 'error',
                    title: 'Sin acceso',
                    text: 'El local del usuario no existe, comuníquese con el administrador.',
                })
            } else if (data != "null") {
                Swal.fire({
                    icon: 'success',
                    title: 'Acceso correcto',
                    text: 'Te estamos redireccionando a la vista principal, espere un momento...'
                })
                setTimeout(function () {
                    $(location).attr("href", "escritorio.php");
                }, 1000);
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Sin acceso',
                    text: 'Usuario y/o contraseña incorrectos.',
                })
            }
        });
})

function mostrarOcultarClave() {
    console.log("di click =)");
    var claveInput = $('#clave');

    if ($('#rememberMe').is(':checked')) {
        claveInput.attr('type', 'text');
    } else {
        claveInput.attr('type', 'password');
    }
}

function mostrar() {
    $.post("../ajax/verPortada.php?op=mostrar", function (datas, status) {
        data = JSON.parse(datas);
        if (data != null) {
            console.log(data.imagen);
            $("#imagenmuestra").attr("src", "../files/portadas/" + data.imagen);
            $(".fondo-login").css("background-image", "url('../files/portadas/" + data.imagen + "')");
        } else {
            $("#imagenmuestra").attr("src", "../files/portadas/default.jpg");
            $(".fondo-login").css("background-image", "url('../files/portadas/default.jpg')");
        }
    });
}

mostrar();