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

// Dark mode toggle functionality
const darkModeToggle = document.getElementById('darkModeToggle');
const htmlElement = document.documentElement;

// Check for saved user preference
const savedTheme = localStorage.getItem('theme') || 'light';
htmlElement.setAttribute('data-theme', savedTheme);

darkModeToggle.addEventListener('click', () => {
    const currentTheme = htmlElement.getAttribute('data-theme');
    const newTheme = currentTheme === 'light' ? 'dark' : 'light';
    
    htmlElement.setAttribute('data-theme', newTheme);
    localStorage.setItem('theme', newTheme);
});