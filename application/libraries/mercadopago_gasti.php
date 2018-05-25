<?php

/**
 * MercadoPago Integration Library
 * Access MercadoPago for payments integration
 *
 * @author hcasatti
 *
 */
$GLOBALS["LIB_LOCATION"] = dirname(__FILE__);

class mercadopago_Gasti {

    private $client_id = "3713889655589498";
    private $client_secret = "5M0LpvuHUiojSGkohsQ7KxEbINtXk3Wa";
    private $access_data;
    //private $sandbox = FALSE;

    public function init($country) {
        if($country == 'AR'){
            $this->client_id = "3713889655589498";
            $this->client_secret = "5M0LpvuHUiojSGkohsQ7KxEbINtXk3Wa";
        }else if($country == 'MX'){
            $this->client_id = "1577949684708528";
            $this->client_secret = "to9n2YXsZEtuednWD9btMjztjEsFfGu8";
        }
    }
/*
    public function sandbox_mode($enable = NULL) {
        if (!is_null($enable)) {
            $this->sandbox = $enable === TRUE;
        }

        return $this->sandbox;
    }
*/
    /**
     * Get Access Token for API use
     */
    public function get_access_token() {
        $appClientValues = $this->build_query(array(
            'client_id' => $this->client_id,
            'client_secret' => $this->client_secret,
            'grant_type' => 'client_credentials'
                ));

        $access_data = $this->post("/oauth/token", $appClientValues, "application/x-www-form-urlencoded");

        $this->access_data = $access_data['response'];

        return $this->access_data['access_token'];
    }

    /**
     * Create a checkout preference
     * @param array $preference
     * @return array(json)
     */
    public function create_preference($preference) {
        $accessToken = $this->get_access_token();

        $preferenceResult = $this->post("/checkout/preferences?access_token=" . $accessToken, $preference);

        return $preferenceResult;
    }

    /*     * **************************************************************************************** */

    private function build_query($params) {
        if (function_exists("http_build_query")) {
            return http_build_query($params);
        } else {
            foreach ($params as $name => $value) {
                $elements[] = "{$name}=" . urlencode($value);
            }

            return implode("&", $elements);
        }
    }

    /*     * **************************************************************************************** */

    private function getConnect($uri, $method, $contentType) {
        $connect = curl_init("https://api.mercadolibre.com" . $uri);

        curl_setopt($connect, CURLOPT_USERAGENT, "MercadoPago PHP SDK v0.2.1");
        //curl_setopt($connect, CURLOPT_CAINFO, $GLOBALS["LIB_LOCATION"] . "/cacert.pem");
        curl_setopt($connect, CURLOPT_SSLVERSION, 3);
        curl_setopt($connect, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($connect, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($connect, CURLOPT_HTTPHEADER, array("Accept: application/json", "Content-Type: " . $contentType));

        return $connect;
    }

    private function setData(&$connect, $data, $contentType) {
        if ($contentType == "application/json") {
            if (gettype($data) == "string") {
                json_decode($data, true);
            } else {
                $data = json_encode($data);
            }

            if (function_exists('json_last_error')) {
                $json_error = json_last_error();
                if ($json_error != JSON_ERROR_NONE) {
                    throw new Exception("JSON Error [{$json_error}] - Data: {$data}");
                }
            }
        }

        curl_setopt($connect, CURLOPT_POSTFIELDS, $data);
    }

    private function exec($method, $uri, $data, $contentType) {
        $connect = $this->getConnect($uri, $method, $contentType);

        if ($data)
            $this->setData($connect, $data, $contentType);

        $apiResult = curl_exec($connect);
        $apiHttpCode = curl_getinfo($connect, CURLINFO_HTTP_CODE);

        $response = array(
            "status" => $apiHttpCode,
            "response" => json_decode($apiResult, true)
        );

        if ($response['status'] >= 400) {
            throw new Exception($response['response']['message'], $response['status']);
        }

        curl_close($connect);

        return $response;
    }

    private function post($uri, $data, $contentType = "application/json") {
        return $this->exec("POST", $uri, $data, $contentType);
    }
}
?>