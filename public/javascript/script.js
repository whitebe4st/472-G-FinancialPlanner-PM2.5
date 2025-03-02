function toggleAllCheckboxes() {
    const selectAllCheckbox = document.getElementById("selectAll");
    const rowCheckboxes = document.querySelectorAll(".row-checkbox");

    if (!selectAllCheckbox || rowCheckboxes.length === 0) {
        console.error("Select All checkbox or row checkboxes not found!");
        return;
    }

    rowCheckboxes.forEach((checkbox) => {
        checkbox.checked = selectAllCheckbox.checked;
    });

    updateActionBar(); // อัปเดต Action Bar ทันที
}

function showAddTransactionPopup() {
    const popup = document.getElementById("addTransactionPopup");
    popup.style.display = "flex";
    // Trigger reflow to ensure transition works
    popup.offsetHeight;
    popup.classList.add("active");
}

function hideAddTransactionPopup() {
    const popup = document.getElementById("addTransactionPopup");
    popup.classList.remove("active");
    // Wait for animation to finish before hiding
    setTimeout(() => {
        popup.style.display = "none";
    }, 300);
}

let categories = []; // Will store all user categories

function loadCategories() {
    fetch("/transactions/categories")
        .then((response) => response.json())
        .then((data) => {
            categories = data;
            const dropdown = document.getElementById("categoryDropdown");
            updateCategoryDropdown("");
        });
}

function updateCategoryDropdown(filter) {
    const dropdown = document.getElementById("categoryDropdown");
    dropdown.innerHTML = "";

    const filteredCategories = categories.filter((cat) =>
        cat.toLowerCase().includes(filter.toLowerCase())
    );

    if (filteredCategories.length > 0) {
        filteredCategories.forEach((category) => {
            const div = document.createElement("div");
            div.className = "category-option";
            div.textContent = category;
            div.onclick = () => selectCategory(category);
            dropdown.appendChild(div);
        });
    } else if (filter) {
        const div = document.createElement("div");
        div.className = "no-categories";
        div.textContent = `Press Enter to add "${filter}" as new category`;
        dropdown.appendChild(div);
    } else {
        const div = document.createElement("div");
        div.className = "no-categories";
        div.textContent = "No categories found";
        dropdown.appendChild(div);
    }
}

function filterCategories(value) {
    const dropdown = document.getElementById("categoryDropdown");
    dropdown.classList.add("show");
    updateCategoryDropdown(value);
}

function selectCategory(category) {
    document.getElementById("category").value = category;
    document.getElementById("categoryDropdown").classList.remove("show");
}

// Handle clicking outside to close dropdown
document.addEventListener("click", function (e) {
    const dropdown = document.getElementById("categoryDropdown");
    const input = document.getElementById("category");
    if (!input.contains(e.target) && !dropdown.contains(e.target)) {
        dropdown.classList.remove("show");
    }
});

// Handle category input keyboard events
document.getElementById("category").addEventListener("keydown", function (e) {
    const dropdown = document.getElementById("categoryDropdown");
    if (e.key === "Enter" && !categories.includes(this.value)) {
        e.preventDefault();
        // Allow new category
        dropdown.classList.remove("show");
    }
});

// Focus on category input
document.getElementById("category").addEventListener("focus", function () {
    document.getElementById("categoryDropdown").classList.add("show");
    updateCategoryDropdown(this.value);
});

document.addEventListener("DOMContentLoaded", function () {
    // Load categories when page loads
    loadCategories();

    // Handle form submission
    document
        .getElementById("transactionForm")
        .addEventListener("submit", function (e) {
            e.preventDefault();
            console.log("Form submitted"); // Debug log

            const formData = new FormData(this);
            const formDataObject = Object.fromEntries(formData);
            console.log("Form data:", formDataObject); // Debug log

            fetch("/transactions", {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": document.querySelector(
                        'meta[name="csrf-token"]'
                    ).content,
                    Accept: "application/json",
                    "Content-Type": "application/json",
                },
                body: JSON.stringify(formDataObject),
            })
                .then((response) => {
                    console.log("Response:", response); // Debug log
                    return response.json();
                })
                .then((data) => {
                    console.log("Data:", data); // Debug log
                    if (data.success) {
                        hideAddTransactionPopup();
                        window.location.reload();
                    } else {
                        alert("Error: " + data.message);
                    }
                })
                .catch((error) => {
                    console.error("Error:", error);
                    alert("Failed to add transaction");
                });
        });

    updateDateTime();
});

// Dark mode toggle functionality
const darkModeToggle = document.getElementById("darkModeToggle");
const htmlElement = document.documentElement;

// Check for saved user preference
const savedTheme = localStorage.getItem("theme") || "light";
htmlElement.setAttribute("data-theme", savedTheme);

darkModeToggle.addEventListener("click", () => {
    const currentTheme = htmlElement.getAttribute("data-theme");
    const newTheme = currentTheme === "light" ? "dark" : "light";

    htmlElement.setAttribute("data-theme", newTheme);
    localStorage.setItem("theme", newTheme);
});

function showBookmarkSelectionModal() {
    const addTransactionPopup = document.getElementById("addTransactionPopup");
    const bookmarkModal = document.getElementById("bookmarkSelectionModal");

    if (!addTransactionPopup || !bookmarkModal) {
        console.error("Modal elements not found");
        return;
    }

    // Hide transaction popup
    addTransactionPopup.style.display = "none";

    // Show and setup bookmark modal
    bookmarkModal.style.display = "flex";
    bookmarkModal.classList.add("active");
    loadBookmarkedTransactions();
}

function hideBookmarkSelectionModal() {
    const bookmarkModal = document.getElementById("bookmarkSelectionModal");
    const addTransactionPopup = document.getElementById("addTransactionPopup");

    if (!bookmarkModal || !addTransactionPopup) {
        console.error("Modal elements not found");
        return;
    }

    // Hide and reset bookmark modal
    bookmarkModal.style.display = "none";
    bookmarkModal.classList.remove("active");

    // Show transaction popup
    addTransactionPopup.style.display = "flex";
}

function loadBookmarkedTransactions() {
    const tbody = document.getElementById("bookmarkTableBody");
    if (!tbody) {
        console.error("Bookmark table body not found");
        return;
    }

    tbody.innerHTML =
        '<tr><td colspan="5" class="loading">Loading bookmarks...</td></tr>';

    fetch("/api/bookmarks")
        .then((response) => {
            if (!response.ok) {
                throw new Error("Network response was not ok");
            }
            return response.json();
        })
        .then((bookmarks) => {
            if (bookmarks.length === 0) {
                tbody.innerHTML =
                    '<tr><td colspan="5" class="text-center">No bookmarks found</td></tr>';
                return;
            }

            tbody.innerHTML = bookmarks
                .map(
                    (bookmark) => `
                <tr>
                    <td><input type="checkbox" class="bookmark-checkbox" value="${
                        bookmark.bookmark_id
                    }"></td>
                    <td>${bookmark.description}</td>
                    <td>$${parseFloat(bookmark.amount).toFixed(2)}</td>
                    <td>
                        <span class="${
                            bookmark.type === "income" ? "income" : "expense"
                        }">
                            ${bookmark.type === "income" ? "Inc." : "Exp."}
                        </span>
                    </td>
                    <td>${bookmark.category}</td>
                </tr>
            `
                )
                .join("");
        })
        .catch((error) => {
            console.error("Error loading bookmarks:", error);
            tbody.innerHTML =
                '<tr><td colspan="5" class="error">Failed to load bookmarks</td></tr>';
        });
}

function toggleAllBookmarks() {
    const mainCheckbox = document.getElementById("selectAllBookmarks");
    const checkboxes = document.querySelectorAll(".bookmark-checkbox");
    checkboxes.forEach((checkbox) => (checkbox.checked = mainCheckbox.checked));
}

function addSelectedBookmarks() {
    const checkboxes = document.querySelectorAll(".bookmark-checkbox:checked");
    const selectedIds = Array.from(checkboxes).map((cb) => cb.value);

    if (selectedIds.length === 0) {
        alert("Please select at least one bookmark");
        return;
    }

    const today = new Date().toISOString().split("T")[0];

    fetch("/api/add-bookmarked-transactions", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')
                .content,
            Accept: "application/json",
        },
        body: JSON.stringify({
            bookmark_ids: selectedIds,
            transaction_date: today,
        }),
    })
        .then((response) => response.json())
        .then((data) => {
            if (data.success) {
                hideBookmarkSelectionModal();
                window.location.reload();
            } else {
                alert(data.message || "Failed to add transactions");
            }
        })
        .catch((error) => {
            console.error("Error:", error);
            alert("Failed to add transactions");
        });
}

document.addEventListener("turbolinks:load", function () {
    const clockElement = document.getElementById("clock");
    const calendarElement = document.getElementById("calendar");

    if (!clockElement || !calendarElement) return;

    function updateDisplay(time) {
        clockElement.textContent = time.toLocaleTimeString("en-US", {
            hour: "2-digit",
            minute: "2-digit",
        });

        calendarElement.textContent = time.toLocaleDateString("en-US", {
            weekday: "short",
            month: "short",
            day: "numeric",
        });
    }

    // Add this page's display as an observer
    window.Clock.addObserver(updateDisplay);

    // Cleanup when leaving page
    document.addEventListener(
        "turbolinks:before-cache",
        function () {
            window.Clock.removeObserver(updateDisplay);
        },
        { once: true }
    );
});

function updateActionBar() {
    console.log("🔄 Running updateActionBar...");

    const actionBar = document.getElementById("action-bar");
    const selectedCount = document.getElementById("selected-count");
    const editButton = document.querySelector(".edit-btn");

    const selectedItems = document.querySelectorAll(".row-checkbox:checked");

    console.log("✅ Selected Items:", selectedItems.length);

    if (selectedItems.length > 0) {
        actionBar.classList.remove("hidden");
        actionBar.style.display = "flex"; // แสดง Action Bar
        selectedCount.textContent = `${selectedItems.length} Item(s)`;

        if (selectedItems.length > 1) {
            editButton.classList.add("hidden");
        } else {
            editButton.classList.remove("hidden");
        }
    } else {
        actionBar.classList.add("hidden");
        actionBar.style.display = "none";
    }
}

// ✅ ใช้ Event Delegation ให้ Checkbox ถูกจับทุกตัว แม้โหลดใหม่
document.body.addEventListener("change", function (event) {
    if (event.target.classList.contains("row-checkbox")) {
        console.log("🆗 Checkbox Clicked - data-id:", event.target.getAttribute("data-id"));
        updateActionBar();
    }
});

// ✅ ใช้ MutationObserver ตรวจจับเมื่อ Table ถูกโหลดใหม่
const observer = new MutationObserver(() => {
    console.log("🔄 Table Updated! Re-attaching checkbox event listeners...");
    updateActionBar();
});

observer.observe(document.getElementById('transactionTable'), {
    childList: true,
    subtree: true
});

// ✅ อัปเดต Action Bar ตอนโหลดหน้าเว็บ
document.addEventListener("DOMContentLoaded", function () {
    console.log("🚀 Initializing Action Bar...");
    loadTransactions();
    updateActionBar();
});



document.addEventListener("DOMContentLoaded", function () {
    const editButton = document.querySelector(".edit-btn");

    if (!editButton) {
        console.error("❌ Edit button not found!");
        return;
    }

    editButton.addEventListener("click", function () {
        let selectedCheckbox = document.querySelector(".row-checkbox:checked");

        if (!selectedCheckbox) {
            alert("Please select a transaction to edit.");
            console.error("❌ No checkbox selected!");
            return;
        }

        let transactionId = selectedCheckbox.getAttribute("data-id");
        console.log("🆔 Fetching transaction ID:", transactionId); // ✅ Debug

        if (!transactionId || transactionId === "undefined" || transactionId === "null") {
            alert("Invalid transaction ID!");
            console.error("❌ Invalid transaction ID!");
            return;
        }

        fetch(`/transactions/${transactionId}`)
            .then(response => {
                console.log("🔄 Response status:", response.status);
                if (!response.ok) {
                    throw new Error(`HTTP error! Status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log("📄 Transaction data received:", data);
                if (data.success) {
                    showEditTransactionPopup(data.transaction);
                } else {
                    alert("Transaction not found.");
                }
            })
            .catch(error => {
                console.error("❌ Error fetching transaction:", error);
                alert("Failed to load transaction data.");
            });
    });
});




// ✅ ฟังก์ชันแสดง Edit Popup
function showEditTransactionPopup(transaction) {
    const popup = document.getElementById("editTransactionPopup");

    if (!popup) {
        console.error("Edit transaction popup not found!");
        return;
    }

    console.log("Editing Transaction:", transaction); // ✅ Debug Log

    // ✅ เติมค่าลงในฟอร์มแก้ไข
    document.getElementById("edit_id").value = transaction.transaction_id;
    document.getElementById("edit_description").value = transaction.description;
    document.getElementById("edit_amount").value = transaction.amount;
    document.getElementById("edit_type").value = transaction.type;
    document.getElementById("edit_category").value = transaction.category;
    document.getElementById("edit_transaction_date").value =
        transaction.transaction_date.split("T")[0];

    popup.style.display = "flex";
    popup.offsetHeight; // ✅ Trigger reflow เพื่อให้ transition ทำงาน
    popup.classList.add("active");
}

    

//  ฟังก์ชันปิด Edit Popup
function hideEditTransactionPopup() {
    const popup = document.getElementById("editTransactionPopup");
    popup.classList.remove("active");
    setTimeout(() => {
        popup.style.display = "none";
    }, 300);
}

//  กด Save และส่ง `PUT` request เพื่ออัปเดตข้อมูล
document
    .getElementById("editTransactionForm")
    .addEventListener("submit", function (e) {
        e.preventDefault();

        const formData = new FormData(this);
        const formDataObject = Object.fromEntries(formData);
        const transactionId = formDataObject.id; //  ดึง ID ของ Transaction

        fetch(`/transactions/${transactionId}`, {
            method: "PUT",
            headers: {
                "X-CSRF-TOKEN": document.querySelector(
                    "meta[name='csrf-token']"
                ).content,
                Accept: "application/json",
                "Content-Type": "application/json",
            },
            body: JSON.stringify(formDataObject),
        })
            .then((response) => response.json())
            .then((data) => {
                if (data.success) {
                    hideEditTransactionPopup();
                    window.location.reload();
                } else {
                    alert("Error: " + data.message);
                }
            })
            .catch((error) => {
                console.error("Error:", error);
                alert("Failed to update transaction");
            });
    });
