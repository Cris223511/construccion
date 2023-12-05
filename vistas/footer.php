    <footer class="main-footer">
      <div class="pull-right hidden-xs">
        <b>Version</b> 3.0.0
      </div>
      <strong>Copyright &copy; 2023 <a href="#" style="color: #002a8e;">Sistema de construcciones</a>.</strong> Todos los derechos reservados.
    </footer>
    <!-- jQuery -->
    <script src="../public/js/jquery-3.1.1.min.js"></script>
    <!-- Bootstrap 3.3.5 -->
    <script src="../public/js/bootstrap.min.js"></script>
    <!-- AdminLTE App -->
    <script src="../public/js/app.min.js"></script>

    <!-- DATATABLES -->
    <script src="../public/datatables/jquery.dataTables.min.js"></script>
    <script src="../public/datatables/dataTables.buttons.min.js"></script>
    <script src="../public/datatables/buttons.html5.min.js"></script>
    <script src="../public/datatables/buttons.colVis.min.js"></script>
    <script src="../public/datatables/jszip.min.js"></script>
    <script src="../public/datatables/pdfmake.min.js"></script>
    <script src="../public/datatables/vfs_fonts.js"></script>

    <script src="../public/js/bootbox.min.js"></script>
    <script src="../public/js/bootstrap-select.min.js"></script>

    <script>
      function capitalizarPalabras(palabra) {
        return palabra.charAt(0).toUpperCase() + palabra.slice(1);
      }

      function capitalizarTodasLasPalabras(palabra) {
        return palabra.toUpperCase();
      }

      const thElements = document.querySelectorAll("#tbllistado th");

      thElements.forEach((e) => {
        e.textContent = e.textContent.toUpperCase();
      });

      const boxTitle = document.querySelectorAll(".box-title");

      boxTitle.forEach((e) => {
        e.childNodes.forEach((node) => {
          if (node.nodeType === Node.TEXT_NODE) {
            node.textContent = node.textContent.toUpperCase();
          }
        });
      });

      function changeValue(dropdown) {
        var option = dropdown.options[dropdown.selectedIndex].value;
        var field = $('#num_documento');

        $("#num_documento").val("");

        if (option == 'DNI') {
          field.attr('maxLength', 8);
        } else if (option == 'CEDULA') {
          field.attr('maxLength', 10);
        } else {
          field.attr('maxLength', 11);
        }
      }

      $('#mostrarClave').click(function() {
        var claveInput = $('#clave');
        var ojitoIcon = $('#mostrarClave i');

        if (claveInput.attr('type') === 'password') {
          claveInput.attr('type', 'text');
          ojitoIcon.removeClass('fa-eye').addClass('fa-eye-slash');
        } else {
          claveInput.attr('type', 'password');
          ojitoIcon.removeClass('fa-eye-slash').addClass('fa-eye');
        }
      });

      $(document).ajaxSuccess(function(event, xhr, settings) {
        if (settings.url.includes("op=listar") || settings.url.includes("op=guardaryeditar") || settings.url.includes("op=desactivar") || settings.url.includes("op=activar") || settings.url.includes("op=eliminar")) {
          $(".modal-footer .btn-primary").removeClass("btn-primary").addClass("btn-bcp");
        }
      });

      function evitarNegativo(e) {
        if (e.key === "-")
          e.preventDefault();
      }

      function validarNumeroDecimal(input, maxLength) {
        input.value = input.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');

        if (input.value.length > maxLength) {
          input.value = input.value.slice(0, maxLength);
        }
      }
    </script>

    <script>
      $('.selectpicker').selectpicker({
        noneResultsText: 'No se encontraron resultados.'
      });
    </script>

    <script>
      function actualizarFecha() {
        var hoy = new Date();
        var fecha = hoy.getFullYear() + '-' + ('0' + (hoy.getMonth() + 1)).slice(-2) + '-' + ('0' + hoy.getDate()).slice(-2);
        var hora = ('0' + hoy.getHours()).slice(-2) + ':' + ('0' + hoy.getMinutes()).slice(-2);

        var fecha_hora = fecha + 'T' + hora;
        document.getElementById('fecha_hora').value = fecha_hora;
      }
    </script>

    </body>

    </html>