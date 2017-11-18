<?php
use CRM_Kunsten_ExtensionUtil as E;

function _civicrm_api3_kunsten_Getcontactinfo_spec(&$spec) {
  $spec['id'] = array(
    'api.required' => 1,
    'title' => 'Contact ID',
    'type' => CRM_Utils_Type::T_INT,
  );

  $spec['hash'] = array(
    'api.required' => 1,
    'title' => 'Contact hash',
    'type' => CRM_Utils_Type::T_STRING,
  );
}

function civicrm_api3_kunsten_Getcontactinfo($params) {
  try {
    if (!array_key_exists('id', $params)) {
      throw new Exception('id is required');
    }

    if (!array_key_exists('hash', $params)) {
      throw new Exception('hash is required');
    }

    // get the contact info
    $p = array(
      'id' => $params['id'],
      'hash' => $params['hash'],
      'is_deleted' => 0,
      'is_deceased' => 0,
    );
    $c = civicrm_api3('Contact', 'getsingle', $p);

    $returnArr = array();
    $returnArr['gender_id'] = $c['gender_id'];
    $returnArr['first_name'] = $c['first_name'];
    $returnArr['last_name'] = $c['last_name'];
    $returnArr['current_employer'] = $c['current_employer'];
    $returnArr['email'] = $c['email'];
  }
  catch (Exception $e) {
    throw new API_Exception('Could not retrieve contact: ' . $e->getMessage(), 999);
  }

  return civicrm_api3_create_success($returnArr, $params);
}
