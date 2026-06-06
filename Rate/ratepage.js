document.addEventListener("DOMContentLoaded", () => {
  const form = document.querySelector("form[action='ratepageStuff.php']");
  const flash = document.getElementById("flashMessage");

  form.addEventListener("submit", (e) => {
    const rating = form.querySelector("input[name='rating']:checked");
    const feedback = form.querySelector("#feedback").value.trim();
    const email = form.querySelector("#email").value.trim();

    let errors = [];

    if (!rating) errors.push("Please select a rating.");
    if (!feedback) errors.push("Please provide your feedback.");
    if (email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
      errors.push("Please enter a valid email address.");
    }

    if (errors.length > 0) {
      e.preventDefault(); // stop form submission
      flash.textContent = errors.join(" ");
      flash.className = "flash-message error";
      flash.style.display = "block";
      return;
    }

    // valid, form submits normally (PHP handles redirect)
    flash.style.display = "none";
  });
});
