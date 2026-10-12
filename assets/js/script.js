(() => {
    const enc = (value) => encodeURIComponent(String(value || '').trim());

    const showImageError = (image) => {
        if (!image || image.dataset.imageFailed === '1') return;
        image.dataset.imageFailed = '1';
        image.style.display = 'none';
        const box = image.parentElement;
        if (!box) return;
        box.classList.add('poster-error', 'product-visual-error');
        if (!box.querySelector('.poster-empty, .product-visual-empty')) {
            const empty = document.createElement('div');
            empty.className = box.classList.contains('poster') ? 'poster-empty' : 'product-visual-empty';
            empty.innerHTML = '<span>Gambar tidak tersedia</span>';
            box.appendChild(empty);
        }
    };

    const wikipediaFallback = async (image, title) => {
        try {
            const response = await fetch(
                'https://en.wikipedia.org/w/api.php?action=query&generator=search&gsrsearch=' + enc(title) +
                '&gsrnamespace=0&gsrlimit=5&prop=pageimages&piprop=thumbnail&pithumbsize=700&format=json&origin=*',
                { credentials: 'omit' }
            );
            if (!response.ok) return false;
            const data = await response.json();
            const pages = Object.values(data?.query?.pages || {});
            const page = pages.find(item => item?.thumbnail?.source);
            if (!page?.thumbnail?.source) return false;
            image.dataset.fallbackStage = 'wikipedia';
            image.src = page.thumbnail.source;
            return true;
        } catch (_) {
            return false;
        }
    };

    window.imageFallback = async (image) => {
        if (!image || image.dataset.imageFailed === '1') return;

        const title = image.dataset.posterTitle || image.dataset.productTitle ||
            image.alt.replace(/^Poster\s+/i, '').trim();
        const original = image.dataset.originalImage || '';
        const stage = image.dataset.fallbackStage || '';

        // First fallback: an external image proxy. This keeps the URL from the
        // database intact while avoiding hotlink restrictions in the browser.
        if (stage === '' && original) {
            image.dataset.fallbackStage = 'wsrv';
            image.src = 'https://wsrv.nl/?url=' + enc(original);
            return;
        }

        // Second fallback: Wikipedia thumbnail by title.
        if (stage !== 'wikipedia' && title) {
            const ok = await wikipediaFallback(image, title);
            if (ok) return;
        }

        showImageError(image);
    };
})();

document.addEventListener('DOMContentLoaded', () => {
    const formatRupiah = (value) => 'Rp ' + new Intl.NumberFormat('id-ID').format(value);

    const cards = document.querySelectorAll('.product-card');
    const totalElement = document.getElementById('fnv-total');

    const clampQuantity = (input) => {
        const max = Number(input.max) || 20;
        const value = Number.parseInt(input.value, 10);
        input.value = Number.isNaN(value) ? 0 : Math.min(Math.max(value, 0), max);
    };

    const refreshTotal = () => {
        if (!totalElement) {
            return;
        }

        let total = 0;

        cards.forEach((card) => {
            const input = card.querySelector('.qty-input');
            total += Number(card.dataset.price) * Number(input.value);
            card.classList.toggle('is-selected', Number(input.value) > 0);
        });

        totalElement.textContent = formatRupiah(total);
    };

    document.addEventListener('click', (event) => {
        const button = event.target.closest('.qty-btn');

        if (!button) {
            return;
        }

        const input = button.parentElement.querySelector('.qty-input');
        const step = button.dataset.action === 'increase' ? 1 : -1;
        input.value = Number(input.value) + step;
        clampQuantity(input);
        refreshTotal();
    });

    cards.forEach((card) => {
        card.querySelector('.qty-input').addEventListener('input', (event) => {
            clampQuantity(event.target);
            refreshTotal();
        });
    });

    document.querySelectorAll('form[data-confirm]').forEach((form) => {
        form.addEventListener('submit', (event) => {
            if (!window.confirm(form.dataset.confirm)) {
                event.preventDefault();
            }
        });
    });

    refreshTotal();
});
