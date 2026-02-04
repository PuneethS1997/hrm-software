<?php require '../app/views/layouts/header.php'; ?>
<?php require '../app/views/layouts/sidebar.php'; ?>

<div id="main-content" class="main-content">
  <div class="content-wrapper container-fluid">

  <button class="btn btn-dark mb-3"
      data-bs-toggle="offcanvas"
      data-bs-target="#adminCalendarCanvas">
      📅 Leave Calendar View
      </button>
      <button class="btn btn-dark"
        data-bs-toggle="offcanvas"
        data-bs-target="#leavePolicyCanvas">
          Manage Leave Types
      </button>

      <button class="btn btn-warning"
data-bs-toggle="offcanvas"
data-bs-target="#holidayCanvas">
Add Holiday
</button>

<div class="offcanvas offcanvas-end" id="holidayCanvas">

<div class="offcanvas-header">
<h5>Add Holiday</h5>
<button class="btn-close" data-bs-dismiss="offcanvas"></button>
</div>

<div class="offcanvas-body">

<form id="holidayForm">

<input name="title" class="form-control mb-2"
placeholder="Holiday Name" required>

<input type="date"
name="holiday_date"
class="form-control mb-2" required>

<select name="type" class="form-control mb-2">
<option value="public">Public Holiday</option>
<option value="company">Company Holiday</option>
<option value="optional">Optional Holiday</option>
</select>

<button class="btn btn-success w-100">
Save Holiday
</button>

</form>

</div>
</div>


      <div class="offcanvas offcanvas-end"
     tabindex="-1"
     id="leavePolicyCanvas"
     style="width:500px">

    <div class="offcanvas-header">
        <h5>Leave Types & Policy</h5>
        <button type="button"
                class="btn-close"
                data-bs-dismiss="offcanvas"></button>
    </div>

    <div class="offcanvas-body">

        <form id="leaveTypeForm">

            <div class="mb-3">
                <label>Leave Name</label>
                <input type="text"
                       name="name"
                       class="form-control"
                       required>
            </div>

            <div class="mb-3">
                <label>Max Days Per Year</label>
                <input type="number"
                       name="max_days"
                       class="form-control"
                       required>
            </div>

            <div class="mb-3">
                <label>Carry Forward</label>
                <select name="carry_forward"
                        class="form-control">
                    <option value="1">Allowed</option>
                    <option value="0">Not Allowed</option>
                </select>
            </div>

            <button class="btn btn-success w-100">
                Save Policy
            </button>

        </form>

    </div>
</div>



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
 
  </div>
</div>

<script>
document.getElementById('adminCalendarCanvas')
.addEventListener('shown.bs.offcanvas', function () {

if(window.adminCalendarLoaded) return;

Promise.all([
fetch("<?= BASE_URL ?>/leave/enterpriseCalendar").then(r=>r.json()),
fetch("<?= BASE_URL ?>/holiday/calendar").then(r=>r.json())
])
.then(([leaves,holidays])=>{

new FullCalendar.Calendar(calendarEl,{
events:[...leaves,...holidays]
}).render();

});


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
document.getElementById('leaveTypeForm')
.addEventListener('submit', function(e){

    e.preventDefault();

    fetch("<?= BASE_URL ?>/leave/storeLeaveType", {
        method: 'POST',
        body: new FormData(this)
    })
    .then(response => response.json())
    .then(() => {
        alert('Policy Saved');
        location.reload();
    })
    .catch(err => {
        console.error(err);
        alert('Something went wrong');
    });

});
</script>

<script>
document.getElementById('holidayForm')
.addEventListener('submit',function(e){

e.preventDefault();

fetch("<?= BASE_URL ?>/holiday/store",{
method:'POST',
body:new FormData(this)
})
.then(r=>r.json())
.then(()=>{
showToast("Holiday Added 🎉");
location.reload();
});
});
</script>

<?php require '../app/views/layouts/footer.php'; ?>
