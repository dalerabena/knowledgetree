<?php

/**
 * $Id:$
 *
 * KnowledgeTree Open Source Edition
 * Document Management Made Simple
 * Copyright (C) 2004 - 2008 The Jam Warehouse Software (Pty) Limited
 *
 * This program is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License version 3 as published by the
 * Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more
 * details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * You can contact The Jam Warehouse Software (Pty) Limited, Unit 1, Tramber Place,
 * Blake Street, Observatory, 7925 South Africa. or email info@knowledgetree.com.
 *
 * The interactive user interfaces in modified source and object code versions
 * of this program must display Appropriate Legal Notices, as required under
 * Section 5 of the GNU General Public License version 3.
 *
 * In accordance with Section 7(b) of the GNU General Public License version 3,
 * these Appropriate Legal Notices must retain the display of the "Powered by
 * KnowledgeTree" logo and retain the original copyright notice. If the display of the
 * logo is not reasonably feasible for technical reasons, the Appropriate Legal Notices
 * must display the words "Powered by KnowledgeTree" and retain the original
 * copyright notice.
 * Contributor( s): ______________________________________
 *
 */

define('SEARCH2_INDEXER_DIR',realpath(dirname(__FILE__)) . '/');
require_once('indexing/extractorCore.inc.php');
require_once(KT_DIR . '/plugins/ktcore/scheduler/schedulerUtil.php');


class IndexerInconsistencyException extends Exception {};

class QueryResultItem
{
	protected $document_id;
	protected $title;
	protected $rank;
	protected $text;
	protected $filesize;
	protected $fullpath;
	protected $live;
	protected $version;
	protected $mimeType;
	protected $filename;
	protected $thumbnail; // TODO: if not null, gui can display a thumbnail
	protected $viewer; // TODO: if not null, a viewer can be used to view the document
	protected $document;
	protected $checkedOutUser;
	protected $dateCheckedout;
	protected $workflowState;
	protected $workflow;
	protected $modifiedBy;
	protected $dateModified;
	protected $createdBy;
	protected $dateCreated;
	protected $owner;
	protected $immutable;
	protected $deleted;
	protected $status;
	protected $folderId;
	protected $storagePath;
	protected $documentType;
	protected $mimeIconPath;
	protected $mimeDisplay;
	protected $oemDocumentNo;

	public function __construct($document_id, $rank=null, $title=null, $text=null)
	{
		$this->document_id=(int) $document_id;
		$this->rank= $rank;
		$this->title=$title;
		$this->text = $text;
		$this->live = true;
		$this->loadDocumentInfo();
	}

	protected function __isset($property)
	{
		switch($property)
		{
			case 'DocumentID': return isset($this->document_id);
			case 'Rank': return isset($this->rank);
			case 'Text': return isset($this->text);
			case 'Title': return isset($this->title);
			case null: break;
			default:
				throw new Exception("Unknown property '$property' to get on QueryResultItem");
		}
		return true; // should not be reached
	}

	public function loadDocumentInfo()
	{
		global $default;
		$sql = "SELECT
					d.folder_id, f.full_path, f.name, dcv.size as filesize, dcv.major_version,
					dcv.minor_version, dcv.filename, cou.name as checkoutuser, w.human_name as workflow, ws.human_name as workflowstate,
					mt.mimetypes as mimetype, md.mime_doc as mimedoc, d.checkedout, mbu.name as modifiedbyuser, d.modified,
					cbu.name as createdbyuser, ou.name as owneruser, d.immutable, d.status_id, d.created,dcv.storage_path, dtl.name as document_type,
					mt.icon_path as mime_icon_path, mt.friendly_name as mime_display, d.oem_no
				FROM
					documents d
					INNER JOIN document_metadata_version dmv ON d.metadata_version_id = dmv.id
					INNER JOIN document_content_version dcv ON dmv.content_version_id = dcv.id
					INNER JOIN mime_types mt ON dcv.mime_id=mt.id
					LEFT JOIN document_types_lookup dtl ON dtl.id=dmv.document_type_id
					LEFT JOIN folders f ON f.id=d.folder_id
					LEFT JOIN users cou ON d.checked_out_user_id=cou.id
					LEFT JOIN workflows w ON dmv.workflow_id=w.id
					LEFT JOIN workflow_states ws ON dmv.workflow_state_id = ws.id
					LEFT JOIN mime_documents md ON mt.mime_document_id = md.id
					LEFT JOIN users mbu ON d.modified_user_id=mbu.id
					LEFT JOIN users cbu ON d.creator_id=cbu.id
					LEFT JOIN users ou ON d.owner_id=ou.id
				WHERE
					d.id=$this->document_id";

		$result = DBUtil::getOneResult($sql);

		if (PEAR::isError($result) || empty($result))
		{
			$this->live = false;
			if (PEAR::isError($result))
			{
				throw new Exception('Database exception! There appears to be an error in the system: ' .$result->getMessage());
			}

			$default->log->error('QueryResultItem: $result is null');
			$msg = 'The database did not have a record matching the result from the document indexer. This may occur if there is an inconsistency between the document indexer and the repository. The indexer needs to be repaired. Please consult the administrator guide as to how to repair your indexer.';
			$default->log->error('QueryResultItem: ' . $msg);
			// TODO: repair process where we scan documents in index, and delete those for which there is nothing in the repository
			throw new IndexerInconsistencyException(_kt($msg));
		}

		// document_id, relevance, text, title

		$this->documentType = $result['document_type'];
		$this->filename=$result['filename'];
		$this->filesize = KTUtil::filesizeToString($result['filesize']);
		$this->folderId = $result['folder_id'];

		$this->createdBy = $result['createdbyuser'];
		$this->dateCreated = $result['created'];

		$this->modifiedBy = $result['modifiedbyuser'];
		$this->dateModified = $result['modified'];

		$this->checkedOutUser = $result['checkoutuser'];
		$this->dateCheckedout = $result['checkedout'];

		$this->owner = $result['owneruser'];

		$this->version = $result['major_version'] . '.' . $result['minor_version'];

		$this->immutable = ($result['immutable'] + 0)?_kt('Immutable'):'';

		$this->workflow = $result['workflow'];
		$this->workflowState = $result['workflowstate'];

		$this->oemDocumentNo = $result['oem_no'];
		if (empty($this->oemDocumentNo)) $this->oemDocumentNo = 'n/a';

		if (is_null($result['name']))
		{
			$this->fullpath = '(orphaned)';
		}
		else
		{
			$this->fullpath = $result['full_path'];
		}

		$this->mimeType = $result['mimetype'];
		$this->mimeIconPath = $result['mime_icon_path'];
		$this->mimeDisplay = $result['mime_display'];

		$this->storagePath = $result['storage_path'];
		$this->status = Document::getStatusString($result['status_id']);
	}

	protected function __get($property)
	{
		switch($property)
		{
			case null: return '';
			case 'DocumentID': return  (int) $this->document_id;
			case 'Relevance':
			case 'Rank': return (float) $this->rank;
			case 'Text': return (string) $this->text;
			case 'Title': return (string) $this->title;
			case 'FullPath': return (string)  $this->fullpath;
			case 'IsLive': return (bool) $this->live;
			case 'Filesize': return $this->filesize;
			case 'Version': return (string) $this->version;
			case 'Filename': return (string)$this->filename;
			case 'FolderId': return (int)$this->folderId;
			case 'OemDocumentNo': return (string) $this->oemDocumentNo;
			case 'Document':
					if (is_null($this->document))
					{
						$this->document = Document::get($this->document_id);
					}
					return $this->document;
			case 'IsAvailable':
				return $this->Document->isLive();
			case 'CheckedOutUser':
			case 'CheckedOutBy':
				return  (string) $this->checkedOutUser;
			case 'WorkflowOnly':
			case 'Workflow':
				return (string)$this->workflow;
			case 'WorkflowStateOnly':
			case 'WorkflowState':
				return (string)$this->workflowState;
			case 'WorkflowAndState':
				if (is_null($this->workflow))
				{
					return '';
				}
				return "$this->workflow - $this->workflowState";
			case 'MimeType':
				return (string) $this->mimeType;
			case 'MimeIconPath':
				return (string) $this->mimeIconPath;
			case 'MimeDisplay':
				return (string) $this->mimeDisplay;
			case 'DateCheckedOut':
				return (string) $this->dateCheckedout;
			case 'ModifiedBy':
				return (string) $this->modifiedBy;
			case 'DateModified':
				return (string) $this->dateModified;
			case 'CreatedBy':
				return (string) $this->createdBy;
			case 'DateCreated':
				return (string) $this->dateCreated;
			case 'Owner':
			case 'OwnedBy':
				return (string) $this->owner;
			case 'IsImmutable':
			case 'Immutable':
				return (bool) $this->immutable;
			case 'Status':
				return $this->status;
			case 'StoragePath':
				return $this->storagePath;
			case 'DocumentType':
				return $this->documentType;
			case 'Permissions':
				return 'not available';
			case 'CanBeReadByUser':
				if (!$this->live)
					return false;
				if (Permission::userHasDocumentReadPermission($this->Document))
					return true;
				if (Permission::adminIsInAdminMode())
					return true;
				return false;
			default:
				throw new Exception("Unknown property '$property' to get on QueryResultItem");
		}
		return ''; // Should not be reached
	}

	protected function __set($property, $value)
	{
		switch($property)
		{
			case 'Rank': $this->rank = number_format($value,2,'.',','); break;
			case 'Title': $this->title = $value; break;
			case 'Text': $this->text = $value; break;
			default:
				throw new Exception("Unknown property '$property' to set on QueryResultItem");
		}
	}
}

function MatchResultCompare($a, $b)
{
    if ($a->Rank == $b->Rank) {
        return 0;
    }
    return ($a->Rank < $b->Rank) ? -1 : 1;
}

abstract class Indexer
{
	/**
	 * Cache of extractors
	 *
	 * @var array
	 */
	private $extractorCache;

	/**
	 * Indicates if the indexer will do logging.
	 *
	 * @var boolean
	 */
	private $debug;
	/**
	 * Cache on mime related hooks
	 *
	 * @var unknown_type
	 */
	private $mimeHookCache;
	/**
	 * Cache on general hooks.
	 *
	 * @var array
	 */
	private $generalHookCache;

	/**
	 * This is a path to the extractors.
	 *
	 * @var string
	 */
	private $extractorPath;
	/**
	 * This is a path to the hooks.
	 *
	 * @var string
	 */
	private $hookPath;

	private $enabledExtractors;

	/**
	 * Initialise the indexer
	 *
	 */
	protected function __construct()
	{
		$config = KTConfig::getSingleton();

		$this->extractorCache	= array();
		$this->debug 			= $config->get('indexer/debug', true);
		$this->hookCache 		= array();
		$this->generalHookCache = array();
		$this->extractorPath 	= $config->get('indexer/extractorPath', 'extractors');
		$this->hookPath 		= $config->get('indexer/extractorHookPath','extractorHooks');

		$this->loadExtractorStatus();
	}

	/**
	 * Get the list if enabled extractors
	 *
	 */
	private function loadExtractorStatus()
	{
		$sql = "SELECT id, name FROM mime_extractors WHERE active=1";
		$rs = DBUtil::getResultArray($sql);
		$this->enabledExtractors = array();
		foreach($rs as $item)
		{
			$this->enabledExtractors[] = $item['name'];
		}
	}

	private function isExtractorEnabled($extractor)
	{
		return in_array($extractor, $this->enabledExtractors);
	}

	/**
	 * Returns a reference to the main class
	 *
	 * @return Indexer
	 */
	public static function get()
	{
		static $singleton = null;

		if (is_null($singleton))
		{
			$config = KTConfig::getSingleton();
			$classname = $config->get('indexer/coreClass');

			require_once('indexing/indexers/' . $classname . '.inc.php');

			if (!class_exists($classname))
			{
				throw new Exception("Class '$classname' does not exist.");
			}

			$singleton = new $classname;
		}

		return $singleton;
	}

	public abstract function deleteDocument($docid);

	/**
	 * Remove the association of all extractors to mime types on the database.
	 *
	 */
	public function clearExtractors()
	{
		global $default;

		$sql = "update mime_types set extractor_id=null";
		DBUtil::runQuery($sql);

		$sql = "delete from mime_extractors";
		DBUtil::runQuery($sql);

		if ($this->debug) $default->log->debug('clearExtractors');
	}

	/**
	 * lookup the name of the extractor class based on the mime type.
	 *
	 * @param string $type
	 * @return string
	 */
	public static function resolveExtractor($type)
	{
		global $default;
		$sql = "select extractor from mime_types where filetypes='$type'";
		$class = DBUtil::getOneResultKey($sql,'extractor');
		if (PEAR::isError($class))
		{
			$default->log->error("resolveExtractor: cannot resolve $type");
			return $class;
		}
		if ($this->debug) $default->log->debug(sprintf(_kt("resolveExtractor: Resolved '%s' from mime type '%s'."), $class, $type));
		return $class;
	}

	/**
	 * Return all the discussion text.
	 *
	 * @param int $docid
	 * @return string
	 */
	public static function getDiscussionText($docid)
	{
		$sql = "SELECT
					dc.subject, dc.body
				FROM
					discussion_threads dt
					INNER JOIN discussion_comments dc ON dc.thread_id=dt.id AND dc.id BETWEEN dt.first_comment_id AND dt.last_comment_id
				WHERE
					dt.document_id=$docid";
		$result = DBUtil::getResultArray($sql);
		$text = '';

		foreach($result as $record)
		{
			$text .= $record['subject'] . "\n" . $record['body'] . "\n";
		}

		return $text;
	}

	/**
	 * Schedule the indexing of a document.
	 *
	 * @param string $document
	 * @param string $what
	 */
    public static function index($document, $what='C')
    {
    	global $default;

        $document_id = $document->getId();
        $userid=$_SESSION['userID'];
        if (empty($userid)) $userid=1;

        // we dequeue the document so that there are no issues when enqueuing
        Indexer::unqueueDocument($document_id);

        // enqueue item
        $sql = "INSERT INTO index_files(document_id, user_id, what) VALUES($document_id, $userid, '$what')";
        DBUtil::runQuery($sql);

        $default->log->debug("index: Queuing indexing of $document_id");
    }

	public static function reindexQueue()
	{
		$sql = "UPDATE index_files SET processdate = null";
		DBUtil::runQuery($sql);
	}

	public static function reindexDocument($documentId)
	{
		$sql = "UPDATE index_files SET processdate=null, status_msg=null WHERE document_id=$documentId";
		DBUtil::runQuery($sql);
	}



    public static function indexAll()
    {
    	 $userid=$_SESSION['userID'];
    	 if (empty($userid)) $userid=1;
    	$sql = "INSERT INTO index_files(document_id, user_id, what) SELECT id, $userid, 'C' FROM documents WHERE status_id=1";
    	DBUtil::runQuery($sql);
    }

    /**
     * Clearout the scheduling of documents that no longer exist.
     *
     */
    public static function clearoutDeleted()
    {
    	global $default;

        $sql = 'DELETE FROM
					index_files AS iff USING index_files AS iff, documents
				WHERE
					NOT EXISTS(
						SELECT
							d.id
						FROM
							documents AS d
							INNER JOIN document_metadata_version dmv ON d.metadata_version_id=dmv.id
						WHERE
							iff.document_id = d.id OR dmv.status_id=3
					);';
        DBUtil::runQuery($sql);

        $default->log->debug("Indexer::clearoutDeleted: removed documents from indexing queue that have been deleted");
    }


    /**
     * Check if a document is scheduled to be indexed
     *
     * @param mixed $document This may be a document or document id
     * @return boolean
     */
    public static function isDocumentScheduled($document)
    {
    	if (is_numeric($document))
    	{
    		$docid = $document;
    	}
    	else if ($document instanceof Document)
    	{
    		$docid = $document->getId();
    	}
    	else
    	{
    		return false;
    	}
    	$sql = "SELECT 1 FROM index_files WHERE document_id=$docid";
    	$result = DBUtil::getResultArray($sql);
    	return count($result) > 0;
    }

    /**
     * Filters text removing redundant characters such as continuous newlines and spaces.
     *
     * @param string $filename
     */
    private function filterText($filename)
    {
    	$content = file_get_contents($filename);

    	$src = array("([\r\n])","([\n][\n])","([\n])","([\t])",'([ ][ ])');
    	$tgt = array("\n","\n",' ',' ',' ');

    	// shrink what is being stored.
    	do
    	{
    		$orig = $content;
    		$content = preg_replace($src, $tgt, $content);
    	} while ($content != $orig);

    	return file_put_contents($filename, $content) !== false;
    }

    /**
     * Load hooks for text extraction process.
     *
     */
    private function loadExtractorHooks()
    {
    	$this->generalHookCache = array();
    	$this->mimeHookCache = array();

		$dir = opendir($this->hookPath);
		while (($file = readdir($dir)) !== false)
		{
			if (substr($file,-12) == 'Hook.inc.php')
			{
				require_once($this->hookPath . '/' . $file);
				$class = substr($file, 0, -8);

				if (!class_exists($class))
				{
					continue;
				}

				$hook = new $class;
				if (!($class instanceof ExtractorHook))
				{
					continue;
				}

				$mimeTypes = $hook->registerMimeTypes();
				if (is_null($mimeTypes))
				{
					$this->generalHookCache[] = & $hook;
				}
				else
				{
					foreach($mimeTypes as $type)
					{
						$this->mimeHookCache[$type][] = & $hook;
					}
				}

			}
        }
        closedir($dir);
    }

    /**
     * This is a refactored function to execute the hooks.
     *
     * @param DocumentExtractor $extractor
     * @param string $phase
     * @param string $mimeType Optional. If set, indicates which hooks must be used, else assume general.
     */
    private function executeHook($extractor, $phase, $mimeType = null)
    {
    	$hooks = array();
		if (is_null($mimeType))
		{
			$hooks = $this->generalHookCache;
		}
		else
		{
			if (array_key_exists($mimeType, $this->mimeHookCache))
			{
				$hooks = $this->mimeHookCache[$mimeType];
			}
		}
		if (empty($hooks))
		{
			return;
		}

		foreach($hooks as $hook)
		{
			$hook->$phase($extractor);
		}
    }

    private function doesDiagnosticsPass($simple=false)
    {
		global $default;

    	$config =& KTConfig::getSingleton();
		// create a index log lock file in case there are errors, and we don't need to log them forever!
    	// this function will create the lockfile if an error is detected. It will be removed as soon
    	// as the problems with the indexer are removed.
    	$lockFile = $config->get('cache/cacheDirectory') . '/index.log.lock';

    	$diagnosis = $this->diagnose();
    	if (!is_null($diagnosis))
    	{
			if (!is_file($lockFile))
			{
				$default->log->error(_kt('Indexer problem: ') . $diagnosis);
			}
			touch($lockFile);
    		return false;
    	}

    	if ($simple)
    	{
    		return true;
    	}

    	$diagnosis = $this->diagnoseExtractors();
    	if (!empty($diagnosis))
    	{
    		if (!is_file($lockFile))
			{
	    		foreach($diagnosis as $diag)
	    		{
    				$default->log->error(sprintf(_kt('%s problem: %s'), $diag['name'],$diag['diagnosis']));
    			}
			}
			touch($lockFile);
    		return false;
    	}

    	if (is_file($lockFile))
    	{
    		$default->log->info(_kt('Issues with the indexer have been resolved!'));
    		unlink($lockFile);
    	}

    	return true;
    }

    /**
     * This does the initial mime type association between mime types and text extractors
     *
     */
    public function checkForRegisteredTypes()
    {
    	global $default;

    	// we are only doing this once!
    	$initRegistered = KTUtil::getSystemSetting('mimeTypesRegistered', false);
    	if ($initRegistered)
    	{
    		return;
    	}
    	if ($this->debug) $default->log->debug('checkForRegisteredTypes: start');

    	$date = date('Y-m-d H:i');
    	$sql = "UPDATE scheduler_tasks SET run_time='$date'";
    	DBUtil::runQuery($sql);

    	$this->registerTypes(true);

    	$disable = array(
    		OS_WINDOWS=>array('PSExtractor'),
    		OS_UNIX => array()
    	);

    	$disableForOS = OS_WINDOWS?$disable[OS_WINDOWS]:$disable[OS_UNIX];

		foreach($disableForOS as $extractor)
		{
    		$sql = "UPDATE mime_extractors SET active=0 WHERE name='$extractor'";
    		DBUtil::runQuery($sql);
    		$default->log->info("checkForRegisteredTypes: disabled '$extractor'");
    	}

    	if ($this->debug) $default->log->debug('checkForRegisteredTypes: done');
    	KTUtil::setSystemSetting('mimeTypesRegistered', true);
    }

    private function updatePendingDocumentStatus($documentId, $message, $level)
    {
    	$this->indexingHistory .=  "\n" . $level . ': ' . $message;
    	$message = sanitizeForSQL($this->indexingHistory);
    	$sql = "UPDATE index_files SET status_msg='$message' WHERE document_id=$documentId";
    	DBUtil::runQuery($sql);
    }

     /**
     *
     * @param int $documentId
     * @param string $message
     * @param string $level This may be info, error, debug
     */
    private function logPendingDocumentInfoStatus($documentId, $message, $level)
    {
		$this->updatePendingDocumentStatus($documentId, $message, $level);
		global $default;

		switch ($level)
		{
			case 'debug':
				if ($this->debug)
				{
					$default->log->debug($message);
				}
				break;
			default:
				$default->log->$level($message);
		}
    }



	public function getExtractor($extractorClass)
	{
		$includeFile = SEARCH2_INDEXER_DIR . 'extractors/' . $extractorClass . '.inc.php';
		if (!file_exists($includeFile))
		{
			throw new Exception("Extractor file does not exist: $includeFile");
		}

		require_once($includeFile);

        if (!class_exists($extractorClass))
        {
        	throw new Exception("Extractor '$classname' not defined in file: $includeFile");
        }

        $extractor = new $extractorClass();

        if (!($extractor instanceof DocumentExtractor))
		{
        	throw new Exception("Class $classname was expected to be of type DocumentExtractor");
		}

        return $extractor;
	}

	public static function getIndexingQueue($problemItemsOnly=true)
	{

		if ($problemItemsOnly)
		{
			$sql = "SELECT
	        			iff.document_id, iff.indexdate, mt.filetypes, mt.mimetypes, me.name as extractor, iff.what, iff.status_msg, dcv.filename
					FROM
						index_files iff
						INNER JOIN documents d ON iff.document_id=d.id
						INNER JOIN document_metadata_version dmv ON d.metadata_version_id=dmv.id
						INNER JOIN document_content_version dcv ON dmv.content_version_id=dcv.id
						INNER JOIN mime_types mt ON dcv.mime_id=mt.id
						LEFT JOIN mime_extractors me ON mt.extractor_id=me.id
	 				WHERE
	 					(iff.status_msg IS NOT NULL AND iff.status_msg <> '') AND d.status_id=1
					ORDER BY indexdate ";
		}
		else
		{
			$sql = "SELECT
						iff.document_id, iff.indexdate, mt.filetypes, mt.mimetypes, me.name as extractor, iff.what, iff.status_msg, dcv.filename
					FROM
						index_files iff
						INNER JOIN documents d ON iff.document_id=d.id
						INNER JOIN document_metadata_version dmv ON d.metadata_version_id=dmv.id
						INNER JOIN document_content_version dcv ON dmv.content_version_id=dcv.id
						INNER JOIN mime_types mt ON dcv.mime_id=mt.id
						LEFT JOIN mime_extractors me ON mt.extractor_id=me.id
	 				WHERE
	 					(iff.status_msg IS NULL or iff.status_msg = '') AND d.status_id=1
					ORDER BY indexdate ";
		}
		$aResult = DBUtil::getResultArray($sql);

		return $aResult;
	}

	public static function getPendingIndexingQueue()
	{
		return Indexer::getIndexingQueue(false);
	}

    /**
     * The main function that may be called repeatedly to index documents.
     *
     * @param int $max Default 20
     */
    public function indexDocuments($max=null)
    {
    	global $default;
    	$config =& KTConfig::getSingleton();

    	/*$indexLockFile = $config->get('cache/cacheDirectory') . '/main.index.lock';
    	if (is_file($indexLockFile))
    	{
			$default->log->info('indexDocuments: main.index.lock seems to exist. it could be that the indexing is still underway.');
			$default->log->info('indexDocuments: Remove "' . $indexLockFile . '" if the indexing is not running or extend the frequency at which the background task runs!');
			return;
    	}
    	touch($indexLockFile);*/


    	$this->checkForRegisteredTypes();

    	if ($this->debug) $default->log->debug('indexDocuments: start');
    	if (!$this->doesDiagnosticsPass())
    	{
    		//unlink($indexLockFile);
    		if ($this->debug) $default->log->debug('indexDocuments: stopping - diagnostics problem. The dashboard will provide more information.');
    		return;
    	}

    	if (is_null($max))
    	{
			$max = $config->get('indexer/batchDocuments',20);
    	}

    	$this->loadExtractorHooks();

    	Indexer::clearoutDeleted();

    	$date = date('Y-m-d H:j:s');
    	// identify the indexers that must run
        // mysql specific limit!
        $sql = "SELECT
        			iff.document_id, mt.filetypes, mt.mimetypes, me.name as extractor, iff.what
				FROM
					index_files iff
					INNER JOIN documents d ON iff.document_id=d.id
					INNER JOIN document_metadata_version dmv ON d.metadata_version_id=dmv.id
					INNER JOIN document_content_version dcv ON dmv.content_version_id=dcv.id
					INNER JOIN mime_types mt ON dcv.mime_id=mt.id
					LEFT JOIN mime_extractors me ON mt.extractor_id=me.id
 				WHERE
 					(iff.processdate IS NULL or iff.processdate < cast(cast('$date' as date) -1 as date)) AND dmv.status_id=1
				ORDER BY indexdate
 					LIMIT $max";
        $result = DBUtil::getResultArray($sql);
        if (PEAR::isError($result))
        {
        	//unlink($indexLockFile);
        	if ($this->debug) $default->log->debug('indexDocuments: stopping - db error');
        	return;
        }
        KTUtil::setSystemSetting('luceneIndexingDate', time());

        // bail if no work to do
        if (count($result) == 0)
        {
        	//unlink($indexLockFile);
        	if ($this->debug) $default->log->debug('indexDocuments: stopping - no work to be done');
            return;
        }

        // identify any documents that need indexing and mark them
        // so they are not taken in a followup run
		$ids = array();
		foreach($result as $docinfo)
		{
			$ids[] = $docinfo['document_id'];
		}

		// mark the documents as being processed

        $ids=implode(',',$ids);
        $sql = "UPDATE index_files SET processdate='$date' WHERE document_id in ($ids)";
        DBUtil::runQuery($sql);

        $extractorCache = array();
        $storageManager = KTStorageManagerUtil::getSingleton();

        $tempPath = $config->get("urls/tmpDirectory");

        foreach($result as $docinfo)
        {
        	$docId=$docinfo['document_id'];
        	$extension=$docinfo['filetypes'];
        	$mimeType=$docinfo['mimetypes'];
        	$extractorClass=$docinfo['extractor'];
        	$indexDocument = in_array($docinfo['what'], array('A','C'));
        	$indexDiscussion = in_array($docinfo['what'], array('A','D'));
			$this->indexingHistory = '';

        	$this->logPendingDocumentInfoStatus($docId, sprintf(_kt("Indexing docid: %d extension: '%s' mimetype: '%s' extractor: '%s'"), $docId, $extension,$mimeType,$extractorClass), 'debug');


        	if (empty($extractorClass))
        	{
        		Indexer::unqueueDocument($docId, sprintf(_kt("No extractor for docid: %d"),$docId));
        		continue;
        	}

        	if (!$this->isExtractorEnabled($extractorClass))
			{
				$this->logPendingDocumentInfoStatus($docId, sprintf(_kt("diagnose: Not indexing docid: %d because extractor '%s' is disabled."), $docId, $extractorClass), 'info');
				continue;
			}

        	if ($this->debug)
        	{
        		$this->logPendingDocumentInfoStatus($docId, sprintf(_kt("Processing docid: %d.\n"),$docId), 'info');
        	}

        	$removeFromQueue = true;
        	if ($indexDocument)
        	{
        		if (array_key_exists($extractorClass, $extractorCache))
        		{
        			$extractor = $extractorCache[$extractorClass];
        		}
        		else
        		{
        			$extractor = $extractorCache[$extractorClass] = $this->getExtractor($extractorClass);
        		}

				if (!($extractor instanceof DocumentExtractor))
				{
					$this->logPendingDocumentInfoStatus($docId, sprintf(_kt("indexDocuments: extractor '%s' is not a document extractor class."),$extractorClass), 'error');
					continue;
				}

        		$document = Document::get($docId);
        		$version = $document->getMajorVersionNumber() . '.' . $document->getMinorVersionNumber();
        		$sourceFile = $storageManager->temporaryFile($document);

        		if (empty($sourceFile) || !is_file($sourceFile))
        		{
        			Indexer::unqueueDocument($docId,sprintf(_kt("indexDocuments: source file '%s' for document %d does not exist."),$sourceFile,$docId), 'error');
        			continue;
        		}

        		if ($extractor->needsIntermediateSourceFile())
        		{
        			$extension =  pathinfo($document->getFileName(), PATHINFO_EXTENSION);

        			$intermediate = $tempPath . '/'. $docId . '.' . $extension;
        			$result = @copy($sourceFile, $intermediate);
        			if ($result === false)
        			{
        				$this->logPendingDocumentInfoStatus($docId, sprintf(_kt("Could not create intermediate file from document %d"),$docId), 'error');
        				// problem. lets try again later. probably permission related. log the issue.
        				continue;
        			}
        			$sourceFile = $intermediate;
        		}

        		$targetFile = tempnam($tempPath, 'ktindexer');

        		$extractor->setSourceFile($sourceFile);
        		$extractor->setMimeType($mimeType);
        		$extractor->setExtension($extension);
        		$extractor->setTargetFile($targetFile);
        		$extractor->setDocument($document);
        		$extractor->setIndexingStatus(null);
        		$extractor->setExtractionStatus(null);

        		$this->logPendingDocumentInfoStatus($docId, sprintf(_kt("Extra Info docid: %d Source File: '%s' Target File: '%s'"),$docId,$sourceFile,$targetFile), 'debug');

        		$this->executeHook($extractor, 'pre_extract');
				$this->executeHook($extractor, 'pre_extract', $mimeType);
				$removeFromQueue = false;

        		if ($extractor->extractTextContent())
        		{
        			// the extractor may need to create another target file
        			$targetFile = $extractor->getTargetFile();

        			$extractor->setExtractionStatus(true);
        			$this->executeHook($extractor, 'pre_index');
					$this->executeHook($extractor, 'pre_index', $mimeType);

					$title = $document->getName();
        			if ($indexDiscussion)
        			{
        				$indexStatus = $this->indexDocumentAndDiscussion($docId, $targetFile, $title, $version);
        				$removeFromQueue = $indexStatus;
        				if (!$indexStatus)
        				{
        					$this->logPendingDocumentInfoStatus($docId, sprintf(_kt("Problem indexing document %d - indexDocumentAndDiscussion"),$docId), 'error');
        				}

        				$extractor->setIndexingStatus($indexStatus);
        			}
        			else
        			{
        				if (!$this->filterText($targetFile))
        				{
        					$this->logPendingDocumentInfoStatus($docId, sprintf(_kt("Problem filtering document %d"),$docId), 'error');
        				}
						else
						{
							$indexStatus = $this->indexDocument($docId, $targetFile, $title, $version);
							$removeFromQueue = $indexStatus;

							if (!$indexStatus)
							{
								$this->logPendingDocumentInfoStatus($docId, sprintf(_kt("Problem indexing document %d - indexDocument"),$docId), 'error');
								$this->logPendingDocumentInfoStatus($docId, '<output>' . $extractor->output . '</output>', 'error');
							}

        					$extractor->setIndexingStatus($indexStatus);
						}
        			}

					$this->executeHook($extractor, 'post_index', $mimeType);
        			$this->executeHook($extractor, 'post_index');
        		}
        		else
        		{
        			$extractor->setExtractionStatus(false);
        			$this->logPendingDocumentInfoStatus($docId, sprintf(_kt("Could not extract contents from document %d"),$docId), 'error');
					$this->logPendingDocumentInfoStatus($docId, '<output>' . $extractor->output . '</output>', 'error');
        		}

				$this->executeHook($extractor, 'post_extract', $mimeType);
        		$this->executeHook($extractor, 'post_extract');

        		if ($extractor->needsIntermediateSourceFile())
        		{
        			@unlink($sourceFile);
        		}

        		@unlink($targetFile);

        	}
        	else
        	{
				$this->indexDiscussion($docId);
        	}

        	if ($removeFromQueue)
        	{
        		Indexer::unqueueDocument($docId, sprintf(_kt("Done indexing docid: %d"),$docId));
        	}
        	else
        	{
        		if ($this->debug) $default->log->debug(sprintf(_kt("Document docid: %d was not removed from the queue as it looks like there was a problem with the extraction process"),$docId));
        	}
        }
        if ($this->debug) $default->log->debug('indexDocuments: done');
        //unlink($indexLockFile);
    }

    public function migrateDocuments($max=null)
    {
    	global $default;

    	$default->log->info(_kt('migrateDocuments: starting'));

    	if (!$this->doesDiagnosticsPass(true))
    	{
    		$default->log->info(_kt('migrateDocuments: stopping - diagnostics problem. The dashboard will provide more information.'));
    		return;
    	}

    	if (KTUtil::getSystemSetting('migrationComplete') == 'true')
    	{
    		$default->log->info(_kt('migrateDocuments: stopping - migration is complete.'));
    		return;
    	}

    	$config =& KTConfig::getSingleton();
    	if (is_null($max))
    	{
			$max = $config->get('indexer/batchMigrateDocument',500);
    	}

    	$lockFile = $config->get('cache/cacheDirectory') . '/migration.lock';
    	if (is_file($lockFile))
    	{
    		$default->log->info(_kt('migrateDocuments: stopping - migration lockfile detected.'));
    		return;
    	}
    	touch($lockFile);

    	$startTime = KTUtil::getSystemSetting('migrationStarted');
    	if (is_null($startTime))
    	{
    		KTUtil::setSystemSetting('migrationStarted', time());
    	}

    	$maxLoops = 5;

    	$max = ceil($max / $maxLoops);

		$start =KTUtil::getBenchmarkTime();
		$noDocs = false;
		$numDocs = 0;

    	for($loop=0;$loop<$maxLoops;$loop++)
    	{

    		$sql = "SELECT
        			document_id, document_text
				FROM
					document_text
				ORDER BY document_id
 					LIMIT $max";
    		$result = DBUtil::getResultArray($sql);
    		if (PEAR::isError($result))
    		{
    			$default->log->info(_kt('migrateDocuments: db error'));
    			break;
    		}

    		$docs = count($result);
    		if ($docs == 0)
    		{
    			$noDocs = true;
    			break;
    		}
    		$numDocs += $docs;

    		foreach($result as $docinfo)
    		{
    			$docId = $docinfo['document_id'];

    			$document = Document::get($docId);
    			if (PEAR::isError($document) || is_null($document))
    			{
    				$sql = "DELETE FROM document_text WHERE document_id=$docId";
    				DBUtil::runQuery($sql);
    				$default->log->error(sprintf(_kt('migrateDocuments: Could not get document %d\'s document! Removing content!'),$docId));
    				continue;
    			}

    			$version = $document->getMajorVersionNumber() . '.' . $document->getMinorVersionNumber();

    			$targetFile = tempnam($tempPath, 'ktindexer');

    			if (file_put_contents($targetFile, $docinfo['document_text']) === false)
    			{
    				$default->log->error(sprintf(_kt('migrateDocuments: Cannot write to \'%s\' for document id %d'), $targetFile, $docId));
    				continue;
    			}
    			// free memory asap ;)
    			unset($docinfo['document_text']);

    			$title = $document->getName();

    			$indexStatus = $this->indexDocumentAndDiscussion($docId, $targetFile, $title, $version);

    			if ($indexStatus)
    			{
    				$sql = "DELETE FROM document_text WHERE document_id=$docId";
    				DBUtil::runQuery($sql);
    			}
    			else
    			{
    				$default->log->error(sprintf(_kt("migrateDocuments: Problem indexing document %d"), $docId));
    			}

    			@unlink($targetFile);
    		}
    	}

    	@unlink($lockFile);

    	$time = KTUtil::getBenchmarkTime() - $start;

    	KTUtil::setSystemSetting('migrationTime', KTUtil::getSystemSetting('migrationTime',0) + $time);
    	KTUtil::setSystemSetting('migratedDocuments', KTUtil::getSystemSetting('migratedDocuments',0) + $numDocs);

    	$default->log->info(sprintf(_kt('migrateDocuments: stopping - done in %d seconds!'), $time));
    	if ($noDocs)
    	{
	    	$default->log->info(_kt('migrateDocuments: Completed!'));
	    	KTUtil::setSystemSetting('migrationComplete', 'true');
	    	schedulerUtil::deleteByName('Index Migration');
	    	$default->log->debug(_kt('migrateDocuments: Disabling \'Index Migration\' task by removing scheduler entry.'));
    	}
    }

    /**
     * Index a document. The base class must override this function.
     *
     * @param int $docId
     * @param string $textFile
     */
    protected abstract function indexDocument($docId, $textFile, $title, $version);


    public function updateDocumentIndex($docId, $text)
    {
    	$config = KTConfig::getSingleton();
    	$tempPath = $config->get("urls/tmpDirectory");
    	$tempFile = tempnam($tempPath,'ud_');

    	file_put_contents($tempFile, $text);

    	$document = Document::get($docId);
    	$title = $document->getDescription();
    	$version = $document->getVersion();

    	$result = $this->indexDocument($docId, $tempFile, $title, $version);

    	if (file_exists($tempFile))
    	{
    		unlink($tempFile);
    	}

    	return $result;
    }

    /**
     * Index a discussion. The base class must override this function.
     *
     * @param int $docId
     */
    protected abstract function indexDiscussion($docId);

    /**
     * Diagnose the indexer. e.g. Check that the indexing server is running.
     *
     */
	public abstract function diagnose();

    /**
     * Diagnose the extractors.
     *
     * @return array
     */
    public function diagnoseExtractors()
    {
		$diagnosis = $this->_diagnose($this->extractorPath, 'DocumentExtractor', 'Extractor.inc.php');
		$diagnosis = array_merge($diagnosis, $this->_diagnose($this->hookPath, 'Hook', 'Hook.inc.php'));

		return $diagnosis;
    }

    /**
     * This is a refactored diagnose function.
     *
     * @param string $path
     * @param string $class
     * @param string $extension
     * @return array
     */
    private function _diagnose($path, $baseclass, $extension)
    {
    	global $default;

    	$diagnoses = array();
    	$dir = opendir($path);
    	$extlen = - strlen($extension);

		while (($file = readdir($dir)) !== false)
		{
			if (substr($file,0,1) == '.')
			{
				continue;
			}
			if (substr($file,$extlen) != $extension)
			{
				$default->log->error(sprintf(_kt("diagnose: '%s' does not have extension '%s'."), $file, $extension));
				continue;
			}

			require_once($path . '/' . $file);

			$class = substr($file, 0, -8);
			if (!class_exists($class))
			{
				$default->log->error(sprintf(_kt("diagnose: class '%s' does not exist."), $class));
				continue;
			}

			if (!$this->isExtractorEnabled($class))
			{
				$default->log->debug(sprintf(_kt("diagnose: extractor '%s' is disabled."), $class));
				continue;
			}

			$extractor = new $class();
			if (!is_a($extractor, $baseclass))
			{
				$default->log->error(sprintf(_kt("diagnose(): '%s' is not of type DocumentExtractor"), $class));
				continue;
			}

			$types = $extractor->getSupportedMimeTypes();
			if (empty($types))
			{
				if ($this->debug) $default->log->debug(sprintf(_kt("diagnose: class '%s' does not support any types."), $class));
				continue;
			}

			$diagnosis=$extractor->diagnose();
			if (empty($diagnosis))
			{
				continue;
			}
			$diagnoses[$class] = array(
			'name'=>$extractor->getDisplayName(),
			'diagnosis'=>$diagnosis
			);

        }
        closedir($dir);

        return $diagnoses;
    }


    /**
     * Register the extractor types.
     *
     * @param boolean $clear. Optional. Defaults to false.
     */
    public function registerTypes($clear=false)
    {
    	if ($clear)
    	{
    		$this->clearExtractors();
    	}
    	$dir = opendir($this->extractorPath);
		while (($file = readdir($dir)) !== false)
		{
			if (substr($file,-17) == 'Extractor.inc.php')
			{
				require_once($this->extractorPath . '/' . $file);
				$class = substr($file, 0, -8);

				if (!class_exists($class))
				{
					// if the class does not exist, we can't do anything.
					continue;
				}

				$extractor = new $class;
				if ($extractor instanceof DocumentExtractor)
				{
					$extractor->registerMimeTypes();
				}
			}
        }
        closedir($dir);
    }

    /**
     * This is used as a possible obtimisation effort. It may be overridden in that case.
     *
     * @param int $docId
     * @param string $textFile
     */
    protected function indexDocumentAndDiscussion($docId, $textFile, $title, $version)
    {
    	$this->indexDocument($docId, $textFile, $title, $version);
    	$this->indexDiscussion($docId);
    }

    /**
     * Remove the document from the queue. This is normally called when it has been processed.
     *
     * @param int $docid
     */
    public static function unqueueDocument($docid, $reason=false, $level='debug')
    {
    	$sql = "DELETE FROM index_files WHERE document_id=$docid";
        DBUtil::runQuery($sql);
        if ($reason !== false)
        {
        	global $default;
        	$default->log->$level("Indexer: removing document $docid from the queue - $reason");
        }
    }

    /**
     * Run a query on the index.
     *
     * @param string $query
     * @return array
     */
    public abstract function query($query);

	/**
	 * Converts an integer to a string that can be easily compared and reversed.
	 *
	 * @param int $int
	 * @return string
	 */
	public static function longToString($int)
    {
    	$maxlen = 14;

        $a2z = array('a','b','c','d','e','f','g','h','i','j');
        $o29 = array('0','1','2','3','4','5','6','7','8','9');
        $l = str_pad('',$maxlen - strlen("$int"),'0') . $int;

        return str_replace($o29,  $a2z, $l);
    }

    /**
     * Converts a string to an integer.
     *
     * @param string $str
     * @return int
     */
	public static function stringToLong($str)
    {
        $a2z = array('a','b','c','d','e','f','g','h','i','j');
        $o29 = array('0','1','2','3','4','5','6','7','8','9');

        $int = str_replace($a2z, $o29, $str) + 0;

        return $int;
    }

    /**
     * Possibly we can optimise indexes. This method must be overriden.
     * The new function must call the parent!
     *
     */
    public function optimise()
    {
    	KTUtil::setSystemSetting('luceneOptimisationDate', time());
    }

    /**
     * Shuts down the indexer
     *
     */
    public function shutdown()
    {
    	// do nothing generally
    }

    /**
     * Returns the name of the indexer.
     *
     * @return string
     */
    public abstract function getDisplayName();


    /**
     * Returns the number of non-deleted documents in the index.
     *
     * @return int
     */
    public abstract function getDocumentsInIndex();

    /**
     * Returns the path to the index directory
     *
     * @return string
     */
    public function getIndexDirectory()
    {
    	$config = KTConfig::getSingleton();
    	$directory = $config->get('indexer/luceneDirectory');
    	return $directory;
    }
}

?>