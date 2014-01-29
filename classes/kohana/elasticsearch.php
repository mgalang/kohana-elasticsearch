<?php defined('SYSPATH') or die('No direct script access.');

/*
 * Elasticsearch Kohana Module
 */
class Kohana_Elasticsearch{
  public $host, $index, $port;

  private static $instance;

  public static function instance($index = null){
    if(!self::$instance){
      $config = Kohana::$config->load('elasticsearch');
      return self::$instance = new self($config->get('host'), $config->get('port'), isset($index) ? $index : $config->get('index'));
    } else {
      return self::$instance;
    }
  }

  private function __construct($host, $port, $index){
    $this->index = $index;
    $this->host = $host;
    $this->port = $port;
  }

  private function request($path, $method = 'GET', $content = array()){
    // create url
    $url = $this->host .'/'. $this->index . '/' . $path;

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_PORT, $this->port);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($method));

    if(!empty($content)){
      $data = $content;
      if(is_array($content))
        $data = json_encode($content);

      curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    }

    $result = curl_exec($ch);
    curl_close($ch);
    return json_decode($result);
  }

  function count($type, $data = false){
    return $this->request($type . '/_count', 'GET', ($data) ? $data : '{ matchAll:{} }');
  }

  function status(){
    return $this->request('_status');
  }

  function add($type, $id, $data){
    return $this->request($type . '/' . $id, 'PUT', $data);
  }

  function delete($type, $id){
    $this->request($type . '/' . $id, 'DELETE');
  }

  function deleteall($type){
    $this->request($type, 'DELETE');
  }

  function update($type, $id, $data){
    $this->request($type . '/' . $id . '/_update', 'POST', $data);
  }

  function mapping($type, $data){
    return $this->request($type . '/_mapping', 'PUT', $data);
  }

  function search($type, $query = array()){
    return $this->request($type . '/_search', 'POST', $query);
  }
}
