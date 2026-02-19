export function initCartaCard() {
    initCartaImageZoom();

    const buttons = document.querySelectorAll('.btn-container .btn');
    const inputs = document.querySelectorAll('input[name="numero_in_collezione"]');

    const pendingRequests = new Map();
    const DELAY_MS = 1000;

    buttons.forEach(button => {
        button.addEventListener('click', function() {
            const input = this.closest('.btn-container').querySelector('input[name="numero_in_collezione"]');
            let currentValue = parseInt(input.value) || 0;

            if (this.dataset.btnType === 'increment') {
                input.value = currentValue + 1;
            } else if (this.dataset.btnType === 'decrement' && currentValue > 0) {
                input.value = currentValue - 1;
            }

            saveCartaWithDelay(input);
        });
    });

    inputs.forEach(input => {
        input.addEventListener('input', function() {
            saveCartaWithDelay(this);
        });
    });

    function saveCartaWithDelay(input) {
        const cartaId = input.dataset.idCarta;
        const quantita = parseInt(input.value) || 0;

        if (pendingRequests.has(cartaId)) {
            clearTimeout(pendingRequests.get(cartaId));
        }

        const timeoutId = setTimeout(() => {
            salvaCartaAllaCollezione(cartaId, quantita, input);
            pendingRequests.delete(cartaId);
        }, DELAY_MS);

        pendingRequests.set(cartaId, timeoutId);
    }

    function salvaCartaAllaCollezione(cartaId, quantita, input) {
        fetch('/api/collezione-utente/aggiorna', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                car_id_carta: cartaId,
                quantita: quantita
            })
        })
        .then(response => {
            if (response.status === 401) {
                throw new Error('not_authenticated');
            }
            if (!response.ok) {
                throw new Error('Errore nel salvataggio');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                input.classList.add('border-success');
                setTimeout(() => {
                    input.classList.remove('border-success');
                }, 2000);
            }
        })
        .catch(error => {
            if (error.message === 'not_authenticated') {
                if (typeof openLoginModal === 'function') {
                    openLoginModal();
                }
                input.value = parseInt(input.value) - (Math.sign(quantita - (parseInt(input.value) - 1))) || 0;
            } else {
                console.error('Errore:', error);
                input.classList.add('border-danger');
                setTimeout(() => {
                    input.classList.remove('border-danger');
                }, 2000);
            }
        });
    }
}

function initCartaImageZoom() {
    const cartaImages = document.querySelectorAll('.carta-image');

    if (!document.getElementById('modal-carta-ingrandita')) {
        const modal = document.createElement('div');
        modal.id = 'modal-carta-ingrandita';
        modal.className = 'modal-carta-ingrandita';

        const contenuto = document.createElement('div');
        contenuto.className = 'modal-carta-contenuto';
        const img = document.createElement('img');
        contenuto.appendChild(img);
        modal.appendChild(contenuto);

        document.body.appendChild(modal);
    }

    const modal = document.getElementById('modal-carta-ingrandita');
    const imgInModal = modal.querySelector('img');

    cartaImages.forEach(img => {
        img.addEventListener('click', (e) => {
            e.stopPropagation();
            imgInModal.src = img.src;
            imgInModal.alt = img.alt;
            modal.classList.add('active');

            initModal3DEffect(modal);
        });
    });
    const closeModal = () => {
        modal.classList.remove('active');
        removeModal3DEffect();
    };


    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            closeModal();
        }
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && modal.classList.contains('active')) {
            closeModal();
        }
    });
}

function initModal3DEffect(modal) {
    const contenuto = modal.querySelector('.modal-carta-contenuto');
    const immagine = modal.querySelector('img');
    const MAX_ROTATION = 10;
    let animationFrameId = null;

    const handleMouseMove = (e) => {
        if (animationFrameId) {
            cancelAnimationFrame(animationFrameId);
        }

        animationFrameId = requestAnimationFrame(() => {
            const rect = immagine.getBoundingClientRect();
            const centerX = rect.left + rect.width / 2;
            const centerY = rect.top + rect.height / 2;

            const mouseX = e.clientX - centerX;
            const mouseY = e.clientY - centerY;

            const rotateY = (mouseX / (rect.width / 2)) * MAX_ROTATION;
            const rotateX = -(mouseY / (rect.height / 2)) * MAX_ROTATION;

            contenuto.style.transform = `rotateX(${rotateX}deg) rotateY(${rotateY}deg)`;
        });
    };

    immagine.addEventListener('mousemove', handleMouseMove);
    immagine._mouseMoveHandler = handleMouseMove;

    immagine.addEventListener('mouseleave', () => {
        contenuto.style.transform = 'rotateX(0deg) rotateY(0deg)';
        if (animationFrameId) {
            cancelAnimationFrame(animationFrameId);
        }
    });
    modal = document.getElementById('modal-carta-ingrandita');
    if (modal && modal._mouseMoveHandler) {
        modal.removeEventListener('mousemove', modal._mouseMoveHandler);
        const contenuto = modal.querySelector('.modal-carta-contenuto');
        contenuto.style.transform = 'rotateX(0deg) rotateY(0deg)';
    }
}

document.addEventListener('DOMContentLoaded', initCartaCard);
