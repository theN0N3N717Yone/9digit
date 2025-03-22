<?php
$userMessage = $_POST['message'];

// Simple logic for generating bot responses
$botResponse = generateBotResponse($userMessage);

echo $botResponse;

function generateBotResponse($message) {
    // Implement your bot's logic here
    switch ($message) {
        case "hi":
        case "hello":
            return "Hello there!";
        case "how are you":
            return "I'm just a bot, but thanks for asking!";
        case "1":
            return "Please enter transaction ID";
        case "2":
            return "Please enter PAN card number";
        case "3":
            return "Please enter your mobile number";
        case "4":
            return "Please enter your email address";
        case "5":
            return "Please enter your recharge ID";
        case "6":
            return "Please enter your query";
        case "7":
            return "Please enter another option";
        case "back":
            return "Returning to previous menu"; // You can customize this message as needed
        default:
            // Check if the input starts with a number followed by a dot, indicating an option selection
            if (preg_match("/^\d+\./", $message)) {
                $option = substr($message, 0, 1); // Extract the option number
                switch ($option) {
                    case "1":
                        return "You selected Transaction Status. Please enter transaction ID.";
                    case "2":
                        return "You selected PAN Card Status. Please enter PAN card number.";
                    case "3":
                        return "You selected Mobile Update. Please enter your mobile number.";
                    case "4":
                        return "You selected Email Update. Please enter your email address.";
                    case "5":
                        return "You selected Recharge Status. Please enter your recharge ID.";
                    case "6":
                        return "You selected Query. Please enter your query.";
                    default:
                        return "Invalid option selected. Type 'back' to return to the previous menu.";
                }
            } elseif (isValidInput($message)) {
                return "Your input: " . $message;
            } else {
                return "Invalid input. Please try again or type 'back' to return to the previous menu.";
            }
    }
}

function isValidInput($input) {
    // Implement your validation logic here
    switch ($input) {
        case "3":
            // Validate mobile number format
            return preg_match("/^\d{10}$/", $input); // Assuming 10-digit mobile number
        case "4":
            // Validate email address format
            return filter_var($input, FILTER_VALIDATE_EMAIL) !== false;
        default:
            // No specific validation required for other inputs
            return true;
    }
}
?>
