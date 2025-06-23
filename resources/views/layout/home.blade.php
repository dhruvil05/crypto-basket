@extends('layout.home-layout')
@section('content')
    <section class="hero">
        <div class="container">
            <div class="hero-content">
                <h1>Diversify with Crypto Baskets</h1>
                <p>Invest in curated cryptocurrency baskets instead of individual coins. Reduce risk, maximize returns
                    with professionally managed crypto collections.</p>
                <div class="hero-buttons">
                    <a href="#" class="btn-primary">Explore Baskets</a>
                    <a href="#" class="btn-secondary">View Live Prices</a>
                </div>
            </div>
        </div>
    </section>

    <section class="features" id="features">
        <div class="container">
            <h2>Smart Crypto Baskets</h2>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">🗂️</div>
                    <h3>Diversified Baskets</h3>
                    <p>Invest in curated collections of cryptocurrencies instead of picking individual coins. Reduce
                        risk through professional diversification.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">⚡</div>
                    <h3>One-Click Investment</h3>
                    <p>Buy entire crypto baskets with a single transaction. No need to research and purchase multiple
                        cryptocurrencies separately.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">📊</div>
                    <h3>Expert Curation</h3>
                    <p>Baskets created by crypto experts based on themes like DeFi, Gaming, Layer 1s, and emerging
                        technologies.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="stats">
        <div class="container">
            <div class="stats-grid">
                <div class="stat-item">
                    <h3>15+</h3>
                    <p>Curated Baskets</p>
                </div>
                <div class="stat-item">
                    <h3>2M+</h3>
                    <p>Active Investors</p>
                </div>
                <div class="stat-item">
                    <h3>85%</h3>
                    <p>Avg. Basket Performance</p>
                </div>
                <div class="stat-item">
                    <h3>24/7</h3>
                    <p>Live Tracking</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Crypto Table Section -->
    <!-- Crypto Table Section -->
    <section class="crypto-table-section" id="crypto-prices">
        <div class="container">
            <h2>Live Crypto Prices</h2>
            <p class="section-subtitle">Track the performance of major crypto currencies in real-time</p>

            <!-- Search and Controls -->
            <div class="table-controls">
                <div class="search-container">
                    <span class="search-icon">🔍</span>
                    <input type="text" id="cryptoSearch" class="search-input" placeholder="Search crypto currencies...">
                </div>
                <div class="per-page-selector">
                    <label for="perPage">Show:</label>
                    <select id="perPage" class="per-page-select">
                        <option value="5">5</option>
                        <option value="10" selected>10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                    </select>
                    <span>per page</span>
                </div>
            </div>

            <div class="table-container">


                <table class="table crypto-table" id="crypto-table">
                    <thead>
                        <tr>
                            <th>Rank</th>
                            <th>Name</th>
                            <th>Symbol</th>
                            <th>Price</th>
                            <th>24h Change</th>
                            <th>Market Cap</th>
                            <th>Volume (24h)</th>
                        </tr>
                    </thead>
                    <tbody id="crypto-body">
                        <!-- Table rows will be populated by JavaScript -->
                    </tbody>
                </table>
                <div id="noResults" class="no-results" style="display: none;">
                    <p>No cryptocurrencies found matching your search.</p>
                </div>
            </div>

            <!-- Pagination -->
            <div class="pagination" id="pagination">
                <!-- Pagination will be populated by JavaScript -->
            </div>
        </div>
    </section>

    <section class="cta-section">
        <div class="container">
            <h2>Start Building Your Crypto Basket</h2>
            <p>Join thousands of investors who trust CryptoVault with their diversified crypto investments. Start with
                professionally curated baskets today.</p>
            <a href="#" class="btn-primary">Browse Baskets</a>
        </div>
    </section>
@endsection

@section('scripts')
    <script>
        const binanceSocket = 'wss://stream.binance.com:9443/ws/!ticker@arr';
        const coinMap = @json($coinMap); // Provided from your controller
        const prices = {}; // Stores all incoming crypto data
        let filteredAndSortedData = []; // Data after search and before pagination
        let currentPage = 1;
        let itemsPerPage = 10; // Default items per page, matching HTML default selected option
        let currentSort = 'market_cap'; // Default sort key
        let sortDirection = 'desc'; // Default sort direction (descending for market_cap)

        // Elements
        const cryptoBody = document.getElementById('crypto-body');
        const cryptoSearchInput = document.getElementById('cryptoSearch'); // Corrected ID
        const perPageSelect = document.getElementById('perPage'); // Corrected ID
        const paginationContainer = document.getElementById('pagination');
        const noResultsDiv = document.getElementById('noResults');
        const loaderElement = document.getElementById('loader'); // Assuming you have a loader div
        const cryptoTable = document.getElementById('crypto-table'); // Assuming you want to hide/show table

        // --- WebSocket Connection ---
        function connectWebSocket() {
            const ws = new WebSocket(binanceSocket);

            ws.onopen = () => {
                console.log('WebSocket connected.');
                // Assuming 'error-msg' is for general connection errors
                const errorMsgElement = document.getElementById('error-msg');
                if (errorMsgElement) {
                    errorMsgElement.classList.add('d-none');
                }
                if (loaderElement) {
                    loaderElement.classList.add('d-none'); // Hide loader on successful connection
                }
                if (cryptoTable) {
                    cryptoTable.classList.remove('d-none'); // Show table once connected
                }
            };

            ws.onmessage = (event) => {
                const updates = JSON.parse(event.data);
                updates.forEach(coin => {
                    const symbol = coin.s;
                    const baseSymbol = symbol.replace('USDT', ''); // e.g., BTCUSDT -> BTC

                    if (coinMap[baseSymbol]) {
                        // Update or add coin data
                        prices[symbol] = {
                            symbol: baseSymbol, // Use base symbol for easier mapping to CoinGecko data
                            fullSymbol: symbol, // Keep full symbol if needed elsewhere
                            name: coinMap[baseSymbol].name,
                            logo: coinMap[baseSymbol].logo,
                            market_cap: parseFloat(coinMap[baseSymbol].market_cap ||
                                0), // Ensure it's a number, default to 0
                            price: parseFloat(coin.c),
                            change: parseFloat(coin.P),
                            volume: parseFloat(coin.q),
                        };
                    }
                });
                // Every time new data arrives, re-filter, re-sort, and re-paginate
                updateTableData();
            };

            ws.onerror = (error) => {
                console.error('WebSocket error:', error);
                const errorMsgElement = document.getElementById('error-msg');
                if (errorMsgElement) {
                    errorMsgElement.classList.remove('d-none');
                }
            };

            ws.onclose = (event) => {
                console.warn('WebSocket closed:', event.code, event.reason);
                // Reconnect after a delay if connection is closed unexpectedly
                setTimeout(connectWebSocket, 3000);
            };
        }

        // --- Data Processing and Rendering ---
        function updateTableData() {
            let data = Object.values(prices); // Get all current crypto data

            // 1. Search Filtering
            const search = cryptoSearchInput.value.toLowerCase(); // Use cryptoSearchInput
            if (search) {
                data = data.filter(c =>
                    c.name.toLowerCase().includes(search) ||
                    c.symbol.toLowerCase().includes(search) ||
                    c.fullSymbol.toLowerCase().includes(search) // Also search full symbol like BTCUSDT
                );
            }

            // Show/hide no results message
            if (data.length === 0 && search) {
                noResultsDiv.style.display = 'block';
                cryptoBody.innerHTML = ''; // Clear table body
                paginationContainer.innerHTML = ''; // Clear pagination
                return; // No data to display
            } else {
                noResultsDiv.style.display = 'none';
            }

            // 2. Sorting
            data.sort((a, b) => {
                const valA = a[currentSort];
                const valB = b[currentSort];

                if (valA === undefined || valB === undefined) {
                    // Handle cases where a sort key might be missing (e.g., market_cap not available for all)
                    return 0;
                }

                if (sortDirection === 'asc') {
                    return valA - valB;
                } else {
                    return valB - valA;
                }
            });

            filteredAndSortedData = data; // Store for pagination

            // 3. Pagination
            paginateData(); // Call paginateData to render the current page
        }


        function paginateData() {
            const totalPages = Math.ceil(filteredAndSortedData.length / itemsPerPage);
            const startIndex = (currentPage - 1) * itemsPerPage;
            const endIndex = startIndex + itemsPerPage;
            const paginated = filteredAndSortedData.slice(startIndex, endIndex);

            renderTable(paginated);
            renderPagination(totalPages);
        }

        function renderTable(dataToRender) {
            cryptoBody.innerHTML = ''; // Clear existing rows

            if (dataToRender.length === 0) {
                // This case is already handled in updateTableData for search results
                return;
            }

            let rankOffset = (currentPage - 1) * itemsPerPage; // Calculate rank based on current page
            dataToRender.forEach((coin, index) => {
                const rank = rankOffset + index + 1; // Rank is 1-based index on the current page

                // Determine text color for 24h Change
                const changeClass = coin.change >= 0 ? 'text-success' : 'text-danger';

                // Format numbers for display
                const formattedPrice = `$${coin.price.toFixed(2)}`;
                const formattedChange = `${coin.change.toFixed(2)}%`;
                const formattedVolume = `$${(coin.volume / 1e9).toFixed(2)}B`; // Billions
                const formattedMarketCap = coin.market_cap ? `$${(coin.market_cap / 1e12).toFixed(2)}T` :
                    'N/A'; // Trillions, handle missing market_cap

                const row = `
                <tr>
                    <td>${rank}</td>
                    <td class="coin-name-col">
                        <img src="${coin.logo}" alt="${coin.name} logo" class="coin-logo">
                        <strong>${coin.name}</strong>
                    </td>
                    <td>${coin.symbol}</td> 
                    <td>${formattedPrice}</td>
                    <td class="${changeClass}">${formattedChange}</td>
                    <td>${formattedMarketCap}</td>
                    <td>${formattedVolume}</td>
                </tr>
            `;
                cryptoBody.insertAdjacentHTML('beforeend', row);
            });
        }


        function renderPagination(totalPages) {
            paginationContainer.innerHTML = ''; // Clear existing pagination

            if (totalPages <= 1) {
                return; // No need for pagination if only one page
            }

            const createPaginationItem = (text, page, isActive = false, isDisabled = false) => {
                const li = document.createElement('li');
                li.className = 'page-item';
                if (isActive) li.classList.add('active');
                if (isDisabled) li.classList.add('disabled');

                const a = document.createElement('a');
                a.className = 'page-link';
                a.href = '#';
                a.innerHTML = text;

                if (!isDisabled) {
                    a.addEventListener('click', (e) => {
                        e.preventDefault();
                        currentPage = page;
                        paginateData();
                    });
                }
                li.appendChild(a);
                return li;
            };

            // Previous button
            paginationContainer.appendChild(createPaginationItem('‹', currentPage - 1, false, currentPage === 1));

            // Page numbers
            const maxPagesToShow = 7; // Max number of page buttons to display
            let startPage = Math.max(1, currentPage - Math.floor(maxPagesToShow / 2));
            let endPage = Math.min(totalPages, startPage + maxPagesToShow - 1);

            if (endPage - startPage + 1 < maxPagesToShow) {
                startPage = Math.max(1, endPage - maxPagesToShow + 1);
            }

            if (startPage > 1) {
                paginationContainer.appendChild(createPaginationItem('1', 1));
                if (startPage > 2) {
                    const liDots = document.createElement('li');
                    liDots.className = 'page-item disabled';
                    liDots.innerHTML = '<span class="page-link">...</span>';
                    paginationContainer.appendChild(liDots);
                }
            }

            for (let i = startPage; i <= endPage; i++) {
                paginationContainer.appendChild(createPaginationItem(i, i, i === currentPage));
            }

            if (endPage < totalPages) {
                if (endPage < totalPages - 1) {
                    const liDots = document.createElement('li');
                    liDots.className = 'page-item disabled';
                    liDots.innerHTML = '<span class="page-link">...</span>';
                    paginationContainer.appendChild(liDots);
                }
                paginationContainer.appendChild(createPaginationItem(totalPages, totalPages));
            }


            // Next button
            paginationContainer.appendChild(createPaginationItem('›', currentPage + 1, false, currentPage === totalPages));
        }


        // --- Event Listeners ---
        cryptoSearchInput.addEventListener('input', () => {
            currentPage = 1; // Reset to first page on new search
            updateTableData();
        });

        perPageSelect.addEventListener('change', (e) => {
            itemsPerPage = parseInt(e.target.value);
            currentPage = 1; // Reset to first page when items per page changes
            updateTableData();
        });

        // Add event listeners for sortable headers
        document.querySelectorAll('.sortable').forEach(header => {
            header.addEventListener('click', () => {
                const sortKey = header.dataset.sort;

                // Toggle sort direction if clicking the same column
                if (currentSort === sortKey) {
                    sortDirection = sortDirection === 'asc' ? 'desc' : 'asc';
                } else {
                    currentSort = sortKey;
                    sortDirection = 'desc'; // Default to descending for new sort column
                }

                // Remove existing sort indicators
                document.querySelectorAll('.sortable').forEach(h => h.classList.remove('asc', 'desc'));

                // Add new sort indicator
                header.classList.add(sortDirection);

                currentPage = 1; // Reset to first page on sort change
                updateTableData();
            });
        });

        // --- Initial Call ---
        // Start WebSocket connection when the page loads
        document.addEventListener('DOMContentLoaded', () => {
            // Hide table and show loader initially
            if (loaderElement) {
                loaderElement.classList.remove('d-none');
            }
            if (cryptoTable) {
                cryptoTable.classList.add('d-none');
            }
            connectWebSocket();
        });
    </script>
@endsection
