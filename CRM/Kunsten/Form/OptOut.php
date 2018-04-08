<?php

// optout url:
// https://crm.kunsten.be/civicrm/?page=CiviCRM&q=civicrm/optout&a=SOMETHING&b=ANOTHER-THING&lang=nl

use CRM_Kunsten_ExtensionUtil as E;

class CRM_Kunsten_Form_OptOut extends CRM_Core_Form {

  public function buildQuickForm() {
    // add css to hide some stuff
    CRM_Core_Resources::singleton()->addStyle('#sidebar, #printer-friendly, div.breadcrumb { display: none; } ');

    // check if we're in a submit
    $optoutstatus = CRM_Utils_Array::value('optoutstatus', $_POST, 0);
    if ($optoutstatus !== 0) {
      // show the message and quit
      CRM_Core_Session::setStatus($optoutstatus);
      return;
    }

    // get query parameters
    $contactID = CRM_Utils_Array::value('a', $_GET, 0);
    $hash = CRM_Utils_Array::value('b', $_GET, 0);
    $language = CRM_Utils_Array::value('lang', $_GET);

    // check the language: if not 'en' it will be 'nl'
    if ($language !== 'en') {
      $language = 'nl';
    }

    // lookup the contact
    $errorMessage = '';
    try {
      $params = [
        'id' => $contactID,
        'hash' => $hash,
      ];
      $contact = civicrm_api3('Contact', 'getsingle', $params);
    }
    catch (Exception $e) {
      if ($language == 'nl') {
        $errorMessage = '<p>Er is een fout opgetreden. Mogelijk is de opt-out url niet correct.</p>';
      }
      else {
        $errorMessage = '<p>An error occured. Probably an error in the opt-out url.</p>';
      }
    }

    // set the different texts
    if ($language == 'nl') {
      $title = 'Opt-out Kunstenpunt';
      $submitText = 'Bevestigen';
      $optOutMessage = 'U bent uitgeschreven.';

      if ($errorMessage) {
        $introText = $errorMessage;
        $email = '';
      }
      else {
        $introText = '<p>Bevestig uw uitschrijving voor alle communicatie van Kunstenpunt (opt-out).</p>';
        $email = '<p>E-mail:' . $contact['email'] . '</p>';
      }
    }
    else {
      $title = 'Opt Out Flanders Arts Institute';
      $submitText = 'Confirm';
      $optOutMessage = 'Succesfully opted out.';

      if ($errorMessage) {
        $introText = $errorMessage;
        $email = '';
      }
      else {
        $introText = '<p>Confirm to opt out from all Flanders Arts Institute communication.</p>';
        $email = '<p>Email:' . $contact['email'] . '</p>';
      }
    }

    // set the title
    CRM_Utils_System::setTitle($title);

    // set the texts
    $this->assign('introText', $introText);
    $this->assign('email', $email);

    // submit button (only if no errors)
    if (!$errorMessage) {
      $this->addButtons([
        [
          'type' => 'submit',
          'name' => $submitText,
          'isDefault' => TRUE,
        ],
      ]);
      $this->addElement('hidden', 'id', $contactID);
      $this->addElement('hidden', 'optoutstatus', $optOutMessage);
    }

    parent::buildQuickForm();
  }

  public function postProcess() {
    // get the contact id
    $contactID = CRM_Utils_Array::value('id', $_POST, 0);
    $optOutMessage = CRM_Utils_Array::value('optoutmessage', $_POST, 0);

    // opt out the contact
    if ($contactID > 0) {
      $params = [
        'id' => $contactID,
        'is_opt_out' => 1,
      ];
      civicrm_api3('Contact', 'create', $params);
    }

    // create an activity
    $config = CRM_Kunsten_Config::singleton();
    $p = array(
      'activity_type_id' => $config->getChangedDataActivityTypeID(),
      'subject' => 'Opt-out vanuit mailing',
      'activity_date_time' => date('YmdHis'),
      'is_test' => 0,
      'status_id' => 2,
      'priority_id' => 2,
      'details' => '',
      'source_contact_id' => $contactID,
      'target_contact_id' => $contactID,
    );
    CRM_Activity_BAO_Activity::create($p);

    $this->addElement('hidden', 'optoutstatus', $optOutMessage);

    parent::postProcess();
  }

}