document.addEventListener('DOMContentLoaded', function() {
    // Find all the "View Details" buttons
    const toggleButtons = document.querySelectorAll('.btn-view');

    toggleButtons.forEach(button => {
        button.addEventListener('click', function(event) {
            // Prevent the link from trying to navigate away
            event.preventDefault(); 
            
            // Find the parent card of the button that was clicked
            const parentCard = this.closest('.subject-card.collapsible');
            
            // Toggle the 'active' class ONLY on that specific card
            if (parentCard) {
                parentCard.classList.toggle('active');
            }
        });
    });
});