function city_aoCode() {
document.getElementById('area_code').innerHTML = '<option value="">Select</option>';
document.getElementById('aotype').innerHTML = '<option value="">Select</option>';
document.getElementById('rangecode').innerHTML = '<option value="">Select</option>';
document.getElementById('aocode').innerHTML = '<option value="">Select</option>';

var city = $("#city").val();
if (!city==""){
$('.ajax_loader').show();
  $.ajax({
url: '../aocode.php',
type: 'POST',
data: {city_aoCode: $("#city").val()},
success: function (result) {
	 $('.ajax_loader').hide();
	
var birds = result;

      var ele = document.getElementById('area_code');
        for (var i = 0; i < birds.length; i++) {
            // POPULATE SELECT ELEMENT WITH JSON.
            ele.innerHTML = ele.innerHTML + '<option value="' + birds[i]['area_code'] + '">' + birds[i]['area_code'] + '</option>';
        }
        
        var ele = document.getElementById('aotype');
        for (var i = 0; i < birds.length; i++) {
            // POPULATE SELECT ELEMENT WITH JSON.
            ele.innerHTML = ele.innerHTML + '<option value="' + birds[i]['aotype'] + '">' + birds[i]['aotype'] + '</option>';
        }
        
       var ele = document.getElementById('rangecode');
        for (var i = 0; i < birds.length; i++) {
            // POPULATE SELECT ELEMENT WITH JSON.
            ele.innerHTML = ele.innerHTML + '<option value="' + birds[i]['rangecode'] + '">' + birds[i]['rangecode'] + '</option>';
        }
 
       var ele = document.getElementById('aocode');
        for (var i = 0; i < birds.length; i++) {
            // POPULATE SELECT ELEMENT WITH JSON.
            ele.innerHTML = ele.innerHTML + '<option value="' + birds[i]['aocode'] + '">' + birds[i]['aocode'] + '</option>';
        }


}

});	
  
} else { 
alert("Please Select City!");
}
  
  
}