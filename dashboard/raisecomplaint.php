<?php
$pageName = "Raise Complaint"; // Replace this with the actual page name
$_SESSION['userAuth'] = "User Authentication";
require_once('../layouts/mainHeader.php');

// Process complaint submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_ticket'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $subject = $_POST['subject'];
    $description = $_POST['description'];
    $userid = getUsersInfo('id');

    try {
    // Prepare SQL statement
    $stmt = $conn->prepare("INSERT INTO complaints (userid, name, email, subject, description) VALUES (:userid, :name, :email, :subject, :description)");

    // Bind parameters
    $stmt->bindParam(':userid', $userid); // Assuming $userid contains the ID of the current user
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':subject', $subject);
    $stmt->bindParam(':description', $description);

    // Execute the statement
    $stmt->execute();

    echo "Complaint submitted successfully";
    } catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

// Close connection (optional, PDO closes the connection automatically when the script ends)
//$conn = null;
?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<style>
body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
    background-color: #f2f2f2;
}

.chat-container {
    max-width: 400px;
    margin: 50px auto;
    background-color: #fff;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    padding: 20px;
    box-sizing: border-box;
}

.chat-box {
    max-height: 300px;
    overflow-y: auto;
    padding: 10px;
}

.user-input-container {
    display: flex;
}

.user-input {
    width: calc(100% - 80px);
    padding: 8px;
    border: 1px solid #ccc;
    border-radius: 4px 0 0 4px;
    margin-bottom: 10px;
    box-sizing: border-box;
}

.send-btn {
    width: 70px;
    padding: 8px;
    border: none;
    background-color: #4CAF50;
    color: white;
    border-radius: 0 4px 4px 0;
    cursor: pointer;
}

.user-msg-container {
    display: flex;
    justify-content: flex-end;
    margin-bottom: 10px;
}

.user-msg {
    background-color: #4CAF50;
    color: white;
    padding: 10px;
    border-radius: 8px 8px 0 8px;
    margin-left: 10px;
}

.user-icon {
    width: 30px;
    height: 30px;
    margin-left: 5px;
    align-self: flex-end;
}

.bot-msg-container {
    display: flex;
    margin-bottom: 10px;
}

.bot-msg {
    background-color: #f2f2f2;
    color: #333;
    padding: 10px;
    border-radius: 8px 8px 8px 0;
    margin-right: 10px;
}

.bot-icon {
    width: 30px;
    height: 30px;
    margin-right: 5px;
    align-self: flex-end;
}

</style>
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
      <!-- ---------------------  Voter Print Start  ---------------- -->
      <div class="col-lg-10 d-flex align-items-strech m-auto <?php echo $getResult ? 'd-none' : ''; ?> ">
         <div class="chat-container">
            <div class="chat-box" id="chat-box"></div>
            <div class="user-input-container">
                <input type="text" id="user-input" class="user-input" placeholder="Type your message...">
                <button onclick="sendMessage()" class="send-btn">Send</button>
            </div>
        </div>
      </div>
<!-- ------------------------- Aadhaar Details Fetch Success ---------------------------- -->
</div>
      </div>
<script>
function sendMessage() {
    var userInput = $('#user-input').val();
    $('#chat-box').append('<div class="user-msg-container"><div class="user-msg">' + userInput + '</div><img src="../assets/img/icons/unicons/bot-user.svg" class="user-icon"></div>');
    $('#user-input').val('');

    $.ajax({
        type: 'POST',
        url: 'process_message.php',
        data: { message: userInput },
        success: function(response) {
            $('#chat-box').append('<div class="bot-msg-container"><img src="../assets/img/icons/unicons/chatbot-icon.webp" class="bot-icon"><div class="bot-msg">' + response + '</div></div>');
            $(".chat-box").animate({ scrollTop: $(".chat-box")[0].scrollHeight }, "fast");
        }
    });
}
</script>
<?php
require_once('../layouts/mainFooter.php');
?>