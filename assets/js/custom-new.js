function panValidation(txt)
{
	txt = txt.toUpperCase();
	var regex = /[a-zA-Z]{3}[PCHFATBLJG]{1}[a-zA-Z]{1}[0-9]{4}[a-zA-Z]{1}$/;
    var pan = {C:"Company", P:"Personal", H:"Hindu Undivided Family (HUF)", F:"Firm", A:"Association of Persons (AOP)", T:"AOP (Trust)", B:"Body of Individuals (BOI)", L:"Local Authority", J:"Artificial Juridical Person", G:"Govt"};
    pan=pan[txt[3]];
	if(regex.test(txt)){
    if(pan!="undefined"){
    document.getElementById("pan_no").value = txt;
    }else{
	  document.getElementById("pan_no").value = '';
	  alert("PAN Number is not Valid");
	}
    }else{
	  document.getElementById("pan_no").value = '';
	  alert("PAN Number is not Valid");
	}
}

function isNumber(evt) {
    evt = (evt) ? evt : window.event;
    var charCode = (evt.which) ? evt.which : evt.keyCode;
    if (charCode > 31 && (charCode < 48 || charCode > 57)) {
        return false;
    }
    return true;
}

function funLocation(elm) {
var x = document.getElementById(elm);
getLocation();
function getLocation() {
  if (navigator.geolocation) { 
    navigator.geolocation.getCurrentPosition(showPosition);
  } else { 
    alert("Geolocation is not supported by this browser.");
  }
}

function showPosition(position) {
var obj = {latitude: position.coords.latitude, longitude:position.coords.longitude};
var myJSON = JSON.stringify(obj);
x.value = myJSON;  
}
}

function upiCountdown(elm,minute,second,url) {
document.getElementById(elm).innerHTML =minute + ":" + second; startTimer();

function startTimer() {
  var presentTime = document.getElementById(elm).innerHTML;
  var timeArray = presentTime.split(/[:]+/);
  var m = timeArray[0];
  var s = checkSecond((timeArray[1] - 1));
  if(s==59){m=m-1}
  if(m<0){
      
      swal ( "Oops" ,  "Transaction Timeout!" ,  "error" );
      window.location.href = url;
      
  }
  document.getElementById(elm).innerHTML =
    m + ":" + s;
  //console.log(m)
  setTimeout(startTimer, 1000);
}

function checkSecond(sec) {
  if (sec < 10 && sec >= 0) {sec = "0" + sec}; // add zero in front of numbers < 10
  if (sec < 0) {sec = "59"};
  return sec;
}
}

function GenerateQR(upi_id, payer_name, amount, txnid, note) {
  
     var sampleQR = new QRCode('qrcode', { 
		text: 'upi://pay?cu=INR&pa='+upi_id+'&pn='+payer_name+'&am='+amount+'&mam='+amount+'&tr='+txnid+'&tn='+note+'',
		width: 228,
		height: 228,
		colorDark : '#000000',
		colorLight : '#ffffff',
		correctLevel : QRCode.CorrectLevel.H
	})
	
	 updateQR = (upi_id, payer_name, amount, note) => { 
		 sampleQR.makeCode('upi://pay?cu=INR&pa='+upi_id+'&pn='+payer_name+'&am='+amount+'&mam='+amount+'&tr='+txnid+'&tn='+note+'') 
	} 
}


function SendOtpCode(){
    
   var lid = $(".lid").val();
   var lpwd = $(".lpwd").val();
   if(lid===""){
   swal("Alert!", "Enter Your Login Username!", "error");       
   }else{
   if(lpwd===""){
   swal("Alert!", "Enter Your Login Password!", "error");       
   }else{
       
   var base_url = window.location.origin;    
   document.getElementById("load").innerHTML = "<button type='button' class='btn btn-primary btn-sm'><img src='"+base_url+"/assets/img/animated_spinner.webp' width='20' width=''20></button>";        
    
   $.ajax({ 
            url: "../system/sendotpcode",
			type: "POST", 
			data:{lid:lid,lpwd:lpwd},
            success: function(result) {
			var obj = JSON.parse(result);
			if(obj.status===true){
			swal("Registered Email & Mobile Number!", obj.msg, "success"); 
			document.getElementById("load").innerHTML = "<button type='button' class='btn btn-primary btn-sm'>Wait 10 Second</button>";
			setTimeout(function(){ 
			document.getElementById("load").innerHTML = "<button type='button' class='btn btn-primary btn-sm' onclick='SendOtpCode();'>Resend OTP Code</button>";     
			}, 10000);
			}else{
			swal("Alert!", obj.msg, "error");  
			document.getElementById("load").innerHTML = "<button type='button' class='btn btn-primary btn-sm' onclick='SendOtpCode();'>Send OTP Code</button>";       
			}
         
			  
            }
    });
   }
}
}