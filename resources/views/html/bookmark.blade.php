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
                    <input type="checkbox" class="select-all">
                </th>
                <th style="width: 25%">Transaction</th>
                <th style="width: 20%">Amount</th>
                <th style="width: 15%">Type</th>
                <th style="width: 35%">Category</th>
            </tr>
            @foreach($bookmarkedTransactions as $bookmark)
            <tr data-bookmark-id="{{ $bookmark->bookmark_id }}">
                <td>
                    <input type="checkbox" 
                           class="bookmark-checkbox" 
                           id="bookmark-checkbox-{{ $bookmark->bookmark_id }}" 
                           data-id="{{ $bookmark->bookmark_id }}" 
                           autocomplete="off">
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
// Main initialization when page loads
document.addEventListener('DOMContentLoaded', function() {
    console.log('🌟 DOM fully loaded, initializing bookmark page...');
    initializeBookmarkPage();
    
    // Setup event listener for Turbolinks or AJAX loads
    document.addEventListener('page:load', function() {
        console.log('🔄 Page loaded via Turbolinks, reinitializing...');
        initializeBookmarkPage();
    });
    
    // Set up a periodic check to ensure event listeners are attached
    // This helps with dynamic content loading or after AJAX operations
    setInterval(checkAndReinitialize, 2000);
});

// Function to check if elements need reinitialization
function checkAndReinitialize() {
    // Check if there are bookmark checkboxes without listeners
    const checkboxes = document.querySelectorAll(".bookmark-checkbox:not([data-listener-initialized='true'])");
    if (checkboxes.length > 0) {
        console.log(`🔄 Found ${checkboxes.length} bookmark checkboxes without listeners, reattaching...`);
        attachBookmarkCheckboxListeners();
    }
    
    // Check if there are rows that need click handlers
    const rows = document.querySelectorAll('#transactionTable tr:not(.head-table):not([data-row-click-listener="true"])');
    if (rows.length > 0) {
        console.log(`🔄 Found ${rows.length} rows without click listeners, setting up...`);
        makeRowsClickableForCheckboxes();
    }
    
    // Check if the action bar is in the correct state based on checkbox selection
    const hasCheckedItems = document.querySelectorAll(".bookmark-checkbox:checked").length > 0;
    const actionBarVisible = !document.getElementById("action-bar").classList.contains("hidden");
    
    if (hasCheckedItems !== actionBarVisible) {
        console.log("⚠️ Action bar state doesn't match checkbox state, updating...");
        updateBookmarkActionBar();
    }
}

// Force reattach all checkbox event listeners
function forceReattachAllCheckboxListeners() {
    const checkboxes = document.querySelectorAll(".bookmark-checkbox");
    
    // Remove the initialization attribute from all checkboxes to force reinitialization
    checkboxes.forEach(checkbox => {
        checkbox.removeAttribute('data-listener-initialized');
    });
    
    // Also reset the select-all checkbox
    const selectAllCheckbox = document.querySelector(".select-all");
    if (selectAllCheckbox) {
        selectAllCheckbox.removeAttribute('data-listener-initialized');
    }
    
    // Reattach all listeners
    attachBookmarkCheckboxListeners();
}

function updateBookmarkActionBar() {
    console.log("🔄 Running updateBookmarkActionBar...");

    const actionBar = document.getElementById("action-bar");
    const selectedCount = document.getElementById("selected-count");
    const selectedItems = document.querySelectorAll(".bookmark-checkbox:checked");
    
    if (!actionBar || !selectedCount) {
        console.error("❌ One or more action bar elements not found!");
        return;
    }
    
    console.log("✅ Selected Items:", selectedItems.length);

    // Force style update to remove any stale display settings
    actionBar.setAttribute('style', '');

    const selectedIds = Array.from(selectedItems).map(checkbox => checkbox.dataset.id);
    console.log("📌 Selected Bookmark IDs:", selectedIds);

    if (selectedItems.length > 0) {
        // Show action bar
        actionBar.classList.remove("hidden");
        
        // Ensure it's displayed through both CSS and the style attribute
        actionBar.style.display = "flex";
        actionBar.style.opacity = "1";
        actionBar.style.transform = "translateX(-50%)";
        actionBar.style.pointerEvents = "auto";
        
        selectedCount.textContent = `${selectedItems.length} Item(s)`;

        // Create fresh buttons to avoid event listener buildup
        const actionButtons = document.querySelector(".action-buttons");
        if (!actionButtons) {
            console.error("❌ Action buttons container not found!");
            return;
        }
        
        // Clear existing buttons
        actionButtons.innerHTML = '';
        
        // Edit button - only for single selection
        if (selectedItems.length === 1) {
            const newEditButton = document.createElement("button");
            newEditButton.className = "edit-btn";
            newEditButton.innerHTML = `
                <svg width="16" height="16" viewBox="0 0 24 24">
                    <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z" fill="currentColor"/>
                </svg>
                Edit
            `;
            
            // Add click event for editing
            const bookmarkId = selectedItems[0].getAttribute("data-id");
            console.log("🏷️ Selected bookmark ID for editing:", bookmarkId);
            
            newEditButton.onclick = function(e) {
                e.preventDefault();
                e.stopPropagation();
                if (bookmarkId) {
                    editBookmark(bookmarkId);
                }
                return false;
            };
            
            actionButtons.appendChild(newEditButton);
        }

        // Remove button - enable for any selection
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
            removeSelectedBookmarks(selectedIds);
            return false;
        };
        
        actionButtons.appendChild(newRemoveButton);
    } else {
        // Hide action bar when no items selected
        actionBar.classList.add("hidden");
        // Set both CSS and style properties
        actionBar.style.opacity = "0";
        actionBar.style.transform = "translate(-50%, 100px)";
        actionBar.style.pointerEvents = "none";
        
        setTimeout(() => {
            if (!document.querySelectorAll(".bookmark-checkbox:checked").length) {
                actionBar.style.display = "none";
            }
        }, 300);
    }
    
    // Trigger a layout recalculation to ensure CSS transitions work
    void actionBar.offsetWidth;
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

    // Show loading indicator or disable buttons
    const actionBar = document.getElementById("action-bar");
    if (actionBar) {
        actionBar.style.opacity = "0.5";
        actionBar.style.pointerEvents = "none";
    }

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
            
            // Give notification time to display before reload
            setTimeout(() => {
                console.log('✅ Reloading page after successful deletion');
                window.location.reload();
            }, 1000);
        } else {
            throw new Error(data.message || 'Failed to delete bookmarks');
        }
    })
    .catch(error => {
        console.error('❌ Error:', error);
        showNotification('Error deleting bookmarks: ' + error.message, 'error');
        
        // Restore action bar functionality
        if (actionBar) {
            actionBar.style.opacity = "1";
            actionBar.style.pointerEvents = "auto";
        }
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

    // Reset any existing popups first
    cleanupAllPopups();

    // Get popup element
    const popup = document.getElementById("editBookmarkPopup");
    if (!popup) {
        console.error("❌ Edit bookmark popup element not found!");
        showNotification("Error: Popup element not found", "error");
        return;
    }

    try {
        // Fill the form with bookmark data
        document.getElementById("edit_bookmark_id").value = bookmark.bookmark_id;
        document.getElementById("edit_bookmark_description").value = bookmark.description;
        document.getElementById("edit_bookmark_amount").value = bookmark.amount;
        document.getElementById("edit_bookmark_type").value = bookmark.type;
        document.getElementById("edit_bookmark_category").value = bookmark.category;

        // Set a flag in local storage to track that we're editing
        localStorage.setItem('isEditing', 'true');
        localStorage.setItem('editingBookmarkId', bookmark.bookmark_id);

        // Force the popup to be visible
        popup.style.display = "flex";
        
        // Critical: Force browser layout calculation
        void popup.offsetWidth;
        
        // Add the active class to trigger CSS transition
        popup.classList.add("active");

        // Initialize form listeners
        setTimeout(() => {
            initializeEditBookmarkFormListener();
            
            // Initialize category dropdown
            if (typeof setupEditBookmarkCategoryListeners === 'function') {
                setupEditBookmarkCategoryListeners();
            }
        }, 50);

        // Add a click handler to close the popup when clicking outside
        document.body.addEventListener('click', closePopupOnOutsideClick);

        console.log("✅ Popup should be visible now");
    } catch (error) {
        console.error("❌ Error showing popup:", error);
        showNotification('Error showing edit form: ' + error.message, 'error');
        hideEditBookmarkPopup();
    }
}

function hideEditBookmarkPopup() {
    console.log("🔄 Hiding edit bookmark popup");
    
    // Remove outside click handler
    document.body.removeEventListener('click', closePopupOnOutsideClick);
    
    // Clear editing flags from localStorage
    localStorage.removeItem('isEditing');
    localStorage.removeItem('editingBookmarkId');
    
    const popup = document.getElementById("editBookmarkPopup");
    if (!popup) {
        console.error("❌ Edit bookmark popup element not found!");
        return;
    }
    
    // Start the fade-out animation
    popup.classList.remove("active");
    
    // Wait for the transition before hiding completely
    setTimeout(() => {
        popup.style.display = "none";
        
        // Reset form state
        const form = document.getElementById('editBookmarkForm');
        if (form) form.reset();
        
        console.log("✅ Popup hidden successfully");
    }, 300);
}

// Close popup when clicking outside of it
function closePopupOnOutsideClick(event) {
    const popup = document.querySelector('.popup.active .popup-content');
    
    // If we click outside the popup content but the popup is active
    if (popup && !popup.contains(event.target)) {
        hideEditBookmarkPopup();
        // Remove this event listener to prevent multiple bindings
        document.body.removeEventListener('click', closePopupOnOutsideClick);
    }
}

// Cleanup all popups (use when showing a new popup)
function cleanupAllPopups() {
    console.log("🧹 Cleaning up any open popups");
    
    // Hide all popups
    document.querySelectorAll('.popup').forEach(popup => {
        popup.classList.remove('active');
        popup.style.display = 'none';
    });
    
    // Remove any outside click handlers
    document.body.removeEventListener('click', closePopupOnOutsideClick);
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

    // Add a data attribute to track initialization
    newForm.setAttribute('data-initialized', 'true');
    console.log("🔄 Setting up edit bookmark form listener");

    // Track form submission state to prevent multiple submissions
    let isSubmitting = false;

    newForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Prevent double submission
        if (isSubmitting) {
            console.log("⚠️ Form already submitting, ignoring additional submit");
            return;
        }
        
        console.log("🔄 Form submitted");
        
        // Disable form during submission
        isSubmitting = true;
        const submitButton = this.querySelector('button[type="submit"]');
        if (submitButton) {
            submitButton.disabled = true;
            submitButton.textContent = 'Saving...';
        }
        
        const formData = new FormData(this);
        const bookmarkId = document.getElementById('edit_bookmark_id').value;
        
        // Convert FormData to JSON
        const formDataObject = {};
        formData.forEach((value, key) => {
            formDataObject[key] = value;
        });
        
        // Basic form validation
        if (!formDataObject.description?.trim()) {
            showNotification("Description is required", "error");
            resetFormSubmitState();
            return;
        }
        
        if (!formDataObject.amount || isNaN(formDataObject.amount) || Number(formDataObject.amount) <= 0) {
            showNotification("Amount must be a positive number", "error");
            resetFormSubmitState();
            return;
        }
        
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
        .then(response => {
            if (!response.ok) {
                throw new Error(`Server error: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                hideEditBookmarkPopup();
                showNotification(data.message || 'Bookmark updated successfully', 'success');
                
                // Clear editing state
                localStorage.removeItem('isEditing');
                localStorage.removeItem('editingBookmarkId');
                
                // Give notification time to display before reload
                setTimeout(() => {
                    console.log('✅ Reloading page after successful update');
                    window.location.href = window.location.pathname; // Clean reload without query params
                }, 1000);
            } else {
                throw new Error(data.message || 'Failed to update bookmark');
            }
        })
        .catch(error => {
            console.error('❌ Error:', error);
            showNotification('Error updating bookmark: ' + error.message, 'error');
            resetFormSubmitState();
        });
        
        // Helper function to reset form state after errors
        function resetFormSubmitState() {
            isSubmitting = false;
            if (submitButton) {
                submitButton.disabled = false;
                submitButton.textContent = 'Save';
            }
        }
    });
    
    // Add cancel button handler directly
    const cancelButton = newForm.querySelector('button[type="button"]');
    if (cancelButton) {
        // Remove old listeners
        const newCancelButton = cancelButton.cloneNode(true);
        cancelButton.parentNode.replaceChild(newCancelButton, cancelButton);
        
        // Add new click handler
        newCancelButton.addEventListener('click', function(e) {
            e.preventDefault();
            hideEditBookmarkPopup();
        });
    }
    
    console.log("✅ Form listener setup complete");
}

// Attach event listeners to bookmark checkboxes
function attachBookmarkCheckboxListeners() {
    console.log("🔄 Attaching event listeners to bookmark checkboxes...");
    
    // Find all bookmark checkboxes
    const checkboxes = document.querySelectorAll(".bookmark-checkbox:not([data-listener-initialized='true'])");
    console.log(`📋 Found ${checkboxes.length} bookmark checkboxes that need listeners`);
    
    if (checkboxes.length === 0) {
        console.log("ℹ️ No new bookmark checkboxes found to attach listeners to");
        return;
    }
    
    // Process each checkbox
    checkboxes.forEach(checkbox => {
        // Mark as initialized to prevent duplicate listeners
        checkbox.setAttribute('data-listener-initialized', 'true');
        
        // Add single change handler with debounce
        let debounceTimer;
        checkbox.addEventListener("change", function() {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => {
                console.log(`🔄 Checkbox changed for bookmark ID: ${this.dataset.id}`);
                updateBookmarkActionBar();
            }, 10);
        });
    });
    
    // Also handle the "select all" checkbox
    const selectAllCheckbox = document.querySelector(".select-all:not([data-listener-initialized='true'])");
    if (selectAllCheckbox) {
        selectAllCheckbox.setAttribute('data-listener-initialized', 'true');
        
        // Remove the inline onclick to prevent double firing
        selectAllCheckbox.removeAttribute('onclick');
        
        // Add change event handler
        selectAllCheckbox.addEventListener("change", function() {
            const checkboxes = document.querySelectorAll(".bookmark-checkbox");
            console.log(`🔄 Select all toggled, setting ${checkboxes.length} checkboxes to ${this.checked}`);
            
            // Set all checkboxes to match
            checkboxes.forEach((checkbox) => {
                checkbox.checked = this.checked;
            });
            
            // Update the action bar once for all changes
            updateBookmarkActionBar();
        });
    }
    
    console.log("✅ Event listeners attached successfully");
}

// Initialize bookmark page
function initializeBookmarkPage() {
    console.log("🚀 Initializing bookmark page...");
    
    // Attach event listeners to checkboxes
    attachBookmarkCheckboxListeners();
    
    // Make rows clickable to toggle checkbox
    makeRowsClickableForCheckboxes();
    
    // Initialize filter functionality
    const filterButtons = document.querySelectorAll('.dropdown-content a');
    filterButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const filter = this.getAttribute('data-filter');
            window.location = `/bookmark?filter=${filter}`;
        });
    });
    
    // Initial update of action bar
    updateBookmarkActionBar();

    // Check if we were editing when the page refreshed
    checkForRestoredEditing();
    
    console.log("✅ Bookmark page initialization complete");
}

// Make the entire row clickable to toggle the checkbox
function makeRowsClickableForCheckboxes() {
    const rows = document.querySelectorAll('#transactionTable tr:not(.head-table)');
    console.log(`📋 Setting up ${rows.length} clickable rows`);
    
    rows.forEach(row => {
        // Skip if already processed
        if (row.hasAttribute('data-row-click-listener')) {
            return;
        }
        
        row.setAttribute('data-row-click-listener', 'true');
        
        row.addEventListener('click', function(e) {
            // Don't toggle if clicking on a checkbox directly or a button
            if (e.target.type === 'checkbox' || 
                e.target.tagName === 'BUTTON' || 
                e.target.closest('button') ||
                e.target.closest('.bookmark-checkbox')) {
                return;
            }
            
            // Add a flag to prevent recursive toggle
            if (this.hasAttribute('data-processing-click')) {
                return;
            }
            
            // Set processing flag
            this.setAttribute('data-processing-click', 'true');
            
            // Find the checkbox in this row
            const checkbox = this.querySelector('.bookmark-checkbox');
            if (checkbox) {
                // Toggle the checkbox
                checkbox.checked = !checkbox.checked;
                
                // Manually trigger actionBar update instead of relying on events
                updateBookmarkActionBar();
                
                console.log(`🔄 Row clicked, toggled checkbox: ${checkbox.dataset.id} to ${checkbox.checked}`);
            }
            
            // Remove processing flag after a short delay
            setTimeout(() => {
                this.removeAttribute('data-processing-click');
            }, 50);
        });
    });
    
    console.log("✅ Row click listeners attached successfully");
}

// Check if we need to restore an edit operation after a page refresh
function checkForRestoredEditing() {
    const isEditing = localStorage.getItem('isEditing');
    const bookmarkId = localStorage.getItem('editingBookmarkId');
    
    if (isEditing === 'true' && bookmarkId) {
        console.log("🔄 Detected unfinished editing session, restoring...");
        
        // Slight delay to ensure the DOM is fully ready
        setTimeout(() => {
            // First select the checkbox for this bookmark
            const checkbox = document.querySelector(`.bookmark-checkbox[data-id="${bookmarkId}"]`);
            if (checkbox) {
                checkbox.checked = true;
                updateBookmarkActionBar();
                
                // Now fetch the bookmark data and show the popup
                editBookmark(bookmarkId);
            } else {
                console.warn("⚠️ Bookmark checkbox not found, can't restore editing");
                // Clear the editing state since we couldn't restore it
                localStorage.removeItem('isEditing');
                localStorage.removeItem('editingBookmarkId');
            }
        }, 300);
    }
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

    /* Improved checkbox styling */
    .bookmark-checkbox,
    .select-all {
        width: 20px;
        height: 20px;
        cursor: pointer;
        position: relative;
        appearance: none;
        -webkit-appearance: none;
        background-color: #fff;
        border: 2px solid #71D881;
        border-radius: 4px;
        box-sizing: border-box;
        outline: none;
        transition: all 0.2s ease;
    }

    .bookmark-checkbox:checked,
    .select-all:checked {
        background-color: #71D881;
        border-color: #71D881;
        position: relative;
    }

    .bookmark-checkbox:checked::after,
    .select-all:checked::after {
        content: "";
        position: absolute;
        top: 2px;
        left: 6px;
        width: 5px;
        height: 10px;
        border: solid white;
        border-width: 0 2px 2px 0;
        transform: rotate(45deg);
    }

    /* Ensure rows are clickable and show hover state */
    #transactionTable tr:not(.head-table) {
        cursor: pointer;
        transition: background-color 0.2s ease;
    }

    #transactionTable tr:not(.head-table):hover {
        background-color: rgba(113, 216, 129, 0.05);
    }

    /* Highlight selected rows */
    #transactionTable tr:has(.bookmark-checkbox:checked) {
        background-color: rgba(113, 216, 129, 0.1);
    }

    /* Fix for action bar visibility */
    .action-bar {
        visibility: visible;
        transition: opacity 0.3s ease, transform 0.3s ease, visibility 0s;
    }
    
    .action-bar.hidden {
        visibility: hidden;
        transition: opacity 0.3s ease, transform 0.3s ease, visibility 0s 0.3s;
    }
</style>
@endpush


