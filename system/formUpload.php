<?php
require_once('connectivity_functions.php');

$response = array(); // Initialize response array

if (isset($_POST['id'])) {
    $stmt = $conn->prepare("SELECT * FROM nsdlpancard WHERE id = ? AND order_id = ?");
    $stmt->execute([$_POST['id'], $_POST['order_id']]);
    $row = $stmt->fetch();

    if (empty($row)) {
        $response['status'] = 'error';
        $response['message'] = 'Application Data Not Available!';
    } else {
        if (isset($_POST['upload'])) {
            $allowedExts = array("pdf");
            $formexn = pathinfo($_FILES["form_pdf"]["name"], PATHINFO_EXTENSION);
            $maxFileSize = 2 * 1024 * 1024; // 2MB

            if (
                $_FILES["form_pdf"]["type"] == "application/pdf" &&
                $_FILES["form_pdf"]["size"] < $maxFileSize &&
                in_array($formexn, $allowedExts)
            ) {
                $form_pdf = str_replace(' ', '_', $row["name_card"]) . "-" . rand(100000, 999999) . "_Form.pdf";
                $form_link = "http://" . $_SERVER['SERVER_NAME'] . "/downloads.php?files=" . $form_pdf;
                $target_dir = "../pan_doc/";

                if (move_uploaded_file($_FILES["form_pdf"]["tmp_name"], $target_dir . $form_pdf)) {
                    $nsdlsql = "UPDATE nsdlpancard SET form_pdf=:form_pdf, remark=:remark, status=:status WHERE id=:row_id";
                    $nsdl_remark = 'From Upload Successfully';
                    $nsdl_status = 'PROCESS';

                    $nsdl = $conn->prepare($nsdlsql);
                    $nsdl->bindParam(":form_pdf", $form_link);
                    $nsdl->bindParam(":remark", $nsdl_remark);
                    $nsdl->bindParam(":status", $nsdl_status);
                    $nsdl->bindParam(":row_id", $row['id'], PDO::PARAM_INT);

                    if ($nsdl->execute()) {
                        $response['status'] = 'success';
                        $response['message'] = 'Form Upload Successfully!';
                    } else {
                        $response['status'] = 'error';
                        $response['message'] = 'Data Not Inserted!';
                    }
                } else {
                    $response['status'] = 'error';
                    $response['message'] = 'Failed to move uploaded file!';
                }
            } else {
                $response['status'] = 'error';
                $response['message'] = 'Invalid File Format or Size is Large!';
            }
        }
    }
}

// Set content type header and echo the JSON-encoded response
header('Content-Type: application/json');
echo json_encode($response);
?>
