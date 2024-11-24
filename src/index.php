<?php
session_start(); // Start the session for user authentication
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Task Manager</title>
    <!-- Bootstrap CSS for styling -->
    <link rel="stylesheet" href="css/bootstrap/bootstrap.min.css">
    <!-- Custom CSS for styling -->
    <link rel="stylesheet" href="css/styles.css">
    <!-- jQuery library -->
    <script src="js/jquery-3.7.1.min.js"></script>
    <!-- Bootstrap JS for interactive components -->
    <script src="js/bootstrap/bootstrap.bundle.min.js"></script>

    <!-- Fonts from Google  -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">
    <!-- Font Awesome 4 Icons  -->
    <link rel="stylesheet" href="font-awesome/css/font-awesome.min.css">

</head>

<body>
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand navbar-light bg-light taskmanager-header">
        <div class="container">
            <!-- Navbar Brand -->
            <a class="navbar-brand" href="#">Task Manager</a>

            <!-- Navbar Right -->
            <div class="navbar-collapse">
                <ul class="navbar-nav ms-auto">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <!-- Dropdown -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <?php if (isset($_SESSION['profile_picture'])): ?>
                                    <img src="<?php echo htmlspecialchars($_SESSION['profile_picture']); ?>" alt="photo" class="profile-img">
                                <?php else: ?>
                                    <img src="placeholder.jpg" alt="photo" class="profile-img">
                                <?php endif; ?>
                                <?php echo htmlspecialchars($_SESSION['username']); ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                <li><a class="dropdown-item" href="#" id="changePasswordLink">Change Password</a></li>
                                <li><a class="dropdown-item" href="#" id="updateProfileLink">Update Profile</a></li>
                                <li><a class="dropdown-item" href="#" id="logoutLink">Log Out</a></li>
                            </ul>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container" id="mainContent">
        <?php if (!isset($_SESSION['user_id'])): ?>
            <!-- Login Form -->
            <div class="row">
                <div class="col-md-6 offset-md-3">
                    <div id="loginForm">
                        <h2 class="text-center">Login</h2>
                        <form>
                            <div class="mb-3">
                                <label for="loginUsername" class="form-label">Username:</label>
                                <input type="text" class="form-control" id="loginUsername" required>
                            </div>
                            <div class="mb-3">
                                <label for="loginPassword" class="form-label">Password:</label>
                                <input type="password" class="form-control" id="loginPassword" required>
                            </div>
                            <button type="submit" class="btn btn-primary" id="loginButton">Login</button>
                        </form>
                        <p class="mt-3">Don't have an account? <a href="#" id="showSignupForm">Sign up here</a></p>
                    </div>

                    <div id="signupForm" class="d-none">
                        <h2 class="text-center">Sign Up</h2>
                        <form>
                            <div class="mb-3">
                                <label for="signupUsername" class="form-label">Username:</label>
                                <input type="text" class="form-control" id="signupUsername" required>
                            </div>
                            <div class="mb-3">
                                <label for="signupEmail" class="form-label">Email:</label>
                                <input type="email" class="form-control" id="signupEmail" required>
                            </div>
                            <div class="mb-3">
                                <label for="signupPassword" class="form-label">Password:</label>
                                <input type="password" class="form-control" id="signupPassword" required>
                            </div>
                            <button type="submit" class="btn btn-primary" id="signupButton">Sign Up</button>
                        </form>
                        <p class="mt-3">Already have an account? <a href="#" id="showLoginForm">Login here</a></p>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <!-- Task List -->
            <h2 class="text-center m-3">Your Tasks</h2>
            <button class="btn btn-primary btn-lg mb-3" id="addTaskButton">Add Task</button>
            <table class="table table-striped table-hover table-responsive" id="tasksTable">
                <thead>
                    <tr>
                        <th>Task</th>
                        <th>Category</th>
                        <th>Due Date</th>
                        <th>Completed</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Tasks will load here via Ajax -->
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <!-- Toast Container -->
    <div class="toast-container position-fixed bottom-0 end-0 p-3" id="toastContainer">
        <!-- Toasts will be dynamically added here -->
    </div>

    <!-- Modals -->
    <!-- Add Task Modal -->
    <div class="modal fade" id="addTaskModal" tabindex="-1" aria-labelledby="addTaskModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="addTaskForm">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addTaskModalLabel">Add New Task</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Form fields -->
                        <div class="mb-3">
                            <label for="taskName" class="form-label">Task Name:</label>
                            <input type="text" class="form-control" id="taskName" required>
                        </div>
                        <div class="mb-3">
                            <label for="taskCategory" class="form-label">Category:</label>
                            <input type="text" class="form-control" id="taskCategory">
                        </div>
                        <div class="mb-3">
                            <label for="taskDueDate" class="form-label">Due Date:</label>
                            <input type="date" class="form-control" id="taskDueDate">
                        </div>
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="taskCompleted">
                            <label class="form-check-label" for="taskCompleted">Completed</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Add Task</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Task Modal -->
    <div class="modal fade" id="editTaskModal" tabindex="-1" aria-labelledby="editTaskModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="editTaskForm">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editTaskModalLabel">Edit Task</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Form fields -->
                        <input type="hidden" id="editTaskId">
                        <div class="mb-3">
                            <label for="editTaskName" class="form-label">Task Name:</label>
                            <input type="text" class="form-control" id="editTaskName" required>
                        </div>
                        <div class="mb-3">
                            <label for="editTaskCategory" class="form-label">Category:</label>
                            <input type="text" class="form-control" id="editTaskCategory">
                        </div>
                        <div class="mb-3">
                            <label for="editTaskDueDate" class="form-label">Due Date:</label>
                            <input type="date" class="form-control" id="editTaskDueDate">
                        </div>
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="editTaskCompleted">
                            <label class="form-check-label" for="editTaskCompleted">Completed</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteTaskModal" tabindex="-1" aria-labelledby="deleteTaskModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="deleteTaskForm">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteTaskModalLabel">Confirm Delete</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Are you sure you want to delete this task?
                        <input type="hidden" id="deleteTaskId">
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-danger">Delete</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Change Password Modal -->
    <div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="changePasswordForm">
                    <div class="modal-header">
                        <h5 class="modal-title" id="changePasswordModalLabel">Change Password</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="currentPassword" class="form-label">Current Password:</label>
                            <input type="password" class="form-control" id="currentPassword" name="currentPassword" required>
                        </div>
                        <div class="mb-3">
                            <label for="newPassword" class="form-label">New Password:</label>
                            <input type="password" class="form-control" id="newPassword" name="newPassword" required>
                        </div>
                        <div class="mb-3">
                            <label for="confirmNewPassword" class="form-label">Confirm New Password:</label>
                            <input type="password" class="form-control" id="confirmNewPassword" name="confirmNewPassword" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Change Password</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Update Profile Modal -->
    <div class="modal fade" id="updateProfileModal" tabindex="-1" aria-labelledby="updateProfileModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="updateProfileForm" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title" id="updateProfileModalLabel">Update Profile</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="email" class="form-label">Email:</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="mb-3 text-center">
                            <img id="currentProfilePicture" src="" alt="Profile Picture" class="rounded-circle mb-3" style="width: 100px; height: 100px; object-fit: cover;">
                        </div>
                        <div class="mb-3">
                            <label for="profilePicture" class="form-label">Profile Picture:</label>
                            <input type="file" class="form-control" id="profilePicture" name="profilePicture" accept="image/*">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Define JavaScript Variables -->
    <script>
        var isLoggedIn = <?php echo isset($_SESSION['user_id']) ? 'true' : 'false'; ?>;
    </script>
    <!-- Custom JavaScript -->
    <script src="js/app.js"></script>
</body>

</html>