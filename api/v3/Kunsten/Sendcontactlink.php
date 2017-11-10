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

    // lookup get the contact info
    $p = array(
      'email' => $params['email'],
      'sequential' => 1,
      'is_deleted' => 0,
      'is_deceased' => 0,
    );
    $c = civicrm_api3('Contact', 'get', $p);

    if ($c['count'] == 0) {
      // no contact with this email address: create it
      $p['contact_type'] = 'Individual';
      $c = civicrm_api3('Contact', 'create', $p);
    }

    $kunstenConfig = CRM_Kunsten_Config::singleton();
    $url = $kunstenConfig->getProfilePageLink();

    if ($c['count'] == 1) {

      list($mailSent, $subject, $message, $html) = CRM_Core_BAO_MessageTemplate::sendTemplate(
        array(
          'groupName' => 'msg_tpl_workflow_event',
          'valueName' => 'participant_' . strtolower($mailType),
          'contactId' => $c['values'][0]->id,
          'tplParams' => array(
            'checksumValue' => 'IS DIT NODIG?',
          ),
          'from' => $receiptFrom,
          'toName' => $participantName,
          'toEmail' => $toEmail,
        )
      );

      if ($mailSent) {
        $returnArr = 'OK';
      }
      else {
        throw new Exception('Sendmail returned an error');
      }
    }
  }
  catch (Exception $e) {
    throw new API_Exception('Could not retrieve contact: ' . $e->getMessage(), 999);
  }

  return civicrm_api3_create_success($returnArr, $params);
}
