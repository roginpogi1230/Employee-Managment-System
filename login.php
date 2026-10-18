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
    <title>Login - Employee Management System</title>
    
    <!-- Custom Circular Favicon na kapareho ng Logo Badge -->
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><circle cx='50' cy='50' r='50' fill='%23ffffff'/><text x='50' y='36' font-family='Georgia, serif' font-style='italic' font-weight='bold' font-size='18' fill='%231b4332' text-anchor='middle'>Employee</text><text x='50' y='55' font-family='Georgia, serif' font-style='italic' font-weight='bold' font-size='14' fill='%231b4332' text-anchor='middle'>&amp;</text><text x='50' y='75' font-family='Georgia, serif' font-style='italic' font-weight='bold' font-size='16' fill='%231b4332' text-anchor='middle'>Management</text></svg>">

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body, html {
            height: 100%;
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            overflow: hidden;
        }

        /* Background Garden Image - Naka-center na ang laman */
        .bg-garden {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)),
                        url('https://images.unsplash.com/photo-1518531933037-91b2f5f229cc?q=80&w=1920&auto=format&fit=crop') no-repeat center center/cover;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 15px;
        }

        /* Intro Preloader Screen Overlay */
        #intro-screen {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(rgba(0, 0, 0, 0.75), rgba(0, 0, 0, 0.75)),
                        url('https://images.unsplash.com/photo-1518531933037-91b2f5f229cc?q=80&w=1920&auto=format&fit=crop') no-repeat center center/cover;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            transition: opacity 0.8s ease, visibility 0.8s ease;
            padding: 20px;
        }

        /* Circular Logo Badge */
        .intro-logo-badge {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: #ffffff;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            box-shadow: 0 0 25px rgba(0, 0, 0, 0.6);
            margin-bottom: 25px;
            padding: 10px;
            text-align: center;
            font-family: 'Georgia', serif;
            font-style: italic;
            font-weight: bold;
            color: #1b4332;
            overflow: hidden;
            box-sizing: border-box;
        }

        .intro-logo-badge .title-top {
            font-size: 0.85rem;
            line-height: 1;
            white-space: nowrap;
        }

        .intro-logo-badge .title-amp {
            font-size: 0.65rem;
            line-height: 1;
            margin: 2px 0;
        }

        .intro-logo-badge .title-bottom {
            font-size: 0.8rem;
            line-height: 1;
            white-space: nowrap;
        }

        /* Animated Welcome Text */
        .animated-welcome-wrapper {
            display: inline-block;
            max-width: 100%;
            text-align: center;
        }

        .animated-welcome-text {
            font-size: 2.2rem;
            font-weight: 800;
            color: #ffffff;
            border-right: 3px solid #2ecc71;
            white-space: nowrap;
            overflow: hidden;
            display: inline-block;
            width: 0;
            animation: typing 2.2s steps(38, end) forwards, blinkCursor 0.75s step-end infinite alternate;
            text-shadow: 0 3px 12px rgba(0,0,0,0.8);
            letter-spacing: 0.5px;
        }

        @keyframes typing { from { width: 0; } to { width: 100%; } }
        @keyframes blinkCursor { from, to { border-color: transparent; } 50% { border-color: #2ecc71; } }

        /* Glassmorphism Login Card */
        .login-card-container {
            opacity: 0;
            transform: translateY(20px);
            transition: opacity 0.8s ease, transform 0.8s ease;
            width: 100%;
            max-width: 420px;
            z-index: 1;
        }

        .login-card-container.show {
            opacity: 1;
            transform: translateY(0);
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
            color: #1b4332;
            text-align: center;
            padding: 8px;
            box-sizing: border-box;
        }

        .card-logo-circle .title-top { font-size: 0.75rem; line-height: 1; }
        .card-logo-circle .title-amp { font-size: 0.55rem; margin: 1px 0; }
        .card-logo-circle .title-bottom { font-size: 0.7rem; line-height: 1; }

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

        @media (max-width: 768px) {
            .animated-welcome-text { font-size: 1.2rem; }
            .intro-logo-badge { width: 100px; height: 100px; }
        }
    </style>
</head>
<body>

    <!-- Intro Screen Animation -->
    <div id="intro-screen">
        <div class="intro-logo-badge">
            <span class="title-top">Employee</span>
            <span class="title-amp">&</span>
            <span class="title-bottom">Management</span>
        </div>
        <div class="animated-welcome-wrapper">
            <div class="animated-welcome-text">
                Welcome To Employee Management System
            </div>
        </div>
    </div>

    <!-- Main Login View -->
    <div class="bg-garden">
        <div class="login-card-container" id="loginWrapper">
            <div class="glass-card text-center">
                <div class="card-logo-circle">
                    <span class="title-top">Employee</span>
                    <span class="title-amp">&</span>
                    <span class="title-bottom">Management</span>
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
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const introScreen = document.getElementById('intro-screen');
            const loginWrapper = document.getElementById('loginWrapper');
            const hasError = <?php echo (!empty($login_err) || !empty($username_err) || !empty($password_err)) ? 'true' : 'false'; ?>;

            if (hasError) {
                introScreen.style.display = 'none';
                loginWrapper.classList.add('show');
            } else {
                setTimeout(function () {
                    introScreen.style.opacity = '0';
                    introScreen.style.visibility = 'hidden';
                    setTimeout(function() {
                        loginWrapper.classList.add('show');
                    }, 300);
                }, 2800); 
            }
        });
    </script>
</body>
</html>