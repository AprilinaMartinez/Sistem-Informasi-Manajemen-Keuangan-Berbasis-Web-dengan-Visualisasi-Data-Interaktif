// Calendar Widget
let currentDate = new Date();
let selectedDate = null;

function populateDropdowns() {
    const monthSelect = document.getElementById('monthSelect');
    const yearSelect = document.getElementById('yearSelect');
    const monthNames = ["Januari", "Februari", "Maret", "April", "Mei", "Juni",
        "Juli", "Agustus", "September", "Oktober", "November", "Desember"];

    // Populate Months
    monthSelect.innerHTML = '';
    monthNames.forEach((month, index) => {
        const option = document.createElement('option');
        option.value = index;
        option.textContent = month;
        monthSelect.appendChild(option);
    });

    // Populate Years (Current +/- 5)
    const currentYear = new Date().getFullYear();
    yearSelect.innerHTML = '';
    for (let i = currentYear - 5; i <= currentYear + 5; i++) {
        const option = document.createElement('option');
        option.value = i;
        option.textContent = i;
        yearSelect.appendChild(option);
    }

    // Set initial values
    monthSelect.value = currentDate.getMonth();
    yearSelect.value = currentDate.getFullYear();
}

function updateCalendarFromDropdown() {
    const month = parseInt(document.getElementById('monthSelect').value);
    const year = parseInt(document.getElementById('yearSelect').value);
    currentDate = new Date(year, month, 1);
    renderCalendar();
}

function renderCalendar() {
    const dayNames = ["Min", "Sen", "Sel", "Rab", "Kam", "Jum", "Sab"];

    const year = currentDate.getFullYear();
    const month = currentDate.getMonth();

    // Sync dropdowns
    const monthSelect = document.getElementById('monthSelect');
    const yearSelect = document.getElementById('yearSelect');
    if (monthSelect && yearSelect) {
        monthSelect.value = month;
        yearSelect.value = year;
    }

    // Get first day of month and number of days
    const firstDay = new Date(year, month, 1).getDay();
    const daysInMonth = new Date(year, month + 1, 0).getDate();
    const daysInPrevMonth = new Date(year, month, 0).getDate();

    // Build calendar HTML
    let html = '';

    // Day names
    dayNames.forEach(day => {
        html += `<div class="calendar-day-name">${day}</div>`;
    });

    // Previous month days
    for (let i = firstDay - 1; i >= 0; i--) {
        html += `<div class="calendar-day other-month">${daysInPrevMonth - i}</div>`;
    }

    // Current month days
    const today = new Date();
    for (let day = 1; day <= daysInMonth; day++) {
        const isToday = (day === today.getDate() && month === today.getMonth() && year === today.getFullYear());
        const isSelected = selectedDate && (day === selectedDate.getDate() && month === selectedDate.getMonth() && year === selectedDate.getFullYear());

        let classes = 'calendar-day';
        if (isToday) classes += ' today';
        if (isSelected) classes += ' selected';

        html += `<div class="${classes}" onclick="selectDate(${day}, ${month}, ${year})">${day}</div>`;
    }

    // Next month days to fill grid
    const totalCells = Math.ceil((firstDay + daysInMonth) / 7) * 7;
    const remainingCells = totalCells - (firstDay + daysInMonth);
    for (let day = 1; day <= remainingCells; day++) {
        html += `<div class="calendar-day other-month">${day}</div>`;
    }

    const calendarDays = document.getElementById('calendarDays');
    if (calendarDays) {
        calendarDays.innerHTML = html;
    }
}

function selectDate(day, month, year) {
    selectedDate = new Date(year, month, day);
    renderCalendar();
}

// Initialize calendar
document.addEventListener('DOMContentLoaded', function () {
    populateDropdowns();
    renderCalendar();
});
