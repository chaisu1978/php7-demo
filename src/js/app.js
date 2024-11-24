$(document).ready(function () {
    // Function to display Bootstrap Toasts
    function showToast(message, type) {
        var toastId = 'toast' + Date.now();
        var toastHtml = `
        <div id="${toastId}" class="toast align-items-center text-white bg-${type || 'info'} border-0" role="alert" aria-live="assertive" aria-atomic="true">
          <div class="d-flex">
            <div class="toast-body">
              ${message}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
          </div>
        </div>
        `;

        $('#toastContainer').append(toastHtml);
        var toastEl = document.getElementById(toastId);
        var toast = new bootstrap.Toast(toastEl, { delay: 3000 });
        toast.show();

        // Remove the toast element after it's hidden
        toastEl.addEventListener('hidden.bs.toast', function () {
            $(toastEl).remove();
        });
    }

    // Toggle to show signup form
    $(document).on('click', '#showSignupForm', function (e) {
        e.preventDefault();
        $('#loginForm').addClass('d-none');
        $('#signupForm').removeClass('d-none');
    });

    // Toggle to show login form
    $(document).on('click', '#showLoginForm', function (e) {
        e.preventDefault();
        $('#signupForm').addClass('d-none');
        $('#loginForm').removeClass('d-none');
    });

    // Login Form Submission
    $(document).on('click', '#loginButton', function (e) {
        e.preventDefault();
        $.ajax({
            url: 'includes/login.php',
            type: 'POST',
            dataType: 'json',
            data: {
                username: $('#loginUsername').val(),
                password: $('#loginPassword').val()
            },
            success: function (response) {
                if (response && response.success) {
                    location.reload();
                } else {
                    showToast(response.message || 'Login failed. Please try again.', 'danger');
                }
            },
            error: function (jqXHR) {
                showToast(`An error occurred during login: ${jqXHR.status} ${jqXHR.responseText}`, 'danger');
            }
        });
    });

    // Signup Form Submission
    $(document).on('click', '#signupButton', function (e) {
        e.preventDefault();
        $.ajax({
            url: 'includes/signup.php',
            type: 'POST',
            dataType: 'json',
            data: {
                username: $('#signupUsername').val(),
                email: $('#signupEmail').val(),
                password: $('#signupPassword').val()
            },
            success: function (response) {
                if (response && response.success) {
                    showToast('Signup successful! Please log in.', 'success');
                    $('#signupForm').addClass('d-none');
                    $('#loginForm').removeClass('d-none');
                } else {
                    showToast(response.message || 'Signup failed. Please try again.', 'danger');
                }
            },
            error: function (jqXHR) {
                showToast(`An error occurred during signup: ${jqXHR.status} ${jqXHR.responseText}`, 'danger');
            }
        });
    });

    // Load tasks if user is logged in
    if (typeof isLoggedIn !== 'undefined' && isLoggedIn) {
        loadTasks();
    }

    // Function to load tasks
    function loadTasks() {
        $.ajax({
            url: 'includes/get_tasks.php',
            type: 'GET',
            dataType: 'json',
            success: function (response) {
                var tbody = $('#tasksTable tbody');
                tbody.empty();
                if (response && response.tasks && response.tasks.length > 0) {
                    $.each(response.tasks, function (index, task) {
                        var row = $(`
                            <tr>
                                <td data-task-id="${task.id}">${task.task_name}</td>
                                <td>${task.category || 'General'}</td>
                                <td>${task.due_date || ''}</td>
                                <td>${task.is_completed ? '<i class="fa fa-check-square icon-color-ok"> Yes</i>' : '<i class="fa fa-check-square icon-color-warning"> No</i>'}</td>
                                <td>
                                    <button class="btn btn-primary btn-sm edit-task-btn">Edit</button>
                                    <button class="btn btn-tertiary btn-sm delete-task-btn">Delete</button>
                                </td>
                            </tr>
                        `);
                        tbody.append(row);
                    });
                } else {
                    tbody.append('<tr><td colspan="5">No tasks found.</td></tr>');
                }
            },
            error: function (jqXHR) {
                showToast(`An error occurred while loading tasks: ${jqXHR.status} ${jqXHR.responseText}`, 'danger');
            }
        });
    }

    // Add Task Button Click
    $(document).on('click', '#addTaskButton', function () {
        $('#addTaskModal').modal('show');
    });

    // Handle Add Task Form Submission
    $(document).on('submit', '#addTaskForm', function (e) {
        e.preventDefault();
        var taskName = $('#taskName').val();
        var taskCategory = $('#taskCategory').val();
        var taskDueDate = $('#taskDueDate').val();
        var taskCompleted = $('#taskCompleted').is(':checked') ? 1 : 0;

        $.ajax({
            url: 'includes/add_task.php',
            type: 'POST',
            dataType: 'json',
            data: {
                task_name: taskName,
                category: taskCategory,
                due_date: taskDueDate,
                is_completed: taskCompleted
            },
            success: function (response) {
                if (response && response.success) {
                    $('#addTaskModal').modal('hide');
                    loadTasks();
                    showToast('Task added successfully!', 'success');
                    // reset fields after adding task
                    $('#taskName').val('');
                    $('#taskCategory').val('');
                    $('#taskDueDate').val('');
                    $('#taskCompleted').prop('checked', false);
                } else {
                    showToast(response.message || 'Failed to add task.', 'danger');
                }
            },
            error: function (jqXHR) {
                showToast(`An error occurred while adding the task: ${jqXHR.status} ${jqXHR.responseText}`, 'danger');
            }
        });
    });

    // Edit Task Button Click
    $(document).on('click', '.edit-task-btn', function () {
        var taskId = $(this).closest('tr').find('td[data-task-id]').attr('data-task-id');

        // Get the task data from the row
        var taskName = $(this).closest('tr').find('td[data-task-id]').text();
        var taskCategory = $(this).closest('tr').find('td').eq(1).text();
        var taskDueDate = $(this).closest('tr').find('td').eq(2).text();
        var taskCompletedText = $(this).closest('tr').find('td').eq(3).text().trim();
        var taskCompleted = taskCompletedText.includes('Yes');

        // Populate the modal fields
        $('#editTaskId').val(taskId);
        $('#editTaskName').val(taskName);
        $('#editTaskCategory').val(taskCategory !== 'General' ? taskCategory : '');
        $('#editTaskDueDate').val(taskDueDate);
        $('#editTaskCompleted').prop('checked', taskCompleted);

        // Show the modal
        $('#editTaskModal').modal('show');
    });

    // Handle Edit Task Form Submission
    $(document).on('submit', '#editTaskForm', function (e) {
        e.preventDefault();
        var taskId = $('#editTaskId').val();
        var taskName = $('#editTaskName').val();
        var taskCategory = $('#editTaskCategory').val();
        var taskDueDate = $('#editTaskDueDate').val();
        var taskCompleted = $('#editTaskCompleted').is(':checked') ? 1 : 0;

        $.ajax({
            url: 'includes/edit_task.php',
            type: 'POST',
            dataType: 'json',
            data: {
                task_id: taskId,
                task_name: taskName,
                category: taskCategory,
                due_date: taskDueDate,
                is_completed: taskCompleted
            },
            success: function (response) {
                if (response && response.success) {
                    $('#editTaskModal').modal('hide');
                    loadTasks();
                    showToast('Task updated successfully!', 'success');
                    // reset the fields after updating task
                    $('#editTaskName').val('');
                    $('#editTaskCategory').val('');
                    $('#editTaskDueDate').val('');
                    $('#editTaskCompleted').prop('checked', false);
                } else {
                    showToast(response.message || 'Failed to update task.', 'danger');
                }
            },
            error: function (jqXHR) {
                showToast(`An error occurred while updating the task: ${jqXHR.status} ${jqXHR.responseText}`, 'danger');
            }
        });
    });

    // Delete Task Button Click
    $(document).on('click', '.delete-task-btn', function () {
        var taskId = $(this).closest('tr').find('td[data-task-id]').attr('data-task-id');

        $('#deleteTaskId').val(taskId);
        $('#deleteTaskModal').modal('show');
    });

    // Handle Delete Task Form Submission
    $(document).on('submit', '#deleteTaskForm', function (e) {
        e.preventDefault();
        var taskId = $('#deleteTaskId').val();

        $.ajax({
            url: 'includes/delete_task.php',
            type: 'POST',
            dataType: 'json',
            data: { task_id: taskId },
            success: function (response) {
                if (response && response.success) {
                    $('#deleteTaskModal').modal('hide');
                    loadTasks();
                    showToast('Task deleted successfully!', 'success');
                } else {
                    showToast(response.message || 'Failed to delete task.', 'danger');
                }
            },
            error: function (jqXHR) {
                showToast(`An error occurred while deleting the task: ${jqXHR.status} ${jqXHR.responseText}`, 'danger');
            }
        });
    });

    // Logout
    $(document).on('click', '#logoutLink', function (e) {
        e.preventDefault();
        $.ajax({
            url: 'includes/logout.php',
            type: 'POST',
            dataType: 'json',
            success: function (response) {
                if (response && response.success) {
                    location.reload();
                } else {
                    showToast('Failed to log out.', 'danger');
                }
            },
            error: function (jqXHR) {
                showToast(`An error occurred during logout: ${jqXHR.status} ${jqXHR.responseText}`, 'danger');
            }
        });
    });

    // Open Change Password Modal
    $(document).on('click', '#changePasswordLink', function (e) {
        e.preventDefault();
        $('#changePasswordForm')[0].reset(); // Reset the form fields
        $('#changePasswordModal').modal('show');
    });

    // Open Update Profile Modal
    $(document).on('click', '#updateProfileLink', function (e) {
        e.preventDefault();

        // Load current user data (email and profile picture)
        $.ajax({
            url: 'includes/get_user_data.php',
            type: 'GET',
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    $('#email').val(response.email || '');
                    $('#currentProfilePicture').attr('src', response.profilePictureUrl || 'placeholder.png');
                    $('#updateProfileModal').modal('show');
                } else {
                    showToast('Failed to load profile data.', 'danger');
                }
            },
            error: function () {
                showToast('Error loading profile data.', 'danger');
            }
        });
    });


    // Change Password Form Submission
    $(document).on('submit', '#changePasswordForm', function (e) {
        e.preventDefault();
        var formData = $(this).serialize();

        $.ajax({
            url: 'includes/change_password.php',
            type: 'POST',
            dataType: 'json',
            data: formData,
            success: function (response) {
                if (response.success) {
                    $('#changePasswordModal').modal('hide');
                    showToast('Password changed successfully!', 'success');
                } else {
                    showToast(response.message || 'Failed to change password.', 'danger');
                }
            },
            error: function (jqXHR) {
                showToast(`Error: ${jqXHR.responseText}`, 'danger');
            }
        });
    });

    // Update Profile Form Submission
    $(document).on('submit', '#updateProfileForm', function (e) {
        e.preventDefault();
        var formData = new FormData(this);

        $.ajax({
            url: 'includes/update_profile.php',
            type: 'POST',
            dataType: 'json',
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {
                if (response.success) {
                    $('#updateProfileModal').modal('hide');
                    showToast('Profile updated successfully!', 'success');
                    // Update displayed profile picture
                    $('#currentProfilePicture').attr('src', response.profilePictureUrl || 'placeholder.png');
                    // Reset the form fields
                    $('#currentProfilePicture').val('');
                    $('#profilePicture').val('');

                } else {
                    showToast(response.message || 'Failed to update profile.', 'danger');
                }
            },
            error: function (jqXHR) {
                showToast(`Error: ${jqXHR.responseText}`, 'danger');
            }
        });
    });

    // Open Modals and Populate Existing Data
    $(document).on('click', '#editProfileLink', function (e) {
        e.preventDefault();

        // Load current user data
        $.ajax({
            url: 'includes/get_user_data.php',
            type: 'GET',
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    $('#email').val(response.email || '');
                    $('#currentProfilePicture').attr('src', response.profilePictureUrl || 'placeholder.png');
                    $('#updateProfileModal').modal('show');
                } else {
                    showToast('Failed to load profile data.', 'danger');
                }
            },
            error: function () {
                showToast('Error loading profile data.', 'danger');
            }
        });
    });
});
