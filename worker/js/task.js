// Fake Data
const tasks = [
    {
        id: 1,
        area: "Residential Zone A",
        time: "09:00 AM",
        address: "Gandhi Road, Sector 5",
        status: "pending",
        priority: "high",
        type: "Mixed Waste",
        instructions: "Separate plastic bags"
    },
    {
        id: 2,
        area: "Commercial Complex",
        time: "10:30 AM",
        address: "MG Road, Block C",
        status: "in-progress",
        priority: "medium",
        type: "Dry Waste",
        instructions: "Check recycling bins"
    },
    {
        id: 3,
        area: "Market Area",
        time: "02:00 PM",
        address: "Vegetable Market, Sector 12",
        status: "completed",
        priority: "high",
        type: "Organic Waste",
        instructions: "Immediate collection needed"
    }
];

// Task Template
function createTaskCard(task) {
    const statusClass = {
        pending: 'bg-secondary',
        'in-progress': 'bg-primary',
        completed: 'bg-success',
        delayed: 'bg-danger'
    }[task.status];

    return `
        <div class="col-md-6 col-lg-4 task-card ${task.status}">
            <div class="card h-100">
                <div class="card-body position-relative">
                    <span class="badge ${statusClass} task-status">${task.status}</span>
                    <h5 class="card-title">${task.area}</h5>
                    <div class="mb-2">
                        <i class="bi bi-clock me-1"></i>
                        ${task.time}
                    </div>
                    <div class="mb-2 text-muted">
                        <i class="bi bi-geo-alt me-1"></i>
                        ${task.address}
                    </div>
                    <div class="mb-2">
                        <span class="badge bg-light text-dark">
                            ${task.type}
                        </span>
                        <span class="badge priority-${task.priority}">
                            Priority: ${task.priority}
                        </span>
                    </div>
                    <p class="small text-muted">${task.instructions}</p>
                    
                    <div class="task-actions">
                        ${task.status !== 'completed' ? `
                            <button class="btn btn-sm btn-success" 
                                onclick="updateTaskStatus(${task.id}, 'completed')">
                                <i class="bi bi-check-circle"></i> Complete
                            </button>
                        ` : ''}
                        
                        ${task.status !== 'delayed' ? `
                            <button class="btn btn-sm btn-warning" 
                                onclick="updateTaskStatus(${task.id}, 'delayed')">
                                <i class="bi bi-exclamation-triangle"></i> Delay
                            </button>
                        ` : ''}
                    </div>
                </div>
            </div>
        </div>
    `;
}

// Render Tasks
function renderTasks(filter = 'all') {
    const container = document.getElementById('taskContainer');
    const filtered = filter === 'all' ? tasks : tasks.filter(t => t.status === filter);
    container.innerHTML = filtered.map(createTaskCard).join('');
}

// Filter Handling
document.querySelectorAll('[data-filter]').forEach(btn => {
    btn.addEventListener('click', function() {
        document.querySelectorAll('[data-filter]').forEach(b => b.classList.remove('active'));
        this.classList.add('active');
        renderTasks(this.dataset.filter);
    });
});

// Task Status Update
function updateTaskStatus(taskId, newStatus) {
    const task = tasks.find(t => t.id === taskId);
    if(task) {
        task.status = newStatus;
        renderTasks(document.querySelector('[data-filter].active').dataset.filter);
    }
}

// Initial Render
renderTasks();