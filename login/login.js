// This function sets up the slide-on-hover animation for the role selector.
function setupRoleSelectorAnimation() {
    const selector = document.querySelector('.role-selector');
    const highlight = document.querySelector('.role-highlight');
    const inputs = document.querySelectorAll('input[name="role"]');

    // Exit if the necessary elements don't exist on the page.
    if (!selector || !highlight || !inputs.length) {
        return;
    }

    // This function moves the highlight to a specific target label.
    function moveHighlightTo(targetLabel) {
        if (!targetLabel) return;
        const labelWidth = targetLabel.offsetWidth;
        const labelLeft = targetLabel.offsetLeft;

        highlight.style.width = `${labelWidth}px`;
        highlight.style.transform = `translateX(${labelLeft}px)`;
    }

    // --- SETUP EVENT LISTENERS ---

    // 1. Initial position: Find the currently checked input and move the highlight there.
    const initiallyCheckedInput = document.querySelector('input[name="role"]:checked');
    const initialLabel = document.querySelector(`label[for="${initiallyCheckedInput.id}"]`);
    moveHighlightTo(initialLabel);


    // 2. Hover effect: When the mouse enters a label, slide the highlight to it.
    const allLabels = document.querySelectorAll('.role-selector label');
    allLabels.forEach(label => {
        label.addEventListener('mouseenter', () => {
            moveHighlightTo(label);
        });
    });


    // 3. Mouse leave effect: When the mouse leaves the entire container,
    //    slide the highlight back to the currently selected (checked) option.
    selector.addEventListener('mouseleave', () => {
        const checkedInput = document.querySelector('input[name="role"]:checked');
        const checkedLabel = document.querySelector(`label[for="${checkedInput.id}"]`);
        moveHighlightTo(checkedLabel);
    });

    // 4. Click Persistence: No extra JS needed. When a label is clicked,
    //    the radio button becomes checked. The 'mouseleave' event will then
    //    automatically know the new correct position to slide back to.
    
    // 5. Responsive Resizing: Recalculate position on window resize.
    window.addEventListener('resize', () => {
        const checkedInput = document.querySelector('input[name="role"]:checked');
        const checkedLabel = document.querySelector(`label[for="${checkedInput.id}"]`);
        moveHighlightTo(checkedLabel);
    });
}

// Run the setup function after the page has finished loading.
document.addEventListener('DOMContentLoaded', setupRoleSelectorAnimation);