<?php
/**
* @author decima
*
*/
require_once 'CurlExtend.php';
require_once 'DoodleData.php';
class DoodleAPI extends CurlExtend {

    protected $key;
    protected $secret;

    const DOODLE_URL_API = "http://doodle.com/api1/";
    const DOODLE_OAUTH_REQUEST_TOKEN = "oauth/requesttoken";
    const DOODLE_OAUTH_ACCESS_TOKEN = "oauth/accesstoken";
    const DOODLE_POLL_URL = "polls/";

    public function __construct($key, $secret) {
        $this->key = $key;
        $this->secret = $secret;
    }

    protected function initiateDoodle($header = null) {
        $in_params=array();
        $in_params['oauth_consumer_key'] = $this->key;
        $in_params['oauth_consumer_secret'] = $this->secret;
        $url = self::DOODLE_URL_API . self::DOODLE_OAUTH_REQUEST_TOKEN;
        $out = $this->getCurl($url, $in_params, $header);
        $out = $this->parse_get_result($out);

        $in_params['oauth_token'] = $out['oauth_token'];
        $in_params['oauth_token_secret'] = $out['oauth_token_secret'];

        $url = self::DOODLE_URL_API . self::DOODLE_OAUTH_ACCESS_TOKEN;

        $out = $this->getCurl($url, $in_params, $header);
        $out = $this->parse_get_result($out);

        $in_params['oauth_token'] = $out['oauth_token'];
        $in_params['oauth_token_secret'] = $out['oauth_token_secret'];

        return $in_params;
    }

    public function getPoll($poll_id, $x_poll_id = null) {
        $header = null;
        if ($x_poll_id) {
            $header[] = "X-DoodleKey:" . $x_poll_id;
        }

        $in_params = $this->initiateDoodle();

        $url = self::DOODLE_URL_API . self::DOODLE_POLL_URL . $poll_id;
        $r = $this->getCurl($url, $in_params, $header);
        $r = $this->formate_get_result($r);

        return $r;
    }

    public function createPoll($xml_string) {

        $in_params = $this->initiateDoodle();

        $url = self::DOODLE_URL_API . self::DOODLE_POLL_URL;
        
        $res = $this->postCurl($url, $in_params, $xml_string);
        
        $results = $this->formate_post_result($res);
        
        if (stristr($results[0], "201"))
            return $results;
        else
            throw new Exception($results[0]);
    }

    public function updatePoll($poll_id, $x_poll_id, $xml_string) {
        $header = "X-DoodleKey:" . $x_poll_id;
        $in_params = $this->initiateDoodle();
        $url = self::DOODLE_URL_API . self::DOODLE_POLL_URL . $poll_id;
        $res = $this->putCurl($url, $in_params, $xml_string, $header);
        $results = $this->formate_post_result($res);
        if (stristr($results[0], "200"))
            return $results;
        else
            throw new Exception($results[0]);
    }

    public function deletePoll($poll_id, $x_poll_id) {
        $header = "X-DoodleKey:" . $x_poll_id;
        $in_params = $this->initiateDoodle();
        $url = self::DOODLE_URL_API . self::DOODLE_POLL_URL . $poll_id;
        $res = $this->deleteCurl($url, $in_params, $header);
        $results = $this->formate_post_result($res);
        if (stristr($results[0], "204"))
            return $results;
        else
            throw new Exception($results[0]);
    }

    public function addParticipant($poll_id, $xml_string) {
        $in_params = $this->initiateDoodle();
        $url = self::DOODLE_URL_API . self::DOODLE_POLL_URL . $poll_id . "/participants";
        $res = $this->postCurl($url, $in_params, $xml_string);
        $results = $this->formate_post_result($res);
        if (stristr($results[0], "201"))
            return $results;
        else
            throw new Exception($results[0]);
    }

    public function updateParticipant($poll_id, $x_poll_id, $participant_id, $xml_string) {
        $header = "X-DoodleKey:" . $x_poll_id;

        $in_params = $this->initiateDoodle();
        $url = self::DOODLE_URL_API . self::DOODLE_POLL_URL . $poll_id . "/participants/" . $participant_id;
        $res = $this->putCurl($url, $in_params, $xml_string, $header);
        $results = $this->formate_post_result($res);
        if (stristr($results[0], "200"))
            return $results;
        else
            throw new Exception($results[0]);
    }

    public function deleteParticipant($poll_id, $x_poll_id, $participant_id) {
        $header = "X-DoodleKey:" . $x_poll_id;
        $in_params = $this->initiateDoodle();
        $url = self::DOODLE_URL_API . self::DOODLE_POLL_URL . $poll_id . "/participants/" . $participant_id;
        $res = $this->deleteCurl($url, $in_params, $header);
        $results = $this->formate_post_result($res);
        if (stristr($results[0], "204"))
            return $results;
        else
            throw new Exception($results[0]);
    }

    public function addComment($poll_id, $xml_string) {
        $in_params = $this->initiateDoodle();
        $url = self::DOODLE_URL_API . self::DOODLE_POLL_URL . $poll_id . "/comments";
        $res = $this->postCurl($url, $in_params, $xml_string);
        $results = $this->formate_post_result($res);
        if (stristr($results[0], "201"))
            return $results;
        else
            throw new Exception($results[0]);
    }

    public function deleteComment($poll_id, $x_poll_id, $comment_id) {
        $header = "X-DoodleKey:" . $x_poll_id;
        $in_params = $this->initiateDoodle();
        $url = self::DOODLE_URL_API . self::DOODLE_POLL_URL . $poll_id . "/comments/" . $comment_id;
        $res = $this->deleteCurl($url, $in_params, $header);
        $results = $this->formate_post_result($res);
        if (stristr($results[0], "204"))
            return $results;
        else
            throw new Exception($results[0]);
    }

}
