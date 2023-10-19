<?php

$json_data = json_decode(file_get_contents("php://input"));
$orderId = $json_data->queryResult->parameters->orderid;

$apiData = http_build_query(array (
    "orderId" => $orderId
));
$json_data->queryResult->parameters->orderid = $orderId;

$request = curl_init();
curl_setopt($request, CURLOPT_URL, 'https://orderstatusapi-dot-organization-project-311520.uc.r.appspot.com/api/getOrderStatus');
curl_setopt($request, CURLOPT_POST, TRUE);
curl_setopt($request, CURLOPT_POSTFIELDS, $apiData);
curl_setopt($request, CURLOPT_RETURNTRANSFER, TRUE);

$response = curl_exec($request);
if ($response === FALSE) {
    echo curl_error($request);
} else {
    $response = json_decode($response);
    $shipmentDate = $response->shipmentDate;
    $easyDate = date('l, d M Y', strtotime($shipmentDate));

    $webhookResponse = json_decode(file_get_contents("data.json"));
    $webhookResponse->fulfillmentText = "Your order will be shipped on " . $easyDate;
    $webhookResponse->fulfillmentMessages[0]->text->text[0] = "Your order will be shipped on " . $easyDate;

    echo json_encode($webhookResponse);
}

?>