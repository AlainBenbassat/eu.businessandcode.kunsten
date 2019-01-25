<?php
use CRM_Kunsten_ExtensionUtil as E;

function _civicrm_api3_kunsten_Clearnewsletterpreferences_spec(&$spec) {
}

function civicrm_api3_kunsten_Clearnewsletterpreferences($params) {
  // remove all newsletter preferences from contacts who opted out
  $sql = "
    UPDATE
      civicrm_value_kunstenpunt_communicatie
    SET
      kunstenpunt_nieuws = ''
      , flanders_arts_institute_news = ''
      , initiatieven_themas = ''
    WHERE
      (kunstenpunt_nieuws <> '' or flanders_arts_institute_news <> '' or initiatieven_themas <> '')
    AND 
      entity_id in (select id from civicrm_contact where is_opt_out = 1)
  ";
  CRM_Core_DAO::executeQuery($sql);

  return civicrm_api3_create_success('OK', $params, 'Kunsten', 'Clearnewsletterpreferences');
}
