<?php
// Receiving data from another server here
header('Content-Type: application/json');
$request = file_get_contents('php://input');

// // If you want to know what data you will receive from Paddle // //
// $req_dump = print_r( $request, true );
// $fp = file_put_contents( 'request.log', $req_dump );

// Your Paddle 'Public Key'
$public_key_string = 'Put_Your_Paddle_Public_Key_Here';
$public_key        = openssl_get_publickey($public_key_string);

// Get the p_signature parameter & base64 decode it.
$signature = base64_decode($_POST['p_signature']);

// Get the fields sent in the request, and remove the p_signature parameter
$fields = $_POST;
unset($fields['p_signature']);

// ksort() and serialize the fields
ksort($fields);
foreach ($fields as $k => $v) {
    if (!in_array(gettype($v), array('object', 'array'))) {
        $fields[$k] = "$v";
    }
}
$data = serialize($fields);

// Verify the signature
$verification = openssl_verify($data, $signature, $public_key, OPENSSL_ALGO_SHA1);

if ($verification == 1) {

    //Fetch the details of Customer
    $billingEmail   = $_POST['email'];
    $billingEmail   = strtolower($billingEmail);
    $billingName    = $_POST['customer_name'];
    $country        = $_POST['p_country'];
    $currency       = $_POST['p_currency'];
    $subscriptionId = $_POST['p_order_id'];
    $productID      = $_POST['p_product_id'];
    $totalCost      = $_POST['p_sale_gross'];
    $dateCreated    = $_POST['event_time'];

} else {
    echo 'The signature is invalid!';
}
