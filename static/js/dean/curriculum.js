$(document).ready(function () {

  // ---------------------------
  // Load Subjects
  // ---------------------------
  function loadSubjects() {
  $.ajax({
    url: '../controller/end-points/get_controller.php',
    method: 'GET',
    data: { requestType: 'get_all_subjects' },
    dataType: 'json',
    success: function (res) {
      let options = '';
      res.data.forEach(s => {
        options += `<option value="${s.subject_id}">${s.subject_code} - ${s.subject_name} (${s.subject_unit})</option>`;
      });
      $('#subjectSelect, #editSubjectSelect').html(options);

      // Initialize Select2 after options are loaded
      $('#subjectSelect, #editSubjectSelect').select2({
        placeholder: "Select Subject(s)",
        width: '100%'
      });
    }
  });
}

  loadSubjects();

  // ---------------------------
  // Load Curriculum Table
  // ---------------------------
  function fetchCurriculum() {
    $.ajax({
      url: '../controller/end-points/get_controller.php',
      method: 'GET',
      data: { requestType: 'get_curriculum' },
      dataType: 'json',
      success: function (res) {
        let html = '';
        res.data.forEach(sub => {
          html += `
            <tr>
              <td class="p-2">${sub.year_semester}</td>
              <td class="p-2">${sub.subject_code}</td>
              <td class="p-2">${sub.subject_name}</td>
              <td class="p-2">${sub.subject_unit}</td>
              <td class="p-2 flex gap-2">
                <button class="editBtn px-2 py-1 bg-yellow-400 rounded" data-id="${sub.id}">Edit</button>
                <button class="deleteBtn px-2 py-1 bg-red-600 text-white rounded" data-id="${sub.id}">Delete</button>
              </td>
            </tr>`;
        });
        $('#curriculumBody').html(html);
      }
    });
  }
  fetchCurriculum();

  // ---------------------------
  // Open/Close Modals
  // ---------------------------
  $('#addBtn').click(() => $('#addCurriculumModal').show());
  $('#closeAddCurriculumModal').click(() => $('#addCurriculumModal').hide().find('form')[0].reset());

  $('#closeEditCurriculumModal').click(() => $('#editCurriculumModal').hide().find('form')[0].reset());

  // ---------------------------
  // Add Curriculum
  // ---------------------------
  $('#addCurriculumForm').submit(function (e) {
    e.preventDefault();
    let year_semester = $('input[name="year_semester"]').val();
    let subjects = $('#subjectSelect').val();
    if (!year_semester || !subjects || subjects.length === 0) {
      alert("Please fill in Year/Semester and select at least one subject.");
      return;
    }

    $.ajax({
      url: '../controller/end-points/post_controller.php',
      method: 'POST',
      data: { year_semester: year_semester, subject_ids: subjects, requestType: 'add_curriculum' },
      success: function () {
        fetchCurriculum();
        $('#addCurriculumModal').hide().find('form')[0].reset();
      }
    });
  });

  // ---------------------------
  // Edit Curriculum
  // ---------------------------
  $(document).on('click', '.editBtn', function () {
    let id = $(this).data('id');
    $.ajax({
      url: '../controller/end-points/get_controller.php',
      method: 'GET',
      data: { requestType: 'get_curriculum_by_id', id: id },
      dataType: 'json',
      success: function (res) {
        let form = $('#editCurriculumForm');
        form.find('input[name="id"]').val(res.data.id);
        form.find('input[name="year_semester"]').val(res.data.year_semester);
        form.find('#editSubjectSelect').val([res.data.subject_id]); // array for multiple select
        $('#editCurriculumModal').show();
      }
    });
  });

  $('#editCurriculumForm').submit(function (e) {
    e.preventDefault();
    $.ajax({
      url: '../controller/end-points/post_controller.php',
      method: 'POST',
      data: $(this).serialize() + '&requestType=update_curriculum',
      success: function () {
        fetchCurriculum();
        $('#editCurriculumModal').hide();
      }
    });
  });

  // ---------------------------
  // Delete Curriculum
  // ---------------------------
  $(document).on('click', '.deleteBtn', function () {
    if (!confirm('Are you sure you want to delete this curriculum?')) return;
    let id = $(this).data('id');
    $.ajax({
      url: '../controller/end-points/post_controller.php',
      method: 'POST',
      data: { id: id, requestType: 'delete_curriculum' },
      success: function () {
        fetchCurriculum();
      }
    });
  });

});
