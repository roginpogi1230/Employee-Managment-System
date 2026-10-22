<?php
session_start();

if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    header("location: index.php");
    exit;
}

require_once "config.php";

$username = $password = "";
$username_err = $password_err = $login_err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (empty(trim($_POST["username"]))) {
        $username_err = "Please enter username.";
    } else {
        $username = trim($_POST["username"]);
    }

    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter your password.";
    } else {
        $password = trim($_POST["password"]);
    }

    if (empty($username_err) && empty($password_err)) {
        $sql = "SELECT id, username, password, role FROM users WHERE username = ?";

        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "s", $param_username);
            $param_username = $username;

            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_store_result($stmt);

                if (mysqli_stmt_num_rows($stmt) == 1) {
                    mysqli_stmt_bind_result($stmt, $id, $username, $hashed_password, $role);
                    if (mysqli_stmt_fetch($stmt)) {
                        if (password_verify($password, $hashed_password) || $password === $hashed_password) {
                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["username"] = $username;
                            $_SESSION["role"] = !empty($role) ? $role : 'admin';

                            header("location: index.php");
                            exit;
                        } else {
                            $login_err = "Invalid username or password.";
                        }
                    }
                } else {
                    $login_err = "Invalid username or password.";
                }
            } else {
                $login_err = "Oops! Something went wrong. Please try again later.";
            }
            mysqli_stmt_close($stmt);
        }
    }
    mysqli_close($link);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Management System - Login</title>
    
    <!-- Custom Circular Favicon: Employee Management System -->
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'%3E%3Ccircle cx='50' cy='50' r='48' fill='white' stroke='%230b3b24' stroke-width='3'/%3E%3Ctext x='50%25' y='36%25' font-family='Georgia, serif' font-style='italic' font-weight='bold' font-size='10' fill='%230b3b24' text-anchor='middle' dominant-baseline='middle'%3EEmployee%3C/text%3E%3Ctext x='50%25' y='52%25' font-family='Georgia, serif' font-style='italic' font-weight='bold' font-size='9' fill='%230b3b24' text-anchor='middle' dominant-baseline='middle'%3EManagement%3C/text%3E%3Ctext x='50%25' y='68%25' font-family='Georgia, serif' font-style='italic' font-weight='bold' font-size='10' fill='%230b3b24' text-anchor='middle' dominant-baseline='middle'%3ESystem%3C/text%3E%3C/svg%3E">

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body, html {
            height: 100%;
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            overflow: hidden;
        }

        .bg-garden {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(rgba(0, 0, 0, 0.55), rgba(0, 0, 0, 0.55)),
                        url('https://images.unsplash.com/photo-1518531933037-91b2f5f229cc?q=80&w=1920&auto=format&fit=crop') no-repeat center center/cover;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 15px;
        }

        .login-card-container {
            width: 100%;
            max-width: 420px;
            z-index: 1;
        }

        .glass-card {
            background: rgba(30, 41, 59, 0.85);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 20px;
            padding: 35px 30px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.5);
            color: #ffffff;
        }

        .card-logo-circle {
            width: 90px;
            height: 90px;
            border-radius: 50%;
            background: #ffffff;
            margin: 0 auto 15px auto;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
            font-family: 'Georgia', serif;
            font-style: italic;
            font-weight: bold;
            color: #0b3b24;
            text-align: center;
            padding: 8px;
            box-sizing: border-box;
            overflow: hidden;
        }

        .card-logo-circle .title-top { font-size: 0.65rem; line-height: 1.1; white-space: nowrap; }
        .card-logo-circle .title-mid { font-size: 0.55rem; line-height: 1.1; white-space: nowrap; }
        .card-logo-circle .title-bottom { font-size: 0.65rem; line-height: 1.1; white-space: nowrap; }

        .form-control-custom {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #ffffff;
            border-radius: 10px;
            padding: 12px 15px;
        }

        .form-control-custom:focus {
            background: rgba(255, 255, 255, 0.15);
            color: #ffffff;
            border-color: #2ecc71;
            box-shadow: none;
        }

        .form-control-custom::placeholder {
            color: rgba(255, 255, 255, 0.6);
        }

        .input-group-text-custom {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #2ecc71;
            border-top-left-radius: 10px;
            border-bottom-left-radius: 10px;
        }

        .btn-success-custom {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            border: none;
            color: white;
            font-weight: 600;
            padding: 12px;
            border-radius: 10px;
            transition: all 0.3s ease;
        }

        .btn-success-custom:hover {
            opacity: 0.9;
            transform: translateY(-1px);
        }
    </style>
</head>
<body>

    <div class="bg-garden">
        <div class="login-card-container">
            <div class="glass-card text-center">
                <div class="card-logo-circle">
                    <span class="title-top">Employee</span>
                    <span class="title-mid">Management</span>
                    <span class="title-bottom">System</span>
                </div>
                <h4 class="font-weight-bold mb-1">Employee Portal</h4>
                <p class="text-white-50 small mb-4">Inventory & Staff Management System</p>

                <?php if(!empty($login_err)): ?>
                    <div class="alert alert-danger p-2 small rounded mb-3"><i class="fas fa-exclamation-circle mr-1"></i> <?php echo $login_err; ?></div>
                <?php endif; ?>

                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" class="text-left">
                    <div class="form-group mb-3">
                        <label class="small font-weight-bold text-white-50">Username</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text input-group-text-custom"><i class="fas fa-user"></i></span>
                            </div>
                            <input type="text" name="username" class="form-control form-control-custom <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($username); ?>" placeholder="Enter username" required>
                        </div>
                    </div>    

                    <div class="form-group mb-4">
                        <label class="small font-weight-bold text-white-50">Password</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text input-group-text-custom"><i class="fas fa-lock"></i></span>
                            </div>
                            <input type="password" name="password" class="form-control form-control-custom <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>" placeholder="Enter password" required>
                        </div>
                    </div>

                    <div class="form-group mb-0">
                        <button type="submit" class="btn btn-success-custom btn-block shadow-sm">
                            <i class="fas fa-sign-in-alt mr-1"></i> Login
                        </button>
                    </div>
                </form>
                <div class="mt-4 text-center">
                    <small class="text-white-50" style="font-size: 0.75rem;">© Employee Management System Portal</small>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>