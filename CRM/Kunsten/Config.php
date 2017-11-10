<?php

class CRM_Kunsten_Config {

  private static $singleton;

  private $_profilePageLink;

  private function __construct() {
    try {
      $this->_profilePageLink = civicrm_api3('OptionValue', 'getvalue', array(
        'return' => 'value',
        'name' => 'profile_page_link',
        'option_group_id' => 'contact_profile',
      ));
    }
    catch (Exception $e) {
      throw new Exception('Could not retrieve the option value: profile page link');
    }
  }

  public static function singleton() {
    if (!self::$singleton) {
      self:: $singleton = new CRM_Kunsten_Config();
    }
    return self::$singleton;
  }

  public function getProfilePageLink() {
    return $this->_profilePageLink;
  }
}