$(document).ready(function () {

  // ===============================
  // VARIABLES
  // ===============================
  let subjects = []; // store all subjects
  let currentPage = 1;
  const rowsPerPage = 10;

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
        if (response.status === 200 && Array.isArray(response.data)) {
          subjects = response.data;
          renderTable();
          renderPagination();
        } else {
          $("#subjectTableBody").html(`<tr><td colspan="12" class="text-center p-6 text-gray-500">No subjects found.</td></tr>`);
          $("#pagination").empty();
        }
      },
      error: function () {
        $("#subjectTableBody").html(`<tr><td colspan="12" class="text-center p-6 text-red-500">Failed to load subjects.</td></tr>`);
      }
    });
  }

  // ===============================
  // RENDER TABLE
  // ===============================
  function renderTable() {
    const tbody = $("#subjectTableBody");
    tbody.empty();

    const searchValue = $('#subjectSearch').val()?.toLowerCase() || '';
    const filteredSubjects = subjects.filter(s =>
      s.program.toLowerCase().includes(searchValue) ||
      s.curriculum_year.toLowerCase().includes(searchValue) ||
      s.year_level.toString().includes(searchValue) ||
      s.semester.toLowerCase().includes(searchValue) ||
      s.subject_code.toLowerCase().includes(searchValue) ||
      s.subject_name.toLowerCase().includes(searchValue)
    );

    const start = (currentPage - 1) * rowsPerPage;
    const paginatedSubjects = filteredSubjects.slice(start, start + rowsPerPage);

    if (paginatedSubjects.length === 0) {
      tbody.html(`<tr><td colspan="12" class="text-center p-6 text-gray-500">No subjects found.</td></tr>`);
      return;
    }

    paginatedSubjects.forEach(subject => {
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
            <button class="editBtn bg-gray-700 cursor-pointer text-white px-3 py-1 rounded" data-id="${subject.curriculum_id}">Edit</button>
            <button class="deleteBtn bg-red-700 cursor-pointer text-white px-3 py-1 rounded" data-id="${subject.curriculum_id}">Delete</button>
          </td>
        </tr>
      `);
    });
  }

function renderPagination() {
  const searchValue = $('#subjectSearch').val()?.toLowerCase() || '';
  const filteredSubjects = subjects.filter(s =>
    s.program.toLowerCase().includes(searchValue) ||
    s.curriculum_year.toLowerCase().includes(searchValue) ||
    s.year_level.toString().includes(searchValue) ||
    s.semester.toLowerCase().includes(searchValue) ||
    s.subject_code.toLowerCase().includes(searchValue) ||
    s.subject_name.toLowerCase().includes(searchValue)
  );

  const pageCount = Math.ceil(filteredSubjects.length / rowsPerPage);
  const pagination = $("#pagination");
  pagination.empty();

  if (pageCount <= 1) return;

  const maxPageButtons = 5; // Max visible page buttons at a time
  let startPage = Math.floor((currentPage - 1) / maxPageButtons) * maxPageButtons + 1;
  let endPage = Math.min(startPage + maxPageButtons - 1, pageCount);

  // Prev group button
  if (startPage > 1) {
    pagination.append(`
      <button class="cursor-pointer px-3 py-1 rounded border bg-white hover:bg-gray-100 text-gray-700" data-page="${startPage - 1}">
        &laquo;
      </button>
    `);
  }

  // Prev page button
  const prevDisabled = currentPage === 1 ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer';
  pagination.append(`
    <button class="px-3 py-1 rounded border bg-white hover:bg-gray-100 ${prevDisabled}" data-page="${currentPage - 1}">
      Prev
    </button>
  `);

  // Page numbers
  for (let i = startPage; i <= endPage; i++) {
    const active = i === currentPage 
      ? 'bg-red-900 text-white border-red-900 cursor-pointer' 
      : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-100 cursor-pointer';
    pagination.append(`
      <button class="px-3 py-1 border rounded ${active}" data-page="${i}">
        ${i}
      </button>
    `);
  }

  // Next page button
  const nextDisabled = currentPage === pageCount ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer';
  pagination.append(`
    <button class="px-3 py-1 rounded border bg-white hover:bg-gray-100 ${nextDisabled}" data-page="${currentPage + 1}">
      Next
    </button>
  `);

  // Next group button
  if (endPage < pageCount) {
    pagination.append(`
      <button class="cursor-pointer px-3 py-1 rounded border bg-white hover:bg-gray-100 text-gray-700" data-page="${endPage + 1}">
        &raquo;
      </button>
    `);
  }
}


// Pagination click remains the same
$(document).on('click', '#pagination button', function () {
  const page = $(this).data('page');
  const totalPages = Math.ceil(subjects.length / rowsPerPage);
  if (page < 1 || page > totalPages) return;
  currentPage = page;
  renderTable();
  renderPagination();
});


  // ===============================
  // SEARCH FILTER
  // ===============================
  $(document).on('keyup', '#subjectSearch', function () {
    currentPage = 1;
    renderTable();
    renderPagination();
  });

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
    $submitBtn.prop('disabled', true);

    $.ajax({
      url: '../controller/end-points/post_controller.php',
      method: 'POST',
      data: $form.serialize() + '&requestType=add_subject',
      dataType: 'json',
      success: function (res) {
        if (res.status === 'success') {
          Swal.fire('Success', res.message, 'success').then(() => fetchSubjects());
          $('#addSubjectModal').addClass('hidden').removeClass('flex');
          $form[0].reset();
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

          console.log(s);

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
          Swal.fire('Updated!', res.message, 'success').then(() => fetchSubjects());
          $('#editSubjectModal').addClass('hidden').removeClass('flex');
          $form[0].reset();
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
              Swal.fire('Deleted!', res.message, 'success').then(() => fetchSubjects());
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

  // ===============================
  // INITIAL FETCH
  // ===============================
  fetchSubjects();
});
