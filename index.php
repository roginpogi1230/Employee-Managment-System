<?php
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

require_once "config.php";

$user_role = $_SESSION["role"] ?? 'staff'; 
$is_admin = ($user_role === 'admin');

$total_employees = 0;
$total_payroll = 0;
$avg_salary = 0;

$stats_sql = "SELECT COUNT(*) as total_count, SUM(salary) as total_sal, AVG(salary) as avg_sal FROM employees";
if ($stats_result = mysqli_query($link, $stats_sql)) {
    $stats = mysqli_fetch_assoc($stats_result);
    $total_employees = $stats['total_count'] ?? 0;
    $total_payroll = $stats['total_sal'] ?? 0;
    $avg_salary = $stats['avg_sal'] ?? 0;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Management System</title>
    
    <!-- Favicon Logo Icon sa Tab ng Browser -->
    <link rel="icon" type="image/png" href="logo.png">
    <!-- Kung may sarili kang image file sa folder mo (e.g., logo.png), palitan lang ang href sa: href="logo.png" -->

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/dataTables.bootstrap4.min.css">
    <style>
        body { background-color: #f4f6f9; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .navbar-brand { font-weight: 700; }
        .nav-link { font-weight: 500; }
        .nav-item.active .nav-link { color: #2ecc71 !important; border-bottom: 2px solid #2ecc71; }
        .main-container { margin-top: 25px; margin-bottom: 50px; }
        .content-card { border: none; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.08); background: #ffffff; }
        .stat-card { border: none; border-radius: 10px; color: #fff; transition: transform 0.2s; }
        .stat-card:hover { transform: translateY(-3px); }
        .user-badge { background: rgba(255, 255, 255, 0.2); padding: 6px 14px; border-radius: 20px; }
        .role-badge-admin { background-color: #dc3545; color: #fff; font-size: 0.75rem; padding: 3px 8px; border-radius: 10px; font-weight: 600; }
        .role-badge-staff { background-color: #17a2b8; color: #fff; font-size: 0.75rem; padding: 3px 8px; border-radius: 10px; font-weight: 600; }
        #preloader { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: #0f172a; z-index: 99999; display: flex; justify-content: center; align-items: center; opacity: 1; visibility: visible; transition: opacity 0.8s ease-out, visibility 0.8s ease-out; }
        #preloader.fade-out { opacity: 0; visibility: hidden; }
        .welcome-intro-title { font-size: 2.5rem; font-weight: 800; background: linear-gradient(270deg, #2ecc71, #17a2b8, #007bff, #2ecc71); background-size: 600% 600%; -webkit-background-clip: text; -webkit-text-fill-color: transparent; animation: gradientShift 6s ease infinite, zoomInIntro 0.8s ease-out; margin-bottom: 10px; }
        @keyframes gradientShift { 0% { background-position: 0% 50%; } 50% { background-position: 100% 50%; } 100% { background-position: 0% 50%; } }
        @keyframes zoomInIntro { from { opacity: 0; transform: scale(0.85) translateY(-15px); } to { opacity: 1; transform: scale(1) translateY(0); } }
    </style>
</head>
<body>
    <div id="preloader">
        <div class="text-center px-3">
            <div class="spinner-border text-success mb-3" style="width: 3.5rem; height: 3.5rem;" role="status"><span class="sr-only">Loading...</span></div>
            <h1 class="welcome-intro-title"><i class="fas fa-smile text-warning mr-2"></i>Welcome to Employee Management System</h1>
        </div>
    </div>

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
        <div class="container-fluid px-4">
            <a class="navbar-brand" href="index.php"><i class="fas fa-leaf text-success mr-2"></i> Employee Management System</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"><span class="navbar-toggler-icon"></span></button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mr-auto ml-lg-3">
                    <li class="nav-item active"><a class="nav-link" href="index.php"><i class="fas fa-users mr-1"></i> Employees</a></li>
                    <li class="nav-item"><a class="nav-link text-light" href="inventory.php"><i class="fas fa-boxes mr-1"></i> Inventory / Stocks</a></li>
                    <li class="nav-item"><a class="nav-link text-light" href="reports.php"><i class="fas fa-chart-line mr-1"></i> Reports</a></li>
                </ul>
                <ul class="navbar-nav ml-auto align-items-center">
                    <li class="nav-item mr-2"><a href="create.php" class="btn btn-success btn-sm"><i class="fas fa-user-plus mr-1"></i> Add Employee</a></li>
                    <li class="nav-item mr-3"><a href="register.php" class="btn btn-primary btn-sm"><i class="fas fa-user-shield mr-1"></i> Add User</a></li>
                    <li class="nav-item mr-3">
                        <span class="navbar-text text-light user-badge">
                            <i class="fas fa-user-circle mr-1"></i> Hi, <b><?php echo htmlspecialchars($_SESSION["username"]); ?></b>
                            <span class="<?php echo $is_admin ? 'role-badge-admin' : 'role-badge-staff'; ?> ml-1"><?php echo strtoupper(htmlspecialchars($user_role)); ?></span>
                        </span>
                    </li>
                    <li class="nav-item"><a href="logout.php" class="btn btn-outline-danger btn-sm"><i class="fas fa-sign-out-alt mr-1"></i> Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid px-4 main-container">
        <div class="row mb-4">
            <div class="col-md-4 mb-3 mb-md-0">
                <div class="card stat-card bg-primary p-3 shadow-sm">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase mb-1 text-white-50 font-weight-bold">Total Staff</h6>
                            <h2 class="font-weight-bold mb-0"><?php echo number_format($total_employees); ?></h2>
                        </div>
                        <i class="fas fa-users fa-2x text-white-50"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3 mb-md-0">
                <div class="card stat-card bg-success p-3 shadow-sm">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase mb-1 text-white-50 font-weight-bold">Monthly Payroll</h6>
                            <h2 class="font-weight-bold mb-0">₱<?php echo number_format((float)$total_payroll, 2); ?></h2>
                        </div>
                        <i class="fas fa-money-bill-wave fa-2x text-white-50"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card stat-card bg-info p-3 shadow-sm">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase mb-1 text-white-50 font-weight-bold">Average Salary</h6>
                            <h2 class="font-weight-bold mb-0">₱<?php echo number_format((float)$avg_salary, 2); ?></h2>
                        </div>
                        <i class="fas fa-calculator fa-2x text-white-50"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="card content-card p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="font-weight-bold text-dark mb-0"><i class="fas fa-users-cog text-success mr-2"></i> Employee List</h3>
            </div>
            <div class="table-responsive">
                <?php
                $sql = "SELECT * FROM employees ORDER BY id DESC";
                if($result = mysqli_query($link, $sql)){
                    if(mysqli_num_rows($result) > 0){
                        echo '<table id="employeeTable" class="table table-bordered table-hover align-middle w-100">';
                            echo "<thead class='thead-dark'><tr><th>#</th><th>Name</th><th>Address</th><th>Salary</th><th style='width: 240px;'>Actions</th></tr></thead>";
                            echo "<tbody>";
                            while($row = mysqli_fetch_array($result)){
                                echo "<tr>";
                                    echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                                    echo "<td><b>" . htmlspecialchars($row['name']) . "</b></td>";
                                    echo "<td>" . htmlspecialchars($row['address']) . "</td>";
                                    echo "<td>₱" . number_format((float)$row['salary'], 2) . "</td>";
                                    echo "<td>";
                                        echo '<a href="read.php?id='. $row['id'] .'" class="btn btn-info btn-sm mr-1"><i class="fas fa-eye"></i> View</a>';
                                        echo '<a href="update.php?id='. $row['id'] .'" class="btn btn-warning btn-sm mr-1"><i class="fas fa-edit"></i> Edit</a>';
                                        echo '<a href="delete.php?id='. $row['id'] .'" class="btn btn-danger btn-sm" onclick="return confirm(\'Sigurado ka bang gusto mong burahin ang empleyadong ito?\');"><i class="fas fa-trash"></i> Delete</a>';
                                    echo "</td>";
                                echo "</tr>";
                            }
                            echo "</tbody>";                            
                        echo "</table>";
                        mysqli_free_result($result);
                    } else {
                        echo '<div class="alert alert-warning text-center my-3"><em>No employee records found.</em></div>';
                    }
                }
                mysqli_close($link);
                ?>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap4.min.js"></script>
    <script>
        $(window).on('load', function() {
            setTimeout(function() { $('#preloader').addClass('fade-out'); }, 1800);
        });
        $(document).ready(function() {
            $('#employeeTable').DataTable({ "pageLength": 10, "ordering": true, "responsive": true });
        });
    </script>
</body>
</html>