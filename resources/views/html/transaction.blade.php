@extends('layout/layout')

@section('title')
    Transaction
@endsection

@section('content')
<div class="content-wrapper" style="padding: 2rem;">
    <h1 style="margin-bottom: 1rem;">Transactions</h1>
    <p style="margin-bottom: 2rem;">Track your finances and achieve your financial goal.</p>
    
    <!-- Debug Button -->
    <button id="debugActionBarBtn" style="background: #ff5722; color: white; padding: 8px 15px; border: none; border-radius: 4px; margin-bottom: 20px; cursor: pointer;">Debug: Toggle Action Bar</button>

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
        <div class="pagination-container" style="display: flex; flex-direction: column; justify-content: center; align-items: center; margin-top: 20px; gap: 10px;">
            <div style="display: flex; gap: 10px; align-items: center;">
                <button id="prevPageBtn" class="pagination-btn" style="width: 36px; height: 36px; background: #f7f7f7; border: 1px solid #e0e0e0; border-radius: 50%; color: #555; font-size: 18px; font-weight: bold; cursor: pointer; display: flex; justify-content: center; align-items: center; transition: all 0.2s ease; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">&lt;</button>
                <div class="addBar-btn">
                    <button onclick="showAddTransactionPopup()">+</button>
                </div>
                <button id="nextPageBtn" class="pagination-btn" style="width: 36px; height: 36px; background: #f7f7f7; border: 1px solid #e0e0e0; border-radius: 50%; color: #555; font-size: 18px; font-weight: bold; cursor: pointer; display: flex; justify-content: center; align-items: center; transition: all 0.2s ease; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">&gt;</button>
            </div>
            <span id="pageIndicator" class="page-indicator" style="font-size: 14px; color: #666; margin-top: 5px;">Page <span id="currentPageNum">1</span></span>
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

<style>
    /* Fix for action bar visibility */
    .action-bar {
        visibility: visible;
        transition: opacity 0.3s ease, transform 0.3s ease, visibility 0s;
    }
    
    .action-bar.hidden {
        visibility: hidden;
        transition: opacity 0.3s ease, transform 0.3s ease, visibility 0s 0.3s;
    }
    
    /* Loading spinner for buttons */
    .spinner-mini {
        display: inline-block;
        width: 12px;
        height: 12px;
        border: 2px solid rgba(255, 255, 255, 0.3);
        border-radius: 50%;
        border-top-color: #fff;
        animation: spin 1s ease-in-out infinite;
        margin-right: 5px;
        vertical-align: middle;
    }
    
    @keyframes spin {
        to { transform: rotate(360deg); }
    }
</style>

@push('scripts')
<script>
let currentPage = 1;
let currentFilters = {
    type: 'all',
    time: 'today',
    sort: 'date',
    order: 'desc'
};

// Make the filters globally accessible
window.currentFilters = currentFilters;

// Ensure updateActionBar is global
window.updateActionBar = updateActionBar;

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
            makeRowsClickableForCheckboxes();
            
            // Update pagination
            updatePaginationControls(data);
            
            // Force check for action bar update
            setTimeout(updateActionBar, 100);
            
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
    // Check if the requested page exists based on last known pagination data
    const lastKnownPageData = window.lastKnownPageData || { last_page: 1 };
    
    // Don't allow navigating to pages that don't exist
    if (page < 1 || (lastKnownPageData.last_page && page > lastKnownPageData.last_page)) {
        console.log(`⛔ Attempted to navigate to invalid page ${page}. Last page is ${lastKnownPageData.last_page}`);
        return;
    }
    
    // Get pagination buttons for updating UI
    const prevPageBtn = document.getElementById('prevPageBtn');
    const nextPageBtn = document.getElementById('nextPageBtn');
    
    // Temporarily disable buttons during page transition
    if (prevPageBtn) prevPageBtn.disabled = true;
    if (nextPageBtn) nextPageBtn.disabled = true;
    
    // Add visual feedback for the buttons
    if (page < window.currentPage) {
        if (prevPageBtn) prevPageBtn.style.opacity = '0.7'; // Visual feedback for clicked button
    } else if (page > window.currentPage) {
        if (nextPageBtn) nextPageBtn.style.opacity = '0.7'; // Visual feedback for clicked button
    }
    
    // Update the global currentPage
    window.currentPage = page;
    
    // Update current page indicator
    const currentPageNum = document.getElementById('currentPageNum');
    if (currentPageNum) currentPageNum.textContent = page;
    
    // Load transactions for the new page
    loadTransactions({
        type: currentFilters.type || 'all',
        time: currentFilters.time || 'today',
        sort: currentFilters.sort || 'date',
        order: currentFilters.order || 'desc',
        page: page
    });
    
    // Reset button opacity after a short delay
    setTimeout(() => {
        if (prevPageBtn) prevPageBtn.style.opacity = '';
        if (nextPageBtn) nextPageBtn.style.opacity = '';
    }, 300);
}

function attachCheckboxListeners() {
    console.log("🔄 Attaching event listeners to checkboxes...");

    // Find all row checkboxes without listeners
    const checkboxes = document.querySelectorAll(".row-checkbox:not([data-listener-initialized='true'])");
    console.log(`📋 Found ${checkboxes.length} checkboxes that need listeners`);
    
    if (checkboxes.length === 0) {
        console.log("ℹ️ No new checkboxes found to attach listeners to");
        return;
    }
    
    // Process each checkbox - use direct event attachment
    checkboxes.forEach(checkbox => {
        console.log("Adding listener to checkbox with ID:", checkbox.getAttribute("data-id"));
        
        // Mark as initialized to prevent duplicate listeners
        checkbox.setAttribute('data-listener-initialized', 'true');
        
        // REMOVE any existing click handlers to prevent duplicates
        const newCheckbox = checkbox.cloneNode(true);
        checkbox.parentNode.replaceChild(newCheckbox, checkbox);
        
        // Add the click handler directly 
        newCheckbox.setAttribute('data-listener-initialized', 'true');
        newCheckbox.addEventListener('click', function() {
            console.log("Checkbox clicked:", this.getAttribute("data-id"));
            // Force an update of the action bar
            setTimeout(updateActionBar, 0);
        });
    });
    
    // Also handle the "select all" checkbox with direct DOM handling
    const selectAllCheckbox = document.getElementById("selectAll");
    if (selectAllCheckbox) {
        console.log("Setting up select all checkbox");
        
        // Replace the checkbox to remove all existing handlers
        const newSelectAll = selectAllCheckbox.cloneNode(false);
        selectAllCheckbox.parentNode.replaceChild(newSelectAll, selectAllCheckbox);
        
        // Add direct click handler
        newSelectAll.addEventListener('click', function() {
            console.log("Select all toggled:", this.checked);
            const checkboxes = document.querySelectorAll(".row-checkbox");
            
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            
            // Force action bar update
            setTimeout(updateActionBar, 0);
        });
    }
    
    // Force an initial action bar update
    setTimeout(updateActionBar, 0);
    // Immediately update action bar in case there are already checked checkboxes
    updateActionBar();
}

// Named function to use as event listener
function updateActionBarOnCheckboxChange() {
    console.log("🔄 Checkbox changed - updating action bar");
    updateActionBar();
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
    console.log("🚀 DOM Content Loaded - Initializing Transaction Page");
    
    // Register our functions globally
    window.editTransaction = editTransaction;
    window.editSelected = editSelected;
    window.showEditTransactionPopup = showEditTransactionPopup;
    window.hideEditTransactionPopup = hideEditTransactionPopup;
    window.initializeEditFormListener = initializeEditFormListener;
    
    console.log("Action Bar Element: ", document.getElementById('action-bar'));
    
    loadTransactions();
    updateSelectedStates();
    makeRowsClickableForCheckboxes();
    
    // Debug button for action bar
    const debugBtn = document.getElementById('debugActionBarBtn');
    if (debugBtn) {
        debugBtn.addEventListener('click', function() {
            console.log("🔧 Debug button clicked - toggling action bar visibility");
            const actionBar = document.getElementById('action-bar');
            
            if (!actionBar) {
                console.error("❌ Action bar element not found");
                return;
            }
            
            // Toggle the action bar class
            if (actionBar.classList.contains('hidden')) {
                // Show action bar
                actionBar.classList.remove('hidden');
                actionBar.style.display = "flex";
                actionBar.style.opacity = "1";
                actionBar.style.transform = "translateX(-50%)";
                actionBar.style.pointerEvents = "auto";
            } else {
                // Hide action bar
                actionBar.classList.add('hidden');
                actionBar.style.opacity = "0";
                actionBar.style.transform = "translate(-50%, 100px)";
                actionBar.style.pointerEvents = "none";
            }
            
            // Update the text
            document.getElementById('selected-count').textContent = "Debug Mode";
        });
    }
    
    // Add event listeners for action buttons
    const actionButtons = document.querySelector('.action-buttons');
    if (actionButtons) {
        actionButtons.addEventListener('click', function(e) {
            // Event delegation for the action buttons
            if (e.target.classList.contains('edit-btn') || e.target.closest('.edit-btn')) {
                e.preventDefault();
                console.log("Edit button clicked via delegation");
                editSelected();
            } else if (e.target.classList.contains('bookmark-btn') || e.target.closest('.bookmark-btn')) {
                e.preventDefault();
                console.log("Bookmark button clicked via delegation");
                bookmarkSelected();
            } else if (e.target.classList.contains('remove-btn') || e.target.closest('.remove-btn')) {
                e.preventDefault();
                console.log("Remove button clicked via delegation");
                removeSelected();
            }
        });
    }
    
    // Add event delegation for checkboxes
    document.addEventListener('change', function(event) {
        // Check if the changed element is a row checkbox
        if (event.target.classList.contains('row-checkbox')) {
            console.log("🔄 Checkbox changed via event delegation");
            updateActionBar();
        }
    });
});

// Function to update pagination controls based on response data
function updatePaginationControls(data) {
    console.log("📄 Updating pagination controls:", {
        currentPage: data.current_page,
        lastPage: data.last_page,
        total: data.total,
        perPage: data.per_page,
        dataCount: data.data?.length || 0
    });

    const prevPageBtn = document.getElementById('prevPageBtn');
    const nextPageBtn = document.getElementById('nextPageBtn');
    const pageIndicator = document.getElementById('pageIndicator');
    const currentPageNum = document.getElementById('currentPageNum');
    
    if (!prevPageBtn || !nextPageBtn || !pageIndicator || !currentPageNum) {
        console.error("❌ Pagination controls not found in DOM");
        return;
    }
    
    // Get the actual values from the response
    const currentPage = data.current_page || 1;
    const lastPage = Math.max(data.last_page || 1, 1); // Ensure lastPage is at least 1
    const total = data.total || 0;
    const hasNextPage = currentPage < lastPage;
    
    // Save the pagination data for future reference
    window.lastKnownPageData = {
        total: total,
        current_page: currentPage,
        last_page: lastPage,
        per_page: data.per_page || 10
    };
    
    // Update the current page indicator
    currentPageNum.textContent = currentPage;
    
    // Show the page indicator with accurate information
    pageIndicator.style.display = 'inline';
    pageIndicator.style.textAlign = 'center';
    pageIndicator.style.marginTop = '5px';
    pageIndicator.style.fontWeight = '500';
    pageIndicator.innerHTML = `Page <span id="currentPageNum">${currentPage}</span> of ${lastPage} (${total} total)`;
    
    // Set button state based on current page position
    if (currentPage <= 1) {
        prevPageBtn.disabled = true;
        prevPageBtn.classList.add('disabled');
    } else {
        prevPageBtn.disabled = false;
        prevPageBtn.classList.remove('disabled');
    }
    
    // Only enable next button if there is a next page
    if (hasNextPage) {
        nextPageBtn.disabled = false;
        nextPageBtn.classList.remove('disabled');
    } else {
        nextPageBtn.disabled = true;
        nextPageBtn.classList.add('disabled');
    }
    
    console.log(`✅ Pagination updated - Current page: ${currentPage}, Last page: ${lastPage}, Has next page: ${hasNextPage}`);
    
    // Fix the direct onclick handlers on the pagination buttons
    prevPageBtn.setAttribute('onclick', `changePage(${Math.max(1, currentPage - 1)})`);
    nextPageBtn.setAttribute('onclick', `changePage(${hasNextPage ? currentPage + 1 : currentPage})`);
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
    console.log("🔄 Running updateActionBar function");
    
    const actionBar = document.getElementById("action-bar");
    const selectedCount = document.getElementById("selected-count");
    const selectedItems = document.querySelectorAll('.row-checkbox:checked');
    
    if (!actionBar || !selectedCount) {
        console.error("❌ Action bar elements not found");
        return;
    }
    
    const count = selectedItems.length;
    console.log(`✅ Selected count: ${count}`);
    
    // Force style update to remove any stale display settings
    actionBar.setAttribute('style', '');
    
    const selectedIds = Array.from(selectedItems).map(checkbox => checkbox.getAttribute('data-id'));
    console.log(`📌 Selected Transaction IDs:`, selectedIds);
    
    if (count > 0) {
        console.log("🟢 Showing action bar");
        
        // Show action bar
        actionBar.classList.remove("hidden");
        
        // Ensure it's displayed through both CSS and the style attribute
        actionBar.style.display = "flex";
        actionBar.style.opacity = "1";
        actionBar.style.transform = "translateX(-50%)";
        actionBar.style.pointerEvents = "auto";
        
        // Update the item count text
        selectedCount.textContent = `${count} Item${count > 1 ? 's' : ''}`;
        
        // Create fresh buttons to avoid event listener buildup
        const actionButtons = document.querySelector(".action-buttons");
        if (!actionButtons) {
            console.error("❌ Action buttons container not found!");
            return;
        }
        
        // Clear existing buttons
        actionButtons.innerHTML = '';
        
        // Edit button - only enabled for single selection
        const newEditButton = document.createElement("button");
        newEditButton.className = "edit-btn";
        newEditButton.innerHTML = `
            <svg width="16" height="16" viewBox="0 0 24 24">
                <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z" fill="currentColor"/>
            </svg>
            Edit
        `;
        
        // Enable or disable based on selection count
        newEditButton.disabled = count !== 1;
        
        // Add click event for editing
        newEditButton.onclick = function(e) {
            e.preventDefault();
            e.stopPropagation();
            editSelected();
            return false;
        };
        
        actionButtons.appendChild(newEditButton);
        
        // Bookmark button
        const newBookmarkButton = document.createElement("button");
        newBookmarkButton.className = "bookmark-btn";
        newBookmarkButton.innerHTML = `
            <svg width="16" height="16" viewBox="0 0 24 24">
                <path d="M6 4H18V20L12 14L6 20V4Z" stroke="currentColor" stroke-width="2" fill="none"/>
            </svg>
            Bookmark
        `;
        
        newBookmarkButton.onclick = function(e) {
            e.preventDefault();
            e.stopPropagation();
            bookmarkSelected();
            return false;
        };
        
        actionButtons.appendChild(newBookmarkButton);
        
        // Remove button
        const newRemoveButton = document.createElement("button");
        newRemoveButton.className = "remove-btn";
        newRemoveButton.innerHTML = `
            <svg width="16" height="16" viewBox="0 0 24 24">
                <path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z" fill="currentColor"/>
            </svg>
            Remove
        `;
        
        newRemoveButton.onclick = function(e) {
            e.preventDefault();
            e.stopPropagation();
            removeSelected();
            return false;
        };
        
        actionButtons.appendChild(newRemoveButton);
        
        console.log("✅ Action bar should now be visible");
    } else {
        console.log("🔴 Hiding action bar");
        
        // Hide action bar when no items selected
        actionBar.classList.add("hidden");
        
        // Set both CSS and style properties
        actionBar.style.opacity = "0";
        actionBar.style.transform = "translate(-50%, 100px)";
        actionBar.style.pointerEvents = "none";
        
        setTimeout(() => {
            if (!document.querySelectorAll('.row-checkbox:checked').length) {
                actionBar.style.display = "none";
            }
        }, 300);
        
        console.log("✅ Action bar hidden completely");
    }
}

// Function to edit selected transaction
function editSelected() {
    const selectedCheckboxes = document.querySelectorAll('.row-checkbox:checked');
    
    if (selectedCheckboxes.length !== 1) {
        console.warn("⚠️ Edit requires exactly one selected transaction");
        alert("Please select exactly one transaction to edit.");
        return;
    }
    
    const transactionId = selectedCheckboxes[0].getAttribute('data-id');
    if (!transactionId) {
        console.error("❌ Selected transaction has no ID");
        alert("Error: Cannot edit transaction with missing ID.");
        return;
    }
    
    console.log(`🔄 Editing transaction: ${transactionId}`);
    
    // Call the editTransaction function from script.js
    if (typeof editTransaction === 'function') {
        editTransaction(transactionId);
    } else {
        // As a fallback, try the global window.editTransaction
        if (typeof window.editTransaction === 'function') {
            window.editTransaction(transactionId);
        } else {
            // If all else fails, redirect to the edit page as a last resort
            console.error("❌ editTransaction function not found - falling back to redirect");
            alert("Edit functionality is not fully available. Redirecting to edit page.");
            window.location.href = `/transactions/${transactionId}/edit`;
        }
    }
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
    fetch('/transactions/bookmark-multiple', {
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
    fetch('/transactions', {
        method: 'DELETE',
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

// Make the entire row clickable to toggle the checkbox
function makeRowsClickableForCheckboxes() {
    console.log("🔄 Making rows clickable for checkboxes...");
    
    // Find all rows without click listeners
    const rows = document.querySelectorAll('#transactionTable tr:not(.head-table):not([data-row-click-listener="true"])');
    console.log(`📋 Found ${rows.length} rows that need click listeners`);
    
    if (rows.length === 0) {
        console.log("ℹ️ No new rows found to attach listeners to");
        return;
    }
    
    rows.forEach(row => {
        row.setAttribute('data-row-click-listener', 'true');
        
        row.addEventListener('click', function(e) {
            // Ignore clicks on the checkbox itself or SVG elements
            if (e.target.type === 'checkbox' || e.target.tagName === 'svg' || e.target.tagName === 'path') {
                return;
            }
            
            // Find the checkbox in this row
            const checkbox = this.querySelector('.row-checkbox');
            if (checkbox) {
                // Toggle checkbox state
                checkbox.checked = !checkbox.checked;
                
                // Manually trigger change event
                const event = new Event('change', { bubbles: true });
                checkbox.dispatchEvent(event);
                
                console.log(`🔄 Row clicked - toggled checkbox for ID: ${checkbox.getAttribute('data-id')}`);
            }
        });
    });
}

// Function to periodically check for uninitialized elements
function checkAndReinitialize() {
    // Check if there are checkboxes without listeners
    const checkboxes = document.querySelectorAll(".row-checkbox:not([data-listener-initialized='true'])");
    if (checkboxes.length > 0) {
        console.log(`🔄 Found ${checkboxes.length} checkboxes without listeners, reattaching...`);
        attachCheckboxListeners();
    }
    
    // Check if there are rows that need click handlers
    const rows = document.querySelectorAll('#transactionTable tr:not(.head-table):not([data-row-click-listener="true"])');
    if (rows.length > 0) {
        console.log(`🔄 Found ${rows.length} rows without click listeners, setting up...`);
        makeRowsClickableForCheckboxes();
    }
    
    // Check if the action bar is in the correct state based on checkbox selection
    const hasCheckedItems = document.querySelectorAll(".row-checkbox:checked").length > 0;
    const actionBarVisible = !document.getElementById("action-bar").classList.contains("hidden");
    
    if (hasCheckedItems !== actionBarVisible) {
        console.log("⚠️ Action bar state doesn't match checkbox state, updating...");
        updateActionBar();
    }
}

// Set up action bar button handlers
function setupActionBarButtons() {
    const actionBar = document.getElementById('action-bar');
    if (!actionBar) return;
    
    // Edit button
    const editBtn = actionBar.querySelector('.edit-btn');
    if (editBtn) {
        // Remove existing listeners
        const newEditBtn = editBtn.cloneNode(true);
        editBtn.parentNode.replaceChild(newEditBtn, editBtn);
        
        newEditBtn.addEventListener('click', function(e) {
            e.preventDefault();
            console.log("Edit button clicked via fix script");
            
            // Get the selected checkbox ID
            const selectedCheckbox = document.querySelector('.row-checkbox:checked');
            if (selectedCheckbox) {
                const id = selectedCheckbox.getAttribute('data-id');
                console.log("Editing transaction with ID:", id);
                
                // Call the existing editSelected function if available
                if (typeof window.editSelected === 'function') {
                    window.editSelected();
                } else {
                    // Fallback direct implementation
                    const baseUrl = "{{ url('/transaction') }}";
                    window.location.href = baseUrl + "/" + id + "/edit";
                }
            }
        });
    }
    
    // Bookmark button
    const bookmarkBtn = actionBar.querySelector('.bookmark-btn');
    if (bookmarkBtn) {
        // Remove existing listeners
        const newBookmarkBtn = bookmarkBtn.cloneNode(true);
        bookmarkBtn.parentNode.replaceChild(newBookmarkBtn, bookmarkBtn);
        
        newBookmarkBtn.addEventListener('click', function(e) {
            e.preventDefault();
            console.log("Bookmark button clicked via fix script");
            
            // Get all selected checkbox IDs
            const selectedCheckboxes = document.querySelectorAll('.row-checkbox:checked');
            if (selectedCheckboxes.length === 0) {
                alert("Please select at least one transaction to bookmark");
                return;
            }
            
            const ids = Array.from(selectedCheckboxes).map(checkbox => checkbox.getAttribute('data-id'));
            console.log("Bookmarking transactions:", ids);
            
            // Call the existing bookmarkSelected function if available
            if (typeof window.bookmarkSelected === 'function') {
                window.bookmarkSelected();
            } else {
                // Fallback direct implementation
                fetch('/transactions/bookmark-multiple', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ ids: ids })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message || "Transactions bookmarked successfully");
                    } else {
                        alert("Error: " + (data.message || "Failed to bookmark transactions"));
                    }
                })
                .catch(error => {
                    console.error("Error bookmarking transactions:", error);
                    alert("Failed to bookmark transactions. Please try again.");
                });
            }
        });
    }
    
    // Remove button
    const removeBtn = actionBar.querySelector('.remove-btn');
    if (removeBtn) {
        // Remove existing listeners
        const newRemoveBtn = removeBtn.cloneNode(true);
        removeBtn.parentNode.replaceChild(newRemoveBtn, removeBtn);
        
        newRemoveBtn.addEventListener('click', function(e) {
            e.preventDefault();
            console.log("Remove button clicked via fix script");
            
            // Get all selected checkbox IDs
            const selectedCheckboxes = document.querySelectorAll('.row-checkbox:checked');
            if (selectedCheckboxes.length === 0) {
                alert("Please select at least one transaction to remove");
                return;
            }
            
            if (!confirm(`Are you sure you want to delete ${selectedCheckboxes.length} transaction(s)?`)) {
                return;
            }
            
            const ids = Array.from(selectedCheckboxes).map(checkbox => checkbox.getAttribute('data-id'));
            console.log("Removing transactions:", ids);
            
            // Call the existing removeSelected function if available
            if (typeof window.removeSelected === 'function') {
                window.removeSelected();
            } else {
                // Fallback direct implementation
                fetch('/transactions', {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ ids: ids })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message || "Transactions deleted successfully");
                        // Reload transactions to update the table
                        if (typeof window.loadTransactions === 'function') {
                            window.loadTransactions();
                        } else {
                            window.location.reload();
                        }
                    } else {
                        alert("Error: " + (data.message || "Failed to delete transactions"));
                    }
                })
                .catch(error => {
                    console.error("Error deleting transactions:", error);
                    alert("Failed to delete transactions. Please try again.");
                });
            }
        });
    }
}

// Function to ensure all global functions are properly registered in the window object
function initializeGlobalFunctions() {
    console.log("🔄 Initializing global functions");
    
    // Register core functions to window object
    window.editSelected = editSelected;
    window.bookmarkSelected = bookmarkSelected;
    window.updateActionBar = updateActionBar;
    
    // Provide a fallback implementation of editTransaction if not already available
    if (typeof window.editTransaction !== 'function') {
        window.editTransaction = function(transactionId) {
            console.log('🔄 Fallback editTransaction called with ID:', transactionId);
            
            if (!transactionId) {
                console.error('❌ Transaction ID is missing!');
                alert('Invalid transaction ID!');
                return;
            }
            
            // Fetch transaction data with correct API endpoint
            fetch(`/transactions/${transactionId}`)
                .then(response => {
                    console.log('📡 API Response:', response);
                    if (!response.ok) {
                        throw new Error(`HTTP error! Status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('📦 Transaction data:', data);
                    if (data.success) {
                        // Call showEditTransactionPopup with the transaction data
                        if (typeof window.showEditTransactionPopup === 'function') {
                            window.showEditTransactionPopup(data.transaction);
                        } else {
                            // If showEditTransactionPopup is not available, use our fallback
                            fallbackShowEditTransactionPopup(data.transaction);
                        }
                    } else {
                        throw new Error(data.message || 'Failed to load transaction');
                    }
                })
                .catch(error => {
                    console.error('❌ Error:', error);
                    alert('Error loading transaction data: ' + error.message);
                });
        };
        console.log('✅ Fallback editTransaction function registered');
    }
    
    // Provide a fallback implementation for showEditTransactionPopup
    if (typeof window.showEditTransactionPopup !== 'function') {
        window.showEditTransactionPopup = function(transaction) {
            fallbackShowEditTransactionPopup(transaction);
        };
        console.log('✅ Fallback showEditTransactionPopup function registered');
    }
    
    // Provide a fallback implementation for hideEditTransactionPopup
    if (typeof window.hideEditTransactionPopup !== 'function') {
        window.hideEditTransactionPopup = function() {
            console.log("🔄 Fallback hideEditTransactionPopup called");
            const popup = document.getElementById("editTransactionPopup");
            if (!popup) {
                console.error("❌ Edit popup element not found!");
                return;
            }
            
            popup.classList.remove("active");
            setTimeout(() => {
                popup.style.display = "none";
            }, 300);
        };
        console.log('✅ Fallback hideEditTransactionPopup function registered');
    }
    
    console.log("✅ Global functions initialized");
}

// The actual fallback function
function fallbackShowEditTransactionPopup(transaction) {
    console.log("📝 Fallback showing edit popup for transaction:", transaction);

    const popup = document.getElementById("editTransactionPopup");
    if (!popup) {
        console.error("❌ Edit popup element not found!");
        alert("Edit popup not found. Please refresh the page.");
        return;
    }

    try {
        // Fill in the form fields
        document.getElementById("edit_id").value = transaction.transaction_id;
        document.getElementById("edit_description").value = transaction.description;
        document.getElementById("edit_amount").value = transaction.amount;
        document.getElementById("edit_type").value = transaction.type;
        document.getElementById("edit_category").value = transaction.category;
        
        // Format date properly
        let transactionDate = transaction.transaction_date;
        if (transactionDate && transactionDate.includes('T')) {
            transactionDate = transactionDate.split('T')[0];
        }
        document.getElementById("edit_transaction_date").value = transactionDate;

        // Show popup
        popup.style.display = "flex";
        setTimeout(() => {
            popup.classList.add("active");
        }, 10);

        // Initialize form listener if available
        if (typeof window.initializeEditFormListener === 'function') {
            window.initializeEditFormListener();
        } else {
            // Simple form submission handler
            setupBasicEditFormSubmission();
        }

        console.log("✅ Edit popup should be visible now");
    } catch (error) {
        console.error("❌ Error while populating form:", error);
        alert("Error showing edit form: " + error.message);
    }
}

// Basic edit form submission handler
function setupBasicEditFormSubmission() {
    const editForm = document.getElementById('editTransactionForm');
    if (!editForm) {
        console.error("❌ Edit form not found!");
        return;
    }

    // Remove existing listeners
    const newForm = editForm.cloneNode(true);
    editForm.parentNode.replaceChild(newForm, editForm);

    newForm.addEventListener('submit', function(e) {
        e.preventDefault();
        console.log("🔄 Form submitted");
        
        const formData = new FormData(this);
        const transactionId = document.getElementById('edit_id').value;
        
        // Convert FormData to JSON
        const formDataObject = {};
        formData.forEach((value, key) => {
            formDataObject[key] = value;
        });
        
        console.log("📦 Sending data:", formDataObject);
        
        fetch(`/transactions/${transactionId}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify(formDataObject)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                if (typeof window.hideEditTransactionPopup === 'function') {
                    window.hideEditTransactionPopup();
                } else {
                    const popup = document.getElementById("editTransactionPopup");
                    if (popup) {
                        popup.classList.remove("active");
                        setTimeout(() => {
                            popup.style.display = "none";
                        }, 300);
                    }
                }
                
                if (typeof window.loadTransactions === 'function') {
                    window.loadTransactions();
                } else {
                    window.location.reload();
                }
            } else {
                throw new Error(data.message || 'Failed to update transaction');
            }
        })
        .catch(error => {
            console.error('❌ Error:', error);
            alert('Error updating transaction: ' + error.message);
        });
    });
}

// Make the edit button call our function properly
function fixEditButton() {
    console.log("🔧 Fixing edit button functionality");
    const actionBar = document.getElementById('action-bar');
    if (!actionBar) {
        console.error("❌ Action bar not found");
        return;
    }
    
    const editBtn = actionBar.querySelector('.edit-btn');
    if (!editBtn) {
        console.error("❌ Edit button not found in action bar");
        return;
    }
    
    // Replace with a fresh button to remove any existing listeners
    const newEditBtn = editBtn.cloneNode(true);
    editBtn.parentNode.replaceChild(newEditBtn, editBtn);
    
    newEditBtn.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        console.log("🖱️ Edit button clicked");
        
        // Call our editSelected function
        if (typeof window.editSelected === 'function') {
            window.editSelected();
        } else {
            console.error("❌ editSelected function not found");
            alert("Error: Edit functionality not available. Please refresh the page.");
        }
    });
    
    console.log("✅ Edit button fixed");
}

// Global implementation of editTransaction function
function editTransaction(transactionId) {
    console.log('🔄 editTransaction function called with ID:', transactionId);
    
    if (!transactionId) {
        console.error('❌ Transaction ID is missing!');
        alert('Invalid transaction ID!');
        return;
    }
    
    // Show loading indicator
    const actionBar = document.getElementById('action-bar');
    if (actionBar) {
        const editBtn = actionBar.querySelector('.edit-btn');
        if (editBtn) {
            editBtn.innerHTML = `<div class="spinner-mini"></div> Loading...`;
            editBtn.disabled = true;
        }
    }
    
    // Fetch transaction data with correct API endpoint
    fetch(`/transactions/${transactionId}`)
        .then(response => {
            console.log('📡 API Response Status:', response.status);
            
            // Check for specific error status codes
            if (response.status === 404) {
                throw new Error('Transaction not found. It may have been deleted.');
            } else if (response.status === 403) {
                throw new Error('You do not have permission to edit this transaction.');
            } else if (!response.ok) {
                throw new Error(`Server error: ${response.status}`);
            }
            
            return response.json();
        })
        .then(data => {
            console.log('📦 Transaction data:', data);
            
            // Reset the edit button
            if (actionBar) {
                const editBtn = actionBar.querySelector('.edit-btn');
                if (editBtn) {
                    editBtn.innerHTML = `
                        <svg width="16" height="16" viewBox="0 0 24 24">
                            <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z" fill="currentColor"/>
                        </svg>
                        Edit
                    `;
                    editBtn.disabled = false;
                }
            }
            
            if (data.success) {
                // Show edit popup with the transaction data
                showEditTransactionPopup(data.transaction);
            } else {
                throw new Error(data.message || 'Failed to load transaction data');
            }
        })
        .catch(error => {
            console.error('❌ Error in editTransaction:', error);
            
            // Reset the edit button
            if (actionBar) {
                const editBtn = actionBar.querySelector('.edit-btn');
                if (editBtn) {
                    editBtn.innerHTML = `
                        <svg width="16" height="16" viewBox="0 0 24 24">
                            <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z" fill="currentColor"/>
                        </svg>
                        Edit
                    `;
                    editBtn.disabled = false;
                }
            }
            
            // Show error message
            alert('Error loading transaction: ' + error.message);
        });
}

// Implementation of showEditTransactionPopup
function showEditTransactionPopup(transaction) {
    console.log("📝 Showing edit popup for transaction:", transaction);

    const popup = document.getElementById("editTransactionPopup");
    if (!popup) {
        console.error("❌ Edit popup element not found!");
        return;
    }

    try {
        // Fill form fields
        document.getElementById("edit_id").value = transaction.transaction_id;
        document.getElementById("edit_description").value = transaction.description;
        document.getElementById("edit_amount").value = transaction.amount;
        document.getElementById("edit_type").value = transaction.type;
        document.getElementById("edit_category").value = transaction.category;
        
        // Format date properly
        let transactionDate = transaction.transaction_date;
        if (transactionDate && transactionDate.includes('T')) {
            transactionDate = transactionDate.split('T')[0];
        }
        document.getElementById("edit_transaction_date").value = transactionDate;

        // Show popup
        popup.style.display = "flex";
        setTimeout(() => {
            popup.classList.add("active");
        }, 10);

        // Initialize form listener
        initializeEditFormListener();

        console.log("✅ Edit popup should be visible now");
    } catch (error) {
        console.error("❌ Error while populating form:", error);
        alert("Error showing edit form: " + error.message);
    }
}

// Implementation of hideEditTransactionPopup
function hideEditTransactionPopup() {
    console.log("🔄 Hiding edit transaction popup");
    const popup = document.getElementById("editTransactionPopup");
    if (!popup) {
        console.error("❌ Edit popup element not found!");
        return;
    }
    
    popup.classList.remove("active");
    setTimeout(() => {
        popup.style.display = "none";
    }, 300);
}

// Implementation of editFormListener
function initializeEditFormListener() {
    console.log("🔄 Initializing edit form listener");
    const editForm = document.getElementById('editTransactionForm');
    if (!editForm) {
        console.error("❌ Edit form not found!");
        return;
    }

    // Remove existing listeners by cloning and replacing
    const newForm = editForm.cloneNode(true);
    editForm.parentNode.replaceChild(newForm, editForm);

    newForm.addEventListener('submit', function(e) {
        e.preventDefault();
        console.log("🔄 Edit form submitted");
        
        // Set up loading state for the submit button
        const submitButton = this.querySelector('button[type="submit"]');
        const originalButtonText = submitButton.innerHTML;
        submitButton.innerHTML = '<div class="spinner-mini"></div> Saving...';
        submitButton.disabled = true;
        
        // Get the form data
        const formData = new FormData(this);
        const transactionId = document.getElementById('edit_id').value;
        
        // Convert FormData to JSON object
        const formDataObject = {};
        formData.forEach((value, key) => {
            formDataObject[key] = value;
        });
        
        console.log("📦 Sending transaction update data:", formDataObject);
        
        // Get CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
        if (!csrfToken) {
            console.error("❌ CSRF token not found");
            alert("Security token missing. Please refresh the page and try again.");
            submitButton.innerHTML = originalButtonText;
            submitButton.disabled = false;
            return;
        }
        
        // Send the update request
        fetch(`/transactions/${transactionId}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify(formDataObject)
        })
        .then(response => {
            // Check for specific response codes
            if (response.status === 404) {
                throw new Error('Transaction not found. It may have been deleted.');
            } else if (response.status === 403) {
                throw new Error('You do not have permission to update this transaction.');
            } else if (response.status === 422) {
                return response.json().then(data => {
                    throw new Error('Validation error: ' + 
                        Object.values(data.errors || {}).flat().join(', '));
                });
            } else if (!response.ok) {
                throw new Error(`Server error: ${response.status}`);
            }
            
            return response.json();
        })
        .then(data => {
            console.log("✅ Update response:", data);
            
            if (data.success) {
                // Success message
                alert('Transaction updated successfully!');
                
                // Hide the popup
                hideEditTransactionPopup();
                
                // Reload the data
                loadTransactions();
            } else {
                throw new Error(data.message || 'Failed to update transaction');
            }
        })
        .catch(error => {
            console.error('❌ Error updating transaction:', error);
            alert('Error updating transaction: ' + error.message);
            
            // Reset button state
            submitButton.innerHTML = originalButtonText;
            submitButton.disabled = false;
        });
    });
    
    // Add listener for the cancel button
    const cancelButton = newForm.querySelector('button[type="button"]');
    if (cancelButton) {
        cancelButton.addEventListener('click', function(e) {
            e.preventDefault();
            hideEditTransactionPopup();
        });
    }
    
    console.log("✅ Edit form listeners initialized");
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
        <div class="button-group" style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 20px;">
            <button type="button" onclick="hideAddTransactionPopup()" style="padding: 8px 15px; border: none; background-color: #dc3545; color: white; border-radius: 20px; cursor: pointer; transition: all 0.3s ease;">Cancel</button>
            <button type="button" class="btn-bookmark" onclick="showBookmarkSelectionModal()" style="background-color: #71D881; color: white; border: none; padding: 8px 15px; border-radius: 20px; display: flex; align-items: center; gap: 6px; cursor: pointer; transition: all 0.3s ease;">
                <svg width="16" height="16" viewBox="0 0 24 24">
                    <path d="M6 4H18V20L12 14L6 20V4Z" stroke="white" stroke-width="2" fill="none"/>
                </svg>
                Add from Bookmark
            </button>
            <button type="submit" style="padding: 8px 15px; background-color: #4CAF50; color: white; border: none; border-radius: 20px; cursor: pointer; transition: all 0.3s ease;">Add</button>
        </div>
        
        <style>
            .button-group button:hover {
                opacity: 0.85;
                transform: translateY(-2px);
                box-shadow: 0 2px 5px rgba(0,0,0,0.2);
            }
        </style>
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

@section('bookmarkSelectionModal')
<div class="popup-content">
    <div class="popup-header">
        <h2>Select from Bookmarks</h2>
        <button onclick="hideBookmarkSelectionModal()">&times;</button>
    </div>

    <div class="bookmark-table" style="max-height: 400px; overflow-y: auto; margin-bottom: 20px; border-radius: 10px; border: 1px solid #eee;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead style="position: sticky; top: 0; background-color: #ffffff; z-index: 10;">
                <tr>
                    <th style="padding: 12px; text-align: center; width: 5%;">
                        <input type="checkbox" id="selectAllBookmarks" onclick="toggleAllBookmarks()">
                    </th>
                    <th style="padding: 12px; text-align: left; width: 30%;">Transaction</th>
                    <th style="padding: 12px; text-align: left; width: 20%;">Amount</th>
                    <th style="padding: 12px; text-align: left; width: 15%;">Type</th>
                    <th style="padding: 12px; text-align: left; width: 30%;">Category</th>
                </tr>
            </thead>
            <tbody id="bookmarkTableBody" style="max-height: 350px;">
                <!-- Will be populated dynamically -->
            </tbody>
        </table>
    </div>

    <div id="bookmarkLoadingIndicator" style="display: none; text-align: center; padding: 20px;">
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
        <p style="margin-top: 10px; color: #666;">Loading bookmarks...</p>
    </div>

    <div id="noBookmarksMessage" style="display: none; text-align: center; padding: 20px; color: #666;">
        <p>No bookmarks found. Create some bookmarks first!</p>
    </div>

    <div class="button-group" style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 20px;">
        <button type="button" onclick="hideBookmarkSelectionModal()" style="padding: 8px 15px; border: none; background-color: #dc3545; color: white; border-radius: 20px; cursor: pointer; transition: all 0.3s ease;">Cancel</button>
        <button type="button" onclick="addSelectedBookmarks()" style="padding: 8px 15px; background-color: #4CAF50; color: white; border: none; border-radius: 20px; cursor: pointer; transition: all 0.3s ease;">Add Selected</button>
    </div>
    
    <style>
        .button-group button:hover {
            opacity: 0.85;
            transform: translateY(-2px);
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }
        
        #bookmarkTableBody tr {
            transition: background-color 0.2s ease;
        }
        
        #bookmarkTableBody tr:hover {
            background-color: #f9f9f9;
        }
        
        #bookmarkTableBody td {
            padding: 10px 12px;
            border-bottom: 1px solid #eee;
        }
        
        /* Style for checkboxes */
        #bookmarkTableBody input[type="checkbox"], #selectAllBookmarks {
            width: 18px;
            height: 18px;
            cursor: pointer;
        }
    </style>
</div>
@endsection

@push('styles')
<style>
    /* Fix for action bar visibility */
    .action-bar {
        visibility: visible;
        transition: opacity 0.3s ease, transform 0.3s ease, visibility 0s;
    }
    
    .action-bar.hidden {
        visibility: hidden;
        transition: opacity 0.3s ease, transform 0.3s ease, visibility 0s 0.3s;
    }
    
    /* Loading spinner for buttons */
    .spinner-mini {
        display: inline-block;
        width: 12px;
        height: 12px;
        border: 2px solid rgba(255, 255, 255, 0.3);
        border-radius: 50%;
        border-top-color: #fff;
        animation: spin 1s ease-in-out infinite;
        margin-right: 5px;
        vertical-align: middle;
    }
    
    @keyframes spin {
        to { transform: rotate(360deg); }
    }
</style>
@endpush

{{-- Add a direct script to fix checkbox handling in the transaction page --}}
<script>
// Wait for DOM to be fully loaded
document.addEventListener('DOMContentLoaded', function() {
    console.log("⚡️ Fix script loaded for transaction page");
    
    // Create a MutationObserver to watch for changes in the table
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.type === 'childList') {
                // Check if checkboxes were added and attach handlers if needed
                attachCheckboxHandlers();
            }
        });
    });
    
    // Start observing the transaction table for changes
    const table = document.getElementById('transactionTable');
    if (table) {
        observer.observe(table, { childList: true, subtree: true });
    }
    
    // Force initial attachment
    attachCheckboxHandlers();
    
    // Directly fix the debug button
    const debugBtn = document.getElementById('debugActionBarBtn');
    if (debugBtn) {
        debugBtn.addEventListener('click', function() {
            forceShowActionBar();
            return false;
        });
    }
    
    // Fix the action bar buttons
    fixActionButtons();
});

// Function to force show the action bar (for debugging)
function forceShowActionBar() {
    console.log("🔧 Forcing action bar to show");
    const actionBar = document.getElementById('action-bar');
    if (actionBar) {
        actionBar.classList.remove('hidden');
        actionBar.style.display = "flex";
        actionBar.style.opacity = "1";
        actionBar.style.transform = "translateX(-50%)";
        actionBar.style.pointerEvents = "auto";
        document.getElementById('selected-count').textContent = "Debug Mode";
    }
}

// Function to handle action bar visibility
function updateActionBarVisibility() {
    console.log("⚡️ Updating action bar visibility");
    const actionBar = document.getElementById('action-bar');
    const selectedCheckboxes = document.querySelectorAll('.row-checkbox:checked');
    const count = selectedCheckboxes.length;
    
    if (!actionBar) return;
    
    console.log(`Found ${count} selected checkboxes`);
    
    // Update the counter
    const countElement = document.getElementById('selected-count');
    if (countElement) {
        countElement.textContent = `${count} Item${count !== 1 ? 's' : ''}`;
    }
    
    // Enable/disable the edit button based on selection count
    const editBtn = actionBar.querySelector('.edit-btn');
    if (editBtn) {
        editBtn.disabled = count !== 1;
    }
    
    if (count > 0) {
        // Show action bar
        actionBar.classList.remove('hidden');
        actionBar.style.display = "flex";
        actionBar.style.opacity = "1";
        actionBar.style.transform = "translateX(-50%)";
        actionBar.style.pointerEvents = "auto";
    } else {
        // Hide action bar
        actionBar.classList.add('hidden');
        actionBar.style.opacity = "0";
        actionBar.style.transform = "translate(-50%, 100px)";
        actionBar.style.pointerEvents = "none";
    }
}

// Function to attach handlers to checkboxes
function attachCheckboxHandlers() {
    // Find all checkboxes without handlers
    const checkboxes = document.querySelectorAll('.row-checkbox:not([data-handler-attached="true"])');
    console.log(`Found ${checkboxes.length} checkboxes without handlers`);
    
    checkboxes.forEach(function(checkbox) {
        // Mark as having handler attached
        checkbox.setAttribute('data-handler-attached', 'true');
        
        // Add change event listener
        checkbox.addEventListener('change', function() {
            console.log("Checkbox changed:", this.checked);
            updateActionBarVisibility();
        });
        
        // Also ensure original handler gets called if it exists
        if (window.updateActionBar && typeof window.updateActionBar === 'function') {
            checkbox.addEventListener('change', window.updateActionBar);
        }
    });
    
    // Handle select all checkbox
    const selectAll = document.getElementById('selectAll');
    if (selectAll && !selectAll.hasAttribute('data-handler-attached')) {
        selectAll.setAttribute('data-handler-attached', 'true');
        
        // Replace with a clone to remove any existing handlers
        const newSelectAll = selectAll.cloneNode(false);
        if (selectAll.parentNode) {
            selectAll.parentNode.replaceChild(newSelectAll, selectAll);
        }
        
        newSelectAll.addEventListener('change', function() {
            const isChecked = this.checked;
            console.log("Select all changed:", isChecked);
            
            // Update all row checkboxes
            document.querySelectorAll('.row-checkbox').forEach(function(checkbox) {
                checkbox.checked = isChecked;
            });
            
            // Update action bar
            updateActionBarVisibility();
            
            // Also call original handler if it exists
            if (window.updateActionBar && typeof window.updateActionBar === 'function') {
                window.updateActionBar();
            }
        });
    }
    
    // Make rows clickable to toggle checkboxes
    makeRowsClickable();
    
    // Initial update of action bar
    updateActionBarVisibility();
}

// Make rows clickable to toggle checkboxes
function makeRowsClickable() {
    const rows = document.querySelectorAll('#transactionTable tr:not(.head-table):not([data-clickable="true"])');
    console.log(`Found ${rows.length} rows to make clickable`);
    
    rows.forEach(function(row) {
        row.setAttribute('data-clickable', 'true');
        
        row.addEventListener('click', function(e) {
            // Don't act if clicking on checkbox itself or SVG elements
            if (e.target.type === 'checkbox' || e.target.tagName === 'svg' || e.target.tagName === 'path') {
                return;
            }
            
            // Find checkbox in this row
            const checkbox = this.querySelector('.row-checkbox');
            if (checkbox) {
                // Toggle the checkbox
                checkbox.checked = !checkbox.checked;
                console.log("Row clicked, toggled checkbox to:", checkbox.checked);
                
                // Create and dispatch a change event
                const event = new Event('change', { bubbles: true });
                checkbox.dispatchEvent(event);
            }
        });
    });
}

// Fix action bar buttons by ensuring they call the correct functions
function fixActionButtons() {
    const actionBar = document.getElementById('action-bar');
    if (!actionBar) return;
    
    // Replace all buttons with clones to remove any existing listeners
    const actionButtons = actionBar.querySelector('.action-buttons');
    if (actionButtons) {
        const newActionButtons = actionButtons.cloneNode(true);
        actionButtons.parentNode.replaceChild(newActionButtons, actionButtons);
        
        // Set up the edit button
        const editBtn = newActionButtons.querySelector('.edit-btn');
        if (editBtn) {
            editBtn.addEventListener('click', function(e) {
                e.preventDefault();
                console.log("Edit button clicked");
                
                const transactionId = document.querySelector('.row-checkbox:checked')?.getAttribute('data-id');
                if (transactionId) {
                    console.log("Editing transaction:", transactionId);
                    // Use correct URL format for your application
                    window.location.href = `/transactions/${transactionId}/edit`;
                } else {
                    console.error("No transaction selected");
                }
            });
        }
        
        // Set up the bookmark button
        const bookmarkBtn = newActionButtons.querySelector('.bookmark-btn');
        if (bookmarkBtn) {
            bookmarkBtn.addEventListener('click', function(e) {
                e.preventDefault();
                console.log("Bookmark button clicked");
                
                const selectedIds = Array.from(document.querySelectorAll('.row-checkbox:checked'))
                    .map(checkbox => checkbox.getAttribute('data-id'));
                
                if (selectedIds.length === 0) {
                    alert("Please select at least one transaction to bookmark");
                    return;
                }
                
                console.log("Bookmarking transactions:", selectedIds);
                
                // Call the existing bookmarkSelected function if available
                if (typeof window.bookmarkSelected === 'function') {
                    window.bookmarkSelected();
                } else {
                    // Fallback direct implementation
                    fetch('/transactions/bookmark-multiple', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({ ids: selectedIds })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert(data.message || "Transactions bookmarked successfully");
                        } else {
                            alert("Error: " + (data.message || "Failed to bookmark transactions"));
                        }
                    })
                    .catch(error => {
                        console.error("Error bookmarking transactions:", error);
                        alert("Failed to bookmark transactions. Please try again.");
                    });
                }
            });
        }
        
        // Set up the remove button
        const removeBtn = newActionButtons.querySelector('.remove-btn');
        if (removeBtn) {
            removeBtn.addEventListener('click', function(e) {
                e.preventDefault();
                console.log("Remove button clicked");
                
                const selectedIds = Array.from(document.querySelectorAll('.row-checkbox:checked'))
                    .map(checkbox => checkbox.getAttribute('data-id'));
                
                if (selectedIds.length === 0) {
                    alert("Please select at least one transaction to remove");
                    return;
                }
                
                if (!confirm(`Are you sure you want to delete ${selectedIds.length} transaction(s)?`)) {
                    return;
                }
                
                console.log("Removing transactions:", selectedIds);
                
                // Call the existing removeSelected function if available
                if (typeof window.removeSelected === 'function') {
                    window.removeSelected();
                } else {
                    // Fallback direct implementation
                    fetch('/transactions', {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({ ids: selectedIds })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert(data.message || "Transactions deleted successfully");
                            // Reload transactions to update the table
                            if (typeof window.loadTransactions === 'function') {
                                window.loadTransactions();
                            } else {
                                window.location.reload();
                            }
                        } else {
                            alert("Error: " + (data.message || "Failed to delete transactions"));
                        }
                    })
                    .catch(error => {
                        console.error("Error deleting transactions:", error);
                        alert("Failed to delete transactions. Please try again.");
                    });
                }
            });
        }
    }
}
</script>
</script>