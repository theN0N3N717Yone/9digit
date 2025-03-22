<?php
// Example user data
$userName = "John Doe";
$courseName = "Web Development";
$date = date("Y-m-d");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Certificate</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        .certificate {
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            border: 1px solid #ccc;
            position: relative;
        }

        .title {
            font-size: 30px;
            text-align: center;
        }

        .content {
            font-size: 20px;
            margin-top: 20px;
        }

        .user-name {
            font-weight: bold;
        }

        .course-name {
            margin-top: 10px;
        }

        .date {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="certificate">
        <div class="title">Certificate of Completion</div>
        <div class="content">
            <p>This is to certify that</p>
            <p class="user-name"><?= $userName ?></p>
            <p>has successfully completed the</p>
            <p class="course-name"><?= $courseName ?></p>
            <p class="date">Date: <?= $date ?></p>
        </div>
    </div>
</body>
</html>
