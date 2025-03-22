// Development Company: Pansprint Infotech
// Author: Tinku Kumawat
// Version: 1.0
// Date: February 23, 2024
// Description: JavaScript functions for handling OTP verification and countdown timer

function SendOtpCode() {
    var id = $(".u-id").val();
    var pass = $(".u-pass").val();
    var csrf = $(".csrf_token").val();

    if (id === "") {
        toastr.error('Username cannot be empty.', { "class": "my-toast-error" });
    } else if (pass === "") {
        toastr.error('Password cannot be empty.', { "class": "my-toast-error" });
    } else if (csrf.length !== 64) {
        toastr.error('Invalid CSRF Token. Please refresh the page and try again.', { "class": "my-toast-error" });
    } else {
        var base_url = window.location.origin;
        document.getElementById("load").innerHTML = "<button type='button' class='btn btn-danger d-grid w-100'>Executing...</button>";

        $.ajax({
            url: "system/loginOTP.php",
            type: "POST",
            data: { id: id, pass: pass, csrf: csrf },
            success: function (result) {
                var obj = JSON.parse(result);
                if (obj.msg === 'Login successful') {
                    toastr.success("Login successful. Please wait a moment.");
                    setTimeout(function () {
                        window.location.href = "dashboard/index.php";
                    }, 2000);
                }else{  
                if (obj.msg === 'OTP send successful') {
                    startCountdown();
                    $('#login-div').addClass('d-none');
                    $('#otp-div').removeClass('d-none');
                    $('#mobile').html(obj.mobile);
                    toastr.info("Two Step Verification OTP sent successfully to your registered mobile or email. " + obj.mobile);

                    document.getElementById("load").innerHTML = "<button type='button' class='btn btn-primary d-grid w-100'>Wait 10 Seconds</button>";
                    setTimeout(function () {
                        document.getElementById("load").innerHTML = "<button type='button' class='btn btn-primary d-grid w-100' onclick='SendOtpCode();'>Sign in</button>";
                    }, 10000);
                } else {
                    toastr.error(`${obj.msg}`);
                    document.getElementById("load").innerHTML = "<button type='button' class='btn btn-primary d-grid w-100' onclick='SendOtpCode();'>Sign in</button>";
                }
                }
            }
        });
    }
}

var countdownInterval;
var countdownSeconds = 60;

function startCountdown() {
    $("#myDiv").hide();
    $("#countdown").text("Didn't get the code? Wait " + countdownSeconds + " Sec");
    $("#countdown").show();

    countdownInterval = setInterval(function() {
        countdownSeconds--;
        if (countdownSeconds <= 0) {
            clearInterval(countdownInterval);
            $("#countdown").hide();
            $("#myDiv").show();
        } else {
            $("#countdown").text("Didn't get the code? Wait " + countdownSeconds + " Sec");
        }
    }, 1000); // Update countdown every second (1000 milliseconds)
}