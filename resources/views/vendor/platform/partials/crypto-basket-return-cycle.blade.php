@php
    $existingCycles = old('return_cycles_json')
        ? json_decode(old('return_cycles_json'), true)
        : (isset($returnCycles)
            ? $returnCycles
            : []);
@endphp

<div class="mb-3" id="return-cycles-wrapper">
    <div id="return-cycles-container"></div>

    <button type="button" class="btn btn-primary mt-2" onclick="addReturnCycle()">+ Add Return Cycle</button>

    <!-- Hidden input for submitting JSON -->
    <input type="hidden" name="return_cycles_json" id="return_cycles_json">
</div>

<script>
(function () {
    const existingCycles = @json($existingCycles);
    const defaultMonths = [3, 6, 9, 12];

    function createCycleRow(cycle = { months: '', return_percentage: '' }) {
        const container = document.createElement('div');
        container.className = 'row mb-2 align-items-end return-cycle-row';

        container.innerHTML = `
            <div class="col-md-5">
                <label>Months</label>
                <input type="number" class="form-control months-input" value="${cycle.months}" required>
            </div>
            <div class="col-md-5">
                <label>Return %</label>
                <input type="number" class="form-control percentage-input" value="${cycle.return_percentage}" 
                       required min="0" step="0.01">
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-danger" onclick="removeReturnCycle(this)">-</button>
            </div>
        `;

        document.getElementById('return-cycles-container').appendChild(container);
        updateReturnCyclesJson();
    }

    window.addReturnCycle = function () {
        createCycleRow();
    };

    window.removeReturnCycle = function (button) {
        const row = button.closest('.return-cycle-row');
        row.remove();
        updateReturnCyclesJson();
    };

    function updateReturnCyclesJson() {
        const rows = document.querySelectorAll('.return-cycle-row');
        const data = Array.from(rows).map(row => ({
            months: row.querySelector('.months-input').value,
            return_percentage: row.querySelector('.percentage-input').value
        }));

        const hiddenInput = document.getElementById('return_cycles_json');
        if (hiddenInput) {
            hiddenInput.value = JSON.stringify(data);
        }
    }

    function initializeReturnCycleFields() {
        const wrapper = document.getElementById('return-cycles-wrapper');
        const container = document.getElementById('return-cycles-container');

        if (!wrapper || !container) return;

        container.innerHTML = ''; // Clear existing rows

        if (existingCycles.length > 0) {
            existingCycles.forEach(cycle => createCycleRow(cycle));
        } else {
            defaultMonths.forEach(month => {
                createCycleRow({ months: month, return_percentage: '' });
            });
        }

        container.addEventListener('input', updateReturnCyclesJson);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initializeReturnCycleFields);
    } else {
        initializeReturnCycleFields();
    }

    setTimeout(() => {
        initializeReturnCycleFields();
    }, 200);
})();
</script>

