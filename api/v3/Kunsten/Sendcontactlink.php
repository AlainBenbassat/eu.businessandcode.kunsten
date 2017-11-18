<?php
use CRM_Kunsten_ExtensionUtil as E;

function _civicrm_api3_kunsten_Sendcontactlink_spec(&$spec) {
  $spec['email'] = array(
    'api.required' => 1,
    'title' => 'Email',
    'type' => CRM_Utils_Type::T_EMAIL,
  );
}

function civicrm_api3_kunsten_Sendcontactlink($params) {
  try {
    if (!array_key_exists('email', $params)) {
      throw new Exception('email is required');
    }

    // get the configuration
    $kunstenConfig = CRM_Kunsten_Config::singleton();

    // lookup get the contact, based on the email address
    $p = array(
      'email' => $params['email'],
      'sequential' => 1,
      'contact_type' => 'Individual',
      'is_deleted' => 0,
      'is_deceased' => 0,
    );
    $c = civicrm_api3('Contact', 'get', $p);

    if ($c['count'] > 1) {
      // multiple contacts with that email address, return an error
      throw new Exception('Multiple contacts with that email address');
    }

    if ($c['count'] == 0) {
      // no contact with this email address: create it
      $c = civicrm_api3('Contact', 'create', $p);

      // get the welcome message template ID
      $templateID = $kunstenConfig->getWelcomeMessageTemplateID();
    }
    else {
      // get the update message template ID
      $templateID = $kunstenConfig->getUpdateMessageTemplateID();
    }

    // make sure the template id is filled in
    if ($templateID == 0) {
      throw new Exception('Template id for update and welcome message should be configured');
    }

    // check the email params
    if ($kunstenConfig->getFromName() == 'FIXME') {
      throw new Exception('Email settings should be configured');
    }

    // send the mail
    $p = array(
      'contact_id' => $c['values'][0]['id'],
      'template_id' => $templateID,
      'from_name' => $kunstenConfig->getFromName(),
      'from_email' => $kunstenConfig->getFromEmail(),
    );
    $ret = civicrm_api3('Email', 'send', $p);

    if ($ret['is_error'] == 1) {
      throw new Exception('Send mail returned an error');
    }
  }
  catch (Exception $e) {
    throw new API_Exception('Could not retrieve contact: ' . $e->getMessage(), 999);
  }

  return civicrm_api3_create_success('Message sent', $params);
}
