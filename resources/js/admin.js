/**
 * Panel Administrativo - JavaScript
 */

import * as bootstrap from 'bootstrap';
import axios from 'axios';
import Sortable from 'sortablejs';
import Chart from 'chart.js/auto';

// Expose to window for use in blade templates
window.bootstrap = bootstrap;
window.axios = axios;
window.Sortable = Sortable;
window.Chart = Chart;

// Configure axios
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
const token = document.querySelector('meta[name="csrf-token"]');
if (token) {
    axios.defaults.headers.common['X-CSRF-TOKEN'] = token.getAttribute('content');
}

// ===========================================
// Sidebar Toggle
// ===========================================

document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.querySelector('.sidebar');
    const backdrop = document.querySelector('.sidebar-backdrop');
    const toggleBtn = document.querySelector('.btn-toggle-sidebar');
    const body = document.body;

    // Toggle sidebar on button click
    if (toggleBtn) {
        toggleBtn.addEventListener('click', function() {
            if (window.innerWidth >= 992) {
                body.classList.toggle('sidebar-mini');
                localStorage.setItem('sidebar-mini', body.classList.contains('sidebar-mini'));
            } else {
                sidebar.classList.toggle('show');
                backdrop.classList.toggle('show');
            }
        });
    }

    // Close sidebar on backdrop click (mobile)
    if (backdrop) {
        backdrop.addEventListener('click', function() {
            sidebar.classList.remove('show');
            backdrop.classList.remove('show');
        });
    }

    // Restore sidebar state
    if (localStorage.getItem('sidebar-mini') === 'true' && window.innerWidth >= 992) {
        body.classList.add('sidebar-mini');
    }

    // Handle window resize
    window.addEventListener('resize', function() {
        if (window.innerWidth >= 992) {
            sidebar.classList.remove('show');
            backdrop.classList.remove('show');
        }
    });
});

// ===========================================
// Tooltips & Popovers
// ===========================================

document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    tooltipTriggerList.forEach(function(tooltipTriggerEl) {
        new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Initialize popovers
    const popoverTriggerList = document.querySelectorAll('[data-bs-toggle="popover"]');
    popoverTriggerList.forEach(function(popoverTriggerEl) {
        new bootstrap.Popover(popoverTriggerEl);
    });
});

// ===========================================
// Confirm Delete with SweetAlert2
// ===========================================

document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('[data-confirm]').forEach(function(element) {
        element.addEventListener('click', function(e) {
            e.preventDefault();

            const message = this.dataset.confirm || '¿Estás seguro de que deseas eliminar este elemento?';
            const confirmButtonText = this.dataset.confirmButton || 'Sí, eliminar';
            const cancelButtonText = this.dataset.cancelButton || 'Cancelar';
            const confirmButtonColor = this.dataset.confirmColor || '#dc3545';
            const successMessage = this.dataset.successMessage || 'El elemento ha sido eliminado correctamente.';
            const form = this.closest('form');

            Swal.fire({
                title: '¿Estás seguro?',
                text: message,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: confirmButtonColor,
                cancelButtonColor: '#6c757d',
                confirmButtonText: confirmButtonText,
                cancelButtonText: cancelButtonText,
                reverseButtons: true,
                focusCancel: true
            }).then((result) => {
                if (result.isConfirmed && form) {
                    // Show loading state
                    Swal.fire({
                        title: 'Eliminando...',
                        text: 'Por favor espera',
                        icon: 'info',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showConfirmButton: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // Send request via AJAX
                    const formData = new FormData(form);
                    axios.post(form.action, formData)
                        .then(function(response) {
                            Swal.fire({
                                title: '¡Eliminado!',
                                text: successMessage,
                                icon: 'success',
                                confirmButtonColor: '#46c37b',
                                confirmButtonText: 'Aceptar',
                                timer: 2000,
                                timerProgressBar: true
                            }).then(() => {
                                // Reload page or remove row
                                const row = form.closest('tr');
                                if (row) {
                                    row.style.transition = 'opacity 0.3s ease';
                                    row.style.opacity = '0';
                                    setTimeout(() => {
                                        row.remove();
                                        // Check if table is now empty
                                        const tbody = document.querySelector('table tbody');
                                        if (tbody && tbody.querySelectorAll('tr').length === 0) {
                                            window.location.reload();
                                        }
                                    }, 300);
                                } else {
                                    window.location.reload();
                                }
                            });
                        })
                        .catch(function(error) {
                            let errorMessage = 'No se pudo eliminar el elemento.';
                            if (error.response && error.response.data && error.response.data.message) {
                                errorMessage = error.response.data.message;
                            }
                            Swal.fire({
                                title: 'Error',
                                text: errorMessage,
                                icon: 'error',
                                confirmButtonColor: '#d26a5c',
                                confirmButtonText: 'Aceptar'
                            });
                        });
                }
            });
        });
    });
});

// ===========================================
// Alert Auto-dismiss
// ===========================================

document.addEventListener('DOMContentLoaded', function() {
    const alerts = document.querySelectorAll('.alert-dismissible.auto-dismiss');
    alerts.forEach(function(alert) {
        setTimeout(function() {
            const bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
            bsAlert.close();
        }, 5000);
    });
});

// ===========================================
// Sortable Tables
// ===========================================

document.addEventListener('DOMContentLoaded', function() {
    const sortableTables = document.querySelectorAll('[data-sortable]');

    sortableTables.forEach(function(table) {
        const tbody = table.querySelector('tbody');
        const url = table.dataset.sortable;

        if (tbody && url) {
            new Sortable(tbody, {
                handle: '.drag-handle',
                animation: 150,
                onEnd: function() {
                    const order = Array.from(tbody.querySelectorAll('tr')).map(function(row) {
                        return row.dataset.id;
                    });

                    axios.post(url, { order: order })
                        .then(function(response) {
                            if (response.data.success) {
                                showToast('Orden actualizado', 'success');
                            }
                        })
                        .catch(function() {
                            showToast('Error al actualizar el orden', 'danger');
                        });
                }
            });
        }
    });
});

// ===========================================
// Image Preview
// ===========================================

document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('input[type="file"][data-preview]').forEach(function(input) {
        const previewId = input.dataset.preview;
        const preview = document.getElementById(previewId);

        if (preview) {
            input.addEventListener('change', function() {
                if (this.files && this.files[0]) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        preview.src = e.target.result;
                        preview.classList.remove('d-none');
                    };
                    reader.readAsDataURL(this.files[0]);
                }
            });
        }
    });
});

// ===========================================
// Toast Notifications
// ===========================================

window.showToast = function(message, type = 'info') {
    const container = document.querySelector('.toast-container') || createToastContainer();

    const toast = document.createElement('div');
    toast.className = `toast align-items-center text-white bg-${type} border-0`;
    toast.setAttribute('role', 'alert');
    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">${message}</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    `;

    container.appendChild(toast);

    const bsToast = new bootstrap.Toast(toast);
    bsToast.show();

    toast.addEventListener('hidden.bs.toast', function() {
        toast.remove();
    });
};

function createToastContainer() {
    const container = document.createElement('div');
    container.className = 'toast-container position-fixed bottom-0 end-0 p-3';
    container.style.zIndex = '1100';
    document.body.appendChild(container);
    return container;
}

// ===========================================
// Dynamic Form Fields
// ===========================================

window.toggleFields = function(selectElement, fieldMappings) {
    const value = selectElement.value;

    Object.keys(fieldMappings).forEach(function(key) {
        const fields = fieldMappings[key];
        const show = key === value;

        fields.forEach(function(fieldId) {
            const field = document.getElementById(fieldId);
            if (field) {
                field.closest('.form-group, .mb-3').style.display = show ? 'block' : 'none';
            }
        });
    });
};

// ===========================================
// Character Counter
// ===========================================

document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('[data-maxlength]').forEach(function(input) {
        const max = parseInt(input.dataset.maxlength);
        const counter = document.createElement('small');
        counter.className = 'text-muted mt-1 d-block';

        input.parentNode.appendChild(counter);

        function updateCounter() {
            const remaining = max - input.value.length;
            counter.textContent = `${remaining} caracteres restantes`;
            counter.classList.toggle('text-danger', remaining < 20);
        }

        input.addEventListener('input', updateCounter);
        updateCounter();
    });
});

console.log('Admin panel initialized');
