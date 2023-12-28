    <footer class="main-footer">
      <div class="pull-right hidden-xs">
        <b>Version</b> 3.0.0
      </div>
      <strong>Copyright &copy; 2023 <a href="#" style="color: #002a8e;">Almacenes S.A.C</a>.</strong> Todos los derechos reservados.
    </footer>
    <!-- jQuery -->
    <script src="../public/js/jquery-3.1.1.min.js"></script>
    <!-- Bootstrap 3.3.5 -->
    <script src="../public/js/bootstrap.min.js"></script>
    <!-- AdminLTE App -->
    <script src="../public/js/app.min.js"></script>
    <!-- Quagga JS -->
    <script src="../public/js/quagga.min.js"></script>
    <!-- Lightbox JS -->
    <script src="../public/glightbox/js/glightbox.min.js"></script>

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
      function inicializeGLightbox() {
        const glightbox = GLightbox({
          selector: '.glightbox'
        });

        const galelryLightbox = GLightbox({
          selector: ".galleria-lightbox",
        });
      }
    </script>

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

      $(document).on('draw.dt', function(e, settings) {
        if ($(settings.nTable).is('#tbllistado') || $(settings.nTable).is('#tblarticulos')) {
          const table = $(settings.nTable).DataTable();
          if (table.rows({
              page: 'current'
            }).count() > 0) {
            inicializeGLightbox();
          }
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
      function actualizarFecha() {
        var hoy = new Date();
        var fecha = hoy.getFullYear() + '-' + ('0' + (hoy.getMonth() + 1)).slice(-2) + '-' + ('0' + hoy.getDate()).slice(-2);
        var hora = ('0' + hoy.getHours()).slice(-2) + ':' + ('0' + hoy.getMinutes()).slice(-2);

        var fecha_hora = fecha + 'T' + hora;
        document.getElementById('fecha_hora').value = fecha_hora;
      }
    </script>

    <script>
      $('.selectpicker').selectpicker({
        noneResultsText: 'No se encontraron resultados.'
      });
    </script>

    <script>
      var camposNumericos = document.querySelectorAll('input[type="number"]');
      camposNumericos.forEach(function(campo) {
        campo.addEventListener('keydown', function(event) {
          var teclasPermitidas = [46, 8, 9, 27, 13, 110, 190]; // ., delete, tab, escape, enter
          if ((event.ctrlKey || event.metaKey) && event.which === 65) return; // Permitir Ctrl+A o Command+A
          if (teclasPermitidas.includes(event.which)) return; // Si es una tecla permitida, no hacer nada
          if ((event.which < 48 || event.which > 57) && (event.which < 96 || event.which > 105) && event.which !== 190 && event.which !== 110) {
            event.preventDefault(); // Prevenir cualquier otra tecla no numérica ni punto
          }
        });
      });
    </script>

    </body>

    </html>