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

// Handle Add Product Form Submission
$add_msg = "";
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["add_product"])) {
    $item_name = trim($_POST["name"]);
    $low_stock = (int)$_POST["low_stock"];
    $stock_out = (int)$_POST["stock_out"];

    if (!empty($item_name)) {
        $insert_sql = "INSERT INTO inventory (name, low_stock, stock_out) VALUES (?, ?, ?)";
        if ($stmt_add = mysqli_prepare($link, $insert_sql)) {
            mysqli_stmt_bind_param($stmt_add, "sii", $item_name, $low_stock, $stock_out);
            if (mysqli_stmt_execute($stmt_add)) {
                $add_msg = '<div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fas fa-check-circle mr-1"></i> Product added successfully!
                                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                            </div>';
            } else {
                $add_msg = '<div class="alert alert-danger">Error adding product: ' . mysqli_error($link) . '</div>';
            }
            mysqli_stmt_close($stmt_add);
        }
    }
}

// Search Query
$search = "";
if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
    $search = trim($_GET['search']);
    $sql = "SELECT * FROM inventory WHERE name LIKE ? ORDER BY id DESC";
} else {
    $sql = "SELECT * FROM inventory ORDER BY id DESC";
}

// Statistics Query
$total_items = 0;
$total_low_stock = 0;
$total_stock_out = 0;

$stats_query = "SELECT 
                    COUNT(*) as total_count, 
                    COALESCE(SUM(low_stock), 0) as total_low, 
                    COALESCE(SUM(stock_out), 0) as total_out 
                FROM inventory";

if ($stats_result = mysqli_query($link, $stats_query)) {
    $row_stats = mysqli_fetch_assoc($stats_result);
    $total_items = $row_stats['total_count'] ?? 0;
    $total_low_stock = $row_stats['total_low'] ?? 0;
    $total_stock_out = $row_stats['total_out'] ?? 0;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Management - Garden Cafe IPA</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body { background-color: #f4f6f9; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .navbar-brand { font-weight: 700; }
        .nav-link { font-weight: 500; }
        .nav-item.active .nav-link { color: #2ecc71 !important; border-bottom: 2px solid #2ecc71; }
        .main-container { margin-top: 25px; margin-bottom: 50px; }
        .welcome-banner { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); border-radius: 12px; padding: 22px; color: #ffffff; text-align: center; }
        .stat-card { border: none; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        .content-card { border: none; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.08); background: #ffffff; }
        .table thead th { background-color: #343a40; color: #ffffff; border: none; }
        .user-badge { background: rgba(255, 255, 255, 0.2); padding: 6px 14px; border-radius: 20px; }
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
                    <li class="nav-item active"><a class="nav-link" href="inventory.php"><i class="fas fa-boxes mr-1"></i> Inventory / Stocks</a></li>
                    <li class="nav-item"><a class="nav-link text-light" href="reports.php"><i class="fas fa-chart-line mr-1"></i> Reports</a></li>
                </ul>
                <ul class="navbar-nav ml-auto align-items-center">
                    <li class="nav-item mr-3">
                        <span class="navbar-text text-light user-badge"><i class="fas fa-user-circle mr-1"></i> Hi, <b><?php echo htmlspecialchars((string)($_SESSION["username"] ?? 'User')); ?></b></span>
                    </li>
                    <li class="nav-item"><a href="logout.php" class="btn btn-outline-danger btn-sm"><i class="fas fa-sign-out-alt mr-1"></i> Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid px-4 main-container">
        <?php echo $add_msg; ?>

        <div class="welcome-banner mb-4">
            <h3 class="font-weight-bold text-uppercase mb-1"><i class="fas fa-boxes mr-2"></i> Garden Cafe Inventory & Stock Control</h3>
            <p class="mb-0 opacity-90">Monitor supplies, low stock alerts, and stock outs in real time.</p>
        </div>

        <div class="row mb-4">
            <div class="col-md-4 mb-3">
                <div class="card stat-card bg-primary text-white p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase mb-1" style="opacity: 0.85;">Total Inventory Items</h6>
                            <h2 class="mb-0 font-weight-bold"><?php echo number_format($total_items); ?></h2>
                        </div>
                        <i class="fas fa-box-open fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card stat-card bg-warning text-white p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase mb-1" style="opacity: 0.85;">Low Stock Threshold Total</h6>
                            <h2 class="mb-0 font-weight-bold"><?php echo number_format($total_low_stock); ?></h2>
                        </div>
                        <i class="fas fa-exclamation-triangle fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card stat-card bg-danger text-white p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase mb-1" style="opacity: 0.85;">Out of Stock Total</h6>
                            <h2 class="mb-0 font-weight-bold"><?php echo number_format($total_stock_out); ?></h2>
                        </div>
                        <i class="fas fa-times-circle fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="card content-card p-4">
            <div class="row align-items-center mb-4">
                <div class="col-md-4 mb-2 mb-md-0">
                    <h3 class="font-weight-bold text-dark mb-0"><i class="fas fa-warehouse text-success mr-2"></i> Stock List</h3>
                </div>
                <div class="col-md-5 mb-2 mb-md-0">
                    <form action="inventory.php" method="GET">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" placeholder="Search item name..." value="<?php echo htmlspecialchars((string)$search); ?>">
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-success"><i class="fas fa-search"></i> Search</button>
                                <?php if (!empty($search)): ?>
                                    <a href="inventory.php" class="btn btn-secondary"><i class="fas fa-undo"></i> Reset</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </form>
                </div>
                <!-- ADD PRODUCT BUTTON -->
                <div class="col-md-3 text-md-right">
                    <button type="button" class="btn btn-success" data-toggle="modal" data-target="#addProductModal">
                        <i class="fas fa-plus-circle mr-1"></i> Add Product
                    </button>
                </div>
            </div>

            <div class="table-responsive">
                <?php
                if ($stmt = mysqli_prepare($link, $sql)) {
                    if (!empty($search)) {
                        $param_search = "%" . $search . "%";
                        mysqli_stmt_bind_param($stmt, "s", $param_search);
                    }
                    if (mysqli_stmt_execute($stmt)) {
                        $result = mysqli_stmt_get_result($stmt);
                        if (mysqli_num_rows($result) > 0) {
                            echo '<table class="table table-hover table-bordered align-middle">';
                                echo "<thead><tr><th style='width: 60px;'>#</th><th>Item Name</th><th>Low Stock Alert Level</th><th>Stock Out Status</th></tr></thead>";
                                echo "<tbody>";
                                while ($row = mysqli_fetch_array($result)) {
                                    $item_id = htmlspecialchars((string)($row['id'] ?? ''));
                                    $item_name = htmlspecialchars((string)($row['name'] ?? 'Unnamed Product'));
                                    $low_stock_val = htmlspecialchars((string)($row['low_stock'] ?? '0'));
                                    $is_out = (int)($row['stock_out'] ?? 0) > 0;

                                    $status_badge = $is_out 
                                        ? '<span class="badge badge-danger p-2"><i class="fas fa-times-circle mr-1"></i> Out of Stock</span>' 
                                        : '<span class="badge badge-success p-2"><i class="fas fa-check-circle mr-1"></i> In Stock</span>';

                                    echo "<tr>";
                                        echo "<td><span class='badge badge-secondary'>" . $item_id . "</span></td>";
                                        echo "<td><b>" . $item_name . "</b></td>";
                                        echo "<td><span class='badge badge-warning text-dark'>" . $low_stock_val . " units threshold</span></td>";
                                        echo "<td>" . $status_badge . "</td>";
                                    echo "</tr>";
                                }
                                echo "</tbody></table>";
                        } else {
                            echo '<div class="alert alert-warning text-center my-4"><i class="fas fa-exclamation-triangle mr-2"></i> No inventory items found.</div>';
                        }
                    }
                    mysqli_stmt_close($stmt);
                }
                mysqli_close($link);
                ?>
            </div>
        </div>
    </div>

    <!-- ADD PRODUCT MODAL -->
    <div class="modal fade" id="addProductModal" tabindex="-1" role="dialog" aria-labelledby="addProductModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="inventory.php" method="POST">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title font-weight-bold" id="addProductModalLabel"><i class="fas fa-plus-circle mr-1"></i> Add New Product</h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label class="font-weight-bold">Product Name</label>
                            <input type="text" name="name" class="form-control" placeholder="e.g. Coffee Beans" required>
                        </div>
                        <div class="form-group">
                            <label class="font-weight-bold">Low Stock Alert Level</label>
                            <input type="number" name="low_stock" class="form-control" placeholder="e.g. 5" min="0" value="5" required>
                        </div>
                        <div class="form-group">
                            <label class="font-weight-bold">Stock Status</label>
                            <select name="stock_out" class="form-control">
                                <option value="0">In Stock</option>
                                <option value="1">Out of Stock</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" name="add_product" class="btn btn-success"><i class="fas fa-save mr-1"></i> Save Product</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>