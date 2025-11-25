document.addEventListener("DOMContentLoaded", function () {
    toggleProposalForm(); // Initialize proposal form toggle functionality
});

var proposals = [];

function toggleProposalForm() {
    // Find all proposal buttons and add the event listener
    document.querySelectorAll(".propose-btn").forEach(button => {
        button.addEventListener("click", function () {
            let id = this.getAttribute("data-id");
            let proposalForm = document.querySelector(".ProposalForm_" + id);

            if (!proposalForm) {
                console.error("No '.ProposalForm' found for id: " + id);
                return;
            }

            toggleProposalFormVisibility(proposalForm);
        });
    });
}

function toggleProposalFormVisibility(proposalForm) {
    if (window.getComputedStyle(proposalForm).display === "none") {
        proposalForm.style.display = "block";
    } else {
        proposalForm.style.display = "none";
    }
}
function toggleForm(id) {
    var form = document.getElementById('edit-form-' + id);
    if (form.style.display === "none" || form.style.display === "") {
        form.style.display = "block";
    } else {
        form.style.display = "none";
    }
}

function openProposalsModal() {
    document.getElementById('proposalsModal').style.display = 'block';
}

function closeProposalsModal() {
    document.getElementById('proposalsModal').style.display = 'none';
}






function validateCategories() {
    // Prevent form submission by default
    var checkboxes = document.querySelectorAll('input[name="categories[]"]:checked');
    
    // Check if any checkboxes are checked
    if (checkboxes.length == 0) {
        alert("Veuillez sélectionner au moins une catégorie.");
        return false;  // Stop form submission
    }

    // Allow form submission if validation passes
    return true;
}
function toggleForm(id) {
    var form = document.getElementById('edit-form-' + id);
    if (form.style.display === "none" || form.style.display === "") {
        form.style.display = "block";
    } else {
        form.style.display = "none";
    }
}

