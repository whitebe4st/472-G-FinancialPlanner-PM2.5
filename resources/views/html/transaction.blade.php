@extends('layout/layout')

@section('title')
    Transaction
@endsection

@section('content')
<div class="content-wrapper" style="padding: 2rem;">
    <h1 style="margin-bottom: 1rem;">Transactions</h1>
    <p style="margin-bottom: 2rem;">Track your finances and achieve your financial goal.</p>

    <div class="table-container" style="width: 95%; margin: 0 auto;">
        <div class="table-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
            <h2>Transaction History</h2>
            <div class="filter-buttons" style="display: flex; gap: 1rem;">
                <!-- Type Filter Dropdown -->
                <div class="dropdown" style="position: relative; display: inline-block;">
                    <button class="filter-btn" onclick="toggleDropdown('typeFilter')" style="padding: 0.5rem 1rem; display: flex; align-items: center; gap: 0.5rem;">
                        <svg width="24" height="24" viewBox="0 0 24 24">
                            <path d="M4 4H20M8 12H16M10 20H14" stroke="#A0A0A0" stroke-width="2"/>
                        </svg>
                        <span id="selectedType">All Types</span>
                    </button>
                    <div id="typeFilter" class="dropdown-content" style="display: none; position: absolute; background: white; box-shadow: 0 2px 4px rgba(0,0,0,0.1); border-radius: 5px; min-width: 150px; z-index: 1;">
                        <a href="#" onclick="updateFilter('type', 'all', 'All Types')">All Types</a>
                        <a href="#" onclick="updateFilter('type', 'income', 'Income')">Income</a>
                        <a href="#" onclick="updateFilter('type', 'expense', 'Expense')">Expense</a>
                        <hr style="margin: 0.5rem 0;">
                        <a href="#" onclick="updateSort('amount', 'asc')">Amount (Low to High)</a>
                        <a href="#" onclick="updateSort('amount', 'desc')">Amount (High to Low)</a>
                        <a href="#" onclick="updateSort('date', 'asc')">Date (Oldest)</a>
                        <a href="#" onclick="updateSort('date', 'desc')">Date (Newest)</a>
                    </div>
                </div>

                <!-- Time Range Filter -->
                <div class="dropdown" style="position: relative; display: inline-block;">
                    <button class="filter-btn" onclick="toggleDropdown('timeFilter')" style="padding: 0.5rem 1rem; display: flex; align-items: center; gap: 0.5rem;">
                        <svg width="24" height="24" viewBox="0 0 24 24">
                            <path d="M8 2V5M16 2V5M3.5 9.09H20.5M21 8.5V17C21 20 19.5 22 16 22H8C4.5 22 3 20 3 17V8.5C3 5.5 4.5 3.5 8 3.5H16C19.5 3.5 21 5.5 21 8.5Z" stroke="#A0A0A0" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
                        </svg>
                        <span id="selectedTime">Today</span>
                    </button>
                    <div id="timeFilter" class="dropdown-content" style="display: none; position: absolute; background: white; box-shadow: 0 2px 4px rgba(0,0,0,0.1); border-radius: 5px; min-width: 150px; z-index: 1;">
                        <a href="#" onclick="updateFilter('time', 'today', 'Today')">Today</a>
                        <a href="#" onclick="updateFilter('time', 'weekly', 'This Week')">This Week</a>
                        <a href="#" onclick="updateFilter('time', 'monthly', 'This Month')">This Month</a>
                        <a href="#" onclick="updateFilter('time', 'yearly', 'This Year')">This Year</a>
                    </div>
                </div>
            </div>
        </div>

        <div id="loadingAnimation" style="display: none; text-align: center; padding: 2rem;">
            <div class="spinner" style="display: inline-block;">
                <svg width="40" height="40" viewBox="0 0 50 50">
                    <circle cx="25" cy="25" r="20" fill="none" stroke="#333" stroke-width="4" stroke-dasharray="60 20">
                        <animateTransform
                            attributeName="transform"
                            type="rotate"
                            from="0 25 25"
                            to="360 25 25"
                            dur="1s"
                            repeatCount="indefinite"/>
                    </circle>
                </svg>
            </div>
        </div>

        <div id="transactionTable">
            <!-- Table content will be loaded here -->
        </div>

        <!-- Pagination Container -->
        <div class="pagination-container" style="display: flex; justify-content: center; align-items: center; margin-top: 20px; gap: 10px;">
            <button id="prevPageBtn" class="pagination-btn" onclick="changePage(currentPage - 1)" style="width: 36px; height: 36px; background: #f7f7f7; border: 1px solid #e0e0e0; border-radius: 50%; color: #555; font-size: 18px; font-weight: bold; cursor: pointer; display: flex; justify-content: center; align-items: center; transition: all 0.2s ease; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">&lt;</button>
            <div class="addBar-btn">
                <button onclick="showAddTransactionPopup()">+</button>
            </div>
            <button id="nextPageBtn" class="pagination-btn" onclick="changePage(currentPage + 1)" style="width: 36px; height: 36px; background: #f7f7f7; border: 1px solid #e0e0e0; border-radius: 50%; color: #555; font-size: 18px; font-weight: bold; cursor: pointer; display: flex; justify-content: center; align-items: center; transition: all 0.2s ease; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">&gt;</button>
            <span id="pageIndicator" class="page-indicator" style="font-size: 14px; color: #666; margin-left: 10px; font-weight: 500;">Page <span id="currentPageNum">1</span></span>
        </div>

        <div id="action-bar" class="action-bar hidden">
            <span id="selected-count">0 Items</span>
            <div class="action-buttons">
                <button class="edit-btn" disabled>
                    <svg width="16" height="16" viewBox="0 0 24 24">
                        <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z" fill="currentColor"/>
                    </svg>
                    Edit
                </button>
                <button class="bookmark-btn">
                    <svg width="16" height="16" viewBox="0 0 24 24">
                        <path d="M6 4H18V20L12 14L6 20V4Z" stroke="currentColor" stroke-width="2" fill="none"/>
                    </svg>
                    Bookmark
                </button>
                <button class="remove-btn">
                    <svg width="16" height="16" viewBox="0 0 24 24">
                        <path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z" fill="currentColor"/>
                    </svg>
                    Remove
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
let currentPage = 1;
let currentFilters = {
    type: 'all',
    time: 'today',
    sort: 'date',
    order: 'desc'
};

function toggleDropdown(id) {
    const dropdown = document.getElementById(id);
    const allDropdowns = document.getElementsByClassName('dropdown-content');
    const button = dropdown.previousElementSibling;
    
    Array.from(allDropdowns).forEach(d => {
        if (d.id !== id) {
            d.classList.remove('show');
            d.style.display = 'none';
            d.previousElementSibling.classList.remove('active');
        }
    });
    
    if (dropdown.style.display === 'none') {
        dropdown.style.display = 'block';
        setTimeout(() => dropdown.classList.add('show'), 0);
        button.classList.add('active');
    } else {
        dropdown.classList.remove('show');
        setTimeout(() => dropdown.style.display = 'none', 200);
        button.classList.remove('active');
    }
    
    updateSelectedStates();
}

function updateSelectedStates() {
    // Update type filter selections
    const typeLinks = document.querySelectorAll('#typeFilter a');
    typeLinks.forEach(link => {
        link.classList.remove('selected');
        const filterValue = link.getAttribute('onclick').match(/'([^']+)'/g)[1].replace(/'/g, '');
        if (filterValue === currentFilters.type) {
            link.classList.add('selected');
        }
    });

    // Update time filter selections
    const timeLinks = document.querySelectorAll('#timeFilter a');
    timeLinks.forEach(link => {
        link.classList.remove('selected');
        const filterValue = link.getAttribute('onclick').match(/'([^']+)'/g)[1].replace(/'/g, '');
        if (filterValue === currentFilters.time) {
            link.classList.add('selected');
        }
    });

    // Update sort selections
    const sortLinks = document.querySelectorAll('#typeFilter a[onclick*="updateSort"]');
    sortLinks.forEach(link => {
        link.classList.remove('selected');
        const onclick = link.getAttribute('onclick');
        const [field, order] = onclick.match(/'([^']+)'/g).map(str => str.replace(/'/g, ''));
        if (field === currentFilters.sort && order === currentFilters.order) {
            link.classList.add('selected');
        }
    });
}

function updateFilter(filterType, value, label) {
    currentFilters[filterType] = value;
    document.getElementById(`selected${filterType.charAt(0).toUpperCase() + filterType.slice(1)}`).textContent = label;
    loadTransactions();
    updateSelectedStates();
    toggleDropdown(filterType + 'Filter');
}

function updateSort(field, order) {
    currentFilters.sort = field;
    currentFilters.order = order;
    loadTransactions();
    updateSelectedStates();
    toggleDropdown('typeFilter');
}

function loadTransactions() {
    const tableContainer = document.getElementById('transactionTable');
    const loadingAnimation = document.getElementById('loadingAnimation');
    
    // Show loading animation
    tableContainer.style.opacity = '0';
    loadingAnimation.style.display = 'block';
    
    // Create the base URL
    let urlParams = new URLSearchParams();
    urlParams.append('page', currentPage);
    
    // Add standard filters
    for (const [key, value] of Object.entries(currentFilters)) {
        urlParams.append(key, value);
    }
    
    // Add special debug parameters for pagination
    urlParams.append('per_page', 10); // Force 10 items per page
    urlParams.append('debug', 'true'); // Add debug flag
    urlParams.append('force_pagination', 'true'); // Try to force pagination
    
    const url = `/api/transactions?${urlParams.toString()}`;
    console.log(`📡 API URL: ${url}`);
    
    // Abort any ongoing fetch requests
    if (window.currentRequest) {
        console.log("⚠️ Aborting previous request");
        window.currentRequest.abort();
    }
    
    // Create a new AbortController
    window.currentRequest = new AbortController();
    const signal = window.currentRequest.signal;
    
    fetch(url, { signal })
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
            
            // Store the pagination data for future reference
            window.lastKnownPageData = {
                total: data.total || 0,
                current_page: data.current_page || currentPage,
                last_page: data.last_page || 1,
                per_page: data.per_page || 10
            };
            
            // Clear the current request reference
            window.currentRequest = null;
            
            // Hide loading animation and show table
            loadingAnimation.style.display = 'none';
            tableContainer.innerHTML = generateTableHTML(data);
            requestAnimationFrame(() => {
                tableContainer.style.opacity = '1';
            });
            attachCheckboxListeners();
            
            // Update pagination
            updatePaginationControls(data);
            
            // Log useful debugging info about the request and response
            console.log(`📊 Page ${currentPage} requested, got page ${data.current_page || 'undefined'}`);
            console.log(`📊 Total items: ${data.total || 0}, Items in response: ${data.data?.length || 0}`);
        })
        .catch(error => {
            // If this is an AbortError, we can ignore it
            if (error.name === 'AbortError') {
                console.log('🛑 Request was aborted - likely replaced by a newer request');
                return;
            }
            
            // Clear the current request reference
            window.currentRequest = null;
            
            console.error('❌ Error loading transactions:', error);
            loadingAnimation.style.display = 'none';
            tableContainer.innerHTML = `
                <div style="text-align: center; padding: 2rem; color: #dc3545;">
                    <p>Error loading transactions</p>
                    <p style="font-size: 0.9rem; margin-top: 0.5rem;">${error.message}</p>
                    <details style="margin-top: 1rem; text-align: left; max-width: 600px; margin: 1rem auto; padding: 1rem; background: #f8f8f8; border-radius: 4px;">
                        <summary style="cursor: pointer; font-weight: bold;">Debug Info</summary>
                        <p><strong>Error Type:</strong> ${error.name}</p>
                        <p><strong>API URL:</strong> ${url}</p>
                        <p><strong>Applied Filters:</strong></p>
                        <pre style="overflow-x: auto; padding: 0.5rem; background: #f0f0f0; font-size: 12px;">${JSON.stringify(currentFilters, null, 2)}</pre>
                        <p><strong>Browser Info:</strong> ${navigator.userAgent}</p>
                    </details>
                    <div style="margin-top: 1rem;">
                        <button onclick="loadTransactions()" class="retry-btn" style="padding: 8px 16px; background: #f0f0f0; border: 1px solid #ddd; border-radius: 4px; cursor: pointer;">
                            Try Again
                        </button>
                    </div>
                </div>
            `;
            tableContainer.style.opacity = '1';
        });
}

function generateTableHTML(data) {
    console.log("🔍 Checking transaction data before rendering:", data); // Debug Full Data
    
    // Safety check for data structure
    if (!data || !data.data || !Array.isArray(data.data)) {
        console.error("❌ Invalid data structure received:", data);
        return `
            <div style="text-align: center; padding: 2rem; color: #dc3545;">
                <p>Error: Invalid data format received from server.</p>
                <details style="margin-top: 1rem; text-align: left; max-width: 600px; margin: 1rem auto; padding: 1rem; background: #f8f8f8; border-radius: 4px;">
                    <summary style="cursor: pointer; font-weight: bold;">Debug Info</summary>
                    <pre style="overflow-x: auto; padding: 1rem; background: #f0f0f0; font-size: 12px;">Data Type: ${typeof data}\nStructure: ${JSON.stringify(data, null, 2)}\nFilters: ${JSON.stringify(currentFilters, null, 2)}</pre>
                </details>
                <div style="margin-top: 1rem;">
                    <button onclick="loadTransactions()" class="retry-btn" style="padding: 8px 16px; background: #f0f0f0; border: 1px solid #ddd; border-radius: 4px; cursor: pointer;">
                        Try Again
                    </button>
                </div>
            </div>
        `;
    }
    
    // Check if the data is empty
    if (data.data.length === 0) {
        return `
            <div style="text-align: center; padding: 2rem; color: #666;">
                <p>No transactions found for the selected filters.</p>
                <div style="margin-top: 1rem; display: flex; gap: 10px; justify-content: center;">
                    <button onclick="loadTransactions()" class="retry-btn" style="padding: 8px 16px; background: #f0f0f0; border: 1px solid #ddd; border-radius: 4px; cursor: pointer;">
                        Refresh
                    </button>
                    <button onclick="showAddTransactionPopup()" class="add-btn" style="padding: 8px 16px; background: #4CAF50; color: white; border: none; border-radius: 4px; cursor: pointer;">
                        Add Transaction
                    </button>
                </div>
                <div style="margin-top: 20px; padding: 15px; background: #f8f8f8; border-radius: 4px; display: inline-block; text-align: left;">
                    <p><strong>Current Filters:</strong></p>
                    <ul style="margin: 5px 0; padding-left: 20px;">
                        <li>Type: ${currentFilters.type === 'all' ? 'All Types' : currentFilters.type.charAt(0).toUpperCase() + currentFilters.type.slice(1)}</li>
                        <li>Time Period: ${currentFilters.time.charAt(0).toUpperCase() + currentFilters.time.slice(1)}</li>
                        <li>Sort: ${currentFilters.sort === 'date' ? 'Date' : 'Amount'} (${currentFilters.order === 'asc' ? 'Ascending' : 'Descending'})</li>
                    </ul>
                    <p style="margin-top: 10px; font-style: italic;">Try changing your filters or adding new transactions.</p>
                </div>
            </div>
        `;
    }

    // If we have valid data, generate the table
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
                    try {
                        console.log(`🆔 Row ${index + 1} - transaction:`, transaction); // Debug transaction data
                        
                        // Safely get the transaction ID with fallback
                        let transactionId = transaction?.transaction_id || `row-${index}`;
                        console.log(`✅ Set data-id for row ${index + 1}:`, transactionId);
                        
                        // Safely handle transaction properties with fallbacks
                        const description = transaction?.description || 'Unknown';
                        const category = transaction?.category || 'Uncategorized';
                        let dateDisplay = 'N/A';
                        
                        try {
                            if (transaction?.transaction_date) {
                                dateDisplay = new Date(transaction.transaction_date).toLocaleDateString();
                            }
                        } catch (dateError) {
                            console.error("Error formatting date:", dateError);
                        }
                        
                        let amountDisplay = '0.00';
                        try {
                            if (transaction?.amount) {
                                amountDisplay = parseFloat(transaction.amount).toFixed(2);
                            }
                        } catch (amountError) {
                            console.error("Error formatting amount:", amountError);
                        }
                        
                        const type = transaction?.type || 'unknown';
                        const typeDisplay = type === 'expense' ? 'Exp.' : (type === 'income' ? 'Inc.' : type);
                        
                        return `
                        <tr style="animation-delay: ${index * 0.05}s">
                            <td><input type="checkbox" class="row-checkbox" data-id="${transactionId}"></td>
                            <td data-category="${category}">${description}</td>
                            <td>${dateDisplay}</td>
                            <td>$${amountDisplay}</td>
                            <td data-type="${type}">${typeDisplay}</td>
                            <td>${category}</td>
                            <td>
                                <svg width="24" height="24" viewBox="0 0 24 24">
                                    <path d="M6 4H18V20L12 14L6 20V4Z" stroke="#A0A0A0" stroke-width="2" fill="none"></path>
                                </svg>
                            </td>
                        </tr>`;
                    } catch (rowError) {
                        console.error(`Error generating row ${index}:`, rowError);
                        return `<tr><td colspan="7" style="color: #dc3545;">Error generating row data</td></tr>`;
                    }
                }).join('')}
            </tbody>
        </table>
    `;
}

function generatePaginationHTML(data) {
    let html = `
        <button class="page-nav" onclick="changePage(${data.current_page - 1})" ${data.current_page === 1 ? 'disabled' : ''}>&lt;</button>
    `;

    for (let i = 1; i <= data.last_page; i++) {
        html += `
            <button class="page-btn ${data.current_page === i ? 'active' : ''}" 
                    onclick="changePage(${i})">${i}</button>
        `;
    }

    html += `
        <button class="page-nav" onclick="changePage(${data.current_page + 1})" 
                ${data.current_page === data.last_page ? 'disabled' : ''}>&gt;</button>
    `;

    return html;
}

function changePage(page) {
    if (page < 1) return;
    
    // Get pagination buttons for updating UI
    const prevPageBtn = document.getElementById('prevPageBtn');
    const nextPageBtn = document.getElementById('nextPageBtn');
    
    // Temporarily disable buttons during page transition
    if (prevPageBtn) prevPageBtn.disabled = true;
    if (nextPageBtn) nextPageBtn.disabled = true;
    
    // Add visual feedback for the buttons
    if (page < currentPage) {
        if (prevPageBtn) prevPageBtn.style.opacity = '0.7'; // Visual feedback for clicked button
    } else if (page > currentPage) {
        if (nextPageBtn) nextPageBtn.style.opacity = '0.7'; // Visual feedback for clicked button
    }
    
    currentPage = page;
    
    // Update current page indicator
    const currentPageNum = document.getElementById('currentPageNum');
    if (currentPageNum) currentPageNum.textContent = page;
    
    loadTransactions();
    
    // Reset button opacity after a short delay
    setTimeout(() => {
        if (prevPageBtn) prevPageBtn.style.opacity = '';
        if (nextPageBtn) nextPageBtn.style.opacity = '';
    }, 300);
}

function attachCheckboxListeners() {
    console.log("🔄 Attaching event listeners to checkboxes...");

    document.querySelectorAll(".row-checkbox").forEach(checkbox => {
        console.log("✅ Found checkbox - data-id:", checkbox.getAttribute("data-id")); // Debug

        checkbox.addEventListener("change", function () {
            updateActionBar();
        });
    });
}

// Close dropdowns when clicking outside
document.addEventListener('click', function(event) {
    if (!event.target.closest('.dropdown')) {
        const dropdowns = document.getElementsByClassName('dropdown-content');
        const buttons = document.getElementsByClassName('filter-btn');
        Array.from(dropdowns).forEach(d => d.style.display = 'none');
        Array.from(buttons).forEach(b => b.classList.remove('active'));
    }
});

// Initial states
document.addEventListener('DOMContentLoaded', function() {
    loadTransactions();
    updateSelectedStates();
    
    // Safely call initializeEventListeners if it exists
    if (typeof initializeEventListeners === 'function') {
        initializeEventListeners();
    } else {
        console.warn('⚠️ initializeEventListeners is not defined. Make sure script.js is loaded properly.');
        // Fallback for basic functionality
        document.querySelectorAll('.action-buttons button').forEach(button => {
            button.addEventListener('click', function() {
                if (this.classList.contains('edit-btn')) {
                    editSelected();
                } else if (this.classList.contains('bookmark-btn')) {
                    bookmarkSelected();
                } else if (this.classList.contains('remove-btn')) {
                    removeSelected();
                }
            });
        });
    }
});

// Function to update pagination controls based on response data
function updatePaginationControls(data) {
    console.log("📄 Updating pagination controls:", {
        currentPage: data.current_page,
        lastPage: data.last_page,
        total: data.total,
        perPage: data.per_page
    });

    const prevPageBtn = document.getElementById('prevPageBtn');
    const nextPageBtn = document.getElementById('nextPageBtn');
    const pageIndicator = document.getElementById('pageIndicator');
    const currentPageNum = document.getElementById('currentPageNum');
    
    if (!prevPageBtn || !nextPageBtn || !pageIndicator || !currentPageNum) {
        console.error("❌ Pagination controls not found in DOM");
        return;
    }
    
    // Update the current page indicator
    currentPageNum.textContent = data.current_page || 1;
    
    // Always show page indicator with forced "more pages" messaging
    // This is a temporary override to test if there are indeed more pages
    pageIndicator.style.display = 'inline';
    
    // Override the pagination text to indicate we're forcing pagination
    if (data.total == 0) {
        pageIndicator.innerHTML = `Page <span id="currentPageNum">${data.current_page}</span> (Testing pagination)`;
    } else {
        pageIndicator.innerHTML = `Page <span id="currentPageNum">${data.current_page}</span> of ${Math.max(data.last_page, 2)} (${data.total} total)`;
    }
    
    // Add special styling for buttons when at limits
    if (data.current_page <= 1) {
        prevPageBtn.disabled = true;
        prevPageBtn.classList.add('disabled');
    } else {
        prevPageBtn.disabled = false;
        prevPageBtn.classList.remove('disabled');
    }
    
    // ALWAYS enable the next button regardless of data.last_page
    // This is a temporary override to test if there are more pages
    nextPageBtn.disabled = false;
    nextPageBtn.classList.remove('disabled');
    
    console.log(`✅ Pagination updated - Prev: ${!prevPageBtn.disabled}, Next: ${!nextPageBtn.disabled}`);
}

// Function to toggle all checkboxes in the transaction table
function toggleAllCheckboxes() {
    const selectAllCheckbox = document.getElementById('selectAll');
    const rowCheckboxes = document.querySelectorAll('.row-checkbox');
    
    if (!selectAllCheckbox) {
        console.error("❌ Select all checkbox not found");
        return;
    }
    
    console.log(`🔄 Toggling all checkboxes to: ${selectAllCheckbox.checked}`);
    
    rowCheckboxes.forEach(checkbox => {
        checkbox.checked = selectAllCheckbox.checked;
    });
    
    updateActionBar();
}

// Function to update the action bar based on selected items
function updateActionBar() {
    const selectedCheckboxes = document.querySelectorAll('.row-checkbox:checked');
    const actionBar = document.getElementById('action-bar');
    const selectedCount = document.getElementById('selected-count');
    const editBtn = document.querySelector('.action-buttons .edit-btn');
    
    if (!actionBar || !selectedCount) {
        console.warn("⚠️ Action bar elements not found");
        return;
    }
    
    const count = selectedCheckboxes.length;
    console.log(`✅ Selected count: ${count}`);
    
    if (count > 0) {
        actionBar.classList.remove('hidden');
        selectedCount.textContent = `${count} Item${count > 1 ? 's' : ''}`;
        
        // Enable/disable edit button based on selection count
        if (editBtn) {
            editBtn.disabled = count !== 1;
        }
    } else {
        actionBar.classList.add('hidden');
    }
}

// Function to edit selected transaction
function editSelected() {
    const selectedCheckboxes = document.querySelectorAll('.row-checkbox:checked');
    
    if (selectedCheckboxes.length !== 1) {
        console.warn("⚠️ Edit requires exactly one selected transaction");
        return;
    }
    
    const transactionId = selectedCheckboxes[0].getAttribute('data-id');
    console.log(`🔄 Editing transaction: ${transactionId}`);
    
    window.location.href = "{{ url('/transaction') }}/" + transactionId + "/edit";
}

// Function to bookmark selected transactions
function bookmarkSelected() {
    const selectedCheckboxes = document.querySelectorAll('.row-checkbox:checked');
    
    if (selectedCheckboxes.length === 0) {
        console.warn("⚠️ No transactions selected for bookmarking");
        return;
    }
    
    const ids = Array.from(selectedCheckboxes).map(checkbox => 
        checkbox.getAttribute('data-id')
    );
    
    console.log(`🔖 Bookmarking ${ids.length} transactions: `, ids);
    
    // Call the bookmark API endpoint
    fetch('/api/transactions/bookmark-multiple', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ ids: ids })
    })
    .then(response => response.json())
    .then(data => {
        console.log('Bookmark response:', data);
        if (data.success) {
            alert(data.message);
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error bookmarking transactions:', error);
        alert('Failed to bookmark transactions');
    });
}

// Function to remove selected transactions
function removeSelected() {
    const selectedCheckboxes = document.querySelectorAll('.row-checkbox:checked');
    
    if (selectedCheckboxes.length === 0) {
        console.warn("⚠️ No transactions selected for removal");
        return;
    }
    
    if (!confirm(`Are you sure you want to delete ${selectedCheckboxes.length} transaction(s)?`)) {
        return;
    }
    
    const ids = Array.from(selectedCheckboxes).map(checkbox => 
        checkbox.getAttribute('data-id')
    );
    
    console.log(`🗑️ Removing ${ids.length} transactions: `, ids);
    
    // Call the delete API endpoint
    fetch('/api/transactions/delete-multiple', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ ids: ids })
    })
    .then(response => response.json())
    .then(data => {
        console.log('Delete response:', data);
        if (data.success) {
            alert(data.message);
            // Reload transactions to update the table
            loadTransactions();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error deleting transactions:', error);
        alert('Failed to delete transactions');
    });
}
</script>
@endpush

@endsection

@section('addTransactionPopup')
<div class="popup-content">
    <div class="popup-header">
        <h2>Add Transaction</h2>
        <button onclick="hideAddTransactionPopup()">&times;</button>
    </div>
    
    <div class="form-actions">
        <button type="button" class="btn-bookmark" onclick="showBookmarkSelectionModal()">
            <svg width="20" height="20" viewBox="0 0 24 24">
                <path d="M6 4H18V20L12 14L6 20V4Z" stroke="currentColor" stroke-width="2" fill="none"/>
            </svg>
            Add from Bookmark
        </button>
    </div>

    <form id="transactionForm">
        @csrf
        <div class="form-group">
            <label for="description">Description</label>
            <input type="text" id="description" name="description" required>
        </div>
        <div class="form-group">
            <label for="amount">Amount</label>
            <input type="number" id="amount" name="amount" step="0.01" required>
        </div>
        <div class="form-group">
            <label for="type">Type</label>
            <select id="type" name="type" required>
                <option value="expense">Expense</option>
                <option value="income">Income</option>
            </select>
        </div>
        <div class="form-group">
            <label for="category">Category</label>
            <div class="category-input-container">
                <input 
                    type="text" 
                    id="category" 
                    name="category" 
                    list="categories" 
                    autocomplete="off"
                    required
                    oninput="filterCategories(this.value)"
                >
                <div id="categoryDropdown" class="category-dropdown">
                    <!-- Categories will be populated here -->
                </div>
            </div>
        </div>
        <div class="form-group">
            <label for="transaction_date">Date</label>
            <input type="date" id="transaction_date" name="transaction_date" required>
        </div>
        <div class="button-group">
            <button type="button" onclick="hideAddTransactionPopup()">Cancel</button>
            <button type="submit">Add</button>
        </div>
    </form>
</div>
@endsection

@section('editTransactionPopup')
<div class="popup-content">
    <div class="popup-header">
        <h2>Edit Transaction</h2>
        <button onclick="hideEditTransactionPopup()">&times;</button>
    </div>

    <form id="editTransactionForm">
        @csrf
        <input type="hidden" id="edit_id" name="id">
        
        <div class="form-group">
            <label for="edit_description">Description</label>
            <input type="text" id="edit_description" name="description" required>
        </div>

        <div class="form-group">
            <label for="edit_amount">Amount</label>
            <input type="number" id="edit_amount" name="amount" step="0.01" required>
        </div>

        <div class="form-group">
            <label for="edit_type">Type</label>
            <select id="edit_type" name="type" required>
                <option value="expense">Expense</option>
                <option value="income">Income</option>
            </select>
        </div>

        <div class="form-group">
            <label for="edit_category">Category</label>
            <div class="category-input-container">
                <input 
                    type="text" 
                    id="edit_category" 
                    name="category" 
                    list="categories" 
                    autocomplete="off"
                    required
                    oninput="filterCategories(this.value)"
                >
                <div id="editCategoryDropdown" class="category-dropdown">
                    <!-- Categories will be populated here -->
                </div>
            </div>
        </div>

        <div class="form-group">
            <label for="edit_transaction_date">Date</label>
            <input type="date" id="edit_transaction_date" name="transaction_date" required>
        </div>

        <div class="button-group">
            <button type="button" onclick="hideEditTransactionPopup()">Cancel</button>
            <button type="submit">Save</button>
        </div>
    </form>
</div>
@endsection