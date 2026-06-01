(() => {
    const container = document.getElementById('formula-list');
    const addButton = document.getElementById('formula-add');
    const dataNode = document.getElementById('formula-options');

    if (!container || !addButton || !dataNode) {
        return;
    }

    const options = JSON.parse(dataNode.textContent || '[]');
    let index = container.querySelectorAll('.formula-row').length;

    const renderOptions = function () {
        return ['<option value="">Chọn nguyên liệu</option>'].concat(options.map(function (item) {
            return '<option value="' + item.id + '">' + item.label + '</option>';
        })).join('');
    };

    addButton.addEventListener('click', function () {
        const wrapper = document.createElement('div');
        wrapper.className = 'row g-3 align-items-end formula-row';
        wrapper.innerHTML = `
            <div class="col-md-7">
                <label class="form-label">Nguyên liệu</label>
                <select class="form-select" name="items[${index}][ma_nguyen_lieu]">${renderOptions()}</select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Khối lượng hiện tại</label>
                <input class="form-control" type="number" step="0.01" min="0" name="items[${index}][so_luong]">
            </div>
            <div class="col-md-1"><button class="btn btn-outline-danger w-100 remove-row" type="button">-</button></div>
        `;
        container.appendChild(wrapper);
        index += 1;
    });

    container.addEventListener('click', function (event) {
        if (event.target.classList.contains('remove-row')) {
            event.target.closest('.formula-row').remove();
        }
    });
})();
