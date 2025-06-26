@php
    $cryptos = $cryptos ?? [];
    $selected = $selected ?? [];

    // Retrieve old input values, if available
    $oldCryptocurrencies = old('basket.cryptocurrencies', $selected['cryptocurrencies'] ?? []);
    $oldCoinIds = old('basket.coin_ids', $selected['coin_ids'] ?? []);
    $oldNames = old('basket.names', $selected['names'] ?? []);
    $oldPercentages = old('basket.percentages', $selected['percentages'] ?? []);
@endphp

<div id="crypto-basket-rows">
    @php
        $count = count($oldCryptocurrencies ?? []);
        $count = $count > 0 ? $count : 1; // At least one row
    @endphp

    @for($i = 0; $i < $count; $i++)
    <div class="row mb-2 crypto-basket-row">
        <div class="col-md-6">
            <select name="basket[cryptocurrencies][]" class="form-control crypto-select select2" required>
                <option value="">Select Cryptocurrency</option>
                @foreach($cryptos as $symbol => $crypto)
                    <option value="{{ $symbol }}"
                        data-coin_id="{{ $crypto['coin_id'] }}"
                        data-name="{{ $crypto['name'] }}"
                        @if(($oldCryptocurrencies[$i] ?? '') == $symbol) selected @endif
                    >
                        {{ $crypto['label'] }}
                    </option>
                @endforeach
            </select>
            <input type="hidden" name="basket[coin_ids][]" class="coin-id-field" value="{{ $oldCoinIds[$i] ?? '' }}">
            <input type="hidden" name="basket[names][]" class="coin-name-field" value="{{ $oldNames[$i] ?? '' }}">
        </div>
        <div class="col-md-4">
            <input type="number" name="basket[percentages][]" class="form-control"
                   placeholder="Percentage" min="0" max="100" step="0.01" required
                   value="{{ $oldPercentages[$i] ?? '' }}">
        </div>
        <div class="col-md-2">
            <button type="button" class="btn btn-danger remove-row" onclick="removeCryptoRow(this)">-</button>
        </div>
    </div>
    @endfor
</div>

<button type="button" class="btn btn-primary mt-2" onclick="addCryptoRow()">+ Add Currency</button>

<!-- Include Select2 assets -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    function initSelect2() {
        $('.select2').select2({
            placeholder: 'Select Cryptocurrency',
            allowClear: true,
            width: '100%'
        });
    }

    function updateHiddenFields(select) {
        let selected = select.options[select.selectedIndex];
        let row = select.closest('.crypto-basket-row');
        row.querySelector('.coin-id-field').value = selected.getAttribute('data-coin_id') || '';
        row.querySelector('.coin-name-field').value = selected.getAttribute('data-name') || '';
    }

    function addCryptoRow() {
        // Use the first row as template
        let firstRow = document.querySelector('.crypto-basket-row');

        // Get clean HTML (without Select2 wrapper elements)
        let cloneHtml = firstRow.cloneNode(true).outerHTML;

        // Insert the HTML to the container
        document.getElementById('crypto-basket-rows').insertAdjacentHTML('beforeend', cloneHtml);

        // Get the last added row (just inserted)
        let newRow = document.querySelectorAll('.crypto-basket-row');
        let addedRow = newRow[newRow.length - 1];

        // Reset the inputs
        addedRow.querySelector('select').selectedIndex = 0;
        addedRow.querySelector('.coin-id-field').value = '';
        addedRow.querySelector('.coin-name-field').value = '';
        addedRow.querySelector('input[type=number]').value = '';

        // Remove existing Select2 to avoid duplicates
        $(addedRow).find('select.select2').next('.select2-container').remove();

        // Re-initialize Select2 for the new select box
        $(addedRow).find('select.select2').select2({
            placeholder: 'Select Cryptocurrency',
            allowClear: true,
            width: '100%'
        });

        // Rebind change handler
        addedRow.querySelector('select').addEventListener('change', function () {
            updateHiddenFields(this);
        });

        // Set hidden fields based on initial select
        updateHiddenFields(addedRow.querySelector('select'));
    }

    function removeCryptoRow(btn) {
        const rows = document.querySelectorAll('.crypto-basket-row');
        if (rows.length > 1) {
            btn.closest('.crypto-basket-row').remove();
        }
    }

    document.addEventListener('turbo:load', function () {
        document.querySelectorAll('.crypto-select').forEach(function (select) {
            updateHiddenFields(select);
            select.addEventListener('change', function () {
                updateHiddenFields(this);
            });
        });

        initSelect2(); // Init once on page load
    });
</script>

{{-- <div id="crypto-basket-rows">
    @php
        $count = count($oldCryptocurrencies ?? []);
        $count = $count > 0 ? $count : 1; // At least one row
    @endphp

    @for($i = 0; $i < $count; $i++)
    <div class="row mb-2 crypto-basket-row">
        <div class="col-md-6">
            <select name="basket[cryptocurrencies][]" class="form-control crypto-select" required onchange="updateHiddenFields(this)">
                <option value="">Select Cryptocurrency</option>
                @foreach($cryptos as $symbol => $crypto)
                    <option value="{{ $symbol }}"
                        data-coin_id="{{ $crypto['coin_id'] }}"
                        data-name="{{ $crypto['name'] }}"
                        @if(($oldCryptocurrencies[$i] ?? '') == $symbol) selected @endif
                    >
                        {{ $crypto['label'] }}
                    </option>
                @endforeach
            </select>
            <input type="hidden" name="basket[coin_ids][]" class="coin-id-field"
                value="{{ $oldCoinIds[$i] ?? '' }}">
            <input type="hidden" name="basket[names][]" class="coin-name-field"
                value="{{ $oldNames[$i] ?? '' }}">
        </div>
        <div class="col-md-4">
            <input type="number" name="basket[percentages][]" class="form-control"
                placeholder="Percentage" min="0" max="100" step="0.01" required
                value="{{ $oldPercentages[$i] ?? '' }}">
        </div>
        <div class="col-md-2">
            <button type="button" class="btn btn-danger remove-row" onclick="removeCryptoRow(this)">-</button>
        </div>
    </div>
    @endfor
</div>
<button type="button" class="btn btn-primary mt-2" onclick="addCryptoRow()">+ Add Currency</button>

<script>
function addCryptoRow() {
    let row = document.querySelector('.crypto-basket-row');
    let clone = row.cloneNode(true);
    // Reset values
    clone.querySelector('select').selectedIndex = 0;
    clone.querySelector('input[type=number]').value = '';
    clone.querySelector('.coin-id-field').value = '';
    clone.querySelector('.coin-name-field').value = '';
    document.getElementById('crypto-basket-rows').appendChild(clone);
}
function removeCryptoRow(btn) {
    let rows = document.querySelectorAll('.crypto-basket-row');
    if (rows.length > 1) {
        btn.closest('.crypto-basket-row').remove();
    }
}
function updateHiddenFields(select) {
    let selected = select.options[select.selectedIndex];
    let row = select.closest('.crypto-basket-row');
    row.querySelector('.coin-id-field').value = selected.getAttribute('data-coin_id') || '';
    row.querySelector('.coin-name-field').value = selected.getAttribute('data-name') || '';
}
// Initialize hidden fields on page load
document.querySelectorAll('.crypto-select').forEach(function(select) {
    updateHiddenFields(select);
    select.addEventListener('change', function() {
        updateHiddenFields(this);
    });
});
</script> --}}
