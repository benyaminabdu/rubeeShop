<?php 

    namespace App\Functions;

    use Firebase\JWT\JWT;
    use GuzzleHttp\Client;

    class Santimpay {
        var $URL = "https://services.santimpay.com/api/v1/gateway";

        public $privateKey;
        public $merchantId;
    
        public function __construct($merchantId, $privateKey){
            $this->privateKey =  $privateKey;
            $this->merchantId =  $merchantId;
        }
    
        function generateSignedToken($amount, $paymentReason){
            $time = time();
            $data = array(
                'amount' => $amount,
                'paymentReason' => $paymentReason,
                'merchantId' => $this->merchantId,
                'generated' => $time
            );
    
            $jwt = JWT::encode($data, $this->privateKey, 'ES256');
    
            return $jwt;
        }
    
        function generatePaymentURL($id, $amount, $paymentReason, $succesRedirectUrl, $failureRedirectUrl, $notifyUrl, $cancelRedirectUrl){
            $client = new \GuzzleHttp\Client();
    
            $token = $this->generateSignedToken($amount, $paymentReason);
            $headers = array(
                'Content-Type' => 'application/json; charset=UTF-8'
            );
            $body = array(
                'id' => $id,
                'amount' => $amount,
                'reason' => $paymentReason,
                'merchantId' => $this->merchantId,
                'signedToken' => $token,
                'successRedirectUrl' => $succesRedirectUrl,
                'failureRedirectUrl' => $failureRedirectUrl,
                'notifyUrl' => $notifyUrl,
                'cancelRedirectUrl' => $cancelRedirectUrl
            );
    
    
            try {
                $response = $client->post($this->URL . '/initiate-payment', [
                    'headers' => $headers,
                    'body' => json_encode($body)
                ]);
    
                $responseContent = $response->getBody()->getContents();

                return $responseContent;

            } catch (\Exception $e) {
                echo $e->getMessage();
            }
        }
        
    }

?>