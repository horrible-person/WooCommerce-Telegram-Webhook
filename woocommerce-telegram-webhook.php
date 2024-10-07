<?php
/**
 * Plugin Name: WooCommerce Telegram Webhook
 * Description: Sends a Telegram message when a new order is created.
 * Version: 1.0
 * Author: horrible_person
 */

require_once 'config.php';

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

// Add custom action to send Telegram message when a new order is created
add_action('woocommerce_new_order', 'send_telegram_message_on_new_order', 10, 1);

function send_telegram_message_on_new_order($order_id) {
    $bot_token = 'TELEGRAM_BOT_TOKEN; // Replace with your bot token
    $chat_id = TELEGRAM_CHAT_ID; // Replace with your chat ID
    $telegram_api_url = "https://api.telegram.org/bot{$bot_token}/sendMessage";

    $order = wc_get_order($order_id);
    $message = "Новый заказ #{$order_id} был создан!\n\n";
    $message .= "Имя клиента: {$order->get_billing_first_name()} {$order->get_billing_last_name()}\n";
    $message .= "Email: {$order->get_billing_email()}\n";
    $message .= "Телефон: {$order->get_billing_phone()}\n";
    $message .= "Адрес доставки: {$order->get_shipping_address_1()}, {$order->get_shipping_city()}\n";
    $message .= "Общая сумма: {$order->get_total()}\n";

    $data = [
        'chat_id' => $chat_id,
        'text' => $message
    ];

    $options = array(
        'http' => array(
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
            'method'  => 'POST',
            'content' => http_build_query($data),
        ),
    );
    $context  = stream_context_create($options);
    $result = file_get_contents($telegram_api_url, false, $context);

    if ($result === FALSE) {
        // Handle error
    }
}