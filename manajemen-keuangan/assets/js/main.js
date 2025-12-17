// Main JavaScript Utilities

// Initialize DataTables
function initDataTable(selector, options = {}) {
    const defaultOptions = {
        pageLength: 10,
        language: {
            search: "Cari:",
            lengthMenu: "Tampilkan _MENU_ data per halaman",
            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
            infoEmpty: "Tidak ada data",
            infoFiltered: "(disaring dari _MAX_ total data)",
            paginate: {
                first: "Pertama",
                last: "Terakhir",
                next: "Selanjutnya",
                previous: "Sebelumnya"
            },
            zeroRecords: "Tidak ada data yang ditemukan"
        }
    };

    return $(selector).DataTable({ ...defaultOptions, ...options });
}

// Format currency to Rupiah
function formatRupiah(amount) {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(amount);
}

// Confirm delete
function confirmDelete(message = 'Apakah Anda yakin ingin menghapus data ini?') {
    return confirm(message);
}

// Show loading spinner
function showLoading() {
    if ($('#loadingSpinner').length === 0) {
        $('body').append('<div id="loadingSpinner" class="position-fixed top-50 start-50 translate-middle" style="z-index: 9999;"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>');
    }
}

// Hide loading spinner
function hideLoading() {
    $('#loadingSpinner').remove();
}

// Alert helper
function showAlert(message, type = 'success') {
    const alertHtml = `
        <div class="alert alert-${type} alert-dismissible fade show" role="alert">
            <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i> ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;

    $('.content-wrapper').prepend(alertHtml);

    // Auto-dismiss after 5 seconds
    setTimeout(() => {
        $('.alert').fadeOut('slow', function () {
            $(this).remove();
        });
    }, 5000);
}

// Logout Confirmation
function confirmLogout(event) {
    event.preventDefault();

    Swal.fire({
        title: 'Apakah Anda yakin ingin logout?',
        text: "Anda akan keluar dari sesi saat ini.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, Logout',
        cancelButtonText: 'Batal',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = '/manajemen-keuangan/logout.php';
        }
    });
}

// Document ready
$(document).ready(function () {
    // Add fade-in animation to cards
    $('.card').addClass('fade-in');

    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
