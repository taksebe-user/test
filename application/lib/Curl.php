<?php


namespace application\lib;

//use \RuntimeException;


class Curl {

    protected $url;
    protected $headers;
    protected $query;
    protected $responses;

    public function __construct($url, $headers = array()){
        $this->prepare($url,[],$headers);
    }

    /**
     * @param $url
     * @param $headers
     * @param $query
     */
    public function prepare($url, $query, $headers = array()) {
        $this->url = $url;
        $this->headers = $headers;
        $this->query = http_build_query($query);
    }

    /**
     *  Execute post method curl request
     */
    public function exec_post() {

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL,$this->url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $this->headers);
        curl_setopt($curl, CURLOPT_ENCODING, 'gzip');
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $this->query);
        $this->responses = curl_exec($curl);
        curl_close($curl);
    }

    /**
     *  Execute get method curl request
     */
    public function exec_get() {

        $full_url = $this->url.'?'.$this->query;

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL,$full_url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_ENCODING, 'gzip');
        curl_setopt($curl, CURLOPT_HTTPHEADER, $this->headers);
        $this->responses = curl_exec($curl);
        curl_close($curl);

    }

    /**
     * @return mixed
     */
    public function get_response() {
        return $this->responses;
    }

    /**
     * @return mixed
     */
    public function get_response_assoc() {
        return json_decode($this->responses, true);
    }


}
?>