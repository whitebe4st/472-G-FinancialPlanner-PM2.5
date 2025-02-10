<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction</title>
    <link rel="stylesheet" href="/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Jost:wght@400;500;600;700&display=swap" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    <div class="grid">
        <div class="container">
            <nav>
                <h1 class="logo">Logo</h1>
                <ul class="sideBar">
                    <li>
                        <a href="/dashboard">
                            <svg width="24" height="24" viewBox="0 0 24 24">
                                <!-- Dashboard icon path -->
                                <circle cx="12" cy="12" r="8" stroke="#A0A0A0" stroke-width="2" fill="none"/>
                            </svg>
                            Dashboard
                        </a>
                    </li>
                    <li>
                        <a href="#" class="active">
                            <svg width="24" height="24" viewBox="0 0 24 24">
                                <!-- Transaction icon path -->
                                <path d="M4 12H20M4 12L8 8M4 12L8 16" stroke="#A0A0A0" stroke-width="2"/>
                            </svg>
                            Transaction
                        </a>
                    </li>
                    <li>
                        <a href="/bookmark">
                            <svg width="24" height="24" viewBox="0 0 24 24">
                                <!-- Bookmark icon path -->
                                <path d="M6 4H18V20L12 14L6 20V4Z" stroke="#A0A0A0" stroke-width="2" fill="none"/>
                            </svg>
                            Bookmark
                        </a>
                    </li>
                    <li class="logout-container">
                        <form action="/logout" method="POST">
                            @csrf
                            <button type="submit" class="logout-btn">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
                                    <polyline points="16 17 21 12 16 7" />
                                    <line x1="21" y1="12" x2="9" y2="12" />
                                </svg>
                                Logout
                            </button>
                        </form>
                    </li>
                </ul>
            </nav>
        </div>

        <div class="container">
            <div class="navBar">
                <div class="searchBar">
                    <svg width="16" height="16" viewBox="0 0 16 16">
                        <circle cx="7" cy="7" r="6" stroke="#D8D8D8" stroke-width="2" fill="none"/>
                        <path d="M11 11L15 15" stroke="#D8D8D8" stroke-width="2"/>
                    </svg>
                    <input 
                        type="text" 
                        placeholder="Search" 
                        class="searchInput"
                        name="search"
                    >
                </div>
                <div class="user">
                    <a href="/notifications" class="notification-icon">
                        <img src="/images/NotificationIcon.png" alt="Notification">
                    </a>
                    <a href="/profile" class="profile">
                        <img src="/images/ProfileIcon.png" class="profile-img" alt="Profile">
                        <span>{{ Auth::user()->username }}</span>
                    </a>
                </div>
            </div>

            <div class="content">
                <h1>Transactions</h1>
                <p>Track your finances and achieve your financial goal.</p>

                <div class="table-container">
                    <div class="table-header">
                        <h2>Transaction History</h2>
                        <div class="filter-buttons">
                            <button class="filter-btn">
                                <svg width="24" height="24" viewBox="0 0 24 24">
                                    <!-- Filter icon path -->
                                    <path d="M4 4H20M8 12H16M10 20H14" stroke="#A0A0A0" stroke-width="2"/>
                                </svg>
                                Filter
                            </button>
                            <button class="filter-btn">
                                <svg width="24" height="24" viewBox="0 0 24 24">
                                    <!-- Calendar icon path -->
                                    <rect x="4" y="4" width="16" height="16" stroke="#A0A0A0" stroke-width="2" fill="none"/>
                                </svg>
                                Yearly
                            </button>
                        </div>
                    </div>

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
                            @foreach(Auth::user()->transactions()->orderBy('transaction_date', 'desc')->get() as $transaction)
                            <tr>
                                <td><input type="checkbox" class="row-checkbox"></td>
                                <td>{{ $transaction->description }}</td>
                                <td>{{ $transaction->transaction_date->format('d/m/y') }}</td>
                                <td>${{ number_format($transaction->amount, 2) }}</td>
                                <td>{{ $transaction->type === 'expense' ? 'Exp.' : 'Inc.' }}</td>
                                <td>{{ $transaction->category }}</td>
                                <td>
                                    <svg width="24" height="24" viewBox="0 0 24 24">
                                        <path d="M6 4H18V20L12 14L6 20V4Z" stroke="#A0A0A0" stroke-width="2" fill="none"/>
                                    </svg>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="addBar-btn">
                        <button onclick="showAddTransactionPopup()">+</button>
                    </div>

                    <div class="pagination">
                        <button class="page-nav" data-page="prev">&lt;</button>
                        <div class="page-numbers">
                            <button class="page-btn" data-page="1">1</button>
                            <button class="page-btn active" data-page="2">2</button>
                            <button class="page-btn" data-page="3">3</button>
                            <button class="page-btn" data-page="4">4</button>
                            <button class="page-btn" data-page="5">5</button>
                            <button class="page-btn" data-page="6">6</button>
                            <button class="page-btn" data-page="7">7</button>
                            <button class="page-btn" data-page="8">8</button>
                            <button class="page-btn" data-page="9">9</button>
                            <button class="page-btn" data-page="10">10</button>
                        </div>
                        <button class="page-nav" data-page="next">&gt;</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="addTransactionPopup" class="popup">
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
    </div>

    <script>
    function toggleAllCheckboxes() {
        const selectAllCheckbox = document.getElementById('selectAll');
        const rowCheckboxes = document.getElementsByClassName('row-checkbox');
        
        for (let checkbox of rowCheckboxes) {
            checkbox.checked = selectAllCheckbox.checked;
        }
    }

    function showAddTransactionPopup() {
        const popup = document.getElementById('addTransactionPopup');
        popup.style.display = 'flex';
        // Trigger reflow to ensure transition works
        popup.offsetHeight;
        popup.classList.add('active');
    }

    function hideAddTransactionPopup() {
        const popup = document.getElementById('addTransactionPopup');
        popup.classList.remove('active');
        // Wait for animation to finish before hiding
        setTimeout(() => {
            popup.style.display = 'none';
        }, 300);
    }

    let categories = []; // Will store all user categories

    function loadCategories() {
        fetch('/transactions/categories')
            .then(response => response.json())
            .then(data => {
                categories = data;
                const dropdown = document.getElementById('categoryDropdown');
                updateCategoryDropdown('');
            });
    }

    function updateCategoryDropdown(filter) {
        const dropdown = document.getElementById('categoryDropdown');
        dropdown.innerHTML = '';
        
        const filteredCategories = categories.filter(cat => 
            cat.toLowerCase().includes(filter.toLowerCase())
        );

        if (filteredCategories.length > 0) {
            filteredCategories.forEach(category => {
                const div = document.createElement('div');
                div.className = 'category-option';
                div.textContent = category;
                div.onclick = () => selectCategory(category);
                dropdown.appendChild(div);
            });
        } else if (filter) {
            const div = document.createElement('div');
            div.className = 'no-categories';
            div.textContent = `Press Enter to add "${filter}" as new category`;
            dropdown.appendChild(div);
        } else {
            const div = document.createElement('div');
            div.className = 'no-categories';
            div.textContent = 'No categories found';
            dropdown.appendChild(div);
        }
    }

    function filterCategories(value) {
        const dropdown = document.getElementById('categoryDropdown');
        dropdown.classList.add('show');
        updateCategoryDropdown(value);
    }

    function selectCategory(category) {
        document.getElementById('category').value = category;
        document.getElementById('categoryDropdown').classList.remove('show');
    }

    // Handle clicking outside to close dropdown
    document.addEventListener('click', function(e) {
        const dropdown = document.getElementById('categoryDropdown');
        const input = document.getElementById('category');
        if (!input.contains(e.target) && !dropdown.contains(e.target)) {
            dropdown.classList.remove('show');
        }
    });

    // Handle category input keyboard events
    document.getElementById('category').addEventListener('keydown', function(e) {
        const dropdown = document.getElementById('categoryDropdown');
        if (e.key === 'Enter' && !categories.includes(this.value)) {
            e.preventDefault();
            // Allow new category
            dropdown.classList.remove('show');
        }
    });

    // Focus on category input
    document.getElementById('category').addEventListener('focus', function() {
        document.getElementById('categoryDropdown').classList.add('show');
        updateCategoryDropdown(this.value);
    });

    document.addEventListener('DOMContentLoaded', function() {
        // Load categories when page loads
        loadCategories();
        
        // Handle form submission
        document.getElementById('transactionForm').addEventListener('submit', function(e) {
            e.preventDefault();
            console.log('Form submitted'); // Debug log
            
            const formData = new FormData(this);
            const formDataObject = Object.fromEntries(formData);
            console.log('Form data:', formDataObject); // Debug log
            
            fetch('/transactions', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(formDataObject)
            })
            .then(response => {
                console.log('Response:', response); // Debug log
                return response.json();
            })
            .then(data => {
                console.log('Data:', data); // Debug log
                if (data.success) {
                    hideAddTransactionPopup();
                    window.location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to add transaction');
            });
        });
    });
    </script>
</body>
</html>