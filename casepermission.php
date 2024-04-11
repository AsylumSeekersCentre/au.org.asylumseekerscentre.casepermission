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
 * Implements hook_civicrm_install().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function casepermission_civicrm_install() {
  _casepermission_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function casepermission_civicrm_enable() {
  _casepermission_civix_civicrm_enable();
}

function casepermission_civicrm_permission(&$permissions) {
    $caseTypes = civicrm_api3('CaseType', 'get', array('sequential' => 0, 'options' => array('limit' => 0),));
    //$actions = array('add', 'view', 'edit', 'delete');
    $actions = array('access');
    $prefix = ts('CiviCase') . ': ';
    foreach ($caseTypes['values'] as $caseTypeObject) {
        $caseType = $caseTypeObject['name'];
        foreach ($actions as $action) {
            $permissions[$action . ' cases of type ' . $caseType] = [
                'label' => $prefix . ts($action . ' cases of type ') . $caseType,
                'description' => ts(ucfirst($action) . ' cases of type ' ) . $caseType,
            ];
        }
    }
    $permissions['administer CiviCRM Case Type Permissions'] = [
        'label' => $prefix . ts('administer CiviCRM Case Type Permissions'),
        'description' => ts('Administer access to CiviCRM Case Type Permissions'),
    ];
    $permissions['access Food Bank menu items'] = [
        'label' => 'CiviCRM: ' . ts('access Food Bank menu items'),
        'description' => ts('access Food Bank menu items'),
    ];
}

function casepermission_civicrm_selectWhereClause($entity, &$clauses) {
  if (strtolower($entity) == "case") {
    $caseTypes = civicrm_api3('CaseType', 'get', array('sequential' => 0, 'options' => array('limit' => 0),));
    $extra = array();
    foreach($caseTypes['values'] as $caseTypeId => $caseType) {
      $caseTypeName = $caseType['name'];
      $permission = 'access cases of type '.$caseTypeName;
      $access = CRM_Core_Permission::check($permission);
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

 // */

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
