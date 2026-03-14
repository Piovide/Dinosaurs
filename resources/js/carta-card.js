export function initCartaCard() {
    initCartaImageZoom();

    const pendingRequests = new Map();
    const DELAY_MS = 1000;

    // Wire up +/- buttons
    document.querySelectorAll('.btn-container .btn:not([data-cc-init])').forEach(button => {
        button.dataset.ccInit = '1';
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
    document.querySelectorAll('input[name="numero_in_collezione"]:not([data-cc-init])').forEach(input => {
        input.dataset.ccInit = '1';
        input.addEventListener('input', function () {
            saveCartaWithDelay(this);
        });
    });

    // Wire up rarity radio buttons — look up combo from data-combos
    document.querySelectorAll('.rarita-radio:not([data-cc-init])').forEach(radio => {
        radio.dataset.ccInit = '1';
        radio.addEventListener('change', function () {
            const cartaId = this.name.replace('rarita_sel_', '');
            const card = this.closest('[data-carta-id="' + cartaId + '"]');
            const input = card.querySelector('input[name="numero_in_collezione"]');
            if (!input) return;
            const versioneId = input.dataset.versioneId ?? '';

            input.dataset.raritaId = this.value;
            input.value = getComboQty(card, this.value, versioneId);
            console.log('[CartaCard] rarita cambiata →', { cartaId, raritaId: this.value, versioneId, qty: input.value });
        });
    });

    // Wire up version radio buttons — look up combo from data-combos
    document.querySelectorAll('.versione-radio:not([data-cc-init])').forEach(radio => {
        radio.dataset.ccInit = '1';
        radio.addEventListener('change', function () {
            const cartaId = this.name.replace('versione_sel_', '');
            const card = this.closest('[data-carta-id="' + cartaId + '"]');
            const input = card.querySelector('input[name="numero_in_collezione"]');
            const raritaId = input?.dataset?.raritaId ?? '';

            // Reset nested version selects when a radio version is chosen (visual — runs always).
            card.querySelectorAll('.versione-select').forEach(select => {
                select.selectedIndex = 0;
                select.classList.remove('versione-select-active');
                const wrap = select.closest('.versione-select-wrap');
                if (wrap) wrap.classList.remove('versione-select-wrap-active');
            });

            if (!input) return;

            input.dataset.versioneId = this.value;
            input.value = getComboQty(card, raritaId, this.value);
            console.log('[CartaCard] versione radio cambiata →', { cartaId, raritaId, versioneId: this.value, qty: input.value });
        });
    });

    // Wire up nested version selects.
    document.querySelectorAll('.versione-select:not([data-cc-init])').forEach(select => {
        select.dataset.ccInit = '1';
        select.addEventListener('change', function () {
            const cartaId = this.dataset.cartaId;
            const card = this.closest('[data-combos]');
            const input = card.querySelector('input[name="numero_in_collezione"]');
            const raritaId = input?.dataset?.raritaId ?? '';

            // Selecting a nested option deselects all non-nested versions (visual — runs always).
            card.querySelectorAll('.versione-radio').forEach(radio => {
                radio.checked = false;
            });

            const selected = this.options[this.selectedIndex];
            const versioneId = selected?.dataset?.versioneId ?? '';

            console.log('[CartaCard] versione select cambiata →', {
                cartaId,
                raritaId,
                selectValue: this.value,
                dataVersioneId: selected?.dataset?.versioneId,
                versioneIdEffettivo: versioneId,
            });

            // Highlight only the active select (visual — runs always).
            card.querySelectorAll('.versione-select').forEach(other => {
                other.classList.remove('versione-select-active');
                const otherWrap = other.closest('.versione-select-wrap');
                if (otherWrap) otherWrap.classList.remove('versione-select-wrap-active');
            });

            if (versioneId) {
                this.classList.add('versione-select-active');
                const wrap = this.closest('.versione-select-wrap');
                if (wrap) wrap.classList.add('versione-select-wrap-active');
            }

            if (!input) return;

            input.dataset.versioneId = versioneId;
            input.value = getComboQty(card, raritaId, versioneId);
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
            const key = (raritaId ?? '') + '__' + (versioneId ?? '');
            combos[key] = qty;
            card.dataset.combos = JSON.stringify(combos);
        } catch (e) { }
    }

    function saveCartaWithDelay(input) {
        const cartaId = input.dataset.idCarta;
        const raritaId = input.dataset.raritaId || null;
        const versioneId = input.dataset.versioneId || null;
        const quantita = parseInt(input.value) || 0;
        const key = `${cartaId}__${raritaId ?? 'null'}__${versioneId ?? 'null'}`;

        console.log('[CartaCard] salvataggio in coda →', { cartaId, raritaId, versioneId, quantita, key });

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
            quantita: quantita,
        };
        if (raritaId) body.rar_id_collezione_rarita = raritaId;
        if (versioneId) body.ver_id_versione = versioneId;

        console.log('[CartaCard] → POST /api/collezione-utente/aggiorna', body);

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
                console.log('[CartaCard] ← risposta API', data);
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
    const cartaImages = document.querySelectorAll('.carta-image:not([data-cc-init])');

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
        img.dataset.ccInit = '1';

        // Handle click (desktop)
        img.addEventListener('click', (e) => {
            e.stopPropagation();
            imgInModal.src = img.src;
            imgInModal.alt = img.alt;
            modal.classList.add('active');

            initModal3DEffect(modal);
        });

        // Handle touch (mobile) - tap detection: only open if finger didn't scroll
        let touchStartX = 0, touchStartY = 0;
        img.addEventListener('touchstart', (e) => {
            touchStartX = e.touches[0].clientX;
            touchStartY = e.touches[0].clientY;
        }, { passive: true });

        img.addEventListener('touchend', (e) => {
            if (modal.classList.contains('active')) return;
            const dx = Math.abs(e.changedTouches[0].clientX - touchStartX);
            const dy = Math.abs(e.changedTouches[0].clientY - touchStartY);
            if (dx < 10 && dy < 10) {
                imgInModal.src = img.src;
                imgInModal.alt = img.alt;
                modal.classList.add('active');
                initModal3DEffect(modal);
            }
        }, { passive: true });
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
            const mouseX = clientX - (rect.left + rect.width / 2);
            const mouseY = clientY - (rect.top + rect.height / 2);
            const rotateY = (mouseX / (rect.width / 2)) * MAX_ROTATION;
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
    modal.addEventListener('touchend', resetTilt);
    modal.addEventListener('touchcancel', resetTilt);

    // Store refs so they can be cleaned up when the modal is closed
    immagine._mouseMoveHandler = handleMouseMove;
    modal._touchMoveHandler = handleTouchMove;
}

window.initCartaCard = initCartaCard;
document.addEventListener('DOMContentLoaded', initCartaCard);
