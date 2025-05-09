<?php
// Set session cookie parameters for a 1-hour timeout
// This must be called before session_start()
ini_set('session.gc_maxlifetime', 3600); // 1 hour in seconds
session_set_cookie_params(3600); // 1 hour in seconds

// Initialize the session
session_start();

// Check if the user is logged in, if not then redirect to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

// Include database connection and functions
require_once "../config/db.php";
require_once "../functions/image_functions.php";

// Process form submission for adding new partner logo
if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["add_partner"])){
    $name = trim($_POST["name"]);
    $error = "";
    
    // Check if name is empty
    if(empty($name)){
        $error = "Please enter partner name.";
    }
    
    if(empty($error)) {
        // Use our image upload function
        $upload_result = uploadPartnerLogo($_FILES["logo"], $name);
        
        if($upload_result['status']) {
            // File uploaded successfully, now save to database
            $logo_path = $upload_result['path'];
            
            // Prepare an insert statement
            $sql = "INSERT INTO partners (name, logo_path) VALUES (?, ?)";
            
            if($stmt = $mysqli->prepare($sql)){
                // Bind variables to the prepared statement as parameters
                $stmt->bind_param("ss", $param_name, $param_logo_path);
                
                // Set parameters
                $param_name = $name;
                $param_logo_path = $logo_path;
                
                // Attempt to execute the prepared statement
                if($stmt->execute()){
                    // Redirect to dashboard with success message
                    $_SESSION['success_msg'] = "Partner logo added successfully.";
                    header("location: index.php");
                    exit();
                } else{
                    $error = "Something went wrong. Please try again later.";
                }
                
                // Close statement
                $stmt->close();
            }
        } else {
            $error = $upload_result['message'];
        }
    }
    
    // If we got here, there was an error
    $_SESSION['error_msg'] = $error;
}

// Process deletion of partner logo
if(isset($_GET['delete']) && is_numeric($_GET['delete'])){
    $id = $_GET['delete'];
    
    // First get the logo path to delete the file
    $sql = "SELECT logo_path FROM partners WHERE id = ?";
    if($stmt = $mysqli->prepare($sql)){
        $stmt->bind_param("i", $param_id);
        $param_id = $id;
        
        if($stmt->execute()){
            $stmt->store_result();
            
            if($stmt->num_rows == 1){
                $stmt->bind_result($logo_path);
                if($stmt->fetch()){
                    // Use our delete function
                    deletePartnerLogo($logo_path);
                }
            }
        }
        $stmt->close();
    }
    
    // Now delete from database
    $sql = "DELETE FROM partners WHERE id = ?";
    if($stmt = $mysqli->prepare($sql)){
        $stmt->bind_param("i", $param_id);
        $param_id = $id;
        
        if($stmt->execute()){
            $_SESSION['success_msg'] = "Partner logo deleted successfully.";
        } else {
            $_SESSION['error_msg'] = "Error deleting partner logo.";
        }
        
        $stmt->close();
    }
    
    header("location: index.php");
    exit();
}

// Fetch all partner logos
$partners = [];
$sql = "SELECT id, name, logo_path FROM partners ORDER BY id DESC";
if($result = $mysqli->query($sql)){
    while($row = $result->fetch_assoc()){
        $partners[] = $row;
    }
    $result->free();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Partner Logos Admin Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
    <style>
        body {
            padding-top: 20px;
            background-color: #f8f9fa;
        }
        .wrapper {
            width: 100%;
            padding: 20px;
        }
        .logo-preview {
            width: 100px;
            height: 60px;
            object-fit: contain;
            background-color: #fff;
            padding: 5px;
            border-radius: 4px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .table-responsive {
            margin-top: 20px;
        }
        .alert {
            margin-bottom: 20px;
        }
        .card {
            margin-bottom: 20px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .card-header {
            background-color: #0056b3;
            color: white;
            font-weight: bold;
        }
        .btn-logout {
            position: absolute;
            top: 20px;
            right: 20px;
        }
        .dashboard-header {
            display: flex;
            align-items: center;
            margin-bottom: 30px;
        }
        .dashboard-logo {
            height: 50px;
            margin-right: 15px;
        }
        .dashboard-title {
            margin: 0;
            color: #0056b3;
        }
        .btn-view-site {
            margin-left: auto;
            margin-right: 10px;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="dashboard-header">
            <img src="../assets/images/logo.jpg" alt="Logo" class="dashboard-logo">
            <h1 class="dashboard-title">Partner Logos Admin Dashboard</h1>
            <a href="../index.html" class="btn btn-info btn-view-site" target="_blank">
                <i class="fas fa-external-link-alt"></i> View Website
            </a>
            <a href="logout.php" class="btn btn-danger">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
        
        <?php if(isset($_SESSION['success_msg'])): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <?php echo $_SESSION['success_msg']; unset($_SESSION['success_msg']); ?>
            </div>
        <?php endif; ?>
        
        <?php if(isset($_SESSION['error_msg'])): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i> <?php echo $_SESSION['error_msg']; unset($_SESSION['error_msg']); ?>
            </div>
        <?php endif; ?>
        
        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-plus-circle"></i> Add New Partner Logo
                    </div>
                    <div class="card-body">
                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
                            <div class="form-group">
                                <label><i class="fas fa-building"></i> Partner Name</label>
                                <input type="text" name="name" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label><i class="fas fa-image"></i> Logo Image (PNG, JPG, JPEG, GIF)</label>
                                <input type="file" name="logo" class="form-control-file" required>
                                <small class="form-text text-muted">Max file size: 5MB. Recommended size: 200x100px</small>
                            </div>
                            <div class="form-group">
                                <button type="submit" name="add_partner" class="btn btn-primary btn-block">
                                    <i class="fas fa-save"></i> Add Partner
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-list"></i> Manage Partner Logos
                    </div>
                    <div class="card-body">
                        <?php if(empty($partners)): ?>
                            <div class="text-center py-5">
                                <i class="fas fa-info-circle fa-3x text-muted mb-3"></i>
                                <p class="lead">No partner logos found. Add your first partner logo using the form.</p>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>ID</th>
                                            <th>Name</th>
                                            <th>Logo</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($partners as $partner): ?>
                                            <tr>
                                                <td><?php echo $partner['id']; ?></td>
                                                <td><?php echo htmlspecialchars($partner['name']); ?></td>
                                                <td>
                                                    <img src="../<?php echo htmlspecialchars($partner['logo_path']); ?>" alt="<?php echo htmlspecialchars($partner['name']); ?>" class="logo-preview">
                                                </td>
                                                <td>
                                                    <a href="index.php?delete=<?php echo $partner['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this partner logo?')">
                                                        <i class="fas fa-trash"></i> Delete
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
