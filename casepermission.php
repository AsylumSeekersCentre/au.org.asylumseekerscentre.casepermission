<?php

require_once 'casepermission.civix.php';
use CRM_Casepermission_ExtensionUtil as E;

/**
 * Implements hook_civicrm_config().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
 */
function casepermission_civicrm_config(&$config) {
  _casepermission_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_xmlMenu().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_xmlMenu
 */
function casepermission_civicrm_xmlMenu(&$files) {
  _casepermission_civix_civicrm_xmlMenu($files);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function casepermission_civicrm_install() {
  _casepermission_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_postInstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_postInstall
 */
function casepermission_civicrm_postInstall() {
  _casepermission_civix_civicrm_postInstall();
}

/**
 * Implements hook_civicrm_uninstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_uninstall
 */
function casepermission_civicrm_uninstall() {
  _casepermission_civix_civicrm_uninstall();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function casepermission_civicrm_enable() {
  _casepermission_civix_civicrm_enable();
}

/**
 * Implements hook_civicrm_disable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_disable
 */
function casepermission_civicrm_disable() {
  _casepermission_civix_civicrm_disable();
}

/**
 * Implements hook_civicrm_upgrade().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_upgrade
 */
function casepermission_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _casepermission_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implements hook_civicrm_managed().
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_managed
 */
function casepermission_civicrm_managed(&$entities) {
  _casepermission_civix_civicrm_managed($entities);
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
function casepermission_civicrm_caseTypes(&$caseTypes) {
  _casepermission_civix_civicrm_caseTypes($caseTypes);
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
function casepermission_civicrm_angularModules(&$angularModules) {
  _casepermission_civix_civicrm_angularModules($angularModules);
}

/**
 * Implements hook_civicrm_alterSettingsFolders().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_alterSettingsFolders
 */
function casepermission_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _casepermission_civix_civicrm_alterSettingsFolders($metaDataFolders);
}

function casepermission_civicrm_permission(&$permissions) {
    $caseTypes = civicrm_api3('CaseType', 'get', array('sequential' => 0,));
    //$actions = array('add', 'view', 'edit', 'delete');
    $actions = array('access');
    $prefix = ts('CiviCase') . ': ';
    foreach ($caseTypes['values'] as $caseTypeObject) {
        $caseType = $caseTypeObject['name'];
        foreach ($actions as $action) {
            $permissions[$action . ' cases of type ' . $caseType] = array(
                $prefix . ts($action . ' cases of type ') . $caseType,
                ts(ucfirst($action) . ' cases of type ' ) . $caseType,
            );
        }
    }
    $permissions['administer CiviCRM Case Type Permissions'] = array(
        $prefix . ts('administer CiviCRM Case Type Permissions'),
        ts('Administer access to CiviCRM Case Type Permissions'),
    );
    $permissions['access Food Bank menu items'] = array(
        'CiviCRM: ' . ts('access Food Bank menu items'),
        ts('access Food Bank menu items'),
    );
}

function casepermission_civicrm_selectWhereClause($entity, &$clauses) {
  if (strtolower($entity) == "case") {
    $caseTypes = civicrm_api3('CaseType', 'get', array('sequential' => 0,));
    $extra = array();
    foreach($caseTypes['values'] as $caseTypeId => $caseType) {
      $caseTypeName = $caseType['name'];
      $permission = 'access cases of type '.$caseTypeName;
      $access = user_access($permission);
      if ($access) {
        $extra[] = (int)$caseType['id'];
      }
    }
    if(count($extra) > 0) {
      $clauses['case_type_id'][] = 'IN ('.trim(json_encode($extra),'[]').')';
    }
    else {
      $clauses['case_type_id'][] = '= -999999999';
    }
  }
}

// --- Functions below this ship commented out. Uncomment as required. ---

/**
 * Implements hook_civicrm_preProcess().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_preProcess
 *
function casepermission_civicrm_preProcess($formName, &$form) {

} // */

/**
 * Implements hook_civicrm_navigationMenu().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_navigationMenu
 *
function casepermission_civicrm_navigationMenu(&$menu) {
  _casepermission_civix_insert_navigation_menu($menu, NULL, array(
    'label' => E::ts('The Page'),
    'name' => 'the_page',
    'url' => 'civicrm/the-page',
    'permission' => 'access CiviReport,access CiviContribute',
    'operator' => 'OR',
    'separator' => 0,
  ));
  _casepermission_civix_navigationMenu($menu);
} // */
