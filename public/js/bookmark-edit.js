// Variables to store categories and the current request state
let bookmarkCategories = [];
let currentBookmarkRequest = null;
let editBookmarkObserver = null;

// Listen for both regular document ready and Turbolinks page loads
document.addEventListener('DOMContentLoaded', initializeBookmarkEdit);
document.addEventListener('turbolinks:load', initializeBookmarkEdit);

// Initialize the bookmark edit functionality
function initializeBookmarkEdit() {
    console.log('🚀 Initializing bookmark-edit.js');
    
    // Load categories
    loadCategoriesForBookmarkEdit();
    
    // Set up event listeners for the category field
    setupEditBookmarkCategoryListeners();
    
    // Set up a mutation observer to watch for popup visibility
    setupPopupObserver();
    
    // Clean up any stale state from previous page loads
    window.addEventListener('beforeunload', function() {
        if (editBookmarkObserver) {
            editBookmarkObserver.disconnect();
        }
    });
}

// Fetch categories from the server
function loadCategoriesForBookmarkEdit() {
    // Check if we already have categories loaded
    if (bookmarkCategories.length > 0) {
        console.log('📋 Using cached categories:', bookmarkCategories);
        return;
    }
    
    fetch('/transactions/categories')
        .then(response => response.json())
        .then(data => {
            bookmarkCategories = data;
            console.log('📋 Loaded categories:', bookmarkCategories);
        })
        .catch(error => {
            console.error('❌ Error loading categories:', error);
        });
}

// Set up event listeners for category dropdown
function setupEditBookmarkCategoryListeners() {
    // Target both edit forms by using a class selector or a more general approach
    const categoryInputs = document.querySelectorAll('#edit_bookmark_category, [name="category"]');
    
    if (categoryInputs.length === 0) {
        console.log('⚠️ Category inputs not found - might be loaded later');
        return;
    }
    
    console.log(`✅ Found ${categoryInputs.length} category inputs, attaching listeners`);
    
    categoryInputs.forEach(input => {
        // Skip if already processed
        if (input.hasAttribute('data-category-listener')) {
            return;
        }
        
        // Show dropdown on focus
        input.addEventListener('focus', function() {
            const dropdownId = this.id === 'edit_bookmark_category' ? 
                'editBookmarkCategoryDropdown' : 'categoryDropdown';
            const dropdown = document.getElementById(dropdownId);
            
            if (dropdown) {
                dropdown.classList.add('show');
                updateEditBookmarkCategoryDropdown(this.value, dropdown);
            }
        });
        
        // Filter categories as user types
        input.addEventListener('input', function() {
            const dropdownId = this.id === 'edit_bookmark_category' ? 
                'editBookmarkCategoryDropdown' : 'categoryDropdown';
            const dropdown = document.getElementById(dropdownId);
            
            if (dropdown) {
                dropdown.classList.add('show');
                updateEditBookmarkCategoryDropdown(this.value, dropdown);
            }
        });
        
        // Mark as processed
        input.setAttribute('data-category-listener', 'true');
    });
    
    // Global click handler to close dropdowns
    if (!document.hasAttribute('data-category-click-handler')) {
        document.addEventListener('click', function(e) {
            const dropdowns = document.querySelectorAll('.category-dropdown');
            dropdowns.forEach(dropdown => {
                const inputId = dropdown.id === 'editBookmarkCategoryDropdown' ? 
                    'edit_bookmark_category' : 'category';
                const input = document.getElementById(inputId);
                
                if (input && !input.contains(e.target) && !dropdown.contains(e.target)) {
                    dropdown.classList.remove('show');
                }
            });
        });
        document.setAttribute('data-category-click-handler', 'true');
    }
}

// Update the category dropdown content
function updateEditBookmarkCategoryDropdown(filter, dropdown) {
    if (!dropdown) return;
    
    dropdown.innerHTML = '';
    
    const filteredCategories = bookmarkCategories.filter(cat => 
        cat.toLowerCase().includes(filter.toLowerCase())
    );
    
    if (filteredCategories.length > 0) {
        filteredCategories.forEach(category => {
            const div = document.createElement('div');
            div.className = 'category-option';
            div.textContent = category;
            div.onclick = () => {
                // Find the associated input
                let input;
                if (dropdown.id === 'editBookmarkCategoryDropdown') {
                    input = document.getElementById('edit_bookmark_category');
                } else {
                    input = document.getElementById('category');
                }
                
                if (input) {
                    input.value = category;
                    dropdown.classList.remove('show');
                }
            };
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

// Populate Edit Bookmark Form
function populateEditBookmarkForm(bookmark) {
    try {
        // Fill the form with bookmark data
        document.getElementById("edit_bookmark_id").value = bookmark.bookmark_id;
        document.getElementById("edit_bookmark_description").value = bookmark.description;
        document.getElementById("edit_bookmark_amount").value = bookmark.amount;
        document.getElementById("edit_bookmark_type").value = bookmark.type;
        document.getElementById("edit_bookmark_category").value = bookmark.category;
        
        console.log("✅ Form populated with bookmark data");
    } catch (error) {
        console.error("❌ Error populating form:", error);
        showNotification('Error populating form: ' + error.message, 'error');
    }
}

// Form validation
function validateBookmarkForm(formData) {
    const errors = [];
    
    if (!formData.description || formData.description.trim() === '') {
        errors.push('Description is required');
    }
    
    if (!formData.amount || isNaN(formData.amount) || parseFloat(formData.amount) <= 0) {
        errors.push('A valid amount greater than 0 is required');
    }
    
    if (!formData.type || !['income', 'expense'].includes(formData.type)) {
        errors.push('Type must be either income or expense');
    }
    
    if (!formData.category || formData.category.trim() === '') {
        errors.push('Category is required');
    }
    
    return errors;
}

// Setup a MutationObserver to detect when the popup becomes visible
function setupPopupObserver() {
    // Disconnect any existing observer
    if (editBookmarkObserver) {
        editBookmarkObserver.disconnect();
    }
    
    // We'll check for visibility changes on the popup
    const popup = document.getElementById('editBookmarkPopup');
    if (!popup) {
        console.log('⚠️ Edit bookmark popup not found in DOM yet');
        return;
    }
    
    console.log('🔍 Setting up observer for edit bookmark popup');
    
    // Create new observer to monitor style and class changes
    editBookmarkObserver = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            // When popup becomes visible
            if ((mutation.attributeName === 'style' && 
                popup.style.display === 'flex') ||
                (mutation.attributeName === 'class' && 
                popup.classList.contains('active'))) {
                
                console.log('👁️ Popup display detected, reinitializing fields');
                
                // Allow time for the popup content to be completely rendered
                setTimeout(() => {
                    setupEditBookmarkCategoryListeners();
                }, 100);
            }
        });
    });
    
    // Configure and start the observer
    editBookmarkObserver.observe(popup, { 
        attributes: true, 
        attributeFilter: ['style', 'class'] 
    });
    
    console.log('✅ Popup observer setup complete');
} 