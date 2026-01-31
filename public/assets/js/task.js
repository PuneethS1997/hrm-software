function updateStatus(id, status) {
  fetch('/tasks/updateStatus', {
    method: 'POST',
    headers: {'Content-Type':'application/x-www-form-urlencoded'},
    body: `id=${id}&status=${status}`
  });
}
