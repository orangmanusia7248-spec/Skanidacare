document.addEventListener('submit', (event) => {
    const form = event.target;
    const confirmation = form.dataset.confirm;

    if (confirmation && !window.confirm(confirmation)) {
        event.preventDefault();
        return;
    }

    const submitButton = event.submitter;
    if (submitButton && !submitButton.disabled) {
        submitButton.disabled = true;
        submitButton.dataset.originalText = submitButton.textContent;
        submitButton.textContent = 'Memproses...';
    }
}, true);
