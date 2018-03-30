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
    // prepare params to update the contact
    $p = array(
      'id' => $c['id'],
    );

    // mailing preference: kunstenpunt nieuws
    if (array_key_exists('kunstenpunt_nieuws', $params)) {
      $p[$config->getCustomFieldColumn('kunstenpunt_nieuws')] = $params['kunstenpunt_nieuws'];
    }

    // mailing preference: flanders a.i.
    if (array_key_exists('flanders_arts_institute_news', $params)) {
      $p[$config->getCustomFieldColumn('flanders_arts_institute_news')] = $params['flanders_arts_institute_news'];
    }

    // mailing preference: initiatieven
    if (array_key_exists('initiatieven_themas', $params)) {
      $p[$config->getCustomFieldColumn('initiatieven_themas')] = $params['initiatieven_themas'];
    }

    // check the first name
    if ($c['first_name'] != $params['first_name']) {
      // accept the first name it the original was empty
      if (empty($c['first_name'])) {
        $p['first_name'] = $params['first_name'];
      }
      else {
        // original was not empty, don't override but create activity
        updatecontactinfo_createActivity(
          $config->getChangedDataActivityTypeID(),
          $c['id'],
          'Voornaam gewijzigd',
          $c['first_name'],
          $params['first_name']
        );
      }
    }

    // check the last name
    if ($c['last_name'] != $params['last_name']) {
      // accept the last name it the original was empty
      if (empty($c['last_name'])) {
        $p['last_name'] = $params['last_name'];
      }
      else {
        // original was not empty, don't override but create activity
        updatecontactinfo_createActivity(
          $config->getChangedDataActivityTypeID(),
          $c['id'],
          'Achternaam gewijzigd',
          $c['last_name'],
          $params['last_name']
        );
      }
    }

    // update the contact
    civicrm_api3('Contact', 'create', $p);

    // check the current employer
    if ($c['current_employer'] != $params['current_employer']) {
      // create an activity
      updatecontactinfo_createActivity(
        $config->getChangedDataActivityTypeID(),
        $c['id'],
        'Organisatie gewijzigd',
        $c['current_employer'],
        $params['current_employer']
      );
    }

    // check the e-mail
    if ($c['email'] != $params['email']) {
      // create an activity
      updatecontactinfo_createActivity(
        $config->getChangedDataActivityTypeID(),
        $c['id'],
        'E-mail gewijzigd',
        $c['email'],
        $params['email']
      );
    }
  }
  catch (Exception $e) {
    throw new API_Exception('Could not save contact: ' . $e->getMessage(), 999);
  }

  return civicrm_api3_create_success('OK', $params);
}

function updatecontactinfo_createActivity($activityID, $contactID, $subject, $oldVal, $newVal) {
  $details = '<p>oude waarde: ' . $oldVal .
    '<br>nieuwe waarde: ' . $newVal .
    '</p>';

  // create an activity
  $p = array(
    'activity_type_id' => $activityID,
    'subject' => $subject,
    'activity_date_time' => date('YmdHis'),
    'is_test' => 0,
    'status_id' => 1,
    'priority_id' => 2,
    'details' => $details,
    'source_contact_id' => $contactID,
    'target_contact_id' => $contactID,
  );
  CRM_Activity_BAO_Activity::create($p);
}