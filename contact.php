<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Include the Composer autoload file
require 'vendor/autoload.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = htmlspecialchars(trim($_POST['name']));
    $email = htmlspecialchars(trim($_POST['email']));
    $message = htmlspecialchars(trim($_POST['message']));

    $mail = new PHPMailer(true);
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; // Gmail SMTP server
        $mail->SMTPAuth = true;
        $mail->Username = 'retawcontacts@gmail.com'; // Gmail address
        $mail->Password = 'jiav ykos lwdw yjoc'; // Gmail password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // SSL encryption
        $mail->Port = 465; // SMTP port for SSL

        // Recipients
        $mail->setFrom('retawcontacts@gmail.com', 'Website Contact Form'); // Gmail address
        $mail->addAddress('retawcontacts@gmail.com'); // Recipient email
        $mail->addReplyTo($email, $name); // User's email for reply

        // Subject
        $mail->Subject = 'New Contact Request from Retaw.mv';

        // Email Body (HTML and Plain Text Versions)
        $htmlTemplate = "
        <html>
        <head>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    line-height: 1.6;
                    background-color: #f4f4f4;
                    margin: 0;
                    padding: 0;
                }
                .container {
                    max-width: 600px;
                    margin: 20px auto;
                    background: #ffffff;
                    border-radius: 8px;
                    overflow: hidden;
                    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
                }
                .header {
                    background-color: #1a237e;
                    color: #ffffff;
                    padding: 20px;
                    text-align: center;
                    font-size: 20px;
                }
                .content {
                    padding: 20px;
                    color: #333333;
                }
                .content h2 {
                    color: #1a237e;
                }
                .footer {
                    background-color: #333333;
                    color: #ffffff;
                    text-align: center;
                    padding: 10px;
                    font-size: 12px;
                }
                .footer a {
                    color: #ffffff;
                    text-decoration: none;
                }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <strong>New Contact Request from Retaw.mv</strong>
                </div>
                <div class='content'>
                    <h2>Contact Form Submission Details</h2>
                    <p><strong>Name:</strong> $name</p>
                    <p><strong>Email:</strong> $email</p>
                    <p><strong>Message:</strong></p>
                    <p>$message</p>
                </div>
                <div class='footer'>
                    &copy; 2025 Idearigs. All rights reserved.
                </div>
            </div>
        </body>
        </html>";

        $plainText = "New Contact Form Submission\n\nName: $name\nEmail: $email\nMessage: $message";

        // Content
        $mail->isHTML(true); // Use HTML for email
        $mail->Body = $htmlTemplate; // HTML email body
        $mail->AltBody = $plainText; // Plain text version

        $mail->send();
        echo json_encode(['status' => 'success', 'message' => 'Message sent successfully.']);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => "Message could not be sent. Mailer Error: {$mail->ErrorInfo}"]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}
?>
