$("#frmAcceso").on('submit', function (e) {
    e.preventDefault();
    $("#btnGuardar").prop("disabled", true);

    usuario = $("#usuario").val();
    clave = $("#clave").val();

    $.post('../ajax/login.php?op=verificar', { "usuario": usuario, "clave": clave },
        function (data) {
            console.log(data);
            var parsedData = JSON.parse(data);
            console.log(parsedData);
            if (parsedData != false) {
                Swal.fire({
                    icon: 'success',
                    title: 'Acceso correcto',
                    text: 'Te estamos redireccionando a la vista principal, espere un momento...'
                })
                setTimeout(function () {
                    $(location).attr("href", "../../dash/views/home/dashboard.php");
                }, 2500);
            } else {
                $("#btnGuardar").prop("disabled", false);
                Swal.fire({
                    icon: 'error',
                    title: 'Sin acceso',
                    text: 'Usuario y/o contraseña incorrectos.',
                })
            }
        });
})

function agregar(e) {
    e.preventDefault();
    $("#btnGuardar").prop("disabled", true);
    $("#btnGuardar").text("Cargando...").css("color", "white");

    var formData = new FormData($("#formulario")[0]);

    $.ajax({
        url: "../ajax/login.php?op=registro",
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
        success: function (data) {
            console.log(data)
            Swal.fire({
                icon: 'success',
                title: 'Bienvenido',
                text: 'Te haz registrado correctamente, te enviaremos a la página de iniciar sesión...'
            })
            $("#btnGuardar").prop("disabled", false);
            $("#btnGuardar").text("Registrarse");

            setTimeout(function () {
                $(location).attr("href", "../../../main/views/auth/signIn.php");
            }, 3000);
        }
    });
}