<h1>Create Application</h1>
<form method='post' action='#'>
    <div class="form-group">
        <label for="date_from">Vacation Start</label>
        <input type="datetime-local" class="form-control" id="date_from" name="date_from" required>
    </div>
    <div class="form-group">
        <label for="date_to">Vacation End</label>
        <input type="datetime-local" class="form-control" id="date_to" name="date_to" required>
    </div>
    <div class="form-group">
        <label for="reason">Reason</label>
        <textarea id="reason" class="form-control" name="reason" placeholder="Enter a reason" rows="4" cols="50" required></textarea>
    </div>
    <button type="submit" class="btn btn-primary">Submit</button>
</form>