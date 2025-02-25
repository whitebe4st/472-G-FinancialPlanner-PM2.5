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

        <div class="addBar-btn">
            <button onclick="showAddTransactionPopup()">+</button>
        </div>
    </div>
</div>

<style>
.dropdown-content {
    padding: 0.5rem 0;
    transform-origin: top;
    transform: scaleY(0);
    transition: all 0.2s ease-in-out;
    opacity: 0;
}

.dropdown-content.show {
    transform: scaleY(1);
    opacity: 1;
}

.dropdown-content a {
    padding: 0.5rem 1rem;
    text-decoration: none;
    color: #333;
    display: block;
    position: relative;
    transition: all 0.2s ease;
}

.dropdown-content a:hover {
    background-color: #f5f5f5;
    padding-left: 1.5rem;
}

.dropdown-content a.selected {
    background-color: #f0f0f0;
    font-weight: 500;
    color: #000;
}

.dropdown-content a.selected::after {
    content: "✓";
    position: absolute;
    right: 1rem;
    color: #28a745;
    opacity: 0;
    animation: fadeIn 0.3s ease forwards;
}

.filter-btn {
    background: white;
    border: 1px solid #ddd;
    border-radius: 5px;
    cursor: pointer;
    transition: all 0.2s ease;
}

.filter-btn:hover {
    background: #f5f5f5;
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.filter-btn.active {
    background: #f0f0f0;
    border-color: #ccc;
}

.addBar-btn button {
    transition: all 0.3s ease;
}

.addBar-btn button:hover {
    transform: rotate(90deg);
}

/* Table animations */
#transactionTable table {
    animation: fadeIn 0.5s ease;
}

#transactionTable tr {
    animation: slideIn 0.3s ease;
}

/* Pagination animations */
.pagination button {
    transition: all 0.2s ease;
}

.pagination button:hover {
    transform: translateY(-2px);
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.pagination button.active {
    transform: scale(1.1);
}

/* Animation keyframes */
@keyframes fadeIn {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}

@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateX(-10px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

@keyframes scaleIn {
    from {
        transform: scale(0.95);
        opacity: 0;
    }
    to {
        transform: scale(1);
        opacity: 1;
    }
}

hr {
    border: none;
    border-top: 1px solid #eee;
    margin: 0.5rem 0;
    animation: expandWidth 0.3s ease;
}

@keyframes expandWidth {
    from {
        width: 0;
    }
    to {
        width: 100%;
    }
}

/* Loading animation styles */
#loadingAnimation {
    animation: fadeIn 0.2s ease;
}

.spinner {
    opacity: 0.7;
}

/* Table container transition */
#transactionTable {
    min-height: 200px;
    transition: opacity 0.2s ease;
}

/* Optimize table animation */
#transactionTable table {
    animation: fadeIn 0.2s ease;
}

#transactionTable tr {
    animation: slideIn 0.2s ease;
    animation-fill-mode: both;
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

function toggleDropdown(id) {
    const dropdown = document.getElementById(id);
    const allDropdowns = document.getElementsByClassName('dropdown-content');
    const button = dropdown.previousElementSibling;
    
    // Close all other dropdowns and remove active state
    Array.from(allDropdowns).forEach(d => {
        if (d.id !== id) {
            d.classList.remove('show');
            d.style.display = 'none';
            d.previousElementSibling.classList.remove('active');
        }
    });
    
    // Toggle current dropdown
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
    
    fetch(`/api/transactions?page=${currentPage}&${new URLSearchParams(currentFilters)}`)
        .then(response => response.json())
        .then(data => {
            // Hide loading animation and show table
            loadingAnimation.style.display = 'none';
            tableContainer.innerHTML = generateTableHTML(data);
            requestAnimationFrame(() => {
                tableContainer.style.opacity = '1';
            });
        })
        .catch(error => {
            console.error('Error:', error);
            loadingAnimation.style.display = 'none';
            tableContainer.innerHTML = '<div style="text-align: center; padding: 2rem; color: #dc3545;">Error loading transactions. Please try again.</div>';
            tableContainer.style.opacity = '1';
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
                ${data.data.map((transaction, index) => `
                    <tr style="animation-delay: ${index * 0.05}s">
                        <td><input type="checkbox" class="row-checkbox"></td>
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
                    </tr>
                `).join('')}
            </tbody>
        </table>
        <div class="pagination" style="margin-top: 1rem; display: flex; justify-content: center; gap: 0.5rem;">
            ${generatePaginationHTML(data)}
        </div>
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
    currentPage = page;
    loadTransactions();
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
});
</script>
@endpush

@endsection

@section('addTransactionPopup')
<div class="popup-content">
            <h2>Add Transaction</h2>
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