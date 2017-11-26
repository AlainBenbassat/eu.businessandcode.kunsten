<?php
use CRM_Kunsten_ExtensionUtil as E;

function _civicrm_api3_kunsten_Updatecontactinfo_spec(&$spec) {
}

function civicrm_api3_kunsten_Updatecontactinfo($params) {
  // make sure the combination id/hash is correct
  try {
    $p = array(
      'id' => $params['id'],
      'hash' => $params['hash'],
    );
    $c = civicrm_api3('Contact', 'getsingle', $p);
  }
  catch (Exception $e) {
    throw new API_Exception('Could not retrieve contact', 999);
  }

  // get the configuration
  $config = CRM_Kunsten_Config::singleton();

  try {
    $p = array(
      'id' => $params['id'],
      'first_name' => $params['first_name'],
      'last_name' => $params['last_name'],
      $config->getCustomFieldColumn('kunstenpunt_nieuws') => $params['kunstenpunt_nieuws'],
      $config->getCustomFieldColumn('flanders_arts_institute_news') => $params['flanders_arts_institute_news'],
      $config->getCustomFieldColumn('initiatieven_themas') => $params['initiatieven_themas'],
    );
    $c = civicrm_api3('Contact', 'create', $p);
  }
  catch (Exception $e) {
    throw new API_Exception('Could not save contact: ' . $e->getMessage(), 999);
  }

  return civicrm_api3_create_success('OK', $params);
}
