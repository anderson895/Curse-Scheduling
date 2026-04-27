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

        // Initialize Select2 once
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
              <td class="p-2">
                <div class="flex gap-2">
                  <button class="editBtn pc-btn pc-btn-ghost pc-btn-sm" data-id="${sub.id}"><span class="material-icons">edit</span> Edit</button>
                  <button class="deleteBtn pc-btn pc-btn-danger pc-btn-sm" data-id="${sub.id}"><span class="material-icons">delete</span> Delete</button>
                </div>
              </td>
            </tr>`;
        });
        $('#curriculumBody').html(html);
      }
    });
  }
  fetchCurriculum();

  // ---------------------------
  // Open Add Modal
  // ---------------------------
  $('#addBtn').click(() => {
    // Clear previous selections and input
    $('#addCurriculumForm')[0].reset();
    $('#subjectSelect').val(null).trigger('change'); // clear Select2
    $('#addCurriculumModal').removeClass('hidden');
  });

  $('#closeAddCurriculumModal').click(() => {
    $('#addCurriculumModal').addClass('hidden');
    $('#addCurriculumForm')[0].reset();
    $('#subjectSelect').val(null).trigger('change'); // clear Select2
  });

  $('#closeEditCurriculumModal').click(() => {
    $('#editCurriculumModal').addClass('hidden');
    $('#editCurriculumForm')[0].reset();
    $('#editSubjectSelect').val(null).trigger('change'); // clear Select2
  });

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
        $('#addCurriculumModal').addClass('hidden');
        $('#addCurriculumForm')[0].reset();
        $('#subjectSelect').val(null).trigger('change'); // clear Select2
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
        form.find('#editSubjectSelect').val([res.data.subject_id]).trigger('change'); // set Select2 value
        $('#editCurriculumModal').removeClass('hidden');
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
        $('#editCurriculumModal').addClass('hidden');
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
