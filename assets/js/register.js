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

            if(value.length >= 8) score++;
            if(/[A-Z]/.test(value)) score++;
            if(/[0-9]/.test(value)) score++;
            if(/[^A-Za-z0-9]/.test(value)) score++;

            switch(score){

                case 0:

                    strengthFill.style.width = "0%";
                    strengthFill.style.background = "#666";
                    strengthText.textContent = "Password Strength";
                    break;

                case 1:

                    strengthFill.style.width = "25%";
                    strengthFill.style.background = "#E74C3C";
                    strengthText.textContent = "Weak";
                    break;

                case 2:

                    strengthFill.style.width = "50%";
                    strengthFill.style.background = "#F39C12";
                    strengthText.textContent = "Fair";
                    break;

                case 3:

                    strengthFill.style.width = "75%";
                    strengthFill.style.background = "#3498DB";
                    strengthText.textContent = "Good";
                    break;

                case 4:

                    strengthFill.style.width = "100%";
                    strengthFill.style.background = "#2ECC71";
                    strengthText.textContent = "Strong";
                    break;

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