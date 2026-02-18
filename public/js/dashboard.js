// --- Data ---
const notificationsData = [
    { title: 'Booking Confirmed', text: 'Toyota Camry booking confirmed from Oct 10 to Oct 12.', time: '1 hour ago', read: false, icon: 'fa-check', color: '#2e7d32' },
    { title: 'Reminder', text: 'Vehicle pickup in 30 minutes.', time: '2 hours ago', read: false, icon: 'fa-clock', color: '#ef6c00' },
    { title: 'Booking Ended', text: 'Hyundai Elantra booking ended. Please follow up.', time: 'Yesterday', read: true, icon: 'fa-circle-exclamation', color: '#c62828' }
];

function renderNotifications() {
    const list = document.getElementById('notifications-list');
    if(!list) return;
    list.innerHTML = notificationsData.map(n => `
        <div class="notif-item ${!n.read ? 'unread' : ''}">
            <div class="notif-icon-box" style="background:${n.color}"><i class="fa-solid ${n.icon}"></i></div>
            <div class="notif-content">
                <h4>${n.title}</h4>
                <p>${n.text}</p>
                <span class="notif-time">${n.time}</span>
            </div>
        </div>
    `).join('');
}

document.addEventListener('DOMContentLoaded', () => {
    // Only run if we are on the dashboard page or element exists
    if(document.getElementById('notifications-list')) {
        renderNotifications();
    }
});
