document.addEventListener('DOMContentLoaded', function() {
    // 1. Demande de confirmation avant suppression
    const liensSuppression = document.querySelectorAll('.btn-supprimer');
    liensSuppression.forEach(function(lien) {
        lien.addEventListener('click', function(e) {
            if (!confirm("Voulez-vous vraiment supprimer cet étudiant ?")) {
                e.preventDefault();
            }
        });
    });

    // 2. Recherche instantanée dans le tableau
    const searchInput = document.getElementById('search-input');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const filter = searchInput.value.toLowerCase();
            const lignes = document.querySelectorAll('.data-table tbody tr');

            lignes.forEach(function(ligne) {
                const texteLigne = ligne.textContent.toLowerCase();
                if (texteLigne.includes(filter)) {
                    ligne.style.display = '';
                } else {
                    ligne.style.display = 'none';
                }
            });
        });
    }
});