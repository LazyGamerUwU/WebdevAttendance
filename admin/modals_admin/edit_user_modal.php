<div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editUserModalLabel">Edit User</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form method="POST" action="users.php">
        <div class="modal-body">
          <input type="hidden" id="update_id" name="update_id">
          <div class="form-group">
            <label for="modal-username">Username</label>
            <input type="text" class="form-control" id="modal-username" name="username">
          </div>
          <div class="form-group">
            <label for="modal-password">Password</label>
            <input type="password" class="form-control" id="modal-password" name="password">
          </div>
          <div class="form-group">
            <label for="modal-role">Role</label>
            <select class="form-select" id="modal-role" name="role">
              <option value="admin">Admin</option>
              <option value="officer">Officer</option>
            </select>
          </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Update User</button>
        </div>
      </form>
    </div>
  </div>
</div>
</div>
</div>