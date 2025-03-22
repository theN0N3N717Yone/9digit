<?php
$pageName = "Form Upload"; // Replace this with the actual page name
$_SESSION['userAuth'] = "User Authentication";
require_once('../layouts/mainHeader.php');
?>

<div class="content-wrapper">
    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- DataTales Example -->
        <div class="card shadow-lg ps-background-table">
            <div class="card-header">
                <h4 style="color:#333">Form Upload / Edit</h4>
            </div>
            <div class="card-body">
                <div id="demo_info" class="box table-responsive text-nowrap">
                    <table id="example" class="table table-striped">
                        <thead style="background: #000cad;">
                            <tr>
                                <th style="display:none;">#</th>
                                <th style="color: #fff">ORDER</th>
                                <th style="color: #fff">Name</th>
                                <th style="color: #fff">Father Name</th>
                                <th style="color: #fff">type</th>
                                <th style="color: #fff">Forms</th>
                                <th style="color: #fff">UPLOAD</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            <?php
                            $stmt = $conn->prepare("select * from nsdlpancard WHERE remark='From Upload Panding' AND user_id=? ORDER BY `id` DESC");
                            $stmt->execute([$userdata['id']]);
                            $sl = 1;
                            while ($row = $stmt->fetch()) {
                                $usql = $conn->prepare("select * from users WHERE id = ?");
                                $usql->execute([$row['user_id']]);
                                $usr_d = $usql->fetch();

                                if (strtoupper($row['pan_number']) == "NEWPAN") {
                                    $type = 'newpan';
                                    $t_type = 'NEW';
                                    $modle = '49A';
                                    $pan_number = '';
                                } else {
                                    $type = 'csfpan';
                                    $t_type = 'CSF';
                                    $modle = 'CSF';
                                    $pan_number = $row['pan_number'];
                                }

                                if (strtoupper($row['ack_no']) == "") {
                                    $ack = '<a style="color:red" href="../printManagement/nsdlreceipt.php?order_id=' . $row['order_id'] . '" target="_blank"><i class="bx bx-printer"></i></a>';
                                } else {
                                    $ack = '';
                                }
                            ?>
                            <tr style="font-size:14px;">
                                <td style='display:none'><?= $sl ?></td>
                                <td style='color: black'><b><?= strtoupper($row['order_id']) ?></b><br><?= date("d M Y h:i A", strtotime($row['timestamp'])) ?></td>
                                <td style='color: black'><?= strtoupper($row['name_card']) ?></td>
                                <td style='color: black'><?= strtoupper($row['faf_name']) ?> <?= strtoupper($row['fam_name']) ?> <?= strtoupper($row['fal_name']) ?></td>
                                <td style='color: black'><?= strtoupper($t_type) ?></td>
                                <td style='color: blue;'><a href='../printManagement/<?=$type?>.php?datakey=<?= base64_encode($row['id']) . "&keypass=" . base64_encode($row['order_id']) ?>' target='_blank'><b>Form 49A / CR</b></a> <br><span style='color: green;'><?= $ack ?></span><br><span style='color: red;'><?= $row['remark'] ?></span></td>
                                <td class="text-end"><button
                                    data-id="<?= $row['id'] ?>"
                                    data-name="<?= $row['name_card'] ?>"
                                    data-token="<?= $row['order_id'] ?>"
                                    data-bs-toggle="modal"
                                    data-bs-target="#upload_form"
                                    class="btn btn-sm btn-icon btn-outline-primary">
                                    <i class="bx bx-upload"></i>
                                </button>
                                <a target='_blanck' href='pan-edit.php?datakey=<?= base64_encode($row['id'])?>&keypass=<?=base64_encode($row['order_id'])?>'
                                           class='btn btn-sm btn-icon btn-outline-primary'><i class="bx bx-edit"></i></a>
                                </td>
                            </tr>
                            <?php $sl++;
                            } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Form Upload -->
<div class="modal fade" id="upload_form" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header ps-background-table">
                <h5 class="modal-title">Upload Form (<?=$modle?>) : <span style="color:red" id="modal-name"></span></h5>
            </div>
            <hr class="border-danger mb-0">
            <div class="modal-body">
                <form action="" method="post" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col">
                            <input type="hidden" name="order_id" class="form-control" id="modal-token">
                            <input type="hidden" name="id" class="form-control" id="modal-id">
                            <input type="hidden" name="upload" class="form-control">
                            <input type="file" name="form_pdf" class="form-control mb-2" id="form_pdf_input" required>
                            <!-- Add this to your HTML -->
                            <div id="progress" style="display: none;">
                                <div class="progress">
                                    <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%"></div>
                                </div>
                            </div>
                            <div id="uploadError" class="alert d-flex align-items-center bg-label-danger mb-0 d-none mt-2" role="alert">
                                <span  style="color: black;" id="uploadStatus">
                                    
                                </span>
                            </div>
                            <div id="uploadSuccess" class="alert d-flex align-items-center bg-label-danger mb-0 mt-2" role="alert">
                                <span  style="color: black;">
                                    The form PDF should be clean or last 2MB and the photo and signature should be correct, otherwise the form will be stopped.
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        Close
                    </button>
                    <button type="submit" name="" class="btn btn-primary">
                        <span class="uploading">Submit</span>&nbsp;&nbsp;&nbsp;
                        <span class="spinner-border spinner-border-sm d-none uploading-spinner" role="status" aria-hidden="true"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- /.container-fluid -->
<?php
require_once('../layouts/mainFooter.php');
?>
<script>
$(document).ready(function() {
    $('button[data-bs-target="#upload_form"]').on('click', function() {
        var id = $(this).data('id');
        var name = $(this).data('name');
        var token = $(this).data('token');
        // Now you can use these variables to populate the modal's content
        $('#modal-id').val(id);
        $('#modal-name').text(name);
        $('#modal-token').val(token);
    });
    // Triggered when the form is submitted
// Triggered when the file input changes
$('#form_pdf_input').on('change', function() {
    var formData = new FormData($('form')[0]);

    // Show progress bar
    $('#progress').show();

    // Initialize progress to 0%
    $('#progress .progress-bar').css({
        width: '0%'
    });
    
    // Change button text to "Uploading Process"
    $('.uploading').text('Uploading');
    $('.uploading-spinner').removeClass('d-none');

    // Simulate slow progress
    setTimeout(function() {
        $.ajax({
            url: '../system/formUpload.php',
            type: 'POST',
            data: formData,
            xhr: function() {
                
                var xhr = new window.XMLHttpRequest();
                xhr.upload.addEventListener("progress", function(evt) {
                    if (evt.lengthComputable) {
                        var percentComplete = evt.loaded / evt.total;
                        // Update progress bar
                        $('#progress .progress-bar').css({
                            width: percentComplete * 100 + '%'
                        });
                    }
                }, false);
                return xhr;
            },
            success: function(response) {

                // Check if the response contains the expected fields
                if ('status' in response && 'message' in response) {
                    if (response.status === 'success') {
                        $('#uploadError').removeClass('d-none');
                        $('#uploadSuccess').addClass('d-none');
                        $('#uploadStatus').html(response.message);
                        $('.uploading').text('Success');
                        $('.uploading-spinner').addClass('d-none');
                        // Redirect after 3 seconds
                        setTimeout(function() {
                            window.location.href = "PhysicalPanUpload";
                        }, 5000);

                    } else {
                        $('#uploadError').removeClass('d-none');
                        $('#uploadSuccess').addClass('d-none');
                        $('#uploadStatus').html(response.message);
                        $('.uploading').text('Error');
                        $('.uploading-spinner').addClass('d-none');
                    }
                } else {
                    // Handle unexpected response format
                    console.error("Unexpected response format:", response);
                    toastr.error("Unexpected response from server");
                    $('.uploading').text('Error');
                    $('.uploading-spinner').addClass('d-none');
                }
            },
            error: function(xhr, status, error) {
                // Hide progress bar on error
                $('#progress').hide();
                // Reset progress bar to 0%
                $('#progress .progress-bar').css({
                    width: '0%'
                });
                // Handle error
                toastr.error("Error uploading file: " + error);
            },
            cache: false,
            contentType: false,
            processData: false
        });
    }, 5000); // Adjust the delay before AJAX call (in milliseconds) as needed

    event.preventDefault();
});

});
</script>