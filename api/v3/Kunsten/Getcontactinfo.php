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
    if (!array_key_exists('id', $params) || $params['id'] == '') {
      throw new Exception('id is required');
    }

    if (!array_key_exists('hash', $params) || $params['hash'] == '') {
      throw new Exception('hash is required');
    }

    // get the configuration
    $config = CRM_Kunsten_Config::singleton();

    // get the contact info
    $p = array(
      'id' => $params['id'],
      'hash' => $params['hash'],
      'is_deleted' => 0,
      'is_deceased' => 0,
      'return' => array(
        'gender_id',
        'first_name',
        'last_name',
        'current_employer',
        'email',
        $config->getCustomFieldColumn('kunstenpunt_nieuws'),
        $config->getCustomFieldColumn('flanders_arts_institute_news'),
        $config->getCustomFieldColumn('initiatieven_themas'),
      ),
    );
    $c = civicrm_api3('Contact', 'getsingle', $p);

    // return only these fields
    $returnArr = array();
    $returnArr['id'] = $c['id'];
    $returnArr['hash'] = $c['hash'];
    $returnArr['gender_id'] = $c['gender_id'];
    $returnArr['first_name'] = $c['first_name'];
    $returnArr['last_name'] = $c['last_name'];
    $returnArr['current_employer'] = $c['current_employer'];
    $returnArr['email'] = $c['email'];
    $returnArr['kunstenpunt_nieuws'] = $c[$config->getCustomFieldColumn('kunstenpunt_nieuws')];
    $returnArr['flanders_arts_institute_news'] = $c[$config->getCustomFieldColumn('flanders_arts_institute_news')];
    $returnArr['initiatieven_themas'] = $c[$config->getCustomFieldColumn('initiatieven_themas')];
  }
  catch (Exception $e) {
    throw new API_Exception('Could not retrieve contact: ' . $e->getMessage(), 999);
  }

  return civicrm_api3_create_success($returnArr, $params);
}
