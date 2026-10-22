<?php
// index.php o inventory.php - Employee Management System Dashboard
session_start();

if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

require_once "config.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Management System - Dashboard</title>
    
    <!-- Custom Circular Favicon: Employee Management System -->
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'%3E%3Ccircle cx='50' cy='50' r='48' fill='white' stroke='%230b3b24' stroke-width='3'/%3E%3Ctext x='50%25' y='36%25' font-family='Georgia, serif' font-style='italic' font-weight='bold' font-size='10' fill='%230b3b24' text-anchor='middle' dominant-baseline='middle'%3EEmployee%3C/text%3E%3Ctext x='50%25' y='52%25' font-family='Georgia, serif' font-style='italic' font-weight='bold' font-size='9' fill='%230b3b24' text-anchor='middle' dominant-baseline='middle'%3EManagement%3C/text%3E%3Ctext x='50%25' y='68%25' font-family='Georgia, serif' font-style='italic' font-weight='bold' font-size='10' fill='%230b3b24' text-anchor='middle' dominant-baseline='middle'%3ESystem%3C/text%3E%3C/svg%3E">

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            background-color: #f4f6f9;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .main-banner {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            color: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            margin-bottom: 25px;
        }
        .stat-card-blue {
            background-color: #007bff;
            color: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        .stat-card-yellow {
            background-color: #ffc107;
            color: #212529;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        .stat-card-red {
            background-color: #dc3545;
            color: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        .card-table-header {
            background-color: #343a40;
            color: white;
        }
    </style>
</head>
<body>

    <div class="container my-4">
        
        <!-- Top Navigation / Logout Bar -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="font-weight-bold text-dark"><i class="fas id-card mr-2"></i>EMS Portal</h4>
            <div>
                <span class="mr-3 font-weight-bold text-secondary">Welcome, <?php echo htmlspecialchars($_SESSION["username"]); ?>!</span>
                <a href="logout.php" class="btn btn-outline-danger btn-sm"><i class="fas fa-sign-out-alt mr-1"></i> Logout</a>
            </div>
        </div>

        <!-- Main Banner (Employee Management System Theme) -->
        <div class="main-banner text-center">
            <h2><i class="fas fa-users-cog mr-2"></i> EMPLOYEE MANAGEMENT & STAFF CONTROL</h2>
            <p class="mb-0 text-white-50">Monitor staff records, active departments, and attendance status in real time.</p>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-4 mb-3">
                <div class="stat-card-blue d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase font-weight-bold mb-1" style="font-size: 0.85rem;">Total Employees</h6>
                        <h2 class="font-weight-bold mb-0">24</h2>
                    </div>
                    <i class="fas fa-users fa-2x opacity-50"></i>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="stat-card-yellow d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase font-weight-bold mb-1" style="font-size: 0.85rem;">Active Departments</h6>
                        <h2 class="font-weight-bold mb-0">5</h2>
                    </div>
                    <i class="fas fa-building fa-2x opacity-50"></i>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="stat-card-red d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase font-weight-bold mb-1" style="font-size: 0.85rem;">On Leave / Absent</h6>
                        <h2 class="font-weight-bold mb-0">2</h2>
                    </div>
                    <i class="fas fa-user-clock fa-2x opacity-50"></i>
                </div>
            </div>
        </div>

        <!-- Employee List Section -->
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="font-weight-bold mb-0 text-dark"><i class="fas fa-list mr-2"></i> Employee Directory</h5>
                <div class="d-flex">
                    <input type="text" class="form-control form-control-sm mr-2" placeholder="Search employee..." style="width: 220px;">
                    <a href="create.php" class="btn btn-success btn-sm"><i class="fas fa-user-plus mr-1"></i> Add Employee</a>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="card-table-header">
                            <tr>
                                <th style="width: 60px;">#</th>
                                <th>Full Name</th>
                                <th>Position / Role</th>
                                <th>Department</th>
                                <th>Status</th>
                                <th class="text-center" style="width: 150px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1</td>
                                <td class="font-weight-bold">Juan Dela Cruz</td>
                                <td>System Administrator</td>
                                <td>IT Department</td>
                                <td><span class="badge badge-success px-2 py-1">Active</span></td>
                                <td class="text-center">
                                    <a href="update.php?id=1" class="text-primary mr-2" title="Edit"><i class="fas fa-edit"></i></a>
                                    <a href="read.php?id=1" class="text-info mr-2" title="View"><i class="fas fa-eye"></i></a>
                                    <a href="delete.php?id=1" class="text-danger" title="Delete"><i class="fas fa-trash-alt"></i></a>
                                </td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td class="font-weight-bold">Maria Santos</td>
                                <td>HR Manager</td>
                                <td>Human Resources</td>
                                <td><span class="badge badge-success px-2 py-1">Active</span></td>
                                <td class="text-center">
                                    <a href="update.php?id=2" class="text-primary mr-2" title="Edit"><i class="fas fa-edit"></i></a>
                                    <a href="read.php?id=2" class="text-info mr-2" title="View"><i class="fas fa-eye"></i></a>
                                    <a href="delete.php?id=2" class="text-danger" title="Delete"><i class="fas fa-trash-alt"></i></a>
                                </td>
                            </tr>
                            <tr>
                                <td>3</td>
                                <td class="font-weight-bold">Pedro Penduko</td>
                                <td>Cashier / Staff</td>
                                <td>Operations</td>
                                <td><span class="badge badge-warning px-2 py-1">On Leave</span></td>
                                <td class="text-center">
                                    <a href="update.php?id=3" class="text-primary mr-2" title="Edit"><i class="fas fa-edit"></i></a>
                                    <a href="read.php?id=3" class="text-info mr-2" title="View"><i class="fas fa-eye"></i></a>
                                    <a href="delete.php?id=3" class="text-danger" title="Delete"><i class="fas fa-trash-alt"></i></a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>