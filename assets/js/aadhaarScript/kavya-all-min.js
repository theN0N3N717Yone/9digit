    var quality = 100;
var timeout = 10;
function GetInfo() {
    document.getElementById("tdSerial").innerHTML = "";
    document.getElementById("tdCertification").innerHTML = "";
    document.getElementById("tdMake").innerHTML = "";
    document.getElementById("tdModel").innerHTML = "";
    document.getElementById("tdWidth").innerHTML = "";
    document.getElementById("tdHeight").innerHTML = "";
    document.getElementById("tdLocalMac").innerHTML = "";
    document.getElementById("tdLocalIP").innerHTML = "";
    document.getElementById("tdSystemID").innerHTML = "";
    document.getElementById("tdPublicIP").innerHTML = "";
    var key = document.getElementById("txtKey").value;

    var res;
    if (key.length == 0) {
        res = GetMFS100Info();
    } else {
        res = GetMFS100KeyInfo(key);
    }

    if (res.httpStaus) {
        document.getElementById("txtStatus").value = "ErrorCode: " + res.data.ErrorCode + " ErrorDescription: " + res.data.ErrorDescription;

        if (res.data.ErrorCode == "0") {
            document.getElementById("tdSerial").innerHTML = res.data.DeviceInfo.SerialNo;
            document.getElementById("tdCertification").innerHTML = res.data.DeviceInfo.Certificate;
            document.getElementById("tdMake").innerHTML = res.data.DeviceInfo.Make;
            document.getElementById("tdModel").innerHTML = res.data.DeviceInfo.Model;
            document.getElementById("tdWidth").innerHTML = res.data.DeviceInfo.Width;
            document.getElementById("tdHeight").innerHTML = res.data.DeviceInfo.Height;
            document.getElementById("tdLocalMac").innerHTML = res.data.DeviceInfo.LocalMac;
            document.getElementById("tdLocalIP").innerHTML = res.data.DeviceInfo.LocalIP;
            document.getElementById("tdSystemID").innerHTML = res.data.DeviceInfo.SystemID;
            document.getElementById("tdPublicIP").innerHTML = res.data.DeviceInfo.PublicIP;
        }
    } else {
        alert(res.err);
    }
    return false;
}
var count = 0;
$(document).ready(function () {
    $(".capture1").click(function () {
        Capture(1);
    });
    $(".capture2").click(function () {
        Capture(2);
    });
    $(".capture3").click(function () {
        Capture(3);
    });
    $(".capture4").click(function () {
        Capture(4);
    });
    $(".capture5").click(function () {
        Capture(5);
    });

    $(".capture").on("click", function () {
        var img_id = $(this).attr("data-id");
        Capture(img_id);
    });
});

function Capture(no) {
    // alert(no)
    count = no;

    if (count > 5) {
        return 0;
        // var id='#pic'+count
        // $(id).val(count*10)
    }

    try {
        //  alert('you are in captue')
        var res = CaptureFinger(quality, timeout);

        if (res.httpStaus) {
            // document.getElementById('txtStatus').value = "ErrorCode: " + res.data.ErrorCode + " ErrorDescription: " + res.data.ErrorDescription;

            if (res.data.ErrorCode == "0") {
                var imagdata = "data:image/bmp;base64," + res.data.BitmapData;
                var checkmark = "data:image/bmp;base64,";
                // console.log(res.data.Quality)

                $("#pic" + count).val(imagdata);
                $("#q" + count).html(res.data.Quality + "%");
                $("#im" + count).hide();
                $("#cim" + count).show();
                $("#cim" + count).attr("src", imagdata);
            }
        } else {
            alert(res.err);
        }
    } catch (e) {
        alert(e);
    }
    return false;
}

