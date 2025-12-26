$(document).ready(function () {

  // ==========================
  // OPEN MODAL
  // ==========================
  $('#addBtn').on('click', function () {
    $('#addSubjectModal').removeClass('hidden').addClass('flex');
  });

  // ==========================
  // CLOSE MODAL
  // ==========================
  $('#closeAddSubjectModal').on('click', function () {
    $('#addSubjectModal').addClass('hidden').removeClass('flex');
    $('#addSubjectForm')[0].reset();
  });

  // CLOSE WHEN CLICK OUTSIDE
  $('#addSubjectModal').on('click', function (e) {
    if (e.target.id === 'addSubjectModal') {
      $(this).addClass('hidden').removeClass('flex');
      $('#addSubjectForm')[0].reset();
    }
  });

  // ==========================
  // SUBMIT FORM (AJAX)
  // ==========================
  $('#addSubjectForm').on('submit', function (e) {
    e.preventDefault();

    $.ajax({
      url: '../controller/end-points/post_controller.php',
      method: 'POST',
      data: $(this).serialize() + '&requestType=add_subject',
      dataType: 'json',
      success: function (response) {

        if (response.status === 'success') {

          Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: response.message || 'Subject added successfully',
            confirmButtonColor: '#7f1d1d' // Tailwind red-900
          }).then(() => {
            $('#addSubjectModal').addClass('hidden').removeClass('flex');
            $('#addSubjectForm')[0].reset();

            // OPTIONAL: reload subject list
            location.reload();
          });

        } else {

          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: response.message || 'Failed to add subject',
            confirmButtonColor: '#7f1d1d'
          });

        }
      },
      error: function () {
        Swal.fire({
          icon: 'error',
          title: 'Server Error',
          text: 'Something went wrong. Please try again.',
          confirmButtonColor: '#7f1d1d'
        });
      }
    });
  });

});



// GET ALL SUBJECTS AND POPULATE TABLE
$(document).ready(function() {

  // ===============================
  // Function to fetch all subjects
  // ===============================
  function fetchSubjects() {
    $.ajax({
      url: '../controller/end-points/get_controller.php',
      type: "GET",
      data: { requestType: "get_all_subjects" },
      dataType: "json",
      success: function(response) {
        const tbody = $("#subjectTableBody");
        tbody.empty(); // Clear previous content

        if(response.status === 200 && response.data.length > 0) {
          response.data.forEach(subject => {
            tbody.append(`
              <tr class="hover:bg-gray-50">
                <td class="p-3">${subject.subject_code}</td>
                <td class="p-3">${subject.subject_name}</td>
                <td class="p-3">${subject.subject_unit}</td>
                <td class="p-3 text-center">
                  <button class="editBtn px-3 py-1 bg-gray-700 cursor-pointer text-white rounded-md text-sm" data-id="${subject.subject_id}">Edit</button>
                  <button class="deleteBtn px-3 py-1 bg-red-700 cursor-pointer text-white rounded-md text-sm" data-id="${subject.subject_id}">Delete</button>
                </td>
              </tr>
            `);
          });
        } else {
          tbody.append(`
            <tr>
              <td colspan="4" class="text-center p-6 text-gray-500">No subjects found.</td>
            </tr>
          `);
        }
      },
      error: function(err) {
        console.error("Error fetching subjects:", err);
        $("#subjectTableBody").html(`
          <tr>
            <td colspan="4" class="text-center p-6 text-red-500">Failed to load subjects.</td>
          </tr>
        `);
      }
    });
  }

  // Initial fetch
  fetchSubjects();

  // ===============================
  // Add Subject Modal Open/Close
  // ===============================
  $('#addBtn').on('click', function() {
    $('#addSubjectModal').removeClass('hidden').addClass('flex');
  });

  $('#closeAddSubjectModal').on('click', function() {
    $('#addSubjectModal').removeClass('flex').addClass('hidden');
    $('#addSubjectForm')[0].reset();
  });


  // ===============================
  // ===============================
// Open Edit Modal and Populate Fields
// ===============================
$(document).on('click', '.editBtn', function() {
  const id = $(this).data('id');

  // Fetch subject details by ID (you can also pass data from the table directly)
  $.ajax({
    url: '../controller/end-points/get_controller.php',
    type: 'GET',
    data: { requestType: 'get_subject_by_id', subject_id: id },
    dataType: 'json',
    success: function(response) {
      if(response.status === 200) {
        const subject = response.data;

        // Populate form fields
        $('#edit_subject_id').val(subject.subject_id);
        $('#edit_subject_code').val(subject.subject_code);
        $('#edit_subject_name').val(subject.subject_name);
        $('#edit_subject_unit').val(subject.subject_unit);

        // Open modal
        $('#editSubjectModal').removeClass('hidden').addClass('flex');
      } else {
        alert('Failed to fetch subject details.');
      }
    },
    error: function() {
      alert('Server error while fetching subject.');
    }
  });
});

// ===============================
// Close Edit Modal
// ===============================
$('#closeEditSubjectModal').on('click', function() {
  $('#editSubjectModal').addClass('hidden').removeClass('flex');
  $('#editSubjectForm')[0].reset();
});

// ===============================
// Submit Edit Form (AJAX)
// ===============================
$('#editSubjectForm').on('submit', function(e) {
  e.preventDefault();

  $.ajax({
    url: '../controller/end-points/post_controller.php',
    type: 'POST',
    data: $(this).serialize() + '&requestType=update_subject',
    dataType: 'json',
    success: function(response) {
      if(response.status === 'success') {
        Swal.fire({
          icon: 'success',
          title: 'Updated!',
          text: response.message || 'Subject updated successfully',
          confirmButtonColor: '#7f1d1d'
        }).then(() => {
          $('#editSubjectModal').addClass('hidden').removeClass('flex');
          $('#editSubjectForm')[0].reset();
          fetchSubjects(); // Refresh table
        });
      } else {
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: response.message || 'Failed to update subject',
          confirmButtonColor: '#7f1d1d'
        });
      }
    },
    error: function() {
      Swal.fire({
        icon: 'error',
        title: 'Server Error',
        text: 'Something went wrong. Please try again.',
        confirmButtonColor: '#7f1d1d'
      });
    }
  });
});
  // ===============================
  // Delete Subject Handler
  // ===============================

  // ===============================
// Delete Subject
// ===============================
$(document).on('click', '.deleteBtn', function() {
  const id = $(this).data('id');

  Swal.fire({
    title: 'Are you sure?',
    text: "This will permanently delete the subject.",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#7f1d1d', // Tailwind red-900
    cancelButtonColor: '#6b7280', // Tailwind gray-500
    confirmButtonText: 'Yes, delete it!',
    cancelButtonText: 'Cancel'
  }).then((result) => {
    if (result.isConfirmed) {
      $.ajax({
        url: '../controller/end-points/post_controller.php',
        type: 'POST',
        data: { requestType: 'delete_subject', subject_id: id },
        dataType: 'json',
        success: function(response) {
          if(response.status === 'success') {
            Swal.fire({
              icon: 'success',
              title: 'Deleted!',
              text: response.message || 'Subject deleted successfully',
              confirmButtonColor: '#7f1d1d'
            });
            fetchSubjects(); // Refresh table
          } else {
            Swal.fire({
              icon: 'error',
              title: 'Error',
              text: response.message || 'Failed to delete subject',
              confirmButtonColor: '#7f1d1d'
            });
          }
        },
        error: function() {
          Swal.fire({
            icon: 'error',
            title: 'Server Error',
            text: 'Something went wrong. Please try again.',
            confirmButtonColor: '#7f1d1d'
          });
        }
      });
    }
  });
});


});
