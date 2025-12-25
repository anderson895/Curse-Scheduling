$(document).ready(function () {

    $("#createAccountForm").submit(function (e) {
        e.preventDefault();

        const password = $("#password").val();
        const confirmPassword = $("#confirm_password").val();

        if (password !== confirmPassword) {
            Swal.fire({
                icon: 'error',
                title: 'Passwords do not match',
                confirmButtonColor: '#3085d6',
            });
            return;
        }

        // Disable submit button to prevent double submission
        $("#createAccountForm button[type=submit]").prop("disabled", true);

        // Show Swal loader
        Swal.fire({
            title: 'Processing...',
            text: 'Please wait while we register your account.',
            allowOutsideClick: false,
            allowEscapeKey: false,
            didOpen: () => Swal.showLoading()
        });

        // Serialize form data using FormData
        const formData = new FormData(this);
        formData.append('requestType', 'CreateAccount');
        formData.append('user_status', '0');

        $.ajax({
            type: "POST",
            url: "controller/end-points/post_controller.php",
            data: formData,
            processData: false, // Important for FormData
            contentType: false, // Important for FormData
            dataType: 'json',
            success: function (response) {
                Swal.close(); // close the loader
                $("#createAccountForm button[type=submit]").prop("disabled", false);

                if (response.status === "success") {
                    Swal.fire({
                        icon: 'success',
                        title: 'Registration Successful',
                        confirmButtonColor: '#3085d6'
                    }).then(() => {
                        window.location.href = "login"; 
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Registration Failed',
                        text: response.message,
                        confirmButtonColor: '#3085d6'
                    });
                }
            },
            error: function () {
                Swal.close();
                $("#createAccountForm button[type=submit]").prop("disabled", false);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred. Please try again.',
                    confirmButtonColor: '#3085d6'
                });
            }
        });
    });

});
