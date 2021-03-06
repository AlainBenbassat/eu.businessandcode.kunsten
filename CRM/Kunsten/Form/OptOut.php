<?php

// optout url:
// https://crm.kunsten.be/civicrm/?page=CiviCRM&q=civicrm/optout&a=SOMETHING&b=ANOTHER-THING&lang=nl

use CRM_Kunsten_ExtensionUtil as E;

class CRM_Kunsten_Form_OptOut extends CRM_Core_Form {

  public function buildQuickForm() {
    // add css to hide some stuff
    CRM_Core_Resources::singleton()->addStyle('#sidebar, #printer-friendly, div.breadcrumb, div.site-info { display: none; } body { background-color: #ffffff; } ');

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
      $logo = 'https://www.kunsten.be/wp-content/themes/kunstenpunt/assets/feec82ad707125cbda9bcdf8c094efe9b740ec92/images/kunstenpunt/logo.svg';
      $title = 'Uitschrijven van alle toekomstige mails van Kunstenpunt?';
      $submitText = 'Bevestigen';

      if ($errorMessage) {
        $introText = $errorMessage;
      }
      else {
        $introText = '<p>Klik op ‘Bevestigen’ en je ontvangt geen enkele mail meer op<br><strong>'
          . $contact['email']
          . '</strong>, tenzij je je opnieuw inschrijft<br>via <a href="https://www.kunsten.be/">kunsten.be</a>.</p>';
      }
    }
    else {
      $logo = 'https://www.flandersartsinstitute.be/wp-content/themes/kunstenpunt/assets/feec82ad707125cbda9bcdf8c094efe9b740ec92/images/flandersartsinstitute/logo.svg';
      $title = 'Unsubscribe from all future mails of Flanders Arts Institute?';
      $submitText = 'Confirm';

      if ($errorMessage) {
        $introText = $errorMessage;
      }
      else {
        $introText = '<p>Click ‘Confirm’ to stop receiving mails at <strong>'
          . $contact['email']
          . '</strong>, unless you re-subscribe via <a href="https://www.flandersartsinstitute.be">flandersartsinstitute.be</a></p>';
      }
    }

    // set the title
    CRM_Utils_System::setTitle($title);

    // set the texts and logo
    $this->assign('introText', $introText);
    $this->assign('logo', $logo);

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
      $this->addElement('hidden', 'lang', $language);
    }

    parent::buildQuickForm();
  }

  public function postProcess() {
    // get the contact id
    $contactID = CRM_Utils_Array::value('id', $_POST, 0);
    $language = CRM_Utils_Array::value('lang', $_POST, 'nl');

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

    // redirect to website
    if ($language == 'en') {
      $url = 'https://www.flandersartsinstitute.be';
    }
    else {
      $url = 'https://www.kunsten.be';
    }
    CRM_Utils_System::redirect($url);

    parent::postProcess();
  }

}
