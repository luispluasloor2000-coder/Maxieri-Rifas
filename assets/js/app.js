document.addEventListener('click', (event) => {
    const confirmTarget = event.target.closest('[data-confirm]');
    if (confirmTarget && !window.confirm(confirmTarget.dataset.confirm)) {
        event.preventDefault();
    }

    const closeButton = event.target.closest('[data-close-modal]');
    if (closeButton) {
        closeButton.closest('dialog')?.close();
    }

    const numberButton = event.target.closest('.admin-board .number-cell');
    if (numberButton) {
        const modal = document.getElementById('numberModal');
        if (!modal) return;
        const data = JSON.parse(numberButton.dataset.number);
        document.getElementById('modalNumberId').value = data.id;
        document.getElementById('modalTitle').textContent = `Número ${data.number_value}`;
        document.getElementById('modalStatus').value = data.status;
        document.getElementById('modalBuyerName').value = data.buyer_name || '';
        document.getElementById('modalBuyerPhone').value = data.buyer_phone || '';
        document.getElementById('modalBuyerCity').value = data.buyer_city || '';
        document.getElementById('modalNotes').value = data.notes || '';
        document.getElementById('ticketLink').href = `${window.APP_BASE}/tickets/ticket.php?raffle_id=${window.RAFFLE_ID}&number=${data.number_value}`;
        document.getElementById('publicLink').href = `${window.APP_BASE}/rifa/${window.RAFFLE_ID}/numero/${data.number_value}`;
        const sellButton = modal.querySelector('button[value="sell_print"]');
        if (sellButton) {
            sellButton.disabled = data.status === 'sold';
            sellButton.textContent = data.status === 'sold' ? 'Ya vendido' : 'Vender e imprimir';
        }
        modal.showModal();
    }
});

document.addEventListener('submit', (event) => {
    const form = event.target.closest('#numberModal form');
    const button = event.submitter;
    if (!form || !button || button.value !== 'sell_print') return;

    const buyer = document.getElementById('modalBuyerName');
    if (buyer && buyer.value.trim() === '') {
        event.preventDefault();
        buyer.focus();
    }
});

function updateCountdowns() {
    document.querySelectorAll('[data-countdown]').forEach((box) => {
        const output = box.querySelector('.countdown-output');
        if (!output) return;
        const target = new Date(box.dataset.countdown.replace(' ', 'T')).getTime();
        const diff = target - Date.now();
        if (Number.isNaN(target)) {
            output.textContent = '';
            return;
        }
        if (diff <= 0) {
            output.textContent = 'Sorteo finalizado o en curso';
            return;
        }
        const days = Math.floor(diff / 86400000);
        const hours = Math.floor((diff % 86400000) / 3600000);
        const minutes = Math.floor((diff % 3600000) / 60000);
        output.textContent = `${days}d ${hours}h ${minutes}m`;
    });
}

updateCountdowns();
setInterval(updateCountdowns, 30000);

function updateAdminBoard(numbers) {
    const board = document.querySelector('.admin-board');
    if (!board) return;

    numbers.forEach((number) => {
        const cell = board.querySelector(`.number-cell[data-number-id="${number.id}"]`);
        if (!cell) return;

        cell.classList.remove('available', 'reserved', 'sold');
        cell.classList.add(number.status);
        cell.dataset.number = JSON.stringify(number);
    });
}

function pollAdminBoard() {
    const board = document.querySelector('.admin-board');
    if (!board || !window.RAFFLE_ID) return;

    const base = window.APP_BASE || '';
    fetch(`${base}/api/numbers.php?raffle_id=${window.RAFFLE_ID}`, {
        headers: { 'Accept': 'application/json' },
        credentials: 'same-origin',
    })
        .then((response) => response.ok ? response.json() : null)
        .then((payload) => {
            if (payload && payload.ok) {
                updateAdminBoard(payload.numbers);
            }
        })
        .catch(() => {});
}

if (document.querySelector('.admin-board')) {
    setInterval(pollAdminBoard, 5000);
}

function updatePublicBoard(numbers) {
    const board = document.querySelector('.number-board:not(.admin-board)');
    if (!board) return;

    numbers.forEach((number) => {
        const cell = board.querySelector(`.number-cell[data-number-value="${number.number_value}"]`);
        if (!cell) return;

        cell.classList.remove('available', 'reserved', 'sold');
        cell.classList.add(number.status);
    });
}

function pollPublicBoard() {
    const board = document.querySelector('.number-board:not(.admin-board)');
    if (!board || !window.RAFFLE_ID) return;

    const base = window.APP_BASE || '';
    fetch(`${base}/api/public_numbers.php?raffle_id=${window.RAFFLE_ID}`, {
        headers: { 'Accept': 'application/json' },
    })
        .then((response) => response.ok ? response.json() : null)
        .then((payload) => {
            if (payload && payload.ok) {
                updatePublicBoard(payload.numbers);
            }
        })
        .catch(() => {});
}

if (document.querySelector('.number-board:not(.admin-board)')) {
    setInterval(pollPublicBoard, 5000);
}

if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        const base = window.APP_BASE || '';
        navigator.serviceWorker.register(`${base}/service-worker.js`).catch(() => {});
    });
}
