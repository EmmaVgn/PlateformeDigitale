document.addEventListener('DOMContentLoaded', () => {
    const ratingStars = document.querySelectorAll('.star-rating i');
    const ratingInput = document.querySelector('#review_rating'); // Vérifiez cet ID
    if (!ratingInput) {
        console.error('Le champ caché de la note est introuvable.');
        return;
    }

    ratingStars.forEach(star => {
        // Gestion du survol des étoiles
        star.addEventListener('mouseover', (event) => {
            const ratingValue = event.target.getAttribute('data-rating');
            updateStarsDisplay(ratingValue);
        });

        // Réinitialisation lors du survol
        star.addEventListener('mouseout', () => {
            updateStarsDisplay(ratingInput.value || 0); // Réinitialiser à la valeur actuelle
        });

        // Gestion du clic sur les étoiles
        star.addEventListener('click', (event) => {
            const ratingValue = event.target.getAttribute('data-rating');
            ratingInput.value = parseInt(ratingValue);  // Convertir la valeur en entier
            console.log("Valeur attribuée :", ratingInput.value); // Vérifiez dans la console
            updateStarsDisplay(ratingValue);  // Mettre à jour l'affichage des étoiles
        });
    });

    // Fonction pour mettre à jour l'affichage des étoiles
    function updateStarsDisplay(value) {
        ratingStars.forEach((star) => {
            const starValue = star.getAttribute('data-rating');
            if (starValue <= value) {
                star.classList.remove('far');
                star.classList.add('fas');
            } else {
                star.classList.remove('fas');
                star.classList.add('far');
            }
        });
    }
});
