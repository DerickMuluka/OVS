function loadCandidates(position) {
    if (position === "") {
        document.getElementById("candidate_id").innerHTML = "<option value=''>Select Candidate</option>";
        return;
    }

    const xhr = new XMLHttpRequest();
    xhr.open("GET", "../admin/get_candidates.php?position=" + encodeURIComponent(position), true);
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            document.getElementById("candidate_id").innerHTML = xhr.responseText;
        }
    };
    xhr.send();
}



// New function to handle form input and submit button state
document.addEventListener('DOMContentLoaded', function () {
    const forms = document.querySelectorAll('form');

    forms.forEach(form => {
        const formInputs = form.querySelectorAll('input[type="text"], input[type="password"]');
        const submitButton = form.querySelector('input[type="submit"]');

        formInputs.forEach(input => {
            input.addEventListener('input', function () {
                if (Array.from(formInputs).every(input => input.value)) {
                    submitButton.classList.add('active');
                } else {
                    submitButton.classList.remove('active');
                }
            });
        });
    });
});

