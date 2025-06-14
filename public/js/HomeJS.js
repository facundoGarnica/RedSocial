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

    // Borrar posts vía fetch
    document.querySelectorAll('.btn-eliminar-post').forEach(button => {
        button.addEventListener('click', async function (e) {
            e.preventDefault();

            if (!confirm('¿Seguro que querés eliminar este post?')) {
                return;
            }

            // El formulario padre del botón
            const form = button.closest('form');
            if (!form) {
                alert('No se encontró el formulario para borrar el post.');
                return;
            }

            const url = form.action;
            const formData = new FormData(form);

            try {
                const res = await fetch(url, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (res.ok) {
                    // Eliminar post del DOM
                    const postItem = form.closest('.post-item');
                    if (postItem) {
                        postItem.remove();
                    }
                    alert('Post eliminado correctamente.');
                } else {
                    const data = await res.json();
                    alert(data.error || 'Error al eliminar el post.');
                }
            } catch (err) {
                console.error(err);
                alert('Error de red al eliminar el post.');
            }
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
