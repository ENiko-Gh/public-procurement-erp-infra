// assets/js/app.js

// Confirm delete
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.btn-delete').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            if (!confirm('¿Está seguro de eliminar este registro? Esta acción no se puede deshacer.')) {
                e.preventDefault();
            }
        });
    });

    // Auto-dismiss alerts after 5 seconds
    setTimeout(function() {
        document.querySelectorAll('.alert').forEach(function(alert) {
            var bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 5000);
});

// Edit modal loader - fills modal form fields from row data
function openEditModal(modalId, data) {
    var modal = document.getElementById(modalId);
    if (!modal) return;
    Object.keys(data).forEach(function(key) {
        var el = modal.querySelector('[name="' + key + '"]');
        if (el) el.value = data[key];
    });
    new bootstrap.Modal(modal).show();
}
