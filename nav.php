<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Portal - Navigation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3>🎓 Attendance Management Portal</h3>
                    </div>
                    <div class="card-body">
                        <h5>Choose Login Method:</h5>
                        
                        <div class="row mt-4">
                            <div class="col-md-6">
                                <div class="card border-primary">
                                    <div class="card-body text-center">
                                        <h6>🚀 Main Application</h6>
                                        <p class="text-muted">Full MVC Application</p>
                                        <a href="index.php?route=login" class="btn btn-primary">Login Page</a>
                                        <a href="index.php" class="btn btn-outline-primary btn-sm mt-2">Home</a>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="card border-success">
                                    <div class="card-body text-center">
                                        <h6>🔧 Simple Login</h6>
                                        <p class="text-muted">Direct Database Test</p>
                                        <a href="simple-login.php" class="btn btn-success">Simple Login</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <hr class="my-4">
                        
                        <h6>🔐 Login Credentials:</h6>
                        <div class="row">
                            <div class="col-md-4">
                                <strong>Admin:</strong><br>
                                <small>admin@school.com<br>admin123</small>
                            </div>
                            <div class="col-md-4">
                                <strong>Teacher:</strong><br>
                                <small>teacher@school.com<br>teacher123</small>
                            </div>
                            <div class="col-md-4">
                                <strong>Student:</strong><br>
                                <small>student@school.com<br>student123</small>
                            </div>
                        </div>
                        
                        <hr class="my-4">
                        
                        <h6>🛠️ Debug Tools:</h6>
                        <div class="btn-group" role="group">
                            <a href="debug.php" class="btn btn-outline-info btn-sm">System Debug</a>
                            <a href="test-connection.php" class="btn btn-outline-warning btn-sm">DB Test</a>
                            <a href="test-routes.php" class="btn btn-outline-secondary btn-sm">Route Test</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>