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
            "program chair": "programchair/dashboard",
            dean: "dean/dashboard",
            gec: "gec/dashboard"
          };

          if (!routes[user_type]) {
            pcToast('Unknown user role: ' + user_type, 'error');
            $('#spinner').hide();
            $('#btnLogin').prop('disabled', false);
            return;
          }

          pcToast('Welcome back! Redirecting to your dashboard…', 'success', {
            title: 'Login successful',
            duration: 1200
          });

          setTimeout(() => {
            window.location.href = routes[user_type];
          }, 900);

        } else {
          pcToast(response.message || 'Invalid credentials', 'error', {
            title: 'Login failed'
          });
          $('#spinner').hide();
          $('#btnLogin').prop('disabled', false);
        }

      },

      error: function () {
        pcToast('Please try again later.', 'error', { title: 'Server error' });
        $('#spinner').hide();
        $('#btnLogin').prop('disabled', false);
      }

    });

  });

});
