    <footer class="main-footer">
      <div class="pull-right hidden-xs">
        <b>Version</b> 3.0.0
      </div>
      <strong>Copyright &copy; 2024 <a href="#" style="color: #002a8e;">Almacenes S.A.C</a>.</strong> Todos los derechos reservados.
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
      // Evento click en el documento
      $(document).on('click', function(e) {
        // Comprobar si el clic fue fuera del popover
        if ($(e.target).closest('[data-toggle="popover"]').length === 0) {
          // Cerrar el popover
          $('[data-toggle="popover"]').popover('hide');
        }
      });

      // Evitar que el popover se cierre al hacer clic dentro de él
      $(document).on('click', '.popover', function(e) {
        e.stopPropagation();
      });
    </script>

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
      $('#imagen, #imagen2').on('change', function() {
        const file = this.files[0];
        const maxSizeMB = 3;
        const maxSizeBytes = maxSizeMB * 1024 * 1024;
        const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/bmp'];

        // Validar tamaño
        if (file.size > maxSizeBytes) {
          bootbox.alert(`El archivo es demasiado grande. El tamaño máximo permitido es de ${maxSizeMB} MB.`);
          $(this).val('');
          $('#imagenmuestra').attr('src', '').hide();
          return;
        }

        // Validar tipo
        console.log("el file type es =) =>", file.type);
        if (!allowedTypes.includes(file.type)) {
          bootbox.alert('El archivo debe ser una imagen de tipo JPG, JPEG, PNG, JFIF o BMP.');
          $(this).val('');
          $('#imagenmuestra').attr('src', '').hide();
          return;
        }

        const reader = new FileReader();
        reader.onload = function(e) {
          $('#imagenmuestra').attr('src', e.target.result).show();
        };
        reader.readAsDataURL(file);
      });
    </script>

    <script>
      function capitalizarPalabras(palabra) {
        return palabra.charAt(0).toUpperCase() + palabra.slice(1);
      }

      function capitalizarTodasLasPalabras(palabra) {
        return palabra.toUpperCase();
      }

      const thElements = document.querySelectorAll("#tbllistado_1 th, #tbllistado_2 th, #tblarticulos th, #tbllistado th, #tbltrabajadores th");

      thElements.forEach((e) => {
        e.textContent = e.textContent.toUpperCase();
        e.classList.add('nowrap-cell');
      });

      const thElementsNoUpper = document.querySelectorAll("#detalles th");

      thElementsNoUpper.forEach((e) => {
        e.classList.add('nowrap-cell');
      });

      const boxTitle = document.querySelectorAll(".box-title");

      boxTitle.forEach((e) => {
        e.childNodes.forEach((node) => {
          if (node.nodeType === Node.TEXT_NODE) {
            node.textContent = node.textContent.toUpperCase();
          }
        });
      });

      function nowrapCells() {
        ["#tbllistado", "#detalles", "#tbllistado2", "#tbllistado3", "#tblarticulos", "#tbldetalles", "#tbldetalles2"].forEach(selector => {
          addClassToCells(selector, "nowrap-cell");
        });
      }

      function addClassToCells(selector, className) {
        var table = document.querySelector(selector);

        if (!table) return;

        var columnIndices = Array.from(table.querySelectorAll("th")).reduce((indices, th, index) => {
          if (["CLIENTE", "PROVEEDOR", "NOMBRE", "NOMBRE PRODUCTO", "NOMBRES", "DESCRIPCIÓN", "DESCRIPCIÓN DEL LOCAL", "UBICACIÓN DEL LOCAL", "ORIGEN", "DESTINO", "ALMACÉN", "LOCAL"].includes(th.innerText.trim())) {
            indices.push(index);
          }
          return indices;
        }, []);

        table.querySelectorAll("td, th").forEach((cell, index) => {
          var cellIndex = index % table.rows[0].cells.length;
          if (!columnIndices.includes(cellIndex)) {
            cell.classList.add(className);
          }
        });
      }

      $(document).ajaxSuccess(function(event, xhr, settings) {
        if (settings.url.includes("op=listar") || settings.url.includes("op=listarDetalle")) {
          nowrapCells();
        }
      });

      function changeValue(dropdown) {
        var option = dropdown.options[dropdown.selectedIndex].value;

        console.log(option);

        $("#num_documento").val("");

        if (option == 'DNI') {
          setMaxLength('#num_documento', 8);
          setMaxLength('#num_documento2', 8);
        } else if (option == 'CEDULA') {
          setMaxLength('#num_documento', 10);
          setMaxLength('#num_documento2', 10);
        } else if (option == 'CARNET DE EXTRANJERIA') {
          setMaxLength('#num_documento', 20);
          setMaxLength('#num_documento2', 20);
        } else {
          setMaxLength('#num_documento', 11);
          setMaxLength('#num_documento2', 11);
        }
      }

      function setMaxLength(fieldSelector, maxLength) {
        $(fieldSelector).attr('maxLength', maxLength);
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
        if ($(settings.nTable).is('#tbllistado_1') || $(settings.nTable).is('#tbllistado_2') || $(settings.nTable).is('#tbllistado') || $(settings.nTable).is('#tblarticulos') || $(settings.nTable).is('#detalles')) {
          const table = $(settings.nTable).DataTable();
          if (table.rows({
              page: 'current'
            }).count() > 0) {
            inicializeGLightbox();
            nowrapCells();
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
      function evitarCaracteresEspecialesCamposNumericos() {
        var camposNumericos = document.querySelectorAll('input[type="number"]:not(#ganancia)');

        camposNumericos.forEach(function(campo) {
          campo.addEventListener('keydown', function(event) {
            var teclasPermitidas = [46, 8, 9, 27, 13, 110, 190, 37, 38, 39, 40, 17, 82]; // ., delete, tab, escape, enter, flechas, Ctrl+R

            // Permitir Ctrl+C, Ctrl+V, Ctrl+X y Ctrl+A
            if ((event.ctrlKey || event.metaKey) && (event.which === 67 || event.which === 86 || event.which === 88 || event.which === 65)) {
              return;
            }

            // Permitir Ctrl+Z y Ctrl+Alt+Z
            if ((event.ctrlKey || event.metaKey) && event.which === 90) {
              if (!event.altKey) {
                // Permitir Ctrl+Z
                return;
              } else if (event.altKey) {
                // Permitir Ctrl+Alt+Z
                return;
              }
            }

            if (teclasPermitidas.includes(event.which) || (event.which >= 48 && event.which <= 57) || (event.which >= 96 && event.which <= 105) || event.which === 190 || event.which === 110) {
              // Si es una tecla permitida o numérica, no hacer nada
              return;
            } else {
              event.preventDefault(); // Prevenir cualquier otra tecla no permitida
            }
          });
        });
      }

      evitarCaracteresEspecialesCamposNumericos();
    </script>

    <script>
      function restrict(input) {
        var prev = input.getAttribute("data-prev");
        prev = (prev != '') ? prev : '';
        if (Math.round(input.value * 100) / 100 != input.value) {
          input.value = prev;
        }
        input.setAttribute("data-prev", input.value);
      }

      function aplicarRestrictATodosLosInputs() {
        var camposNumericos = document.querySelectorAll('input[type="number"]');
        camposNumericos.forEach(function(campo) {
          campo.addEventListener('input', function(event) {
            restrict(event.target);
          });
        });
      }

      aplicarRestrictATodosLosInputs();
    </script>

    <script>
      function convertirMayus(inputElement) {
        if (typeof inputElement.value === 'string') {
          inputElement.value = inputElement.value.toUpperCase();
        }
      }

      function onlyNumbersAndMaxLenght(input) {
        let newValue = "";

        if (input.value.length > input.maxLength)
          newValue = input.value.slice(0, input.maxLength);

        newValue = input.value.replace(/\D/g, '');
        input.value = newValue;
      }

      function onlyNumbers(input) {
        let newValue = "";
        newValue = input.value.replace(/\D/g, '');
        input.value = newValue;
      }

      function validarNumeroDecimal(input, maxLength) {
        input.value = input.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');

        if (input.value.length > maxLength) {
          input.value = input.value.slice(0, maxLength);
        }
      }
    </script>

    <script>
      function generarSiguienteCorrelativo(numero) {
        console.log("Número recibido por el servidor: ", numero);

        let numFormat = numero.trim();
        let num = isNaN(parseInt(numFormat, 10)) ? 0 : parseInt(numFormat, 10);

        console.log("Número a incrementar: ", num);
        num++;

        let siguienteCorrelativo = num < 10000 ? num.toString().padStart(4, '0') : num.toString();

        console.log("Número incrementado a setear: ", siguienteCorrelativo);
        return siguienteCorrelativo;
      }

      function limpiarCadena(cadena) {
        let cadenaLimpia = cadena.trim();
        cadenaLimpia = cadenaLimpia.replace(/^[\n\r]+/, '');
        return cadenaLimpia;
      }

      function formatearNumero() {
        var campos = ["#cod_2"];

        campos.forEach(function(campo) {
          let numValor = $(campo).val();
          if (typeof numValor !== 'undefined') {
            numValor = numValor.trim();
            let num = parseInt(numValor, 10);
            let numFormateado = num < 10000 ? num.toString().padStart(4, '0') : num.toString();
            $(campo).val(numFormateado);
          }
        });
      }
    </script>

    <script>
      $(document).on('show.bs.modal', function(event) {
        const modal = $(event.target);

        if (modal.hasClass('bootbox') && modal.hasClass('bootbox-confirm')) {
          modal.find('.modal-footer .btn-default').text('Cancelar');
          modal.find('.modal-footer .btn-primary').text('Aceptar');
        }

        const okButton = modal.find('.modal-footer button[data-bb-handler="ok"]');
        if (okButton.length) {
          okButton.text('Aceptar').removeClass('btn-default').addClass('btn-bcp');
        }
      });
    </script>
    </body>

    </html>