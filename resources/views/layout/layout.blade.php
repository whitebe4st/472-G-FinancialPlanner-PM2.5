<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="/images/logo.png">
    
    <link rel="stylesheet" href="/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Jost:wght@400;500;600;700&display=swap" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/turbolinks/5.2.0/turbolinks.js" defer></script>
    <script src="{{ asset('javascript/script.js') }}" defer></script>
</head>
<body>
    <div class="grid">
        <div class="container">
            <nav>
                <div class="logo" style="padding: 0.75rem; display: flex; align-items: center; gap: 0.5rem;">
                    <img src="/images/logo.png" alt="Logo" class="logo-light" style="width: 60px; height: 60px; object-fit: contain;">
                    <img src="/images/logo-darkmode.png" alt="Logo" class="logo-dark" style="width: 60px; height: 60px; object-fit: contain;">
                    <div style="font-size: 1.2rem; font-weight: 600;">
                        <span style="color: #71D881;">Financial</span>
                        <span>Planner</span>
                    </div>
                </div>
                
                <!-- Add this clock and calendar div -->
                <div class="datetime-display">
                    <div class="time" id="clock"></div>
                    <div class="date" id="calendar"></div>
                </div>

                <ul class="sideBar">
                    <li>
                        <a href="/dashboard" class="{{ Request::is('dashboard') ? 'active' : '' }}">
                            <svg width="24" height="24" viewBox="0 0 24 24">
                                <!-- Dashboard icon path -->
                                <circle cx="12" cy="12" r="8" stroke="#A0A0A0" stroke-width="2" fill="none"/>
                            </svg>
                            Dashboard
                        </a>
                    </li>
                    <li>
                        <a href="/transaction" class="{{ Request::is('transaction') ? 'active' : '' }}">
                            <svg width="24" height="24" viewBox="0 0 24 24">
                                <!-- Transaction icon path -->
                                <path d="M4 12H20M4 12L8 8M4 12L8 16" stroke="#A0A0A0" stroke-width="2"/>
                            </svg>
                            Transaction
                        </a>
                    </li>
                    <li>
                        <a href="/bookmark" class="{{ Request::is('bookmark') ? 'active' : '' }}">
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
                    <button id="darkModeToggle" class="dark-mode-toggle">
                        <svg width="24" height="24" viewBox="0 0 24 24" class="moon">
                            <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z" stroke="#A0A0A0" stroke-width="2" fill="none"/>
                        </svg>
                        <svg width="24" height="24" viewBox="0 0 24 24" class="sun">
                            <circle cx="12" cy="12" r="5" stroke="#A0A0A0" stroke-width="2" fill="none"/>
                            <path d="M12 1v2M12 21v2M4.22 4.22l1.42 1.42M18.36 18.36l1.42 1.42M1 12h2M21 12h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42" stroke="#A0A0A0" stroke-width="2"/>
                        </svg>
                    </button>
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
               @yield('content')
                
            </div>
        </div>
    </div>

    <div id="addTransactionPopup" class="popup">
        @yield('addTransactionPopup')
    </div>

    <!-- Add this new modal -->
    <div id="bookmarkSelectionModal" class="popup">
        <div class="popup-content">
            <div class="popup-header">
                <h2>Select Bookmarks</h2>
                <button type="button" class="close-button" onclick="hideBookmarkSelectionModal()">&times;</button>
            </div>
            
            <div class="bookmark-table">
                <table>
                    <thead>
                        <tr>
                            <th style="width: 5%">
                                <input type="checkbox" id="selectAllBookmarks" onclick="toggleAllBookmarks()">
                            </th>
                            <th style="width: 25%">Transaction</th>
                            <th style="width: 20%">Amount</th>
                            <th style="width: 15%">Type</th>
                            <th style="width: 35%">Category</th>
                        </tr>
                    </thead>
                    <tbody id="bookmarkTableBody">
                        <!-- Will be populated dynamically -->
                    </tbody>
                </table>
            </div>

            <div class="popup-footer">
                <button type="button" class="btn-secondary" onclick="hideBookmarkSelectionModal()">Cancel</button>
                <button type="button" class="btn-primary" onclick="addSelectedBookmarks()">Add to Transaction</button>
            </div>
        </div>
    </div>

    
    
    <script src='javascript/script.js'>
   
    </script>
    @stack('scripts')
    <script src="{{ asset('js/bookmark.js') }}"></script>
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="{{ asset('js/loading.js') }}"></script>
</body>
</html>