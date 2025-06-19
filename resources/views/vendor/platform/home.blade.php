@extends('platform::home_layout')

@section('content')

<div class="container py-4">
    <h2 class="text-center mb-4">📊 Live Crypto Prices</h2>

    <div class="row mb-3">
        <div class="col-md-6">
            <input type="text" id="search" class="form-control" placeholder="Search by Symbol or Name">
        </div>
        <div class="col-md-6 text-end">
            <select id="sort-by" class="form-select w-auto d-inline">
                <option value="market_cap">Sort by Market Cap</option>
                <option value="price">Sort by Price</option>
                <option value="change">Sort by Change %</option>
                <option value="volume">Sort by Volume</option>
            </select>
        </div>
    </div>

    <div class="alert alert-danger d-none" id="error-msg">⚠️ Live data is temporarily unavailable. Reconnecting...</div>

    <div class="text-center py-3" id="loader">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>

    <table class="table table-bordered table-hover table-striped d-none" id="crypto-table">
        <thead class="table-light">
            <tr>
                <th>Logo</th>
                <th>Name</th>
                <th class="sortable" data-sort="symbol">Symbol ⬍</th>
                <th class="sortable" data-sort="price">Price ⬍</th>
                <th class="sortable" data-sort="change">Change (24h) ⬍</th>
                <th class="sortable" data-sort="volume">Volume ⬍</th>
                <th class="sortable" data-sort="market_cap">Market Cap ⬍</th>
            </tr>
        </thead>
        <tbody id="crypto-body"></tbody>
    </table>

    <nav class="d-flex justify-content-end">
        <ul class="pagination" id="pagination"></ul>
    </nav>
</div>
@endsection

@section('scripts')
<script>
const binanceSocket = 'wss://stream.binance.com:9443/ws/!ticker@arr';
const coinMap = @json($coinMap); // You should provide this from your controller
const prices = {};
let paginated = [], currentPage = 1, itemsPerPage = 30, isConnected = false;
let currentSort = 'market_cap';

function connectWebSocket() {
    const ws = new WebSocket(binanceSocket);

    ws.onopen = () => {
        isConnected = true;
        document.getElementById('error-msg').classList.add('d-none');
    };

    ws.onmessage = (event) => {
        const updates = JSON.parse(event.data);
        updates.forEach(coin => {
            const symbol = coin.s;
            const baseSymbol = symbol.replace('USDT', '');
            if (coinMap[baseSymbol]) {
                prices[symbol] = {
                    symbol,
                    name: coinMap[baseSymbol].name,
                    logo: coinMap[baseSymbol].logo,
                    market_cap: coinMap[baseSymbol].market_cap,
                    price: parseFloat(coin.c),
                    change: parseFloat(coin.P),
                    volume: parseFloat(coin.q),
                };
            }
        });
        document.getElementById('loader').classList.add('d-none');
        document.getElementById('crypto-table').classList.remove('d-none');
        paginateData();
    };

    ws.onerror = () => document.getElementById('error-msg').classList.remove('d-none');
    ws.onclose = () => setTimeout(connectWebSocket, 3000);
}

function paginateData() {
    let data = Object.values(prices);
    const search = document.getElementById('search').value.toLowerCase();

    if (search) {
        data = data.filter(c => c.name.toLowerCase().includes(search) || c.symbol.toLowerCase().includes(search));
    }

    data.sort((a, b) => b[currentSort] - a[currentSort]);

    const totalPages = Math.ceil(data.length / itemsPerPage);
    paginated = data.slice((currentPage - 1) * itemsPerPage, currentPage * itemsPerPage);
    renderTable();
    renderPagination(totalPages);
}

function renderTable() {
    const body = document.getElementById('crypto-body');
    body.innerHTML = '';
    paginated.forEach(coin => {
        const row = `
            <tr>
                <td><img src="${coin.logo}" width="24"></td>
                <td><strong>${coin.name}</strong></td>
                <td>${coin.symbol}</td>
                <td>$${coin.price.toFixed(2)}</td>
                <td class="${coin.change >= 0 ? 'text-success' : 'text-danger'}">${coin.change.toFixed(2)}%</td>
                <td>$${(coin.volume / 1e9).toFixed(2)}B</td>
                <td>$${(coin.market_cap / 1e12).toFixed(2)}T</td>
            </tr>
        `;
        body.insertAdjacentHTML('beforeend', row);
    });
}

function renderPagination(totalPages) {
    const pag = document.getElementById('pagination');
    pag.innerHTML = '';

    const prev = document.createElement('li');
    prev.className = 'page-item' + (currentPage === 1 ? ' disabled' : '');
    prev.innerHTML = `<a class="page-link" href="#">‹</a>`;
    prev.addEventListener('click', (e) => {
        e.preventDefault();
        if (currentPage > 1) {
            currentPage--;
            paginateData();
        }
    });
    pag.appendChild(prev);

    const pages = [];
    if (totalPages <= 7) {
        for (let i = 1; i <= totalPages; i++) pages.push(i);
    } else {
        if (currentPage <= 4) {
            pages.push(1, 2, 3, 4, 5, '...', totalPages);
        } else if (currentPage >= totalPages - 3) {
            pages.push(1, '...', totalPages - 4, totalPages - 3, totalPages - 2, totalPages - 1, totalPages);
        } else {
            pages.push(1, '...', currentPage - 1, currentPage, currentPage + 1, '...', totalPages);
        }
    }

    pages.forEach(page => {
        const li = document.createElement('li');
        if (page === '...') {
            li.className = 'page-item disabled';
            li.innerHTML = `<span class="page-link">…</span>`;
        } else {
            li.className = 'page-item' + (page === currentPage ? ' active' : '');
            li.innerHTML = `<a class="page-link" href="#">${page}</a>`;
            li.addEventListener('click', (e) => {
                e.preventDefault();
                currentPage = page;
                paginateData();
            });
        }
        pag.appendChild(li);
    });

    const next = document.createElement('li');
    next.className = 'page-item' + (currentPage === totalPages ? ' disabled' : '');
    next.innerHTML = `<a class="page-link" href="#">›</a>`;
    next.addEventListener('click', (e) => {
        e.preventDefault();
        if (currentPage < totalPages) {
            currentPage++;
            paginateData();
        }
    });
    pag.appendChild(next);
}

document.getElementById('search').addEventListener('input', () => {
    currentPage = 1;
    paginateData();
});

document.getElementById('sort-by').addEventListener('change', (e) => {
    currentSort = e.target.value;
    paginateData();
});

document.querySelectorAll('.sortable').forEach(col => {
    col.addEventListener('click', () => {
        currentSort = col.dataset.sort;
        paginateData();
    });
});

connectWebSocket();
</script>
@endsection
