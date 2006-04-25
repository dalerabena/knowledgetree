<?php

/**
 * $Id$
 *
 * Copyright (c) 2006 Jam Warehouse http://www.jamwarehouse.com
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; using version 2 of the License.
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
 * -------------------------------------------------------------------------
 *
 * You can contact the copyright owner regarding licensing via the contact
 * details that can be found on the KnowledgeTree web site:
 *
 *         http://www.ktdms.com/
 */

require_once(KT_LIB_DIR . '/unitmanagement/Unit.inc');

require_once(KT_LIB_DIR . "/templating/templating.inc.php");
require_once(KT_LIB_DIR . "/dispatcher.inc.php");
require_once(KT_LIB_DIR . "/templating/kt3template.inc.php");
require_once(KT_LIB_DIR . "/widgets/fieldWidgets.php");

require_once(KT_LIB_DIR . "/browse/DocumentCollection.inc.php");
require_once(KT_LIB_DIR . "/browse/BrowseColumns.inc.php");
require_once(KT_LIB_DIR . "/browse/PartialQuery.inc.php");

require_once(KT_LIB_DIR . "/foldermanagement/folderutil.inc.php");
require_once(KT_LIB_DIR . "/permissions/permissionutil.inc.php");

class KTUnitAdminDispatcher extends KTAdminDispatcher {
    var $sHelpPage = 'ktcore/admin/control units.html';
    var $bAutomaticTransaction = true;

    function check() {
        $this->aBreadcrumbs[] = array('url' => $_SERVER['PHP_SELF'], 'name' => _kt('Unit Management'));
        return parent::check();
    }

    function do_main() {
		$this->oPage->setBreadcrumbDetails(_kt('select a unit'));
		$this->oPage->setTitle(_kt("Unit Management"));

		$unit_list =& Unit::getList();
		
		$oTemplating =& KTTemplating::getSingleton();        
		$oTemplate = $oTemplating->loadTemplate("ktcore/principals/unitadmin");
		$aTemplateData = array(
			"context" => $this,
			"unit_list" => $unit_list,
		);
 		return $oTemplate->render($aTemplateData);
    }

    function do_addUnit() {
        $this->oPage->setBreadcrumbDetails(_kt('Add a new unit'));
        $this->oPage->setTitle(_kt("Add a new unit"));

        $add_fields = array();
        $add_fields[] =  new KTStringWidget(_kt('Unit Name'), _kt('A short name for the unit.  e.g. <strong>Accounting</strong>.'), 'unit_name', null, $this->oPage, true);

        $oTemplating =& KTTemplating::getSingleton();
        $oTemplate = $oTemplating->loadTemplate("ktcore/principals/addunit");
        $aTemplateData = array(
            "context" => $this,
            "add_fields" => $add_fields,
        );
        return $oTemplate->render($aTemplateData);
    }

    function do_addUnit2() {
        $this->oPage->setBreadcrumbDetails(_kt('Add a new unit'));
        $this->oPage->setTitle(_kt("Add a new unit"));

        $aOptions = array(
            'redirect_to' => array('addUnit'),
            'message' => _kt('No name given'),
        );
        $sName = $this->oValidator->validateString($_REQUEST['unit_name'], $aOptions);
		$aOptions['message'] = _kt('A unit with that name already exists.');
		$sName = $this->oValidator->validateDuplicateName('Unit', $sName, $aOptions);

        $iFolderId = KTUtil::arrayGet($_REQUEST, 'fFolderId', 1);
        $_REQUEST['fFolderId'] = $iFolderId;
        $oFolder = $this->oValidator->validateFolder($_REQUEST['fFolderId']);

        $collection = new DocumentCollection();
        $collection->addColumn(new KTUnitTitleColumn($sName));
        $qObj = new FolderBrowseQuery($oFolder->getId());
        $collection->setQueryObject($qObj);
        $batchPage = (int) KTUtil::arrayGet($_REQUEST, "page", 0);
        $batchSize = 20;

        $resultURL = KTUtil::addQueryString($_SERVER['PHP_SELF'], sprintf("action=addUnit2&unit_name=%s&fFolderId=%d", $sName, $oFolder->getId()));
        $collection->setBatching($resultURL, $batchPage, $batchSize);

        // ordering. (direction and column)
        $displayOrder = KTUtil::arrayGet($_REQUEST, 'sort_order', "asc");
        if ($displayOrder !== "asc") { $displayOrder = "desc"; }
        $displayControl = KTUtil::arrayGet($_REQUEST, 'sort_on', "title");

        $collection->setSorting($displayControl, $displayOrder);

        $collection->getResults();

        $aBreadcrumbs = array();
        $folder_path_names = $oFolder->getPathArray();
        $folder_path_ids = explode(',', $oFolder->getParentFolderIds());
        $folder_path_ids[] = $oFolder->getId();
        if ($folder_path_ids[0] == 0) {
            array_shift($folder_path_ids);
            array_shift($folder_path_names);
        }

        foreach (range(0, count($folder_path_ids) - 1) as $index) {
            $id = $folder_path_ids[$index];
            $url = KTUtil::addQueryString($_SERVER['PHP_SELF'], sprintf("action=addUnit2&unit_name=%s&fFolderId=%d", $sName, $id));
            $aBreadcrumbs[] = array("url" => $url, "name" => $folder_path_names[$index]);
        }

        $add_fields = array();
        $add_fields[] =  new KTStaticTextWidget(_kt('Unit Name'), _kt('A short name for the unit.  e.g. <strong>Accounting</strong>.'), 'unit_name', $sName, $this->oPage, true);

		$isValid = true;
		if (KTFolderUtil::exists($oFolder, $sName)) {
			$isValid = false; // can't add a unit folder with the same name.
		}

        $oTemplating =& KTTemplating::getSingleton();
        $oTemplate = $oTemplating->loadTemplate("ktcore/principals/addunit2");
        $aTemplateData = array(
            "context" => $this,
            "add_fields" => $add_fields,
            "collection" => $collection,
            "collection_breadcrumbs" => $aBreadcrumbs,
            "folder" => $oFolder,
            "name" => $sName,
			"is_valid" => $isValid,
        );
        return $oTemplate->render($aTemplateData);
    }

    function do_createUnit() {
        $aOptions = array(
            'redirect_to' => array('main'),
            'message' => _kt('Invalid folder chosen'),
        );
        $oParentFolder = $this->oValidator->validateFolder($_REQUEST['fFolderId'], $aOptions);
        $aOptions = array(
            'redirect_to' => array('addUnit', sprintf('fFolderId=%d', $oParentFolder->getId())),
            'message' => _kt('No name given'),
        );
        $sName = $this->oValidator->validateString($_REQUEST['unit_name'], $aOptions);
		$aOptions['message'] = _kt('A unit with that name already exists.');
		$sName = $this->oValidator->validateDuplicateName('Unit', $sName, $aOptions);

        $oFolder = KTFolderUtil::add($oParentFolder, $sName, $this->oUser);
        $aOptions = array(
            'redirect_to' => array('addUnit2', sprintf('fFolderId=%d&unit_name=%s', $oParentFolder->getId(), $sName)),
            'defaultmessage' => 'Error creating folder',
        );
        $this->oValidator->notError($oFolder, $aOptions);

        KTPermissionUtil::copyPermissionObject($oFolder);
        
        $oUnit = Unit::createFromArray(array(
            'name' => $sName,
            'folderid' => $oFolder->getId(),
        ));
        return $this->successRedirectToMain(_kt('Unit created'));
    }

    function do_editUnit() {
        $oUnit =& $this->oValidator->validateUnit($_REQUEST['unit_id']); 

        $fields = array();
        $fields[] =  new KTStringWidget(_kt('Unit Name'), _kt('A short name for the unit.  e.g. <strong>Accounting</strong>.'), 'unit_name', $oUnit->getName(), $this->oPage, true);

        $oTemplate =& $this->oValidator->validateTemplate('ktcore/principals/editunit');
        $aTemplateData = array(
            "context" => $this,
            "edit_unit" => $oUnit,
            "edit_fields" => $fields,
        );
        return $oTemplate->render($aTemplateData);
    }

    function do_saveUnit() {
        $oUnit =& $this->oValidator->validateUnit($_REQUEST['unit_id']); 
        $aOptions = array(
            'redirect_to' => array('editUnit', sprintf('unit_id=%d', $oUnit->getId())),
            'message' => _kt('No name given'),
        );
        $sName = $this->oValidator->validateString($_REQUEST['unit_name'], $aOptions);
		$aOptions['message'] = _kt('A unit with that name already exists.');
		$aOptions['rename'] = $oUnit->getId();
		$sName = $this->oValidator->validateDuplicateName('Unit', $sName, $aOptions);
        $oUnit->setName($sName);
        $res = $oUnit->update();
        if (($res == false) || (PEAR::isError($res))) {
            return $this->errorRedirectToMain(_kt('Failed to set unit details.'));
        }
        $iFolderId = $oUnit->getFolderId();
        $oFolder = Folder::get($iFolderId);
        if (!PEAR::isError($oFolder) && ($oFolder !== false)) {
            KTFolderUtil::rename($oFolder, $sName, $this->oUser);
        }

        $this->successRedirectToMain(_kt("Unit details updated"));
    }

    function do_deleteUnit() {
        $oUnit =& $this->oValidator->validateUnit($_REQUEST['unit_id']); 

        $fields = array();
        $fields[] = new KTCheckboxWidget(_kt('Delete folder'), _kt('Each unit has an associated folder.  While the unit is being deleted, there may be some documents within the associated folder.  By unselecting this option, they will not be removed.'), 'delete_folder', true, $this->oPage, true);

        $oTemplate =& $this->oValidator->validateTemplate('ktcore/principals/deleteunit');
        $aTemplateData = array(
            "context" => $this,
            "unit" => $oUnit,
            "fields" => $fields,
        );
        return $oTemplate->render($aTemplateData);
    }

    function do_removeUnit() {
        $oUnit =& $this->oValidator->validateUnit($_REQUEST['unit_id']); 
        $bDeleteFolder = KTUtil::arrayGet($_REQUEST, 'delete_folder', false);
        $res = $oUnit->delete();
        $aOptions = array(
            'redirect_to' => array('main'),
            'message' => _kt("Could not delete this unit because it has groups assigned to it"),
            'no_exception' => true,
        );
        $this->oValidator->notError($res, $aOptions);
        if ($bDeleteFolder) {
            $iFolderId = $oUnit->getFolderId();
            $oFolder = Folder::get($iFolderId);
            if (!PEAR::isError($oFolder) && ($oFolder !== false)) {
                $aOptions = array(
                    'ignore_permissions' => true,
                );
                KTFolderUtil::delete($oFolder, $this->oUser, "Unit deleted", $aOptions);
            }
        }
        $this->successRedirectToMain(_kt("Unit removed"));
    }
}

class KTUnitTitleColumn extends TitleColumn {
    function KTUnitTitleColumn($sName) {
        $this->sName = $sName;
        parent::TitleColumn("Unit", "title");
    }
    function buildFolderLink($aDataRow) {
        return KTUtil::addQueryString($_SERVER['PHP_SELF'], 
            sprintf('action=addUnit2&unit_name=%s&fFolderId=%d', $this->sName, $aDataRow['folder']->getId()));
    }
}


?>
