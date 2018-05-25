<?php

require_once 'Request.php';

class Caller {

    /**
     * calls displaywords api
     *
     * @param ANRequest $request
     */
    public static function call(Request $request) {
        try {
            $con2 = mysql_connect("205.186.153.231", "produccion", "prod_2013");
            mysql_select_db("produccion_mediafem", $con2);

            if (empty($request->uri)) {
                throw new Exception("Request.uri could not be empty");
            }
            if (empty($request->method)) {
                throw new Exception("Request.method could not be empty");
            }
            $method = strtolower($request->method);
            $curlHandle = curl_init($request->uri);
            if ('put' == $method || 'post' == $method) {
                if (empty($request->data)) {
                    throw new Exception("Request.method is " . $request->method . " but Request.data is empty");
                }
                if ('post' == $method) {
                    curl_setopt($curlHandle, CURLOPT_POST, 1);
                    curl_setopt($curlHandle, CURLOPT_POSTFIELDS, json_encode($request->data));
                } else {
                    $jsonString = json_encode($request->data);
                    $fh = fopen("php://memory", "w");
                    fwrite($fh, $jsonString);
                    rewind($fh);
                    ////curl_setopt($curlHandle, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($curlHandle, CURLOPT_PUT, true);
                    curl_setopt($curlHandle, CURLOPT_INFILE, $fh);
                    curl_setopt($curlHandle, CURLOPT_INFILESIZE, strlen($jsonString));
                }
            }
            curl_setopt($curlHandle, CURLOPT_FOLLOWLOCATION, 0);
            curl_setopt($curlHandle, CURLOPT_HEADER, 0);
            curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, 1);
            if (!empty($request->token)) {
                curl_setopt($curlHandle, CURLOPT_HTTPHEADER, array('Authorization: ' . $request->token));
            }
            $rs = curl_exec($curlHandle);
            
            mysql_query("INSERT INTO requests (uri, aplicacion, method, ip, response) VALUES ('$request->uri', 'anunciantes', '$request->method','" .
            $_SERVER['REMOTE_ADDR'] . "', '$rs');");

            if ($request->decodeResponse) {
                $rs = json_decode($rs);
                //die();
                if (isset($rs->response->error)) {

                    echo '<pre>';
                    var_dump($rs);
                    echo '</pre>';

                    echo 'ERROR CALLER: ';
                    echo strtoupper($rs->response->error_id . ' - ' . $rs->response->error . ' - ' . $rs->response->error_code) . "\n";
                    die();
                    
                    if ($rs->response->error_code == "NOTFOUND_ADVERTISER") {
                        return "NO_EXISTE";
                    } else if ($rs->response->error_code == "UNAUTH_ADVERTISER") {
                        return "NO_EXISTE";
                    } else if ($rs->response->error_code == "RATE_EXCEEDED" || $rs->response->error_id == "LIMIT") {
                        sleep(5);
                        return "RATE_EXCEEDED";
                    } else {
                        return false;
                    }

                    throw new Exception($rs->response->error_id . '::' . $rs->response->error);
                } else if (isset($rs->response->status) && $rs->response->status == 'OK') {
                    return $rs;
                } else {
                    echo ' ERROR 2: ';
                    return false;
                    throw new Exception('Request failed');
                }
            }
            return $rs;
        } catch (Exception $ex) {
            //echo "ERROR GRAL: " . $ex->getMessage() . "<br>";
            return false;
        }
    }
    
    public static function call_dfp(Request $request) {
        try {
         
            if (empty($request->uri)) {
                throw new Exception("Request.uri could not be empty");
            }
            if (empty($request->method)) {
                throw new Exception("Request.method could not be empty");
            }
            $method = strtolower($request->method);
            $curlHandle = curl_init($request->uri);
            if ('put' == $method || 'post' == $method) {
                if (empty($request->data)) {
                    throw new Exception("Request.method is " . $request->method . " but Request.data is empty");
                }
                if ('post' == $method) {
                    curl_setopt($curlHandle, CURLOPT_POST, 1);
                    curl_setopt($curlHandle, CURLOPT_POSTFIELDS, json_encode($request->data));
                } else {
                    $jsonString = json_encode($request->data);
                    $fh = fopen("php://memory", "w");
                    fwrite($fh, $jsonString);
                    rewind($fh);
                    ////curl_setopt($curlHandle, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($curlHandle, CURLOPT_PUT, true);
                    curl_setopt($curlHandle, CURLOPT_INFILE, $fh);
                    curl_setopt($curlHandle, CURLOPT_INFILESIZE, strlen($jsonString));
                }
            }
            curl_setopt($curlHandle, CURLOPT_FOLLOWLOCATION, 0);
            curl_setopt($curlHandle, CURLOPT_HEADER, 0);
            curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, 1);
            if (!empty($request->token)) {
                curl_setopt($curlHandle, CURLOPT_HTTPHEADER, array('Authorization: ' . $request->token));
            }
            $rs = curl_exec($curlHandle);
            
            return $rs;
        } catch (Exception $ex) {
            //echo "ERROR GRAL: " . $ex->getMessage() . "<br>";
            return false;
        }
    }
    
    public static function call_download_report_appnexus(Request $request) {

        $con = mysql_connect("205.186.153.231", "produccion", "prod_2013");

        mysql_select_db("produccion_mediafem", $con);

        if (empty($request->uri)) {
            throw new Exception("Request.uri could not be empty");
        }
        if (empty($request->method)) {
            throw new Exception("Request.method could not be empty");
        }
        $method = strtolower($request->method);
        $curlHandle = curl_init($request->uri);
        if ('put' == $method || 'post' == $method) {
            if (empty($request->data)) {
                throw new Exception("Request.method is " . $request->method . " but Request.data is empty");
            }
            if ('post' == $method) {
                curl_setopt($curlHandle, CURLOPT_POST, 1);
                curl_setopt($curlHandle, CURLOPT_POSTFIELDS, json_encode($request->data));
            } else {
                $jsonString = json_encode($request->data);
                $fh = fopen("php://memory", "w");
                fwrite($fh, $jsonString);
                rewind($fh);
                ////curl_setopt($curlHandle, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($curlHandle, CURLOPT_PUT, true);
                curl_setopt($curlHandle, CURLOPT_INFILE, $fh);
                curl_setopt($curlHandle, CURLOPT_INFILESIZE, strlen($jsonString));
            }
        }
        if ('delete' == $method) {
            curl_setopt($curlHandle, CURLOPT_POSTFIELDS, json_encode($request->data));
            curl_setopt($curlHandle, CURLOPT_CUSTOMREQUEST, "DELETE");
        }
        curl_setopt($curlHandle, CURLOPT_FOLLOWLOCATION, 0);
        curl_setopt($curlHandle, CURLOPT_HEADER, 0);
        curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, 1);
        //curl_setopt($curlHandle, CURLOPT_NOBODY , 1);
        if (!empty($request->token)) {
            curl_setopt($curlHandle, CURLOPT_HTTPHEADER, array('Authorization: ' . $request->token));
        }
        $rs = curl_exec($curlHandle);

        //mysql_query("INSERT INTO requests (uri, aplicacion, method, ip, response) VALUES ('$request->uri', 'devsitiosfile', '$request->method','".
        //    $_SERVER['REMOTE_ADDR']."', '$rs');");

        return $rs;
    }

    public static function call_eplanning(Request $request) {
        if (empty($request->uri)) {
            throw new Exception("Request.uri could not be empty");
        }
        if (empty($request->method)) {
            throw new Exception("Request.method could not be empty");
        }
        $method = strtolower($request->method);
        $curlHandle = curl_init($request->uri);

        curl_setopt($curlHandle, CURLOPT_USERPWD, "api@mediafem" . ":" . "seba5859");
        //curl_setopt($curlHandle, CURLOPT_USERPWD, "sebastian.redvlog@net_demo" . ":" . "asd123");

        curl_setopt($curlHandle, CURLOPT_FOLLOWLOCATION, 0);
        curl_setopt($curlHandle, CURLOPT_HEADER, 0);
        curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, 1);

        $rs = curl_exec($curlHandle);

        if ($request->decodeResponse) {
            if (isset($rs->response->error)) {
                //echo ' ERROR: ';
                //echo strtoupper($rs->response->error_id . ' - ' . $rs->response->error);
                throw new Exception($rs->response->error_id . '::' . $rs->response->error);
            } else {
                return $rs;
            }
        }

        return $rs;
    }

}
