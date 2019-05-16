<?php 

$transport = new Swift_SmtpTransport("smtp.mailtrap.io", 2525);
$transport->setUsername("d6606dc19c5e58");
$transport->setPassword("a16d58e8f836a8");

$mailer = new Swift_Mailer($transport);

$logger = new Swift_Plugins_Loggers_ArrayLogger();
$mailer->registerPlugin(new Swift_Plugins_LoggerPlugin($logger));

$sql1 = "SELECT  bets.id, item_id, items.name AS item_name, bets.user_id, users.name AS user_name, email, winner_id FROM bets 
    JOIN items ON bets.item_id = items.id
    JOIN users ON bets.user_id = users.id
    WHERE bets.id IN 
        (
        SELECT MAX(id) FROM bets WHERE ts_end <= NOW() GROUP BY item_id
        ) 
    AND winner_id IS NULL
";

$result1 = mysqli_query($conn, $sql1);
if (!$result1) {
    print('Ошибка MYSQL: ' . mysqli_error($conn));
}

if (mysqli_num_rows($result1)) {
    $winBets = mysqli_fetch_all($result1, MYSQLI_ASSOC);

    foreach ($winBets as $key => $winBet) {

       $userID = $winBet['user_id'];
        $ID = $winBet['id'];
/*
        $sql2 = "UPDATE bets 
            SET winner_id = '$userID'
            WHERE id = '$ID'
        ";

        $result2 = mysqli_query($conn, $sql2); */
        if (false) {
            print('Ошибка MYSQL: ' . mysqli_error($conn));
        }

        else { 
            $winBet['winner_id'] = $userID;

            $recipients[$winBet['email']] = $winBet['user_name'];

print_r($recipients);

            $email_content = 'Тест - тело сообщения'; /*include_template('email.php', [
                'winBet' => $winBet
            ]);*/

            $message = new Swift_Message();
            $message->setSubject("Ваша ставка победила!");
            $message->setFrom(['keks@phpdemo.ru' => 'yeticave']);
            $message->setBcc($recipients);
    
            $msg_content = $email_content;
            $message->setBody($msg_content, 'text/html');

            $result = $mailer->send($message);
print_r('999999999');
            /*if ($result) {
                print("Рассылка успешно отправлена");
            }
            else {
                print("Не удалось отправить рассылку: " . $logger->dump());
            }*/
        }
    }
}

