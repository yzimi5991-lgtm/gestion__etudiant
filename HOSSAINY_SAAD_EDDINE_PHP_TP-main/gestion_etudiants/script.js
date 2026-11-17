/**
 * Script JavaScript pour l'application Gestion des étudiants (Exercice 11 et Bonus)
 */
document.addEventListener('DOMContentLoaded', () => {

    // 1. Demande de confirmation avant suppression (Exercice 11)
    const deleteButtons = document.querySelectorAll('.btn-confirm-delete, a[href*="supprimer.php"]');
    deleteButtons.forEach(button => {
        button.addEventListener('click', (event) => {
            const confirmed = window.confirm("Êtes-vous sûr de vouloir supprimer cet étudiant ? Cette action est irréversible.");
            if (!confirmed) {
                event.preventDefault();
            }
        });
    });

    // 2. Recherche instantanée en temps réel dans le tableau (Exercice 11)
    const searchInput = document.getElementById('search-input');
    const table = document.querySelector('.table-etudiants');

    if (searchInput && table) {
        const rows = table.querySelectorAll('tbody tr:not(.empty-row)');

        searchInput.addEventListener('input', function () {
            const query = this.value.trim().toLowerCase();
            let visibleCount = 0;

            rows.forEach(row => {
                const nomText     = row.querySelector('.col-nom')?.textContent.toLowerCase() || '';
                const prenomText  = row.querySelector('.col-prenom')?.textContent.toLowerCase() || '';
                const filiereText = row.querySelector('.col-filiere')?.textContent.toLowerCase() || '';
                const emailText   = row.querySelector('.col-email')?.textContent.toLowerCase() || '';

                // Vérifie si le nom ou le prénom (ou filière/email) correspond
                const matches = nomText.includes(query) ||
                                prenomText.includes(query) ||
                                filiereText.includes(query) ||
                                emailText.includes(query);

                if (matches) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });

            // Affichage d'un message dynamique si aucun résultat trouvé
            let dynamicEmptyRow = document.getElementById('js-no-results-row');
            if (visibleCount === 0 && rows.length > 0) {
                if (!dynamicEmptyRow) {
                    dynamicEmptyRow = document.createElement('tr');
                    dynamicEmptyRow.id = 'js-no-results-row';
                    dynamicEmptyRow.className = 'empty-row';
                    dynamicEmptyRow.innerHTML = '<td colspan="7" class="text-center empty-msg">Aucun étudiant ne correspond à votre recherche en direct.</td>';
                    table.querySelector('tbody').appendChild(dynamicEmptyRow);
                }
                dynamicEmptyRow.style.display = '';
            } else if (dynamicEmptyRow) {
                dynamicEmptyRow.style.display = 'none';
            }
        });
    }

    // 3. Basculement dynamique du thème clair / sombre (Bonus)
    const themeToggleBtn = document.getElementById('theme-toggle');
    if (themeToggleBtn) {
        themeToggleBtn.addEventListener('click', () => {
            const htmlElement = document.documentElement;
            const currentTheme = htmlElement.getAttribute('data-theme') || 'clair';
            const newTheme = (currentTheme === 'sombre') ? 'clair' : 'sombre';

            htmlElement.setAttribute('data-theme', newTheme);

            // Mémorisation dans un cookie valable 30 jours
            const d = new Date();
            d.setTime(d.getTime() + (30 * 24 * 60 * 60 * 1000));
            document.cookie = `theme=${newTheme};expires=${d.toUTCString()};path=/;SameSite=Lax`;
        });
    }

    // 4. Auto-dismiss des alertes après 6 secondes (amélioration UX)
    const alerts = document.querySelectorAll('.alert-success');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.transition = 'opacity 0.6s ease';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 600);
        }, 5000);
    });
});


