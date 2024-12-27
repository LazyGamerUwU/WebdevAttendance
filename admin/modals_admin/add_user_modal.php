<div class="fade modal" aria-hidden="true" aria-labelledby="addUserModalLabel" id="addUserModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="userForm" action="users.php" method="POST">
        <div class="modal-header">
          <h5 class="modal-title" id="addUserModalLabel">Add New User</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          
          <!-- Username -->
          <div class="mb-3">
            <label class="form-label" for="username">Username</label>
            <input type="text" name="username" class="form-control">
            <span class="error-message text-danger"><?php echo $username_err ?? ''; ?></span>
          </div>

          <!-- Password -->
          <div class="mb-3">
            <label class="form-label" for="password">Password</label>
            <input type="password" name="password" class="form-control" value="<?= htmlspecialchars($password ?? '') ?>">
            <span class="error-message text-danger"><?php echo $password_err ?? ''; ?></span>
          </div>

          <!-- Role -->
          <div class="mb-3">
            <label class="form-label" for="role">Role</label>
            <select class="form-select" id="role" name="role">
              <option value="" disabled <?= empty($role) ? 'selected' : ''; ?>>Select Role</option>
              <option value="admin" <?= (isset($role) && $role === 'admin') ? 'selected' : ''; ?>>Admin</option>
              <option value="officer" <?= (isset($role) && $role === 'officer') ? 'selected' : ''; ?>>Officer</option>
            </select>
            <span class="error-message text-danger"><?php echo $role_err ?? ''; ?></span>
          </div>

          
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="closeModalBtn">Close</button>
          <button class="btn btn-primary" type="submit">Add User</button>
        </div>
      </form>
    </div>
  </div>
</div>