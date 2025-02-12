<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
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

    
    
    <script src='javascript/script.js'>
   
    </script>
</body>
</html>