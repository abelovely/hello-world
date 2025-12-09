/**
 * General Wingate Polytechnic College - Employee Management System
 * Custom JavaScript
 */

// Confirm delete action
function confirmDelete(message) {
    return confirm(message || 'Are you sure you want to delete this item? This action cannot be undone.');
}

// Form validation
function validateForm(formId) {
    const form = document.getElementById(formId);
    if (!form) return false;
    
    let isValid = true;
    const requiredFields = form.querySelectorAll('[required]');
    
    requiredFields.forEach(field => {
        if (!field.value.trim()) {
            field.classList.add('is-invalid');
            isValid = false;
        } else {
            field.classList.remove('is-invalid');
            field.classList.add('is-valid');
        }
    });
    
    return isValid;
}

// Clear form validation
function clearValidation(formId) {
    const form = document.getElementById(formId);
    if (!form) return;
    
    const fields = form.querySelectorAll('.is-invalid, .is-valid');
    fields.forEach(field => {
        field.classList.remove('is-invalid', 'is-valid');
    });
}

// Calculate weighted score
function calculateWeightedScore(score, weight) {
    return (parseFloat(score) * parseFloat(weight)).toFixed(2);
}

// Format number with commas
function formatNumber(num) {
    return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

// Show loading spinner
function showLoading() {
    const spinner = document.createElement('div');
    spinner.id = 'loading-spinner';
    spinner.className = 'spinner-overlay';
    spinner.innerHTML = '<div class="spinner-border text-light" role="status"><span class="visually-hidden">Loading...</span></div>';
    document.body.appendChild(spinner);
}

// Hide loading spinner
function hideLoading() {
    const spinner = document.getElementById('loading-spinner');
    if (spinner) {
        spinner.remove();
    }
}

// Print report
function printReport() {
    window.print();
}

// Export to PDF (requires html2pdf library)
function exportToPDF(elementId, filename) {
    const element = document.getElementById(elementId);
    if (!element) {
        alert('Element not found for PDF export');
        return;
    }
    
    // Check if html2pdf is available
    if (typeof html2pdf === 'undefined') {
        alert('PDF export library not loaded. Using print instead...');
        window.print();
        return;
    }
    
    const opt = {
        margin: 10,
        filename: filename || 'report.pdf',
        image: { type: 'jpeg', quality: 0.98 },
        html2canvas: { scale: 2 },
        jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
    };
    
    html2pdf().set(opt).from(element).save();
}

// Auto-dismiss alerts after 5 seconds
document.addEventListener('DOMContentLoaded', function() {
    const alerts = document.querySelectorAll('.alert:not(.alert-permanent)');
    alerts.forEach(alert => {
        setTimeout(() => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 5000);
    });
});

// Enable tooltips
document.addEventListener('DOMContentLoaded', function() {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});

// Enable popovers
document.addEventListener('DOMContentLoaded', function() {
    const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    popoverTriggerList.map(function(popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });
});

// Password visibility toggle
function togglePasswordVisibility(inputId) {
    const input = document.getElementById(inputId);
    const icon = event.target;
    
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

// Score input validation
function validateScore(input, maxScore) {
    const value = parseFloat(input.value);
    const max = parseFloat(maxScore);
    
    if (isNaN(value) || value < 0) {
        input.value = 0;
    } else if (value > max) {
        input.value = max;
    }
    
    // Update weighted score display if exists
    updateWeightedScore(input);
}

// Update weighted score display
function updateWeightedScore(scoreInput) {
    const row = scoreInput.closest('tr');
    if (!row) return;
    
    const weight = parseFloat(row.dataset.weight || 1);
    const score = parseFloat(scoreInput.value || 0);
    const weightedScore = score * weight;
    
    const weightedDisplay = row.querySelector('.weighted-score');
    if (weightedDisplay) {
        weightedDisplay.textContent = weightedScore.toFixed(2);
    }
}

// Calculate total score
function calculateTotalScore() {
    const scoreInputs = document.querySelectorAll('.score-input');
    let totalWeighted = 0;
    let totalWeight = 0;
    
    scoreInputs.forEach(input => {
        const row = input.closest('tr');
        const weight = parseFloat(row.dataset.weight || 0);
        const score = parseFloat(input.value || 0);
        
        totalWeighted += score * weight;
        totalWeight += weight;
    });
    
    const averageScore = totalWeight > 0 ? totalWeighted / totalWeight : 0;
    
    const totalDisplay = document.getElementById('total-score');
    if (totalDisplay) {
        totalDisplay.textContent = averageScore.toFixed(2);
    }
    
    const totalWeightedDisplay = document.getElementById('total-weighted-score');
    if (totalWeightedDisplay) {
        totalWeightedDisplay.textContent = totalWeighted.toFixed(2);
    }
}

// Filter table
function filterTable(inputId, tableId) {
    const input = document.getElementById(inputId);
    const table = document.getElementById(tableId);
    
    if (!input || !table) return;
    
    const filter = input.value.toUpperCase();
    const rows = table.getElementsByTagName('tr');
    
    for (let i = 1; i < rows.length; i++) { // Skip header row
        const cells = rows[i].getElementsByTagName('td');
        let found = false;
        
        for (let j = 0; j < cells.length; j++) {
            const cell = cells[j];
            if (cell) {
                const textValue = cell.textContent || cell.innerText;
                if (textValue.toUpperCase().indexOf(filter) > -1) {
                    found = true;
                    break;
                }
            }
        }
        
        rows[i].style.display = found ? '' : 'none';
    }
}

// Bulk action handler
function handleBulkAction() {
    const action = document.getElementById('bulk-action');
    const checkboxes = document.querySelectorAll('.item-checkbox:checked');
    
    if (!action || checkboxes.length === 0) {
        alert('Please select items first');
        return;
    }
    
    const actionValue = action.value;
    if (!actionValue) {
        alert('Please select an action');
        return;
    }
    
    const ids = Array.from(checkboxes).map(cb => cb.value);
    
    if (confirm(`Are you sure you want to ${actionValue} ${ids.length} item(s)?`)) {
        // Submit form or make AJAX request
        console.log(`Performing ${actionValue} on items:`, ids);
    }
}

// Select all checkboxes
function toggleSelectAll(source) {
    const checkboxes = document.querySelectorAll('.item-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = source.checked;
    });
}

// Auto-save draft (for forms)
function autoSaveDraft(formId, key) {
    const form = document.getElementById(formId);
    if (!form) return;
    
    const formData = new FormData(form);
    const data = {};
    
    formData.forEach((value, key) => {
        data[key] = value;
    });
    
    localStorage.setItem(key, JSON.stringify(data));
}

// Restore draft
function restoreDraft(formId, key) {
    const form = document.getElementById(formId);
    if (!form) return;
    
    const data = localStorage.getItem(key);
    if (!data) return;
    
    const parsedData = JSON.parse(data);
    
    Object.keys(parsedData).forEach(key => {
        const input = form.querySelector(`[name="${key}"]`);
        if (input) {
            input.value = parsedData[key];
        }
    });
}

// Clear draft
function clearDraft(key) {
    localStorage.removeItem(key);
}

// Character counter for textareas
function updateCharCount(textarea, counterId) {
    const counter = document.getElementById(counterId);
    if (!counter) return;
    
    const current = textarea.value.length;
    const max = textarea.maxLength;
    
    counter.textContent = `${current}/${max}`;
    
    if (current >= max * 0.9) {
        counter.classList.add('text-danger');
    } else {
        counter.classList.remove('text-danger');
    }
}

// Date validation
function validateDateRange(startDateId, endDateId) {
    const startDate = document.getElementById(startDateId);
    const endDate = document.getElementById(endDateId);
    
    if (!startDate || !endDate) return true;
    
    if (startDate.value && endDate.value) {
        if (new Date(startDate.value) > new Date(endDate.value)) {
            alert('End date must be after start date');
            return false;
        }
    }
    
    return true;
}

// Smooth scroll to element
function scrollToElement(elementId) {
    const element = document.getElementById(elementId);
    if (element) {
        element.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
}

// Copy to clipboard
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        alert('Copied to clipboard!');
    }).catch(err => {
        console.error('Failed to copy:', err);
    });
}
