<?php

namespace alexeevdv\sms\ru;

class Client extends \yii\base\Component {

    public $api_id;

    public $test;

    const HTTP_URL = "http://sms.ru/";

    public function init() {

        parent::init();

        if (empty($this->api_id)){
            throw new \yii\base\InvalidConfigException("`api_id` param is required.");
        }
    }

    public function send(\alexeevdv\sms\ru\Sms $sms) {

        $data = [
            "to" => $sms->to,
            "text" => $sms->text,
        ];

        if ($this->test || $sms->test) {
            $data['test'] = 1;
        }

        return $this->apiCall("sms/send", $data);
    }

    public function cost(\alexeevdv\sms\ru\Sms $sms) {

        $data = [
            "to" => $sms->to,
            "text" => $sms->text,
        ];

        return $this->apiCall("sms/cost", $data);
    }

    public function balance() {        
       
        return $this->apiCall("my/balance");
    }

    public function limit() {

        return $this->apiCall("my/limit");
    }

    public function senders() {

        return $this->apiCall("my/senders");
    }

    public function status($id) {

        return $this->apiCall("sms/status", [
            "id" => $id,
        ]);
    }

    public function stopListAdd($phone, $description) {

        return $this->apiCall("stoplist/add", [
            "stoplist_phone" => $phone,
            "stoplist_text" => $description,
        ]);
    }

    public function stopListDelete($phone) {

        return $this->apiCall("stoplist/del", [
            "stoplist_phone" => $phone,
        ]);
    }

    public function stopListGet() {

        return $this->apiCall("stoplist/get");
    }

    private function apiCall($method, array $params = []) {

        //@todo Add other auth methods
        $params['api_id'] = $this->api_id;
        
        // via CURL
        if (function_exists('curl_version')) {

            $ch = curl_init(self::HTTP_URL.$method);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
            curl_setopt($ch, CURLOPT_TIMEOUT, 3);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
            $httpResponse = curl_exec($ch);
            $curl_errno = curl_errno($ch);
            $curl_error = curl_error($ch);
            curl_close($ch);    

            if ($curl_errno > 0) {
                throw new \yii\web\ServerErrorHttpException($curl_error);
            }

        // via file_get_contents
        } else {

            try {
                $httpResponse = file_get_contents(self::HTTP_URL.$method, false, stream_context_create([
                    'http' => [
                        'method' => 'POST',
                        'header' => 'Content-Type: application/x-www-form-urlencoded'.PHP_EOL,
                        'content' => http_build_query($params),
                        'timeout' => 3.0,
                    ],
                ]));
            } catch(\Exception $e) {
                throw new \yii\web\ServerErrorHttpException($e->getMessage());
            }
        }

        $response = new \alexeevdv\sms\ru\Response;
        $response->code = (int) substr($httpResponse, 0, 3);

        if ($response->code === 100) { // Success

            // Retrieving data from HTTP response
            switch($method) {
                case "sms/send":
                    $response->data = substr($httpResponse, 4);
                    // Server returns SMS id with balance information separated by whitespace. We don't need it
                    if (($pos = strpos($response->data, "\n")) !== false) {
                        $response->data = substr($response->data, 0, $pos);
                    }
                    break;
                case "my/balance":
                    $response->data = substr($httpResponse, 4);
                    break;
                case "my/limit":
                case "my/senders":
                case "sms/cost":
                    $response->data = [];
                    $lines = explode("\n", $httpResponse);
                    for ($i = 1; $i != count($lines); ++$i) {
                        $response->data[] = $lines[$i];
                    }
                    break;
                case "stoplist/get":
                    $response->data = [];
                    $lines = explode("\n", $httpResponse);
                    for ($i = 1; $i != count($lines); ++$i) {
                        list($phone, $description) = explode(";", $lines[$i], 2);
                        $response->data[$phone] = $description;
                    }

                    break;
            }
        }

        return $response;
    }
}
