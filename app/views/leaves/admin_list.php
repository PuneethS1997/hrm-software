<?php require '../app/views/layouts/header.php'; ?>
<?php require '../app/views/layouts/sidebar.php'; ?>

<div id="main-content" class="main-content">
  <div class="content-wrapper container-fluid">

  <button class="btn btn-dark mb-3"
      data-bs-toggle="offcanvas"
      data-bs-target="#adminCalendarCanvas">
      📅 Leave Calendar View
      </button>

      <div class="offcanvas offcanvas-end" id="adminCalendarCanvas">
        <div class="offcanvas-header">
        <h5>Company Leave Calendar</h5>
        <button class="btn-close" data-bs-dismiss="offcanvas"></button>
        </div>

        <div class="offcanvas-body">
      

        <div id="adminCalendar"></div>
        </div>
        </div>

    <div class="container mt-4">
      <h4>Pending Leave Requests</h4>

      <table class="table table-striped">
        <thead>
          <tr>
            <th>Employee</th>
            <th>Type</th>
            <th>Dates</th>
            <th>Days</th>
            <th>Reason</th>
            <th>Action</th>
          </tr>
        </thead>

        <tbody>
        <?php foreach ($leaves as $l): ?>
          <tr>
            <td><?= htmlspecialchars($l->name) ?></td>
            <td><?= htmlspecialchars($l->leave_type) ?></td>
            <td><?= $l->start_date ?> → <?= $l->end_date ?></td>
            <td><?= $l->total_days ?></td>
            <td><?= htmlspecialchars($l->reason) ?></td>

            <td>
              <form method="POST" action="<?= BASE_URL ?>/leave/action">
                <input type="hidden" name="id" value="<?= $l->id ?>">

                <input type="text"
                       name="remark"
                       class="form-control mb-1"
                       placeholder="Remark">

                <?php if ($l->requested_days > $l->balance): ?>
                  <div class="alert alert-danger small">
                    ❌ Insufficient leave balance
                  </div>
                <?php endif; ?>

                <button
                  name="status"
                  value="approved"
                  class="btn btn-success btn-sm"
                  <?= $l->requested_days > $l->balance ? 'disabled' : '' ?>>
                  Approve
                </button>

                <button
                  name="status"
                  value="rejected"
                  class="btn btn-danger btn-sm">
                  Reject
                </button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <h6 class="mt-3">🔥 Leave Analytics Heatmap</h6>
        <div id="leaveHeatmap"></div>
  </div>
</div>

<script>
document.getElementById('adminCalendarCanvas')
.addEventListener('shown.bs.offcanvas', function () {

if(window.adminCalendarLoaded) return;

const calendar = new FullCalendar.Calendar(
document.getElementById('adminCalendar'),
{
initialView:'dayGridMonth',
height:'auto',

events:'<?= BASE_URL ?>/leave/enterpriseCalendar',

eventClick:function(info){
showToast(
info.event.title + ' ('+
info.event.extendedProps.status+')'
);
}
});

calendar.render();
window.adminCalendarLoaded=true;
});
</script>
<script>
fetch('<?= BASE_URL ?>/leave/heatmap')
.then(res=>res.json())
.then(data=>{

const series = [{
name:'Leaves',
data:data.map(d=>({
x:d.start_date,
y:parseInt(d.total)
}))
}];

new ApexCharts(
document.querySelector("#leaveHeatmap"),
{
chart:{type:'heatmap',height:250},
dataLabels:{enabled:false},
colors:["#0d6efd"],
series:series
}).render();

});
</script>

<?php require '../app/views/layouts/footer.php'; ?>
