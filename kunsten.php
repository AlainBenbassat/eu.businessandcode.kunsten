<?php

require_once 'kunsten.civix.php';
use CRM_Kunsten_ExtensionUtil as E;


function kunsten_civicrm_tokens(&$tokens) {
  $tokens['contact'] = array(
    'contact.contactlink' => 'Link naar de pagina op kunsten.be waar het contact zijn gegevens kan aanpassen.',
  );
}

function kunsten_civicrm_tokenValues(&$values, $cids, $job = null, $tokens = array(), $context = null) {
  if (array_key_exists('contact', $tokens) && array_key_exists('contactlink', $tokens['contact'])) {
    foreach ($cids as $cid) {
      $sql = "
        SELECT
          id
          , hash
        FROM
          civicrm_contact c
        WHERE
          c.id = %1
      ";
      $sqlParams = array(
        1 => array($cid, 'Integer'),
      );
      $dao = CRM_Core_DAO::executeQuery($sql, $sqlParams);
      $dao->fetch();

      $values[$cid]['contact.contactlink'] = "{$dao->nick_name}";
    }
  }
}


/**
 * Implements hook_civicrm_config().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
 */
function kunsten_civicrm_config(&$config) {
  _kunsten_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_xmlMenu().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_xmlMenu
 */
function kunsten_civicrm_xmlMenu(&$files) {
  _kunsten_civix_civicrm_xmlMenu($files);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function kunsten_civicrm_install() {
  _kunsten_load_config_items();
  _kunsten_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_postInstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_postInstall
 */
function kunsten_civicrm_postInstall() {
  _kunsten_civix_civicrm_postInstall();
}

/**
 * Implements hook_civicrm_uninstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_uninstall
 */
function kunsten_civicrm_uninstall() {
  _kunsten_civix_civicrm_uninstall();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function kunsten_civicrm_enable() {
  _kunsten_load_config_items();
  _kunsten_civix_civicrm_enable();
}

/**
 * Implements hook_civicrm_disable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_disable
 */
function kunsten_civicrm_disable() {
  _kunsten_civix_civicrm_disable();
}

/**
 * Implements hook_civicrm_upgrade().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_upgrade
 */
function kunsten_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _kunsten_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implements hook_civicrm_managed().
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_managed
 */
function kunsten_civicrm_managed(&$entities) {
  _kunsten_load_config_items();
  _kunsten_civix_civicrm_managed($entities);
}

function _kunsten_load_config_items() {
  $path = realpath(__DIR__);
  return civicrm_api3('Civiconfig', 'load_json', array(
    'path' => $path . '/resources/',
  ));
}

/**
 * Implements hook_civicrm_caseTypes().
 *
 * Generate a list of case-types.
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function kunsten_civicrm_caseTypes(&$caseTypes) {
  _kunsten_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implements hook_civicrm_angularModules().
 *
 * Generate a list of Angular modules.
 *
 * Note: This hook only runs in CiviCRM 4.5+. It may
 * use features only available in v4.6+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_angularModules
 */
function kunsten_civicrm_angularModules(&$angularModules) {
  _kunsten_civix_civicrm_angularModules($angularModules);
}

/**
 * Implements hook_civicrm_alterSettingsFolders().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_alterSettingsFolders
 */
function kunsten_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _kunsten_civix_civicrm_alterSettingsFolders($metaDataFolders);
}

// --- Functions below this ship commented out. Uncomment as required. ---

/**
 * Implements hook_civicrm_preProcess().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_preProcess
 *
function kunsten_civicrm_preProcess($formName, &$form) {

} // */

/**
 * Implements hook_civicrm_navigationMenu().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_navigationMenu
 *
function kunsten_civicrm_navigationMenu(&$menu) {
  _kunsten_civix_insert_navigation_menu($menu, NULL, array(
    'label' => E::ts('The Page'),
    'name' => 'the_page',
    'url' => 'civicrm/the-page',
    'permission' => 'access CiviReport,access CiviContribute',
    'operator' => 'OR',
    'separator' => 0,
  ));
  _kunsten_civix_navigationMenu($menu);
} // */
