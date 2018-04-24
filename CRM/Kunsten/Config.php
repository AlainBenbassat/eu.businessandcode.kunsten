<?php

class CRM_Kunsten_Config {

  private static $singleton;

  private $_profilePageLink;
  private $_profilePageLinkEN;
  private $_welcomeMessageTemplateID = [];
  private $_updateMessageTemplateID = [];
  private $_fromEmail;
  private $_fromName;
  private $_customFields;
  private $_changedDataActivityTypeID;

  private function __construct() {
    try {
      $this->_profilePageLink = civicrm_api3('OptionValue', 'getvalue', array(
        'return' => 'value',
        'name' => 'profile_page_link',
        'option_group_id' => 'contact_profile',
      ));

      $this->_profilePageLinkEN = civicrm_api3('OptionValue', 'getvalue', array(
        'return' => 'value',
        'name' => 'profile page link en',
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

      // the option value contains lang and id pairs, separated by comma
      // e.g. nl=13,en=54
      $values = civicrm_api3('OptionValue', 'getvalue', array(
        'return' => 'value',
        'name' => 'welcome_message_template_id',
        'option_group_id' => 'contact_profile',
      ));
      $valuesArray = explode(',', $values);
      foreach ($valuesArray as $v) {
        $langAndID = explode('=', $v);
        if ($langAndID[0] == 'nl') {
          $this->_welcomeMessageTemplateID['nl'] = $langAndID[1];
        }
        else if ($langAndID[0] == 'en') {
          $this->_welcomeMessageTemplateID['en'] = $langAndID[1];
        }
      }

      // the option value contains lang and id pairs, separated by comma
      // e.g. nl=13,en=54
      $values = civicrm_api3('OptionValue', 'getvalue', array(
        'return' => 'value',
        'name' => 'update_message_template_id',
        'option_group_id' => 'contact_profile',
      ));
      $valuesArray = explode(',', $values);
      foreach ($valuesArray as $v) {
        $langAndID = explode('=', $v);
        if ($langAndID[0] == 'nl') {
          $this->_updateMessageTemplateID['nl'] = $langAndID[1];
        }
        else if ($langAndID[0] == 'en') {
          $this->_updateMessageTemplateID['en'] = $langAndID[1];
        }
      }

      $this->_changedDataActivityTypeID = civicrm_api3('OptionValue', 'getvalue', array(
        'return' => 'value',
        'name' => 'Online gegevensaanpassing',
        'option_group_id' => 'activity_type',
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
      throw new Exception('Could not retrieve the option value: ' . $e->getMessage());
    }
  }

  public static function singleton() {
    if (!self::$singleton) {
      self::$singleton = new CRM_Kunsten_Config();
    }
    return self::$singleton;
  }

  public function getProfilePageLink($lang) {
    if ($lang == 'en') {
      return $this->_profilePageLinkEN;
    }
    else {
      return $this->_profilePageLink;
    }
  }

  public function getWelcomeMessageTemplateID($lang) {
    return $this->_welcomeMessageTemplateID[$lang];
  }

  public function getUpdateMessageTemplateID($lang) {
    return $this->_updateMessageTemplateID[$lang];
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

  public function getChangedDataActivityTypeID() {
    return $this->_changedDataActivityTypeID;
  }
}