$(document).ready(function () {

    $("#createAccountForm").submit(function (e) {
        e.preventDefault();

        const password = $("#password").val();
        const confirmPassword = $("#confirm_password").val();

        if (password !== confirmPassword) {
            pcToast('The passwords you entered do not match.', 'error', {
                title: 'Passwords do not match'
            });
            return;
        }

        const $submit = $("#createAccountForm button[type=submit]");
        $submit.prop("disabled", true);

        const formData = new FormData(this);
        formData.append('requestType', 'CreateAccount');
        formData.append('user_status', '0');

        $.ajax({
            type: "POST",
            url: "controller/end-points/post_controller.php",
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function (response) {
                $submit.prop("disabled", false);

                if (response.status === "success") {
                    pcToast('Your account is awaiting approval. Redirecting to login…', 'success', {
                        title: 'Registration successful',
                        duration: 1500
                    });
                    setTimeout(() => {
                        window.location.href = "login";
                    }, 1200);
                } else {
                    pcToast(response.message || 'Please review your details and try again.', 'error', {
                        title: 'Registration failed'
                    });
                }
            },
            error: function () {
                $submit.prop("disabled", false);
                pcToast('An error occurred. Please try again.', 'error', {
                    title: 'Server error'
                });
            }
        });
    });

});
