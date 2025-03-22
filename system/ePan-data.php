<?php
require_once('connectivity_functions.php'); // Make sure to provide the correct file extension
$conn = connectDB();

$stmt = $conn->prepare("select * from nsdlTransaction WHERE status != 'process' AND userId=? ORDER BY `id` DESC");
$stmt->execute([getUsersInfo('id')]);

if (isset($_POST['daterange'])) {
    $dateRange = $_POST['daterange'];
    list($fromdate, $todate) = explode(' to ', $dateRange);

    $stmt = $conn->prepare("SELECT * FROM nsdlTransaction WHERE date BETWEEN :fromdate AND :todate AND userId = :userId AND status != 'process' ORDER BY `id` DESC");
    $stmt->execute([
        'fromdate' => $fromdate,
        'todate' => $todate,
        'userId' => getUsersInfo('id')
    ]);
} else {
    // Default values if the form is not submitted
    $fromdate = $date;
    $todate = $date;

    // Fetch data without filtering
    $stmt = $conn->prepare("SELECT * FROM nsdlTransaction WHERE userId = :userId AND status != 'process' ORDER BY `id` DESC");
    $stmt->execute([
        'userId' => getUsersInfo('id')
    ]);
}

$stmt = $conn->prepare("select * from nsdlTransaction WHERE date between '".$fromdate."' AND '".$todate."' AND userId=? AND status != 'process' ORDER BY `id` DESC");
$stmt->execute([getUsersInfo('id')]);

if(!empty($_POST['search'])){

$search = $_POST['search']; 
$stmt = $conn->prepare("select * from nsdlTransaction WHERE orderId LIKE '{$search}%' OR mobNumber LIKE '{$search}%' OR nsdlStatus LIKE '{$search}%' 
 AND userId=? AND status != 'process' ORDER BY `id` DESC");
$stmt->execute([getUsersInfo('id')]);
}


$sl=1;
// Output fetched data as HTML table rows
while($row = $stmt->fetch()) {
    echo "<tr>
            <td style='display:none'>{$sl}</td>
            <td>{$row['orderId']}<br>" . getUsersInfo('username') . "</td>
            <td>" . date('d M Y : h:i A', strtotime($row['time'])) . "</td>
            <td>{$row['mobNumber']}</td>
            <td>{$row['nsdlAck']}<br>{$row['nsdlStatus']}</td>
            <td class='text-end'>{$row['status']}<br></td>
          </tr>";
    $sl++;
}
?>
