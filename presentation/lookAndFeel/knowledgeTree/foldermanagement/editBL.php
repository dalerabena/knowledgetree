<?php
/**
 * $Id$
 *
 * Business logic used to edit folder properties
 *
 * Expected form variables:
 * o $fFolderID - primary key of folder user is currently browsing
 *
 * Copyright (c) 2003 Jam Warehouse http://www.jamwarehouse.com
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
 * @author Rob Cherry, Jam Warehouse (Pty) Ltd, South Africa
 * @package foldermanagement
 */
 
require_once("../../../../config/dmsDefaults.php");

KTUtil::extractGPC('fCollaborationDelete', 'fCollaborationEdit', 'fFolderID', 'fShowSection');

if (checkSession()) {
    require_once("$default->fileSystemRoot/lib/visualpatterns/PatternListBox.inc");
    require_once("$default->fileSystemRoot/lib/visualpatterns/PatternEditableListFromQuery.inc");
    require_once("$default->fileSystemRoot/lib/visualpatterns/PatternListFromQuery.inc");
    require_once("editUI.inc");
    require_once("$default->fileSystemRoot/lib/security/Permission.inc");
    require_once("$default->fileSystemRoot/lib/visualpatterns/PatternCustom.inc");
    require_once("$default->fileSystemRoot/lib/foldermanagement/Folder.inc");
    require_once("$default->fileSystemRoot/presentation/lookAndFeel/knowledgeTree/foldermanagement/folderUI.inc");
    require_once("$default->fileSystemRoot/presentation/Html.inc");
    require_once("$default->fileSystemRoot/presentation/webpageTemplate.inc");    

	$oPatternCustom = & new PatternCustom();
    if (isset($fFolderID)) {
    	$oFolder = Folder::get($fFolderID);
    	if ($oFolder) {
	        //if the user can edit the folder
	        if (Permission::userHasFolderWritePermission($oFolder)) {
				if (isset($fCollaborationEdit)) {
	                //user attempted to edit the folder collaboration process but could not because there is
	                //a document currently in this process
	                $oPatternCustom->setHtml(getStatusPage($fFolderID, _("You cannot edit this folder collaboration process as a document is currently undergoing this collaboration process")));
	                
	                $main->setHasRequiredFields(true);
	                $main->setFormAction("$default->rootUrl/presentation/lookAndFeel/knowledgeTree/store.php?fReturnURL=" . urlencode("$default->rootUrl/control.php?action=browse&fFolderID=$fFolderID"));
	            } else if (isset($fCollaborationDelete)) {
	                //user attempted to delete the folder collaboration process but could not because there is
	                //a document currently in this process
	                $oPatternCustom->setHtml(getStatusPage($fFolderID, _("You cannot delete this folder collaboration process as a document is currently undergoing this collaboration process")));
	                $main->setHasRequiredFields(true);
	                $main->setFormAction("$default->rootUrl/presentation/lookAndFeel/knowledgeTree/store.php?fReturnURL=" . urlencode("$default->rootUrl/control.php?action=browse&fFolderID=$fFolderID"));
	            } else {
	                // does this folder have a document in it that has started collaboration?
	                $bCollaboration = Folder::hasDocumentInCollaboration($fFolderID);
		            $main->setDHTMLScrolling(false);
		            $main->setOnLoadJavaScript("switchDiv('" . (isset($fShowSection) ? $fShowSection : "folderData") . "', 'folder')");
	                    
	                $oPatternCustom->setHtml(getPage($fFolderID, "", $bCollaboration));
	                $main->setHasRequiredFields(true);
	                $main->setFormAction("$default->rootUrl/presentation/lookAndFeel/knowledgeTree/store.php?fReturnURL=" . urlencode("$default->rootUrl/control.php?action=browse&fFolderID=$fFolderID"));
	            }
	        } else {
	            //user does not have write permission for this folder,
	            $oPatternCustom->setHtml("<a href=\"javascript:history.go(-1)\"><img src=\"" . KTHtml::getBackButton() . "\" border=\"0\" /></a>\n");
	            $main->setErrorMessage(_("You do not have permission to edit this folder"));
	        }
    	} else {
    		// folder doesn't exist
	        $oPatternCustom->setHtml("<a href=\"javascript:history.go(-1)\"><img src=\"" . KTHtml::getBackButton() . "\" border=\"0\" /></a>\n");
	        $main->setErrorMessage(_("The folder you're trying to modify does not exist in the DMS"));
	        $main->setFormAction("$default->rootUrl/presentation/lookAndFeel/knowledgeTree/store.php?fReturnURL=" . urlencode("$default->rootUrl/control.php?action=browse&fFolderID=$fFolderID"));
    	}
    } else {
        //else display an error message
        $oPatternCustom->setHtml("<a href=\"javascript:history.go(-1)\"><img src=\"" . KTHtml::getBackButton() . "\" border=\"0\" /></a>\n");
        $main->setErrorMessage(_("No folder currently selected"));
        $main->setFormAction("$default->rootUrl/presentation/lookAndFeel/knowledgeTree/store.php?fReturnURL=" . urlencode("$default->rootUrl/control.php?action=browse&fFolderID=$fFolderID"));
    }
	$main->setCentralPayload($oPatternCustom);						
	$main->render();
}
?>
