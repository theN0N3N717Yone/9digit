
$(document).ready(function () {
    $('#generateQR').click(function () {
        // Get the orderId value
        var orderId = $('#orderId').val();

        // Define a function to check the status
        function checkStatus() {
            $.ajax({
                url: 'system/paytm_response.php',
                type: 'GET',
                data: { ORDERID: orderId },
                dataType: 'json',
                success: function (response) {
                    console.log('Response:', response); // Log the entire response

                    // Update the image and close the modal based on the correct property
                    if (response.STATUS === 'TXN_SUCCESS' || response.status === 'TXN_SUCCESS') {
                        $('#qrCodeImage')
                            .attr('src', '../assets/img/success_done.svg')
                            .attr('width', '180')
                            .attr('height', '180')
                            .attr('style', 'margin-top: 20px');

                        // Delay modal hiding by 10 seconds
                        setTimeout(function () {
                            $('#qrModal').modal('hide');

                            // Redirect to success.php with parameters
                            var txnId = response.ORDERID;
                            var amount = response.TXNAMOUNT;
                            var ref = response.BANKTXNID;
                            var dateTime = response.TXNDATE;

                           var newUrl = window.location.href + '?txnId=' + txnId + '&amount=' + amount + '&utr=' + ref + '&dateTime=' + dateTime;

                            // Update the URL
                            window.location.href = newUrl;
                        }, 5000);

                        // Stop the interval once the success condition is met
                        clearInterval(intervalId);
                    } else {
                        console.log('Transaction not successful. Status: ' + response.STATUS);
                    }
                },
                error: function (error) {
                    console.log('AJAX Error: ' + error);
                }
            });
        }

        // Call the checkStatus function every 3 seconds
        var intervalId = setInterval(checkStatus, 3000);
    });
    
});
$(document).ready(function () {
    $('#generateQR').click(function () {
        // Validate the input to allow only digits
        var amountInput = $('#amount');
        var amount = amountInput.val();

        if (!/^\d+$/.test(amount)) {
            Ps_alert('Error: Please enter a valid numeric amount.', 'error');
            return; // Stop further execution
        }

        // Check if the amount is less than 500
        if (parseInt(amount) < 500) {
            Ps_alert('Error: Amount should be at least Rs 500.', 'error');
            return; // Stop further execution
        }

        // Rest of your code for form submission using AJAX
        var orderId = $('#orderId').val();
        var Id = $('#Id').val();

        sessionStorage.setItem('orderId', orderId);
        sessionStorage.setItem('Id', Id);

        // Serialize the form data
        var formData = $('#addWallet').serialize();

        $.ajax({
            url: 'generate_qr.php',
            type: 'post',
            data: formData,
            dataType: 'json', // Expect JSON response
            success: function (response) {
                if (response.status === 'success') {
                    $('#qrCodeImage').attr('src', '../assets/img/loading.svg');
                    $('#qrModal').modal('show');
                    setTimeout(function () {
                        $('#qrCodeImage').attr('src', response.qrImage);
                        
                    }, 3000);
                } else {
                    Ps_alert(response.message, 'error');
                    //console.log('Error:', response.message);
                    sessionStorage.removeItem('orderId');
                }
            },
            error: function (error) {
                //console.log('AJAX Error:', error);
                Ps_alert('Error: Unable to generate QR code.', 'error');
                sessionStorage.removeItem('orderId');
            }
        });
    });
});

document.addEventListener('DOMContentLoaded', function () {
    var qrModal = new bootstrap.Modal(document.getElementById('qrModal'));
    var upiTimerElement = document.getElementById('upitimer');
    var expirationTime = 5 * 60; // 5 minutes in seconds

    // Function to update the timer
    function updateTimerDisplay() {
        var minutes = Math.floor(expirationTime / 60);
        var seconds = expirationTime % 60;

        // Format the time as MM:SS
        var formattedTime = minutes + ':' + (seconds < 10 ? '0' : '') + seconds;
        
        // Update the timer display
        upiTimerElement.textContent = formattedTime;
    }

    // Function to start the countdown
    function startCountdown() {
        updateTimerDisplay();

        // Update the timer every second
        var countdownInterval = setInterval(function () {
            expirationTime--;

            // Check if the time has expired
            if (expirationTime <= 0) {
                clearInterval(countdownInterval);
                qrModal.hide(); // Close the modal when the timer expires
            } else {
                updateTimerDisplay();
            }
        }, 1000);
    }

    // Event listener for modal opening
    qrModal._element.addEventListener('shown.bs.modal', function () {
        expirationTime = 5 * 60; // Reset the expiration time when the modal is opened
        startCountdown();
    });

    // Event listener for modal closing
    qrModal._element.addEventListener('hidden.bs.modal', function () {
        clearInterval(countdownInterval); // Clear the interval when the modal is closed
    });
});
