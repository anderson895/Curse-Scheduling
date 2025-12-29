$(document).ready(function () {

  // ===============================
  // FETCH SUBJECTS
  // ===============================
  function fetchSubjects() {
    $.ajax({
      url: '../controller/end-points/get_controller.php',
      type: "GET",
      data: { requestType: "get_all_subjects" },
      dataType: "json",
      success: function (response) {
        const tbody = $("#subjectTableBody");
        tbody.empty();

        if (response.status === 200 && Array.isArray(response.data) && response.data.length > 0) {
          response.data.forEach(subject => {
            tbody.append(`
              <tr class="hover:bg-gray-50">
                <td class="p-3">${subject.program}</td>
                <td class="p-3">${subject.curriculum_year}</td>
                <td class="p-3">${subject.year_level}</td>
                <td class="p-3">${subject.semester}</td>
                <td class="p-3">${subject.subject_code}</td>
                <td class="p-3">${subject.subject_name}</td>
                <td class="p-3">${subject.lec_hours}</td>
                <td class="p-3">${subject.lab_hours}</td>
                <td class="p-3">${subject.lec_units}</td>
                <td class="p-3">${subject.lab_units}</td>
                <td class="p-3">${subject.prerequisite ?? 'N/A'}</td>
                <td class="p-3 text-center space-x-2">
                  <button class="editBtn bg-gray-700 cursor-pointer text-white px-3 py-1 rounded"
                    data-id="${subject.curriculum_id}">Edit</button>
                  <button class="deleteBtn bg-red-700 cursor-pointer text-white px-3 py-1 rounded"
                    data-id="${subject.curriculum_id}">Delete</button>
                </td>
              </tr>
            `);
          });
        } else {
          tbody.html(`<tr><td colspan="12" class="text-center p-6 text-gray-500">No subjects found.</td></tr>`);
        }
      },
      error: function (err) {
        console.error("Error fetching subjects:", err);
        $("#subjectTableBody").html(`<tr><td colspan="12" class="text-center p-6 text-red-500">Failed to load subjects.</td></tr>`);
      }
    });
  }

  fetchSubjects();

  // ===============================
  // OPEN / CLOSE ADD MODAL
  // ===============================
  $('#addBtn').click(() => $('#addSubjectModal').removeClass('hidden').addClass('flex'));
  $('#closeAddSubjectModal').click(() => {
    $('#addSubjectModal').addClass('hidden').removeClass('flex');
    $('#addSubjectForm')[0].reset();
  });
  $('#addSubjectModal').click(function (e) {
    if (e.target.id === 'addSubjectModal') {
      $(this).addClass('hidden').removeClass('flex');
      $('#addSubjectForm')[0].reset();
    }
  });

  // ===============================
// ADD SUBJECT
// ===============================
$('#addSubjectForm').submit(function (e) {
    e.preventDefault();

    const $form = $(this);
    const $submitBtn = $form.find('button[type="submit"]');

    // Disable submit button to prevent multiple clicks
    $submitBtn.prop('disabled', true);

    $.ajax({
        url: '../controller/end-points/post_controller.php',
        method: 'POST',
        data: $form.serialize() + '&requestType=add_subject',
        dataType: 'json',
        success: function (res) {
            if (res.status === 'success') {
                Swal.fire('Success', res.message, 'success').then(() => {
                    location.reload();
                });
            } else {
                Swal.fire('Error', res.message, 'error');
            }
        },
        error: function () {
            Swal.fire('Server Error', 'Something went wrong. Please try again.', 'error');
        },
        complete: function () {
            // Re-enable the submit button
            $submitBtn.prop('disabled', false);
        }
    });
});




  // ===============================
  // OPEN EDIT MODAL & POPULATE
  // ===============================
  $(document).on('click', '.editBtn', function () {
    const id = $(this).data('id');

    $.ajax({
      url: '../controller/end-points/get_controller.php',
      type: 'GET',
      data: { requestType: 'get_curriculum_by_id', curriculum_id: id },
      dataType: 'json',
      success: function (res) {
        if (res.status === 200) {
          const s = res.data;


          // Populate fields
          $('#edit_subject_id').val(s.curriculum_id);
          $('#edit_program').val(s.program);
          $('#edit_curriculum_year').val(s.curriculum_year);
          $('#edit_year_level').val(s.year_level);
          $('#edit_semester').val(s.semester);
          $('#edit_subject_code').val(s.subject_code);
          $('#edit_subject_name').val(s.subject_name);
          $('#edit_lec_hours').val(s.lec_hours);
          $('#edit_lab_hours').val(s.lab_hours);
          $('#edit_lec_units').val(s.lec_units);
          $('#edit_lab_units').val(s.lab_units);
          $('#edit_prerequisite').val(s.prerequisite);

          $('#editSubjectModal').removeClass('hidden').addClass('flex');
        } else {
          Swal.fire('Error', 'Failed to fetch subject details', 'error');
        }
      },
      error: function () {
        Swal.fire('Server Error', 'Something went wrong while fetching subject.', 'error');
      }
    });
  });

  $('#closeEditSubjectModal').click(() => {
    $('#editSubjectModal').addClass('hidden').removeClass('flex');
    $('#editSubjectForm')[0].reset();
  });

  // ===============================
// UPDATE SUBJECT
// ===============================
$('#editSubjectForm').submit(function (e) {
    e.preventDefault();

    const $form = $(this);
    const $submitBtn = $form.find('button[type="submit"]');
    $submitBtn.prop('disabled', true);

    $.ajax({
        url: '../controller/end-points/post_controller.php',
        type: 'POST',
        data: $form.serialize() + '&requestType=update_subject',
        dataType: 'json',
        success: function (res) {
            if (res.status === 'success') {
                Swal.fire('Updated!', res.message, 'success').then(() => {
                    location.reload();
                });
            } else {
                Swal.fire('Error', res.message, 'error');
            }
        },
        error: function () {
            Swal.fire('Server Error', 'Something went wrong. Please try again.', 'error');
        },
        complete: function () {
            $submitBtn.prop('disabled', false);
        }
    });
});

// ===============================
// DELETE SUBJECT
// ===============================
$(document).on('click', '.deleteBtn', function () {
    const id = $(this).data('id');

    Swal.fire({
        title: 'Are you sure?',
        text: "This will permanently delete the subject.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#7f1d1d',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '../controller/end-points/post_controller.php',
                type: 'POST',
                data: { requestType: 'delete_subject', curriculum_id: id },
                dataType: 'json',
                success: function (res) {
                    if (res.status === 'success') {
                        Swal.fire('Deleted!', res.message, 'success').then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire('Error', res.message, 'error');
                    }
                },
                error: function () {
                    Swal.fire('Server Error', 'Something went wrong. Please try again.', 'error');
                }
            });
        }
    });
});


});
