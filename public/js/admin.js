function showSection(id, el) {
    document.querySelectorAll('.content-section').forEach(s => s.classList.remove('active'));
    // If using sections in the same page
    const section = document.getElementById(id);
    if(section) {
        section.classList.add('active');
    }
    
    // Sidebar active state
    document.querySelectorAll('.menu-item').forEach(m => m.classList.remove('active'));
    if(el) {
        el.classList.add('active');
    }
}

function openModal(id) { 
    const modal = document.getElementById(id);
    if(modal) modal.classList.add('active'); 
}

function closeModal(id) { 
    const modal = document.getElementById(id);
    if(modal) modal.classList.remove('active'); 
}


function toggleTheme() {
    const html = document.documentElement;
    const currentTheme = html.getAttribute('data-theme');
    const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
    
    html.setAttribute('data-theme', newTheme);
    localStorage.setItem('theme', newTheme);
    
    const icon = document.getElementById('theme-icon');
    if(icon) {
        icon.className = newTheme === 'dark' ? 'fa-solid fa-sun' : 'fa-solid fa-moon';
    }
}

// Initialize Theme
document.addEventListener('DOMContentLoaded', () => {
    const savedTheme = localStorage.getItem('theme') || 'light';
    document.documentElement.setAttribute('data-theme', savedTheme);
    
    const icon = document.getElementById('theme-icon');
    if(icon) {
        icon.className = savedTheme === 'dark' ? 'fa-solid fa-sun' : 'fa-solid fa-moon';
    }
});

window.onclick = function(event) {
    if (event.target.classList.contains('modal-overlay')) {
        event.target.classList.remove('active');
        // Close specific modals
        const modals = document.querySelectorAll('.modal-overlay');
        modals.forEach(m => m.classList.remove('active'));
    }
}
