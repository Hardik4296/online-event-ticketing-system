<!-- resources/views/errors/403.blade.php -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Forbidden</title>
    <link rel="stylesheet" href="{{ asset('css/errors.css') }}"> <!-- Add your CSS file if needed -->
</head>

<body>
    <div class="container text-center">
        <h1>403</h1>
        <h2>Session Expired</h2>
        <p>Please log in again. Your session has expired.</p>
        <a href="{{ url('/') }}" class="btn btn-primary">Go to Home</a>
    </div>
</body>

</html>
