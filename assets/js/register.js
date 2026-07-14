document.addEventListener("DOMContentLoaded", () => {

    const password = document.getElementById("password");
    const confirmPassword = document.getElementById("confirm_password");

    const strengthFill = document.getElementById("strengthFill");
    const strengthText = document.getElementById("strengthText");
    const passwordMatch = document.getElementById("passwordMatch");

    const togglePassword = document.getElementById("togglePassword");
    const toggleConfirm = document.getElementById("toggleConfirmPassword");

    /* ==========================================
       SHOW / HIDE PASSWORD
    ========================================== */

    function toggleVisibility(input){

        if(input.type === "password"){

            input.type = "text";

        }else{

            input.type = "password";

        }

    }

    if(togglePassword){

        togglePassword.addEventListener("click", () => {

            toggleVisibility(password);

        });

    }

    if(toggleConfirm){

        toggleConfirm.addEventListener("click", () => {

            toggleVisibility(confirmPassword);

        });

    }

    /* ==========================================
       PASSWORD STRENGTH
    ========================================== */

    if(password){

        password.addEventListener("input", () => {

            const value = password.value;

            let score = 0;

        // Length
        if(value.length >= 8) score += 2;
        if(value.length >= 12) score += 1;

        // Character variety
        if(/[a-z]/.test(value)) score += 1;
        if(/[A-Z]/.test(value)) score += 1;
        if(/[0-9]/.test(value)) score += 1;
        if(/[^A-Za-z0-9]/.test(value)) score += 1;
            if(score <= 2){

    strengthFill.style.width = "25%";
    strengthFill.style.background = "#E74C3C";
    strengthText.textContent = "Weak";

}
else if(score <= 4){

    strengthFill.style.width = "50%";
    strengthFill.style.background = "#F39C12";
    strengthText.textContent = "Fair";

}
else if(score <= 6){

    strengthFill.style.width = "75%";
    strengthFill.style.background = "#3498DB";
    strengthText.textContent = "Good";

}
else{

    strengthFill.style.width = "100%";
    strengthFill.style.background = "#2ECC71";
    strengthText.textContent = "Strong";

}

        });

    }

    /* ==========================================
       PASSWORD MATCH
    ========================================== */

    function checkPasswordMatch(){

        if(!confirmPassword.value){

            passwordMatch.textContent = "";
            passwordMatch.className = "match-text";
            return;

        }

        if(password.value === confirmPassword.value){

            passwordMatch.textContent = "✔ Passwords Match";
            passwordMatch.className = "match-text match-success";

        }else{

            passwordMatch.textContent = "✖ Passwords Do Not Match";
            passwordMatch.className = "match-text match-error";

        }

    }

    if(password){

        password.addEventListener("input", checkPasswordMatch);

    }

    if(confirmPassword){

        confirmPassword.addEventListener("input", checkPasswordMatch);

    }

});