<div class="fade modal" aria-hidden="true" aria-labelledby="addStudentModalLabel" id="addStudentModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="studentForm" action="students.php" method="POST">
        <div class="modal-header">
          <h5 class="modal-title" id="addStudentModalLabel">Add New Student</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <!-- Student Number -->
          <div class="mb-3">
            <label class="form-label" for="student_number">Student Number</label>
            <input type="text" name="student_number" class="form-control" value="<?= htmlspecialchars($student_number ?? '') ?>">
            <span class="error-message text-danger"><?php echo $student_number_err ?? ''; ?></span>
          </div>

          <!-- Last Name -->
          <div class="mb-3">
            <label class="form-label" for="last_name">Last Name</label>
            <input type="text" name="last_name" class="form-control" value="<?= htmlspecialchars($last_name ?? '') ?>">
            <span class="error-message text-danger"><?php echo $last_name_err ?? ''; ?></span>
          </div>

          <!-- First Name -->
          <div class="mb-3">
            <label class="form-label" for="first_name">First Name</label>
            <input type="text" name="first_name" class="form-control" value="<?= htmlspecialchars($first_name ?? '') ?>">
            <span class="error-message text-danger"><?php echo $first_name_err ?? ''; ?></span>
          </div>

          <!-- Middle Name -->
          <div class="mb-3">
            <label class="form-label" for="middle_name">Middle Name</label>
            <input type="text" name="middle_name" class="form-control" value="<?= htmlspecialchars($middle_name ?? '') ?>">
            <span class="error-message text-danger"><?php echo $middle_name_err ?? ''; ?></span>
          </div>

          <!-- Course -->
          <div class="mb-3">
            <label class="form-label" for="course">Course</label>
            <input type="text" name="course" class="form-control" value="<?= htmlspecialchars($course ?? '') ?>">
            <span class="error-message text-danger"><?php echo $course_err ?? ''; ?></span>
          </div>

          <!-- Year Level -->
          <div class="mb-3">
            <label class="form-label" for="year_level">Year Level</label>
            <input type="number" name="year_level" class="form-control" value="<?= htmlspecialchars($year_level ?? '') ?>">
            <span class="error-message text-danger"><?php echo $year_level_err ?? ''; ?></span>
          </div>

          <!-- Section -->
          <div class="mb-3">
            <label class="form-label" for="section">Section</label>
            <select class="form-select" id="section" name="section">
              <option value="" disabled <?= empty($section) ? 'selected' : ''; ?>>Select Section</option>
              <option value="A" <?= (isset($section) && $section === 'A') ? 'selected' : ''; ?>>A</option>
              <option value="B" <?= (isset($section) && $section === 'B') ? 'selected' : ''; ?>>B</option>
              <option value="C" <?= (isset($section) && $section === 'C') ? 'selected' : ''; ?>>C</option>
            </select>
            <span class="error-message text-danger"><?php echo $section_err ?? ''; ?></span>
          </div>

          <!-- Birthdate -->
          <div class="mb-3">
            <label class="form-label" for="birthdate">Birthdate</label>
            <input class="form-control" id="birthdate" name="birthdate" type="date" value="<?= htmlspecialchars($birthdate ?? '') ?>">
            <span class="error-message text-danger"><?php echo $birthdate_err ?? ''; ?></span>
          </div>

          <!-- Sex -->
          <div class="mb-3">
            <label class="form-label" for="sex">Sex</label>
            <select class="form-select" id="sex" name="sex">
              <option value="" disabled <?= empty($sex) ? 'selected' : ''; ?>>Select Sex</option>
              <option value="Male" <?= (isset($sex) && $sex === 'Male') ? 'selected' : ''; ?>>Male</option>
              <option value="Female" <?= (isset($sex) && $sex === 'Female') ? 'selected' : ''; ?>>Female</option>
            </select>
            <span class="error-message text-danger"><?php echo $sex_err ?? ''; ?></span>
          </div>

          <!-- Address -->
          <div class="mb-3">
            <label class="form-label" for="address">Address</label>
            <input type="text" name="address" class="form-control" value="<?= htmlspecialchars($address ?? '') ?>">
            <span class="error-message text-danger"><?php echo $address_err ?? ''; ?></span>
          </div>

          <!-- WMSU Email -->
          <div class="mb-3">
            <label class="form-label" for="wmsu_email">WMSU Email</label>
            <input type="text" name="wmsu_email" class="form-control" value="<?= htmlspecialchars($wmsu_email ?? '') ?>">
            <span class="error-message text-danger"><?php echo $wmsu_email_err ?? ''; ?></span>
          </div>

          <!-- Personal Email -->
          <div class="mb-3">
            <label class="form-label" for="personal_email">Personal Email</label>
            <input type="text" name="personal_email" class="form-control" value="<?= htmlspecialchars($personal_email ?? '') ?>">
            <span class="error-message text-danger"><?php echo $personal_email_err ?? ''; ?></span>
          </div>

          <!-- Status -->
          <div class="mb-3">
            <label class="form-label" for="status">Status</label>
            <select class="form-select" id="status" name="status">
              <option value="" disabled <?= empty($status) ? 'selected' : ''; ?>>Select Status</option>
              <option value="Regular" <?= (isset($status) && $status === 'Regular') ? 'selected' : ''; ?>>Regular</option>
              <option value="Irregular" <?= (isset($status) && $status === 'Irregular') ? 'selected' : ''; ?>>Irregular</option>
            </select>
            <span class="error-message text-danger"><?php echo $status_err ?? ''; ?></span>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="closeModalBtn">Close</button>
          <button class="btn btn-primary" type="submit">Add Student</button>
        </div>
      </form>
    </div>
  </div>
</div>
