// Script to prevent form resubmission when using the browser's back button
if (window.history.replaceState) {
    window.history.replaceState(null, null, window.location.href);
}

// Add event listeners to all forms to reset them after submission
document.addEventListener('DOMContentLoaded', function() {
    // Check if the page was accessed via form submission and then back button
    // Using a fallback for browsers that don't support performance.navigation
    const isBackNavigation = 
        (performance.navigation && performance.navigation.type === 2) || 
        (performance.getEntriesByType && performance.getEntriesByType('navigation')[0]?.type === 'back_forward');
    
    if (isBackNavigation) {
        // Reset all forms except admin forms
        const forms = document.querySelectorAll('form:not(.admin-form)');
        forms.forEach(form => {
            form.reset();
        });
    }
    
    // Add the admin-form class to forms in the admin dashboard
    if (window.location.href.includes('adminDashboard.php')) {
        const adminForms = document.querySelectorAll('form');
        adminForms.forEach(form => {
            form.classList.add('admin-form');
        });
    }
});
