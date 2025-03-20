@extends('layout/layout')

@section('title')
    Bookmark
@endsection

@section('content')
<div class="content-wrapper" style="padding: 2rem;">
    <h1 style="margin-bottom: 1rem;">Bookmark</h1>
    <p style="margin-bottom: 2rem;">Your recurring transactions.</p>

    <div id="transactionTable">
        <div class="transaction-header">
            <h2 class="transaction-title">Bookmark</h2>
            <div class="transaction-actions">
                <div class="dropdown">
                    <button class="filter-button">
                        <svg width="24" height="24" viewBox="0 0 24 24">
                            <path d="M4 4H20M8 12H16M10 20H14" stroke="#A0A0A0" stroke-width="2"/>
                        </svg>
                        <span>Filter</span>
                        <svg width="24" height="24" viewBox="0 0 24 24">
                            <path d="M6 9L12 15L18 9" stroke="#A0A0A0" stroke-width="2" fill="none"/>
                        </svg>
                    </button>
                    <div class="dropdown-content">
                        <a href="#" data-filter="all">All</a>
                        <a href="#" data-filter="income">Income</a>
                        <a href="#" data-filter="expense">Expense</a>
                    </div>
                </div>
            </div>
        </div>

        <table style="width: 100%;">
            <tr class="head-table">
                <th style="width: 5%">
                    <input type="checkbox" class="select-all" onclick="toggleAllBookmarkCheckboxes()">
                </th>
                <th style="width: 25%">Transaction</th>
                <th style="width: 20%">Amount</th>
                <th style="width: 15%">Type</th>
                <th style="width: 35%">Category</th>
            </tr>
            @foreach($bookmarkedTransactions as $bookmark)
            <tr>
                <td>
                    <input type="checkbox" class="bookmark-checkbox" data-id="{{ $bookmark->bookmark_id }}">
                </td>
                <td data-category="{{ $bookmark->category }}">{{ $bookmark->description }}</td>
                <td>${{ number_format($bookmark->amount, 2) }}</td>
                <td data-type="{{ $bookmark->type }}">
                    <span class="{{ $bookmark->type === 'income' ? 'income' : 'expense' }}">
                        {{ ucfirst($bookmark->type) }}
                    </span>
                </td>
                <td>{{ $bookmark->category }}</td>
            </tr>
            @endforeach
        </table>

        <div class="pagination-wrapper">
            {{ $bookmarkedTransactions->links() }}
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
@endsection

@section('editBookmarkPopup')
<div class="popup-content">
    <div class="popup-header">
        <h2>Edit Bookmark</h2>
        <button onclick="hideEditBookmarkPopup()">&times;</button>
    </div>

    <form id="editBookmarkForm">
        @csrf
        <input type="hidden" id="edit_bookmark_id" name="id">
        
        <div class="form-group">
            <label for="edit_bookmark_description">Description</label>
            <input type="text" id="edit_bookmark_description" name="description" required>
        </div>

        <div class="form-group">
            <label for="edit_bookmark_amount">Amount</label>
            <input type="number" id="edit_bookmark_amount" name="amount" step="0.01" required>
        </div>

        <div class="form-group">
            <label for="edit_bookmark_type">Type</label>
            <select id="edit_bookmark_type" name="type" required>
                <option value="expense">Expense</option>
                <option value="income">Income</option>
            </select>
        </div>

        <div class="form-group">
            <label for="edit_bookmark_category">Category</label>
            <div class="category-input-container">
                <input 
                    type="text" 
                    id="edit_bookmark_category" 
                    name="category" 
                    list="categories" 
                    autocomplete="off"
                    required
                >
                <div id="editBookmarkCategoryDropdown" class="category-dropdown">
                    <!-- Categories will be populated here -->
                </div>
            </div>
        </div>

        <div class="button-group">
            <button type="button" onclick="hideEditBookmarkPopup()">Cancel</button>
            <button type="submit">Save</button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    initializeBookmarkPage();
});

function toggleAllBookmarkCheckboxes() {
    const selectAllCheckbox = document.querySelector(".select-all");
    const checkboxes = document.querySelectorAll(".bookmark-checkbox");
    
    checkboxes.forEach((checkbox) => {
        checkbox.checked = selectAllCheckbox.checked;
    });
    
    updateBookmarkActionBar();
}

function updateBookmarkActionBar() {
    console.log("🔄 Running updateBookmarkActionBar...");

    const actionBar = document.getElementById("action-bar");
    const selectedCount = document.getElementById("selected-count");
    const editButton = document.querySelector(".edit-btn");
    const removeButton = document.querySelector(".remove-btn");
    const selectedItems = document.querySelectorAll(".bookmark-checkbox:checked");
    
    console.log("✅ Selected Items:", selectedItems.length);

    const selectedIds = Array.from(selectedItems).map(checkbox => checkbox.dataset.id);
    console.log("📌 Selected Bookmark IDs:", selectedIds);

    if (selectedItems.length > 0) {
        // Show action bar
        actionBar.classList.remove("hidden");
        actionBar.style.display = "flex";
        selectedCount.textContent = `${selectedItems.length} Item(s)`;

        // Edit button - only for single selection
        if (selectedItems.length === 1) {
            editButton.disabled = false;
            editButton.style.display = "flex";
            
            // Replace button to remove old event listeners
            const newEditButton = editButton.cloneNode(true);
            editButton.parentNode.replaceChild(newEditButton, editButton);
            
            // Add click event for editing
            newEditButton.addEventListener("click", function() {
                const bookmarkId = selectedItems[0].getAttribute("data-id");
                console.log("bookmark id = ", bookmarkId);
                if (bookmarkId) {
                    editBookmark(bookmarkId);
                }
            });
        } else {
            // Disable edit button for multiple selections
            editButton.disabled = true;
            editButton.style.display = "none";
        }

        // Remove button - enable for any selection
        const newRemoveButton = removeButton.cloneNode(true);
        removeButton.parentNode.replaceChild(newRemoveButton, removeButton);
        
        newRemoveButton.addEventListener("click", function() {
            removeSelectedBookmarks(selectedIds);
        });
    } else {
        // Hide action bar when no items selected
        actionBar.classList.add("hidden");
        setTimeout(() => {
            actionBar.style.display = "none";
        }, 300);
    }
}

function removeSelectedBookmarks(ids) {
    if (!ids || ids.length === 0) {
        console.error('❌ No bookmark IDs provided for removal');
        return;
    }

    if (!confirm(`Are you sure you want to delete ${ids.length} bookmark(s)?`)) {
        return;
    }

    console.log('🗑️ Removing bookmarks:', ids);

    fetch('/bookmarks', {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        body: JSON.stringify({ ids: ids })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! Status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            showNotification(data.message, 'success');
            // Reload the page to reflect changes
            window.location.reload();
        } else {
            throw new Error(data.message || 'Failed to delete bookmarks');
        }
    })
    .catch(error => {
        console.error('❌ Error:', error);
        showNotification('Error deleting bookmarks: ' + error.message, 'error');
    });
}

function editBookmark(bookmarkId) {
    console.log('🔄 Editing bookmark with ID:', bookmarkId);

    if (!bookmarkId) {
        console.error('❌ Bookmark ID is missing!');
        alert('Invalid bookmark ID!');
        return;
    }

    // Fetch bookmark data
    fetch(`/bookmarks/${bookmarkId}`)
        .then(response => {
            console.log('📡 API Response:', response);
            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('📦 Bookmark data:', data);
            if (data.success) {
                showEditBookmarkPopup(data.bookmark);
            } else {
                throw new Error(data.message || 'Failed to load bookmark');
            }
        })
        .catch(error => {
            console.error('❌ Error:', error);
            showNotification('Error loading bookmark data: ' + error.message, 'error');
        });
}

function showEditBookmarkPopup(bookmark) {
    console.log("📝 Showing edit popup for bookmark:", bookmark);

    const popup = document.getElementById("editBookmarkPopup");
    if (!popup) {
        console.error("❌ Edit bookmark popup element not found!");
        return;
    }

    try {
        // Fill the form with bookmark data
        document.getElementById("edit_bookmark_id").value = bookmark.bookmark_id;
        document.getElementById("edit_bookmark_description").value = bookmark.description;
        document.getElementById("edit_bookmark_amount").value = bookmark.amount;
        document.getElementById("edit_bookmark_type").value = bookmark.type;
        document.getElementById("edit_bookmark_category").value = bookmark.category;

        // Show popup
        popup.style.display = "flex";
        popup.offsetHeight; // Trigger reflow
        popup.classList.add("active");

        // Initialize form listener
        initializeEditBookmarkFormListener();

        console.log("✅ Popup should be visible now");
    } catch (error) {
        console.error("❌ Error while populating form:", error);
        showNotification('Error showing edit form: ' + error.message, 'error');
    }
}

function hideEditBookmarkPopup() {
    const popup = document.getElementById("editBookmarkPopup");
    if (!popup) {
        console.error("❌ Edit bookmark popup element not found!");
        return;
    }
    
    popup.classList.remove("active");
    setTimeout(() => {
        popup.style.display = "none";
    }, 300);
}

function initializeEditBookmarkFormListener() {
    const editForm = document.getElementById('editBookmarkForm');
    if (!editForm) {
        console.error("❌ Edit bookmark form not found!");
        return;
    }

    // Remove existing listeners
    const newForm = editForm.cloneNode(true);
    editForm.parentNode.replaceChild(newForm, editForm);

    newForm.addEventListener('submit', function(e) {
        e.preventDefault();
        console.log("🔄 Form submitted");
        
        const formData = new FormData(this);
        const bookmarkId = document.getElementById('edit_bookmark_id').value;
        
        // Convert FormData to JSON
        const formDataObject = {};
        formData.forEach((value, key) => {
            formDataObject[key] = value;
        });
        
        console.log("📦 Sending data:", formDataObject);
        
        fetch(`/bookmarks/${bookmarkId}`, {
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
                hideEditBookmarkPopup();
                showNotification(data.message, 'success');
                window.location.reload();
            } else {
                throw new Error(data.message || 'Failed to update bookmark');
            }
        })
        .catch(error => {
            console.error('❌ Error:', error);
            showNotification('Error updating bookmark: ' + error.message, 'error');
        });
    });
}

// Attach event listeners to bookmark checkboxes
function attachBookmarkCheckboxListeners() {
    document.querySelectorAll(".bookmark-checkbox").forEach(checkbox => {
        checkbox.addEventListener("change", function() {
            updateBookmarkActionBar();
        });
    });
}

// Initialize bookmark page
function initializeBookmarkPage() {
    // Attach event listeners to checkboxes
    attachBookmarkCheckboxListeners();
    
    // Initialize filter functionality
    const filterButtons = document.querySelectorAll('.dropdown-content a');
    filterButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const filter = this.getAttribute('data-filter');
            window.location = `/bookmark?filter=${filter}`;
        });
    });
}

// Show notification function
function showNotification(message, type) {
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.textContent = message;
    document.body.appendChild(notification);

    // Remove notification after 3 seconds
    setTimeout(() => {
        notification.classList.add('fade-out');
        setTimeout(() => {
            notification.remove();
        }, 300);
    }, 2700);
}
</script>
@endpush

@push('styles')
<style>
    .pagination-wrapper {
        margin-top: 2rem;
        display: flex;
        justify-content: center;
    }

    .pagination {
        display: flex;
        list-style: none;
        padding: 0;
        gap: 0.5rem;
    }

    .pagination li {
        display: flex;
    }

    .pagination li a,
    .pagination li span {
        padding: 0.5rem 1rem;
        border: 2px solid var(--border-color);
        border-radius: 8px;
        color: var(--text-color);
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .pagination li.active span {
        background: #71D881;
        color: white;
        border-color: #71D881;
    }

    .pagination li a:hover {
        background: var(--hover-bg);
    }

    [data-theme="dark"] .pagination li a,
    [data-theme="dark"] .pagination li span {
        border-color: rgba(255, 255, 255, 0.1);
    }
</style>
@endpush


