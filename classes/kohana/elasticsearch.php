<?php defined('SYSPATH') or die('No direct script access.');

/*
 * Elasticsearch Kohana Module
 */
class Kohana_Elasticsearch{
  public $host, $index, $port;

  public static function factory(){
    $config = Kohana::$config->load('elasticsearch');
    return new Elasticsearch($config->get('host'), $config->get('port'), $config->get('index'));
  }

  protected function __construct($host, $port, $index = 'kohana'){
    $this->index = $index;
    $this->host = $host;
    $this->port = $port;
  }

  function fetch($path, $method = 'GET', $content = array()){
    // create url
    $url = $this->host .'/'. $this->index . '/' . $path;

    if(is_array($content))
      $data = json_encode($content);
    else
      $data = $content;

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_PORT, $this->port);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($method));
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
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

  function delete(){
     $this->fetch(NULL, 'DELETE');
  }

  function mapping($type, $data){
    return $this->fetch($type . '/_mapping', 'PUT', $data);
  }

  function search($type, $q){
    return $this->fetch($type . '/_search?' . http_build_query(array('q' => $q)));
  }
}

