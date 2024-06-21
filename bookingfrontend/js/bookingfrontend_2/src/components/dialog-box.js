export function showDialogue(title, bodyText, options) {
    return new Promise((resolve, reject) => {
        // Create the dialog element
        const dialog = document.createElement('dialog');
        dialog.className = "options-dialog"

        // Title row
        const titleRow = document.createElement('div');
        titleRow.className = "options-dialog-title"

        titleRow.textContent = title;
        dialog.appendChild(titleRow);

        // Body text row
        const bodyTextRow = document.createElement('div');
        bodyTextRow.className = "options-dialog-body"

        bodyTextRow.textContent = bodyText;
        dialog.appendChild(bodyTextRow);

        // Options row
        const optionsRow = document.createElement('div');
        optionsRow.className = "options-dialog-options"

        options.forEach((option, index) => {
            const button = document.createElement('button');
            button.textContent = option;
            button.className = 'pe-btn  pe-btn--transparent pe-btn-text-primary'
            button.onclick = () => {
                dialog.close(index);
            };
            optionsRow.appendChild(button);
        });

        dialog.appendChild(optionsRow);

        // Close event listener
        dialog.addEventListener('close', () => {
            if (dialog.returnValue) {
                resolve(dialog.returnValue);
            } else {
                reject(new Error('Dialog closed without selection'));
            }
            dialog.remove();
        });

        document.body.appendChild(dialog);

        dialog.showModal();
    });
}
