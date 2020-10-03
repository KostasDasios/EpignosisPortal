<div class="container">
  <h2>Application Status (Id: <?= $id ?>)</h2>
  <?php if($status == 2): ?>
    <div class="alert alert-success">
      Application Status is <strong>Approved!</strong>
    </div>
  <?php elseif ($status == 3): ?>
    <div class="alert alert-danger">
      Application Status is <strong>Rejected!</strong>
    </div>
  <?php else: ?>
    <div class="alert alert-info">
      Application Status is <strong>Pending!</strong>
    </div>
  <?php endif; ?>
</div>