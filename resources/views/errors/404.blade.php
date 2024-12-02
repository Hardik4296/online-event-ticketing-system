<!-- resources/views/errors/404.blade.php -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Page Not Found</title>
    <link rel="stylesheet" href="{{ asset('css/errors.css') }}"> <!-- Link to the CSS -->
</head>

<body>
    <div class="container">
        <h1>404</h1>
        <h2>Page Not Found</h2>
        <p>Sorry, the page you're looking for doesn't exist or may have been moved.</p>
        <a href="{{ url('/') }}">Return to Home</a>
    </div>
</body>

</html>
