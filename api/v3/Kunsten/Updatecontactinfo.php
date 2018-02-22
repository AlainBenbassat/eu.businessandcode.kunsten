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
      'id' => $c['id'],
    );

    if (array_key_exists('kunstenpunt_nieuws', $params)) {
      $p[$config->getCustomFieldColumn('kunstenpunt_nieuws')] = $params['kunstenpunt_nieuws'];
    }

    if (array_key_exists('flanders_arts_institute_news', $params)) {
      $p[$config->getCustomFieldColumn('flanders_arts_institute_news')] = $params['flanders_arts_institute_news'];
    }

    if (array_key_exists('initiatieven_themas', $params)) {
      $p[$config->getCustomFieldColumn('initiatieven_themas')] = $params['initiatieven_themas'];
    }
    civicrm_api3('Contact', 'create', $p);

    // check the current employer
    if ($c['current_employer'] != $params['current_employer']) {
      $details = '<p>oude waarde: ' . $c['current_employer'] .
        '<br>nieuwe waarde: ' . $params['current_employer'];

      // create an activity
      $p = array(
        'activity_type_id' => $config->getChangedDataActivityTypeID(),
        'subject' => 'organisatie gewijzigd',
        'activity_date_time' => date('YmdHis'),
        'is_test' => 0,
        'status_id' => 1,
        'priority_id' => 2,
        'details' => $details,
        'source_contact_id' => $c['id'],
        'target_contact_id' => $c['id'],
      );
      CRM_Activity_BAO_Activity::create($p);
    }

    // check the first name
    if ($c['first_name'] != $params['first_name']) {
      $details = '<p>oude waarde: ' . $c['first_name'] .
        '<br>nieuwe waarde: ' . $params['first_name'];

      // create an activity
      $p = array(
        'activity_type_id' => $config->getChangedDataActivityTypeID(),
        'subject' => 'voornaam gewijzigd',
        'activity_date_time' => date('YmdHis'),
        'is_test' => 0,
        'status_id' => 1,
        'priority_id' => 2,
        'details' => $details,
        'source_contact_id' => $c['id'],
        'target_contact_id' => $c['id'],
      );
      CRM_Activity_BAO_Activity::create($p);
    }

    // check the last name
    if ($c['last_name'] != $params['last_name']) {
      $details = '<p>oude waarde: ' . $c['last_name'] .
        '<br>nieuwe waarde: ' . $params['last_name'];

      // create an activity
      $p = array(
        'activity_type_id' => $config->getChangedDataActivityTypeID(),
        'subject' => 'Achternaam gewijzigd',
        'activity_date_time' => date('YmdHis'),
        'is_test' => 0,
        'status_id' => 1,
        'priority_id' => 2,
        'details' => $details,
        'source_contact_id' => $c['id'],
        'target_contact_id' => $c['id'],
      );
      CRM_Activity_BAO_Activity::create($p);
    }
  }
  catch (Exception $e) {
    throw new API_Exception('Could not save contact: ' . $e->getMessage(), 999);
  }

  return civicrm_api3_create_success('OK', $params);
}
