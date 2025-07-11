<!DOCTYPE html>
<html>
<head>
    <title>Discharge Successful</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        body {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            background-color: #f8f9fa;
            padding: 20px;
        }
        .success-card {
            max-width: 500px;
            width: 100%;
            text-align: center;
        }
        .success-icon {
            font-size: 3rem;
            color: #28a745;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <div class="card success-card">
        <div class="card-body">
            <i class="fas fa-check-circle success-icon"></i>
            <h3 class="mb-3">Discharge Successful</h3>
            <p class="lead">The patient has been discharged successfully.</p>
            <p>The bed is now available for new admissions.</p>
            <div class="text-center mt-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="sr-only">Refreshing...</span>
                </div>
                <p class="mt-2">Refreshing dashboard...</p>
            </div>
        </div>
    </div>

    <script>
        // Refresh parent window after a short delay
        setTimeout(function() {
            if (window.parent) {
                window.parent.location.reload();
            }
        }, 1500);
    </script>
</body>
</html> 