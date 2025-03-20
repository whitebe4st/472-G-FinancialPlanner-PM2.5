// Variables to store categories and the current request state
let bookmarkCategories = [];
let currentBookmarkRequest = null;

// Initialize the bookmark edit page functionality
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Initializing bookmark-edit.js');
    
    // Load categories for the edit form when page loads
    loadCategoriesForBookmarkEdit();
    
    // Setup event listeners for the edit bookmark category field
    setupEditBookmarkCategoryListeners();
});

// Fetch categories from the server
function loadCategoriesForBookmarkEdit() {
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

// Set up listeners for category dropdown
function setupEditBookmarkCategoryListeners() {
    const categoryInput = document.getElementById('edit_bookmark_category');
    
    if (!categoryInput) {
        console.log('⚠️ Category input not found - might be loaded later');
        return;
    }
    
    // Show dropdown on focus
    categoryInput.addEventListener('focus', function() {
        const dropdown = document.getElementById('editBookmarkCategoryDropdown');
        if (dropdown) {
            dropdown.classList.add('show');
            updateEditBookmarkCategoryDropdown(this.value);
        }
    });
    
    // Filter categories as user types
    categoryInput.addEventListener('input', function() {
        const dropdown = document.getElementById('editBookmarkCategoryDropdown');
        if (dropdown) {
            dropdown.classList.add('show');
            updateEditBookmarkCategoryDropdown(this.value);
        }
    });
    
    // Handle keyboard events (Enter key)
    categoryInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && !bookmarkCategories.includes(this.value)) {
            e.preventDefault();
            const dropdown = document.getElementById('editBookmarkCategoryDropdown');
            if (dropdown) {
                dropdown.classList.remove('show');
            }
        }
    });
    
    // Hide dropdown when clicking outside
    document.addEventListener('click', function(e) {
        const dropdown = document.getElementById('editBookmarkCategoryDropdown');
        if (!dropdown) return;
        
        const input = document.getElementById('edit_bookmark_category');
        if (!input) return;
        
        if (!input.contains(e.target) && !dropdown.contains(e.target)) {
            dropdown.classList.remove('show');
        }
    });
}

// Update the category dropdown content based on filter
function updateEditBookmarkCategoryDropdown(filter) {
    const dropdown = document.getElementById('editBookmarkCategoryDropdown');
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
            div.onclick = () => selectEditBookmarkCategory(category);
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

// Select a category from the dropdown
function selectEditBookmarkCategory(category) {
    const input = document.getElementById('edit_bookmark_category');
    const dropdown = document.getElementById('editBookmarkCategoryDropdown');
    
    if (input) input.value = category;
    if (dropdown) dropdown.classList.remove('show');
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