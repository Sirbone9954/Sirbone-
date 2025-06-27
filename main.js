// Confirm before deletion (for links with class 'confirm-delete')
document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('.confirm-delete').forEach(function (el) {
    el.addEventListener('click', function (e) {
      if (!confirm('Are you sure you want to delete this?')) e.preventDefault();
    });
  });
});

// Example: Show alert when room is assigned
function roomAssigned() {
  alert('Room assigned successfully!');
}