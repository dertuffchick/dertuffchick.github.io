<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $to = "kapefrom@gmail.com"
    $subject = "Новая заявка с сайта Нахлобучка";
    $name = strip_tags(trim($_POST["name"] ?? ""));
    $email = strip_tags(trim($_POST["email"] ?? ""));
    $phone = strip_tags(trim($_POST["phone"] ?? ""));
    $message = strip_tags(trim($_POST["message"] ?? ""));
    $email_content = "Поступила новая заявка с сайта:\n\n";
    $email_content .= "Имя: $name\n";
    $email_content .= "Email: $email\n";
    $email_content .= "Телефон: $phone\n";
    $email_content .= "Сообщение:\n$message\n\n";
    if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] == UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['attachment']['tmp_name'];
        $file_name = $_FILES['attachment']['name'];
        $file_size = $_FILES['attachment']['size'];
        $file_type = $_FILES['attachment']['type'];
        $file_content = chunk_split(base64_encode(file_get_contents($file_tmp)));
        $boundary = md5(time());
        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: multipart/mixed; boundary=\"$boundary\"\r\n";
        $headers .= "From: $email\r\n";
        $headers .= "Reply-To: $email\r\n";
        $body = "--$boundary\r\n";
        $body .= "Content-Type: text/plain; charset=utf-8\r\n";
        $body .= "Content-Transfer-Encoding: 8bit\r\n\r\n";
        $body .= $email_content . "\r\n\r\n";
        $body .= "--$boundary\r\n";
        $body .= "Content-Type: $file_type; name=\"$file_name\"\r\n";
        $body .= "Content-Disposition: attachment; filename=\"$file_name\"\r\n";
        $body .= "Content-Transfer-Encoding: base64\r\n\r\n";
        $body .= $file_content . "\r\n\r\n";
        $body .= "--$boundary--";      
    } else {
        $headers = "From: $email\r\n";
        $headers .= "Reply-To: $email\r\n";
        $headers .= "Content-Type: text/plain; charset=utf-8\r\n";
        $body = $email_content;
    }
    if (mail($to, $subject, $body, $headers)) {
        echo json_encode(["success" => true, "message" => "Заявка успешно отправлена"]);
    } else {
        echo json_encode(["success" => false, "message" => "Ошибка при отправке"]);
    }
    
} else {
    echo json_encode(["success" => false, "message" => "Метод не поддерживается"]);
}
?>
