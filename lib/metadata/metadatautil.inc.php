<?php
/**
 * $Id$
 *
 * Utility functions for retrieving information from and managing
 * fieldsets with conditions.
 *
 * Copyright (c) 2005 Jam Warehouse http://www.jamwarehouse.com
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * @version $Revision$
 * @author Neil Blakey-Milner, Jam Warehouse (Pty) Ltd, South Africa
 */

require_once(KT_LIB_DIR . "/ktentity.inc");
require_once(KT_LIB_DIR . '/documentmanagement/MetaData.inc');
require_once(KT_LIB_DIR . '/metadata/valueinstance.inc.php');
require_once(KT_LIB_DIR . '/metadata/fieldset.inc.php');
require_once(KT_LIB_DIR . '/metadata/fieldbehaviour.inc.php');

class KTMetadataUtil {

    function _getNextForBehaviour($oBehaviour, $aCurrentSelections) {
        $oBehaviour =& KTUtil::getObject('KTFieldBehaviour', $oBehaviour);
        $GLOBALS['default']->log->debug('KTMetadataUtil::_getNextForBehaviour, behaviour is ' . $oBehaviour->getId());

        $aValues = KTMetadataUtil::getNextValuesForBehaviour($oBehaviour);
        $iFieldId = $oBehaviour->getFieldId();
        $aNextFields = KTMetadataUtil::getChildFieldIds($iFieldId);
        $GLOBALS['default']->log->debug('KTMetadataUtil::_getNextForBehaviour, next fields for ' . $iFieldId . ' are: ' . print_r($aNextFields, true)); 

        foreach ($aNextFields as $iThisFieldId) {
            if (!in_array($iThisFieldId, array_keys($aCurrentSelections))) {
                $GLOBALS['default']->log->debug('KTMetadataUtil::_getNextForBehaviour, field ' . $iThisFieldId . ' is not selected');

            } else {
                $GLOBALS['default']->log->debug('KTMetadataUtil::_getNextForBehaviour, field ' . $iThisFieldId . ' is selected');
                unset($aValues[$iThisFieldId]);

                $oInstance = KTValueInstance::getByLookupAndParentBehaviour($aCurrentSelections[$iThisFieldId], $oBehaviour);
                $GLOBALS['default']->log->debug('KTMetadataUtil::_getNextForBehaviour, instance is ' . print_r($oInstance, true));
                $oChildBehaviour =& KTFieldBehaviour::get($oInstance->getBehaviourId());
                $GLOBALS['default']->log->debug('KTMetadataUtil::_getNextForBehaviour, field ' . $iThisFieldId . ' is not selected');
                $aMyValues = KTMetadataUtil::_getNextForBehaviour($oChildBehaviour, $aCurrentSelections);
                $GLOBALS['default']->log->debug('KTMetadataUtil::_getNextForBehaviour, values are ' . print_r($aMyValues, true));
                foreach ($aMyValues as $k => $v) {
                    $aValues[$k] = $v;
                }
            }
        }

        $GLOBALS['default']->log->debug('KTMetadataUtil::_getNextForBehaviour, final values are ' . print_r($aValues, true));
        return $aValues;
    }
    
    // {{{ getNext
    /**
     * Given a set of selected values (aCurrentSelections) for a given
     * field set (iFieldSet), returns an array (possibly empty) with the
     * keys set to newly uncovered fields, and the contents an array of
     * the value instances that are available to choose in those fields.
     */
    function getNext($oFieldset, $aCurrentSelections) {
        $oFieldset =& KTUtil::getObject('KTFieldset', $oFieldset);
        $GLOBALS['default']->log->debug('KTMetadataUtil::getNext, selections are: ' . print_r($aCurrentSelections, true));

        if (empty($aCurrentSelections)) {
            $oField =& DocumentField::get($oFieldset->getMasterFieldId());
            return array($oField->getId() => array('field' => $oField, 'values' => $oField->getValues()));
        }

        $oMasterField =& DocumentField::get($oFieldset->getMasterFieldId());
        $aSelectedFields = array_keys($aCurrentSelections);
        $oValueInstance = KTValueInstance::getByLookupSingle($aCurrentSelections[$oMasterField->getId()]);

        $aValues = KTMetadataUtil::_getNextForBehaviour($oValueInstance->getBehaviourId(), $aCurrentSelections);
        $GLOBALS['default']->log->debug('KTMetadataUtil::getNext, values are ' . print_r($aValues, true));
        $aReturn = array();
        foreach ($aValues as $iFieldId => $aValueIds) {
            $aValues = array();
            foreach ($aValueIds as $iLookupId) {
                $aValues[$iLookupId] = MetaData::get($iLookupId);
            }
            $aReturn[$iFieldId] = array(
                'field' => DocumentField::get($iFieldId),
                'values' => $aValues,
            );
        }
        return $aReturn;
    }
    // }}}

    // {{{ getStartFields
    function getMasterField($oFieldset) {
        $oFieldset =& KTUtil::getObject('KTFieldset', $oFieldset);
        if ($oFieldset->getMasterField()) {
            return DocumentField::get($oFieldset->getMasterField());
        }
    }
    // }}}

    // {{{ removeSetsFromDocumentType
    function removeSetsFromDocumentType($oDocumentType, $aFieldsets) {
        if (is_object($oDocumentType)) {
            $iDocumentTypeId = $oDocumentType->getId();
        } else {
            $iDocumentTypeId = $oDocumentType;
        }
        if (!is_array($aFieldsets)) {
            $aFieldsets = array($aFieldsets);
        }
        if (empty($aFieldsets)) {
            return true;
        }
        $aIds = array();
        foreach ($aFieldsets as $oFieldset) {
            if (is_object($oFieldset)) {
                $iFieldsetId = $oFieldset->getId();
            } else {
                $iFieldsetId = $oFieldset;
            }
            $aIds[] = $iFieldsetId;
        }
        // Converts to (?, ?, ?) for query
        $sParam = DBUtil::paramArray($aIds);
        $aWhere = KTUtil::whereToString(array(
            array('document_type_id = ?', array($iDocumentTypeId)),
            array("fieldset_id IN ($sParam)", $aIds),
        ));
        $sTable = KTUtil::getTableName('document_type_fieldsets');
        $aQuery = array(
            "DELETE FROM $sTable WHERE {$aWhere[0]}",
            $aWhere[1],
        );
        return DBUtil::runQuery($aQuery);
    }
    // }}}

    // {{{ addSetsToDocumentType
    function addSetsToDocumentType($oDocumentType, $aFieldsets) {
        if (is_object($oDocumentType)) {
            $iDocumentTypeId = $oDocumentType->getId();
        } else {
            $iDocumentTypeId = $oDocumentType;
        }
        if (!is_array($aFieldsets)) {
            $aFieldsets = array($aFieldsets);
        }
        $aIds = array();
        foreach ($aFieldsets as $oFieldset) {
            if (is_object($oFieldset)) {
                $iFieldsetId = $oFieldset->getId();
            } else {
                $iFieldsetId = $oFieldset;
            }
            $aIds[] = $iFieldsetId;
        }

        $sTable = KTUtil::getTableName('document_type_fieldsets');
        foreach ($aIds as $iId) {
            $res = DBUtil::autoInsert($sTable, array(
                'document_type_id' => $iDocumentTypeId,
                'fieldset_id' => $iId,
            ));
            if (PEAR::isError($res)) {
                return $res;
            }
        }
        return true;
    }
    // }}}

    // {{{ addFieldOrder
    function addFieldOrder($oParentField, $oChildField, $oFieldset) {
        $iParentFieldId = KTUtil::getId($oParentField);
        $iChildFieldId = KTUtil::getId($oChildField);
        $iFieldsetId = KTUtil::getId($oFieldset);

        $aOptions = array('noid' => true);
        $sTable = KTUtil::getTableName('field_orders');
        $aValues = array(
            'parent_field_id' => $iParentFieldId,
            'child_field_id' => $iChildFieldId,
            'fieldset_id' => $iFieldsetId,
        );
        return DBUtil::autoInsert($sTable, $aValues, $aOptions);
    }
    // }}}

    // {{{ removeFieldOrdering
    function removeFieldOrdering($oFieldset) {
        $iFieldsetId = KTUtil::getId($oFieldset);
        $sTable = KTUtil::getTableName('field_orders');
        $aQuery = array(
            "DELETE FROM $sTable WHERE fieldset_id = ?",
            array($iFieldsetId),
        );
        return DBUtil::runQuery($aQuery);
    }
    // }}}

    // {{{ getParentFieldId
    function getParentFieldId($oField) {
        $sTable = KTUtil::getTableName('field_orders');
        $aQuery = array("SELECT parent_field_id FROM $sTable WHERE child_field_id = ?",
            array($oField->getId()),
        );
        return DBUtil::getOneResultKey($aQuery, 'parent_field_id');
    }
    // }}}

    // {{{ getChildFieldIds
    function getChildFieldIds($oField) {
        $iFieldId = KTUtil::getId($oField);
        $sTable = KTUtil::getTableName('field_orders');
        $aQuery = array("SELECT child_field_id FROM $sTable WHERE parent_field_id = ?",
            array($iFieldId),
        );
        return DBUtil::getResultArrayKey($aQuery, 'child_field_id');
    }
    // }}}

    function &getOrCreateValueInstanceForLookup(&$oLookup) {
        $oLookup =& KTUtil::getObject('MetaData', $oLookup);
        $oValueInstance =& KTValueInstance::getByLookupSingle($oLookup);
        if (PEAR::isError($oValueInstance)) {
            return $oValueInstance;
        }
        // If we got a value instance, return it.
        if (!is_null($oValueInstance)) {
            return $oValueInstance;
        }
        return KTValueInstance::createFromArray(array(
            'fieldid' => $oLookup->getDocFieldId(),
            'fieldvalueid' => $oLookup->getId(),
        ));
    }

    function getNextValuesForLookup($oLookup) {
        $oLookup =& KTUtil::getObject('MetaData', $oLookup);
        $oInstance =& KTValueInstance::getByLookupSingle($oLookup);
        if (PEAR::isError($oInstance)) {
            $GLOBALS['default']->log->error('KTMetadataUtil::getNextValuesForLookup, got dud instance id, returned: ' . print_r($oInstance, true));
            return $oInstance;
        }
        if (!is_null($oInstance) && $oInstance->getBehaviourId()) {
            // if we have an instance, and we have a behaviour, return
            // the actual values for that behaviour.
            $oBehaviour =& KTFieldBehaviour::get($oInstance->getBehaviourId());
            if (PEAR::isError($oBehaviour)) {
                $GLOBALS['default']->log->error('KTMetadataUtil::getNextValuesForLookup, got dud behaviour id, returned: ' . print_r($oBehaviour, true));
                return $res;
            }
            return KTMetadataUtil::getNextValuesForBehaviour($oBehaviour);
        }
        // No instance or no behaviour, so send an empty array for each
        // field that we affect.
        $aChildFieldIds = KTMetadataUtil::getChildFieldIds($oLookup->getDocFieldId());
        if (PEAR::isError($aChildFieldIds)) {
            $GLOBALS['default']->log->error('KTMetadataUtil::getNextValuesForLookup, getChildFieldIds returned: ' . print_r($aChildFieldIds, true));
            return $res;
        }
        foreach ($aChildFieldIds as $iFieldId) {
            $aValues[$iFieldId] = array();
        }
        return $aValues;
    }

    function getNextValuesForBehaviour($oBehaviour) {
        $oBehaviour =& KTUtil::getObject('KTFieldBehaviour', $oBehaviour);
        $aValues = array();
        $sTable = KTUtil::getTableName('field_behaviour_options');
        $aChildFieldIds = KTMetadataUtil::getChildFieldIds($oBehaviour->getFieldId());
        foreach ($aChildFieldIds as $iFieldId) {
            $aValues[$iFieldId] = array();
        }
        $aQuery = array(
            "SELECT field_id, instance_id FROM $sTable WHERE behaviour_id = ?",
            array($oBehaviour->getId()),
        );
        $aRows = DBUtil::getResultArray($aQuery);
        if (PEAR::isError($aRows)) {
            return $aRows;
        }
        foreach ($aRows as $aRow) {
            $oInstance =& KTValueInstance::get($aRow['instance_id']);
            $aValues[$aRow['field_id']][] = $oInstance->getFieldValueId();
        }
        return $aValues;
    }
}

?>
