<?php
// index.php - Employee Management System Intro & Main Dashboard
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
        body, html {
            height: 100%;
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f6f9;
        }

        /* Intro Preloader Screen Overlay */
        #intro-screen {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(rgba(0, 0, 0, 0.65), rgba(0, 0, 0, 0.65)),
                        url('https://images.unsplash.com/photo-1518531933037-91b2f5f229cc?q=80&w=1920&auto=format&fit=crop') no-repeat center center/cover;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            transition: opacity 0.8s ease, visibility 0.8s ease;
            padding: 20px;
        }

        /* Circular Logo Badge sa Intro */
        .intro-logo-badge {
            width: 110px;
            height: 110px;
            border-radius: 50%;
            background: #ffffff;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            box-shadow: 0 0 25px rgba(0, 0, 0, 0.6);
            margin-bottom: 20px;
            padding: 8px;
            text-align: center;
            font-family: 'Georgia', serif;
            font-style: italic;
            font-weight: bold;
            color: #0b3b24;
            overflow: hidden;
            box-sizing: border-box;
        }

        .intro-logo-badge .title-top { font-size: 0.65rem; line-height: 1.1; white-space: nowrap; }
        .intro-logo-badge .title-mid { font-size: 0.55rem; line-height: 1.1; white-space: nowrap; }
        .intro-logo-badge .title-bottom { font-size: 0.65rem; line-height: 1.1; white-space: nowrap; }

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
            animation: typing 2.5s steps(40, end) forwards, blinkCursor 0.75s step-end infinite alternate;
            text-shadow: 0 3px 12px rgba(0,0,0,0.8);
            letter-spacing: 0.5px;
        }

        .intro-subtext {
            color: rgba(255, 255, 255, 0.8);
            margin-top: 10px;
            font-size: 0.95rem;
            letter-spacing: 1px;
            text-transform: uppercase;
            text-shadow: 0 2px 8px rgba(0,0,0,0.6);
        }

        @keyframes typing { from { width: 0; } to { width: 100%; } }
        @keyframes blinkCursor { from, to { border-color: transparent; } 50% { border-color: #2ecc71; } }

        @media (max-width: 768px) {
            .animated-welcome-text { font-size: 1.1rem; }
            .intro-logo-badge { width: 90px; height: 90px; }
        }

        /* Main Dashboard Styling */
        .main-content {
            padding: 30px;
        }
        .main-banner {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            color: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            margin-bottom: 25px;
        }
    </style>
</head>
<body>

    <!-- Intro / Welcome Animation Overlay -->
    <div id="intro-screen">
        <div class="intro-logo-badge">
            <span class="title-top">Employee</span>
            <span class="title-mid">Management</span>
            <span class="title-bottom">System</span>
        </div>
        <div class="animated-welcome-wrapper">
            <div class="animated-welcome-text">
                Welcome To Employee Management System
            </div>
        </div>
        <div class="intro-subtext">Employee Management & Staff Control Portal</div>
    </div>

    <!-- Main Dashboard View -->
    <div class="container my-4 main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="font-weight-bold text-dark"><i class="fas fa-users-cog mr-2"></i>EMS Dashboard</h4>
            <div>
                <span class="mr-3 font-weight-bold text-secondary">Hello, <?php echo htmlspecialchars($_SESSION["username"]); ?>!</span>
                <a href="logout.php" class="btn btn-outline-danger btn-sm"><i class="fas fa-sign-out-alt mr-1"></i> Logout</a>
            </div>
        </div>

        <div class="main-banner text-center">
            <h2><i class="fas fa-id-card mr-2"></i> EMPLOYEE MANAGEMENT SYSTEM</h2>
            <p class="mb-0 text-white-50">Manage staff records, departments, and monitor employee availability seamlessly.</p>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="font-weight-bold mb-0 text-dark"><i class="fas fa-list mr-2"></i> Employee Directory</h5>
                <a href="create.php" class="btn btn-success btn-sm"><i class="fas fa-user-plus mr-1"></i> Add Employee</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="thead-dark">
                            <tr>
                                <th style="width: 60px;">#</th>
                                <th>Full Name</th>
                                <th>Position</th>
                                <th>Department</th>
                                <th>Status</th>
                                <th class="text-center" style="width: 140px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1</td>
                                <td class="font-weight-bold">Juan Dela Cruz</td>
                                <td>Administrator</td>
                                <td>IT Department</td>
                                <td><span class="badge badge-success px-2 py-1">Active</span></td>
                                <td class="text-center">
                                    <a href="update.php?id=1" class="text-primary mr-2"><i class="fas fa-edit"></i></a>
                                    <a href="read.php?id=1" class="text-info mr-2"><i class="fas fa-eye"></i></a>
                                    <a href="delete.php?id=1" class="text-danger"><i class="fas fa-trash-alt"></i></a>
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
    <script>
        // JavaScript para kusang mawala ang intro screen pagkatapos ng ilang segundo
        document.addEventListener("DOMContentLoaded", function () {
            const introScreen = document.getElementById('intro-screen');
            setTimeout(function () {
                introScreen.style.opacity = '0';
                introScreen.style.visibility = 'hidden';
            }, 3200); // Mag-a-fade out pagkalipas ng 3.2 segundo
        });
    </script>
</body>
</html>