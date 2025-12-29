<!DOCTYPE html>
<html>
<head>
    <title>Route Testing</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>🧪 Route Testing</h2>
        
        <div class="row">
            <div class="col-md-6">
                <h4>Test Routes:</h4>
                <ul class="list-group">
                    <li class="list-group-item">
                        <a href="index.php?route=" class="btn btn-sm btn-primary">Home</a>
                        <code>index.php?route=</code>
                    </li>
                    <li class="list-group-item">
                        <a href="index.php?route=login" class="btn btn-sm btn-success">Login</a>
                        <code>index.php?route=login</code>
                    </li>
                    <li class="list-group-item">
                        <a href="index.php?route=dashboard" class="btn btn-sm btn-info">Dashboard</a>
                        <code>index.php?route=dashboard</code>
                    </li>
                </ul>
            </div>
            
            <div class="col-md-6">
                <h4>Current Request Info:</h4>
                <table class="table table-sm">
                    <tr><td>REQUEST_URI</td><td><?= $_SERVER['REQUEST_URI'] ?></td></tr>
                    <tr><td>QUERY_STRING</td><td><?= $_SERVER['QUERY_STRING'] ?? 'None' ?></td></tr>
                    <tr><td>Route Param</td><td><?= $_GET['route'] ?? 'None' ?></td></tr>
                    <tr><td>PHP SAPI</td><td><?= php_sapi_name() ?></td></tr>
                </table>
            </div>
        </div>
        
        <hr>
        <p><a href="nav.php" class="btn btn-secondary">Back to Navigation</a></p>
    </div>
</body>
</html>