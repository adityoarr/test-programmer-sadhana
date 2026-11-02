<script>
    const items = @json($items);
    const initialItems = @json(($po ?? null)?->items ?? collect());

    let rowIdx = 0;

    function addRow(data = {}) {
        const tbody = document.querySelector('#items-table tbody');
        const tr = document.createElement('tr');

        const options = items.map((item) => {
            const selected = Number(data.item_id) === Number(item.id) ? 'selected' : '';
            return `<option value="${item.id}" data-price="${item.purchase_price}" ${selected}>${item.code} - ${item.name}</option>`;
        }).join('');

        tr.innerHTML = `
            <td>
                <select name="items[${rowIdx}][item_id]" class="form-select item-select" required>
                    <option value="">-- Pilih Barang --</option>
                    ${options}
                </select>
            </td>
            <td>
                <input type="number"
                       name="items[${rowIdx}][qty]"
                       min="1"
                       value="${data.qty ?? 1}"
                       class="form-control qty"
                       required>
            </td>
            <td>
                <input type="number"
                       name="items[${rowIdx}][price]"
                       min="0"
                       step="0.01"
                       value="${data.price ?? 0}"
                       class="form-control price"
                       required>
            </td>
            <td class="text-end subtotal">0</td>
            <td>
                <button type="button" class="btn btn-sm btn-danger remove-row">&times;</button>
            </td>
        `;

        tbody.appendChild(tr);
        rowIdx++;
        calculate();
    }

    function calculate() {
        let subtotal = 0;

        document.querySelectorAll('#items-table tbody tr').forEach((tr) => {
            const qty = parseFloat(tr.querySelector('.qty')?.value) || 0;
            const price = parseFloat(tr.querySelector('.price')?.value) || 0;
            const lineTotal = qty * price;

            tr.querySelector('.subtotal').textContent = lineTotal.toLocaleString('id-ID');
            subtotal += lineTotal;
        });

        const discount = parseFloat(document.getElementById('discount')?.value) || 0;
        const tax = parseFloat(document.getElementById('tax')?.value) || 0;

        document.getElementById('subtotal').textContent = subtotal.toLocaleString('id-ID');
        document.getElementById('total').textContent = (subtotal - discount + tax).toLocaleString('id-ID');
    }

    document.addEventListener('click', (e) => {
        if (e.target.classList.contains('remove-row')) {
            e.target.closest('tr').remove();
            calculate();
        }
    });

    document.addEventListener('change', (e) => {
        if (e.target.classList.contains('item-select')) {
            const selected = e.target.selectedOptions[0];
            if (selected && selected.value) {
                e.target.closest('tr').querySelector('.price').value = selected.dataset.price;
            }
        }

        if (e.target.matches('.qty, .price, #discount, #tax, .item-select')) {
            calculate();
        }
    });

    document.getElementById('add-row').addEventListener('click', () => addRow());

    if (initialItems.length) {
        initialItems.forEach((line) => addRow(line));
    } else {
        addRow();
    }

    calculate();
</script>