<?php

class CRM_Kunsten_Config {

  private static $singleton;

  private $_profilePageLink;
  private $_welcomeMessageTemplateID;
  private $_updateMessageTemplateID;
  private $_fromEmail;
  private $_fromName;
  private $_customFields;

  private function __construct() {
    try {
      $this->_profilePageLink = civicrm_api3('OptionValue', 'getvalue', array(
        'return' => 'value',
        'name' => 'profile_page_link',
        'option_group_id' => 'contact_profile',
      ));

      $this->_fromName = civicrm_api3('OptionValue', 'getvalue', array(
        'return' => 'value',
        'name' => 'from_name',
        'option_group_id' => 'contact_profile',
      ));

      $this->_fromEmail = civicrm_api3('OptionValue', 'getvalue', array(
        'return' => 'value',
        'name' => 'from_email',
        'option_group_id' => 'contact_profile',
      ));

      $this->_welcomeMessageTemplateID = civicrm_api3('OptionValue', 'getvalue', array(
        'return' => 'value',
        'name' => 'welcome_message_template_id',
        'option_group_id' => 'contact_profile',
      ));

      $this->_updateMessageTemplateID = civicrm_api3('OptionValue', 'getvalue', array(
        'return' => 'value',
        'name' => 'update_message_template_id',
        'option_group_id' => 'contact_profile',
      ));

      // get custom field id's
      $result = civicrm_api3('CustomField', 'get', array(
        'sequential' => 1,
        'custom_group_id' => "kunstenpunt_communicatie",
      ));
      $this->_customFields = array();
      foreach ($result['values'] as $field) {
        $this->_customFields[$field['name']] = 'custom_' . $field['id'];
      }
    }
    catch (Exception $e) {
      throw new Exception('Could not retrieve the option value: profile page link');
    }
  }

  public static function singleton() {
    if (!self::$singleton) {
      self::$singleton = new CRM_Kunsten_Config();
    }
    return self::$singleton;
  }

  public function getProfilePageLink() {
    return $this->_profilePageLink;
  }

  public function getWelcomeMessageTemplateID() {
    return $this->_welcomeMessageTemplateID;
  }

  public function getUpdateMessageTemplateID() {
    return $this->_updateMessageTemplateID;
  }

  public function getFromEmail() {
    return $this->_fromEmail;
  }

  public function getFromName() {
    return $this->_fromName;
  }

  public function getCustomFieldColumn($name) {
    return $this->_customFields[$name];
  }
}