<?php require_once __DIR__ . '/../partials/header.php'; ?>

<div class="login-container">
    <div class="login-box">
        <h2 class="text-center mb-4">Crear Nueva Temporada</h2>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="<?= BASE_URL ?>temporadas/crear" class="needs-validation" novalidate>
            <div class="form-group mb-3">
                <label for="nombre">Nombre de la temporada</label>
                <input type="text" class="form-control" id="nombre" name="nombre" required>
                <div class="invalid-feedback">Por favor, introduce el nombre de la temporada.</div>
            </div>

            <div class="form-check mb-3">
                <input type="checkbox" class="form-check-input" id="activa" name="activa">
                <label class="form-check-label" for="activa">Activar temporada</label>
            </div>

            <button type="submit" class="btn btn-rioja btn-block">Crear</button>
            <a href="<?= BASE_URL ?>temporadas" class="btn btn-secondary btn-block mt-2">Cancelar</a>
        </form>
    </div>
</div>

<style>
    .login-container {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 70vh;
        background: #ffde59; /* Amarillo Rioja */
        padding: 20px;
    }
    .login-box {
        background: white;
        padding: 30px 40px;
        border-radius: 10px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        width: 100%;
        max-width: 400px;
        animation: fadeIn 0.7s ease-in-out;
    }
    .btn-rioja {
        background-color: #006633; /* Verde Rioja */
        color: white;
        font-weight: bold;
        transition: background-color 0.3s ease;
    }
    .btn-rioja:hover {
        background-color: #004d26;
        color: white;
    }
    @keyframes fadeIn {
        from {opacity: 0; transform: translateY(-15px);}
        to {opacity: 1; transform: translateY(0);}
    }
</style>

<script>
    // Bootstrap form validation (optional)
    (() => {
        'use strict'
        const forms = document.querySelectorAll('.needs-validation')
        Array.from(forms).forEach(form => {
            form.addEventListener('submit', event => {
                if (!form.checkValidity()) {
                    event.preventDefault()
                    event.stopPropagation()
                }
                form.classList.add('was-validated')
            }, false)
        })
    })()
</script>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
