{{-- filepath: resources/views/vendor/platform/partials/crypto-basket-dynamic.blade.php --}}
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
</script>