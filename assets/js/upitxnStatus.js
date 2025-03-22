    window.onload = function() {
      var minute = 1;
      var sec = 60;
      setInterval(function() {
        document.getElementById("upitimer").innerHTML = minute + ":" + sec;
        sec--;
        if (sec == 00) {
         $(".timeout-text").css("display", "none");
         $(".show-upitxn").css("display", "flex");
         
        }
      }, 1000);
    }
    
function VerifyUpiTxn(){
  var upi_txn_id= $("#upi_txn_id").val();	
  
  if(upi_txn_id!=''){
  
   $.ajax({ 
            url: "../system/upiTxnStatus",
            data: {'upi_txn_id':upi_txn_id},
			type: "POST", 
            success: function(result) {
			var obj = JSON.parse(result);
			
            if(obj.status=="SUCCESS"){
            $(".show-upitxn").css("display", "none");    
			swal("Payment Verified!", obj.message, "success");		
			document.getElementById("qrcode").innerHTML = "<img src=\'../assets/img/success.gif\' width=\'150\'>";	
            document.getElementById("status").value = obj.status;
            document.getElementById("message").value = obj.message;
            document.getElementById("hash").value = obj.hash;
            document.getElementById("checksum").value = obj.checksum;
			
			setTimeout(function(){ 
			document.getElementById("formSubmit").submit();
            }, 2000);

			}else{
			   swal("Alert!", obj.message, "warning");      
			}  

			  
            }
    });
    
  }else{
  swal("Alert!", "Enter UPI Reference No.!", "warning");   
  }
}    