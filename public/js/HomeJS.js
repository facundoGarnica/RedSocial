document.addEventListener('DOMContentLoaded', function () {
    // Reacciones a posts
    document.querySelectorAll('.form-reaccion').forEach(form => {
        const postId = form.dataset.postId;
        form.querySelectorAll('.reaction-btn').forEach(btn => {
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                const reaccion = btn.dataset.reaccion;
                enviarReaccionFetch(postId, reaccion, form);
            });
        });
    });

    // Reacciones a comentarios
    document.querySelectorAll('.reacciones-comentario').forEach(form => {
        const comentarioId = form.dataset.comentarioId;
        form.querySelectorAll('.reaction-btn').forEach(btn => {
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                const reaccion = btn.dataset.reaccion;
                enviarReaccionComentarioFetch(comentarioId, reaccion, form);
            });
        });
    });
});

async function enviarReaccionFetch(postId, reaccion, contenedor) {
    const formData = new FormData();
    formData.append('reaction', reaccion);

    try {
        const res = await fetch(`/socialred/public/index.php/reaccionarpost/${postId}`, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        const data = await res.json();

        if (res.ok) {
            // Actualiza los contadores dentro del contenedor
            contenedor.querySelectorAll('.reaction-btn').forEach(btn => {
                const tipo = btn.dataset.reaccion;
                const span = btn.querySelector('.contador');
                span.textContent = data.reacciones[tipo] ?? 0;
            });
        } else {
            alert(data.error || 'Error al reaccionar');
        }
    } catch (err) {
        console.error(err);
        alert('Error de red al reaccionar');
    }
}

async function enviarReaccionComentarioFetch(comentarioId, reaccion, contenedor) {
    const formData = new FormData();
    formData.append('reaction', reaccion);

    try {
        const res = await fetch(`/socialred/public/index.php/reaccioncomentario/${comentarioId}`, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        const data = await res.json();

        if (res.ok) {
            // Actualiza los contadores dentro del contenedor
            contenedor.querySelectorAll('.reaction-btn').forEach(btn => {
                const tipo = btn.dataset.reaccion;
                const span = btn.querySelector('.contador');
                span.textContent = data.reacciones[tipo] ?? 0;
            });
        } else {
            alert(data.error || 'Error al reaccionar');
        }
    } catch (err) {
        console.error(err);
        alert('Error de red al reaccionar');
    }
}
