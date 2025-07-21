// Prevent form resubmission on browser back button
if (window.history.replaceState) {
    window.history.replaceState(null, null, window.location.href);
}

document.addEventListener('DOMContentLoaded', function() {
    // Check if page was accessed via back button navigation
    const isBackNavigation = 
        (performance.navigation && performance.navigation.type === 2) || 
        (performance.getEntriesByType && performance.getEntriesByType('navigation')[0]?.type === 'back_forward');
    
    if (isBackNavigation) {
        const forms = document.querySelectorAll('form:not(.admin-form)');
        forms.forEach(form => {
            form.reset();
        });
    }
    
    // Mark admin dashboard forms to exclude from reset
    if (window.location.href.includes('adminDashboard.php')) {
        const adminForms = document.querySelectorAll('form');
        adminForms.forEach(form => {
            form.classList.add('admin-form');
        });
    }
});
