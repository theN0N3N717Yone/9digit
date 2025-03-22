<?php
require_once('../layouts/mainHeader.php');
?>
<div class="content-wrapper">
    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- DataTales Example -->
        <div class="card shadow-lg ps-background-table">
            <div class="card-header flex-column flex-md-row">
                <h4 style="color:black;"><b>PAN <?php if($userdata['usertype'] === 'retailer') { ?>Under<?php } else { ?>Clear<?php } ?> Observation Record</b></h4>
            </div>
            <?php if($userdata['usertype'] === 'retailer') { ?>
            <div class="card-body">
                <div id="demo_info" class="box table-responsive text-nowrap">
                    <table id="example" class="table table-striped">
                        <thead style="background: #000cad;">
                            <tr>
                                <th style="color: #fff">Date</th>
                                <th style="color: #fff">NAME</th>
                                <th style="color: #fff">FATHER_NAME</th>
                                <th style="color: #fff">TXN_ID</th>
                                <th style="color: #fff">PAN_TYPE</th>
                                <th style="color: #fff">ACTION</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            
                            
                            $stmt = $conn->prepare("select * from nsdlpancard WHERE user_id=? AND status='hold' ORDER BY `id` DESC");
                            $stmt->execute([$userdata['id']]);
                            $sl=1;
                            while($row=$stmt->fetch()) {
                                
                            
                            
                            $usql = $conn->prepare("select * from users WHERE id = ?");
                            $usql->execute([$row['user_id']]);
                            $usr_d=$usql->fetch();
                            
        		            // Display the table row
                                echo "<tr>
                                    <td style='color:black;'><b>" . date("d M h:i A", strtotime($row['timestamp'])) . "</b></td>
                                    <td style='color:black;'><b>" . strtoupper($row['name_card']) . "</b></td>
                                    <td style='color:black;'><b>" . strtoupper($row['faf_name']) . " " . strtoupper($row['fam_name']) . " " . strtoupper($row['fal_name']) . "</b></td>
                                    <td style='color:black;'><b>" . strtoupper($row['order_id']) . "</b></td>
                                    <td style='color:black;'><b>" . strtoupper($row['type']) . "</b></td>
                                    <td>
                                        <button 
                                            data-bs-toggle='modal' 
                                            data-bs-target='#view_reasion' 
                                            data-id=" . base64_encode($row['id']) . "
                                            data-token=" . base64_encode($row['order_id']) . "
                                            data-type='panreason'
                                            class='btn btn-danger active btn-sm view-reasion'>View Reason</button>
                                            
                                        <a href='PhysicalPan-ObservationEdit.php?datakey=".base64_encode($row['id'])."&keypass=".base64_encode($row['order_id'])."'
                                           class='btn btn-sm btn-icon btn-outline-danger active'><i class='bx bx-edit'></i></a>
                                    </td>
                                </tr>";
                                $sl++;
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php } ?>
            <?php if($userdata['usertype'] === 'mainadmin') { ?>
            <div class="card-body">
                <div id="demo_info" class="box table-responsive text-nowrap">
                    <table id="example" class="table table-striped">
                        <thead style="background: #000cad;">
                            <tr>
                                <th style="color: #fff">Date</th>
                                <th style="color: #fff">USER</th>
                                <th style="color: #fff">NAME</th>
                                <th style="color: #fff">ACK</th>
                                <th style="color: #fff">Pan_Type</th>
                                <th style="color: #fff">Status</th>
                                <th style="color: #fff">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            
                            
                            $stmt = $conn->prepare("select * from nsdlpancard WHERE status = ? ORDER BY `id` DESC");
                            $stmt->execute(['holdprocess']);
                            $sl=1;
                            while($row=$stmt->fetch()) {
                                
                            
                            
                            $usql = $conn->prepare("select * from users WHERE id = ?");
                            $usql->execute([$row['user_id']]);
                            $usr_d=$usql->fetch();
                            
                            if ($row['type'] == 'Correction pan') {
                                $pan_type = 'CSF';
                            } else {
                                $pan_type = "NEW";
                            }
            
            
                            if(strtoupper($row['ack_no'])==""){
                            $ack = "<span><b>".$row['order_id']."</b></span></a>";
            
                            }else{
                            $ack = "<span><b>".$row['ack_no']."</b></span><br><a href=".$row['ack_pdf']."  target='_blank'><b>Receipt</a>";		
                            }
                            
                            if ($row['status'] && preg_match('/holdprocess/i', $row['status'])) {
                                $status = '<span class="badge bg-primary">OBJ Clear</span>';
                            }
                            if(strtoupper($row['ack_no'])==""){
                            $orderid = '<b>'.$row['order_id'].'</b>
                            <br><a href="nsdlreceipt.php?order_id='.$row['order_id'].'"  target="_blank"><b>Receipt</a>';	
                            }else{
                            $orderid = '<b>'.$row['order_id'].'</b>';		
                            }
        		            // Display the table row
                                echo "<tr>
                                    <td style='color:black;'>" . date("d M h:i A", strtotime($row['timestamp'])) . "</td>
                                    <td><button 
                                            data-bs-toggle='modal' 
                                            data-bs-target='#user_info' 
                                            data-id=" . base64_encode($usr_d['id']) . "
                                            data-token=" . base64_encode($usr_d['username']) . "
                                            data-type='get_user'
                                            class='btn btn-link get-user-data me-1' style='color:black;'>" . strtoupper($usr_d['username']) . " <i class='bx bx-street-view' style='font-size: 22px;'></i></button></td>
                                            
                                    <td style='color:black;'>" . strtoupper($row['name_card']) . "</td>
                                    <td style='color:black;'>" . $ack . "</td>
                                    <td style='color:black;'>" . strtoupper($pan_type) . "</td>
                                    <td style='color:black;'>" . $status . "</td>
                                    <td>
                                    <a href='PhysicalPan-Update-Observation.php?datakey=".base64_encode($row['id'])."&keypass=".base64_encode($row['order_id'])."' class='btn btn-icon btn-outline-primary btn-sm'><i class='bx bx-edit'></i></a>
                                    </td>
                                </tr>";
                                $sl++;
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php } ?>
        </div>
    </div>
</div>
<?php
require_once('../layouts/mainFooter.php');
?>
<script>
    $(document).ready(function() {
        $('.view-reasion').on('click', function() {
            var txnId = $(this).data('id');
            var token = $(this).data('token');
            var dataType = $(this).data('type');

            $.ajax({
                url: '../system/getData.php',
                type: 'GET',
                data: { txnId: txnId, token: token, dataType: dataType },
                success: function(response) {
                    $('.modal-content').html(response);
                    $('#myModal').modal('show');
                },
                error: function() {
                    alert('Error fetching data.');
                }
            });
        });
    });
</script>

