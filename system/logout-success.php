<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logout Success</title>
    <!-- Include SweetAlert library -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
</head>
<body>
    <script>
        // Use SweetAlert to display the success message
        Swal.fire({
            title: 'Logout Successful',
            text: 'You have been successfully logged out.',
            icon: 'success',
            showConfirmButton: true,
            timer: 2000 // Adjust the timer as needed
        }).then(() => {
            // Redirect to another page or perform any additional actions
            window.location.href = '../auth-login.php'; // Redirect to the home page, for example
        });
    </script>
</body>
</html>
