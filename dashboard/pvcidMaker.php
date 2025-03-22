<?php
$pageName = "PVC ID Maker"; // Replace this with the actual page name
$_SESSION['userAuth'] = "User Authentication";

require_once('../layouts/mainHeader.php');
?>

<div class="container-xxl flex-grow-1 container-p-y">
            
            
<!-- Examples -->
<div class="row mb-5">
  <div class="col-md-6 col-lg-3 mb-3">
    <div class="card h-100">
      <img class="card-img-top" src="../assets/img/backgrounds/voter-banner.jpg" alt="Card image cap">
      <div class="card-body">
        <h5 class="card-title">Voter Card</h5>
        <p class="card-text">
          Some quick example text to build on the card title
        </p>
        <button class="btn btn-danger active" disabled>Comming Soon</button>
      </div>
    </div>
  </div>
  <div class="col-md-6 col-lg-3 mb-3">
    <div class="card h-100">
      <img class="card-img-top" src="../assets/img/backgrounds/aadhaar-banner.jpg" alt="Card image cap">
      <div class="card-body">
        <h5 class="card-title">Aadhaar Card</h5>
        <p class="card-text">
         Some quick example text to build on the card title
        </p>
        <button class="btn btn-danger active" disabled>Comming Soon</button>
      </div>
    </div>
  </div>
  <div class="col-md-6 col-lg-3 mb-3">
    <div class="card h-100">
      <img class="card-img-top" src="../assets/img/backgrounds/pan-banner.jpg" alt="Card image cap">
      <div class="card-body">
        <h5 class="card-title">Pan Crad</h5>
        <p class="card-text">
          Some quick example text to build on the card title
        </p>
        <button class="btn btn-danger active" disabled>Comming Soon</button>
      </div>
    </div>
  </div>
  <div class="col-md-6 col-lg-3 mb-3">
    <div class="card h-100">
      <img class="card-img-top" src="../assets/img/backgrounds/upiqr-banner.jpg" alt="Card image cap">
      <div class="card-body">
        <h5 class="card-title">QR Gernater</h5>
        <p class="card-text">
          Some quick example text to build on the card title
        </p>
        <button class="btn btn-danger active" disabled>Comming Soon</button>
      </div>
    </div>
  </div>
</div>



          </div>
<script>
  function generatePDF() {
    // Create a new jsPDF instance
    const pdf = new jsPDF();

    // Get text content from HTML elements
    const cardTitle = document.getElementById('cardTitle').innerText;
    const cardText = document.getElementById('cardText').innerText;

    // Set text on the PDF
    pdf.text(cardTitle, 20, 30);
    pdf.text(cardText, 20, 40);

    // Save the PDF
    pdf.save('id_card.pdf');
  }
</script>
<?php
require_once('../layouts/mainFooter.php');
?>
