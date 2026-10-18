<?php
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once "config.php";

// 1. Employee Statistics
$total_employees = 0;
$total_payroll = 0;
$avg_salary = 0;

$emp_query = "SELECT COUNT(*) as total_count, SUM(salary) as total_salary, AVG(salary) as avg_salary FROM employees";
if ($emp_result = mysqli_query($link, $emp_query)) {
    $row_emp = mysqli_fetch_assoc($emp_result);
    $total_employees = $row_emp['total_count'] ?? 0;
    $total_payroll = $row_emp['total_salary'] ?? 0;
    $avg_salary = $row_emp['avg_salary'] ?? 0;
}

// 2. Inventory Statistics
$total_items = 0;
$total_low_stock = 0;
$total_stock_out = 0;

$inv_query = "SELECT COUNT(*) as total_count, SUM(low_stock) as total_low, SUM(stock_out) as total_out FROM inventory";
if ($inv_result = mysqli_query($link, $inv_query)) {
    $row_inv = mysqli_fetch_assoc($inv_result);
    $total_items = $row_inv['total_count'] ?? 0;
    $total_low_stock = $row_inv['total_low'] ?? 0;
    $total_stock_out = $row_inv['total_out'] ?? 0;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports - Garden Cafe IPA</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body { background-color: #f4f6f9; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .navbar-brand { font-weight: 700; }
        .nav-link { font-weight: 500; }
        .nav-item.active .nav-link { color: #2ecc71 !important; border-bottom: 2px solid #2ecc71; }
        .main-container { margin-top: 25px; margin-bottom: 50px; }
        .welcome-banner { background: linear-gradient(135deg, #4b6cb7 0%, #182848 100%); border-radius: 12px; padding: 22px; color: #ffffff; text-align: center; }
        .stat-card { border: none; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        .content-card { border: none; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.08); background: #ffffff; }
        .user-badge { background: rgba(255, 255, 255, 0.2); padding: 6px 14px; border-radius: 20px; }
        @media print { .navbar, .btn, .welcome-banner { display: none !important; } }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
        <div class="container-fluid px-4">
            <a class="navbar-brand" href="index.php"><i class="fas fa-leaf text-success mr-2"></i> Garden Cafe IPA</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mr-auto ml-lg-3">
                    <li class="nav-item"><a class="nav-link text-light" href="index.php"><i class="fas fa-tachometer-alt mr-1"></i> Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link text-light" href="index.php"><i class="fas fa-users mr-1"></i> Employees</a></li>
                    <li class="nav-item"><a class="nav-link text-light" href="inventory.php"><i class="fas fa-boxes mr-1"></i> Inventory / Stocks</a></li>
                    <li class="nav-item active"><a class="nav-link" href="reports.php"><i class="fas fa-chart-line mr-1"></i> Reports</a></li>
                </ul>
                <ul class="navbar-nav ml-auto align-items-center">
                    <li class="nav-item mr-3">
                        <span class="navbar-text text-light user-badge"><i class="fas fa-user-circle mr-1"></i> Hi, <b><?php echo htmlspecialchars($_SESSION["username"]); ?></b></span>
                    </li>
                    <li class="nav-item"><a href="logout.php" class="btn btn-outline-danger btn-sm"><i class="fas fa-sign-out-alt mr-1"></i> Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid px-4 main-container">
        <div class="welcome-banner mb-4">
            <h3 class="font-weight-bold text-uppercase mb-1"><i class="fas fa-file-invoice-dollar mr-2"></i> System Reports & Analytics</h3>
            <p class="mb-0 opacity-90">Overview of staff compensation and inventory metrics.</p>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="font-weight-bold text-dark mb-0"><i class="fas fa-chart-bar text-primary mr-2"></i> Executive Summary</h4>
            <button onclick="window.print()" class="btn btn-primary"><i class="fas fa-print mr-1"></i> Print / Save as PDF</button>
        </div>

        <div class="row">
            <div class="col-lg-6 mb-4">
                <div class="card content-card p-4 h-100">
                    <h5 class="font-weight-bold text-dark mb-3 border-bottom pb-2"><i class="fas fa-users text-primary mr-2"></i> Employee & Payroll Report</h5>
                    <ul class="list-group list-group-flush mb-3">
                        <li class="list-group-item d-flex justify-content-between"><span>Total Active Staff</span><strong><?php echo $total_employees; ?> employees</strong></li>
                        <li class="list-group-item d-flex justify-content-between"><span>Average Monthly Salary</span><strong>₱<?php echo number_format($avg_salary, 2); ?></strong></li>
                        <li class="list-group-item d-flex justify-content-between bg-light"><span>Total Monthly Payroll</span><strong class="text-primary h5 mb-0">₱<?php echo number_format($total_payroll, 2); ?></strong></li>
                    </ul>
                </div>
            </div>

            <div class="col-lg-6 mb-4">
                <div class="card content-card p-4 h-100">
                    <h5 class="font-weight-bold text-dark mb-3 border-bottom pb-2"><i class="fas fa-boxes text-success mr-2"></i> Inventory Status Report</h5>
                    <ul class="list-group list-group-flush mb-3">
                        <li class="list-group-item d-flex justify-content-between"><span>Total Items Tracked</span><strong><?php echo $total_items; ?> items</strong></li>
                        <li class="list-group-item d-flex justify-content-between"><span>Low Stock Threshold Total</span><strong class="text-warning"><?php echo $total_low_stock; ?> units</strong></li>
                        <li class="list-group-item d-flex justify-content-between bg-light"><span>Out of Stock Items</span><strong class="text-danger h5 mb-0"><?php echo $total_stock_out; ?> items</strong></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</body>
</html>