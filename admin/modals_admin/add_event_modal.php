<div class="modal fade" id="addEventModal" tabindex="-1" aria-labelledby="addEventModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form  id="eventForm" method="POST" action="events.php">
        <div class="modal-header">
          <h5 class="modal-title" id="addEventModalLabel">Add Event</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">

          <!-- Event Title -->
          <div class="mb-3">
            <label for="title" class="form-label">Event Title</label>
            <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($title) ?>">
            <span class="error-message text-danger"><?php echo $title_err ?? ''; ?></span>
          </div>

          <!-- Event Description -->
          <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea name="description" class="form-control"><?= htmlspecialchars($description) ?></textarea>
            <span class="error-message text-danger"><?php echo $description_err?? ''; ?></span>
          </div>

          <!-- Organizers -->
          <div class="mb-3">
            <label for="organizers" class="form-label">Organizers</label>
            <input type="text" name="organizers" class="form-control" value="<?= htmlspecialchars($organizers) ?>">
            <span class="error-message text-danger"><?php echo $organizers_err ?? ''; ?></span>
          </div>

          <div class="row">
          <!-- Start Time -->
          <div class="col-md-6 mb-3">
            <label for="starttime" class="form-label">Start Time</label>
            <input type="time" name="starttime" class="form-control" value="<?= htmlspecialchars($starttime) ?>">
            <span class="error-message text-danger"><?php echo $starttime_err ?? ''; ?></span>
          </div>

          <!-- End Time -->
          <div class="col-md-6 mb-3">
            <label for="endtime" class="form-label">End Time</label>
            <input type="time" name="endtime" class="form-control" value="<?= htmlspecialchars($endtime) ?>">
            <span class="error-message text-danger"><?php echo $endtime_err ?? ''; ?></span>
          </div>
        </div>


          <!-- Venue -->
          <div class="mb-3">
            <label for="venue" class="form-label">Venue</label>
            <input type="text" name="venue" class="form-control" value="<?= htmlspecialchars($venue) ?>">
            <span class="error-message text-danger"><?php echo $venue_err ?? ''; ?></span>
          </div>

          <!-- Event Date -->
          <div class="mb-3">
            <label for="event_date" class="form-label">Event Date</label>
            <input type="date" name="event_date" class="form-control" value="<?= htmlspecialchars($event_date) ?>">
            <span class="error-message text-danger"><?php echo $event_date_err ?? ''; ?></span>
          </div>

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="closeModalBtn">Close</button>
          <button type="submit" class="btn btn-primary">Save Event</button>
        </div>
      </form>
    </div>
  </div>
</div>

