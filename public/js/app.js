document.addEventListener("turbolinks:load", function() {
    // Initialize page-specific content based on current path
    const currentPath = window.location.pathname;
    
    // Update sidebar active states
    document.querySelectorAll('.sideBar a').forEach(link => {
        link.classList.remove('active');
        if (link.getAttribute('href') === currentPath) {
            link.classList.add('active');
        }
    });

    // Initialize Dashboard
    if (currentPath === '/dashboard') {
        initializeDashboard();
    }
    
    // Initialize Transaction page
    if (currentPath === '/transaction') {
        initializeTransactionPage();
    }
    
    // Initialize Bookmark page
    if (currentPath === '/bookmark') {
        initializeBookmarkPage();
    }
});

// Clean up before caching
document.addEventListener("turbolinks:before-cache", function() {
    if (window.myChart) {
        window.myChart.destroy();
        window.myChart = null;
    }
    
    // Cancel any pending transaction requests
    if (currentRequest) {
        currentRequest.abort();
    }
});

function initializeDashboard() {
    const timeFilter = document.getElementById('timeFilter');
    if (!timeFilter) {
        console.error('Time filter not found');
        return;
    }

    console.log('Initializing dashboard...');
    loadChartData('monthly');

    timeFilter.addEventListener('change', function() {
        loadChartData(this.value);
    });
}

function loadChartData(timeFrame) {
    console.log('Fetching data for timeframe:', timeFrame);
    
    fetch(`/api/transactions/chart-data/${timeFrame}`)
        .then(response => response.json())
        .then(data => {
            console.log('Received chart data:', data);
            if (!data.labels || !data.income || !data.expense) {
                console.error('Invalid data format:', data);
                return;
            }
            if (data.labels.length === 0) {
                console.log('No data available for the selected period');
                return;
            }
            renderChart(data);
        })
        .catch(error => {
            console.error('Error loading chart data:', error);
        });
}

function renderChart(data) {
    const canvas = document.getElementById('transactionChart');
    if (!canvas) {
        console.error('Canvas element not found');
        return;
    }

    const ctx = canvas.getContext('2d');
    
    // Destroy existing chart if it exists
    if (window.myChart) {
        window.myChart.destroy();
    }

    // Basic chart configuration
    window.myChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: data.labels,
            datasets: [
                {
                    label: 'Income',
                    data: data.income,
                    borderColor: '#28a745',
                    backgroundColor: 'rgba(40, 167, 69, 0.1)',
                    fill: true
                },
                {
                    label: 'Expense',
                    data: data.expense,
                    borderColor: '#dc3545',
                    backgroundColor: 'rgba(220, 53, 69, 0.1)',
                    fill: true
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });
}

function initializeTransactionPage() {
    console.log('Initializing transaction page');
    
    // Initialize with default values
    let currentFilters = {
        type: 'all',
        time: 'today',
        sort: 'date',
        order: 'desc',
        page: 1
    };

    // Define currentPage globally for proper pagination tracking
    window.currentPage = 1;
    
    loadTransactions(currentFilters);

    // Add event listeners for filter dropdowns
    document.querySelectorAll('.dropdown-content a').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            const onclick = this.getAttribute('onclick');
            if (onclick.includes('updateFilter')) {
                const [filterType, value, label] = onclick.match(/'([^']+)'/g)
                    .map(str => str.replace(/'/g, ''));
                currentFilters[filterType] = value;
                
                // Reset to page 1 when filters change
                window.currentPage = 1;
                currentFilters.page = 1;
            } else if (onclick.includes('updateSort')) {
                const [field, order] = onclick.match(/'([^']+)'/g)
                    .map(str => str.replace(/'/g, ''));
                currentFilters.sort = field;
                currentFilters.order = order;
                
                // Reset to page 1 when sorting changes
                window.currentPage = 1;
                currentFilters.page = 1;
            }
            
            loadTransactions(currentFilters);
        });
    });
    
    // Add event listeners for pagination buttons
    const prevPageBtn = document.getElementById('prevPageBtn');
    const nextPageBtn = document.getElementById('nextPageBtn');
    
    if (prevPageBtn) {
        prevPageBtn.addEventListener('click', function() {
            if (window.currentPage > 1) {
                window.currentPage--;
                currentFilters.page = window.currentPage;
                loadTransactions(currentFilters);
            }
        });
    }
    
    if (nextPageBtn) {
        nextPageBtn.addEventListener('click', function() {
            window.currentPage++;
            currentFilters.page = window.currentPage;
            loadTransactions(currentFilters);
        });
    }
}

// Global variable to track current request
let currentRequest = null;

function loadTransactions(filters) {
    console.log('Loading transactions with filters:', filters);
    
    const tableContainer = document.getElementById('transactionTable');
    const loadingAnimation = document.getElementById('loadingAnimation');
    if (!tableContainer || !loadingAnimation) return;
    
    // Show loading animation
    tableContainer.style.opacity = '0';
    loadingAnimation.style.display = 'block';

    // Cancel previous request if it exists
    if (currentRequest) {
        currentRequest.abort();
    }

    // Create new AbortController for this request
    const controller = new AbortController();
    currentRequest = controller;

    // Build query string - Ensure page parameter is set correctly
    const queryParams = new URLSearchParams();
    
    // Add all filters and ensure page is included
    for (const [key, value] of Object.entries(filters)) {
        queryParams.append(key, value);
    }
    
    // Explicitly ensure the page parameter is set from the window.currentPage
    queryParams.set('page', window.currentPage.toString());
    
    // Debug the request
    console.log(`📡 Loading page ${window.currentPage} with params:`, Object.fromEntries(queryParams.entries()));
    
    const queryString = queryParams.toString();

    // Make the request
    fetch(`/api/transactions?${queryString}`, {
        signal: controller.signal
    })
        .then(response => {
            if (!response.ok) {
                console.error(`❌ Server response not OK: ${response.status} ${response.statusText}`);
                throw new Error(`Server returned ${response.status}: ${response.statusText}`);
            }
            console.log(`📥 Response status: ${response.status}`);
            return response.json();
        })
        .then(data => {
            console.log(`✅ API response data:`, data);
            console.log(`📊 Data for page ${window.currentPage}:`, {
                total: data.total,
                current_page: data.current_page,
                last_page: data.last_page,
                data_length: data.data?.length
            });
            
            // Store the pagination data for future reference
            window.lastKnownPageData = {
                total: data.total || 0,
                current_page: data.current_page || window.currentPage,
                last_page: data.last_page || 1,
                per_page: data.per_page || 10
            };
            
            // Update the global currentPage with the value from the response
            window.currentPage = data.current_page || window.currentPage;
            
            // Clear the current request reference
            currentRequest = null;
            
            // Hide loading animation and show table
            loadingAnimation.style.display = 'none';
            tableContainer.innerHTML = generateTableHTML(data);
            console.log("table2 = ",tableContainer);
            requestAnimationFrame(() => {
                tableContainer.style.opacity = '1';
            });
            
            // Update the pagination controls
            updatePaginationControls(data);
        })
        .catch(error => {
            if (error.name === 'AbortError') {
                console.log('Request was cancelled');
            } else {
                console.error('Error loading transactions:', error);
                loadingAnimation.style.display = 'none';
                tableContainer.innerHTML = '<div style="text-align: center; padding: 2rem; color: #dc3545;">Error loading transactions. Please try again.</div>';
                tableContainer.style.opacity = '1';
            }
        });
}

function generateTableHTML(data) {
    return `
        <table>
            <thead class="head-table">
                <tr>
                    <th><input type="checkbox" id="selectAll" onclick="toggleAllCheckboxes()"></th>
                    <th>Transaction</th>
                    <th>Date</th>
                    <th>Amount</th>
                    <th>Type</th>
                    <th>Category</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                ${data.data.map((transaction, index) => {
                    console.log(`🆔 Row ${index + 1} - transaction:`, transaction); // ✅ Debug ข้อมูล transaction เต็มๆ
                    let transactionId = transaction.transaction_id ?? `row-${index}`;
                    console.log(`✅ Set data-id for row ${index + 1}:`, transactionId); // ✅ Debug ค่า data-id

                    return `
                    <tr style="animation-delay: ${index * 0.05}s">
                        <td><input type="checkbox" class="row-checkbox" data-id="${transactionId}"></td>
                        <td data-category="${transaction.category}">${transaction.description}</td>
                        <td>${new Date(transaction.transaction_date).toLocaleDateString()}</td>
                        <td>$${parseFloat(transaction.amount).toFixed(2)}</td>
                        <td data-type="${transaction.type}">${transaction.type === 'expense' ? 'Exp.' : 'Inc.'}</td>
                        <td>${transaction.category}</td>
                        <td>
                            <svg width="24" height="24" viewBox="0 0 24 24">
                                <path d="M6 4H18V20L12 14L6 20V4Z" stroke="#A0A0A0" stroke-width="2" fill="none"></path>
                            </svg>
                        </td>
                    </tr>`;
                }).join('')}
            </tbody>
        </table>
    `;
}

function initializeBookmarkPage() {
    // Reset any existing state
    const bookmarkTable = document.getElementById('transactionTable');
    if (!bookmarkTable) return;

    // Initialize filter functionality
    const filterButtons = document.querySelectorAll('.dropdown-content a');
    filterButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const filter = this.getAttribute('data-filter');
            window.location = `/bookmark?filter=${filter}`;
        });
    });
    
    // Add pagination link handling
    const paginationLinks = document.querySelectorAll('.pagination-wrapper a');
    console.log('Pagination links found:', paginationLinks.length);
    
    paginationLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const pageUrl = this.getAttribute('href');
            console.log('Pagination link clicked:', pageUrl);
            
            // Show loading indicator
            const tableContainer = document.getElementById('transactionTable');
            if (tableContainer) {
                tableContainer.style.opacity = '0.5';
            }
            
            // Make AJAX request to get the new page
            fetch(pageUrl, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                // Update the table content and pagination
                const table = document.querySelector('#transactionTable table');
                const paginationWrapper = document.querySelector('.pagination-wrapper');
                
                if (table && data.html) {
                    // Extract just the table rows from the returned HTML
                    const tempDiv = document.createElement('div');
                    tempDiv.innerHTML = data.html;
                    const newTableRows = tempDiv.querySelectorAll('tr');
                    
                    // Clear existing rows except the header
                    const headerRow = table.querySelector('.head-table');
                    table.innerHTML = '';
                    table.appendChild(headerRow);
                    
                    // Add the new rows
                    newTableRows.forEach(row => {
                        if (!row.classList.contains('head-table')) {
                            table.appendChild(row);
                        }
                    });
                }
                
                if (paginationWrapper && data.pagination) {
                    paginationWrapper.innerHTML = data.pagination;
                }
                
                // Restore table opacity
                if (tableContainer) {
                    tableContainer.style.opacity = '1';
                }
                
                // Re-initialize the page to attach new event listeners
                initializeBookmarkPage();
                attachBookmarkCheckboxListeners();
                makeRowsClickableForCheckboxes();
                updateBookmarkActionBar();
            })
            .catch(error => {
                console.error('Error loading pagination:', error);
                // Fallback to regular page navigation if AJAX fails
                window.location.href = pageUrl;
            });
        });
    });
}

// Preserve scroll position
Turbolinks.scroll = {};

document.addEventListener("turbolinks:before-visit", function() {
    Turbolinks.scroll['top'] = document.documentElement.scrollTop;
});

document.addEventListener("turbolinks:load", function() {
    if (Turbolinks.scroll['top']) {
        document.documentElement.scrollTop = Turbolinks.scroll['top'];
        Turbolinks.scroll = {};
    }
}); 