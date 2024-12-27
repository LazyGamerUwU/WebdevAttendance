<!-- Edit Student Modal -->
<div class="modal fade" id="editStudentModal" tabindex="-1" aria-labelledby="editStudentModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editStudentModalLabel">Edit Student</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form method="POST" action="students.php">
        <div class="modal-body">
          <input type="hidden" id="update_id" name="update_id">
          <div class="form-group">
            <label for="modal-student_number">Student Number</label>
            <input type="text" class="form-control" id="modal-student_number" name="student_number">
          </div>
          <div class="form-group">
            <label for="modal-last_name">Last Name</label>
            <input type="text" class="form-control" id="modal-last_name" name="last_name">
          </div>
          <div class="form-group">
            <label for="modal-first_name">First Name</label>
            <input type="text" class="form-control" id="modal-first_name" name="first_name">
          </div>
          <div class="form-group">
            <label for="modal-middle_name">Middle Name</label>
            <input type="text" class="form-control" id="modal-middle_name" name="middle_name">
          </div>
          <div class="form-group">
            <label for="modal-course">Course</label>
            <input type="text" class="form-control" id="modal-course" name="course">
          </div>
          <div class="form-group">
            <label for="modal-year_level">Year Level</label>
            <input type="number" class="form-control" id="modal-year_level" name="year_level">
          </div>
          <div class="form-group">
            <label for="modal-section">Section</label>
            <select class="form-select" id="modal-section" name="section">
              <option value="A">A</option>
              <option value="B">B</option>
              <option value="C">C</option>
            </select>
          </div>
          <div class="form-group">
            <label for="modal-birthdate">Birthdate</label>
            <input type="date" class="form-control" id="modal-birthdate" name="birthdate">
          </div>
          <div class="form-group">
            <label for="modal-sex">Sex</label>
            <select class="form-select" id="modal-sex" name="sex">
              <option value="Male">Male</option>
              <option value="Female">Female</option>
            </select>
          </div>
          <div class="form-group">
            <label for="modal-address">Address</label>
            <input type="text" class="form-control" id="modal-address" name="address">
          </div>
          <div class="form-group">
            <label for="modal-wmsu_email">WMSU Email</label>
            <input type="email" class="form-control" id="modal-wmsu_email" name="wmsu_email">
          </div>
          <div class="form-group">
            <label for="modal-personal_email">Personal Email</label>
            <input type="email" class="form-control" id="modal-personal_email" name="personal_email">
          </div>
          <div class="form-group">
            <label for="modal-status">Status</label>
            <select class="form-select" id="modal-status" name="status">
              <option value="Regular">Regular</option>
              <option value="Irregular">Irregular</option>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Update Student</button>
        </div>
      </form>
    </div>
  </div>
</div>
