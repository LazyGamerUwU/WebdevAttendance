
<div class="modal fade" id="editEventModal" tabindex="-1" aria-labelledby="editEventModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editEventModalLabel">Edit Event</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="editEventForm" method="POST" action="events.php">
          <input type="hidden" id="update_id" name="update_id">

          <div class="mb-3">
            <label for="modal-title" class="form-label">Title</label>
            <input type="text" class="form-control" id="modal-title" name="title">
          </div>

          <div class="mb-3">
            <label for="modal-description" class="form-label">Description</label>
            <textarea class="form-control" id="modal-description" name="description"></textarea>
          </div>

          <div class="mb-3">
            <label for="modal-organizers" class="form-label">Organizers</label>
            <input type="text" class="form-control" id="modal-organizers" name="organizers">
          </div>

          <div class="mb-3">
            <label for="modal-event_date" class="form-label">Event Date</label>
            <input type="date" class="form-control" id="modal-event_date" name="event_date">
          </div>

          <div class="mb-3">
            <label for="modal-starttime" class="form-label">Start Time</label>
            <input type="time" class="form-control" id="modal-starttime" name="starttime">
          </div>

          <div class="mb-3">
            <label for="modal-endtime" class="form-label">End Time</label>
            <input type="time" class="form-control" id="modal-endtime" name="endtime">
          </div>

          <div class="mb-3">
            <label for="modal-venue" class="form-label">Venue</label>
            <input type="text" class="form-control" id="modal-venue" name="venue">
          </div>
          
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary">Update Event</button>
          </div>
          
        </form>
      </div>
    </div>
  </div>
</div>


