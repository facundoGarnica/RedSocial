document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.form-reaccion').forEach(form => {
        const input = form.querySelector('.input-reaccion');
        form.querySelectorAll('.reaction-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                input.value = btn.dataset.reaccion;
                form.submit();
            });
        });
    });
});
