document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('seat-form');

    if (!form) {
        return;
    }

    const ticketPrice = Number(form.dataset.price);
    const maxSeats = Number(form.dataset.max);
    const inputs = Array.from(form.querySelectorAll('.seat input[type="checkbox"]'));
    const seatsElement = document.getElementById('selected-seats');
    const totalElement = document.getElementById('total-price');
    const continueButton = document.getElementById('continue-btn');

    const formatRupiah = (value) => 'Rp ' + new Intl.NumberFormat('id-ID').format(value);

    const refresh = () => {
        const selected = inputs.filter((input) => input.checked).map((input) => input.value);

        seatsElement.textContent = selected.length ? selected.join(', ') : '-';
        totalElement.textContent = formatRupiah(selected.length * ticketPrice);
        continueButton.disabled = selected.length === 0;
    };

    inputs.forEach((input) => {
        input.addEventListener('change', () => {
            const selectedCount = inputs.filter((item) => item.checked).length;

            if (selectedCount > maxSeats) {
                input.checked = false;
                seatsElement.textContent = 'Maksimal ' + maxSeats + ' kursi per pesanan';
                window.setTimeout(refresh, 1600);
                return;
            }

            refresh();
        });
    });

    refresh();
});
