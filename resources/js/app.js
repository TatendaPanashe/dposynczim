document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-sidebar-toggle]').forEach((button) => {
        button.addEventListener('click', () => {
            const targetId = button.getAttribute('data-sidebar-toggle');
            const target = targetId ? document.getElementById(targetId) : null;

            if (! target) {
                return;
            }

            const isOpen = ! target.hidden;

            if (! isOpen) {
                document.querySelectorAll('[data-sidebar-panel]').forEach((openPanel) => {
                    openPanel.hidden = true;

                    const openButton = document.querySelector(`[data-sidebar-toggle="${openPanel.id}"]`);
                    openButton?.classList.add('collapsed');
                    openButton?.setAttribute('aria-expanded', 'false');
                });
            }

            target.hidden = isOpen;
            button.classList.toggle('collapsed', isOpen);
            button.setAttribute('aria-expanded', isOpen ? 'false' : 'true');
        });
    });
});
