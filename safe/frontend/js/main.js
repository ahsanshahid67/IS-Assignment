let currentUser = null;
let csrfToken = '';

async function syncSession() {
    try {
        const res = await fetch('../backend/api/init.php');
        const data = await res.json();
        currentUser = data.user;
        csrfToken = data.csrf_token || '';
        updateNavAuth();
    } catch (e) {
        console.error("Session sync error:", e);
    }
}

function updateNavAuth() {
    const authContainer = document.getElementById('nav-auth-container');
    if (!authContainer) return;

    if (currentUser) {
        authContainer.innerHTML = `
            <div class="user-pill">
                <span class="status-dot"></span>
                <span><strong>${escapeHtml(currentUser.username)}</strong></span>
            </div>
            <a href="login.html" class="nav-link">Account</a>
            <button onclick="handleLogout()" class="btn btn-secondary btn-sm">Sign Out</button>
        `;
    } else {
        authContainer.innerHTML = `
            <a href="login.html" class="btn btn-primary btn-sm">Sign In</a>
        `;
    }
}

async function handleLogout() {
    try {
        await fetch('../backend/api/logout.php');
        currentUser = null;
        csrfToken = '';
        window.location.reload();
    } catch (e) {
        alert("Logout failed");
    }
}

function escapeHtml(str) {
    if (!str) return '';
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

document.addEventListener('DOMContentLoaded', () => {
    syncSession();
});
