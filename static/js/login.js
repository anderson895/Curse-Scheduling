$(document).ready(function () {

  $("#frmLogin").submit(function (e) {
    e.preventDefault();

    $('#spinner').show();
    $('#btnLogin').prop('disabled', true);

    let formData = $(this).serializeArray();
    formData.push({ name: 'requestType', value: 'Login' });

    $.ajax({
      type: "POST",
      url: "controller/end-points/post_controller.php",
      data: $.param(formData),
      dataType: 'json',

      success: function (response) {

       if (response.status === "success") {

        const user_type = response.user_type.trim().toLowerCase();

        const routes = {
            faculty: "faculty/dashboard",
            "program chair": "programchair/dashboard", // added space
            dean: "dean/dashboard",
            gec: "gec/dashboard"
        };

        Swal.fire({
        title: 'Login Successful',
        text: 'Redirecting...',
        icon: 'success',
        timer: 1500,
        showConfirmButton: false,
        background: '#7f1d1d', // maroon (red-900)
        color: '#fff',
        iconColor: '#fecaca', // light red
        customClass: {
            popup: 'rounded-xl shadow-xl',
            title: 'text-white font-bold',
            htmlContainer: 'text-red-100'
        }
    }).then(() => {
        if (routes[user_type]) {
            window.location.href = routes[user_type];
        } else {
            Swal.fire({
                title: 'Error',
                text: 'Unknown user role',
                icon: 'error',
                background: '#7f1d1d',
                color: '#fff',
                iconColor: '#fecaca'
            });
        }
    });


    } else {
        Swal.fire({
            title: 'Login Failed',
            text: response.message || 'Invalid credentials',
            icon: 'error'
        });
    }

        },

      error: function () {
        Swal.fire({
          title: 'Server Error',
          text: 'Please try again later.',
          icon: 'error'
        });
      },

      complete: function () {
        $('#spinner').hide();
        $('#btnLogin').prop('disabled', false);
      }

    });

  });

});
