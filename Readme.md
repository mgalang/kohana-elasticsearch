# Kohana Elastic Search module
A simple kohana [Elasticsearch](http://www.elasticsearch.org/) module

## Usage
    $elasticsearch = Elasticsearch::instance();
  
    $elasticsearch->add('myType', 1, array('name' => 'My name'));
    
    $elasticsearch = Elasticsearch::instance('customindex');

## Installation
  Install to kohana's module directory and add to your application's bootstrap:
  
  Copy the configuration file from `config/elasticsearch.php` to your application's config directory.

Alpha version - Use at your own risk
