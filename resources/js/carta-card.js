export function initCartaCard() {
    initCartaImageZoom();

    const pendingRequests = new Map();
    const DELAY_MS = 1000;

    // Wire up +/- buttons
    document.querySelectorAll('.btn-container .btn').forEach(button => {
        button.addEventListener('click', function () {
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

    // Wire up manual text input
    document.querySelectorAll('input[name="numero_in_collezione"]').forEach(input => {
        input.addEventListener('input', function () {
            saveCartaWithDelay(this);
        });
    });

    // Wire up rarity radio buttons — look up combo from data-combos
    document.querySelectorAll('.rarita-radio').forEach(radio => {
        radio.addEventListener('change', function () {
            const cartaId   = this.name.replace('rarita_sel_', '');
            const card      = this.closest('[data-carta-id="' + cartaId + '"]');
            const input     = card.querySelector('input[name="numero_in_collezione"]');
            const versioneId = input.dataset.versioneId ?? '';

            input.dataset.raritaId = this.value;
            input.value = getComboQty(card, this.value, versioneId);
        });
    });

    // Wire up version radio buttons — look up combo from data-combos
    document.querySelectorAll('.versione-radio').forEach(radio => {
        radio.addEventListener('change', function () {
            const cartaId  = this.name.replace('versione_sel_', '');
            const card     = this.closest('[data-carta-id="' + cartaId + '"]');
            const input    = card.querySelector('input[name="numero_in_collezione"]');
            const raritaId = input.dataset.raritaId ?? '';

            input.dataset.versioneId = this.value;
            input.value = getComboQty(card, raritaId, this.value);
        });
    });

    // Helper: read quantity from card's data-combos JSON for a given (raritaId, versioneId)
    function getComboQty(card, raritaId, versioneId) {
        try {
            const combos = JSON.parse(card.dataset.combos || '{}');
            const key = (raritaId ?? '') + '__' + (versioneId ?? '');
            return combos[key] ?? 0;
        } catch (e) {
            return 0;
        }
    }

    // Helper: update combo map after save
    function updateComboQty(card, raritaId, versioneId, qty) {
        try {
            const combos = JSON.parse(card.dataset.combos || '{}');
            const key    = (raritaId ?? '') + '__' + (versioneId ?? '');
            combos[key]  = qty;
            card.dataset.combos = JSON.stringify(combos);
        } catch (e) {}
    }

    function saveCartaWithDelay(input) {
        const cartaId   = input.dataset.idCarta;
        const raritaId  = input.dataset.raritaId  || null;
        const versioneId = input.dataset.versioneId || null;
        const quantita  = parseInt(input.value) || 0;
        const key = `${cartaId}__${raritaId ?? 'null'}__${versioneId ?? 'null'}`;

        if (pendingRequests.has(key)) {
            clearTimeout(pendingRequests.get(key));
        }

        const timeoutId = setTimeout(() => {
            salvaCartaAllaCollezione(cartaId, raritaId, versioneId, quantita, input);
            pendingRequests.delete(key);
        }, DELAY_MS);

        pendingRequests.set(key, timeoutId);
    }

    function salvaCartaAllaCollezione(cartaId, raritaId, versioneId, quantita, input) {
        const body = {
            car_id_carta: cartaId,
            quantita:     quantita,
        };
        if (raritaId)  body.rar_id_collezione_rarita = raritaId;
        if (versioneId) body.ver_id_versione         = versioneId;

        fetch('/api/collezione-utente/aggiorna', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(body)
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
                // Update the stored combo qty so switching selection shows correct value
                const card = document.querySelector('[data-carta-id="' + input.dataset.idCarta + '"]');
                if (card) updateComboQty(card, input.dataset.raritaId ?? '', input.dataset.versioneId ?? '', parseInt(input.value) || 0);
                input.classList.add('border-success');
                setTimeout(() => input.classList.remove('border-success'), 2000);
            }
        })
        .catch(error => {
            if (error.message === 'not_authenticated') {
                if (typeof openLoginModal === 'function') {
                    openLoginModal();
                }
                // Revert the input value
                input.value = Math.max(0, (parseInt(input.value) || 0) - Math.sign(quantita));
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
        // Handle click (desktop)
        img.addEventListener('click', (e) => {
            e.stopPropagation();
            imgInModal.src = img.src;
            imgInModal.alt = img.alt;
            modal.classList.add('active');

            initModal3DEffect(modal);
        });

        // Handle touch (mobile) - open modal on first tap
        img.addEventListener('touchstart', (e) => {
            // Check if modal is already active
            if (!modal.classList.contains('active')) {
                e.preventDefault();
                imgInModal.src = img.src;
                imgInModal.alt = img.alt;
                modal.classList.add('active');

                initModal3DEffect(modal);
            }
        }, { passive: false });
    });

    const closeModal = () => {
        modal.classList.remove('active');
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

    const applyTilt = (clientX, clientY) => {
        if (animationFrameId) cancelAnimationFrame(animationFrameId);
        animationFrameId = requestAnimationFrame(() => {
            const rect = immagine.getBoundingClientRect();
            const mouseX = clientX - (rect.left + rect.width  / 2);
            const mouseY = clientY - (rect.top  + rect.height / 2);
            const rotateY =  (mouseX / (rect.width  / 2)) * MAX_ROTATION;
            const rotateX = -(mouseY / (rect.height / 2)) * MAX_ROTATION;
            contenuto.style.transform = `rotateX(${rotateX}deg) rotateY(${rotateY}deg)`;
        });
    };

    const resetTilt = () => {
        if (animationFrameId) cancelAnimationFrame(animationFrameId);
        contenuto.style.transform = 'rotateX(0deg) rotateY(0deg)';
    };

    // ── Mouse (desktop) ────────────────────────────────────────────
    const handleMouseMove = (e) => applyTilt(e.clientX, e.clientY);
    immagine.addEventListener('mousemove', handleMouseMove);
    immagine.addEventListener('mouseleave', resetTilt);

    // ── Touch (mobile) ─────────────────────────────────────────────
    const handleTouchMove = (e) => {
        // Prevent page scroll while tilting
        e.preventDefault();
        const t = e.touches[0];
        applyTilt(t.clientX, t.clientY);
    };
    // Use the whole modal surface so the finger doesn't have to stay on the tiny image
    modal.addEventListener('touchmove', handleTouchMove, { passive: false });
    modal.addEventListener('touchend',  resetTilt);
    modal.addEventListener('touchcancel', resetTilt);

    // Store refs so they can be cleaned up when the modal is closed
    immagine._mouseMoveHandler = handleMouseMove;
    modal._touchMoveHandler    = handleTouchMove;
}

document.addEventListener('DOMContentLoaded', initCartaCard);
