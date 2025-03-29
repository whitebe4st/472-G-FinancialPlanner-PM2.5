window.showLoading = function() {
    const loader = document.getElementById('loadingAnimation');
    if (loader) loader.style.display = 'block';
}

window.hideLoading = function() {
    const loader = document.getElementById('loadingAnimation');
    if (loader) loader.style.display = 'none';
}

document.addEventListener("turbolinks:before-visit", showLoading);
document.addEventListener("turbolinks:load", hideLoading);
document.addEventListener("turbolinks:before-cache", hideLoading); 