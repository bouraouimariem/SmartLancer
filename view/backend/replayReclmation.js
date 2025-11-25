const form = document.getElementById("responseForm");
const responseField = document.getElementById("response");
const feedback = document.getElementById("responseFeedback");

form.addEventListener("submit", function(e){
    e.preventDefault();

    const value = responseField.value.trim();

    if(value.length < 5){
        responseField.classList.add("input-error");
        responseField.classList.remove("input-success");
        feedback.textContent = "✖";
        feedback.classList.add("error");
        feedback.classList.remove("correct");
    } else {
        responseField.classList.remove("input-error");
        responseField.classList.add("input-success");
        feedback.textContent = "✔";
        feedback.classList.add("correct");
        feedback.classList.remove("error");
        form.submit();
    }
});
