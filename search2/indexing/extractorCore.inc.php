<?php

/**
 * DocumentExtractor is the base class for all text extractors.
 *
 */
abstract class DocumentExtractor
{
	/**
	 * The source filename from which to extract text.
	 *
	 * @var string
	 */
	protected $sourcefile;

	/**
	 * The target filename, where the extracted text must be stored.
	 *
	 * @var string
	 */
	protected $targetfile;

	/**
	 * The mime type of the source file.
	 *
	 * @var string
	 */
	protected $mimetype;

	/**
	 * The extension of the source file.
	 *
	 * @var string
	 */
	protected $extension;

	/**
	 * Reference to the document being indexed.
	 *
	 * @var Document
	 */
	protected $document;

	/**
	 * Indicates if the extractor needs an intermediate file or not.
	 * Generally the source file will be a file within the respository itself. Some extractors may
	 * require the source file to have the correct extension. Setting this to true will result in
	 * a file being created with the extension of the file. It is ideal to disable this if possible.
	 *
	 * @var boolean
	 */
	protected $needsIntermediate;

	/**
	 * The status of the extraction. If null, the extraction has not been done yet.
	 *
	 * @var boolean
	 */
	protected $extractionStatus;

	/**
	 * The status of the indexing. If null, the indexing has not been done yet.
	 *
	 * @var boolean
	 */
	protected $indexStatus;


	public function __construct()
	{
		$this->needsIntermediate=false;
		$this->extractionStatus = null;
		$this->indexStatus = null;
	}

	/**
	 * Sets the status of the indexing.
	 *
	 * @param unknown_type $status
	 */
	public function setIndexingStatus($status)
	{
		$this->indexStatus = $status;
	}
	/**
	 * Returns the indexing status.
	 *
	 * @return boolean
	 */
	public function getIndexingStatus()
	{
		return $this->indexStatus;
	}

	/**
	 * Sets the extraction status.
	 *
	 * @param boolean $status
	 */
	public function setExtractionStatus($status)
	{
		$this->extractionStatus = $status;
	}
	/**
	 * Return the extraction status.
	 *
	 * @return boolean
	 */
	public function getExtractionStatus()
	{
		return $this->extractionStatus;
	}

	/**
	 * This associates all the mime types associated with the extractor class.
	 *
	 */
	public function registerMimeTypes()
	{
		$types = $this->getSupportedMimeTypes();
		if (empty($types))
		{
			return;
		}
		$classname=get_class($this);

		foreach($types as $type)
		{
			$sql = "update mime_types set extractor='$classname'  where mimetypes='$type' and extractor is null";
			DBUtil::runQuery($sql);
		}
	}

	/**
	 * Indicates if an intermediate file is required.
	 *
	 * @param $value boolean Optional. If set, we set the value.
	 * @return boolean
	 */
	public function needsIntermediateSourceFile($value = null)
	{
		if (!is_null($value))
		{
			$this->needsIntermediate = $value;
		}
		return $this->needsIntermediate;
	}

	/**
	 * Sets the source filename for the document extractor.
	 *
	 * @param string $sourcefile
	 */
	public function setSourceFile($sourcefile)
	{
		$this->sourcefile=$sourcefile;
	}

	/**
	 * Returns the source file name.
	 *
	 * @return string
	 */
	public function getSourceFile() { return $this->sourcefile; }

	/**
	 * Sets the source file's mime type.
	 *
	 * @param string $mimetype
	 */
	public function setMimeType($mimetype)
	{
		$this->mimetype=$mimetype;
	}
	/**
	 * Returns the mime type for the source file.
	 *
	 * @return string
	 */
	public function getMimeType() { return $this->mimetype; }

	/**
	 * Indicates the extension for the source file.
	 *
	 * @param string $extension
	 */
	public function setExtension($extension)
	{
		$this->extension=$extension;
	}
	/**
	 * Returns the extension of the source file.
	 *
	 * @return string
	 */
	public function getExtension() { return $this->extension; }

	/**
	 * Sets the file name of the target text file.
	 *
	 * @param string $targetfile
	 */
	public function setTargetFile($targetfile)
	{
		$this->targetfile=$targetfile;
	}

	/**
	 * Gets the file name of the target text file containing the extracted text.
	 *
	 * @return unknown
	 */
	public function getTargetFile() { return $this->targetfile; }

	/**
	 * Filter function that may be applied after extraction. This may be overridden.
	 *
	 * @param string $text
	 * @return string
	 */
	protected function filter($text)
	{
		return $text;
	}

	/**
	 * Set the document that will be indexed.
	 *
	 * @param Document $document
	 */
	public function setDocument($document)
	{
		$this->document = $document;
	}

	/**
	 * Returns a reference to the document.
	 *
	 * @return string
	 */
	public function getDocument()
	{
		return $this->document;
	}

	/**
	 * Returns an array of supported mime types.
	 * e.g. return array('plain/text');
	 *
	 *
	 * @return array
	 *
	 */
	public abstract function getSupportedMimeTypes();

	/**
	 * Extracts the content from the source file.
	 *
	 * @return boolean
	 */
	public abstract function extractTextContent();

	/**
	 * Returns a friendly name for the document text extractor.
	 *
	 * @return string
	 */
	public abstract function getDisplayName();

	/**
	 * Attempts to diagnose any problems with the indexing process.
	 *
	 * @return string
	 */
	public abstract function diagnose();

}

/**
 * This class extends the document extractor to execute some command line application.
 * The getCommandLine() method needs to be overridden.
 *
 */
abstract class ExternalDocumentExtractor extends DocumentExtractor
{
	/**
	 * Initialise the extractor.
	 *
	 */
	public function __construct()
	{
		parent::__construct();
		putenv('LANG=en_US.UTF-8');
	}

	/**
	 * Executes a command. Returns true if successful.
	 *
	 * @param string $cmd A command line instruction.
	 * @return boolean
	 */
	protected  function exec($cmd)
	{
		$aRet = KTUtil::pexec($cmd);
		return $aRet['ret'] == 0;
	}

	/**
	 * Returns the command line string to be executed.
	 * The command returned should include the target filename.
	 *
	 * @return string
	 */
	protected function getCommandLine()
	{
		throw new Exception('getCommandLine is not implemented');
	}

	/**
	 * Executes the command that executes the command.
	 * Returns true if success.
	 *
	 * @return boolean
	 */
	public function extractTextContent()
	{
		global $default;

		$cmdline = $this->getCommandLine();

		$class = get_class($this);
		$default->log->debug("$class: "  . $cmdline);

		return $this->exec($cmdline);
	}

}

/**
 * An extension to the extenal document extractor. A derived class simply needs
 * to implement a constructor and getSupportedMimeTypes().
 *
 */
abstract class ApplicationExtractor extends ExternalDocumentExtractor
{
	/**
	 * The full path to the application that will be run. This will be resolved from
	 * the path or using the config file.
	 *
	 * @var string
	 */
	private $application;
	/**
	 * The command name of the application that can be run.
	 *
	 * @var string
	 */
	private $command;
	/**
	 * This is the friendly name for the extractor.
	 *
	 * @var string
	 */
	private $displayname;
	/**
	 * The command line parameters for the application.
	 * This may include {source} and {target} where substitutions will be done.
	 *
	 * @var string
	 */
	private $params;

	/**
	 * Initialise the extractor.
	 *
	 * @param string $section The section in the config file.
	 * @param string $appname The application name in the config file.
	 * @param string $command The command that can be run.
	 * @param string $displayname
	 * @param string $params
	 */
	public function __construct($section, $appname, $command, $displayname, $params)
	{
		parent::__construct();

		$this->application = KTUtil::findCommand("$section/$appname", $command);
		$this->command = $command;
		$this->displayname = $displayname;
		$this->params = $params;
	}

	/**
	 * Return the display name.
	 *
	 * @return string
	 */
	public function getDisplayName()
	{
		return _kt($this->displayname);
	}

	/**
	 * Returns the command line after performing substitutions.
	 *
	 * @return unknown
	 */
	protected function getCommandLine()
	{
		$sources = array('{source}','{target}');
		$target = array($this->sourcefile, $this->targetfile);
		$cmdline = $this->command . ' ' . str_replace($sources,$target, $params);

		return $cmdline;
	}

	/**
	 * Identifies if there are any circumstances why the command can not run that could result in the text extraction process
	 * failing.
	 *
	 * @return mixed Returns string if there is a problem, null otherwise.
	 */
	public function diagnose()
	{
		if (false === $this->application)
		{
			return _kt("Cannot locate binary for $this->displayname ($this->command).");
		}

		return null;
	}
}

abstract class TextExtractor extends DocumentExtractor
{
	/**
	 * This extracts the text from the document.
	 *
	 * @return boolean
	 */
	public function extractTextContent()
	{
		$content = file_get_contents($this->sourcefile);
		if (false === $content)
		{
			return false;
		}

		$result = file_put_contents($this->targetfile, $this->filter($content));

		return false !== $result;
	}

	/**
	 * There are no external dependancies to diagnose.
	 *
	 * @return null
	 */
	public function diagnose()
	{
		return null;
	}

}

/**
 * The composite extractor implies that a conversion is done to an intermediate form before another extractor is run.
 *
 */
abstract class CompositeExtractor extends DocumentExtractor
{
	/**
	 * The initial extractor
	 *
	 * @var DocumentExtractor
	 */
	private $sourceExtractor;
	/**
	 * The text extractor
	 *
	 * @var DocumentExtractor
	 */
	private $targetExtractor;
	/**
	 * The extension for the initial extraction
	 *
	 * @var string
	 */
	private $targetExtension;
	/**
	 * The mime type of the initial extraction.
	 *
	 * @var string
	 */
	private $targetMimeType;

	public function __construct($sourceExtractor, $targetExtension, $targetMimeType, $targetExtractor, $needsIntermediate)
	{
		$this->sourceExtractor = $sourceExtractor;
		$this->targetExtractor = $targetExtractor;
		$this->targetExtension = $targetExtension;
		$this->targetMimeType = $targetMimeType;
		$this->needsIntermediateSourceFile($needsIntermediate);
	}

	/**
	 * Extracts the content of the document
	 *
	 * @return string
	 */
	public function extractTextContent()
	{
		$intermediateFile = $this->targetfile . '.' . $this->targetExtension;

		$this->sourceExtractor->setSourceFile($this->sourcefile);
		$this->sourceExtractor->setTargetFile($intermediateFile);
		$this->sourceExtractor->setMimeType($this->mimetype);
		$this->sourceExtractor->setExtension($this->extension);
		if ($this->sourceExtractor->extractTextContent())
		{
			return false;
		}

		$this->targetExtractor->setSourceFile($intermediateFile);
		$this->targetExtractor->setTargetFile($this->targetfile);
		$this->targetExtractor->setMimeType($this->targetMimeType);
		$this->targetExtractor->setExtension($this->targetExtension);
		$result = $this->targetExtractor->extractTextContent();

		unlink(@$intermediateFile);

		return $result;
	}

	/**
	 * Diagnose the extractors
	 *
	 * @return mixed
	 */
	public function diagnose()
	{
		$diagnosis = $this->sourceExtractor->diagnose();
		if (!empty($diagnosis))
		{
			return $diagnosis;
		}

		$diagnosis = $this->targetExtractor->diagnose();
		if (!empty($diagnosis))
		{
			return $diagnosis;
		}

		return null;
	}
}


/**
 * The purpose of an extractor hook is to effect the
 *
 */
abstract class ExtractorHook
{
	/**
	 * Returns an array of supported mime types.
	 * e.g. return array('plain/text');
	 *
	 *
	 * @return array
	 *
	 */
	public abstract function getSupportedMimeTypes();

	/**
	 * Returns the friendly name for the hook.
	 *
	 * @return string
	 */
	public abstract function getDisplayName();

	/**
	 * This does a basic diagnosis on the hook.
	 *
	 * @return string
	 */
	public function diagnose()
	{
		return null;
	}

	/**
	 * Perform any pre extraction activities.
	 *
	 * @param DocumentExtractor $extractor
	 */
	public function pre_extract($extractor)
	{
	}

	/**
	 * Perform any post extraction activities.
	 *
	 * @param DocumentExtractor $extractor
	 */
	public function post_extract($extractor)
	{

	}

	/**
	 * Perform any pre indexing activities.
	 *
	 * @param DocumentExtractor $extractor
	 */
	public function pre_index($extractor)
	{

	}

	/**
	 * Perform any post indexing activities.
	 *
	 * @param DocumentExtractor $extractor
	 */
	public function post_index($extractor)
	{

	}
}

?>