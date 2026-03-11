/**
 * Confirmation Dialog Utility
 * 
 * Displays a modal confirmation dialog with customizable message and subject.
 * 
 * @param {Object} options - Configuration options
 * @param {string} options.message - The confirmation message to display
 * @param {string} [options.subject=''] - Optional subject/title for the dialog
 * @param {Function} options.onConfirm - Callback function to execute on confirmation
 * @param {Function} [options.onCancel=null] - Optional callback function to execute on cancellation
 */
window.confirmDialog = function(options) {
    // Support legacy function signature: confirmDialog(message, onConfirm, subject)
    if (typeof options === 'string') {
        options = {
            message: arguments[0],
            onConfirm: arguments[1],
            subject: arguments[2] || ''
        };
    }

    const {
        message = '',
        subject = '',
        onConfirm,
        onCancel = null
    } = options;

    // Validate required parameters
    if (typeof onConfirm !== 'function') {
        console.error('confirmDialog: onConfirm callback is required and must be a function');
        return;
    }

    const $dialog = $('#confirmDialog');
    const $overlay = $('#overlay');
    const $messageEl = $('#confirmMessage');
    const $subjectEl = $('#confirmSubject');

    // Set message content
    if (message) {
        $messageEl.text(message);
    }

    // Set subject content and visibility
    if (subject) {
        $subjectEl.text(subject).show();
    } else {
        $subjectEl.hide();
    }

    // Show dialog
    $overlay.add($dialog).fadeIn();

    // Handle dialog actions using event delegation
    const handleDialogAction = function(event) {
        const action = $(event.currentTarget).data('action');
        
        // Hide dialog
        $overlay.add($dialog).fadeOut();

        // Execute appropriate callback
        if (action === 'confirm') {
            onConfirm(1);
        } else if (action === 'cancel' && typeof onCancel === 'function') {
            onCancel();
        }

        // Clean up event listeners
        cleanup();
    };

    // Cleanup function to remove event listeners
    const cleanup = function() {
        $dialog.off('click', '[data-action]', handleDialogAction);
    };

    // Remove any existing listeners and attach new ones
    cleanup();
    $dialog.on('click', '[data-action]', handleDialogAction);
};
