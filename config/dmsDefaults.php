<?php

/**
 * $Id$
 *
 * Stores the defaults for the DMS application
 *
 * Copyright (c) 1999-2002 The Owl Project Team
 * Licensed under the GNU GPL. For full terms see the file COPYING.
 */

// include the environment settings
require_once("environment.php");


//****************************************************
// Pick your language system default language
// now each user can pick his language
// if they are allowed by the admin to change their
// preferences.
//****************************************************
// b5
// Chinese
// Danish
// Deutsch
// Dutch
// English
// Francais
// Hungarian
// Italian
// NewEnglish <-  NEW LOOK, English will be obsoleted in a future version
// Norwegian
// Portuguese
// Spanish

// Table mappings
// session information
$default->owl_sessions_table = "active_sessions";
//data types table
$default->owl_data_types_table ="data_types";
// document type fields
$default->owl_fields_table = "document_fields";
// links document
$default->owl_document_fields_table = "document_fields_link";
// meta data value lookup table
$default->owl_document_fields_lookup_tables = "metadata_lookup";
// document subscriptions
$default->owl_document_subscriptions_table = "document_subscriptions";
// document transaction types
$default->owl_transaction_types_table = "document_transaction_types_lookup";
// document transactions
$default->owl_document_transactions_table = "document_transactions";
// links document types to document type fields
$default->owl_document_type_fields_table = "document_type_fields_link";
// document type information
$default->owl_document_types_table = "document_types_lookup";
// links documents to words
$default->owl_document_words_table = "document_words_link";
// stores documents
$default->owl_documents_table = "documents";
//link folders to doc types
$default->owl_folder_doctypes_table = "folder_doctypes_link";
// stores folder subscriptions
$default->owl_folder_subscriptions_table = "folder_subscriptions";
// stores folders
$default->owl_folders_table = "folders";
// links folders to users (and roles) for approval collaboration
$default->owl_folders_user_roles_table = "folders_users_roles_link";
// stores approval collaboration information- approval roles mapped to folders with order
$default->owl_groups_folders_approval_table = "groups_folders_approval_link";
// links groups to folders
$default->owl_groups_folders_table = "groups_folders_link";
// stores group information
$default->owl_groups_table = "groups_lookup";
// links groups to units
$default->owl_groups_units_table = "groups_units_link";
// links
$default->owl_links_table = "links";
// Table with metadata
$default->owl_metadata_table = "metadata_lookup";
// Table with mime info
$default->owl_mime_table = "mime_types";
// dashboard news table
$default->owl_news_table = "news";
// organisation information
$default->owl_organisations_table = "organisations_lookup";
// stores role information (name and access)
$default->owl_roles_table = "roles";
// sitemap access classes
$default->owl_site_access_table = "site_access_lookup";
// sitemap sections
$default->owl_site_sections_table = "site_sections_lookup";
// sitemap definition
$default->owl_sitemap_table = "sitemap";
// stores document subscription information
$default->owl_subscriptions_table = "subscriptions";
// stores deleted files
$default->owl_sys_deleted_table = "sys_deleted";
// stores default system settings
$default->owl_system_settings_table = "system_settings";
// Table with discussion threads 
$default->owl_discussion_threads_table = "discussion_threads";
// Table with discussion comments
$default->owl_discussion_comments_table = "discussion_comments";
// Table with unit information
$default->owl_units_table = "units_lookup";
// Table with unit organisation link tables
$default->owl_units_organisations_table = "units_organisations_link";
// Table with user info
$default->owl_users_table = "users";
// links groups to users
$default->owl_users_groups_table = "users_groups_link";
// Table with web documents info for web publishing
$default->owl_web_documents_table = "web_documents";
// Table with web documents info for web publishing
$default->owl_web_documents_status_table = "web_documents_status_lookup";
// stores websites for web publishing
$default->owl_web_sites_table = "web_sites";
// stores indexed words
$default->owl_words_lookup_table = "words_lookup";
//stores help text
$default->owl_help_table = "help";
$default->owl_document_text_table = "document_text";
$default->owl_dependant_document_template_table = "dependant_document_template";
$default->owl_dependant_document_instance_table = "dependant_document_instance";
$default->owl_document_link_table = "document_link";
// archive settings
$default->owl_document_archiving_table = "document_archiving_link";
$default->owl_archiving_type_lookup_table = "archiving_type_lookup";
$default->owl_archiving_settings_table = "archiving_settings";
$default->owl_time_period_table = "time_period";
$default->owl_time_unit_lookup_table = "time_unit_lookup";
$default->owl_archive_restoration_table = "archive_restoration_request";
$default->owl_status_table = "status_lookup";

// logo file that must reside inside lang/graphics directory
$default->logo = "kt.jpg";

$default->version = "owl-dms 1.0 @build-date@";
$default->phpversion = "4.0.2";

// define site mappings
require_once("$default->fileSystemRoot/lib/session/SiteMap.inc");
$default->siteMap = new SiteMap(false);

// action, page, section, group with access, link text

// general pages
$default->siteMap->addPage("login", "/presentation/login.php?loginAction=login", "General", None, "");
$default->siteMap->addPage("loginForm", "/presentation/login.php?loginAction=loginForm", "General", None, "login");

// dashboard
$default->siteMap->addPage("dashboard", "/presentation/lookAndFeel/knowledgeTree/dashboardBL.php", "General", Guest, "dashboard");
// dashboard news
$default->siteMap->addPage("viewNewsItem", "/presentation/lookAndFeel/knowledgeTree/dashboard/news/displayNewsItem.php", "General", Guest, "");
$default->siteMap->addPage("viewNewsImage", "/presentation/lookAndFeel/knowledgeTree/dashboard/news/displayNewsImage.php", "General", Guest, "");

//pages for manage documents section
$default->siteMap->addDefaultPage("browse", "/presentation/lookAndFeel/knowledgeTree/documentmanagement/browseBL.php", "Manage Documents", Guest, "browse documents");
$default->siteMap->addDefaultPage("templateBrowse", "/presentation/lookAndFeel/knowledgeTree/documentmanagement/templateDocumentBrowseBL.php", "Manage Documents", Guest, "browse documents");
$default->siteMap->addPage("viewDocument", "/presentation/lookAndFeel/knowledgeTree/documentmanagement/viewBL.php", "Manage Documents", Guest, "View Document", false);
$default->siteMap->addPage("downloadDocument", "/presentation/lookAndFeel/knowledgeTree/documentmanagement/downloadBL.php", "Manage Documents", Guest, "Download Document", false);
$default->siteMap->addPage("deleteDocument", "/presentation/lookAndFeel/knowledgeTree/documentmanagement/deleteDocumentBL.php", "Manage Documents", User, "Delete document", false);
$default->siteMap->addPage("moveDocument", "/presentation/lookAndFeel/knowledgeTree/documentmanagement/moveDocumentBL.php", "Manage Documents", User, "Move document", false);
$default->siteMap->addPage("viewHistory", "/presentation/lookAndFeel/knowledgeTree/documentmanagement/viewHistoryBL.php", "Manage Documents", User, "View Document History", false);
$default->siteMap->addPage("modifyDocument", "/presentation/lookAndFeel/knowledgeTree/documentmanagement/modifyBL.php", "Manage Documents", User, "Modify Document", false);
$default->siteMap->addPage("emailDocument", "/presentation/lookAndFeel/knowledgeTree/documentmanagement/emailBL.php", "Manage Documents", User, "Email A Document", false);
$default->siteMap->addPage("modifyDocumentGenericMetaData", "/presentation/lookAndFeel/knowledgeTree/documentmanagement/modifyGenericMetaDataBL.php", "Manage Documents", User, "Modify Document Generic MetaData", false);
$default->siteMap->addPage("archiveDocument", "/presentation/lookAndFeel/knowledgeTree/documentmanagement/archiving/archiveDocumentBL.php", "Manage Documents", User, "Archive Document", false);
$default->siteMap->addPage("addDocumentArchiveSettings", "/presentation/lookAndFeel/knowledgeTree/documentmanagement/archiving/addArchiveSettingsBL.php", "Manage Documents", User, "Add Document Archive Settings", false);
$default->siteMap->addPage("modifyDocumentArchiveSettings", "/presentation/lookAndFeel/knowledgeTree/documentmanagement/archiving/modifyArchiveSettingsBL.php", "Manage Documents", User, "Modify Document Archive Settings", false);
$default->siteMap->addPage("requestDocumentRestore", "/presentation/lookAndFeel/knowledgeTree/documentmanagement/archiving/requestDocumentRestoreBL.php", "Manage Documents", User, "Request Document Restoration", false);

$default->siteMap->addPage("addFolder", "/presentation/lookAndFeel/knowledgeTree/foldermanagement/addFolderBL.php", "Manage Documents", User, "Add A Folder");
$default->siteMap->addPage("addFolderDocType", "/presentation/lookAndFeel/knowledgeTree/foldermanagement/addFolderDocTypeBL.php", "Manage Documents", User, "");
$default->siteMap->addPage("deleteFolderDocType", "/presentation/lookAndFeel/knowledgeTree/foldermanagement/deleteFolderDocTypeBL.php", "Manage Documents", User, "");
$default->siteMap->addPage("editFolder", "/presentation/lookAndFeel/knowledgeTree/foldermanagement/editBL.php", "Manage Documents", User, "Modify Folder Properties");
$default->siteMap->addPage("deleteFolder", "/presentation/lookAndFeel/knowledgeTree/foldermanagement/deleteFolderBL.php", "Manage Documents", User, "Delete Current Folder");


// folder access
$default->siteMap->addPage("addGroupFolderLink", "/presentation/lookAndFeel/knowledgeTree/foldermanagement/addGroupFolderLinkBL.php", "Manage Documents", UnitAdmin, "Add Folder Access", false);
$default->siteMap->addPage("modifyGroupFolderLink", "/presentation/lookAndFeel/knowledgeTree/foldermanagement/editGroupFolderLinkBL.php", "Manage Documents", UnitAdmin, "Edit Folder Access", false);
$default->siteMap->addPage("deleteGroupFolderLink", "/presentation/lookAndFeel/knowledgeTree/foldermanagement/deleteGroupFolderLinkBL.php", "Manage Documents", UnitAdmin, "Delete Folder Access", false);

$default->siteMap->addPage("addDocument", "/presentation/lookAndFeel/knowledgeTree/documentmanagement/addDocumentBL.php", "Manage Documents", User, "Add A Document");
$default->siteMap->addPage("modifyDocumentTypeMetaData", "/presentation/lookAndFeel/knowledgeTree/documentmanagement/modifySpecificMetaDataBL.php", "Manage Documents", User, "");
$default->siteMap->addPage("modifyDocumentRouting", "/presentation/lookAndFeel/knowledgeTree/documentmanagement/collaborationBL.php", "Manage Documents", User, "");
$default->siteMap->addPage("collaborationStepReject", "/presentation/lookAndFeel/knowledgeTree/documentmanagement/collaborationRollbackBL.php", "Manage Documents", User, "");
$default->siteMap->addPage("modifyFolderCollaboration", "/presentation/lookAndFeel/knowledgeTree/foldermanagement/collaborationBL.php", "Manage Documents", User, "", false);
$default->siteMap->addPage("addFolderCollaboration", "/presentation/lookAndFeel/knowledgeTree/foldermanagement/addFolderCollaborationBL.php", "Manage Documents", User, "", false);
$default->siteMap->addPage("deleteFolderCollaboration", "/presentation/lookAndFeel/knowledgeTree/foldermanagement/deleteFolderCollaborationBL.php", "Manage Documents", User, "", false);
$default->siteMap->addPage("viewDependantDocument", "/presentation/lookAndFeel/knowledgeTree/foldermanagement/viewDependantDocumentsBL.php", "Manage Documents", User, "", false);
$default->siteMap->addPage("deleteDependantDocument", "/presentation/lookAndFeel/knowledgeTree/foldermanagement/deleteDependantDocumentBL.php", "Manage Documents", User, "", false);
$default->siteMap->addPage("removeDocumentLink", "/presentation/lookAndFeel/knowledgeTree/documentmanagement/removeDocumentLinkBL.php", "Manage Documents", User, "", false);
$default->siteMap->addPage("addDocumentLink", "/presentation/lookAndFeel/knowledgeTree/documentmanagement/addDocumentLinkBL.php", "Manage Documents", User, "", false);
$default->siteMap->addPage("createDependantDocument", "/presentation/lookAndFeel/knowledgeTree/documentmanagement/createDependantDocumentBL.php", "Manage Documents", User, "", false);

$default->siteMap->addPage("addSubscription", "/presentation/lookAndFeel/knowledgeTree/subscriptions/addSubscriptionBL.php", "Manage Documents", User, "Add Folder Subscription");
$default->siteMap->addPage("removeSubscription", "/presentation/lookAndFeel/knowledgeTree/subscriptions/removeSubscriptionBL.php", "Manage Documents", User, "Remove Folder Subscription");

// check in / check out
$default->siteMap->addPage("checkOutDocument", "/presentation/lookAndFeel/knowledgeTree/documentmanagement/checkOutDocumentBL.php", "Manage Documents", User, "Check Out Document", false);
$default->siteMap->addPage("checkInDocument", "/presentation/lookAndFeel/knowledgeTree/documentmanagement/checkInDocumentBL.php", "Manage Documents", User, "Check In Document", false);

$default->siteMap->addSectionColour("Manage Documents", "td", "BDDFE0");
$default->siteMap->addSectionColour("Manage Documents", "th", "57AFAE");

// web documents
$default->siteMap->addPage("webDocument", "/presentation/lookAndFeel/knowledgeTree/documentmanagement/webDocumentBL.php", "Manage Documents", Guest, "View Web Document", false);

// category management
$default->siteMap->addPage("manageCategories", "/presentation/lookAndFeel/knowledgeTree/administration/docfieldmanagement/editDocFieldBL.php?fDocFieldID=1", "Manage Categories", SysAdmin, "Manage Categories");
$default->siteMap->addSectionColour("Manage Categories", "td", "BDDFE0");
$default->siteMap->addSectionColour("Manage Categories", "th", "57AFAE");
// document type management
$default->siteMap->addPage("manageDocumentTypes", "/presentation/lookAndFeel/knowledgeTree/administration/admin.php?sectionName=documentTypeAdministration", "Manage Document Types", SysAdmin, "Manage Document Types");
$default->siteMap->addSectionColour("Manage Document Types", "td", "BDDFE0");
$default->siteMap->addSectionColour("Manage Document Types", "th", "57AFAE");

// pages for administration section
$default->siteMap->addDefaultPage("administration", "/presentation/lookAndFeel/knowledgeTree/administration/admin.php", "Administration", UnitAdmin, "Administration");
$default->siteMap->addPage("userManagement", "/presentation/lookAndFeel/knowledgeTree/administration/admin.php?sectionName=userAdministration", "Administration", UnitAdmin, "User Management");
$default->siteMap->addPage("groupManagement", "/presentation/lookAndFeel/knowledgeTree/administration/admin.php?sectionName=groupAdministration", "Administration", UnitAdmin, "Group Management");
$default->siteMap->addPage("unitManagement", "/presentation/lookAndFeel/knowledgeTree/administration/admin.php?sectionName=unitAdministration", "Administration", SysAdmin, "Unit Management");
$default->siteMap->addPage("orgManagement", "/presentation/lookAndFeel/knowledgeTree/administration/admin.php?sectionName=orgAdministration", "Administration", SysAdmin, "Organisation Management");
//$default->siteMap->addPage("doctypeManagement", "/presentation/lookAndFeel/knowledgeTree/administration/admin.php?sectionName=doctypeAdministration", "Administration", SysAdmin, "Document Type & Field Management");
$default->siteMap->addPage("doctype", "/presentation/lookAndFeel/knowledgeTree/administration/admin.php?sectionName=documentTypeAdministration", "Administration", SysAdmin, "Document Type Management");
$default->siteMap->addPage("docfield", "/presentation/lookAndFeel/knowledgeTree/administration/admin.php?sectionName=documentFieldAdministration", "Administration", SysAdmin, "Document Field Management");
$default->siteMap->addPage("roleManagement", "/presentation/lookAndFeel/knowledgeTree/administration/admin.php?sectionName=roleAdministration", "Administration", SysAdmin, "Role Management");
$default->siteMap->addPage("linkManagement", "/presentation/lookAndFeel/knowledgeTree/administration/admin.php?sectionName=linkAdministration", "Administration", SysAdmin, "QuickLink Management");
$default->siteMap->addPage("newsManagement", "/presentation/lookAndFeel/knowledgeTree/administration/admin.php?sectionName=newsAdministration", "Administration", SysAdmin, "Dashboard News Management");
$default->siteMap->addPage("websiteManagement", "/presentation/lookAndFeel/knowledgeTree/administration/admin.php?sectionName=websiteAdministration", "Administration", SysAdmin, "Website Management");
$default->siteMap->addPage("systemAdministration", "/presentation/lookAndFeel/knowledgeTree/administration/systemsettings/systemSettingsBL.php", "Administration", SysAdmin, "System Settings");
$default->siteMap->addPage("archivedDocuments", "/presentation/lookAndFeel/knowledgeTree/administration/documentmanagement/manageArchivedDocumentsBL.php", "Administration", UnitAdmin, "Restore Archived Documents");
$default->siteMap->addPage("deletedDocuments", "/presentation/lookAndFeel/knowledgeTree/administration/documentmanagement/listDeletedDocumentsBL.php", "Administration", UnitAdmin, "Deleted Documents");
$default->siteMap->addPage("doccheckoutManagement", "/presentation/lookAndFeel/knowledgeTree/administration/admin.php?sectionName=doccheckoutAdministration", "Administration", UnitAdmin, "Document Checkout Management");

$default->siteMap->addSectionColour("Administration", "th", "056DCE");
$default->siteMap->addSectionColour("Administration", "td", "6699CC");

// group management
$default->siteMap->addDefaultPage("editGroupUnit", "/presentation/lookAndFeel/knowledgeTree/administration/groupmanagement/editGroupUnitBL.php", "groupAdministration", UnitAdmin, "editGroupUnit");
$default->siteMap->addPage("listGroups", "/presentation/lookAndFeel/knowledgeTree/administration/groupmanagement/listGroupsBL.php", "groupAdministration", UnitAdmin, "List Groups");
$default->siteMap->addPage("addGroup", "/presentation/lookAndFeel/knowledgeTree/administration/groupmanagement/addGroupBL.php", "groupAdministration", UnitAdmin, "Add A Group");
$default->siteMap->addDefaultPage("editGroup", "/presentation/lookAndFeel/knowledgeTree/administration/groupmanagement/editGroupBL.php", "groupAdministration", UnitAdmin, "Edit Group Properties");
$default->siteMap->addDefaultPage("editGroupSuccess", "/presentation/lookAndFeel/knowledgeTree/administration/groupmanagement/editGroupSuccess.php", "groupAdministration", UnitAdmin, "Updated Group Successfully",false);
$default->siteMap->addDefaultPage("removeGroup", "/presentation/lookAndFeel/knowledgeTree/administration/groupmanagement/removeGroupBL.php", "groupAdministration", UnitAdmin, "Remove a Group");
$default->siteMap->addDefaultPage("assignGroupToUnit", "/presentation/lookAndFeel/knowledgeTree/administration/groupmanagement/assignGroupToUnitBL.php", "groupAdministration", UnitAdmin, "Assign Group to Unit");
$default->siteMap->addDefaultPage("removeGroupFromUnit", "/presentation/lookAndFeel/knowledgeTree/administration/groupmanagement/removeGroupFromUnitBL.php", "groupAdministration", UnitAdmin, "Remove Group From Unit");

$default->siteMap->addSectionColour("groupAdministration", "th", "056DCE");
$default->siteMap->addSectionColour("groupAdministration", "td", "6699CC");

// Unit management
$default->siteMap->addPage("listUnits", "/presentation/lookAndFeel/knowledgeTree/administration/unitmanagement/listUnitsBL.php", "unitAdministration", SysAdmin, "List Units");
$default->siteMap->addDefaultPage("editUnitOrg", "/presentation/lookAndFeel/knowledgeTree/administration/unitmanagement/editUnitOrgBL.php", "unitAdministration", SysAdmin, "Edit Unit Organisations");
$default->siteMap->addDefaultPage("addUnitToOrg", "/presentation/lookAndFeel/knowledgeTree/administration/unitmanagement/addUnitToOrgBL.php", "unitAdministration", SysAdmin, "Add Unit to Organisations");
$default->siteMap->addDefaultPage("removeUnitFromOrg", "/presentation/lookAndFeel/knowledgeTree/administration/unitmanagement/removeUnitFromOrgBL.php", "unitAdministration", SysAdmin, "Remove Unit from Organisations");
$default->siteMap->addPage("addUnit", "/presentation/lookAndFeel/knowledgeTree/administration/unitmanagement/addUnitBL.php", "unitAdministration", SysAdmin, "Add A Unit");
$default->siteMap->addDefaultPage("editUnit", "/presentation/lookAndFeel/knowledgeTree/administration/unitmanagement/editUnitBL.php", "unitAdministration", SysAdmin, "Edit Unit Properties");
$default->siteMap->addPage("addUnitSuccess", "/presentation/lookAndFeel/knowledgeTree/administration/unitmanagement/addUnitSuccess.php", "unitAdministration", SysAdmin, "Unit added Successfully",false);
$default->siteMap->addDefaultPage("removeUnit", "/presentation/lookAndFeel/knowledgeTree/administration/unitmanagement/removeUnitBL.php", "unitAdministration", SysAdmin, "Remove a Unit");

$default->siteMap->addSectionColour("unitAdministration", "th", "056DCE");
$default->siteMap->addSectionColour("unitAdministration", "td", "6699CC");

// Organisation management
$default->siteMap->addPage("listOrg", "/presentation/lookAndFeel/knowledgeTree/administration/orgmanagement/listOrgBL.php", "orgAdministration", SysAdmin, "List Organisations");
$default->siteMap->addPage("addOrg", "/presentation/lookAndFeel/knowledgeTree/administration/orgmanagement/addOrgBL.php", "orgAdministration", SysAdmin, "Add An Organisation");
$default->siteMap->addDefaultPage("editOrg", "/presentation/lookAndFeel/knowledgeTree/administration/orgmanagement/editOrgBL.php", "orgAdministration", SysAdmin, "Edit Organisation Properties");
$default->siteMap->addPage("addOrgSuccess", "/presentation/lookAndFeel/knowledgeTree/administration/orgmanagement/addOrgSuccess.php", "orgAdministration", SysAdmin, "Organisation added Successfully",false);
$default->siteMap->addDefaultPage("removeOrg", "/presentation/lookAndFeel/knowledgeTree/administration/orgmanagement/removeOrgBL.php", "orgAdministration", SysAdmin, "Remove an Organisation");

$default->siteMap->addSectionColour("orgAdministration", "th", "056DCE");
$default->siteMap->addSectionColour("orgAdministration", "td", "6699CC");

// user management
$default->siteMap->addDefaultPage("editUserGroups", "/presentation/lookAndFeel/knowledgeTree/administration/usermanagement/editUserGroupsBL.php", "userAdministration", SysAdmin, "Edit User Groups");
$default->siteMap->addPage("listUsers", "/presentation/lookAndFeel/knowledgeTree/administration/usermanagement/listUsersBL.php", "userAdministration", SysAdmin, "List Users");
$default->siteMap->addPage("addUser", "/presentation/lookAndFeel/knowledgeTree/administration/usermanagement/addUserBL.php", "userAdministration", SysAdmin, "Add User");
$default->siteMap->addDefaultPage("editUser", "/presentation/lookAndFeel/knowledgeTree/administration/usermanagement/editUserBL.php", "userAdministration", SysAdmin, "Edit User Properties");
$default->siteMap->addDefaultPage("removeUser", "/presentation/lookAndFeel/knowledgeTree/administration/usermanagement/removeUserBL.php", "userAdministration", SysAdmin, "Remove User from System");
$default->siteMap->addDefaultPage("addUserToGroup", "/presentation/lookAndFeel/knowledgeTree/administration/usermanagement/addUserToGroupBL.php", "userAdministration", UnitAdmin, "Add User to Group");
$default->siteMap->addDefaultPage("removeUserFromGroup", "/presentation/lookAndFeel/knowledgeTree/administration/usermanagement/removeUserFromGroupBL.php", "userAdministration", UnitAdmin, "Remove User From Group");

$default->siteMap->addSectionColour("userAdministration", "th", "056DCE");
$default->siteMap->addSectionColour("userAdministration", "td", "6699CC");

//document type management
//$default->siteMap->addPage("doctype", "/presentation/lookAndFeel/knowledgeTree/administration/admin.php?sectionName=documentTypeAdministration", "doctypeAdministration", SysAdmin, "Document Type Management");
//$default->siteMap->addPage("docfield", "/presentation/lookAndFeel/knowledgeTree/administration/admin.php?sectionName=documentFieldAdministration", "doctypeAdministration", SysAdmin, "Document Field Management");

// document checkout management
$default->siteMap->addPage("listDocs", "/presentation/lookAndFeel/knowledgeTree/administration/doccheckoutmanagement/listDocumentsBL.php", "doccheckoutAdministration", SysAdmin, "List Documents");
$default->siteMap->addDefaultPage("editDocCheckout", "/presentation/lookAndFeel/knowledgeTree/administration/doccheckoutmanagement/editDocCheckoutBL.php", "doccheckoutAdministration", SysAdmin, "Edit Document Checkout");

$default->siteMap->addSectionColour("doccheckoutAdministration", "th", "056DCE");

// document type stuff
$default->siteMap->addDefaultPage("addDocTypeFieldsLink", "/presentation/lookAndFeel/knowledgeTree/administration/doctypemanagement/addDocTypeFieldsLinkBL.php", "documentTypeAdministration", SysAdmin, "Add a Document Type to Fields Link");
$default->siteMap->addPage("addDocType", "/presentation/lookAndFeel/knowledgeTree/administration/doctypemanagement/addDocTypeBL.php", "documentTypeAdministration", SysAdmin, "Add a Document Type");
$default->siteMap->addPage("addDocTypeSuccess", "/presentation/lookAndFeel/knowledgeTree/administration/doctypemanagement/addDocTypeSuccess.php", "documentTypeAdministration", SysAdmin, "Add a Document Type success", False);
$default->siteMap->addDefaultPage("editDocType", "/presentation/lookAndFeel/knowledgeTree/administration/doctypemanagement/editDocTypeBL.php", "documentTypeAdministration", SysAdmin, "Edit a Document Type");
$default->siteMap->addDefaultPage("editDocTypeFields", "/presentation/lookAndFeel/knowledgeTree/administration/doctypemanagement/editDocTypeFieldsBL.php", "documentTypeAdministration", SysAdmin, "Edit Document Type Fields");
$default->siteMap->addPage("listDocTypes", "/presentation/lookAndFeel/knowledgeTree/administration/doctypemanagement/listDocTypesBL.php", "documentTypeAdministration", SysAdmin, "List Document Types");
$default->siteMap->addDefaultPage("removeDocType", "/presentation/lookAndFeel/knowledgeTree/administration/doctypemanagement/removeDocTypeBL.php", "documentTypeAdministration", SysAdmin, "Remove a Document Type");

$default->siteMap->addSectionColour("documentTypeAdministration", "th", "056DCE");
$default->siteMap->addSectionColour("documentTypeAdministration", "td", "6699CC");

// doc field stuff
$default->siteMap->addPage("listDocFields", "/presentation/lookAndFeel/knowledgeTree/administration/docfieldmanagement/listDocFieldsBL.php", "documentFieldAdministration", SysAdmin, "List Document Fields");
$default->siteMap->addPage("addDocField", "/presentation/lookAndFeel/knowledgeTree/administration/docfieldmanagement/addDocFieldBL.php", "documentFieldAdministration", SysAdmin, "Add a Document Field");
$default->siteMap->addPage("addDocFieldSuccess", "/presentation/lookAndFeel/knowledgeTree/administration/docfieldmanagement/addDocFieldSuccess.php", "documentFieldAdministration", SysAdmin, "Add a Document Field success", False);
$default->siteMap->addDefaultPage("editDocField", "/presentation/lookAndFeel/knowledgeTree/administration/docfieldmanagement/editDocFieldBL.php", "documentFieldAdministration", SysAdmin, "Edit a Document Field");
$default->siteMap->addDefaultPage("removeDocField", "/presentation/lookAndFeel/knowledgeTree/administration/docfieldmanagement/removeDocFieldBL.php", "documentFieldAdministration", SysAdmin, "Remove a Document Field");
$default->siteMap->addDefaultPage("editDocFieldLookups", "/presentation/lookAndFeel/knowledgeTree/administration/docfieldmanagement/editDocFieldLookupsBL.php", "documentFieldAdministration", SysAdmin, "Edit Document Field Lookups");
//metadata
$default->siteMap->addDefaultPage("editMetadataForField", "/presentation/lookAndFeel/knowledgeTree/administration/docfieldmanagement/metadatamanagement/editMetaDataBL.php", "documentFieldAdministration", SysAdmin, "Edit Document Field Lookups");
$default->siteMap->addDefaultPage("addMetaDataForField",  "/presentation/lookAndFeel/knowledgeTree/administration/docfieldmanagement/metadatamanagement/addMetaDataBL.php", "documentFieldAdministration", SysAdmin, "Add Document Field Lookups");
$default->siteMap->addDefaultPage("removeMetaDataFromField",  "/presentation/lookAndFeel/knowledgeTree/administration/docfieldmanagement/metadatamanagement/removeMetaDataBL.php", "documentFieldAdministration", SysAdmin, "Remove Document Field Lookups");

$default->siteMap->addSectionColour("documentFieldAdministration", "th", "056DCE");

$default->siteMap->addSectionColour("metaDataAdministration", "th", "056DCE");
$default->siteMap->addSectionColour("metaDataAdministration", "td", "6699CC");

// rolemanagement
$default->siteMap->addPage("listRole", "/presentation/lookAndFeel/knowledgeTree/administration/rolemanagement/listRolesBL.php", "roleAdministration", SysAdmin, "List Roles");
$default->siteMap->addPage("addRole", "/presentation/lookAndFeel/knowledgeTree/administration/rolemanagement/addRoleBL.php", "roleAdministration", SysAdmin, "Add New Role");
$default->siteMap->addDefaultPage("editRole", "/presentation/lookAndFeel/knowledgeTree/administration/rolemanagement/editRoleBL.php", "roleAdministration", SysAdmin, "Edit Role Properties");
$default->siteMap->addDefaultPage("editRoleSuccess", "/presentation/lookAndFeel/knowledgeTree/administration/rolemanagement/editRoleSuccess.php", "roleAdministration", SysAdmin, "Edit Role Properties", false);
$default->siteMap->addDefaultPage("removeRole", "/presentation/lookAndFeel/knowledgeTree/administration/rolemanagement/removeRoleBL.php", "roleAdministration", SysAdmin, "Remove a Role");

$default->siteMap->addSectionColour("roleAdministration", "th", "056DCE");
$default->siteMap->addSectionColour("roleAdministration", "td", "6699CC");

// link management
$default->siteMap->addPage("listLinks", "/presentation/lookAndFeel/knowledgeTree/administration/linkmanagement/listLinksBL.php", "linkAdministration", SysAdmin, "List Links");
$default->siteMap->addPage("addLink", "/presentation/lookAndFeel/knowledgeTree/administration/linkmanagement/addLinkBL.php", "linkAdministration", SysAdmin, "Add A Link");
$default->siteMap->addPage("addLinkSuccess", "/presentation/lookAndFeel/knowledgeTree/administration/linkmanagement/addLinkSuccess.php", "linkAdministration", SysAdmin, "Add A Link Success ",false);
$default->siteMap->addDefaultPage("editLink", "/presentation/lookAndFeel/knowledgeTree/administration/linkmanagement/editLinkBL.php", "linkAdministration", SysAdmin, "Edit Link Properties");
$default->siteMap->addDefaultPage("removeLink", "/presentation/lookAndFeel/knowledgeTree/administration/linkmanagement/removeLinkBL.php", "linkAdministration", SysAdmin, "Remove a Link");

$default->siteMap->addSectionColour("linkAdministration", "th", "056DCE");
$default->siteMap->addSectionColour("linkAdministration", "td", "6699CC");

// news management
$default->siteMap->addPage("listNews", "/presentation/lookAndFeel/knowledgeTree/administration/news/listNewsBL.php", "newsAdministration", SysAdmin, "List News Items");
$default->siteMap->addPage("addNews", "/presentation/lookAndFeel/knowledgeTree/administration/news/addNewsBL.php", "newsAdministration", SysAdmin, "Add A News Item");
$default->siteMap->addPage("editNews", "/presentation/lookAndFeel/knowledgeTree/administration/news/editNewsBL.php", "newsAdministration", SysAdmin, "");
$default->siteMap->addPage("previewNews", "/presentation/lookAndFeel/knowledgeTree/administration/news/previewNewsBL.php", "newsAdministration", SysAdmin, "");
$default->siteMap->addPage("removeNews", "/presentation/lookAndFeel/knowledgeTree/administration/news/removeNewsBL.php", "newsAdministration", SysAdmin, "");
$default->siteMap->addSectionColour("newsAdministration", "th", "056DCE");
$default->siteMap->addSectionColour("newsAdministration", "td", "6699CC");

//website management
$default->siteMap->addPage("listWebsites", "/presentation/lookAndFeel/knowledgeTree/administration/websitemanagement/listWebsitesBL.php", "websiteAdministration", SysAdmin, "List Websites");
$default->siteMap->addPage("addWebsite", "/presentation/lookAndFeel/knowledgeTree/administration/websitemanagement/addWebsiteBL.php", "websiteAdministration", SysAdmin, "Add a Website");
$default->siteMap->addPage("addWebsiteSuccess", "/presentation/lookAndFeel/knowledgeTree/administration/websitemanagement/addWebsiteSuccess.php", "websiteAdministration", SysAdmin, "Add A Website Success ",false);
$default->siteMap->addDefaultPage("editWebSite", "/presentation/lookAndFeel/knowledgeTree/administration/websitemanagement/editWebsiteBL.php", "websiteAdministration", SysAdmin, "Edit Website");
$default->siteMap->addDefaultPage("removeWebSite", "/presentation/lookAndFeel/knowledgeTree/administration/websitemanagement/removeWebsiteBL.php", "websiteAdministration", SysAdmin, "Remove a Website");

$default->siteMap->addSectionColour("websiteAdministration", "th", "056DCE");
$default->siteMap->addSectionColour("websiteAdministration", "td", "6699CC");

// deleted documents
$default->siteMap->addPage("expungeDeletedDocuments", "/presentation/lookAndFeel/knowledgeTree/administration/documentmanagement/expungeDeletedDocumentsBL.php", "Administration", UnitAdmin, "Expunge Deleted Documents", false);
$default->siteMap->addPage("restoreDeletedDocument", "/presentation/lookAndFeel/knowledgeTree/administration/documentmanagement/restoreDeletedDocumentBL.php", "Administration", UnitAdmin, "Restore Deleted Document", false);

// pages for subscriptions section
$default->siteMap->addDefaultPage("manageSubscriptions", "/presentation/lookAndFeel/knowledgeTree/subscriptions/manageSubscriptionsBL.php", "Subscriptions", User, "Manage Subscriptions");
$default->siteMap->addPage("viewAlert", "/presentation/lookAndFeel/knowledgeTree/subscriptions/viewAlertBL.php", "Subscriptions", User, "Subscriptions", false);

$default->siteMap->addSectionColour("Subscriptions", "th", "FFC602");

// pages for advanced search section
$default->siteMap->addDefaultPage("advancedSearch", "/presentation/lookAndFeel/knowledgeTree/search/advancedSearchBL.php", "Advanced Search", Guest, "Advanced Search", true);
$default->siteMap->addPage("standardSearch", "/presentation/lookAndFeel/knowledgeTree/search/standardSearchBL.php", "Standard Search", Guest, "Standard Search", false);

$default->siteMap->addSectionColour("Advanced Search", "th", "A1571B");
$default->siteMap->addSectionColour("Standard Search", "th", "A1571B");

// pages for prefs section
$default->siteMap->addDefaultPage("preferences", "/presentation/lookAndFeel/knowledgeTree/preferences/editUserPrefsBL.php", "Preferences", User, "Preferences");
$default->siteMap->addPage("editPrefsSuccess", "/presentation/lookAndFeel/knowledgeTree/preferences/editPrefsSuccess.php", "Preferences", User, "Preferences",false);
$default->siteMap->addSectionColour("Preferences", "th", "F87308");
$default->siteMap->addSectionColour("Preferences", "td", "FEE3CE");

// pages for Help section
$default->siteMap->addDefaultPage("help", "/presentation/lookAndFeel/knowledgeTree/help.php", "Help", Guest, "Help");

// pages for logout section section
$default->siteMap->addDefaultPage("logout", "/presentation/logout.php", "Logout", Guest, "Logout");

// pages for discussion threads
$default->siteMap->addDefaultPage("viewDiscussion", "/presentation/lookAndFeel/knowledgeTree/discussions/viewDiscussionBL.php", "Discussion Threads", User, "viewDiscussion"); 
$default->siteMap->addDefaultPage("addComment", "/presentation/lookAndFeel/knowledgeTree/discussions/addCommentBL.php", "Discussion Threads", User, "addComment"); 

$default->siteMap->addSectionColour("Discussion Threads", "td", "BDDFE0");
$default->siteMap->addSectionColour("Discussion Threads", "th", "57AFAE");

// default requires
require_once("$default->fileSystemRoot/phpmailer/class.phpmailer.php");
require_once("$default->fileSystemRoot/lib/session/Session.inc");
require_once("$default->fileSystemRoot/lib/session/control.inc");
require_once("$default->fileSystemRoot/phpSniff/phpSniff.class.php");
require_once("$default->fileSystemRoot/presentation/Html.inc");

// instantiate phpsniffer
$default->phpSniff = new phpSniff($_SERVER["HTTP_USER_AGENT"]);
$default->browser = $default->phpSniff->property("browser");
$default->version = $default->phpSniff->property("version");

require_once("$default->fileSystemRoot/lib/Log.inc");
$default->log = new Log($default->fileSystemRoot . "/log", INFO);

// import request variables and setup language
require_once("$default->fileSystemRoot/lib/dms.inc");
?>