document.addEventListener("DOMContentLoaded", () => {
  const taskForm = document.getElementById("taskForm");
  const subjectForm = document.getElementById("subjectForm");

  if (taskForm) {
    taskForm.addEventListener("submit", async (e) => {
      e.preventDefault();
      const formData = new FormData(taskForm);
      formData.append("action", "add_task");
      const response = await fetch("ajax_tasks.php", { method: "POST", body: formData });
      const result = await response.json();
      if (result.ok) {
        location.reload();
      } else {
        alert(result.message);
      }
    });
  }

  document.querySelectorAll(".ajax-toggle-task").forEach(btn => {
    btn.addEventListener("click", async () => {
      const formData = new FormData();
      formData.append("action", "toggle_task");
      formData.append("id", btn.dataset.id);
      const response = await fetch("ajax_tasks.php", { method: "POST", body: formData });
      const result = await response.json();
      if (result.ok) {
        btn.textContent = btn.textContent.trim() === '⬜' ? '✅' : '⬜';
      } else {
        alert(result.message);
      }
    });
  });

  document.querySelectorAll(".ajax-delete-task").forEach(btn => {
    btn.addEventListener("click", async () => {
      if (!confirm("Delete this task?")) return;
      const formData = new FormData();
      formData.append("action", "delete_task");
      formData.append("id", btn.dataset.id);
      const response = await fetch("ajax_tasks.php", { method: "POST", body: formData });
      const result = await response.json();
      if (result.ok) {
        btn.closest('tr').remove();
        const counter = document.querySelector('.counter span');
        if (counter) counter.textContent = parseInt(counter.textContent) - 1;
      } else {
        alert(result.message);
      }
    });
  });

  if (subjectForm) {
    subjectForm.addEventListener("submit", async (e) => {
      e.preventDefault();
      const formData = new FormData(subjectForm);
      formData.append("action", "add_subject");
      const response = await fetch("ajax_subjects.php", { method: "POST", body: formData });
      const result = await response.json();
      if (result.ok) {
        location.reload();
      } else {
        alert(result.message);
      }
    });
  }

  document.querySelectorAll(".ajax-delete-subject").forEach(btn => {
    btn.addEventListener("click", async () => {
      if (!confirm("Delete this subject?")) return;
      const formData = new FormData();
      formData.append("action", "delete_subject");
      formData.append("id", btn.dataset.id);
      const response = await fetch("ajax_subjects.php", { method: "POST", body: formData });
      const result = await response.json();
      if (result.ok) {
        btn.closest('tr').remove();
      } else {
        alert(result.message);
      }
    });
  });
});