<?php

class Redis extends CComponent {

  public static $_client;

  public static $_pclient;

  /**
   * build Redis Client
   *
   * @return Predis\Client
   * */
  public static function client() {
    if (!class_exists('Predis\Client')) {
      require_once 'Predis/Autoloader.php';
      Predis\Autoloader::register();
    }
    if (!self::$_client instanceOf Predis\Client) {
      self::$_client = new Predis\Client();
      self::$_client->select(5);
    }
    if (!self::$_client->isConnected()) {
      self::$_client->connect();
      self::$_client->select(5);
    }
    return self::$_client;
  }

  public static function pclient($config = array('read_write_timeout'=>0)) {
    if (!class_exists('Predis\Client')) {
      require_once 'Predis/Autoloader.php';
      Predis\Autoloader::register();
    }
    if (!self::$_pclient instanceOf Predis\Client) {
      self::$_pclient = new Predis\Client($config);
    }
    return self::$_pclient;
  }

  /**
   * 触发后台cli运行
   *
   * @param $string $command
   * @param mixed array|string $args
   * @param int 0|n
   * */
  public static function triggerCommand($command, $args) {
    if (is_array($args)) {
      $args = implode(' ', $args);
    }
    return self::client()->publish('control_channel', sprintf('%s %s', $command, $args));
  }
}
