// Wait for the page to be fully loaded before running the script
document.addEventListener('DOMContentLoaded', function() {
    console.log('Result.js loaded successfully!');

    // Find all the elements that are meant to be collapsible
    const collapsibles = document.querySelectorAll('.collapsible');
    console.log('Found collapsibles:', collapsibles.length);

    // Loop through each collapsible element
    collapsibles.forEach((item, index) => {
        // Find the clickable header within each collapsible item
        const header = item.querySelector('.semester-header');
        const content = item.querySelector('.collapsible-content');
        const icon = header.querySelector('i');

        console.log(`Collapsible ${index + 1} setup complete`);

        // Add a click event listener to the header
        header.addEventListener('click', function() {
            console.log(`Clicked on semester ${index + 1}`);
            
            // Toggle the active class on the parent card
            const isActive = item.classList.contains('active');
            
            if (isActive) {
                // Close the dropdown
                content.classList.add('hidden');
                icon.style.transform = 'rotate(0deg)';
                item.classList.remove('active');
                console.log(`Closed semester ${index + 1}`);
            } else {
                // Open the dropdown
                content.classList.remove('hidden');
                icon.style.transform = 'rotate(180deg)';
                item.classList.add('active');
                console.log(`Opened semester ${index + 1}`);
            }
        });
    });

});