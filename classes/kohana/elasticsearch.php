<?php defined('SYSPATH') or die('No direct script access.');

/*
 * Elasticsearch Kohana Module
 */
class Kohana_Elasticsearch{
  public $host, $index, $port;

  private static $instance;

  public static function instance(){
    if(!self::$instance){
      $config = Kohana::$config->load('elasticsearch');
      return self::$instance = new self($config->get('host'), $config->get('port'), $config->get('index'));
    } else {
      return self::$instance;
    }
  }

  private function __construct($host, $port, $index = 'kohana'){
    $this->index = $index;
    $this->host = $host;
    $this->port = $port;
  }

  function fetch($path, $method = 'GET', $content = array()){
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

  function count($type){
    return $this->fetch($type . '/_count', 'GET', '{ matchAll:{} }');
  }

  function status(){
    return $this->fetch('_status');
  }

  function create(){
    $this->fetch(NULL, 'PUT');
  }

  function add($type, $id, $data){
    return $this->fetch($type . '/' . $id, 'PUT', $data);
  }

  function delete($type, $id){
    $this->fetch($type . '/' . $id, 'DELETE');
  }

  function update($type, $id, $data){
    $this->fetch($type . '/' . $id . '/_update', 'POST', $data);
  }

  function mapping($type, $data){
    return $this->fetch($type . '/_mapping', 'PUT', $data);
  }

  function search($type, $query = array()){
    return $this->fetch($type . '/_search', 'GET', $query);
  }
}

