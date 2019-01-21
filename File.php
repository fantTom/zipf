<?php

Class File{

  protected $data;

  public function __construct(){
    $lang = $_GET['lang'];
    $this->data = file_get_contents('files/'.$lang.'.txt');
    //$this->data = "Пример мир";
}

  public function getData(){
    return $this->data;
  }

}

?>