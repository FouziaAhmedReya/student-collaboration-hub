<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Project Task Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
    <h1 class="mb-4">Project Task Management</h1>

    <div class="mb-3">
        <label class="form-label">Project ID</label>
        <input type="number" id="projectId" class="form-control" value="2" style="max-width:150px">
        <button class="btn btn-secondary btn-sm mt-2" onclick="loadTasks()">Load Tasks</button>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title">Add New Task</h5>
            <form id="taskForm" class="row g-2">
                <div class="col-md-3">
                    <input type="text" class="form-control" id="taskName" placeholder="Task name" required>
                </div>
                <div class="col-md-2">
                    <input type="number" class="form-control" id="assignedTo" placeholder="Assigned to (user id)" required>
                </div>
                <div class="col-md-3">
                    <input type="datetime-local" class="form-control" id="deadline" required>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Add Task</button>
                </div>
            </form>
        </div>
    </div>

    <table class="table table-bordered bg-white">
        <thead>
            <tr>
                <th>ID</th>
                <th>Task</th>
                <th>Assigned To</th>
                <th>Deadline</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="taskTableBody">
            <tr><td colspan="6" class="text-center text-muted">Click "Load Tasks" to see tasks</td></tr>
        </tbody>
    </table>
</div>

<script>
const API_BASE = 'http://127.0.0.1:1127/api';

async function loadTasks() {
    const projectId = document.getElementById('projectId').value;
    const res = await fetch(`${API_BASE}/projects/${projectId}/tasks`);
    const tasks = await res.json();
    const tbody = document.getElementById('taskTableBody');
    tbody.innerHTML = '';

    if (tasks.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted">No tasks for this project yet</td></tr>';
        return;
    }

    tasks.forEach(task => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${task.id}</td>
            <td>${task.task_name}</td>
            <td>${task.assignee ? task.assignee.name : task.assigned_to}</td>
            <td>${new Date(task.deadline).toLocaleString()}</td>
            <td><span class="badge bg-${statusColor(task.status)}">${task.status}</span></td>
            <td>
                <select class="form-select form-select-sm d-inline-block w-auto" onchange="updateStatus(${task.id}, this.value)">
                    <option value="Pending" ${task.status === 'Pending' ? 'selected' : ''}>Pending</option>
                    <option value="In Progress" ${task.status === 'In Progress' ? 'selected' : ''}>In Progress</option>
                    <option value="Done" ${task.status === 'Done' ? 'selected' : ''}>Done</option>
                </select>
                <button class="btn btn-sm btn-danger ms-1" onclick="deleteTask(${task.id})">Delete</button>
            </td>
        `;
        tbody.appendChild(row);
    });
}

function statusColor(status) {
    if (status === 'Done') return 'success';
    if (status === 'In Progress') return 'warning';
    return 'secondary';
}

document.getElementById('taskForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const projectId = document.getElementById('projectId').value;
    const taskName = document.getElementById('taskName').value;
    const assignedTo = document.getElementById('assignedTo').value;
    const deadlineInput = document.getElementById('deadline').value;
    const deadline = deadlineInput.replace('T', ' ') + ':00';

    const res = await fetch(`${API_BASE}/tasks`, {
        method: 'POST',
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            project_id: parseInt(projectId),
            task_name: taskName,
            assigned_to: parseInt(assignedTo),
            deadline: deadline
        })
    });

    if (res.ok) {
        document.getElementById('taskForm').reset();
        loadTasks();
    } else {
        const err = await res.json();
        alert('Error: ' + JSON.stringify(err));
    }
});

async function updateStatus(taskId, status) {
    await fetch(`${API_BASE}/tasks/${taskId}/status`, {
        method: 'PATCH',
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ status })
    });
    loadTasks();
}

async function deleteTask(taskId) {
    if (!confirm('Delete this task?')) return;
    await fetch(`${API_BASE}/tasks/${taskId}`, { method: 'DELETE' });
    loadTasks();
}
</script>
</body>
</html>