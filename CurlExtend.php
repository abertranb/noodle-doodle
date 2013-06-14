<?php
/**
* @author decima
*
*/
class CurlExtend {

    protected function encode_rfc3986($input) {
        $output = rawurlencode($input);
        $output = str_replace('%7E', '~', $output);
        $output = str_replace('&amp;', '&', $output);
        $output = str_replace('+', ' ', $output);
        return $output;
    }

    protected function getSignedParams($method, $url, $params, $secret = null) {

        $params['oauth_version'] = "1.0";
        $params['oauth_signature_method'] = "HMAC-SHA1";
        $params['oauth_timestamp'] = time();
        $params['oauth_nonce'] = rand();

        if (isset($params['oauth_consumer_secret'])) {
            $secret = $params['oauth_consumer_secret'];
            unset($params['oauth_consumer_secret']);
        }
        $secret .= "&";
        if (isset($params['oauth_token_secret'])) {
            $secret .= $params['oauth_token_secret'];
            unset($params['oauth_token_secret']);
        }
        ksort($params);
        $enc_params = http_build_query($params);
        $enc_url = strtoupper($method) . "&" . $this->encode_rfc3986($url) . "&" . $this->encode_rfc3986($enc_params);

        $signature = base64_encode(hash_hmac('sha1', $enc_url, $secret, true));
        $params['oauth_signature'] = $signature;

        return $params;
    }

    protected function getCurl($url, $params, $header = array()) {

        $params = $this->getSignedParams("get", $url, $params);

        $href = $url . "?" . http_build_query($params);

        $curl = curl_init($href);
        
        $header[] = "User-Agent: " . $_SERVER['HTTP_USER_AGENT'];
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        if ($header != array()) {

            curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        }
        $response = curl_exec($curl);

        return $response;
    }

    protected function deleteCurl($url, $params, $header = NULL) {

        $params = $this->getSignedParams("delete", $url, $params);
        $href = $url . "?" . http_build_query($params);
        $curl = curl_init($href);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "DELETE");
        $head[] = "Content-type: application/xml";
        $head[] = $header;
        curl_setopt($curl, CURLOPT_HTTPHEADER, $head);
        curl_setopt($curl, CURLOPT_HEADER, 1);

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_VERBOSE, 1);
        $response = curl_exec($curl);


        return $response;
    }

    protected function postCurl($url, $params, $body, $header = null) {

        $params = $this->getSignedParams("post", $url, $params);
        $href = $url . "?" . http_build_query($params);
        $curl = curl_init($href);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $body);
        $head = Array("Content-type: application/xml");
        $head[] = $header;
        curl_setopt($curl, CURLOPT_HTTPHEADER, $head);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_VERBOSE, 1);
        curl_setopt($curl, CURLOPT_HEADER, 1);
        $response = curl_exec($curl);

        return $response;
    }

    protected function putCurl($url, $params, $body, $header = null) {

        $params = $this->getSignedParams("put", $url, $params);
        $href = $url . "?" . http_build_query($params);
        $curl = curl_init($href);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($curl, CURLOPT_POSTFIELDS, $body);
        $head = array("Content-type: application/xml");
        $head[] = $header;

        curl_setopt($curl, CURLOPT_HEADER, 1);

        curl_setopt($curl, CURLOPT_HTTPHEADER, $head);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_VERBOSE, 1);
        $response = curl_exec($curl);

        return $response;
    }

    protected function parse_get_result($in) {
        parse_str($in, $out);
        return $out;
    }

    protected function formate_get_result($in) {
        $out = simplexml_load_string($in);
        return $out;
    }

    protected function formate_post_result($in) {
        $post = str_replace("\r", "", $in);
        $post = explode("\n", $post);
        $res = array();
        foreach ($post as $p) {
            if ($p) {
                $arr = explode(": ", $p);
                if (count($arr) < 2) {
                    $res[] = $p;
                } else if (count($arr) == 2) {
                    $res[$arr[0]] = $arr[1];
                } else {
                    $res[] = $p;
                }
            }
        }

        return $res;
    }

}
