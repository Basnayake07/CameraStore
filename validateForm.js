$(document).ready(function () {
    $("#registrationForm").submit(function (event) {

        var name = $("#reg-username").val();
        var phone = $("#PhoneNum").val().trim();
        var password = $("#reg-password").val().trim();
        var isValid = true;

        // Validate name
        if (name.length < 5) {
            alert("Name must be at least 5 characters long.")
            isValid = false;
        }

        // Validate phone number
        const phonePattern = /^\d{10}$/;
        if (phone === "") {
            alert("Phone is required.");
            isValid = false;
        } else if (!phonePattern.test(phone)) {
            alert("Invalid phone number.");
            isValid = false;
        }

        // Validate password
        if (password.length < 8) {
            alert("Password must be at least 8 characters long.");
            isValid = false;
        }

        // Prevent form submission if there are validation errors
        if (!isValid) {
            event.preventDefault();
        }
    });
});
