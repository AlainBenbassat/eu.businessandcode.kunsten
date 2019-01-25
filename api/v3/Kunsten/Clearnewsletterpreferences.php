<?php
use CRM_Kunsten_ExtensionUtil as E;

function _civicrm_api3_kunsten_Clearnewsletterpreferences_spec(&$spec) {
}

function civicrm_api3_kunsten_Clearnewsletterpreferences($params) {
  // remove all newsletter preferences from contacts who opted out
  // FYI: solving this with 1 update statement conflicts with triggers
  $sql = "
    SELECT
      id
    FROM
      civicrm_contact
    WHERE
      is_opt_out = 1
  ";
  $dao = CRM_Core_DAO::executeQuery($sql);

  while ($dao->fetch()) {
    $sqlUpdate = "
      UPDATE
        civicrm_value_kunstenpunt_communicatie
      SET
        kunstenpunt_nieuws = ''
        , flanders_arts_institute_news = ''
        , initiatieven_themas = ''
      WHERE
        (kunstenpunt_nieuws <> '' or flanders_arts_institute_news <> '' or initiatieven_themas <> '')
      AND 
        entity_id = %1
    ";
    $sqlUpdateParams = [
      1 => [$dao->id, 'Integer'],
    ];
    CRM_Core_DAO::executeQuery($sqlUpdate, $sqlUpdateParams);
  }

  return civicrm_api3_create_success('OK', $params, 'Kunsten', 'Clearnewsletterpreferences');
}
