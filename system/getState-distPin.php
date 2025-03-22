<?php
require_once('connectivity_functions.php');
$conn = connectDB();
if (isset($_GET['pincode'])) {
    $providedPincode = $_GET['pincode'];

    $stmt = $conn->prepare("SELECT `id`, `statename`, `districtname`, `taluk` FROM `pincodes` WHERE `pincode` = :providedPincode");
    $stmt->bindParam(':providedPincode', $providedPincode, PDO::PARAM_STR);
    $stmt->execute();

    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        echo json_encode($result);
    } else {
        echo json_encode(["error" => "Pin code not found"]);
    }
}

if (isset($_GET['state'])) {
    $selectedState = $_GET['state'];

    $stmt = $conn->prepare("SELECT `District` FROM `stateDist` WHERE `State` = :providedState");
    $stmt->bindParam(':providedState', $selectedState, PDO::PARAM_STR);
    $stmt->execute();

    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($results) {
        echo json_encode($results);
    } else {
        echo json_encode(["error" => "Districts not found for the provided state"]);
    }
}






// Return districts as options for the dropdown

?>
