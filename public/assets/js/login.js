$(document).ready(function () {
    // Mostrar / ocultar contraseña
    $('#togglePassword').click(function () {
        const input = $('#password');
        const icon = $(this).find('i');
        if (input.attr('type') === 'password') {
            input.attr('type', 'text');
            icon.removeClass('fa-eye').addClass('fa-eye-slash');
        } else {
            input.attr('type', 'password');
            icon.removeClass('fa-eye-slash').addClass('fa-eye');
        }
    });

    // Mostrar mensaje error (ejemplo básico)
    // Suponemos que PHP puede enviar en GET un ?error=1
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('error')) {
        // Crear toast dinámicamente
        const toastHTML = `
      <div class="toast" style="position: fixed; top: 20px; right: 20px;" data-delay="4000">
        <div class="toast-header bg-danger text-white">
          <strong class="mr-auto">Error de acceso</strong>
          <button type="button" class="ml-2 mb-1 close text-white" data-dismiss="toast">&times;</button>
        </div>
        <div class="toast-body">
          Usuario o contraseña incorrectos.
        </div>
      </div>
    `;
        $('body').append(toastHTML);
        $('.toast').toast('show');
    }
});
