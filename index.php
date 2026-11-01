<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Taskify Pro - Task & Priority Tracker</title>
    <style>
        :root {
            --bg-color: #f4f7f6;
            --card-bg: #ffffff;
            --text-color: #333333;
            --border-color: #e0e0e0;
        }

        body.dark-mode {
            --bg-color: #1e1e2e;
            --card-bg: #2b2b3d;
            --text-color: #5c5c42;
            --border-color: #44475a;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--bg-color);
            color: var(--text-color);
            margin: 0;
            padding: 30px 20px;
            transition: all 0.3s ease;
        }

        .container {
            max-width: 650px;
            background: var(--card-bg);
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0px 4px 15px rgba(0,0,0,0.1);
            margin: auto;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .header h2 { margin: 0; }

        .theme-btn {
            background: transparent;
            border: 2px solid var(--border-color);
            color: var(--text-color);
            padding: 6px 12px;
            border-radius: 20px;
            cursor: pointer;
            font-size: 13px;
        }

        .form-group {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }

        input[type="text"], select {
            padding: 10px;
            border: 1px solid var(--border-color);
            border-radius: 6px;
            background: var(--card-bg);
            color: var(--text-color);
        }

        input[type="text"] { flex: 1; }

        .add-btn {
            padding: 10px 18px;
            background: #28a745;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
        }

        .add-btn:hover { background: #218838; }

        .filter-group {
            display: flex;
            gap: 10px;
            margin-bottom: 15px;
        }

        .filter-btn {
            background: none;
            border: none;
            color: var(--text-color);
            cursor: pointer;
            padding: 5px 10px;
            border-radius: 4px;
            opacity: 0.7;
        }

        .filter-btn.active {
            font-weight: bold;
            opacity: 1;
            border-bottom: 2px solid #007bff;
        }

        ul { list-style: none; padding: 0; margin: 0; }

        li {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            padding: 12px;
            margin-bottom: 10px;
            border-radius: 8px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: 0.2s ease;
        }

        li.completed span.task-text {
            text-decoration: line-through;
            opacity: 0.5;
        }

        .task-left {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .badge {
            font-size: 11px;
            padding: 3px 8px;
            border-radius: 12px;
            color: white;
            font-weight: bold;
        }

        .badge-High { background-color: #dc3545; }
        .badge-Medium { background-color: #ffc107; color: #333; }
        .badge-Low { background-color: #17a2b8; }

        .delete-btn {
            background: #dc3545;
            color: white;
            border: none;
            padding: 6px 10px;
            font-size: 12px;
            border-radius: 4px;
            cursor: pointer;
        }

        .delete-btn:hover { background: #c82333; }
    </style>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>

<div class="container">
    <div class="header">
        <h2>Taskify Pro</h2>
        <button class="theme-btn" id="themeToggle">🌓 Dark Mode</button>
    </div>

    <div class="form-group">
        <input type="text" id="taskInput" placeholder="What needs to be done?">
        <select id="priorityInput">
            <option value="Low">Low</option>
            <option value="Medium" selected>Medium</option>
            <option value="High">High</option>
        </select>
        <button class="add-btn" id="addTaskBtn">Add Task</button>
    </div>

    <div class="filter-group">
        <button class="filter-btn active" data-filter="all">All</button>
        <button class="filter-btn" data-filter="Pending">Pending</button>
        <button class="filter-btn" data-filter="Completed">Completed</button>
    </div>

    <ul id="taskList">
    </ul>
</div>

<script>
    $(document).ready(function() {
        let currentFilter = 'all';

        function loadTasks() {
            $.ajax({
                url: 'backend.php?action=fetch',
                method: 'GET',
                dataType: 'json',
                success: function(data) {
                    let output = '';
                    let filteredData = data.filter(task => {
                        if (currentFilter === 'all') return true;
                        return task.status === currentFilter;
                    });

                    if(filteredData.length === 0) {
                        output = '<li style="text-align:center; color:#888;">No tasks found.</li>';
                    } else {
                        $.each(filteredData, function(index, task) {
                            let checked = task.status === 'Completed' ? 'checked' : '';
                            let completedClass = task.status === 'Completed' ? 'completed' : '';

                            output += `<li class="${completedClass}">
                                <div class="task-left">
                                    <input type="checkbox" class="toggle-status" data-id="${task.id}" data-status="${task.status}" ${checked}>
                                    <span class="task-text">${task.task_name}</span>
                                    <span class="badge badge-${task.priority}">${task.priority}</span>
                                </div>
                                <button class="delete-btn" data-id="${task.id}">Delete</button>
                            </li>`;
                        });
                    }
                    $('#taskList').html(output);
                }
            });
        }

        loadTasks();

        $('#addTaskBtn').click(function() {
            let taskName = $('#taskInput').val();
            let priority = $('#priorityInput').val();

            if(taskName.trim() === '') {
                alert('Please enter a task name!');
                return;
            }

            $.ajax({
                url: 'backend.php',
                method: 'POST',
                data: { action: 'add', task_name: taskName, priority: priority },
                dataType: 'json',
                success: function(response) {
                    if(response.success) {
                        $('#taskInput').val('');
                        loadTasks();
                    } else {
                        alert(response.message);
                    }
                }
            });
        });

        $(document).on('change', '.toggle-status', function() {
            let taskId = $(this).data('id');
            let currentStatus = $(this).data('status');

            $.ajax({
                url: 'backend.php',
                method: 'POST',
                data: { action: 'toggle', id: taskId, status: currentStatus },
                dataType: 'json',
                success: function(response) {
                    if(response.success) {
                        loadTasks();
                    }
                }
            });
        });

        $(document).on('click', '.delete-btn', function() {
            let taskId = $(this).data('id');

            $.ajax({
                url: 'backend.php',
                method: 'POST',
                data: { action: 'delete', id: taskId },
                dataType: 'json',
                success: function(response) {
                    if(response.success) {
                        loadTasks();
                    }
                }
            });
        });

        $('.filter-btn').click(function() {
            $('.filter-btn').removeClass('active');
            $(this).addClass('active');
            currentFilter = $(this).data('filter');
            loadTasks();
        });

        $('#themeToggle').click(function() {
            $('body').toggleClass('dark-mode');
        });
    });
</script>

</body>
</html>