                 <?php
                 require_once('../system/connectivity_functions.php');
                            $conn = connectDB();
                            
                            $stmt = $conn->prepare("select * from recharges WHERE user_id = ? ORDER BY `id` DESC");
                            $stmt->execute([getUsersInfo('id')]);
                            
                            
                            if (isset($_POST['form_date'])) {
                                $form_date = $_POST['form_date'];
                                $to_date = $_POST['to_date'];
                                
                                //echo $form_date . $to_date;

                                $stmt = $conn->prepare("SELECT * FROM recharges WHERE user_id = :user_id AND date_time BETWEEN :fromdate AND :todate ORDER BY `id` DESC");
                                $stmt->execute([
                                    'user_id' => getUsersInfo('id'),
                                    'fromdate' => $form_date,
                                    'todate' => $to_date
                                ]);
                            } else {
                                // Default values if the form is not submitted
                                $todate = date('Y-m-d'); // Current date
                                $fromdate = date('Y-m-d', strtotime('-3 days', strtotime($todate))); // 3 days ago
                            
                                // Fetch data without filtering
                                $stmt = $conn->prepare("SELECT * FROM recharges WHERE user_id = :user_id AND date_time BETWEEN :fromdate AND :todate ORDER BY `id` DESC");
                                $stmt->execute([
                                    'user_id' => getUsersInfo('id'),
                                    'fromdate' => $fromdate,
                                    'todate' => $todate
                                ]);
                            }
                            
                            if(!empty($_POST['search'])){
            
                            $search = $_POST['search']; 
                            $stmt = $conn->prepare("select * from recharges WHERE number LIKE '{$search}%' OR order_id LIKE '{$search}%'
                             AND user_id=? ORDER BY `id` DESC");
                            $stmt->execute([getUsersInfo('id')]);
                            }
                            $sl=1;
                            while($row=$stmt->fetch()) {
                                
                            $usql = $conn->prepare("select * from users WHERE id = ?");
                            $usql->execute([$row['user_id']]);
                            $usr_d=$usql->fetch();
                            $timestamp = DateTime::createFromFormat('m/d/Y h:i:s a', $row['timestamp']);
                            $formatted_timestamp = $timestamp->format('d-M-Y | h:i A');
                            if($row['operator'] === "airtel"){
                                $opImg = "../../assets/img/rechage-page/airtel.png";
                            } else if($row['operator'] === "jio"){
                                $opImg = "../../assets/img/rechage-page/jio.png";
                            } else if($row['operator'] === "idea"){
                                $opImg = "../../assets/img/rechage-page/idea.png";
                            } else if($row['operator'] === "bsnl"){
                               $opImg = "../../assets/img/rechage-page/idea.png"; 
                            }
                            
        		            // Display the table row
                                echo '<div class="d-flex align-items-center justify-content-between p-3 text-dark">
                        <div class="text-start">
                           <div>
                              <span style="font-size: 14px; letter-spacing: 1px; font-weight: 500;">TRANS ID: '.strtoupper($row['ref_id']).'</span>
                           </div>
                        </div>
                        <div class="text-end">
                           <div>
                              <span style="font-size: 14px; font-weight: 500;">Rs '.number_format($row['amount'],2).'</span>
                           </div>
                        </div>
                     </div>
                     <div class="d-flex align-items-center justify-content-between p-3 text-muted" style="margin-top: -35px">
                        <div class="text-start">
                           <div>
                              <span style="font-size: 12px">'.$formatted_timestamp.'</span>
                           </div>
                        </div>
                     </div>
                  <div class="bg-white d-flex align-items-center gap-3 p-3 pb-0 mb-0 shadow-sm" style="margin-top: -15px;">
                     <img src="' . $opImg . '" alt class="border border-light" width="60" style="margin-top: -15px">
                     <div>
                        <h6 class="mb-1">'.ucfirst($row['operator']).'</h6>
                        <p class="text-muted mb-0" style="letter-spacing: 2px;">M-'.$row['number'].'</p>
                        <p class="text-muted">'.ucfirst($row['status']).'
                        </p>
                     </div>
                     
                     <a class="ms-auto" 
                     data-bs-id="'.$row['id'].'"
                     data-bs-order_id="'.$row['order_id'].'"
                     data-bs-number="'.$row['number'].'"
                     data-bs-operator="'.$row['operator'].'"
                     data-bs-type="'.$row['type'].'"
                     data-bs-debit_amt="'.$row['debit_amt'].'"
                     data-bs-balance="'.$row['balance'].'"
                     data-bs-date_time="'.$row['date_time'].'"
                     data-bs-timestamp="'.$row['timestamp'].'"
                     data-bs-ref_id="'.$row['ref_id'].'"
                     data-bs-remark="'.$row['remark'].'"
                     data-bs-status="'.$row['status'].'"
                     data-bs-toggle="modal" data-bs-target="#rcDetails">
                        <span class="mdi mdi-chevron-right mdi-24px"></span>
                     </a>
                  </div>';
                  
                  

                                $sl++;
                            }?>   
<script>
$(document).ready(function() {
    // Handle click event on the anchor
    $('a[data-bs-toggle="modal"]').on('click', function() {
        // Get the data attributes
        console.log('Clicked');
        var id = $(this).data('bs-id');
        var orderId = $(this).data('bs-order_id');
        var number = $(this).data('bs-number');
        var operator = $(this).data('bs-operator');
        var type = $(this).data('bs-type');
        var debitAmt = $(this).data('bs-debit_amt');
        var balance = $(this).data('bs-balance');
        var dateTime = $(this).data('bs-date_time');
        var timestamp = $(this).data('bs-timestamp');
        var refId = $(this).data('bs-ref_id');
        var remark = $(this).data('bs-remark');
        var status = $(this).data('bs-status');
        status = status.charAt(0).toUpperCase() + status.slice(1);
        txoperator = operator.charAt(0).toUpperCase() + operator.slice(1);
        var formattedAmt = debitAmt.toFixed(2);
        
        // Parse the database time into a JavaScript Date object
        var date = new Date(timestamp);
        
        // Define months array for converting month number to month name abbreviation
        var months = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
        
        // Format the date and time
        var formattedDate = date.getDate() + "-" + months[date.getMonth()] + "-" + date.getFullYear();
        var hours = date.getHours() > 12 ? date.getHours() - 12 : date.getHours();
        var ampm = date.getHours() >= 12 ? "PM" : "AM";
        var formattedTime = hours + ":" + (date.getMinutes() < 10 ? "0" : "") + date.getMinutes() + " " + ampm;
        
        // Populate the modal with the data
        $('#txid').html(refId);
        $('#txstatus').html(status);
        $('#amt1').html(formattedAmt);
        $('#amt2').html(formattedAmt);
        $('#txtype').html(type);
        $('#time1').html(formattedDate + " | " + formattedTime);
        $('#time2').html(formattedDate + " | " + formattedTime);
        $('#txmobile').html(number);
        $('#txremark').html(remark);
        
        
        
            // Fetching user information for Airtel, Idea, and Jio
    var commission = calculateCommission(operator, debitAmt);

        
    // Function to calculate commission based on debit amount and operator
    function calculateCommission(operator, debitAmt) {
        // Your implementation to calculate commission based on operator and debit amount
        // This is a placeholder, replace it with your actual calculation logic
        var commissionPercentage;
        switch(operator) {
            case 'airtel':
                operatorImg = "../../assets/img/rechage-page/airtel.png";
                commissionPercentage = <?php echo getUsersInfo('airtel') ?>; // 10% commission for Airtel
                break;
            case 'idea':
                operatorImg = "../../assets/img/rechage-page/idea.png";
                commissionPercentage =<?php echo getUsersInfo('idea') ?>; // 15% commission for Idea
                break;
            case 'jio':
                operatorImg = "../../assets/img/rechage-page/jio.png";
                commissionPercentage = <?php echo getUsersInfo('jio') ?>; // 12% commission for Jio
                break;
            default:
                commissionPercentage = 0; // No commission for unknown operators
        }
        var commission = debitAmt * (commissionPercentage / 100);
        return commission;
        return operatorImg;
    }
    
    
    // Calculating total amount (debit amount + commission)
    var totalAmount = parseFloat(commission);
    
    // Displaying operator, debit amount, and total amount
    $('#operator').html(txoperator);
    $('#operatorImg').attr('src', operatorImg).on('error', function() {
        console.error('Error loading image:', operatorImg);
    });
    $('#txcommission').html(totalAmount);
    
    });
});
</script>