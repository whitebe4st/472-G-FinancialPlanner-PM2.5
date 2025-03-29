document.addEventListener('DOMContentLoaded', function() {
    console.log('Script loaded');

    // Function to attach event listeners to bookmark icons
    function attachBookmarkListeners() {
        console.log('Attaching listeners');
        const bookmarkIcons = document.querySelectorAll('table td svg');
        console.log('Found bookmark icons:', bookmarkIcons.length);

        bookmarkIcons.forEach(icon => {
            // Remove existing listener to prevent duplicates
            icon.removeEventListener('click', bookmarkHandler);
            // Add new listener
            icon.addEventListener('click', bookmarkHandler);
        });
    }

    // Bookmark click handler function
    function bookmarkHandler(e) {
        console.log('Icon clicked');
        e.preventDefault();

        const row = this.closest('tr');
        console.log('Found row:', row);

        const data = {
            description: row.querySelector('td:nth-child(3)').textContent.trim(),
            date: row.querySelector('td:nth-child(4)').textContent.trim(),
            amount: row.querySelector('td:nth-child(5)').textContent.replace(/[^0-9.-]+/g,""),
            type: row.querySelector('td:nth-child(6)').getAttribute('data-type'),
            category: row.querySelector('td:nth-child(3)').getAttribute('data-category'),
        };

        console.log('Data to send:', data);

        fetch('/bookmark-transaction', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify(data)
        })
        .then(response => {
            console.log('Response received:', response.status);
            return response.json();
        })
        .then(data => {
            console.log('Response data:', data);
            if (data.success) {
                this.classList.add('active');
                showNotification('Transaction bookmarked successfully', 'success');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Failed to bookmark transaction', 'error');
        });
    }

    // Add click event listener to the table container (event delegation)
    document.addEventListener('click', function(e) {
        const target = e.target;
        
        // Check if clicked element is SVG path or SVG itself
        if (target.tagName.toLowerCase() === 'path' || target.tagName.toLowerCase() === 'svg') {
            const svg = target.tagName.toLowerCase() === 'path' ? target.parentElement : target;
            const row = svg.closest('tr');
            
            if (row) {
                console.log('Bookmark icon clicked');
                const isBookmarked = svg.classList.contains('active');
                
                const data = {
                    description: row.querySelector('td:nth-child(2)').textContent.trim(),
                    date: row.querySelector('td:nth-child(3)').textContent.trim(),
                    amount: row.querySelector('td:nth-child(4)').textContent.replace(/[^0-9.-]+/g,""),
                    type: row.querySelector('td:nth-child(5)').getAttribute('data-type'),
                    category: row.querySelector('td:nth-child(2)').getAttribute('data-category'),
                    action: isBookmarked ? 'unbookmark' : 'bookmark'
                };

                console.log('Data to send:', data);

                fetch('/bookmark-transaction', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(data)
                })
                .then(response => {
                    console.log('Response status:', response.status);
                    return response.json();
                })
                .then(data => {
                    console.log('Response data:', data);
                    if (data.success) {
                        if (isBookmarked) {
                            svg.classList.remove('active');
                            showNotification('Transaction unbookmarked', 'success');
                        } else {
                            svg.classList.add('active');
                            showNotification('Transaction bookmarked successfully', 'success');
                        }
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('Failed to update bookmark', 'error');
                });
            }
        }
    });

    // Initial attachment of listeners
    attachBookmarkListeners();

    // Notification function
    function showNotification(message, type) {
        const notification = document.createElement('div');
        notification.className = `notification ${type}`;
        notification.textContent = message;
        document.body.appendChild(notification);

        // Remove notification after 3 seconds
        setTimeout(() => {
            notification.remove();
        }, 3000);
    }
}); 