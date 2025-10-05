// Wait for the page to be fully loaded before running the script
document.addEventListener('DOMContentLoaded', function() {

    // Find all the elements that are meant to be collapsible
    const collapsibles = document.querySelectorAll('.collapsible');

    // Loop through each collapsible element
    collapsibles.forEach(item => {
        // Find the clickable header within each collapsible item
        const header = item.querySelector('.semester-header');

        // Add a click event listener to the header
        header.addEventListener('click', function() {
            // When the header is clicked, toggle the 'active' class on the parent card
            item.classList.toggle('active');
        });
    });

});