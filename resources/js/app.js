import './bootstrap';

const catatanForm = document.querySelector('[data-catatan-form]');

if (catatanForm) {
    const items = catatanForm.querySelector('[data-items]');
    const template = document.querySelector('#catatan-item-template');
    const jenis = catatanForm.querySelector('[data-jenis]');
    const total = catatanForm.querySelector('[data-total]');
    const totalHint = catatanForm.querySelector('[data-total-hint]');
    const currency = new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        maximumFractionDigits: 2,
    });

    const updateRows = () => {
        const rows = [...items.querySelectorAll('[data-item-row]')];

        rows.forEach((row, index) => {
            const select = row.querySelector('[data-field="barang_id"]');
            const quantity = row.querySelector('[data-field="jumlah"]');
            const selectLabel = row.querySelector('label[for^="barang-"]');
            const quantityLabel = row.querySelector('label[for^="jumlah-"]');
            const removeButton = row.querySelector('[data-remove-item]');

            select.name = `items[${index}][barang_id]`;
            select.id = `barang-${index}`;
            if (selectLabel) selectLabel.htmlFor = select.id;

            quantity.name = `items[${index}][jumlah]`;
            quantity.id = `jumlah-${index}`;
            if (quantityLabel) quantityLabel.htmlFor = quantity.id;

            removeButton.classList.toggle('hidden', rows.length === 1);
        });
    };

    const updateTotal = () => {
        const priceKey = jenis.value === 'kulakan' ? 'hargaBeli' : 'hargaJual';
        const priceLabel = jenis.value === 'kulakan' ? 'harga beli' : 'harga jual';
        let value = 0;

        items.querySelectorAll('[data-item-row]').forEach((row) => {
            const select = row.querySelector('[data-field="barang_id"]');
            const quantity = row.querySelector('[data-field="jumlah"]');
            const price = Number(select.selectedOptions[0]?.dataset[priceKey] ?? 0);
            const amount = Number(quantity.value);

            if (Number.isFinite(price) && Number.isFinite(amount) && amount > 0) {
                value += price * amount;
            }
        });

        total.textContent = currency.format(value);
        totalHint.textContent = `Berdasarkan ${priceLabel}`;
    };

    catatanForm.addEventListener('click', (event) => {
        const addButton = event.target.closest('[data-add-item]');
        if (addButton) {
            items.append(template.content.cloneNode(true));
            updateRows();
            updateTotal();
            return;
        }

        const removeButton = event.target.closest('[data-remove-item]');
        if (removeButton && items.querySelectorAll('[data-item-row]').length > 1) {
            removeButton.closest('[data-item-row]').remove();
            updateRows();
            updateTotal();
            return;
        }

        const stepButton = event.target.closest('[data-quantity-step]');
        if (stepButton) {
            const quantity = stepButton.closest('[data-item-row]').querySelector('[data-field="jumlah"]');
            const next = Math.round((Number(quantity.value || 0) + Number(stepButton.dataset.quantityStep)) * 100) / 100;
            quantity.value = Math.max(0.01, Math.min(9999999999.99, next)).toFixed(2);
            quantity.dispatchEvent(new Event('input', { bubbles: true }));
        }
    });

    catatanForm.addEventListener('input', updateTotal);
    catatanForm.addEventListener('change', updateTotal);
    updateRows();
    updateTotal();
}
