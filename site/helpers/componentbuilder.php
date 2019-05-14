<?php
/**
 * @package    Joomla.Component.Builder
 *
 * @created    30th April, 2015
 * @author     Llewellyn van der Merwe <http://www.joomlacomponentbuilder.com>
 * @github     Joomla Component Builder <https://github.com/vdm-io/Joomla-Component-Builder>
 * @copyright  Copyright (C) 2015 - 2019 Vast Development Method. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * Componentbuilder component helper
 */
abstract class ComponentbuilderHelper
{

	/**
	*	The Global Site Event Method.
	**/
	public static function globalEvent($document)
	{
		// the Session keeps track of all data related to the current session of this user
		self::loadSession();
	}

	/**
	* Locked Libraries (we can not have these change)
	**/
	public static $libraryNames = array(1 => 'No Library', 2 => 'Bootstrap v4', 3 => 'Uikit v3', 4 => 'Uikit v2', 5 => 'FooTable v2', 6 => 'FooTable v3');

	/**
	* Array of php fields Allowed (16)
	**/
	public static $phpFieldArray = array('', 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'x');

	/**
	* The global params
	**/
	protected static $params = false;

	/**
	* The global updater
	**/
	protected static $globalUpdater = array();

	/**
	* The local company details
	**/
	protected static $localCompany = array();

	/**
	* The snippet paths
	**/
	public static $snippetPath = 'https://raw.githubusercontent.com/vdm-io/Joomla-Component-Builder-Snippets/master/';
	public static $snippetsPath = 'https://api.github.com/repos/vdm-io/Joomla-Component-Builder-Snippets/git/trees/master';

	/**
	* The VDM packages paths
	**/
	public static $vdmGithubPackagesUrl = "https://api.github.com/repos/vdm-io/JCB-Packages/git/trees/master";
	public static $vdmGithubPackageUrl = "https://github.com/vdm-io/JCB-Packages/raw/master/";

	/**
	* The JCB packages paths
	**/
	public static $jcbGithubPackagesUrl = "https://api.github.com/repos/vdm-io/JCB-Community-Packages/git/trees/master";
	public static $jcbGithubPackageUrl = "https://github.com/vdm-io/JCB-Community-Packages/raw/master/";

	// not needed at this time (maybe latter)
	public static $accessToken = "";

	/**
	 * The array of constant paths
	 * 
	 * JPATH_SITE is meant to represent the root path of the JSite application,
	 * just as JPATH_ADMINISTRATOR is mean to represent the root path of the JAdministrator application.
	 * 
	 *    JPATH_BASE is the root path for the current requested application.... so if you are in the administrator application:
	 * 
	 *    JPATH_BASE == JPATH_ADMINISTRATOR
	 * 
	 * If you are in the site application:
	 * 
	 *    JPATH_BASE == JPATH_SITE
	 * 
	 * If you are in the installation application:
	 * 
	 *    JPATH_BASE == JPATH_INSTALLATION.
	 * 
	 *    JPATH_ROOT is the root path for the Joomla install and does not depend upon any application.
	 * 
	 * @var     array
	 */
	public static $constantPaths = array(
		// The path to the administrator folder.
		'JPATH_ADMINISTRATOR' => JPATH_ADMINISTRATOR,
		// The path to the installed Joomla! site, or JPATH_ROOT/administrator if executed from the backend.
		'JPATH_BASE' => JPATH_BASE,
		// The path to the cache folder.
		'JPATH_CACHE' => JPATH_CACHE,
		// The path to the administration folder of the current component being executed.
		'JPATH_COMPONENT_ADMINISTRATOR' => JPATH_COMPONENT_ADMINISTRATOR,
		// The path to the site folder of the current component being executed.
		'JPATH_COMPONENT_SITE' => JPATH_COMPONENT_SITE,
		// The path to the current component being executed.
		'JPATH_COMPONENT' => JPATH_COMPONENT,
		// The path to folder containing the configuration.php file.
		'JPATH_CONFIGURATION' => JPATH_CONFIGURATION,
		// The path to the installation folder.
		'JPATH_INSTALLATION' => JPATH_INSTALLATION,
		// The path to the libraries folder.
		'JPATH_LIBRARIES' => JPATH_LIBRARIES,
		// The path to the plugins folder.
		'JPATH_PLUGINS' => JPATH_PLUGINS,
		// The path to the installed Joomla! site.
		'JPATH_ROOT' => JPATH_ROOT,
		// The path to the installed Joomla! site.
		'JPATH_SITE' => JPATH_SITE,
		// The path to the templates folder.
		'JPATH_THEMES' => JPATH_THEMES
	);

	/*
	 * Get the Array of Existing Validation Rule Names
	 *
	 * @return array
	 */
	public static function getExistingValidationRuleNames($lowercase = false)
	{
		// get the items
		$items = self::get('_existing_validation_rules_VDM', null);
		if (!$items)
		{
			// load the file class
			jimport('joomla.filesystem.file');
			jimport('joomla.filesystem.folder');
			// set the path to the form validation rules
			$path = JPATH_LIBRARIES . '/src/Form/Rule';
			// check if the path exist
			if (!JFolder::exists($path))
			{
				return false;
			}
			// we must first store the current working directory
			$joomla = getcwd();
			// go to that folder
			chdir($path);
			// load all the files in this path
			$items = JFolder::files('.', '\.php', true, true);
			// change back to Joomla working directory
			chdir($joomla);
			// make sure we have an array
			if (!self::checkArray($items))
			{
				return false;
			}
			// remove the Rule.php from the name
			$items = array_map( function ($name) {
				return str_replace(array('./','Rule.php'), '', $name);
			}, $items);
			// store the names for next run
			self::set('_existing_validation_rules_VDM', json_encode($items));
		}
		// make sure it is no longer json
		if (self::checkJson($items))
		{
			$items = json_decode($items, true);
		}
		// check if the names should be all lowercase
		if ($lowercase)
		{
			$items = array_map( function($item) {
				return strtolower($item);
			}, $items);
		}
		return $items;
	}

	/**
	* Get the snippet contributor details
	* 
	* @param  string   $filename   The file name
	* @param  string   $type         The type of file
	*
	* @return  array    On success the contributor details
	* 
	*/
	public static function getContributorDetails($filename, $type = 'snippet')
	{
		// start loading he contributor details
		$contributor = array();
		// get the path & content
		switch ($type)
		{
			case 'snippet':
				$path = $snippetPath.$filename;
				// get the file if available
				$content = self::getFileContents($path);
				if (self::checkJson($content))
				{
					$content = json_decode($content, true);
				}
			break;
			default:
				// only allow types that are being targeted
				return false;
			break;
		}
		// see if we have content and all needed details
		if (isset($content) && self::checkArray($content)
				&& isset($content['contributor_company'])
				&& isset($content['contributor_name'])
				&& isset($content['contributor_email'])
				&& isset($content['contributor_website']))
		{
			// got the details from file
			return array('contributor_company' => $content['contributor_company'] ,'contributor_name' => $content['contributor_name'], 'contributor_email' => $content['contributor_email'], 'contributor_website' => $content['contributor_website'], 'origin' => 'file');
		}
		// get the global settings
		if (!self::checkObject(self::$params))
		{
			self::$params = JComponentHelper::getParams('com_componentbuilder');
		}
		// get the global company details
		if (!self::checkArray(self::$localCompany))
		{
			// Set the person sharing information (default VDM ;)
			self::$localCompany['company']		= self::$params->get('export_company', 'Vast Development Method');
			self::$localCompany['owner']		= self::$params->get('export_owner', 'Llewellyn van der Merwe');
			self::$localCompany['email']		= self::$params->get('export_email', 'joomla@vdm.io');
			self::$localCompany['website']		= self::$params->get('export_website', 'https://www.vdm.io/');
		}
		// default global
		return array('contributor_company' => self::$localCompany['company']	,'contributor_name' => self::$localCompany['owner'], 'contributor_email' => self::$localCompany['email'], 'contributor_website' => self::$localCompany['website'], 'origin' => 'global');
	}

	/**
	* Get the library files
	* 
	* @param  int   $id   The library id to target
	*
	* @return  array    On success the array of files that belong to this library
	* 
	*/
	public static function getLibraryFiles($id)
	{
		// get the library files, folders, and urls
		$files = array();
		// Get a db connection.
		$db = JFactory::getDbo();
		// Create a new query object.
		$query = $db->getQuery(true);
		$query->select($db->quoteName(array('b.name','a.addurls','a.addfolders','a.addfiles')));
		$query->from($db->quoteName('#__componentbuilder_library_files_folders_urls','a'));
		$query->join('LEFT', $db->quoteName('#__componentbuilder_library', 'b') . ' ON (' . $db->quoteName('a.library') . ' = ' . $db->quoteName('b.id') . ')');
		$query->where($db->quoteName('a.library') . ' = ' . (int) $id);
		$db->setQuery($query);
		$db->execute();
		if ($db->getNumRows())
		{			
			// prepare the files
 			$result = $db->loadObject();
 			// first we load the URLs
			if (self::checkJson($result->addurls))
			{
				// convert to array
				$result->addurls = json_decode($result->addurls, true);
				// set urls
				if (self::checkArray($result->addurls))
				{
					// build media folder path
					$mediaPath = '/media/' . strtolower( preg_replace('/\s+/', '-', self::safeString($result->name, 'filename', ' ', false)));
					// load the urls
					foreach($result->addurls as $url)
					{
						if (isset($url['url']) && self::checkString($url['url']))
						{
							// set the path if needed
							if (isset($url['type']) && $url['type'] > 1)
							{
								$fileName = basename($url['url']);
								// build sub path
								if (strpos($fileName, '.js') !== false)
								{
									$path = '/js';
								}
								elseif (strpos($fileName, '.css') !== false)
								{
									$path = '/css';
								}
								else
								{
									$path = '';
								}
								// set the path to library file
								$url['path'] = $mediaPath . $path . '/' . $fileName; // we need this for later
							}
							// if local path is set, then use it first
							if (isset($url['path']))
							{
								// load document script
								$files[md5($url['path'])] =  '(' . JText::_('URL') . ') ' . basename($url['url']) . ' - ' . JText::_('COM_COMPONENTBUILDER_LOCAL');
							}
							// check if link must be added
							if (isset($url['url']) && ((isset($url['type']) && $url['type'] == 1) || (isset($url['type']) && $url['type'] == 3) || !isset($url['type'])))
							{
								// load url also if not building document
								$files[md5($url['url'])] = '(' . JText::_('URL') . ') ' . basename($url['url']) . ' - ' . JText::_('COM_COMPONENTBUILDER_LINK');
							}
						}
					}
				}
			}
			// load the local files
			if (self::checkJson($result->addfiles))
			{
				// convert to array
				$result->addfiles = json_decode($result->addfiles, true);
				// set files
				if (self::checkArray($result->addfiles))
				{
					foreach($result->addfiles as $file)
					{
						if (isset($file['file']) && isset($file['path']))
						{
							$path = '/'.trim($file['path'], '/');
							// check if path has new file name (has extetion)
							$pathInfo = pathinfo($path);
							if (isset($pathInfo['extension']) && $pathInfo['extension'])
							{
								// load document script
								$files[md5($path)] = '(' . JText::_('COM_COMPONENTBUILDER_FILE') . ') ' . $file['file'];
							}
							else
							{
								// load document script
								$files[md5($path.'/'.trim($file['file'],'/'))] = '(' . JText::_('COM_COMPONENTBUILDER_FILE') . ') ' . $file['file'];
							}
						}
					}
				}
			}
 			// load the files in the folder	
			if (self::checkJson($result->addfolders))
			{
				// convert to array
				$result->addfolders = json_decode($result->addfolders, true);
				// set folder
				if (self::checkArray($result->addfolders))
				{
					// get the global settings
					if (!self::checkObject(self::$params))
					{
						self::$params = JComponentHelper::getParams('com_componentbuilder');
					}
					// reset bucket
					$bucket = array();
					// get custom folder path
					$customPath = '/'.trim(self::$params->get('custom_folder_path', JPATH_COMPONENT_ADMINISTRATOR.'/custom'), '/');
					// get all the file paths
					foreach ($result->addfolders as $folder)
					{
						if (isset($folder['path']) && isset($folder['folder']))
						{
							$_path = '/'.trim($folder['path'], '/');
							$customFolder = '/'.trim($folder['folder'], '/');
							if (isset($folder['rename']) && 1 == $folder['rename'])
							{
								if ($_paths = self::getAllFilePaths($customPath.$customFolder))
								{
									$bucket[$_path] = $_paths;
								}
							}
							else
							{
								$path = $_path.$customFolder;
								if ($_paths = self::getAllFilePaths($customPath.$customFolder))
								{
									$bucket[$path] = $_paths;
								}
							}
						}
					}
					// now load the script
					if (self::checkArray($bucket))
					{
						foreach ($bucket as $root => $paths)
						{
							// load per path
							foreach($paths as $path)
							{
								$files[md5($root.'/'.trim($path, '/'))] = '(' . JText::_('COM_COMPONENTBUILDER_FOLDER') . ') ' . basename($path) . ' - ' . basename($root);
							}
						}
					}
				}
			}
			// return files if found
			if (self::checkArray($files))
			{
				return $files;
			}
		}
		return false;
	}

	/**
	 * Fix the path to work in the JCB script <-- (main issue here)
	 *	Since we need / slash in all paths, for the JCB script even if it is Windows
	 *	and since MS works with both forward and back slashes
	 *	we just convert all slashes to forward slashes
	 * 
	 * THIS is just my hack (fix) if you know a better way! speak-up!
	 * 
	 * @param   mix    $values   the array of paths or the path as a string
	 * @param   array  $targets  paths to target
	 *
	 * @return  string
	 * 
	 */
	public static function fixPath(&$values, $targets = array())
	{
		// if multiple to gets searched and fixed
		if (self::checkArray($values) && self::checkArray($targets))
		{
			foreach ($targets as $target)
			{
				if (isset($values[$target]) && strpos($values[$target], '\\') !== false)
				{
					$values[$target] = str_replace('\\', '/', $values[$target]);
				}
			}
		}
		// if just a string
		elseif (self::checkString($values) && strpos($values, '\\') !== false)
		{
			$values = str_replace('\\', '/', $values);
		}
	}

	/**
	 * get all the file paths in folder and sub folders
	 * 
	 * @param   string  $folder     The local path to parse
	 * @param   array   $fileTypes  The type of files to get
	 *
	 * @return  void
	 * 
	 */
	public static function getAllFilePaths($folder, $fileTypes = array('\.php', '\.js', '\.css', '\.less'))
	{
		if (JFolder::exists($folder))
		{
			// we must first store the current woking directory
			$joomla = getcwd();
			// we are changing the working directory to the componet path
			chdir($folder);
			// get the files
			foreach ($fileTypes as $type)
			{
				// get a list of files in the current directory tree
				$files[] = JFolder::files('.', $type, true, true);
			}
			// change back to Joomla working directory
			chdir($joomla);
			// return array of files
			return array_map( function($file) { return str_replace('./', '/', $file); }, (array) self::mergeArrays($files));
		}
		return false;
	}

	/**
	 * get all component IDs
	 */
	public static function getComponentIDs()
	{
		// Get a db connection.
		$db = JFactory::getDbo();
		// Create a new query object.
		$query = $db->getQuery(true);
		$query->select($db->quoteName(array('id')));
		$query->from($db->quoteName('#__componentbuilder_joomla_component'));
		$query->where($db->quoteName('published') . ' >= 1'); // do not backup trash
		$db->setQuery($query);
		$db->execute();
		if ($db->getNumRows())
		{			
				return $db->loadColumn();
		}
		return false;
	}

	/**
	 * Autoloader
	 */
	public static function autoLoader($type = 'compiler')
	{
		// load the type classes
		if ('smart' !== $type)
		{
			foreach (glob(JPATH_ADMINISTRATOR."/components/com_componentbuilder/helpers/".$type."/*.php") as $autoFile)
			{
				require_once $autoFile;
			}
		}
		// load only if compiler
		if ('compiler' === $type)
		{
			// import the Joomla librarys
			jimport('joomla.filesystem.file');
			jimport('joomla.filesystem.folder');
			jimport('joomla.filesystem.archive');
			jimport('joomla.application.component.modellist');
			// include class to minify js
			require_once JPATH_ADMINISTRATOR.'/components/com_componentbuilder/helpers/js.php';
		}
		// load only if smart
		if ('smart' === $type)
		{
			// import the Joomla libraries
			jimport('joomla.filesystem.file');
			jimport('joomla.filesystem.folder');
			jimport('joomla.filesystem.archive');
			jimport('joomla.application.component.modellist');
		}
		// load this for all
		jimport('joomla.application');
	}

	/**
	* 	The dynamic builder of views, tables and fields
	**/
	public static function dynamicBuilder(&$data, $type)
	{
		self::autoLoader('extrusion');
		$extruder = new Extrusion($data);
	}

	/*
	 * Convert repeatable field to subform
	 * 
	 * @param   array    $item       The array to convert
	 * @param   string   $name      The main field name
	 *
	 * @return  array
	 */
	public static function convertRepeatable($item, $name)
	{
		// continue only if we have an array
		if (self::checkArray($item))
		{
			$bucket = array();
			foreach ($item as $key => $values)
			{
				foreach ($values as $nr => $value)
				{
					if (!isset($bucket[$name . $nr]) || !self::checkArray($bucket[$name . $nr]))
					{
						$bucket[$name . $nr] = array();
					}
					$bucket[$name . $nr][$key] = $value;
				}
			}
			return $bucket;
		}
		return $item;
	}

	/*
	 * Convert repeatable field to subform
	 * 
	 * @param   object     $item            The item to update
	 * @param   array      $searcher        The fields to check and update
	 * @param   array      $updater         To update the local table
	 *
	 * @return void
	 */
	public static function convertRepeatableFields($object, $searcher, $updater = array())
	{
		// update the repeatable fields
		foreach ($searcher as  $key => $sleutel)
		{
			if (isset($object->{$key}))
			{
				$isJson = false;
				if (self::checkJson($object->{$key}))
				{
					$object->{$key} = json_decode($object->{$key}, true);
					$isJson = true;
				}
				// check if this is old values for repeatable fields
				if (self::checkArray($object->{$key}) && isset($object->{$key}[$sleutel]))
				{
					// load it back
					$object->{$key} = self::convertRepeatable($object->{$key}, $key);
					// add to global updater
					if (
						self::checkArray($object->{$key}) && self::checkArray($updater) && 
						(
							( isset($updater['table']) && isset($updater['val']) && isset($updater['key']) ) || 
							( isset($updater['unique']) && isset($updater['unique'][$key]) && isset($updater['unique'][$key]['table']) && isset($updater['unique'][$key]['val']) && isset($updater['unique'][$key]['key']) )
						)
					   )
					{
						$_key = null;
						$_value = null;
						$_table = null;
						// check if we have unique id table for this repeatable/subform field
						if ( isset($updater['unique']) && isset($updater['unique'][$key]) && isset($updater['unique'][$key]['table']) && isset($updater['unique'][$key]['val']) && isset($updater['unique'][$key]['key']) )
						{
							$_key = $updater['unique'][$key]['key'];
							$_value = $updater['unique'][$key]['val'];
							$_table = $updater['unique'][$key]['table'];
						}
						elseif ( isset($updater['table']) && isset($updater['val']) && isset($updater['key']) )
						{
							$_key = $updater['key'];
							$_value = $updater['val'];
							$_table = $updater['table'];
						}
						// continue only if values are valid
						if (self::checkString($_table) && self::checkString($_key) && $_value > 0)
						{
							// set target table & item
							$target = trim($_table) . '.' . trim($_key) . '.' . trim($_value);
							if (!isset(self::$globalUpdater[$target]))
							{
								self::$globalUpdater[$target] = new stdClass;
								self::$globalUpdater[$target]->{$_key} = (int) $_value;
							}
							// load the new subform values to global updater
							self::$globalUpdater[$target]->{$key} = json_encode($object->{$key});
						}
					}
				}
				// no set back to json if came in as json
				if ($isJson && self::checkArray($object->{$key}))
				{
					$object->{$key} = json_encode($object->{$key}); 
				}
				// remove if not json or array
				elseif (!self::checkArray($object->{$key}) && !self::checkJson($object->{$key}))
				{
					unset($object->{$key});
				}
			}
		}
		return $object;
	}

	/**
	 * Run Global Updater if any are set
	 * 
	 * @return  void
	 * 
	 */
	public static function runGlobalUpdater()
	{
		// check if any updates are set to run
		if (self::checkArray(self::$globalUpdater))
		{
			// get the database object
			$db = JFactory::getDbo();
			foreach (self::$globalUpdater as $tableKeyID => $object)
			{
				// get the table
				$table = explode('.', $tableKeyID);
				// update the item
				$db->updateObject('#__componentbuilder_' . (string) $table[0] , $object, (string) $table[1]);
			}
			// rest updater
			self::$globalUpdater = array();
		}
	}

	/**
	 * Copy Any Item (only use for direct database copying)
	 * 
	 * @param   int        $id         The item to copy
	 * @param   string   $table     The table and model to copy from and with
	 * @param   array    $config   The values that should change
	 *
	 * @return  boolean   True if success
	 * 
	 */
	public static function copyItem($id, $type, $config = array())
	{
		// only continue if we have an id
		if ((int) $id > 0)
		{
			// get the model
			$model = self::getModel($type);
			$app   = \JFactory::getApplication();
			// get item
			if ($item = $model->getItem($id))
			{
				// update values that should change
				if (self::checkArray($config))
				{
					foreach($config as $key => $value)
					{
						if (isset($item->{$key}))
						{
							$item->{$key} = $value;
						}
					}
				}
				// clone the object
				$data = array();
				foreach ($item as $key => $value)
				{
					$data[$key] = $value;
				}			
				// reset some values
				$data['id'] = 0;
				$data['version'] = 1;
				if (isset($data['tags']))
				{
					$data['tags'] = null;
				}
				if (isset($data['associations']))
				{
					$data['associations'] = array();
				}
				// remove some unneeded values
				unset($data['params']);
				unset($data['asset_id']);
				unset($data['checked_out']);
				unset($data['checked_out_time']);
				// Attempt to save the data.
				if ($model->save($data))
				{
					return true;
				}
			}
		}
		return false;
	}

	/**
	* the basic localkey
	**/
	protected static $localkey = false;

	/**
	* get the localkey
	**/	
	public static function getLocalKey()
	{
		if (!self::$localkey)
		{
			// get the basic key
			self::$localkey = md5(self::getCryptKey('basic', 'localKey34fdWEkl'));
		}
		return self::$localkey;
	}

	/**
	 * indent HTML
	 */
	public static function indent($html)
	{
		// load the class
		require_once JPATH_ADMINISTRATOR.'/components/com_componentbuilder/helpers/indenter.php';
		// set new indenter
		$indenter = new Indenter();
		// return indented html
		return $indenter->indent($html);
	}

	public static function checkFileType($file, $sufix)
	{
		// now check if the file ends with the sufix
		return $sufix === "" || ($sufix == substr(strrchr($file, "."), -strlen($sufix)));
	}

	public static function imageInfo($path, $request = 'type')
	{
		// set image
		$image = JPATH_SITE.'/'.$path;
		// check if exists
		if (file_exists($image) && $result = @getimagesize($image))
		{
			// return type request
			switch ($request)
			{
				case 'width':
					return $result[0];
					break;
				case 'height':
					return $result[1];
					break;
				case 'type':
					$extensions = array(
						IMAGETYPE_GIF => "gif",
						IMAGETYPE_JPEG => "jpg",
						IMAGETYPE_PNG => "png",
						IMAGETYPE_SWF => "swf",
						IMAGETYPE_PSD => "psd",
						IMAGETYPE_BMP => "bmp",
						IMAGETYPE_TIFF_II => "tiff",
						IMAGETYPE_TIFF_MM => "tiff",
						IMAGETYPE_JPC => "jpc",
						IMAGETYPE_JP2 => "jp2",
						IMAGETYPE_JPX => "jpx",
						IMAGETYPE_JB2 => "jb2",
						IMAGETYPE_SWC => "swc",
						IMAGETYPE_IFF => "iff",
						IMAGETYPE_WBMP => "wbmp",
						IMAGETYPE_XBM => "xbm",
						IMAGETYPE_ICO => "ico"
					);
					return $extensions[$result[2]];
					break;
				case 'attr':
					return $result[3];
					break;
				case 'all':
				default:
					return $result;
					break;
			}
		}
		return false;
	}

	/**
	*  set the session defaults if not set
	**/
	protected static function setSessionDefaults()
	{
		// noting for now
		return true;
	}

	/**
	* check if it is a new hash
	**/
	public static function newHash($hash, $name = 'backup', $type = 'hash', $key = '',  $fileType = 'txt')
	{
		// make sure we have a hash
		if (self::checkString($hash))
		{
			// first get the file path
			$path_filename = self::getFilePath('path', $name.$type, $fileType, $key, JPATH_COMPONENT_ADMINISTRATOR);
			// set as read if not already set
			if ($content = self::getFileContents($path_filename, false))
			{
				if ($hash == $content)
				{
					return false;
				}
			}
			// set the hash
			return self::writeFile($path_filename, $hash);
		}
		return false;
	}

	protected static $pkOwnerSearch = array(
		'company' => 'COM_COMPONENTBUILDER_DTCOMPANYDTDDSDD',
		'owner' => 'COM_COMPONENTBUILDER_DTOWNERDTDDSDD',
		'email' => 'COM_COMPONENTBUILDER_DTEMAILDTDDSDD',
		'website' => 'COM_COMPONENTBUILDER_DTWEBSITEDTDDSDD',
		'license' => 'COM_COMPONENTBUILDER_DTLICENSEDTDDSDD',
		'copyright' => 'COM_COMPONENTBUILDER_DTCOPYRIGHTDTDDSDD'
		);

	/**
	* get the JCB package owner details display
	**/
	public static function getPackageOwnerDetailsDisplay(&$info, $trust = false)
	{
		$hasOwner = false;
		$ownerDetails = '<h2 class="module-title nav-header">' . JText::_('COM_COMPONENTBUILDER_PACKAGE_OWNER_DETAILS') . '</h2>';
		$ownerDetails .= '<dl class="uk-description-list-horizontal">';
		// load the list items
		foreach (self::$pkOwnerSearch as $key => $dd)
		{
			if ($value = self::getPackageOwnerValue($key, $info))
			{
				$ownerDetails .= JText::sprintf($dd, $value);
				// check if we have a owner/source name
				if (('owner' === $key || 'company' === $key) && !$hasOwner)
				{
					$hasOwner = true;
					$owner = $value;
				}
			}
		}
		$ownerDetails .= '</dl>';

		// provide some details to how the user can get a key
		if ($hasOwner && isset($info['getKeyFrom']['buy_link']) && self::checkString($info['getKeyFrom']['buy_link']))
		{
			$ownerDetails .= '<hr />';
			$ownerDetails .= JText::sprintf('COM_COMPONENTBUILDER_BGET_THE_KEY_FROMB_A_SSA', 'class="btn btn-primary" href="'.$info['getKeyFrom']['buy_link'].'" target="_blank" title="get a key from '.$owner.'"', $owner);
		}
		// add more custom links
		elseif ($hasOwner && isset($info['getKeyFrom']['buy_links']) && self::checkArray($info['getKeyFrom']['buy_links']))
		{
			$buttons = array();
			foreach ($info['getKeyFrom']['buy_links'] as $keyName => $link)
			{
				$buttons[] = JText::sprintf('COM_COMPONENTBUILDER_BGET_THE_KEY_FROM_SB_FOR_A_SSA', $owner, 'class="btn btn-primary" href="'.$link.'" target="_blank" title="get a key from '.$owner.'"', $keyName);
			}
			$ownerDetails .= '<hr />';
			$ownerDetails .= implode('<br />', $buttons);
		}
		// return the owner details
		if (!$hasOwner)
		{
			$ownerDetails = '<h2>' . JText::_('COM_COMPONENTBUILDER_PACKAGE_OWNER_DETAILS_NOT_FOUND') . '</h2>';
			if (!$trust)
			{
				$ownerDetails .= '<p style="color: #922924;">' . JText::_('COM_COMPONENTBUILDER_BE_CAUTIOUS_DO_NOT_CONTINUE_UNLESS_YOU_TRUST_THE_ORIGIN_OF_THIS_PACKAGE') . '</p>';
			}
		}
		return '<div>'.$ownerDetails.'</div>';
	}

	public static function getPackageOwnerValue($key, &$info)
	{
		$source = (isset($info['source']) && isset($info['source'][$key])) ? 'source' : ((isset($info['getKeyFrom']) && isset($info['getKeyFrom'][$key])) ? 'getKeyFrom' : false);
		if ($source && self::checkString($info[$source][$key]))
		{
			return $info[$source][$key];
		}
		return false;
	}

	/**
	*  get the JCB package component key status
	**/
	public static function getPackageComponentsKeyStatus(&$info)
	{
		// check the package key status
		if (!isset($info['key']))
		{
			if (isset($info['getKeyFrom']) && isset($info['getKeyFrom']['owner']))
			{
				// this just confirms it for older packages
				$info['key'] = true;
			}
			else
			{
				// this just confirms it for older packages
				$info['key'] = false;
			}
		}
		return $info['key'];
	}

	protected static $compOwnerSearch = array(
		'ul' => array (
			'companyname' => 'COM_COMPONENTBUILDER_ICOMPANYI_BSB',
			'author' => 'COM_COMPONENTBUILDER_IAUTHORI_BSB',
			'email' => 'COM_COMPONENTBUILDER_IEMAILI_BSB',
			'website' => 'COM_COMPONENTBUILDER_IWEBSITEI_BSB',
			),
		'other' => array(
			'license' => 'COM_COMPONENTBUILDER_HFOUR_CLASSNAVHEADERLICENSEHFOURPSP',
			'copyright' => 'COM_COMPONENTBUILDER_HFOUR_CLASSNAVHEADERCOPYRIGHTHFOURPSP'
			)
		);

	/**
	* get the JCB package component details display
	**/
	public static function getPackageComponentsDetailsDisplay(&$info)
	{
		// check if these components need a key
		$needKey = self::getPackageComponentsKeyStatus($info);
		if (isset($info['name']) && self::checkArray($info['name'])) 
		{
			$cAmount = count((array) $info['name']);
			$class2 = ($cAmount == 1) ? 'span12' : 'span6';
			$counter = 1;
			$display = array();
			foreach ($info['name'] as $key => $value)
			{
				// set the name
				$name= $value . ' v' . $info['component_version'][$key];
				if ($cAmount > 1 && $counter == 3)
				{
					$display[] = '</div>';
					$counter = 1;
				}
				if ($cAmount > 1 && $counter == 1)
				{
					$display[] = '<div>';
				}
				$display[] = '<div class="well well-small ' . $class2 . '">';
				$display[] = '<h3>';
				$display[] = $name;
				if ($needKey)
				{
					$display[] = ' - <em>' . JText::sprintf('COM_COMPONENTBUILDER_PAIDLOCKED') . '</em>';
				}
				else
				{
					$display[] = ' - <em>' . JText::sprintf('COM_COMPONENTBUILDER_FREEOPEN') . '</em>';
				}
				$display[] = '</h3><h4>';
				$display[] = $info['short_description'][$key];
				$display[] = '</h4>';
				$display[] = '<ul class="uk-list uk-list-striped">';
				// load the list items
				foreach (self::$compOwnerSearch['ul'] as $li => $value)
				{
					if (isset($info[$li]) && isset($info[$li][$key]))
					{
						$display[] = '<li>'.JText::sprintf($value, $info[$li][$key]).'</li>';
					}
				}
				$display[] = '</ul>';
				// if we have a source link we add it
				if (isset($info['joomla_source_link']) && self::checkArray($info['joomla_source_link']) && isset($info['joomla_source_link'][$key]) && self::checkString($info['joomla_source_link'][$key]))
				{
					$display[] = '<a class="uk-button uk-button-mini uk-width-1-1 uk-margin-small-bottom " href="'.$info['joomla_source_link'][$key].'" target="_blank" title="Source Code for Joomla Component ('.$name.')">source code</a>';
				}
				// load other
				foreach (self::$compOwnerSearch['other'] as $other => $value)
				{
					if (isset($info[$other]) && isset($info[$other][$key]))
					{
						$display[] = JText::sprintf($value, $info[$other][$key]);
					}
				}
				$display[] = '</div>';

				$counter++;
			}
			// close the div if needed
			if ($cAmount > 1)
			{
				$display[] = '</div>';
			}
			return implode("\n",$display);
		}
		return '<div>'.JText::_('COM_COMPONENTBUILDER_NO_COMPONENT_DETAILS_FOUND_SO_IT_IS_NOT_SAFE_TO_CONTINUE').'</div>';
	}

	/**
	* get the database table columns
	**/
	public static function getDbTableColumns($tableName, $as, $type)
	{
		// Get a db connection.
		$db = JFactory::getDbo();
        	// get the columns
		$columns = $db->getTableColumns("#__" . $tableName);
		// set the type (multi or single)
		$unique = '';
		if (1 == $type)
		{
			$unique = self::safeString($tableName) . '_';
		}
		if (self::checkArray($columns))
		{
        		// build the return string
			$tableColumns = array();
			foreach ($columns as $column => $typeCast)
			{
				$tableColumns[] =  $as . "." . $column . ' AS ' . $unique . $column;
			}
			return implode("\n", $tableColumns);
		}
		return false;
	}

	/**
	* get the view table columns
	**/
	public static function getViewTableColumns($admin_view, $as, $type)
	{
		// Get a db connection.
		$db = JFactory::getDbo();

		// Create a new query object.
		$query = $db->getQuery(true);
		$query->select($db->quoteName(array('a.addfields', 'b.name_single')));
		$query->from($db->quoteName('#__componentbuilder_admin_fields', 'a'));
		$query->join('LEFT', $db->quoteName('#__componentbuilder_admin_view', 'b') . ' ON (' . $db->quoteName('a.admin_view') . ' = ' . $db->quoteName('b.id') . ')');
		$query->where($db->quoteName('b.published') . ' = 1');
		$query->where($db->quoteName('a.admin_view') . ' = ' . (int) $admin_view);

		// Reset the query using our newly populated query object.
		$db->setQuery($query);
		$db->execute();
		if ($db->getNumRows())
		{
			$result = $db->loadObject();
			$tableName = '';
			if (1 == $type)
			{
				$tableName = self::safeString($result->name_single) . '_';
			}
			$addfields = json_decode($result->addfields, true);
			if (self::checkArray($addfields))
			{
				// reset all buckets
				$field = array();
				$fields = array();
				// get data
				foreach ($addfields as $nr => $value)
				{
					$tmp = self::getFieldNameAndType((int) $value['field']);
					if (self::checkArray($tmp))
					{
						$field[$nr] = $tmp;
					}
					// insure it is set to alias if needed
					if (isset($value['alias']) && $value['alias'] == 1)
					{
						$field[$nr]['name'] = 'alias';
					}
					// remove a field that is not being stored in the database
					if (!isset($value['list']) || $value['list'] == 2)
					{
						unset($field[$nr]);
					}
				}
				// add the basic defaults
				$fields[] = $as . ".id AS " . $tableName . "id";
				$fields[] = $as . ".asset_id AS " . $tableName . "asset_id";
				// load data
				foreach ($field as $n => $f)
				{
					if (self::checkArray($f))
					{
						$fields[] = $as . "." . $f['name'] . " AS " . $tableName . $f['name'];
					}
				}
				// add the basic defaults
				$fields[] = $as . ".published AS " . $tableName . "published";
				$fields[] = $as . ".created_by AS " . $tableName . "created_by";
				$fields[] = $as . ".modified_by AS " . $tableName . "modified_by";
				$fields[] = $as . ".created AS " . $tableName . "created";
				$fields[] = $as . ".modified AS " . $tableName . "modified";
				$fields[] = $as . ".version AS " . $tableName . "version";
				$fields[] = $as . ".hits AS " . $tableName . "hits";
				if (0) // TODO access is not set here but per/view in the form linking this admin view to which these field belong to the components (boooo I know but that is the case and so we can't ever really know at this point if this view has access set)
				{
					$fields[] = $as . ".access AS " . $tableName . "access";
				}
				$fields[] = $as . ".ordering AS " . $tableName . "ordering";
				// return the field of this view
				return implode("\n", $fields);
			}
		}
		return false;
	}

	protected static function getFieldNameAndType($id)
	{
		// Get a db connection.
		$db = JFactory::getDbo();

		// Create a new query object.
		$query = $db->getQuery(true);

		// Order it by the ordering field.
		$query->select($db->quoteName(array('a.name', 'a.xml')));
		$query->select($db->quoteName(array('c.name'), array('type_name')));
		$query->from('#__componentbuilder_field AS a');
		$query->join('LEFT', $db->quoteName('#__componentbuilder_fieldtype', 'c') . ' ON (' . $db->quoteName('a.fieldtype') . ' = ' . $db->quoteName('c.id') . ')');
		$query->where($db->quoteName('a.id') . ' = '. $db->quote($id));

		// Reset the query using our newly populated query object.
		$db->setQuery($query);
		$db->execute();
		if ($db->getNumRows())
		{
			// Load the results as a list of stdClass objects (see later for more options on retrieving data).
			$field = $db->loadObject();
			// load the values form params
			$field->xml = json_decode($field->xml);
			$field->type_name = self::safeString($field->type_name);
			$load = true;
			// if category then name must be catid (only one per view)
			if ($field->type_name == 'category')
			{
				$name = 'catid';
			}
			// if tag is set then enable all tag options for this view (only one per view)
			elseif ($field->type_name == 'tag')
			{
				$name = 'tags';
			}
			// don't add spacers or notes
			elseif ($field->type_name == 'spacer' || $field->type_name == 'note')
			{
				// make sure the name is unique
				return false;
			}
			else
			{
				$name = self::safeString(self::getBetween($field->xml,'name="','"'));
			}

			// use field core name only if not found in xml
			if (!self::checkString($name))
			{
				$name = self::safeString($field->name);;
			}
			return array('name' => $name, 'type' => $field->type_name);
		}
		return false;
	}

	/**
	* validate that a placeholder is unique
	**/
	public static function validateUniquePlaceholder($id, $name, $bool = false)
	{
		// make sure no padding is set
		$name = preg_replace("/[^A-Za-z0-9_]/", '', $name);
		// this list may grow as we find more cases that break the compiler (just open an issue on github)
		if (in_array($name, array('component', 'view', 'views')))
		{
			// check if we must return boolean
			if (!$bool)
			{
				return array (
					'message' => JText::_('COM_COMPONENTBUILDER_SORRY_THIS_PLACEHOLDER_IS_ALREADY_IN_USE_IN_THE_COMPILER'),
					'status' => 'danger');
			}
			return false;
		}
		// add the padding (needed)
		$name = '[[[' . trim($name) . ']]]';
		if (self::placeholderIsSet($id, $name))
		{
			// check if we must return boolean
			if (!$bool)
			{
				return array (
					'message' => JText::_('COM_COMPONENTBUILDER_SORRY_THIS_PLACEHOLDER_IS_ALREADY_IN_USE'),
					'status' => 'danger');
			}
			return false;
		}
		// check if we must return boolean
		if (!$bool)
		{
			return array (
				'name' => $name,
				'message' => JText::_('COM_COMPONENTBUILDER_GREAT_THIS_PLACEHOLDER_WILL_WORK'),
				'status' => 'success');
		}
		return true;
	}

	/**
	* search for placeholder in table
	**/
	protected static function placeholderIsSet($id, $name)
	{
		// query the table for result array
		if (($results = self::getPlaceholderTarget($id, $name)) !== false)
		{
			// check if we must continue the search
			foreach ($results as $_id => $target)
			{
				if ($name === $target)
				{
					return true;
				}
			}
		}
		return false;
	}

	/**
	* get placeholder target
	**/
	protected static function getPlaceholderTarget($id, $name)
	{
		// Get a db connection.
		$db = JFactory::getDbo();
		// Create a new query object.
		$query = $db->getQuery(true);
		$query->select($db->quoteName(array('id', 'target')));
		$query->from($db->quoteName('#__componentbuilder_placeholder'));
		$query->where($db->quoteName('target') . ' = '. $db->quote($name));
		// check if we have id
		if (is_numeric($id))
		{
			$query->where($db->quoteName('id') . ' <> ' . (int) $id);
		}
		$db->setQuery($query);
		$db->execute();
		if ($db->getNumRows())
		{
			return $db->loadAssocList('id', 'target');
		}
		return false;
	}

	/**
	 * The array of dynamic content
	 * 
	 * @var     array
	 */
	protected static $dynamicContent = array(
		// The banners by size
		'banner' => array(
			'728-90' => array(
				'<a href="https://volunteers.joomla.org/" target="_blank" title="Joomla! Volunteers Portal"><img src="https://cdn.joomla.org/volunteers/joomla-heart-wide.gif" alt="Joomla! Volunteers Portal" width="728" height="90" border="0"></a>',
				'<a href="https://magazine.joomla.org" target="_blank" title="Joomla! Community Magazine | Because community matters..."><img alt="Joomla! Community Magazine | Because community matters..." src="https://magazine.joomla.org/images/banners/JCM_2010_728x90.png" width="728" height="90" border="0" /></a>'
			),
			'160-600' => array(
				'<a href="https://volunteers.joomla.org/" target="_blank" title="Joomla! Volunteers Portal"><img src="https://cdn.joomla.org/volunteers/joomla-heart-tall.gif" alt="Joomla! Volunteers Portal" width="160" height="600" border="0"></a>',
				'<a href="https://magazine.joomla.org" target="_blank" title="Joomla! Community Magazine | Because community matters..."><img src="https://magazine.joomla.org/images/banners/JCM_2010_120x600.png" alt="Joomla! Community Magazine | Because community matters..." width="120" height="600" border="0"/></a>'
			)
		),
		// The build-gif by size
		'builder-gif' => array(
			'707-400' => array(
				'<img src="components/com_componentbuilder/assets/images/ajax-loader.gif" />'
			)
		)
	);

	/**
	 * get the dynamic content
	 * 
	 * @param   string   $type     The type of content
	 * @param   string    $size   The size of the content
	 *
	 * @return  string   on success
	 * 
	 */
	public static function getDynamicContent($type, $size, $default = '')
	{
		if (isset(self::$dynamicContent[$type]) && isset(self::$dynamicContent[$type][$size]) && ($nr = self::checkArray(self::$dynamicContent[$type][$size])))
		{
			// get the random item number
			$get = (int) rand(0, --$nr);
			// return found content
			return self::$dynamicContent[$type][$size][$get];
		}
		return $default;
	}

	/**
	* 	the Butler
	**/
	public static $session = array();

	/**
	* 	the Butler Assistant 
	**/
	protected static $localSession = array();

	/**
	* 	start a session if not already set, and load with data
	**/
	public static function loadSession()
	{
		if (!isset(self::$session) || !self::checkObject(self::$session))
		{
			self::$session = JFactory::getSession();
		}
		// set the defaults
		self::setSessionDefaults();
	}

	/**
	* 	give Session more to keep
	**/
	public static function set($key, $value)
	{
		if (!isset(self::$session) || !self::checkObject(self::$session))
		{
			self::$session = JFactory::getSession();
		}
		// set to local memory to speed up program
		self::$localSession[$key] = $value;
		// load to session for later use
		return self::$session->set($key, self::$localSession[$key]);
	}

	/**
	* 	get info from Session
	**/
	public static function get($key, $default = null)
	{
		if (!isset(self::$session) || !self::checkObject(self::$session))
		{
			self::$session = JFactory::getSession();
		}
		// check if in local memory
		if (!isset(self::$localSession[$key]))
		{
			// set to local memory to speed up program
			self::$localSession[$key] = self::$session->get($key, $default);
		}
		return self::$localSession[$key];
	}


	/**
	 * get field options
	 *
	 * @return  array   on success
	 * 
	 */
	public static function getFieldOptions($value, $type, $settings = array(), $xml = null, $db_defaults = false)
	{
		// Get a db connection.
		$db = JFactory::getDbo();

		// Create a new query object.
		$query = $db->getQuery(true);
		$query->select($db->quoteName(array('properties', 'short_description', 'description')));
		// load database default values
		if ($db_defaults)
		{
			$query->select($db->quoteName(array('datadefault', 'datadefault_other', 'datalenght', 'datalenght_other', 'datatype', 'has_defaults', 'indexes', 'null_switch', 'store')));
		}
		$query->from($db->quoteName('#__componentbuilder_fieldtype'));
		$query->where($db->quoteName('published') . ' = 1');
		$query->where($db->quoteName($type) . ' = '. $value);

		// Reset the query using our newly populated query object.
		$db->setQuery($query);
		$db->execute();
		if ($db->getNumRows())
		{
			$result = $db->loadObject();
			$properties = json_decode($result->properties, true);
			$field = array(
				'subform' => array(),
				'nameListOptions' => array(),
				'php' => array(),
				'values' => "<field ", 
				'values_description' => '<table class="uk-table uk-table-hover uk-table-striped uk-table-condensed">', 
				'short_description' => $result->short_description, 
				'description' => $result->description);
			// number pointer
			$nr = 0;
			// php tracker (we must try to load alteast 17 rows
			$phpTracker = array();
			// value to check since there are false and null values even 0 in the values returned
			$confirmation = '8qvZHoyuFYQqpj0YQbc6F3o5DhBlmS-_-a8pmCZfOVSfANjkmV5LG8pCdAY2JNYu6cB';
			// set the headers
			$field['values_description'] .= '<thead><tr><th class="uk-text-right">' . JText::_('COM_COMPONENTBUILDER_PROPERTY') . '</th><th>' . JText::_('COM_COMPONENTBUILDER_EXAMPLE') . '</th><th>' . JText::_('COM_COMPONENTBUILDER_DESCRIPTION') . '</th></thead><tbody>';
			foreach ($properties as $property)
			{
				$example = (isset($property['example']) && self::checkString($property['example'])) ? $property['example'] : '';
				$field['values_description'] .= '<tr><td class="uk-text-right"><code>' . $property['name'] . '</code></td><td>' . $example . '</td><td>' . $property['description'] . '</td></tr>';
				// check if we should load the value
				$value = self::getValueFromXMLstring($xml, $property['name'], $confirmation);
				// check if this is a php field
				$addPHP = false;
				if (strpos($property['name'], 'type_php') !== false)
				{
					$addPHP = true;
					// set the line number
					$phpLine = (int) preg_replace('/[^0-9]/', '', $property['name']);
					// set the key
					$phpKey = trim(preg_replace('/[0-9]+/', '', $property['name']), '_');
					// start array if not already set
					if (!isset($field['php'][$phpKey]))
					{
						$field['php'][$phpKey] = array();
						$field['php'][$phpKey]['value'] = array();
						$field['php'][$phpKey]['desc'] = $property['description'];
						// start tracker
						$phpTracker[$phpKey] = 1;
					}
				}
				// was the settings for the property passed
				if(self::checkArray($settings) && isset($settings[$property['name']]))
				{
					// add the xml values
					$field['values'] .= PHP_EOL . "\t" . $property['name'] . '="'. $settings[$property['name']] . '" ';
					// add the json values
					if ($addPHP)
					{
						$field['php'][$phpKey]['value'][$phpLine] = $settings[$property['name']];
						$phpTracker[$phpKey]++;
					}
					else
					{
						$field['subform']['properties'.$nr] = array('name' => $property['name'], 'value' => $settings[$property['name']], 'desc' => $property['description']);
					}
				}
				elseif (!$xml || $confirmation !== $value)
				{
					// add the xml values
					$field['values'] .= PHP_EOL."\t" . $property['name'] . '="' . ($confirmation !== $value) ? $value : $example .'" ';
					// add the json values
					if ($addPHP)
					{
						$field['php'][$phpKey]['value'][$phpLine] = ($confirmation !== $value) ? $value : $example;
						$phpTracker[$phpKey]++;
					}
					else
					{
						$field['subform']['properties' . $nr] = array('name' => $property['name'], 'value' => ($confirmation !== $value) ? $value : $example, 'desc' => $property['description']);
					}
				}
				// add the name List Options
				if (!$addPHP)
				{
					$field['nameListOptions'][$property['name']] = $property['name'];
				}
				// increment the number
				$nr++;
			}
			// check if all php is loaded using the tracker
			if (self::checkString($xml) && isset($phpTracker) && self::checkArray($phpTracker))
			{
				foreach ($phpTracker as $phpKey => $start)
				{
					if ($start < 30)
					{
						// we must search for more code in the xml just incase
						foreach(range(2, 30) as $t_nr)
						{
							$get_ = $phpKey . '_' . $t_nr;
							if (!isset($field['php'][$phpKey]['value'][$t_nr]) && ($value = self::getValueFromXMLstring($xml, $get_, $confirmation)) !== $confirmation)
							{
								$field['php'][$phpKey]['value'][$t_nr] = $value;
							}
						}
					}
				}
			}
			$field['values'] .= PHP_EOL . "/>";
			$field['values_description'] .= '</tbody></table>';
			// load the database defaults if set and wanted
			if ($db_defaults && isset($result->has_defaults) && $result->has_defaults == 1)
			{
				$field['database'] = array(
					'datatype' => $result->datatype,
					'datadefault' => $result->datadefault,
					'datadefault_other' => $result->datadefault_other,
					'datalenght' => $result->datalenght,
					'datalenght_other' => $result->datalenght_other,
					'indexes' => $result->indexes,
					'null_switch' => $result->null_switch,
					'store' => $result->store
				);
			}
			// return found field options
			return $field;
		}
		return false;
	}

	public static function getValueFromXMLstring(&$xml, &$get, $confirmation = '')
	{
		if (self::checkString($xml))
		{
			// if we have a PHP value, we must base64 decode it
			if (strpos($get, 'type_php') !== false)
			{
				return self::openValidBase64(self::getBetween($xml, $get.'="', '"', $confirmation));
			}
			return self::getBetween($xml, $get . '="', '"', $confirmation);
		}
		return $confirmation;
	}


	/**
	* The zipper method
	* 
	* @param  string   $workingDIR    The directory where the items must be zipped
	* @param  string   $filepath          The path to where the zip file must be placed
	*
	* @return  bool true   On success
	* 
	*/
	public static function zip($workingDIR, &$filepath)
	{
		// store the current joomla working directory
		$joomla = getcwd();

		// we are changing the working directory to the component temp folder
		chdir($workingDIR);

		// the full file path of the zip file
		$filepath = JPath::clean($filepath);

		// delete an existing zip file (or use an exclusion parameter in JFolder::files()
		JFile::delete($filepath);

		// get a list of files in the current directory tree
		$files = JFolder::files('.', '', true, true);
		$zipArray = array();
		// setup the zip array
		foreach ($files as $file)
		{
		   $tmp = array();
		   $tmp['name'] = str_replace('./', '', $file);
		   $tmp['data'] = JFile::read($file);
		   $tmp['time'] = filemtime($file);
		   $zipArray[] = $tmp;
		}

		// change back to joomla working directory
		chdir($joomla);

		// get the zip adapter
		$zip = JArchive::getAdapter('zip');

		//create the zip file
		if ($zip->create($filepath, $zipArray))
		{
			return true;
		}
		return false;
	}


	/**
	* Write a file to the server
	*
	* @param  string   $path    The path and file name where to safe the data
	* @param  string   $data    The data to safe
	*
	* @return  bool true   On success
	*
	*/
	public static function writeFile($path, $data)
	{
		$klaar = false;
		if (self::checkString($data))
		{
			// open the file
			$fh = fopen($path, "w");
			if (!is_resource($fh))
			{
				return $klaar;
			}
			// write to the file
			if (fwrite($fh, $data))
			{
				// has been done
				$klaar = true;
			}
			// close file.
			fclose($fh);
		}
		return $klaar;
	}


	/**
	 * Remove folders with files
	 * 
	 * @param   string   $dir     The path to folder to remove
	 * @param   boolean  $ignore  The folders and files to ignore and not remove
	 *
	 * @return  boolean   True in all is removed
	 * 
	 */
	public static function removeFolder($dir, $ignore = false)
	{
		if (JFolder::exists($dir))
		{
			$it = new RecursiveDirectoryIterator($dir);
			$it = new RecursiveIteratorIterator($it, RecursiveIteratorIterator::CHILD_FIRST);
			// remove ending /
			$dir = rtrim($dir, '/');
			// now loop the files & folders
			foreach ($it as $file)
			{
				if ('.' === $file->getBasename() || '..' ===  $file->getBasename()) continue;
				// set file dir
				$file_dir = $file->getPathname();
				// check if this is a dir or a file
				if ($file->isDir())
				{
					$keeper = false;
					if (self::checkArray($ignore))
					{
						foreach ($ignore as $keep)
						{
							if (strpos($file_dir, $dir.'/'.$keep) !== false)
							{
								$keeper = true;
							}
						}
					}
					if ($keeper)
					{
						continue;
					}
					JFolder::delete($file_dir);
				}
				else
				{
					$keeper = false;
					if (self::checkArray($ignore))
					{
						foreach ($ignore as $keep)
						{
							if (strpos($file_dir, $dir.'/'.$keep) !== false)
							{
								$keeper = true;
							}
						}
					}
					if ($keeper)
					{
						continue;
					}
					JFile::delete($file_dir);
				}
			}
			// delete the root folder if not ignore found
			if (!self::checkArray($ignore))
			{
				return JFolder::delete($dir);
			}
			return true;
		}
		return false;
	}


	/**
	*	get the github repo file list
	*
	*	@return  array on success
	* 
	*/
	public static function getGithubRepoFileList($type, $target)
	{
		// get the current Packages (public)
		if (!$repoData = self::get($type))
		{
			if (self::urlExists($target))
			{
				$repoData = self::getFileContents($target);
				if (self::checkJson($repoData))
				{
					$test = json_decode($repoData);
					if (self::checkObject($test) && isset($test->tree) && self::checkArray($test->tree) )
					{
						// remember to set it
						self::set($type, $repoData);
					}
					// check if we have error message from github
					elseif ($errorMessage = self::githubErrorHandeler(array('error' => null), $test))
					{
						if (self::checkString($errorMessage['error']))
						{
							JFactory::getApplication()->enqueueMessage($errorMessage['error'], 'Error');
						}
						$repoData = false;
					}
				}
				else
				{
					$repoData = false;
				}
			}
			else
			{
				JFactory::getApplication()->enqueueMessage(JText::sprintf('COM_COMPONENTBUILDER_THE_URL_S_SET_TO_RETRIEVE_THE_PACKAGES_DOES_NOT_EXIST', $target), 'Error');
			}
		}
		// check if we could find packages
		if (isset($repoData) && self::checkJson($repoData))
		{
			$repoData = json_decode($repoData);
			if (self::checkObject($repoData) && isset($repoData->tree) && self::checkArray($repoData->tree) )
			{
				return $repoData->tree;
			}
		}
		return false;
	}

	/**
	*	get the github error messages
	*
	*	@return  array of errors on success
	* 
	*/
	protected static function githubErrorHandeler($message, &$github)
	{
		if (self::checkObject($github) && isset($github->message) && self::checkString($github->message))
		{
			// set the message
			$errorMessage = $github->message;
			// add the documentation URL
			if (isset($github->documentation_url) && self::checkString($github->documentation_url))
			{
				$errorMessage = $errorMessage.'<br />'.$github->documentation_url;
			}
			// check the message
			if (strpos($errorMessage, 'Authenticated') !== false)
			{
				// add little more help if it is an access token issue
				$errorMessage = JText::sprintf('COM_COMPONENTBUILDER_SBR_YOU_CAN_ADD_AN_BACCESS_TOKENB_TO_GETBIBLE_GLOBAL_OPTIONS_TO_MAKE_AUTHENTICATED_REQUESTS_AN_ACCESS_TOKEN_WITH_ONLY_PUBLIC_ACCESS_WILL_DO', $errorMessage);
			}
			// set error notice
			$message['error'] = $errorMessage;
			// we have error message
			return $message;
		}
		return false;
	}


	public static function getDynamicScripts($type, $fieldName = false)
	{
		// if field name is passed the convert to type
		if ($fieldName)
		{
			$fieldNames = array(
				'php_import_display' => 'display',
				'php_import_setdata' => 'setdata',
				'php_import_save' => 'save',
				'html_import_view' => 'view',
				'php_import' => 'import',
				'php_import_ext' => 'ext',
				'php_import_headers' => 'headers'
			);
			// first check if the field name is found
			if (isset($fieldNames[$type]))
			{
				$type = $fieldNames[$type];
			}
			else
			{
				return '';
			}
		}
		$script = array();
		if ('display' === $type)
		{
			// set the display script
			$script['display'][] = "\tprotected \$headerList;";
			$script['display'][] = "\tprotected \$hasPackage = false;";
			$script['display'][] = "\tprotected \$headers;";
			$script['display'][] = "\tprotected \$hasHeader = 0;";
			$script['display'][] = "\tprotected \$dataType;";
			$script['display'][] = "\n\tpublic function display(\$tpl = null)";
			$script['display'][] = "\t{";
			$script['display'][] = "\t\tif (\$this->getLayout() !== 'modal')";
			$script['display'][] = "\t\t{";
			$script['display'][] = "\t\t\t// Include helper submenu";
			$script['display'][] = "\t\t\t[[[-#-#-Component]]]Helper::addSubmenu('import');";
			$script['display'][] = "\t\t}";
			$script['display'][] = "\n\t\t\$paths = new stdClass;";
			$script['display'][] = "\t\t\$paths->first = '';";
			$script['display'][] = "\t\t\$state = \$this->get('state');";
			$script['display'][] = "\n\t\t\$this->paths = &\$paths;";
			$script['display'][] = "\t\t\$this->state = &\$state;";
			$script['display'][] = "\t\t// get global action permissions";
			$script['display'][] = "\t\t\$this->canDo = [[[-#-#-Component]]]Helper::getActions('import');";
			$script['display'][] = "\n\t\t// We don't need toolbar in the modal window.";
			$script['display'][] = "\t\tif (\$this->getLayout() !== 'modal')";
			$script['display'][] = "\t\t{";
			$script['display'][] = "\t\t\t\$this->addToolbar();";
			$script['display'][] = "\t\t\t\$this->sidebar = JHtmlSidebar::render();";
			$script['display'][] = "\t\t}";
			$script['display'][] = "\n\t\t// get the session object";
			$script['display'][] = "\t\t\$session = JFactory::getSession();";
			$script['display'][] = "\t\t// check if it has package";
			$script['display'][] = "\t\t\$this->hasPackage \t= \$session->get('hasPackage', false);";
			$script['display'][] = "\t\t\$this->dataType \t= \$session->get('dataType', false);";
			$script['display'][] = "\t\tif(\$this->hasPackage && \$this->dataType)";
			$script['display'][] = "\t\t{";
			$script['display'][] = "\t\t\t\$this->headerList \t= json_decode(\$session->get(\$this->dataType.'_VDM_IMPORTHEADERS', false),true);";
			$script['display'][] = "\t\t\t\$this->headers \t\t= [[[-#-#-Component]]]Helper::getFileHeaders(\$this->dataType);";
			$script['display'][] = "\t\t\t// clear the data type";
			$script['display'][] = "\t\t\t\$session->clear('dataType');";
			$script['display'][] = "\t\t}";
			$script['display'][] = "\n\t\t// Check for errors.";
			$script['display'][] = "\t\tif (count(\$errors = \$this->get('Errors'))){";
			$script['display'][] = "\t\t\tthrow new Exception(implode(".'"\n", $errors), 500);';
			$script['display'][] = "\t\t}";
			$script['display'][] = "\n\t\t// Display the template";
			$script['display'][] = "\t\tparent::display(\$tpl);";
			$script['display'][] = "\t}";
		}
		elseif ('setdata' === $type)
		{
			// set the setdata script
			$script['setdata'] = array();
			$script['setdata'][] = "\t/**";
			$script['setdata'][] = "\t* Set the data from the spreadsheet to the database";
			$script['setdata'][] = "\t*";
			$script['setdata'][] = "\t* @param string  \$package Paths to the uploaded package file";
			$script['setdata'][] = "\t*";
			$script['setdata'][] = "\t* @return  boolean false on failure";
			$script['setdata'][] = "\t*";
			$script['setdata'][] = "\t**/";
			$script['setdata'][] = "\tprotected function setData(\$package,\$table,\$target_headers)";
			$script['setdata'][] = "\t{";
			$script['setdata'][] = "\t\tif ([[[-#-#-Component]]]Helper::checkArray(\$target_headers))";
			$script['setdata'][] = "\t\t{";
			$script['setdata'][] = "\t\t\t// make sure the file is loaded\t\t";
			$script['setdata'][] = "\t\t\tJLoader::import('PHPExcel', JPATH_COMPONENT_ADMINISTRATOR . '/helpers');";
			$script['setdata'][] = "\t\t\t\$jinput = JFactory::getApplication()->input;";
			$script['setdata'][] = "\t\t\tforeach(\$target_headers as \$header)";
			$script['setdata'][] = "\t\t\t{";
			$script['setdata'][] = "\t\t\t\t\$data['target_headers'][\$header] = \$jinput->getString(\$header, null);";
			$script['setdata'][] = "\t\t\t}";
			$script['setdata'][] = "\t\t\t// set the data";
			$script['setdata'][] = "\t\t\tif(isset(\$package['dir']))";
			$script['setdata'][] = "\t\t\t{";
			$script['setdata'][] = "\t\t\t\t\$inputFileType = PHPExcel_IOFactory::identify(\$package['dir']);";
			$script['setdata'][] = "\t\t\t\t\$excelReader = PHPExcel_IOFactory::createReader(\$inputFileType);";
			$script['setdata'][] = "\t\t\t\t\$excelReader->setReadDataOnly(true);";
			$script['setdata'][] = "\t\t\t\t\$excelObj = \$excelReader->load(\$package['dir']);";
			$script['setdata'][] = "\t\t\t\t\$data['array'] = \$excelObj->getActiveSheet()->toArray(null, true,true,true);";
			$script['setdata'][] = "\t\t\t\t\$excelObj->disconnectWorksheets();";
			$script['setdata'][] = "\t\t\t\tunset(\$excelObj);";
			$script['setdata'][] = "\t\t\t\treturn \$this->save(\$data,\$table);";
			$script['setdata'][] = "\t\t\t}";
			$script['setdata'][] = "\t\t}";
			$script['setdata'][] = "\t\treturn false;";
			$script['setdata'][] = "\t}";
		}
		elseif ('headers' === $type)
		{
			$script['headers'] = array();
			$script['headers'][] = "\t/**";
			$script['headers'][] = "\t* Method to get header.";
			$script['headers'][] = "\t*";
			$script['headers'][] = "\t* @return mixed  An array of data items on success, false on failure.";
			$script['headers'][] = "\t*/";
			$script['headers'][] = "\tpublic function getExImPortHeaders()";
			$script['headers'][] = "\t{";
			$script['headers'][] = "\t\t// Get a db connection.";
			$script['headers'][] = "\t\t\$db = JFactory::getDbo();";
			$script['headers'][] = "\t\t// get the columns";
			$script['headers'][] = "\t\t\$columns = \$db->getTableColumns(\"#__[[[-#-#-component]]]_[[[-#-#-view]]]\");";
			$script['headers'][] = "\t\tif ([[[-#-#-Component]]]Helper::checkArray(\$columns))";
			$script['headers'][] = "\t\t{";
			$script['headers'][] = "\t\t\t// remove the headers you don't import/export.";
			$script['headers'][] = "\t\t\tunset(\$columns['asset_id']);";
			$script['headers'][] = "\t\t\tunset(\$columns['checked_out']);";
			$script['headers'][] = "\t\t\tunset(\$columns['checked_out_time']);";
			$script['headers'][] = "\t\t\t\$headers = new stdClass();";
			$script['headers'][] = "\t\t\tforeach (\$columns as \$column => \$type)";
			$script['headers'][] = "\t\t\t{";
			$script['headers'][] = "\t\t\t\t\$headers->{\$column} = \$column;";
			$script['headers'][] = "\t\t\t}";
			$script['headers'][] = "\t\t\treturn \$headers;";
			$script['headers'][] = "\t\t}";
			$script['headers'][] = "\t\treturn false;";
			$script['headers'][] = "\t}";
		}
		elseif ('save' === $type)
		{
			$script['save'] = array();
			$script['save'][] = "\t/**";
			$script['save'][] = "\t* Save the data from the file to the database";
			$script['save'][] = "\t*";
			$script['save'][] = "\t* @param string  \$package Paths to the uploaded package file";
			$script['save'][] = "\t*";
			$script['save'][] = "\t* @return  boolean false on failure";
			$script['save'][] = "\t*";
			$script['save'][] = "\t**/";
			$script['save'][] = "\tprotected function save(\$data,\$table)";
			$script['save'][] = "\t{";
			$script['save'][] = "\t\t// import the data if there is any";
			$script['save'][] = "\t\tif([[[-#-#-Component]]]Helper::checkArray(\$data['array']))";
			$script['save'][] = "\t\t{";
			$script['save'][] = "\t\t\t// get user object";
			$script['save'][] = "\t\t\t\$user  \t\t= JFactory::getUser();";
			$script['save'][] = "\t\t\t// remove header if it has headers";
			$script['save'][] = "\t\t\t\$id_key \t= \$data['target_headers']['id'];";
			$script['save'][] = "\t\t\t\$published_key \t= \$data['target_headers']['published'];";
			$script['save'][] = "\t\t\t\$ordering_key \t= \$data['target_headers']['ordering'];";
			$script['save'][] = "\t\t\t// get the first array set";
			$script['save'][] = "\t\t\t\$firstSet = reset(\$data['array']);";
			$script['save'][] = "";
			$script['save'][] = "\t\t\t// check if first array is a header array and remove if true";
			$script['save'][] = "\t\t\tif(\$firstSet[\$id_key] == 'id' || \$firstSet[\$published_key] == 'published' || \$firstSet[\$ordering_key] == 'ordering')";
			$script['save'][] = "\t\t\t{";
			$script['save'][] = "\t\t\t\tarray_shift(\$data['array']);";
			$script['save'][] = "\t\t\t}";
			$script['save'][] = "\t\t\t";
			$script['save'][] = "\t\t\t// make sure there is still values in array and that it was not only headers";
			$script['save'][] = "\t\t\tif([[[-#-#-Component]]]Helper::checkArray(\$data['array']) && \$user->authorise(\$table.'.import', 'com_[[[-#-#-component]]]') && \$user->authorise('core.import', 'com_[[[-#-#-component]]]'))";
			$script['save'][] = "\t\t\t{";
			$script['save'][] = "\t\t\t\t// set target.";
			$script['save'][] = "\t\t\t\t\$target\t= array_flip(\$data['target_headers']);";
			$script['save'][] = "\t\t\t\t// Get a db connection.";
			$script['save'][] = "\t\t\t\t\$db = JFactory::getDbo();";
			$script['save'][] = "\t\t\t\t// set some defaults";
			$script['save'][] = "\t\t\t\t\$todayDate\t\t= JFactory::getDate()->toSql();";
			$script['save'][] = "\t\t\t\t// get global action permissions";
			$script['save'][] = "\t\t\t\t\$canDo\t\t\t= [[[-#-#-Component]]]Helper::getActions(\$table);";
			$script['save'][] = "\t\t\t\t\$canEdit\t\t= \$canDo->get('core.edit');";
			$script['save'][] = "\t\t\t\t\$canState\t\t= \$canDo->get('core.edit.state');";
			$script['save'][] = "\t\t\t\t\$canCreate\t\t= \$canDo->get('core.create');";
			$script['save'][] = "\t\t\t\t\$hasAlias\t\t= \$this->getAliasesUsed(\$table);";
			$script['save'][] = "\t\t\t\t// prosses the data";
			$script['save'][] = "\t\t\t\tforeach(\$data['array'] as \$row)";
			$script['save'][] = "\t\t\t\t{";
			$script['save'][] = "\t\t\t\t\t\$found = false;";
			$script['save'][] = "\t\t\t\t\tif (isset(\$row[\$id_key]) && is_numeric(\$row[\$id_key]) && \$row[\$id_key] > 0)";
			$script['save'][] = "\t\t\t\t\t{";
			$script['save'][] = "\t\t\t\t\t\t// raw items import & update!";
			$script['save'][] = "\t\t\t\t\t\t\$query = \$db->getQuery(true);";
			$script['save'][] = "\t\t\t\t\t\t\$query";
			$script['save'][] = "\t\t\t\t\t\t\t->select('version')";
			$script['save'][] = "\t\t\t\t\t\t\t->from(\$db->quoteName('#__[[[-#-#-component]]]_'.\$table))";
			$script['save'][] = "\t\t\t\t\t\t\t->where(\$db->quoteName('id') . ' = '. \$db->quote(\$row[\$id_key]));";
			$script['save'][] = "\t\t\t\t\t\t// Reset the query using our newly populated query object.";
			$script['save'][] = "\t\t\t\t\t\t\$db->setQuery(\$query);";
			$script['save'][] = "\t\t\t\t\t\t\$db->execute();";
			$script['save'][] = "\t\t\t\t\t\t\$found = \$db->getNumRows();";
			$script['save'][] = "\t\t\t\t\t}";
			$script['save'][] = "\t\t\t\t\t";
			$script['save'][] = "\t\t\t\t\tif(\$found && \$canEdit)";
			$script['save'][] = "\t\t\t\t\t{";
			$script['save'][] = "\t\t\t\t\t\t// update item";
			$script['save'][] = "\t\t\t\t\t\t\$id \t\t= \$row[\$id_key];";
			$script['save'][] = "\t\t\t\t\t\t\$version\t= \$db->loadResult();";
			$script['save'][] = "\t\t\t\t\t\t// reset all buckets";
			$script['save'][] = "\t\t\t\t\t\t\$query \t\t= \$db->getQuery(true);";
			$script['save'][] = "\t\t\t\t\t\t\$fields \t= array();";
			$script['save'][] = "\t\t\t\t\t\t// Fields to update.";
			$script['save'][] = "\t\t\t\t\t\tforeach(\$row as \$key => \$cell)";
			$script['save'][] = "\t\t\t\t\t\t{";
			$script['save'][] = "\t\t\t\t\t\t\t// ignore column";
			$script['save'][] = "\t\t\t\t\t\t\tif ('IGNORE' == \$target[\$key])";
			$script['save'][] = "\t\t\t\t\t\t\t{";
			$script['save'][] = "\t\t\t\t\t\t\t\tcontinue;";
			$script['save'][] = "\t\t\t\t\t\t\t}";
			$script['save'][] = "\t\t\t\t\t\t\t// update modified";
			$script['save'][] = "\t\t\t\t\t\t\tif ('modified_by' == \$target[\$key])";
			$script['save'][] = "\t\t\t\t\t\t\t{";
			$script['save'][] = "\t\t\t\t\t\t\t\tcontinue;";
			$script['save'][] = "\t\t\t\t\t\t\t}";
			$script['save'][] = "\t\t\t\t\t\t\t// update modified";
			$script['save'][] = "\t\t\t\t\t\t\tif ('modified' == \$target[\$key])";
			$script['save'][] = "\t\t\t\t\t\t\t{";
			$script['save'][] = "\t\t\t\t\t\t\t\tcontinue;";
			$script['save'][] = "\t\t\t\t\t\t\t}";
			$script['save'][] = "\t\t\t\t\t\t\t// update version";
			$script['save'][] = "\t\t\t\t\t\t\tif ('version' == \$target[\$key])";
			$script['save'][] = "\t\t\t\t\t\t\t{";
			$script['save'][] = "\t\t\t\t\t\t\t\t\$cell = (int) \$version + 1;";
			$script['save'][] = "\t\t\t\t\t\t\t}";
			$script['save'][] = "\t\t\t\t\t\t\t// verify publish authority";
			$script['save'][] = "\t\t\t\t\t\t\tif ('published' == \$target[\$key] && !\$canState)";
			$script['save'][] = "\t\t\t\t\t\t\t{";
			$script['save'][] = "\t\t\t\t\t\t\t\tcontinue;";
			$script['save'][] = "\t\t\t\t\t\t\t}";
			$script['save'][] = "\t\t\t\t\t\t\t// set to update array";
			$script['save'][] = "\t\t\t\t\t\t\tif(in_array(\$key, \$data['target_headers']) && is_numeric(\$cell))";
			$script['save'][] = "\t\t\t\t\t\t\t{";
			$script['save'][] = "\t\t\t\t\t\t\t\t\$fields[] = \$db->quoteName(\$target[\$key]) . ' = ' . \$cell;";
			$script['save'][] = "\t\t\t\t\t\t\t}";
			$script['save'][] = "\t\t\t\t\t\t\telseif(in_array(\$key, \$data['target_headers']) && is_string(\$cell))";
			$script['save'][] = "\t\t\t\t\t\t\t{";
			$script['save'][] = "\t\t\t\t\t\t\t\t\$fields[] = \$db->quoteName(\$target[\$key]) . ' = ' . \$db->quote(\$cell);";
			$script['save'][] = "\t\t\t\t\t\t\t}";
			$script['save'][] = "\t\t\t\t\t\t\telseif(in_array(\$key, \$data['target_headers']) && is_null(\$cell))";
			$script['save'][] = "\t\t\t\t\t\t\t{";
			$script['save'][] = "\t\t\t\t\t\t\t\t// if import data is null then set empty";
			$script['save'][] = "\t\t\t\t\t\t\t\t\$fields[] = \$db->quoteName(\$target[\$key]) . \" = ''\";";
			$script['save'][] = "\t\t\t\t\t\t\t}";
			$script['save'][] = "\t\t\t\t\t\t}";
			$script['save'][] = "\t\t\t\t\t\t// load the defaults";
			$script['save'][] = "\t\t\t\t\t\t\$fields[]\t= \$db->quoteName('modified_by') . ' = ' . \$db->quote(\$user->id);";
			$script['save'][] = "\t\t\t\t\t\t\$fields[]\t= \$db->quoteName('modified') . ' = ' . \$db->quote(\$todayDate);";
			$script['save'][] = "\t\t\t\t\t\t// Conditions for which records should be updated.";
			$script['save'][] = "\t\t\t\t\t\t\$conditions = array(";
			$script['save'][] = "\t\t\t\t\t\t\t\$db->quoteName('id') . ' = ' . \$id";
			$script['save'][] = "\t\t\t\t\t\t);";
			$script['save'][] = "\t\t\t\t\t\t";
			$script['save'][] = "\t\t\t\t\t\t\$query->update(\$db->quoteName('#__[[[-#-#-component]]]_'.\$table))->set(\$fields)->where(\$conditions);";
			$script['save'][] = "\t\t\t\t\t\t\$db->setQuery(\$query);";
			$script['save'][] = "\t\t\t\t\t\t\$db->execute();";
			$script['save'][] = "\t\t\t\t\t}";
			$script['save'][] = "\t\t\t\t\telseif (\$canCreate)";
			$script['save'][] = "\t\t\t\t\t{";
			$script['save'][] = "\t\t\t\t\t\t// insert item";
			$script['save'][] = "\t\t\t\t\t\t\$query = \$db->getQuery(true);";
			$script['save'][] = "\t\t\t\t\t\t// reset all buckets";
			$script['save'][] = "\t\t\t\t\t\t\$columns \t= array();";
			$script['save'][] = "\t\t\t\t\t\t\$values \t= array();";
			$script['save'][] = "\t\t\t\t\t\t\$version\t= false;";
			$script['save'][] = "\t\t\t\t\t\t// Insert columns. Insert values.";
			$script['save'][] = "\t\t\t\t\t\tforeach(\$row as \$key => \$cell)";
			$script['save'][] = "\t\t\t\t\t\t{";
			$script['save'][] = "\t\t\t\t\t\t\t// ignore column";
			$script['save'][] = "\t\t\t\t\t\t\tif ('IGNORE' == \$target[\$key])";
			$script['save'][] = "\t\t\t\t\t\t\t{";
			$script['save'][] = "\t\t\t\t\t\t\t\tcontinue;";
			$script['save'][] = "\t\t\t\t\t\t\t}";
			$script['save'][] = "\t\t\t\t\t\t\t// remove id";
			$script['save'][] = "\t\t\t\t\t\t\tif ('id' == \$target[\$key])";
			$script['save'][] = "\t\t\t\t\t\t\t{";
			$script['save'][] = "\t\t\t\t\t\t\t\tcontinue;";
			$script['save'][] = "\t\t\t\t\t\t\t}";
			$script['save'][] = "\t\t\t\t\t\t\t// update created";
			$script['save'][] = "\t\t\t\t\t\t\tif ('created_by' == \$target[\$key])";
			$script['save'][] = "\t\t\t\t\t\t\t{";
			$script['save'][] = "\t\t\t\t\t\t\t\tcontinue;";
			$script['save'][] = "\t\t\t\t\t\t\t}";
			$script['save'][] = "\t\t\t\t\t\t\t// update created";
			$script['save'][] = "\t\t\t\t\t\t\tif ('created' == \$target[\$key])";
			$script['save'][] = "\t\t\t\t\t\t\t{";
			$script['save'][] = "\t\t\t\t\t\t\t\tcontinue;";
			$script['save'][] = "\t\t\t\t\t\t\t}";
			$script['save'][] = "\t\t\t\t\t\t\t// Make sure the alias is incremented";
			$script['save'][] = "\t\t\t\t\t\t\tif ('alias' == \$target[\$key])";
			$script['save'][] = "\t\t\t\t\t\t\t{";
			$script['save'][] = "\t\t\t\t\t\t\t\t\$cell = \$this->getAlias(\$cell,\$table);";
			$script['save'][] = "\t\t\t\t\t\t\t}";
			$script['save'][] = "\t\t\t\t\t\t\t// update version";
			$script['save'][] = "\t\t\t\t\t\t\tif ('version' == \$target[\$key])";
			$script['save'][] = "\t\t\t\t\t\t\t{";
			$script['save'][] = "\t\t\t\t\t\t\t\t\$cell = 1;";
			$script['save'][] = "\t\t\t\t\t\t\t\t\$version = true;";
			$script['save'][] = "\t\t\t\t\t\t\t}";
			$script['save'][] = "\t\t\t\t\t\t\t// set to insert array";
			$script['save'][] = "\t\t\t\t\t\t\tif(in_array(\$key, \$data['target_headers']) && is_numeric(\$cell))";
			$script['save'][] = "\t\t\t\t\t\t\t{";
			$script['save'][] = "\t\t\t\t\t\t\t\t\$columns[] \t= \$target[\$key];";
			$script['save'][] = "\t\t\t\t\t\t\t\t\$values[] \t= \$cell;";
			$script['save'][] = "\t\t\t\t\t\t\t}";
			$script['save'][] = "\t\t\t\t\t\t\telseif(in_array(\$key, \$data['target_headers']) && is_string(\$cell))";
			$script['save'][] = "\t\t\t\t\t\t\t{";
			$script['save'][] = "\t\t\t\t\t\t\t\t\$columns[] \t= \$target[\$key];";
			$script['save'][] = "\t\t\t\t\t\t\t\t\$values[] \t= \$db->quote(\$cell);";
			$script['save'][] = "\t\t\t\t\t\t\t}";
			$script['save'][] = "\t\t\t\t\t\t\telseif(in_array(\$key, \$data['target_headers']) && is_null(\$cell))";
			$script['save'][] = "\t\t\t\t\t\t\t{";
			$script['save'][] = "\t\t\t\t\t\t\t\t// if import data is null then set empty";
			$script['save'][] = "\t\t\t\t\t\t\t\t\$columns[] \t= \$target[\$key];";
			$script['save'][] = "\t\t\t\t\t\t\t\t\$values[] \t= \"''\";";
			$script['save'][] = "\t\t\t\t\t\t\t}";
			$script['save'][] = "\t\t\t\t\t\t}";
			$script['save'][] = "\t\t\t\t\t\t// load the defaults";
			$script['save'][] = "\t\t\t\t\t\t\$columns[] \t= 'created_by';";
			$script['save'][] = "\t\t\t\t\t\t\$values[] \t= \$db->quote(\$user->id);";
			$script['save'][] = "\t\t\t\t\t\t\$columns[] \t= 'created';";
			$script['save'][] = "\t\t\t\t\t\t\$values[] \t= \$db->quote(\$todayDate);";
			$script['save'][] = "\t\t\t\t\t\tif (!\$version)";
			$script['save'][] = "\t\t\t\t\t\t{";
			$script['save'][] = "\t\t\t\t\t\t\t\$columns[] \t= 'version';";
			$script['save'][] = "\t\t\t\t\t\t\t\$values[] \t= 1;";
			$script['save'][] = "\t\t\t\t\t\t}";
			$script['save'][] = "\t\t\t\t\t\t// Prepare the insert query.";
			$script['save'][] = "\t\t\t\t\t\t\$query";
			$script['save'][] = "\t\t\t\t\t\t\t->insert(\$db->quoteName('#__[[[-#-#-component]]]_'.\$table))";
			$script['save'][] = "\t\t\t\t\t\t\t->columns(\$db->quoteName(\$columns))";
			$script['save'][] = "\t\t\t\t\t\t\t->values(implode(',', \$values));";
			$script['save'][] = "\t\t\t\t\t\t// Set the query using our newly populated query object and execute it.";
			$script['save'][] = "\t\t\t\t\t\t\$db->setQuery(\$query);";
			$script['save'][] = "\t\t\t\t\t\t\$done = \$db->execute();";
			$script['save'][] = "\t\t\t\t\t\tif (\$done)";
			$script['save'][] = "\t\t\t\t\t\t{";
			$script['save'][] = "\t\t\t\t\t\t\t\$aId = \$db->insertid();";
			$script['save'][] = "\t\t\t\t\t\t\t// make sure the access of asset is set";
			$script['save'][] = "\t\t\t\t\t\t\t[[[-#-#-Component]]]Helper::setAsset(\$aId,\$table);";
			$script['save'][] = "\t\t\t\t\t\t}";
			$script['save'][] = "\t\t\t\t\t}";
			$script['save'][] = "\t\t\t\t\telse";
			$script['save'][] = "\t\t\t\t\t{";
			$script['save'][] = "\t\t\t\t\t\treturn false;";
			$script['save'][] = "\t\t\t\t\t}";
			$script['save'][] = "\t\t\t\t}";
			$script['save'][] = "\t\t\t\treturn true;";
			$script['save'][] = "\t\t\t}";
			$script['save'][] = "\t\t}";
			$script['save'][] = "\t\treturn false;";
			$script['save'][] = "\t}";
		}
		elseif ('view' === $type)
		{
			$script['view'] = array();
			$script['view'][] = "<script type=\"text/javascript\">";
			$script['view'][] = "<?php if (\$this->hasPackage && [[[-#-#-Component]]]Helper::checkArray(\$this->headerList)) : ?>";
			$script['view'][] = "\tJoomla.continueImport = function()";
			$script['view'][] = "\t{";
			$script['view'][] = "\t\tvar form = document.getElementById('adminForm');";
			$script['view'][] = "\t\tvar error = false;";
			$script['view'][] = "\t\tvar therequired = [<?php \$i = 0; foreach(\$this->headerList as \$name => \$title) { echo (\$i != 0)? ', \"vdm_'.\$name.'\"':'\"vdm_'.\$name.'\"'; \$i++; } ?>];";
			$script['view'][] = "\t\tfor(i = 0; i < therequired.length; i++)";
			$script['view'][] = "\t\t{";
			$script['view'][] = "\t\t\tif(jQuery('#'+therequired[i]).val() == \"\" )";
			$script['view'][] = "\t\t\t{";
			$script['view'][] = "\t\t\t\terror = true;";
			$script['view'][] = "\t\t\t\tbreak;";
			$script['view'][] = "\t\t\t}";
			$script['view'][] = "\t\t}";
			$script['view'][] = "\t\t// do field validation";
			$script['view'][] = "\t\tif (error)";
			$script['view'][] = "\t\t{";
			$script['view'][] = "\t\t\talert(\"<?php echo JTe-#-#-xt::_('COM_[[[-#-#-COMPONENT]]]_IMPORT_MSG_PLEASE_SELECT_ALL_COLUMNS', true); ?>\");";
			$script['view'][] = "\t\t}";
			$script['view'][] = "\t\telse";
			$script['view'][] = "\t\t{";
			$script['view'][] = "\t\t\tjQuery('#loading').css('display', 'block');";
			$script['view'][] = "";
			$script['view'][] = "\n\t\t\tform.gettype.value = 'continue';";
			$script['view'][] = "\t\t\tform.submit();";
			$script['view'][] = "\t\t}";
			$script['view'][] = "\t};";
			$script['view'][] = "<?php else: ?>";
			$script['view'][] = "\tJoomla.submitbutton = function()";
			$script['view'][] = "\t{";
			$script['view'][] = "\t\tvar form = document.getElementById('adminForm');";
			$script['view'][] = "";
			$script['view'][] = "\n\t\t// do field validation";
			$script['view'][] = "\t\tif (form.import_package.value == \"\")";
			$script['view'][] = "\t\t{";
			$script['view'][] = "\t\t\talert(\"<?php echo JTe-#-#-xt::_('COM_[[[-#-#-COMPONENT]]]_IMPORT_MSG_PLEASE_SELECT_A_FILE', true); ?>\");";
			$script['view'][] = "\t\t}";
			$script['view'][] = "\t\telse";
			$script['view'][] = "\t\t{";
			$script['view'][] = "\t\t\tjQuery('#loading').css('display', 'block');";
			$script['view'][] = "";
			$script['view'][] = "\n\t\t\tform.gettype.value = 'upload';";
			$script['view'][] = "\t\t\tform.submit();";
			$script['view'][] = "\t\t}";
			$script['view'][] = "\t};";
			$script['view'][] = "";
			$script['view'][] = "\n\tJoomla.submitbutton3 = function()";
			$script['view'][] = "\t{";
			$script['view'][] = "\t\tvar form = document.getElementById('adminForm');";
			$script['view'][] = "";
			$script['view'][] = "\n\t\t// do field validation";
			$script['view'][] = "\t\tif (form.import_directory.value == \"\"){";
			$script['view'][] = "\t\t\talert(\"<?php echo JTe-#-#-xt::_('COM_[[[-#-#-COMPONENT]]]_IMPORT_MSG_PLEASE_SELECT_A_DIRECTORY', true); ?>\");";
			$script['view'][] = "\t\t}";
			$script['view'][] = "\t\telse";
			$script['view'][] = "\t\t{";
			$script['view'][] = "\t\t\tjQuery('#loading').css('display', 'block');";
			$script['view'][] = "";
			$script['view'][] = "\n\t\t\tform.gettype.value = 'folder';";
			$script['view'][] = "\t\t\tform.submit();";
			$script['view'][] = "\t\t}";
			$script['view'][] = "\t};";
			$script['view'][] = "";
			$script['view'][] = "\n\tJoomla.submitbutton4 = function()";
			$script['view'][] = "\t{";
			$script['view'][] = "\t\tvar form = document.getElementById('adminForm');";
			$script['view'][] = "";
			$script['view'][] = "\n\t\t// do field validation";
			$script['view'][] = "\t\tif (form.import_url.value == \"\" || form.import_url.value == \"http://\")";
			$script['view'][] = "\t\t{";
			$script['view'][] = "\t\t\talert(\"<?php echo JTe-#-#-xt::_('COM_[[[-#-#-COMPONENT]]]_IMPORT_MSG_ENTER_A_URL', true); ?>\");";
			$script['view'][] = "\t\t}";
			$script['view'][] = "\t\telse";
			$script['view'][] = "\t\t{";
			$script['view'][] = "\t\t\tjQuery('#loading').css('display', 'block');";
			$script['view'][] = "";
			$script['view'][] = "\n\t\t\tform.gettype.value = 'url';";
			$script['view'][] = "\t\t\tform.submit();";
			$script['view'][] = "\t\t}";
			$script['view'][] = "\t};";
			$script['view'][] = "<?php endif; ?>";
			$script['view'][] = "";
			$script['view'][] = "\n// Add spindle-wheel for importations:";
			$script['view'][] = "jQuery(document).ready(function(\$) {";
			$script['view'][] = "\tvar outerDiv = \$('body');";
			$script['view'][] = "";
			$script['view'][] = "\n\t\$('<div id=\"loading\"></div>')";
			$script['view'][] = "\t\t.css(\"background\", \"rgba(255, 255, 255, .8) url('components/com_[[[-#-#-component]]]/assets/images/import.gif') 50% 15% no-repeat\")";
			$script['view'][] = "\t\t.css(\"top\", outerDiv.position().top - \$(window).scrollTop())";
			$script['view'][] = "\t\t.css(\"left\", outerDiv.position().left - \$(window).scrollLeft())";
			$script['view'][] = "\t\t.css(\"width\", outerDiv.width())";
			$script['view'][] = "\t\t.css(\"height\", outerDiv.height())";
			$script['view'][] = "\t\t.css(\"position\", \"fixed\")";
			$script['view'][] = "\t\t.css(\"opacity\", \"0.80\")";
			$script['view'][] = "\t\t.css(\"-ms-filter\", \"progid:DXImageTransform.Microsoft.Alpha(Opacity = 80)\")";
			$script['view'][] = "\t\t.css(\"filter\", \"alpha(opacity = 80)\")";
			$script['view'][] = "\t\t.css(\"display\", \"none\")";
			$script['view'][] = "\t\t.appendTo(outerDiv);";
			$script['view'][] = "});";
			$script['view'][] = "";
			$script['view'][] = "\n</script>";
			$script['view'][] = "";
			$script['view'][] = "\n<div id=\"installer-import\" class=\"clearfix\">";
			$script['view'][] = "<form enctype=\"multipart/form-data\" action=\"<?php echo JRoute::_('index.php?option=com_[[[-#-#-component]]]&view=import_[[[-#-#-views]]]');?>\" method=\"post\" name=\"adminForm\" id=\"adminForm\" class=\"form-horizontal form-validate\">";
			$script['view'][] = "";
			$script['view'][] = "\n\t<?php if (!empty( \$this->sidebar)) : ?>";
			$script['view'][] = "\t\t<div id=\"j-sidebar-container\" class=\"span2\">";
			$script['view'][] = "\t\t\t<?php echo \$this->sidebar; ?>";
			$script['view'][] = "\t\t</div>";
			$script['view'][] = "\t\t<div id=\"j-main-container\" class=\"span10\">";
			$script['view'][] = "\t<?php else : ?>";
			$script['view'][] = "\t\t<div id=\"j-main-container\">";
			$script['view'][] = "\t<?php endif;?>";
			$script['view'][] = "";
			$script['view'][] = "\n\t<?php if (\$this->hasPackage && [[[-#-#-Component]]]Helper::checkArray(\$this->headerList) && [[[-#-#-Component]]]Helper::checkArray(\$this->headers)) : ?>";
			$script['view'][] = "\t\t<fieldset class=\"uploadform\">";
			$script['view'][] = "\t\t\t<legend><?php echo JTe-#-#-xt::_('COM_[[[-#-#-COMPONENT]]]_IMPORT_LINK_FILE_TO_TABLE_COLUMNS'); ?></legend>";
			$script['view'][] = "\t\t\t<div class=\"control-group\">";
			$script['view'][] = "\t\t\t\t<label class=\"control-label\" ><h4><?php echo JTe-#-#-xt::_('COM_[[[-#-#-COMPONENT]]]_IMPORT_TABLE_COLUMNS'); ?></h4></label>";
			$script['view'][] = "\t\t\t\t<div class=\"controls\">";
			$script['view'][] = "\t\t\t\t\t<label class=\"control-label\" ><h4><?php echo JTe-#-#-xt::_('COM_[[[-#-#-COMPONENT]]]_IMPORT_FILE_COLUMNS'); ?></h4></label>";
			$script['view'][] = "\t\t\t\t</div>";
			$script['view'][] = "\t\t\t</div>";
			$script['view'][] = "\t\t\t<?php foreach(\$this->headerList as \$name => \$title): ?>";
			$script['view'][] = "\t\t\t\t<div class=\"control-group\">";
			$script['view'][] = "\t\t\t\t\t<label for=\"<?php echo \$name; ?>\" class=\"control-label\" ><?php echo \$title; ?></label>";
			$script['view'][] = "\t\t\t\t\t<div class=\"controls\">";
			$script['view'][] = "\t\t\t\t\t\t<select  name=\"<?php echo \$name; ?>\"  id=\"vdm_<?php echo \$name; ?>\" required class=\"required input_box\" >";
			$script['view'][] = "\t\t\t\t\t\t\t<option value=\"\"><?php echo JTe-#-#-xt::_('COM_[[[-#-#-COMPONENT]]]_IMPORT_PLEASE_SELECT_COLUMN'); ?></option>";
			$script['view'][] = "\t\t\t\t\t\t\t<option value=\"IGNORE\"><?php echo JTe-#-#-xt::_('COM_[[[-#-#-COMPONENT]]]_IMPORT_IGNORE_COLUMN'); ?></option>";
			$script['view'][] = "\t\t\t\t\t\t\t<?php foreach(\$this->headers as \$value => \$option): ?>";
			$script['view'][] = "\t\t\t\t\t\t\t\t<?php \$selected = (strtolower(\$option) ==  strtolower (\$title) || strtolower(\$option) == strtolower(\$name))? 'selected=\"selected\"':''; ?>";
			$script['view'][] = "\t\t\t\t\t\t\t\t<option value=\"<?php echo [[[-#-#-Component]]]Helper::htmlEscape(\$value); ?>\" class=\"required\" <?php echo \$selected ?>><?php echo [[[-#-#-Component]]]Helper::htmlEscape(\$option); ?></option>";
			$script['view'][] = "\t\t\t\t\t\t\t<?php endforeach; ?>";
			$script['view'][] = "\t\t\t\t\t\t</select>";
			$script['view'][] = "\t\t\t\t\t</div>";
			$script['view'][] = "\t\t\t\t</div>";
			$script['view'][] = "\t\t\t<?php endforeach; ?>";
			$script['view'][] = "\t\t\t<div class=\"form-actions\">";
			$script['view'][] = "\t\t\t\t<input class=\"btn btn-primary\" type=\"button\" value=\"<?php echo JTe-#-#-xt::_('COM_[[[-#-#-COMPONENT]]]_IMPORT_CONTINUE'); ?>\" onclick=\"Joomla.continueImport()\" />";
			$script['view'][] = "\t\t\t</div>";
			$script['view'][] = "\t\t</fieldset>";
			$script['view'][] = "\t\t<input type=\"hidden\" name=\"gettype\" value=\"continue\" />";
			$script['view'][] = "\t<?php else: ?>";
			$script['view'][] = "\t\t<?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'upload')); ?>";
			$script['view'][] = "\t\t";
			$script['view'][] = "\t\t<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'upload', JTe-#-#-xt::_('COM_[[[-#-#-COMPONENT]]]_IMPORT_FROM_UPLOAD', true)); ?>";
			$script['view'][] = "\t\t\t<fieldset class=\"uploadform\">";
			$script['view'][] = "\t\t\t\t<legend><?php echo JTe-#-#-xt::_('COM_[[[-#-#-COMPONENT]]]_IMPORT_UPDATE_DATA'); ?></legend>";
			$script['view'][] = "\t\t\t\t<div class=\"control-group\">";
			$script['view'][] = "\t\t\t\t\t<label for=\"import_package\" class=\"control-label\"><?php echo JTe-#-#-xt::_('COM_[[[-#-#-COMPONENT]]]_IMPORT_SELECT_FILE'); ?></label>";
			$script['view'][] = "\t\t\t\t\t<div class=\"controls\">";
			$script['view'][] = "\t\t\t\t\t\t<input class=\"input_box\" id=\"import_package\" name=\"import_package\" type=\"file\" size=\"57\" />";
			$script['view'][] = "\t\t\t\t\t</div>";
			$script['view'][] = "\t\t\t\t</div>";
			$script['view'][] = "\t\t\t\t<div class=\"form-actions\">";
			$script['view'][] = "\t\t\t\t\t<input class=\"btn btn-primary\" type=\"button\" value=\"<?php echo JTe-#-#-xt::_('COM_[[[-#-#-COMPONENT]]]_IMPORT_UPLOAD_BOTTON'); ?>\" onclick=\"Joomla.submitbutton()\" />&nbsp;&nbsp;&nbsp;<small><?php echo JTe-#-#-xt::_('COM_[[[-#-#-COMPONENT]]]_IMPORT_FORMATS_ACCEPTED'); ?> (.csv .xls .ods)</small>";
			$script['view'][] = "\t\t\t\t</div>";
			$script['view'][] = "\t\t\t</fieldset>";
			$script['view'][] = "\t\t<?php echo JHtml::_('bootstrap.endTab'); ?>";
			$script['view'][] = "\t\t";
			$script['view'][] = "\t\t<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'directory', JTe-#-#-xt::_('COM_[[[-#-#-COMPONENT]]]_IMPORT_FROM_DIRECTORY', true)); ?>";
			$script['view'][] = "\t\t\t<fieldset class=\"uploadform\">";
			$script['view'][] = "\t\t\t\t<legend><?php echo JTe-#-#-xt::_('COM_[[[-#-#-COMPONENT]]]_IMPORT_UPDATE_DATA'); ?></legend>";
			$script['view'][] = "\t\t\t\t<div class=\"control-group\">";
			$script['view'][] = "\t\t\t\t\t<label for=\"import_directory\" class=\"control-label\"><?php echo JTe-#-#-xt::_('COM_[[[-#-#-COMPONENT]]]_IMPORT_SELECT_FILE_DIRECTORY'); ?></label>";
			$script['view'][] = "\t\t\t\t\t<div class=\"controls\">";
			$script['view'][] = "\t\t\t\t\t\t<input type=\"text\" id=\"import_directory\" name=\"import_directory\" class=\"span5 input_box\" size=\"70\" value=\"<?php echo \$this->state->get('import.directory'); ?>\" />";
			$script['view'][] = "\t\t\t\t\t</div>";
			$script['view'][] = "\t\t\t\t</div>";
			$script['view'][] = "\t\t\t\t<div class=\"form-actions\">";
			$script['view'][] = "\t\t\t\t\t<input type=\"button\" class=\"btn btn-primary\" value=\"<?php echo JTe-#-#-xt::_('COM_[[[-#-#-COMPONENT]]]_IMPORT_GET_BOTTON'); ?>\" onclick=\"Joomla.submitbutton3()\" />&nbsp;&nbsp;&nbsp;<small><?php echo JTe-#-#-xt::_('COM_[[[-#-#-COMPONENT]]]_IMPORT_FORMATS_ACCEPTED'); ?> (.csv .xls .ods)</small>";
			$script['view'][] = "\t\t\t\t</div>";
			$script['view'][] = "\t\t\t\t</fieldset>";
			$script['view'][] = "\t\t<?php echo JHtml::_('bootstrap.endTab'); ?>";
			$script['view'][] = "";
			$script['view'][] = "\n\t\t<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'url', JTe-#-#-xt::_('COM_[[[-#-#-COMPONENT]]]_IMPORT_FROM_URL', true)); ?>";
			$script['view'][] = "\t\t\t<fieldset class=\"uploadform\">";
			$script['view'][] = "\t\t\t\t<legend><?php echo JTe-#-#-xt::_('COM_[[[-#-#-COMPONENT]]]_IMPORT_UPDATE_DATA'); ?></legend>";
			$script['view'][] = "\t\t\t\t<div class=\"control-group\">";
			$script['view'][] = "\t\t\t\t\t<label for=\"import_url\" class=\"control-label\"><?php echo JTe-#-#-xt::_('COM_[[[-#-#-COMPONENT]]]_IMPORT_SELECT_FILE_URL'); ?></label>";
			$script['view'][] = "\t\t\t\t\t<div class=\"controls\">";
			$script['view'][] = "\t\t\t\t\t\t<input type=\"text\" id=\"import_url\" name=\"import_url\" class=\"span5 input_box\" size=\"70\" value=\"http://\" />";
			$script['view'][] = "\t\t\t\t\t</div>";
			$script['view'][] = "\t\t\t\t</div>";
			$script['view'][] = "\t\t\t\t<div class=\"form-actions\">";
			$script['view'][] = "\t\t\t\t\t<input type=\"button\" class=\"btn btn-primary\" value=\"<?php echo JTe-#-#-xt::_('COM_[[[-#-#-COMPONENT]]]_IMPORT_GET_BOTTON'); ?>\" onclick=\"Joomla.submitbutton4()\" />&nbsp;&nbsp;&nbsp;<small><?php echo JTe-#-#-xt::_('COM_[[[-#-#-COMPONENT]]]_IMPORT_FORMATS_ACCEPTED'); ?> (.csv .xls .ods)</small>";
			$script['view'][] = "\t\t\t\t</div>";
			$script['view'][] = "\t\t\t</fieldset>";
			$script['view'][] = "\t\t<?php echo JHtml::_('bootstrap.endTab'); ?>";
			$script['view'][] = "\t\t<?php echo JHtml::_('bootstrap.endTabSet'); ?>";
			$script['view'][] = "\t\t<input type=\"hidden\" name=\"gettype\" value=\"upload\" />";
			$script['view'][] = "\t<?php endif; ?>";
			$script['view'][] = "\t<input type=\"hidden\" name=\"task\" value=\"import_[[[-#-#-views]]].import\" />";
			$script['view'][] = "\t<?php echo JHtml::_('form.token'); ?>";
			$script['view'][] = "</form>";
			$script['view'][] = "</div>";
		}
		elseif ('import' === $type)
		{
			$script['import'] = array();
			$script['import'][] = "\t/**";
			$script['import'][] = "\t * Import an spreadsheet from either folder, url or upload.";
			$script['import'][] = "\t *";
			$script['import'][] = "\t * @return  boolean result of import";
			$script['import'][] = "\t *";
			$script['import'][] = "\t */";
			$script['import'][] = "\tpublic function import()";
			$script['import'][] = "\t{";
			$script['import'][] = "\t\t\$this->setState('action', 'import');";
			$script['import'][] = "\t\t\$app \t\t= JFactory::getApplication();";
			$script['import'][] = "\t\t\$session \t= JFactory::getSession();";
			$script['import'][] = "\t\t\$package \t= null;";
			$script['import'][] = "\t\t\$continue\t= false;";
			$script['import'][] = "\t\t// get import type";
			$script['import'][] = "\t\t\$this->getType = \$app->input->getString('gettype', NULL);";
			$script['import'][] = "\t\t// get import type";
			$script['import'][] = "\t\t\$this->dataType\t= \$session->get('dataType_VDM_IMPORTINTO', NULL);";
			$script['import'][] = "\n\t\tif (\$package === null)";
			$script['import'][] = "\t\t{";
			$script['import'][] = "\t\t\tswitch (\$this->getType)";
			$script['import'][] = "\t\t\t{";
			$script['import'][] = "\t\t\t\tcase 'folder':";
			$script['import'][] = "\t\t\t\t\t// Remember the 'Import from Directory' path.";
			$script['import'][] = "\t\t\t\t\t\$app->getUserStateFromRequest(\$this->_context . '.import_directory', 'import_directory');";
			$script['import'][] = "\t\t\t\t\t\$package = \$this->_getPackageFromFolder();";
			$script['import'][] = "\t\t\t\t\tbreak;";
			$script['import'][] = "\n\t\t\t\tcase 'upload':";
			$script['import'][] = "\t\t\t\t\t\$package = \$this->_getPackageFromUpload();";
			$script['import'][] = "\t\t\t\t\tbreak;";
			$script['import'][] = "\n\t\t\t\tcase 'url':";
			$script['import'][] = "\t\t\t\t\t\$package = \$this->_getPackageFromUrl();";
			$script['import'][] = "\t\t\t\t\tbreak;";
			$script['import'][] = "\n\t\t\t\tcase 'continue':";
			$script['import'][] = "\t\t\t\t\t\$continue \t= true;";
			$script['import'][] = "\t\t\t\t\t\$package\t= \$session->get('package', null);";
			$script['import'][] = "\t\t\t\t\t\$package\t= json_decode(\$package, true);";
			$script['import'][] = "\t\t\t\t\t// clear session";
			$script['import'][] = "\t\t\t\t\t\$session->clear('package');";
			$script['import'][] = "\t\t\t\t\t\$session->clear('dataType');";
			$script['import'][] = "\t\t\t\t\t\$session->clear('hasPackage');";
			$script['import'][] = "\t\t\t\t\tbreak;";
			$script['import'][] = "\n\t\t\t\tdefault:";
			$script['import'][] = "\t\t\t\t\t\$app->setUserState('com_[[[-#-#-component]]].message', JTe-#-#-xt::_('COM_[[[-#-#-COMPONENT]]]_IMPORT_NO_IMPORT_TYPE_FOUND'));";
			$script['import'][] = "\n\t\t\t\t\treturn false;";
			$script['import'][] = "\t\t\t\t\tbreak;";
			$script['import'][] = "\t\t\t}";
			$script['import'][] = "\t\t}";
			$script['import'][] = "\t\t// Was the package valid?";
			$script['import'][] = "\t\tif (!\$package || !\$package['type'])";
			$script['import'][] = "\t\t{";
			$script['import'][] = "\t\t\tif (in_array(\$this->getType, array('upload', 'url')))";
			$script['import'][] = "\t\t\t{";
			$script['import'][] = "\t\t\t\t\$this->remove(\$package['packagename']);";
			$script['import'][] = "\t\t\t}";
			$script['import'][] = "\n\t\t\t\$app->setUserState('com_[[[-#-#-component]]].message', JTe-#-#-xt::_('COM_[[[-#-#-COMPONENT]]]_IMPORT_UNABLE_TO_FIND_IMPORT_PACKAGE'));";
			$script['import'][] = "\t\t\treturn false;";
			$script['import'][] = "\t\t}";
			$script['import'][] = "\t\t";
			$script['import'][] = "\t\t// first link data to table headers";
			$script['import'][] = "\t\tif(!\$continue){";
			$script['import'][] = "\t\t\t\$package\t= json_encode(\$package);";
			$script['import'][] = "\t\t\t\$session->set('package', \$package);";
			$script['import'][] = "\t\t\t\$session->set('dataType', \$this->dataType);";
			$script['import'][] = "\t\t\t\$session->set('hasPackage', true);";
			$script['import'][] = "\t\t\treturn true;";
			$script['import'][] = "\t\t}";
			$script['import'][] = "\t\t// set the data";
			$script['import'][] = "\t\t\$headerList = json_decode(\$session->get(\$this->dataType.'_VDM_IMPORTHEADERS', false), true);";
			$script['import'][] = "\t\tif (!\$this->setData(\$package,\$this->dataType,\$headerList))";
			$script['import'][] = "\t\t{";
			$script['import'][] = "\t\t\t// There was an error importing the package";
			$script['import'][] = "\t\t\t\$msg = JTe-#-#-xt::_('COM_[[[-#-#-COMPONENT]]]_IMPORT_ERROR');";
			$script['import'][] = "\t\t\t\$back = \$session->get('backto_VDM_IMPORT', NULL);";
			$script['import'][] = "\t\t\tif (\$back)";
			$script['import'][] = "\t\t\t{";
			$script['import'][] = "\t\t\t\t\$app->setUserState('com_[[[-#-#-component]]].redirect_url', 'index.php?option=com_[[[-#-#-component]]]&view='.\$back);";
			$script['import'][] = "\t\t\t\t\$session->clear('backto_VDM_IMPORT');";
			$script['import'][] = "\t\t\t}";
			$script['import'][] = "\t\t\t\$result = false;";
			$script['import'][] = "\t\t}";
			$script['import'][] = "\t\telse";
			$script['import'][] = "\t\t{";
			$script['import'][] = "\t\t\t// Package imported sucessfully";
			$script['import'][] = "\t\t\t\$msg = JTe-#-#-xt::sprintf('COM_[[[-#-#-COMPONENT]]]_IMPORT_SUCCESS', \$package['packagename']);";
			$script['import'][] = "\t\t\t\$back = \$session->get('backto_VDM_IMPORT', NULL);";
			$script['import'][] = "\t\t\tif (\$back)";
			$script['import'][] = "\t\t\t{";
			$script['import'][] = "\t\t\t    \$app->setUserState('com_[[[-#-#-component]]].redirect_url', 'index.php?option=com_[[[-#-#-component]]]&view='.\$back);";
			$script['import'][] = "\t\t\t    \$session->clear('backto_VDM_IMPORT');";
			$script['import'][] = "\t\t\t}";
			$script['import'][] = "\t\t\t\$result = true;";
			$script['import'][] = "\t\t}";
			$script['import'][] = "\n\t\t// Set some model state values";
			$script['import'][] = "\t\t\$app->enqueueMessage(\$msg);";
			$script['import'][] = "\n\t\t// remove file after import";
			$script['import'][] = "\t\t\$this->remove(\$package['packagename']);";
			$script['import'][] = "\t\t\$session->clear(\$this->getType.'_VDM_IMPORTHEADERS');";
			$script['import'][] = "\t\treturn \$result;";
			$script['import'][] = "\t}";
		}
		elseif ('ext' === $type)
		{
			$script['ext'][] = "\t/**";
			$script['ext'][] = "\t * Check the extension";
			$script['ext'][] = "\t *";
			$script['ext'][] = "\t * @param   string  \$file    Name of the uploaded file";
			$script['ext'][] = "\t *";
			$script['ext'][] = "\t * @return  boolean  True on success";
			$script['ext'][] = "\t *";
			$script['ext'][] = "\t */";
			$script['ext'][] = "\tprotected function checkExtension(\$file)";
			$script['ext'][] = "\t{";
			$script['ext'][] = "\t\t// check the extention";
			$script['ext'][] = "\t\tswitch(strtolower(pathinfo(\$file, PATHINFO_EXTENSION)))";
			$script['ext'][] = "\t\t{";
			$script['ext'][] = "\t\t\tcase 'xls':";
			$script['ext'][] = "\t\t\tcase 'ods':";
			$script['ext'][] = "\t\t\tcase 'csv':";
			$script['ext'][] = "\t\t\treturn true;";
			$script['ext'][] = "\t\t\tbreak;";
			$script['ext'][] = "\t\t}";
			$script['ext'][] = "\t\treturn false;";
			$script['ext'][] = "\t}";
		}
		elseif ('routerparse' === $type)
		{
			$script['routerparse'][] = "\t\t\t\t// default script in switch for this view";
			$script['routerparse'][] = "\t\t\t\t\$vars['view'] = '[[[-#-#-sview]]]';";
			$script['routerparse'][] = "\t\t\t\tif (is_numeric(\$segments[\$count-1]))";
			$script['routerparse'][] = "\t\t\t\t{";
			$script['routerparse'][] = "\t\t\t\t\t\$vars['id'] = (int) \$segments[\$count-1];";
			$script['routerparse'][] = "\t\t\t\t}";
			$script['routerparse'][] = "\t\t\t\telseif (\$segments[\$count-1])";
			$script['routerparse'][] = "\t\t\t\t{";
			$script['routerparse'][] = "\t\t\t\t\t\$id = \$this->getVar('[[[-#-#-sview]]]', \$segments[\$count-1], 'alias', 'id');";
			$script['routerparse'][] = "\t\t\t\t\tif(\$id)";
			$script['routerparse'][] = "\t\t\t\t\t{";
			$script['routerparse'][] = "\t\t\t\t\t\t\$vars['id'] = \$id;";
			$script['routerparse'][] = "\t\t\t\t\t}";
			$script['routerparse'][] = "\t\t\t\t}";
		}
		// return the needed script
		if (isset($script[$type]))
		{
			return str_replace('-#-#-', '', implode("\n",$script[$type]));
		}
		return false;
	}


	/**
	* get between
	* 
	* @param  string          $content    The content to search
	* @param  string          $start        The starting value
	* @param  string          $end         The ending value
	* @param  string          $default     The default value if none found
	*
	* @return  string          On success / empty string on failure 
	* 
	*/
	public static function getBetween($content, $start, $end, $default = '')
	{
		$r = explode($start, $content);
		if (isset($r[1]))
		{
			$r = explode($end, $r[1]);
			return $r[0];
		}
		return $default;
	}

	/**
	* get all between
	* 
	* @param  string          $content    The content to search
	* @param  string          $start        The starting value
	* @param  string          $end         The ending value
	*
	* @return  array          On success
	* 
	*/
	public static function getAllBetween($content, $start, $end)
	{
		// reset bucket
		$bucket = array();
		for ($i = 0; ; $i++)
		{
			// search for string
			$found = self::getBetween($content,$start,$end);
			if (self::checkString($found))
			{
				// add to bucket
				$bucket[] = $found;
				// build removal string
				$remove = $start.$found.$end;
				// remove from content
				$content = str_replace($remove,'',$content);
			}
			else
			{
				break;
			}
			// safety catch
			if ($i == 500)
			{
				break;
			}
		}
		// only return unique array of values
		return  array_unique($bucket);
	}


	/**
	 * Field Grouping https://docs.joomla.org/Form_field
	 **/
	protected static $fieldGroups = array(
		'default' => array(
			'accesslevel', 'cachehandler', 'calendar', 'captcha', 'category', 'checkbox', 'checkboxes', 'chromestyle',
			'color', 'combo', 'componentlayout', 'contentlanguage', 'contenttype', 'databaseconnection', //  'components', (TODO) must be added but still in use as a custom field in JCB
			'editor', 'editors', 'email', 'file', 'file', 'filelist', 'folderlist', 'groupedlist', 'headertag', 'helpsite', 'hidden', 'imagelist',
			'integer', 'language', 'list', 'media', 'menu', 'menuitem', 'meter', 'modulelayout', 'moduleorder', 'moduleposition',
			'moduletag', 'note', 'number', 'password', 'plugins', 'predefinedlist', 'radio', 'range', 'repeatable', 'rules',
			'sessionhandler', 'spacer', 'sql', 'subform', 'tag', 'tel', 'templatestyle', 'text', 'textarea', 'timezone', 'url', 'user', 'usergroup'
		),
		'plain' => array(
			'cachehandler', 'calendar', 'checkbox', 'chromestyle', 'color', 'componentlayout', 'contenttype', 'editor', 'editors',
			'email', 'file', 'headertag', 'helpsite', 'hidden', 'integer', 'language', 'media', 'menu', 'menuitem', 'meter', 'modulelayout',
			'moduleorder', 'moduletag', 'number', 'password', 'range', 'rules', 'tag', 'tel', 'text', 'textarea', 'timezone', 'url', 'user', 'usergroup'
		),
		'option' => array(
			'accesslevel', 'category', 'checkboxes', 'combo', 'contentlanguage', 'databaseconnection', // 'components',  (TODO) must be added but still in use as a custom field in JCB
			'filelist', 'folderlist', 'imagelist', 'list', 'plugins', 'predefinedlist', 'radio', 'sessionhandler', 'sql'
		),
		'text' => array(
			'calendar', 'color', 'editor', 'email', 'number', 'password', 'range', 'tel', 'text', 'textarea', 'url'
		),
		'list' => array(
			'checkbox', 'checkboxes', 'list', 'radio'
		),
		'dynamic' => array(
			'category', 'file', 'filelist', 'folderlist', 'headertag', 'imagelist', 'integer', 'media', 'meter', 'rules', 'tag', 'timezone', 'user'
		),
		'spacer' => array(
			'note', 'spacer'
		),
		'special' => array(
			'contentlanguage', 'groupedlist', 'moduleposition', 'plugin', 'repeatable', 'subform', 'templatestyle'
		)
	);

	/**
	 * Field Checker
	 *
	 * @param   string   $type The field type
	 * @param   boolean  $option The field grouping
	 *
	 * @return  boolean if the field was found
	 */
	public static function fieldCheck($type, $option = 'default')
	{
		// now check
		if (isset(self::$fieldGroups[$option]) && in_array($type, self::$fieldGroups[$option]))
		{
			return true;
		}
		return false;
	}

	/**
	 * get the spacer IDs
	 *
	 * @return  array  ids of the spacer field types
	 */
	public static function getSpacerIds()
	{
		// get the database object to use quote
		$db = JFactory::getDbo();
		return self::getVars('fieldtype', (array) array_map(function($name) use($db) { return $db->quote(ucfirst($name)); }, self::$fieldGroups['spacer']), 'name', 'id');
	}


	/**
	 * open base64 string if stored as base64
	 *
	 * @param   string   $data   The base64 string
	 * @param   string   $key    We store the string with that suffix :)
	 * @param   string   $default    The default switch
	 *
	 * @return  string   The opened string
	 *
	 */
	public static function openValidBase64($data, $key = '__.o0=base64=Oo.__', $default = 'string')
	{
		// check that we have a string
		if (self::checkString($data))
		{
			// check if we have a key
			if (self::checkString($key))
			{
				if (strpos($data, $key) !== false)
				{
					return base64_decode(str_replace($key, '', $data));
				}
			}
			// fallback to this, not perfect method
			if (base64_encode(base64_decode($data, true)) === $data)
			{
				return base64_decode($data);
			}
		}
		// check if we should just return the string
		if ('string' === $default)
		{
			return $data;
		}
		return $default;
	}


	/**
	* 	prepare base64 string for url
	**/
	public static function base64_urlencode($string, $encode = false)
	{
		if ($encode)
		{
			$string = base64_encode($string);
		}
		return str_replace(array('+', '/'), array('-', '_'), $string);
	}

	/**
	* 	prepare base64 string form url
	**/
	public static function base64_urldecode($string, $decode = false)
	{
		$string = str_replace(array('-', '_'), array('+', '/'), $string);
		if ($decode)
		{
			$string = base64_decode($string);
		}
		return $string;
	}


	/**
	*	Check if the url exist
	* 
	*	@param  string   $url   The url to check
	*
	*	@return  bool      If exist true
	* 
	*/
	public static function urlExists($url)
	{
		$exists = false;
		// check if we can use curl
		if (function_exists('curl_version'))
		{
			// initiate curl
			$ch = curl_init($url);
			// CURLOPT_NOBODY (do not return body)
			curl_setopt($ch, CURLOPT_NOBODY, true);
			// make call
			$result = curl_exec($ch);
			// check return value
			if ($result !== false)
			{
				// get the http CODE
				$statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
				if ($statusCode !== 404)
				{
					$exists = true;
				}
			}
			// close the connection
			curl_close($ch);
		}
		elseif ($headers = @get_headers($url))
		{
			if(isset($headers[0]) && is_string($headers[0]) && strpos($headers[0],'404') === false)
			{
				$exists = true;
			}
		}
		return $exists;
	}

	/**
	* Get the file path or url
	*
	* @param  string   $type              The (url/path) type to return
	* @param  string   $target            The Params Target name (if set)
	* @param  string   $fileType          The kind of filename to generate (if not set no file name is generated)
	* @param  string   $key               The key to adjust the filename (if not set ignored)
	* @param  string   $default           The default path if not set in Params (fallback path)
	* @param  bool     $createIfNotSet    The switch to create the folder if not found
	*
	* @return  string    On success the path or url is returned based on the type requested
	*
	*/
	public static function getFilePath($type = 'path', $target = 'filepath', $fileType = null, $key = '', $default = '', $createIfNotSet = true)
	{
		// make sure to always have a string/path
		if(!self::checkString($default))
		{
			$default = JPATH_SITE . '/images/';
		}
		// get the global settings
		if (!self::checkObject(self::$params))
		{
			self::$params = JComponentHelper::getParams('com_componentbuilder');
		}
		$filePath = self::$params->get($target, $default);
		// check the file path (revert to default only of not a hidden file path)
		if ('hiddenfilepath' !== $target && strpos($filePath, JPATH_SITE) === false)
		{
			$filePath = $default;
		}
		jimport('joomla.filesystem.folder');
		// create the folder if it does not exist
		if ($createIfNotSet && !JFolder::exists($filePath))
		{
			JFolder::create($filePath);
		}
		// setup the file name
		$fileName = '';
		// Get basic key
		$basickey = 'Th!s_iS_n0t_sAfe_buT_b3tter_then_n0thiug';
		if (method_exists(get_called_class(), "getCryptKey")) 
		{
			$basickey = self::getCryptKey('basic', $basickey);
		}
		// check the key
		if (!self::checkString($key))
		{
			$key = 'vDm';
		}
		// set the file name
		if (self::checkString($fileType))
		{
			// set the name
			$fileName = trim(md5($type.$target.$basickey.$key) . '.' . trim($fileType, '.'));
		}
		else
		{
			$fileName = trim(md5($type.$target.$basickey.$key)) . '.txt';
		}
		// return the url
		if ('url' === $type)
		{
			if (strpos($filePath, JPATH_SITE) !== false)
			{
				$filePath = trim( str_replace( JPATH_SITE, '', $filePath), '/');
				return JURI::root() . $filePath . '/' . $fileName;
			}
			// since the path is behind the root folder of the site, return only the root url (may be used to build the link)
			return JURI::root();
		}
		// sanitize the path
		return '/' . trim( $filePath, '/' ) . '/' . $fileName;
	}


	/**
	* Get the file path or url
	* 
	* @param  string   $type              The (url/path) type to return
	* @param  string   $target            The Params Target name (if set)
	* @param  string   $default           The default path if not set in Params (fallback path)
	* @param  bool     $createIfNotSet    The switch to create the folder if not found
	*
	* @return  string    On success the path or url is returned based on the type requested
	* 
	*/
	public static function getFolderPath($type = 'path', $target = 'folderpath', $default = '', $createIfNotSet = true)
	{
		// make sure to always have a string/path
		if(!self::checkString($default))
		{
			$default = JPATH_SITE . '/images/';
		}
		// get the global settings
		if (!self::checkObject(self::$params))
		{
			self::$params = JComponentHelper::getParams('com_componentbuilder');
		}
		$folderPath = self::$params->get($target, $default);
		jimport('joomla.filesystem.folder');
		// create the folder if it does not exist
		if ($createIfNotSet && !JFolder::exists($folderPath))
		{
			JFolder::create($folderPath);
		}
		// return the url
		if ('url' === $type)
		{
			if (strpos($folderPath, JPATH_SITE) !== false)
			{
				$folderPath = trim( str_replace( JPATH_SITE, '', $folderPath), '/');
				return JURI::root() . $folderPath . '/';
			}
			// since the path is behind the root folder of the site, return only the root url (may be used to build the link)
			return JURI::root();
		}
		// sanitize the path
		return '/' . trim( $folderPath, '/' ) . '/';
	}


	/**
	* get the content of a file
	*
	* @param  string        $path   The path to the file
	* @param  string/bool   $none   The return value if no content was found
	*
	* @return  string   On success
	*
	*/
	public static function getFileContents($path, $none = '')
	{
		if (self::checkString($path))
		{
			// use basic file get content for now
			if (($content = @file_get_contents($path)) !== FALSE)
			{
				return $content;
			}
			// use curl if available
			elseif (function_exists('curl_version'))
			{
				// start curl
				$ch = curl_init();
				// set the options
				$options = array();
				$options[CURLOPT_URL] = $path;
				$options[CURLOPT_USERAGENT] = 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.2.12) Gecko/20101026 Firefox/3.6.12';
				$options[CURLOPT_RETURNTRANSFER] = TRUE;
				$options[CURLOPT_SSL_VERIFYPEER] = FALSE;
				// load the options
				curl_setopt_array($ch, $options);
				// get the content
				$content = curl_exec($ch);
				// close the connection
				curl_close($ch);
				// return if found
				if (self::checkString($content))
				{
					return $content;
				}
			}
			elseif (property_exists('ComponentbuilderHelper', 'curlErrorLoaded') && !self::$curlErrorLoaded)
			{
				// set the notice
				JFactory::getApplication()->enqueueMessage(JText::_('COM_COMPONENTBUILDER_HTWOCURL_NOT_FOUNDHTWOPPLEASE_SETUP_CURL_ON_YOUR_SYSTEM_OR_BCOMPONENTBUILDERB_WILL_NOT_FUNCTION_CORRECTLYP'), 'Error');
				// load this notice only once
				self::$curlErrorLoaded = true;
			}
		}
		return $none;
	}


	/**
	* 	 Composer Switch
	**/
	protected static $composer = false; 

	/**
	* 	Load the Composer Vendors
	**/
	public static function composerAutoload()
	{
		// insure we load the composer vendors only once
		if (!self::$composer)
		{
			// load the autoloader
			require_once JPATH_SITE.'/libraries/vdm_io/vendor/autoload.php';
			// do not load again
			self::$composer = true;
		}
	}

	/**
	 * bc math wrapper (very basic not for accounting)
	 *
	 * @param   string   $type    The type bc math
	 * @param   int      $val1    The first value
	 * @param   int      $val2    The second value
	 * @param   int      $scale   The scale value
	 *
	 * @return int
	 *
	 */
	public static function bcmath($type, $val1, $val2, $scale = 0)
	{
		// build function name
		$function = 'bc' . $type;
		// use the bcmath function if available
		if (function_exists($function))
		{
			return $function($val1, $val2, $scale);
		}
		// if function does not exist we use +-*/ operators (fallback - not ideal)
		switch ($type)
		{
			// Multiply two numbers
			case 'mul':
				return (string) round($val1 * $val2, $scale);
				break;
			// Divide of two numbers
			case 'div':
				return (string) round($val1 / $val2, $scale);
				break;
			// Adding two numbers
			case 'add':
				return (string) round($val1 + $val2, $scale);
				break;
			// Subtract one number from the other
			case 'sub':
				return (string) round($val1 - $val2, $scale);
				break;
			// Raise an arbitrary precision number to another
			case 'pow':
				return (string) round(pow($val1, $val2), $scale);
				break;
			// Compare two arbitrary precision numbers
			case 'comp':
				return (round($val1,2) == round($val2,2));
				break;
		}
		return false;
	}

	/**
	 * Basic sum of an array with more precision
	 *
	 * @param   array   $array    The values to sum
	 * @param   int      $scale   The scale value
	 *
	 * @return float
	 *
	 */
	public static function bcsum($array, $scale = 4)
	{
		// use the bcadd function if available
		if (function_exists('bcadd'))
		{
			// set the start value
			$value = 0.0;
			// loop the values and run bcadd
			foreach($array as $val)
			{
				$value = bcadd($value, $val, $scale);
			}
			return $value;
		}
		// fall back on array sum
		return array_sum($array);
	}

	/**
	* the locker
	*
	* @var array 
	**/
	protected static $locker = array();

	/**
	* the dynamic replacement salt
	*
	* @var array 
	**/
	protected static $globalSalt = array();

	/**
	* the timer
	*
	* @var object
	**/
	protected static $keytimer;

	/**
	* To Lock string
	*
	* @param string   $string     The string/array to lock
	* @param string   $key        The custom key to use
	* @param int      $salt       The switch to add salt and type of salt
	* @param int      $dynamic    The dynamic replacement array of salt build string
	* @param int      $urlencode  The switch to control url encoding
	*
	* @return string    Encrypted String
	*
	**/
	public static function lock($string, $key = null, $salt = 2, $dynamic = null, $urlencode = true)
	{
		// get the global settings
		if (!$key || !self::checkString($key))
		{
			// set temp timer
			$timer = 2;
			// if we have a timer use it
			if ($salt > 0)
			{
				$timer = $salt;
			}
			// set the default key
			$key = self::salt($timer, $dynamic);
			// try getting the system key
			if (method_exists(get_called_class(), "getCryptKey")) 
			{
				// try getting the medium key first the fall back to basic, and then default
				$key = self::getCryptKey('medium', self::getCryptKey('basic', $key));
			}
		}
		// check if we have a salt timer
		if ($salt > 0)
		{
			$key .= self::salt($salt, $dynamic);
		}
		// get the locker settings
		if (!isset(self::$locker[$key]) || !self::checkObject(self::$locker[$key]))
		{
			self::$locker[$key] = new FOFEncryptAes($key, 128);
		}
		// convert array or object to string
		if (self::checkArray($string) || self::checkObject($string))
		{
			$string = serialize($string);
		}
		// prep for url
		if ($urlencode && method_exists(get_called_class(), "base64_urlencode"))
		{
			return self::base64_urlencode(self::$locker[$key]->encryptString($string));
		}
		return self::$locker[$key]->encryptString($string);
	}

	/**
	* To un-Lock string
	*
	* @param string  $string       The string to unlock
	* @param string  $key          The custom key to use
	* @param int      $salt           The switch to add salt and type of salt
	* @param int      $dynamic    The dynamic replacement array of salt build string
	* @param int      $urlencode  The switch to control url decoding
	*
	* @return string    Decrypted String
	*
	**/
	public static function unlock($string, $key = null, $salt = 2, $dynamic = null, $urlencode = true)
	{
		// get the global settings
		if (!$key || !self::checkString($key))
		{
			// set temp timer
			$timer = 2;
			// if we have a timer use it
			if ($salt > 0)
			{
				$timer = $salt;
			}
			// set the default key
			$key = self::salt($timer, $dynamic);
			// try getting the system key
			if (method_exists(get_called_class(), "getCryptKey")) 
			{
				// try getting the medium key first the fall back to basic, and then default
				$key = self::getCryptKey('medium', self::getCryptKey('basic', $key));
			}
		}
		// check if we have a salt timer
		if ($salt > 0)
		{
			$key .= self::salt($salt, $dynamic);
		}
		// get the locker settings
		if (!isset(self::$locker[$key]) || !self::checkObject(self::$locker[$key]))
		{
			self::$locker[$key] = new FOFEncryptAes($key, 128);
		}
		// make sure we have real base64
		if ($urlencode && method_exists(get_called_class(), "base64_urldecode"))
		{
			$string = self::base64_urldecode($string);
		}
		// basic decrypt string.
		if (!empty($string) && !is_numeric($string) && $string === base64_encode(base64_decode($string, true)))
		{
			$string = rtrim(self::$locker[$key]->decryptString($string), "\0");
			// convert serial string to array
			if (self::is_serial($string))
			{
				$string = unserialize($string);
			}
		}
		return $string;
	}

	/**
	* The Salt
	*
	* @param int   $type      The type of length the salt should be valid
	* @param int   $dynamic   The dynamic replacement array of salt build string
	*
	* @return string
	*
	**/
	public static function salt($type = 1, $dynamic = null)
	{
		// get dynamic replacement salt
		$dynamic = self::getDynamicSalt($dynamic);
		// get the key timer
		if (!self::checkObject(self::$keytimer))
		{
			// load the date time object
			self::$keytimer = new DateTime;
			// set the correct time stamp
			$vdmLocalTime = new DateTimeZone('Africa/Windhoek');
			self::$keytimer->setTimezone($vdmLocalTime);
		}
		// set type
		if ($type == 2)
		{
			// hour
			$format = 'Y-m-d \o\n ' . self::periodFix(self::$keytimer->format('H'));
		}
		elseif ($type == 3)
		{
			// day
			$format = 'Y-m-' . self::periodFix(self::$keytimer->format('d'));
		}
		elseif ($type == 4)
		{
			// month
			$format = 'Y-' . self::periodFix(self::$keytimer->format('m'));
		}
		else
		{
			// minute
			$format = 'Y-m-d \o\n H:' . self::periodFix(self::$keytimer->format('i'));
		}
		// get key
		if (self::checkArray($dynamic))
		{
			return md5(str_replace(array_keys($dynamic), array_values($dynamic), self::$keytimer->format($format) . ' @ VDM.I0'));
		}
		return md5(self::$keytimer->format($format) . ' @ VDM.I0');
	}

	/**
	* The function to insure the salt is valid within the given period (third try)
	*
	* @param int $main    The main number
	*/
	protected static function periodFix($main)
	{
		return round($main / 3) * 3;
	}

	/**
	* Check if a string is serialized
	*
	* @param  string   $string
	*
	* @return Boolean
	*
	*/
	public static function is_serial($string)
	{
		return (@unserialize($string) !== false);
	}

	/**
	* Get dynamic replacement salt
	*/
	public static function getDynamicSalt($dynamic = null)
	{
		// load global if not manually set
		if (!self::checkArray($dynamic))
		{
			return self::getGlobalSalt();
		}
		// return manual values if set
		else
		{
			return $dynamic;
		}
	}

	/**
	* The random or dynamic secret salt
	*/
	public static function getSecretSalt($string = null, $size = 9)
	{
		// set the string
		if (!$string)
		{
			// get random string 
			$string = self::randomkey($size);
		}
		// convert string to array
		$string = self::safeString($string);
		// convert string to array
		$array = str_split($string);
		// insure only unique values are used
		$array = array_unique($array);
		// set the size
		$size = ($size <= count($array)) ? $size : count($array);
		// down size the 
		return array_slice($array, 0, $size);
	}

	/**
	* Get global replacement salt
	*/
	public static function getGlobalSalt()
	{
		// load from memory if found
		if (!self::checkArray(self::$globalSalt))
		{
			// get the global settings
			if (!self::checkObject(self::$params))
			{
				self::$params = JComponentHelper::getParams('com_componentbuilder');
			}
			// check if we have a global dynamic replacement array available (format -->  ' 1->!,3->E,4->A')
			$tmp = self::$params->get('dynamic_salt', null);
			if (self::checkString($tmp) && strpos($tmp, ',') !== false && strpos($tmp, '->') !== false)
			{
				$salt = array_map('trim', (array) explode(',', $tmp));
				if (self::checkArray($salt ))
				{
					foreach($salt as $replace)
					{
						$dynamic = array_map('trim', (array) explode('->', $replace));
						if (isset($dynamic[0]) && isset($dynamic[1]))
						{
							self::$globalSalt[$dynamic[0]] = $dynamic[1];
						}
					}
				}
			}
		}
		// return global if found
		if (self::checkArray(self::$globalSalt))
		{
			return self::$globalSalt;
		}
		// return default as fail safe
		return array('1' => '!', '3' => 'E', '4' => 'A');	
	}

	/**
	* Close public protocol
	*/
	public static function closePublicProtocol($id, $public)
	{
		// get secret salt
		$secretSalt = self::getSecretSalt(self::salt(1,array('4' => 'R','1' => 'E','2' => 'G','7' => 'J','8' => 'A')));
		// get the key
		$key = self::salt(1, $secretSalt);
		// get secret salt
		$secret = self::getSecretSalt();
		// set the secret
		$close['SECRET'] = self::lock($secret, $key, 1, array('1' => 's', '3' => 'R', '4' => 'D'));
		// get the key
		$key = self::salt(1, $secret);
		// get the public key
		$close['PUBLIC'] = self::lock($public, $key, 1, array('1' => '!', '3' => 'E', '4' => 'A'));
		// get secret salt
		$secretSalt = self::getSecretSalt($public);
		// get the key
		$key = self::salt(1, $secretSalt);
		// get the ID
		$close['ID'] = self::unlock($id, $key, 1, array('1' => 'i', '3' => 'e', '4' => 'B'));
		// return closed values
		return $close;
	}

	/**
	* Open public protocol
	*/
	public static function openPublicProtocol($SECRET, $ID, $PUBLIC)
	{
		// get secret salt
		$secretSalt = self::getSecretSalt(self::salt(1,array('4' => 'R','1' => 'E','2' => 'G','7' => 'J','8' => 'A')));
		// get the key
		$key = self::salt(1, $secretSalt);
		// get the $SECRET
		$SECRET = self::unlock($SECRET, $key, 1, array('1' => 's', '3' => 'R', '4' => 'D'));
		// get the key
		$key = self::salt(1, $SECRET);
		// get the public key
		$open['public'] = self::unlock($PUBLIC, $key, 1, array('1' => '!', '3' => 'E', '4' => 'A'));
		// get secret salt
		$secretSalt = self::getSecretSalt($open['public']);
		// get the key
		$key = self::salt(1, $secretSalt);
		// get the ID
		$open['id'] = self::unlock($ID, $key, 1, array('1' => 'i', '3' => 'e', '4' => 'B'));
		// return opened values
		return $open;
	}

	/**
	* 	Workers to load tasks
	*
	*	@var array 
	*/
	protected static $worker = array();

	/**
	*	Set a worker dynamic URLs
	*
	* 	@var array 
	*/
	protected static $workerURL = array();	

	/**
	*	Set a worker dynamic HEADERs
	*
	* 	@var array 
	*/
	protected static $workerHEADER = array();

	/**
	* 	Curl Error Notice
	*
	*	@var bool 
	*/
	protected static $curlErrorLoaded = false;

	/**
	* 	check if a worker has more work
	* 
	*	@param  string   $function    The function to target to perform the task
	*
	* 	@return  bool
	* 
	*/
	public static function hasWork(&$function)
	{
		if (isset(self::$worker[$function]) && self::checkArray(self::$worker[$function]))
		{
			return count( (array) self::$worker[$function]);
		}
		return false;
	}

	/**
	* 	Set a worker url
	* 
	*	@param  string   $function    The function to target to perform the task
	*	@param  string   $url            The url of where the task is to be performed
	*
	* 	@return  void
	* 
	*/
	public static function setWorkerUrl(&$function, &$url)
	{
		// set the URL if found
		if (self::checkString($url))
		{
			// make sure task function url is up
			self::$workerURL[$function] = $url;
		}
	}

	/**
	* 	Set a worker headers
	* 
	*	@param  string   $function    The function to target to perform the task
	*	@param  array    $headers    The headers needed for these workers/function
	*
	* 	@return  void
	* 
	*/
	public static function setWorkerHeaders(&$function, &$headers)
	{
		// set the Headers if found
		if (self::checkArray($headers))
		{
			// make sure task function headers are set
			self::$workerHEADER[$function] = $headers;
		}
	}

	/**
	* 	Set a worker that needs to perform a task
	* 
	*	@param  mixed   $data         The data to pass to the task
	*	@param  string   $function    The function to target to perform the task
	*	@param  string   $url            The url of where the task is to be performed
	*	@param  array    $headers    The headers needed for these workers/function
	*
	* 	@return  void
	* 
	*/
	public static function setWorker($data, $function, $url = null, $headers = null)
	{
		// make sure task function is up
		if (!isset(self::$worker[$function]))
		{
			self::$worker[$function] = array();
		}
		// load the task
		self::$worker[$function][] = self::lock($data);
		// set the Headers if found
		if ($headers && !isset(self::$workerHEADER[$function]))
		{
			self::setWorkerHeaders($function, $headers);
		}
		// set the URL if found
		if ($url && !isset(self::$workerURL[$function]))
		{
			self::setWorkerUrl($function, $url);
		}
	}

	/**
	*	Run set Workers
	*
	*	@param  string      $function    The function to target to perform the task
	*	@param  string      $perTask    The amount of task per worker
	* 	@param  function   $callback   The option to do a call back when task is completed
	*	@param  int           $threadSize   The size of the thread
	*
	*	@return  bool true   On success
	*
	*/
	public static function runWorker($function, $perTask = 50, $callback = null, $threadSize = 20)
	{
		// set task
		$task = self::lock($function);
		// build headers
		$headers = array('VDM-TASK: ' .$task);
		// build dynamic headers
		if (isset(self::$workerHEADER[$function]) && self::checkArray(self::$workerHEADER[$function]))
		{
			foreach (self::$workerHEADER[$function] as $header)
			{
				$headers[] = $header;
			}
		}
		// build worker options
		$options = array();
		// make sure worker is up
		if (isset(self::$worker[$function]) && self::checkArray(self::$worker[$function]))
		{
			// this load method is for each
			if (1 == $perTask)
			{
				// working with a string = 1
				$headers[] = 'VDM-VALUE-TYPE: ' .self::lock(1);
				// now load the options
				foreach (self::$worker[$function] as $data)
				{
					$options[] = array(CURLOPT_HTTPHEADER => $headers, CURLOPT_POST => 1,  CURLOPT_POSTFIELDS => 'VDM_DATA='. $data);
				}
			}
			// this load method is for bundles 
			else
			{
				// working with an array = 2
				$headers[] = 'VDM-VALUE-TYPE: ' .self::lock(2);
				// now load the options
				$work = array_chunk(self::$worker[$function], $perTask);
				foreach ($work as $data)
				{
					$options[] = array(CURLOPT_HTTPHEADER => $headers, CURLOPT_POST => 1,  CURLOPT_POSTFIELDS => 'VDM_DATA='. implode('___VDM___', $data));
				}
			}
			// relieve worker of task/function
			self::$worker[$function] = array();
		}
		// do the execution
		if (self::checkArray($options))
		{
			if (isset(self::$workerURL[$function]))
			{
				$url = self::$workerURL[$function];
			}
			else
			{
				$url = JURI::root() . '/index.php?option=com_componentbuilder&task=api.worker';
			}
			return self::curlMultiExec($url, $options, $callback, $threadSize);
		}
		return false;
	}

	/**
	*	Do a multi curl execution of tasks
	*
	* 	@param  string      $url               The url of where the task is to be performed
	*  	@param  array       $_options      The array of curl options/headers to set
	*	@param  function   $callback      The option to do a call back when task is completed
	*	@param  int           $threadSize   The size of the thread
	*
	* 	@return  bool true   On success
	*
	*/
	public static function curlMultiExec(&$url, &$_options, $callback = null, $threadSize = 20)
	{
		// make sure we have curl available
		if (!function_exists('curl_version'))
		{
			if (!self::$curlErrorLoaded)
			{
				// set the notice
				JFactory::getApplication()->enqueueMessage(JText::_('COM_COMPONENTBUILDER_HTWOCURL_NOT_FOUNDHTWOPPLEASE_SETUP_CURL_ON_YOUR_SYSTEM_OR_BCOMPONENTBUILDERB_WILL_NOT_FUNCTION_CORRECTLYP'), 'Error');
				// load the notice only once
				self::$curlErrorLoaded = true;
			}
			return false;
		}
		// make sure we have an url
		if (self::checkString($url))
		{
			// make sure the thread size isn't greater than the # of _options
			$threadSize = (count($_options) < $threadSize) ? count($_options) : $threadSize;
			// set the options
			$options = array();
			$options[CURLOPT_URL] = $url;
			$options[CURLOPT_USERAGENT] = 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.2.12) Gecko/20101026 Firefox/3.6.12';
			$options[CURLOPT_RETURNTRANSFER] = TRUE;
			$options[CURLOPT_SSL_VERIFYPEER] = FALSE;
			// start multi threading :)
			$handle = curl_multi_init();
			// start the first batch of requests
			for ($i = 0; $i < $threadSize; $i++)
			{
				if (isset($_options[$i]))
				{
					$ch = curl_init();
					foreach ($_options[$i] as $curlopt => $string)
					{
						$options[$curlopt] = $string;
					}
					curl_setopt_array($ch, $options);
					curl_multi_add_handle($handle, $ch);
				}
			}
			// we wait for all the calls to finish (should not take long)
			do {
				while(($execrun = curl_multi_exec($handle, $working)) == CURLM_CALL_MULTI_PERFORM);
					if($execrun != CURLM_OK)
						break;
				// a request was just completed -- find out which one
				while($done = curl_multi_info_read($handle))
				{
					if (is_callable($callback))
					{
						// $info = curl_getinfo($done['handle']);
						// request successful. process output using the callback function.
						$output = curl_multi_getcontent($done['handle']);
						$callback($output);
					}
					$key = $i + 1;
					if(isset($_options[$key]))
					{
						// start a new request (it's important to do this before removing the old one)
						$ch = curl_init(); $i++;
						// add options
						foreach ($_options[$key] as $curlopt => $string)
						{
							$options[$curlopt] = $string;
						}
						curl_setopt_array($ch, $options);
						curl_multi_add_handle($handle, $ch);
						// remove options again
						foreach ($_options[$key] as $curlopt => $string)
						{
							unset($options[$curlopt]);
						}
					}
					// remove the curl handle that just completed
					curl_multi_remove_handle($handle, $done['handle']);
				}
				// stop wasting CPU cycles and rest for a couple ms
				usleep(10000);
			} while ($working);
			// close the curl multi thread
			curl_multi_close($handle);
			// okay done
			return true;
		}
		return false;
	}

	/**
	* Move File to Server
	*
	* @param   string    $localPath     The local path to the file
	* @param   string    $fileName      The the actual file name
	* @param   int         $serverID       The server local id to use
	* @param   int         $protocol        The server protocol to use
	* @param   string    $permission    The permission validation area
	*
	* @return  bool      true on success
	**/
	public static function moveToServer($localPath, $fileName, $serverID, $protocol = null, $permission = 'core.export')
	{
		// get the server
		if ($server = self::getServer( (int) $serverID, $protocol, $permission))
		{
			// use the FTP protocol
			if (1 == $server->jcb_protocol)
			{
				// now move the file
				if (!$server->store($localPath, $fileName))
				{
					JFactory::getApplication()->enqueueMessage(JText::sprintf('COM_COMPONENTBUILDER_THE_BSB_FILE_COULD_NOT_BE_MOVED_TO_BSB_SERVER', $fileName, $server->jcb_remote_server_name[(int) $serverID]), 'Error');
					return false;
				}
				// close the connection
				$server->quit();
			}
			// use the SFTP protocol
			elseif (2 == $server->jcb_protocol)
			{
				// now move the file
				if (!$server->put($server->jcb_remote_server_path[(int) $serverID] . $fileName, self::getFileContents($localPath, null)))
				{
					JFactory::getApplication()->enqueueMessage(JText::sprintf('COM_COMPONENTBUILDER_THE_BSB_FILE_COULD_NOT_BE_MOVED_TO_BSB_PATH_ON_BSB_SERVER', $fileName, $server->jcb_remote_server_path[(int) $serverID], $server->jcb_remote_server_name[(int) $serverID]), 'Error');
					return false;
				}
			}
			return true;
		}
		return false;
	}

	/**
	* the SFTP objects
	**/
	protected static $sftp = array();

	/**
	* the FTP objects
	**/
	protected static $ftp = array();

	/**
	* get the server object
	*
	* @param   int         $serverID       The server local id to use
	* @param   int         $protocol        The server protocol to use
	* @param   string    $permission    The permission validation area
	*
	* @return  object     on success server object
	**/
	public static function getServer($serverID, $protocol = null, $permission = 'core.export')
	{
		// if not protocol is given get it (sad I know)
		if (!$protocol)
		{
			$protocol = self::getVar('server', (int) $serverID, 'id', 'protocol');
		}
		// return the server object
		switch ($protocol)
		{
			case 1: // FTP
				return self::getFtp($serverID, $permission);
			break;
			case 2: // SFTP
				return self::getSftp($serverID, $permission);
			break;
		}
		return false;
	}

	/**
	* get the sftp object
	*
	* @param   int         $serverID       The server local id to use
	* @param   string    $permission    The permission validation area
	*
	* @return  object on success with sftp power
	**/
	public static function getSftp($serverID, $permission = 'core.export')
	{
		// check if we have a server with that id
		if ($server = self::getServerDetails($serverID, 2, $permission))
		{
			// check if it was already set
			if (!isset(self::$sftp[$server->cache]) || !self::checkObject(self::$sftp[$server->cache]))
			{
				// make sure we have the composer classes loaded
				self::composerAutoload();
				// make sure we have the phpseclib classes
				if (!class_exists('\phpseclib\Net\SFTP'))
				{
					// class not in place so send out error
					JFactory::getApplication()->enqueueMessage(JText::_('COM_COMPONENTBUILDER_THE_BPHPSECLIBNETSFTPB_LIBRARYCLASS_IS_NOT_AVAILABLE_THIS_LIBRARYCLASS_SHOULD_HAVE_BEEN_ADDED_TO_YOUR_BLIBRARIESVDM_IOVENDORB_FOLDER_PLEASE_CONTACT_YOUR_SYSTEM_ADMINISTRATOR_FOR_MORE_INFO'), 'Error');
					return false;
				}
				// insure the port is set
				$server->port = (isset($server->port) && is_int($server->port) && $server->port > 0) ? $server->port : 22;
				// open the connection
				self::$sftp[$server->cache] = new phpseclib\Net\SFTP($server->host, $server->port);
				// heads-up on protocol
				self::$sftp[$server->cache]->jcb_protocol = 2; // SFTP <-- if called not knowing what type of protocol is being used
				// now login based on authentication type
				switch($server->authentication)
				{
					case 1: // password
						if (!self::$sftp[$server->cache]->login($server->username, $server->password))
						{
							JFactory::getApplication()->enqueueMessage(JText::sprintf('COM_COMPONENTBUILDER_THE_LOGIN_TO_BSB_HAS_FAILED_PLEASE_CHECK_THAT_YOUR_DETAILS_ARE_CORRECT', $server->name), 'Error');
							unset(self::$sftp[$server->cache]);
							return false;
						}
					break;
					case 2: // private key file
						if (self::checkObject(self::crypt('RSA')))
						{
							// check if we have a passprase
							if (self::checkString($server->secret))
							{
								self::crypt('RSA')->setPassword($server->secret);
							}
							// now load the key file
							if (!self::crypt('RSA')->loadKey(self::getFileContents($server->private, null)))
							{
								JFactory::getApplication()->enqueueMessage(JText::sprintf('COM_COMPONENTBUILDER_THE_PRIVATE_KEY_FILE_COULD_NOT_BE_LOADEDFOUND_FOR_BSB_SERVER', $server->name), 'Error');
								unset(self::$sftp[$server->cache]);
								return false;
							}
							// now login
							if (!self::$sftp[$server->cache]->login($server->username, self::crypt('RSA')))
							{
								JFactory::getApplication()->enqueueMessage(JText::sprintf('COM_COMPONENTBUILDER_THE_LOGIN_TO_BSB_HAS_FAILED_PLEASE_CHECK_THAT_YOUR_DETAILS_ARE_CORRECT', $server->name), 'Error');
								unset(self::$sftp[$server->cache]);
								return false;
							}
						}
					break;
					case 3: // both password and private key file
						if (self::checkObject(self::crypt('RSA')))
						{
							// check if we have a passphrase
							if (self::checkString($server->secret))
							{
								self::crypt('RSA')->setPassword($server->secret);
							}
							// now load the key file
							if (!self::crypt('RSA')->loadKey(self::getFileContents($server->private, null)))
							{
								JFactory::getApplication()->enqueueMessage(JText::sprintf('COM_COMPONENTBUILDER_THE_PRIVATE_KEY_FILE_COULD_NOT_BE_LOADEDFOUND_FOR_BSB_SERVER', $server->name), 'Error');
								unset(self::$sftp[$server->cache]);
								return false;
							}
							// now login
							if (!self::$sftp[$server->cache]->login($server->username, $server->password, self::crypt('RSA')))
							{
								JFactory::getApplication()->enqueueMessage(JText::sprintf('COM_COMPONENTBUILDER_THE_LOGIN_TO_BSB_HAS_FAILED_PLEASE_CHECK_THAT_YOUR_DETAILS_ARE_CORRECT', $server->name), 'Error');
								unset(self::$sftp[$server->cache]);
								return false;
							}
						}
					break;
					case 4: // private key field
						if (self::checkObject(self::crypt('RSA')))
						{
							// check if we have a passprase
							if (self::checkString($server->secret))
							{
								self::crypt('RSA')->setPassword($server->secret);
							}
							// now load the key field
							if (!self::crypt('RSA')->loadKey($server->private_key))
							{
								JFactory::getApplication()->enqueueMessage(JText::sprintf('COM_COMPONENTBUILDER_THE_PRIVATE_KEY_FIELD_COULD_NOT_BE_LOADED_FOR_BSB_SERVER', $server->name), 'Error');
								unset(self::$sftp[$server->cache]);
								return false;
							}
							// now login
							if (!self::$sftp[$server->cache]->login($server->username, self::crypt('RSA')))
							{
								JFactory::getApplication()->enqueueMessage(JText::sprintf('COM_COMPONENTBUILDER_THE_LOGIN_TO_BSB_HAS_FAILED_PLEASE_CHECK_THAT_YOUR_DETAILS_ARE_CORRECT', $server->name), 'Error');
								unset(self::$sftp[$server->cache]);
								return false;
							}
						}
					break;
					case 5: // both password and private key field
						if (self::checkObject(self::crypt('RSA')))
						{
							// check if we have a passphrase
							if (self::checkString($server->secret))
							{
								self::crypt('RSA')->setPassword($server->secret);
							}
							// now load the key file
							if (!self::crypt('RSA')->loadKey($server->private_key))
							{
								JFactory::getApplication()->enqueueMessage(JText::sprintf('COM_COMPONENTBUILDER_THE_PRIVATE_KEY_FIELD_COULD_NOT_BE_LOADED_FOR_BSB_SERVER', $server->name), 'Error');
								unset(self::$sftp[$server->cache]);
								return false;
							}
							// now login
							if (!self::$sftp[$server->cache]->login($server->username, $server->password, self::crypt('RSA')))
							{
								JFactory::getApplication()->enqueueMessage(JText::sprintf('COM_COMPONENTBUILDER_THE_LOGIN_TO_BSB_HAS_FAILED_PLEASE_CHECK_THAT_YOUR_DETAILS_ARE_CORRECT', $server->name), 'Error');
								unset(self::$sftp[$server->cache]);
								return false;
							}
						}
					break;
				}
			}
			// only continue if object is set
			if (isset(self::$sftp[$server->cache]) && self::checkObject(self::$sftp[$server->cache]))
			{
				// set the unique buckets
				if (!isset(self::$sftp[$server->cache]->jcb_remote_server_name))
				{
					self::$sftp[$server->cache]->jcb_remote_server_name = array();
					self::$sftp[$server->cache]->jcb_remote_server_path = array();
				}
				// always set the name and remote server path
				self::$sftp[$server->cache]->jcb_remote_server_name[$serverID] = $server->name;
				self::$sftp[$server->cache]->jcb_remote_server_path[$serverID] = (self::checkString($server->path) && $server->path !== '/') ? $server->path : '';
				// return the sftp object
				return self::$sftp[$server->cache];
			}
		}
		return false;
	}

	/**
	* get the JClientFtp object
	*
	* @param   int        $serverID         The server local id to use
	* @param   string    $permission    The permission validation area
	*
	* @return  object on success with ftp power
	**/
	public static function getFtp($serverID, $permission)
	{
		// check if we have a server with that id
		if ($server = self::getServerDetails($serverID, 1, $permission))
		{
			// check if we already have the server instance
			if (isset(self::$ftp[$server->cache]) && self::$ftp[$server->cache] instanceof JClientFtp)
			{
				// always set the name and remote server path
				self::$ftp[$server->cache]->jcb_remote_server_name[$serverID] = $server->name;
				// if still connected we are ready to go
				if (self::$ftp[$server->cache]->isConnected())
				{
					// return the FTP instance
					return self::$ftp[$server->cache];
				}
				// check if we can reinitialise the server
				if (self::$ftp[$server->cache]->reinit())
				{
					// return the FTP instance
					return self::$ftp[$server->cache];
				}
			}
			// make sure we have a string and it is not default or empty
			if (self::checkString($server->signature))
			{
				// turn into variables
				parse_str($server->signature); // because of this I am using strange variable naming to avoid any collisions.
				// set options
				if (isset($options) && self::checkArray($options))
				{
					foreach ($options as $o__p0t1on => $vAln3)
					{
						if ('timeout' === $o__p0t1on)
						{
							$options[$o__p0t1on] = (int) $vAln3;
						}
						if ('type' === $o__p0t1on)
						{
							$options[$o__p0t1on] = (string) $vAln3;
						}
					}
				}
				else
				{
					$options = array();
				}
				// get ftp object
				if (isset($host) && $host != 'HOSTNAME' && isset($port) && $port != 'PORT_INT' && isset($username) && $username != 'user@name.com' && isset($password) && $password != 'password')
				{
					// load for reuse
					self::$ftp[$server->cache] = JClientFtp::getInstance($host, $port, $options, $username, $password);
				}
				else
				{
					// load error to indicate signature was in error
					JFactory::getApplication()->enqueueMessage(JText::sprintf('COM_COMPONENTBUILDER_THE_FTP_SIGNATURE_FOR_BSB_WAS_NOT_WELL_FORMED_PLEASE_CHECK_YOUR_SIGNATURE_DETAILS', $server->name), 'Error');
					return false;
				}
				// check if we are connected
				if (self::$ftp[$server->cache] instanceof JClientFtp && self::$ftp[$server->cache]->isConnected())
				{
					// heads-up on protocol
					self::$ftp[$server->cache]->jcb_protocol = 1; // FTP <-- if called not knowing what type of protocol is being used
					// set the unique buckets
					if (!isset(self::$ftp[$server->cache]->jcb_remote_server_name))
					{
						self::$ftp[$server->cache]->jcb_remote_server_name = array();
					}
					// always set the name and remote server path
					self::$ftp[$server->cache]->jcb_remote_server_name[$serverID] = $server->name;
					// return the FTP instance
					return self::$ftp[$server->cache];
				}
				// reset since we have no connection
				unset(self::$ftp[$server->cache]);
			}
			// load error to indicate signature was in error
			JFactory::getApplication()->enqueueMessage(JText::sprintf('COM_COMPONENTBUILDER_THE_FTP_CONNECTION_FOR_BSB_COULD_NOT_BE_MADE_PLEASE_CHECK_YOUR_SIGNATURE_DETAILS', $server->name), 'Error');
		}
		return false;
	}

	/**
	* get the server details
	*
	* @param   int         $serverID       The server local id to use
	* @param   int         $protocol        The server protocol to use
	* @param   string    $permission    The permission validation area
	*
	* @return  object    on success with server details
	**/
	public static function getServerDetails($serverID, $protocol = 2, $permission = 'core.export')
	{
		// check if this user has permission to access items
		if (!JFactory::getUser()->authorise($permission, 'com_componentbuilder'))
		{
			// set message to inform the user that permission was denied
			JFactory::getApplication()->enqueueMessage(JText::sprintf('COM_COMPONENTBUILDER_YOU_DO_NOT_HAVE_PERMISSION_TO_ACCESS_THE_SERVER_DETAILS_BS_DENIEDB_PLEASE_CONTACT_YOUR_SYSTEM_ADMINISTRATOR_FOR_MORE_INFO', self::safeString($permission, 'w')), 'Error');
			return false;
		}
		// now insure we have correct values 
		if (is_int($serverID) && is_int($protocol))
		{
			// Get a db connection
			$db = JFactory::getDbo();
			// start the query
			$query = $db->getQuery(true);
			// select based to protocol
			if (2 == $protocol)
			{
				// SFTP
				$query->select($db->quoteName(array('name','authentication','username','host','password','path','port','private','private_key','secret')));
				// cache builder
				$cache = array('authentication','username','host','password','port','private','private_key','secret');
			}
			else
			{
				// FTP
				$query->select($db->quoteName(array('name','signature')));
				// cache builder
				$cache = array('signature');
			}
			$query->from($db->quoteName('#__componentbuilder_server'));
			$query->where($db->quoteName('id') . ' = ' . (int) $serverID);
			$query->where($db->quoteName('protocol') . ' = ' . (int) $protocol);
			$db->setQuery($query);
			$db->execute();
			if ($db->getNumRows())
			{
				$server = $db->loadObject();
				// Get the basic encryption.
				$basickey = self::getCryptKey('basic', 'Th1sMnsTbL0ck@d');
				// Get the encryption object.
				$basic = new FOFEncryptAes($basickey, 128);
				// start cache keys
				$keys = array();
				// unlock the needed fields
				foreach($server as $name => &$value)
				{
					// unlock the needed fields
					if ($name !== 'name' && !empty($value) && $basickey && !is_numeric($value) && $value === base64_encode(base64_decode($value, true)))
					{
						// basic decrypt of data
						$value = rtrim($basic->decryptString($value), "\0");
					}
					// build cache (keys) for lower connection latency
					if (in_array($name, $cache))
					{
						$keys[] = $value;
					}
				}
				// check if cache keys were found
				if (self::checkArray($keys))
				{
					// now set cache
					$server->cache = md5(implode('', $keys));
				}
				else
				{
					// default is ID
					$server->cache = $serverID;
				}
				// return the server details
				return $server;
			}
		}
		JFactory::getApplication()->enqueueMessage(JText::sprintf('COM_COMPONENTBUILDER_THE_SERVER_DETAILS_FOR_BID_SB_COULD_NOT_BE_RETRIEVED', $serverID), 'Error');
		return false;
	}

	/**
	* Get an edit button
	* 
	* @param  int      $item       The item to edit
	* @param  string   $view       The type of item to edit
	* @param  string   $views      The list view controller name
	* @param  string   $ref        The return path
	* @param  string   $component  The component these views belong to
	* @param  string   $headsup    The message to show on click of button
	*
	* @return  string    On success the full html link
	* 
	*/
	public static function getEditButton(&$item, $view, $views, $ref = '', $component = 'com_componentbuilder', $headsup = 'COM_COMPONENTBUILDER_ALL_UNSAVED_WORK_ON_THIS_PAGE_WILL_BE_LOST_ARE_YOU_SURE_YOU_WANT_TO_CONTINUE')
	{
		// get URL
		$url = self::getEditURL($item, $view, $views, $ref, $component);
		// check if we found any
		if (self::checkString($url))
		{
			// get the global settings
			if (!self::checkObject(self::$params))
			{
				self::$params = JComponentHelper::getParams('com_componentbuilder');
			}
			// get UIKIT version
			$uikit = self::$params->get('uikit_version', 2);
			// check that we have the ID
			if (self::checkObject($item) && isset($item->id))
			{
				// check if the checked_out is available
				if (isset($item->checked_out))
				{
					$checked_out = (int) $item->checked_out;
				}
				else
				{
					$checked_out = self::getVar($view, $item->id, 'id', 'checked_out', '=', str_replace('com_', '', $component));
				}
			}
			elseif (self::checkArray($item) && isset($item['id']))
			{
				// check if the checked_out is available
				if (isset($item['checked_out']))
				{
					$checked_out = (int) $item['checked_out'];
				}
				else
				{
					$checked_out = self::getVar($view, $item['id'], 'id', 'checked_out', '=', str_replace('com_', '', $component));
				}
			}
			elseif (is_numeric($item) && $item > 0)
			{
				$checked_out = self::getVar($view, $item, 'id', 'checked_out', '=', str_replace('com_', '', $component));
			}
			// set the link title
			$title = self::safeString(JText::_('COM_COMPONENTBUILDER_EDIT') . ' ' . $view, 'W');
			// check that there is a check message
			if (self::checkString($headsup))
			{
				if (3 == $uikit)
				{
					$href = 'onclick="UIkit.modal.confirm(\''.JText::_($headsup).'\').then( function(){ window.location.href = \'' . $url . '\' } )"  href="javascript:void(0)"';
				}
				else
				{
					$href = 'onclick="UIkit2.modal.confirm(\''.JText::_($headsup).'\', function(){ window.location.href = \'' . $url . '\' })"  href="javascript:void(0)"';
				}
			}
			else
			{
				$href = 'href="' . $url . '"';
			}
			// return UIKIT version 3
			if (3 == $uikit)
			{
				// check if it is checked out
				if (isset($checked_out) && $checked_out > 0)
				{
					// is this user the one who checked it out
					if ($checked_out == JFactory::getUser()->id)
					{
						return ' <a ' . $href . ' uk-icon="icon: lock" title="' . $title . '"></a>';
					}
					return ' <a href="#" disabled uk-icon="icon: lock" title="' . JText::sprintf('COM_COMPONENTBUILDER__HAS_BEEN_CHECKED_OUT_BY_S', self::safeString($view, 'W'), JFactory::getUser($checked_out)->name) . '"></a>'; 
				}
				// return normal edit link
				return ' <a ' . $href . ' uk-icon="icon: pencil" title="' . $title . '"></a>';
			}
			// check if it is checked out (return UIKIT version 2)
			if (isset($checked_out) && $checked_out > 0)
			{
				// is this user the one who checked it out
				if ($checked_out == JFactory::getUser()->id)
				{
					return ' <a ' . $href . ' class="uk-icon-lock" title="' . $title . '"></a>';
				}
				return ' <a href="#" disabled class="uk-icon-lock" title="' . JText::sprintf('COM_COMPONENTBUILDER__HAS_BEEN_CHECKED_OUT_BY_S', self::safeString($view, 'W'), JFactory::getUser($checked_out)->name) . '"></a>'; 
			}
			// return normal edit link
			return ' <a ' . $href . ' class="uk-icon-pencil" title="' . $title . '"></a>';
		}
		return '';
	}

	/**
	* Get an edit text button
	* 
	* @param  string   $text       The button text
	* @param  int      $item       The item to edit
	* @param  string   $view       The type of item to edit
	* @param  string   $views      The list view controller name
	* @param  string   $ref        The return path
	* @param  string   $component  The component these views belong to
	* @param  string   $headsup    The message to show on click of button
	*
	* @return  string    On success the full html link
	* 
	*/
	public static function getEditTextButton($text, &$item, $view, $views, $ref = '', $component = 'com_componentbuilder', $jRoute = true, $class = 'uk-button', $headsup = 'COM_COMPONENTBUILDER_ALL_UNSAVED_WORK_ON_THIS_PAGE_WILL_BE_LOST_ARE_YOU_SURE_YOU_WANT_TO_CONTINUE')
	{
		// make sure we have text
		if (!self::checkString($text))
		{
			return self::getEditButton($item, $view, $views, $ref, $component, $headsup);
		}
		// get URL
		$url = self::getEditURL($item, $view, $views, $ref, $component, $jRoute);
		// check if we found any
		if (self::checkString($url))
		{
			// get the global settings
			if (!self::checkObject(self::$params))
			{
				self::$params = JComponentHelper::getParams('com_componentbuilder');
			}
			// get UIKIT version
			$uikit = self::$params->get('uikit_version', 2);
			// check that we have the ID
			if (self::checkObject($item) && isset($item->id))
			{
				// check if the checked_out is available
				if (isset($item->checked_out))
				{
					$checked_out = (int) $item->checked_out;
				}
				else
				{
					$checked_out = self::getVar($view, $item->id, 'id', 'checked_out', '=', str_replace('com_', '', $component));
				}
			}
			elseif (self::checkArray($item) && isset($item['id']))
			{
				// check if the checked_out is available
				if (isset($item['checked_out']))
				{
					$checked_out = (int) $item['checked_out'];
				}
				else
				{
					$checked_out = self::getVar($view, $item['id'], 'id', 'checked_out', '=', str_replace('com_', '', $component));
				}
			}
			elseif (is_numeric($item) && $item > 0)
			{
				$checked_out = self::getVar($view, $item, 'id', 'checked_out', '=', str_replace('com_', '', $component));
			}
			// set the link title
			$title = self::safeString(JText::_('COM_COMPONENTBUILDER_EDIT') . ' ' . $view, 'W');
			// check that there is a check message
			if (self::checkString($headsup))
			{
				if (3 == $uikit)
				{
					$href = 'onclick="UIkit.modal.confirm(\''.JText::_($headsup).'\').then( function(){ window.location.href = \'' . $url . '\' } )"  href="javascript:void(0)"';
				}
				else
				{
					$href = 'onclick="UIkit2.modal.confirm(\''.JText::_($headsup).'\', function(){ window.location.href = \'' . $url . '\' })"  href="javascript:void(0)"';
				}
			}
			else
			{
				$href = 'href="' . $url . '"';
			}
			// return UIKIT version 3
			if (3 == $uikit)
			{
				// check if it is checked out
				if (isset($checked_out) && $checked_out > 0)
				{
					// is this user the one who checked it out
					if ($checked_out == JFactory::getUser()->id)
					{
						return ' <a class="' . $class . '" ' . $href . ' title="' . $title . '">' . $text . '</a>';
					}
					return ' <a class="' . $class . '" href="#" disabled title="' . JText::sprintf('COM_COMPONENTBUILDER__HAS_BEEN_CHECKED_OUT_BY_S', self::safeString($view, 'W'), JFactory::getUser($checked_out)->name) . '">' . $text . '</a>'; 
				}
				// return normal edit link
				return ' <a class="' . $class . '" ' . $href . ' title="' . $title . '">' . $text . '</a>';
			}
			// check if it is checked out (return UIKIT version 2)
			if (isset($checked_out) && $checked_out > 0)
			{
				// is this user the one who checked it out
				if ($checked_out == JFactory::getUser()->id)
				{
					return ' <a class="' . $class . '" ' . $href . ' title="' . $title . '">' . $text . '</a>';
				}
				return ' <a class="' . $class . '" href="#" disabled title="' . JText::sprintf('COM_COMPONENTBUILDER__HAS_BEEN_CHECKED_OUT_BY_S', self::safeString($view, 'W'), JFactory::getUser($checked_out)->name) . '">' . $text . '</a>'; 
			}
			// return normal edit link
			return ' <a class="' . $class . '" ' . $href . ' title="' . $title . '">' . $text . '</a>';
		}
		return '';
	}

	/**
	* Get the edit URL
	* 
	* @param  int      $item        The item to edit
	* @param  string   $view        The type of item to edit
	* @param  string   $views       The list view controller name
	* @param  string   $ref         The return path
	* @param  string   $component   The component these views belong to
	* @param  bool     $jRoute      The switch to add use JRoute or not
	*
	* @return  string    On success the edit url
	* 
	*/
	public static function getEditURL(&$item, $view, $views, $ref = '', $component = 'com_componentbuilder', $jRoute = true)
	{
		// make sure the user has access to view
		if (!JFactory::getUser()->authorise($view. '.access', $component))
		{
			return false;
		}
		// build record
		$record = new stdClass();
		// check that we have the ID
		if (self::checkObject($item) && isset($item->id))
		{
			$record->id = (int) $item->id;
			// check if created_by is available
			if (isset($item->created_by) && $item->created_by > 0)
			{
				$record->created_by = (int) $item->created_by;
			}
		}
		elseif (self::checkArray($item) && isset($item['id']))
		{
			$record->id = (int) $item['id'];
			// check if created_by is available
			if (isset($item['created_by']) && $item['created_by'] > 0)
			{
				$record->created_by = (int) $item['created_by'];
			}
		}
		elseif (is_numeric($item))
		{
			$record->id = (int) $item;
		}
		// check ID
		if (isset($record->id) && $record->id > 0)
		{
			// get user action permission to edit
			$action = self::getActions($view, $record, $views, 'edit', str_replace('com_', '', $component));
			// check if the view permission is set
			if (($edit = $action->get($view . '.edit', 'none-set')) === 'none-set')
			{
				// fall back on the core permission then
				$edit = $action->get('core.edit', 'none-set');
			}
			// can edit
			if ($edit)
			{
				// set the edit link
				if ($jRoute)
				{
					return JRoute::_("index.php?option=" . $component . "&view=" . $views . "&task=" . $view . ".edit&id=" . $record->id . $ref);
				}
				return "index.php?option=" . $component . "&view=" . $views . "&task=" . $view . ".edit&id=" . $record->id . $ref;
			}
		}
		return false;
	}


	/**
	* 	the Crypt objects
	**/
	protected static $CRYPT = array();

	/**
	* 	get the Crypt object
	* 	
	* 	@return  object on success with Crypt power
	**/
	public static function crypt($TYPE)
	{
		// check if it was already set
		if (isset(self::$CRYPT[$TYPE]) && self::checkObject(self::$CRYPT[$TYPE]))
		{
			return self::$CRYPT[$TYPE];
		}
		// make sure we have the composer classes loaded
		self::composerAutoload();
		// build class name
		$CLASS = '\phpseclib\Crypt\\'.$TYPE;
		// make sure we have the phpseclib classes
		if (!class_exists($CLASS))
		{
			// class not in place so send out error
			JFactory::getApplication()->enqueueMessage(JText::sprintf('COM_COMPONENTBUILDER_THE_BSB_LIBRARYCLASS_IS_NOT_AVAILABLE_THIS_LIBRARYCLASS_SHOULD_HAVE_BEEN_ADDED_TO_YOUR_BLIBRARIESVDM_IOVENDORB_FOLDER_PLEASE_CONTACT_YOUR_SYSTEM_ADMINISTRATOR_FOR_MORE_INFO', $CLASS), 'Error');
			return false;
		}
		// set the 
		self::$CRYPT[$TYPE] = new $CLASS();
		// return the object
		return self::$CRYPT[$TYPE];
	}


	/**
	 * set subform type table
	 *
	 * @param   array   $head    The header names
	 * @param   array   $rows    The row values
	 * @param   string  $idName  The prefix to the table id
	 *
	 * @return string
	 *
	 */
	public static function setSubformTable($head, $rows, $idName)
	{
		$table[] = "<div class=\"row-fluid\" id=\"vdm_table_display_".$idName."\">";
		$table[] = "\t<div class=\"subform-repeatable-wrapper subform-table-layout subform-table-sublayout-section-byfieldsets\">";
		$table[] = "\t\t<div class=\"subform-repeatable\">";
		$table[] = "\t\t\t<table class=\"adminlist table table-striped table-bordered\">";
		$table[] = "\t\t\t\t<thead>";
		$table[] = "\t\t\t\t\t<tr>";
		$table[] = "\t\t\t\t\t\t<th>" .  implode("</th><th>", $head) . "</th>";
		$table[] = "\t\t\t\t\t</tr>";
		$table[] = "\t\t\t\t</thead>";
		$table[] = "\t\t\t\t<tbody>";
		foreach ($rows as $row)
		{
			$table[] = "\t\t\t\t\t<tr class=\"subform-repeatable-group\">";
			$table[] = "\t\t\t\t\t\t" . $row;
			$table[] = "\t\t\t\t\t</tr>";
		}
		$table[] = "\t\t\t\t</tbody>";
		$table[] = "\t\t\t</table>";
		$table[] = "\t\t</div>";
		$table[] = "\t</div>";
		$table[] = "</div>";
		// return the table
		return implode("\n", $table);
	}

	
	public static function jsonToString($value, $sperator = ", ", $table = null, $id = 'id', $name = 'name')
	{
		// do some table foot work
		$external = false;
		if (strpos($table, '#__') !== false)
		{
			$external = true;
			$table = str_replace('#__', '', $table);
		}
		// check if string is JSON
		$result = json_decode($value, true);
		if (json_last_error() === JSON_ERROR_NONE)
		{
			// is JSON
			if (self::checkArray($result))
			{
				if (self::checkString($table))
				{
					$names = array();
					foreach ($result as $val)
					{
						if ($external)
						{
							if ($_name = self::getVar(null, $val, $id, $name, '=', $table))
							{
								$names[] = $_name;
							}
						}
						else
						{
							if ($_name = self::getVar($table, $val, $id, $name))
							{
								$names[] = $_name;
							}
						}
					}
					if (self::checkArray($names))
					{
						return (string) implode($sperator,$names);
					}	
				}
				return (string) implode($sperator,$result);
			}
			return (string) json_decode($value);
		}
		return $value;
	}

	/**
	* Load the Component xml manifest.
	**/
	public static function manifest()
	{
		$manifestUrl = JPATH_ADMINISTRATOR."/components/com_componentbuilder/componentbuilder.xml";
		return simplexml_load_file($manifestUrl);
	}

	/**
	* Joomla version object
	**/	
	protected static $JVersion;

	/**
	* set/get Joomla version
	**/
	public static function jVersion()
	{
		// check if set
		if (!self::checkObject(self::$JVersion))
		{
			self::$JVersion = new JVersion();
		}
		return self::$JVersion;
	}

	/**
	* Load the Contributors details.
	**/
	public static function getContributors()
	{
		// get params
		$params	= JComponentHelper::getParams('com_componentbuilder');
		// start contributors array
		$contributors = array();
		// get all Contributors (max 20)
		$searchArray = range('0','20');
		foreach($searchArray as $nr)
		{
			if ((NULL !== $params->get("showContributor".$nr)) && ($params->get("showContributor".$nr) == 2 || $params->get("showContributor".$nr) == 3))
			{
				// set link based of selected option
				if($params->get("useContributor".$nr) == 1)
                                {
					$link_front = '<a href="mailto:'.$params->get("emailContributor".$nr).'" target="_blank">';
					$link_back = '</a>';
				}
                                elseif($params->get("useContributor".$nr) == 2)
                                {
					$link_front = '<a href="'.$params->get("linkContributor".$nr).'" target="_blank">';
					$link_back = '</a>';
				}
                                else
                                {
					$link_front = '';
					$link_back = '';
				}
				$contributors[$nr]['title']	= self::htmlEscape($params->get("titleContributor".$nr));
				$contributors[$nr]['name']	= $link_front.self::htmlEscape($params->get("nameContributor".$nr)).$link_back;
			}
		}
		return $contributors;
	}

	/**
	 *	Load the Component Help URLs.
	 **/
	public static function getHelpUrl($view)
	{
		$user	= JFactory::getUser();
		$groups = $user->get('groups');
		$db	= JFactory::getDbo();
		$query	= $db->getQuery(true);
		$query->select(array('a.id','a.groups','a.target','a.type','a.article','a.url'));
		$query->from('#__componentbuilder_help_document AS a');
		$query->where('a.site_view = '.$db->quote($view));
		$query->where('a.location = 2');
		$query->where('a.published = 1');
		$db->setQuery($query);
		$db->execute();
		if($db->getNumRows())
		{
			$helps = $db->loadObjectList();
			if (self::checkArray($helps))
			{
				foreach ($helps as $nr => $help)
				{
					if ($help->target == 1)
					{
						$targetgroups = json_decode($help->groups, true);
						if (!array_intersect($targetgroups, $groups))
						{
							// if user not in those target groups then remove the item
							unset($helps[$nr]);
							continue;
						}
					}
					// set the return type
					switch ($help->type)
					{
						// set joomla article
						case 1:
							return self::loadArticleLink($help->article);
							break;
						// set help text
						case 2:
							return self::loadHelpTextLink($help->id);
							break;
						// set Link
						case 3:
							return $help->url;
							break;
					}
				}
			}
		}
		return false;
	}

	/**
	 *	Get the Article Link.
	 **/
	protected static function loadArticleLink($id)
	{
		return JURI::root().'index.php?option=com_content&view=article&id='.$id.'&tmpl=component&layout=modal';
	}

	/**
	 *	Get the Help Text Link.
	 **/
	protected static function loadHelpTextLink($id)
	{
		$token = JSession::getFormToken();
		return 'index.php?option=com_componentbuilder&task=help.getText&id=' . (int) $id . '&token=' . $token;
	}

	/**
	* Get any component's model
	**/
	public static function getModel($name, $path = JPATH_COMPONENT_SITE, $component = 'Componentbuilder', $config = array())
	{
		// fix the name
		$name = self::safeString($name);
		// full path
		$fullPath = $path . '/models';
		// set prefix
		$prefix = $component.'Model';
		// load the model file
		JModelLegacy::addIncludePath($fullPath, $prefix);
		// get instance
		$model = JModelLegacy::getInstance($name, $prefix, $config);
		// if model not found (strange)
		if ($model == false)
		{
			jimport('joomla.filesystem.file');
			// get file path
			$filePath = $path.'/'.$name.'.php';
			$fullPath = $fullPath.'/'.$name.'.php';
			// check if it exists
			if (JFile::exists($filePath))
			{
				// get the file
				require_once $filePath;
			}
			elseif (JFile::exists($fullPath))
			{
				// get the file
				require_once $fullPath;
			}
			// build class names
			$modelClass = $prefix.$name;
			if (class_exists($modelClass))
			{
				// initialize the model
				return new $modelClass($config);
			}
		}
		return $model;
	}

	/**
	* Add to asset Table
	*/
	public static function setAsset($id, $table, $inherit = true)
	{
		$parent = JTable::getInstance('Asset');
		$parent->loadByName('com_componentbuilder');
		
		$parentId = $parent->id;
		$name     = 'com_componentbuilder.'.$table.'.'.$id;
		$title    = '';

		$asset = JTable::getInstance('Asset');
		$asset->loadByName($name);

		// Check for an error.
		$error = $asset->getError();

		if ($error)
		{
			return false;
		}
		else
		{
			// Specify how a new or moved node asset is inserted into the tree.
			if ($asset->parent_id != $parentId)
			{
				$asset->setLocation($parentId, 'last-child');
			}

			// Prepare the asset to be stored.
			$asset->parent_id = $parentId;
			$asset->name      = $name;
			$asset->title     = $title;
			// get the default asset rules
			$rules = self::getDefaultAssetRules('com_componentbuilder', $table, $inherit);
			if ($rules instanceof JAccessRules)
			{
				$asset->rules = (string) $rules;
			}

			if (!$asset->check() || !$asset->store())
			{
				JFactory::getApplication()->enqueueMessage($asset->getError(), 'warning');
				return false;
			}
			else
			{
				// Create an asset_id or heal one that is corrupted.
				$object = new stdClass();

				// Must be a valid primary key value.
				$object->id = $id;
				$object->asset_id = (int) $asset->id;

				// Update their asset_id to link to the asset table.
				return JFactory::getDbo()->updateObject('#__componentbuilder_'.$table, $object, 'id');
			}
		}
		return false;
	}

	/**
	 * Gets the default asset Rules for a component/view.
	 */
	protected static function getDefaultAssetRules($component, $view, $inherit = true)
	{
		// if new or inherited
		$assetId = 0;
		// Only get the actual item rules if not inheriting
		if (!$inherit)
		{
			// Need to find the asset id by the name of the component.
			$db = JFactory::getDbo();
			$query = $db->getQuery(true)
				->select($db->quoteName('id'))
				->from($db->quoteName('#__assets'))
				->where($db->quoteName('name') . ' = ' . $db->quote($component));
			$db->setQuery($query);
			$db->execute();
			// check that there is a value
			if ($db->getNumRows())
			{
				// asset already set so use saved rules
				$assetId = (int) $db->loadResult();
			}
		}
		// get asset rules
		$result =  JAccess::getAssetRules($assetId);
		if ($result instanceof JAccessRules)
		{
			$_result = (string) $result;
			$_result = json_decode($_result);
			foreach ($_result as $name => &$rule)
			{
				$v = explode('.', $name);
				if ($view !== $v[0])
				{
					// remove since it is not part of this view
					unset($_result->$name);
				}
				elseif ($inherit)
				{
					// clear the value since we inherit
					$rule = array();
				}
			}
			// check if there are any view values remaining
			if (count($_result))
			{
				$_result = json_encode($_result);
				$_result = array($_result);
				// Instantiate and return the JAccessRules object for the asset rules.
				$rules = new JAccessRules($_result);
				// return filtered rules
				return $rules;
			}
		}
		return $result;
	}

	/**
	 * xmlAppend
	 *
	 * @param   SimpleXMLElement   $xml      The XML element reference in which to inject a comment
	 * @param   mixed              $node     A SimpleXMLElement node to append to the XML element reference, or a stdClass object containing a comment attribute to be injected before the XML node and a fieldXML attribute containing a SimpleXMLElement
	 *
	 * @return  null
	 *
	 */
	public static function xmlAppend(&$xml, $node)
	{
		if (!$node)
		{
			// element was not returned
			return;
		}
		switch (get_class($node))
		{
			case 'stdClass':
				if (property_exists($node, 'comment'))
				{
					self::xmlComment($xml, $node->comment);
				}
				if (property_exists($node, 'fieldXML'))
				{
					self::xmlAppend($xml, $node->fieldXML);
				}
				break;
			case 'SimpleXMLElement':
				$domXML = dom_import_simplexml($xml);
				$domNode = dom_import_simplexml($node);
				$domXML->appendChild($domXML->ownerDocument->importNode($domNode, true));
				$xml = simplexml_import_dom($domXML);
				break;
		}
	}

	/**
	 * xmlComment
	 *
	 * @param   SimpleXMLElement   $xml        The XML element reference in which to inject a comment
	 * @param   string             $comment    The comment to inject
	 *
	 * @return  null
	 *
	 */
	public static function xmlComment(&$xml, $comment)
	{
		$domXML = dom_import_simplexml($xml);
		$domComment = new DOMComment($comment);
		$nodeTarget = $domXML->ownerDocument->importNode($domComment, true);
		$domXML->appendChild($nodeTarget);
		$xml = simplexml_import_dom($domXML);
	}

	/**
	 * xmlAddAttributes
	 *
	 * @param   SimpleXMLElement   $xml          The XML element reference in which to inject a comment
	 * @param   array              $attributes   The attributes to apply to the XML element
	 *
	 * @return  null
	 *
	 */
	public static function xmlAddAttributes(&$xml, $attributes = array())
	{
		foreach ($attributes as $key => $value)
		{
			$xml->addAttribute($key, $value);
		}
	}

	/**
	 * xmlAddOptions
	 *
	 * @param   SimpleXMLElement   $xml          The XML element reference in which to inject a comment
	 * @param   array              $options      The options to apply to the XML element
	 *
	 * @return  void
	 *
	 */
	public static function xmlAddOptions(&$xml, $options = array())
	{
		foreach ($options as $key => $value)
		{
			$addOption = $xml->addChild('option');
			$addOption->addAttribute('value', $key);
			$addOption[] = $value;
		}
	}

	/**
	 * get the field object
	 *
	 * @param   array      $attributes   The array of attributes
	 * @param   string     $default      The default of the field
	 * @param   array      $options      The options to apply to the XML element
	 *
	 * @return  object
	 *
	 */
	public static function getFieldObject($attributes, $default = '', $options = null)
	{
		// make sure we have attributes and a type value
		if (self::checkArray($attributes) && isset($attributes['type']))
		{
			// make sure the form helper class is loaded
			if (!method_exists('JFormHelper', 'loadFieldType'))
			{
				jimport('joomla.form.form');
			}
			// get field type
			$field = JFormHelper::loadFieldType($attributes['type'],true);
			// start field xml
			$XML = new SimpleXMLElement('<field/>');
			// load the attributes
			self::xmlAddAttributes($XML, $attributes);
			// check if we have options
			if (self::checkArray($options))
			{
				// load the options
				self::xmlAddOptions($XML, $options);
			}
			// setup the field
			$field->setup($XML, $default);
			// return the field object
			return $field;
		}
		return false;
	}

	/**
	 * Render Bool Button
	 *
	 * @param   array   $args   All the args for the button
	 *                             0) name
	 *                             1) additional (options class) // not used at this time
	 *                             2) default
	 *                             3) yes (name)
	 *                             4) no (name)
	 *
	 * @return  string    The input html of the button
	 *
	 */
	public static function renderBoolButton()
	{
		$args = func_get_args();
		// check if there is additional button class
		$additional = isset($args[1]) ? (string) $args[1] : ''; // not used at this time
		// button attributes
		$buttonAttributes = array(
			'type' => 'radio',
			'name' => isset($args[0]) ? self::htmlEscape($args[0]) : 'bool_button',
			'label' => isset($args[0]) ? self::safeString(self::htmlEscape($args[0]), 'Ww') : 'Bool Button', // not seen anyway
			'class' => 'btn-group',
			'filter' => 'INT',
			'default' => isset($args[2]) ? (int) $args[2] : 0);
		// set the button options
		$buttonOptions = array(
			'1' => isset($args[3]) ? self::htmlEscape($args[3]) : 'JYES',
			'0' => isset($args[4]) ? self::htmlEscape($args[4]) : 'JNO');
		// return the input
		return self::getFieldObject($buttonAttributes, $buttonAttributes['default'], $buttonOptions)->input;
	}

	/**
	 *  UIKIT Component Classes
	 **/
	public static $uk_components = array(
			'data-uk-grid' => array(
				'grid' ),
			'uk-accordion' => array(
				'accordion' ),
			'uk-autocomplete' => array(
				'autocomplete' ),
			'data-uk-datepicker' => array(
				'datepicker' ),
			'uk-form-password' => array(
				'form-password' ),
			'uk-form-select' => array(
				'form-select' ),
			'data-uk-htmleditor' => array(
				'htmleditor' ),
			'data-uk-lightbox' => array(
				'lightbox' ),
			'uk-nestable' => array(
				'nestable' ),
			'UIkit.notify' => array(
				'notify' ),
			'data-uk-parallax' => array(
				'parallax' ),
			'uk-search' => array(
				'search' ),
			'uk-slider' => array(
				'slider' ),
			'uk-slideset' => array(
				'slideset' ),
			'uk-slideshow' => array(
				'slideshow',
				'slideshow-fx' ),
			'uk-sortable' => array(
				'sortable' ),
			'data-uk-sticky' => array(
				'sticky' ),
			'data-uk-timepicker' => array(
				'timepicker' ),
			'data-uk-tooltip' => array(
				'tooltip' ),
			'uk-placeholder' => array(
				'placeholder' ),
			'uk-dotnav' => array(
				'dotnav' ),
			'uk-slidenav' => array(
				'slidenav' ),
			'uk-form' => array(
				'form-advanced' ),
			'uk-progress' => array(
				'progress' ),
			'upload-drop' => array(
				'upload', 'form-file' )
			);

	/**
	 *  Add UIKIT Components
	 **/
	public static $uikit = false;

	/**
	 *  Get UIKIT Components
	 **/
	public static function getUikitComp($content,$classes = array())
	{
		if (strpos($content,'class="uk-') !== false)
		{
			// reset
			$temp = array();
			foreach (self::$uk_components as $looking => $add)
			{
				if (strpos($content,$looking) !== false)
				{
					$temp[] = $looking;
				}
			}
			// make sure uikit is loaded to config
			if (strpos($content,'class="uk-') !== false)
			{
				self::$uikit = true;
			}
			// sorter
			if (self::checkArray($temp))
			{
				// merger
				if (self::checkArray($classes))
				{
					$newTemp = array_merge($temp,$classes);
					$temp = array_unique($newTemp);
				}
				return $temp;
			}
		}
		if (self::checkArray($classes))
		{
			return $classes;
		}
		return false;
	}

	/**
	 * Get a variable 
	 *
	 * @param   string   $table        The table from which to get the variable
	 * @param   string   $where        The value where
	 * @param   string   $whereString  The target/field string where/name
	 * @param   string   $what         The return field
	 * @param   string   $operator     The operator between $whereString/field and $where/value
	 * @param   string   $main         The component in which the table is found
	 *
	 * @return  mix string/int/float
	 *
	 */
	public static function getVar($table, $where = null, $whereString = 'user', $what = 'id', $operator = '=', $main = 'componentbuilder')
	{
		if(!$where)
		{
			$where = JFactory::getUser()->id;
		}
		// Get a db connection.
		$db = JFactory::getDbo();
		// Create a new query object.
		$query = $db->getQuery(true);
		$query->select($db->quoteName(array($what)));		
		if (empty($table))
		{
			$query->from($db->quoteName('#__'.$main));
		}
		else
		{
			$query->from($db->quoteName('#__'.$main.'_'.$table));
		}
		if (is_numeric($where))
		{
			$query->where($db->quoteName($whereString) . ' '.$operator.' '.(int) $where);
		}
		elseif (is_string($where))
		{
			$query->where($db->quoteName($whereString) . ' '.$operator.' '. $db->quote((string)$where));
		}
		else
		{
			return false;
		}
		$db->setQuery($query);
		$db->execute();
		if ($db->getNumRows())
		{
			return $db->loadResult();
		}
		return false;
	}

	/**
	 * Get array of variables
	 *
	 * @param   string   $table        The table from which to get the variables
	 * @param   string   $where        The value where
	 * @param   string   $whereString  The target/field string where/name
	 * @param   string   $what         The return field
	 * @param   string   $operator     The operator between $whereString/field and $where/value
	 * @param   string   $main         The component in which the table is found
	 * @param   bool     $unique       The switch to return a unique array
	 *
	 * @return  array
	 *
	 */
	public static function getVars($table, $where = null, $whereString = 'user', $what = 'id', $operator = 'IN', $main = 'componentbuilder', $unique = true)
	{
		if(!$where)
		{
			$where = JFactory::getUser()->id;
		}

		if (!self::checkArray($where) && $where > 0)
		{
			$where = array($where);
		}

		if (self::checkArray($where))
		{
			// prep main <-- why? well if $main='' is empty then $table can be categories or users
			if (self::checkString($main))
			{
				$main = '_'.ltrim($main, '_');
			}
			// Get a db connection.
			$db = JFactory::getDbo();
			// Create a new query object.
			$query = $db->getQuery(true);

			$query->select($db->quoteName(array($what)));
			if (empty($table))
			{
				$query->from($db->quoteName('#__'.$main));
			}
			else
			{
				$query->from($db->quoteName('#_'.$main.'_'.$table));
			}
			$query->where($db->quoteName($whereString) . ' '.$operator.' (' . implode(',',$where) . ')');
			$db->setQuery($query);
			$db->execute();
			if ($db->getNumRows())
			{
				if ($unique)
				{
					return array_unique($db->loadColumn());
				}
				return $db->loadColumn();
			}
		}
		return false;
	} 

	public static function isPublished($id,$type)
	{
		if ($type == 'raw')
		{
			$type = 'item';
		}
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select(array('a.published'));
		$query->from('#__componentbuilder_'.$type.' AS a');
		$query->where('a.id = '. (int) $id);
		$query->where('a.published = 1');
		$db->setQuery($query);
		$db->execute();
		$found = $db->getNumRows();
		if($found)
		{
			return true;
		}
		return false;
	}

	public static function getGroupName($id)
	{
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select(array('a.title'));
		$query->from('#__usergroups AS a');
		$query->where('a.id = '. (int) $id);
		$db->setQuery($query);
		$db->execute();
		$found = $db->getNumRows();
		if($found)
		{
			return $db->loadResult();
		}
		return $id;
	}

	/**
	* Get the action permissions
	*
	* @param  string   $view        The related view name
	* @param  int      $record      The item to act upon
	* @param  string   $views       The related list view name
	* @param  mixed    $target      Only get this permission (like edit, create, delete)
	* @param  string   $component   The target component
	*
	* @return  object   The JObject of permission/authorised actions
	* 
	**/
	public static function getActions($view, &$record = null, $views = null, $target = null, $component = 'componentbuilder')
	{
		// get the user object
		$user = JFactory::getUser();
		// load the JObject
		$result = new JObject;
		// make view name safe (just incase)
		$view = self::safeString($view);
		if (self::checkString($views))
		{
			$views = self::safeString($views);
 		}
		// get all actions from component
		$actions = JAccess::getActionsFromFile(
			JPATH_ADMINISTRATOR . '/components/com_' . $component . '/access.xml',
			"/access/section[@name='component']/"
		);
		// if non found then return empty JObject
		if (empty($actions))
		{
			return $result;
		}
		// get created by if not found
		if (self::checkObject($record) && !isset($record->created_by) && isset($record->id))
		{
			$record->created_by = self::getVar($view, $record->id, 'id', 'created_by', '=', $component);
		}
		// set actions only set in component settings
		$componentActions = array('core.admin', 'core.manage', 'core.options', 'core.export');
		// check if we have a target
		$checkTarget = false;
		if ($target)
		{
			// convert to an array
			if (self::checkString($target))
			{
				$target = array($target);
			}
			// check if we are good to go
			if (self::checkArray($target))
			{
				$checkTarget = true;
			}
		}
		// loop the actions and set the permissions
		foreach ($actions as $action)
		{
			// check target action filter
			if ($checkTarget && self::filterActions($view, $action->name, $target))
			{
				continue;
			}
			// set to use component default
			$fallback = true;
			// reset permission per/action
			$permission = false;
			$catpermission = false;
			// set area
			$area = 'comp';
			// check if the record has an ID and the action is item related (not a component action)
			if (self::checkObject($record) && isset($record->id) && $record->id > 0 && !in_array($action->name, $componentActions) &&
				(strpos($action->name, 'core.') !== false || strpos($action->name, $view . '.') !== false))
			{
				// we are in item
				$area = 'item';
				// The record has been set. Check the record permissions.
				$permission = $user->authorise($action->name, 'com_' . $component . '.' . $view . '.' . (int) $record->id);
				// if no permission found, check edit own
				if (!$permission)
				{
					// With edit, if the created_by matches current user then dig deeper.
					if (($action->name === 'core.edit' || $action->name === $view . '.edit') && $record->created_by > 0 && ($record->created_by == $user->id))
					{
						// the correct target
						$coreCheck = (array) explode('.', $action->name);
						// check that we have both local and global access
						if ($user->authorise($coreCheck[0] . '.edit.own', 'com_' . $component . '.' . $view . '.' . (int) $record->id) &&
							$user->authorise($coreCheck[0]  . '.edit.own', 'com_' . $component))
						{
							// allow edit
							$result->set($action->name, true);
							// set not to use global default
							// because we already validated it
							$fallback = false;
						}
						else
						{
							// do not allow edit
							$result->set($action->name, false);
							$fallback = false;
						}
					}
				}
				elseif (self::checkString($views) && isset($record->catid) && $record->catid > 0)
				{
					// we are in item
					$area = 'category';
					// set the core check
					$coreCheck = explode('.', $action->name);
					$core = $coreCheck[0];
					// make sure we use the core. action check for the categories
					if (strpos($action->name, $view) !== false && strpos($action->name, 'core.') === false )
					{
						$coreCheck[0] = 'core';
						$categoryCheck = implode('.', $coreCheck);
					}
					else
					{
						$categoryCheck = $action->name;
					}
					// The record has a category. Check the category permissions.
					$catpermission = $user->authorise($categoryCheck, 'com_' . $component . '.' . $views . '.category.' . (int) $record->catid);
					if (!$catpermission && !is_null($catpermission))
					{
						// With edit, if the created_by matches current user then dig deeper.
						if (($action->name === 'core.edit' || $action->name === $view . '.edit') && $record->created_by > 0 && ($record->created_by == $user->id))
						{
							// check that we have both local and global access
							if ($user->authorise('core.edit.own', 'com_' . $component . '.' . $views . '.category.' . (int) $record->catid) &&
								$user->authorise($core . '.edit.own', 'com_' . $component))
							{
								// allow edit
								$result->set($action->name, true);
								// set not to use global default
								// because we already validated it
								$fallback = false;
							}
							else
							{
								// do not allow edit
								$result->set($action->name, false);
								$fallback = false;
							}
						}
					}
				}
			}
			// if allowed then fallback on component global settings
			if ($fallback)
			{
				// if item/category blocks access then don't fall back on global
				if ((($area === 'item') && !$permission) || (($area === 'category') && !$catpermission))
				{
					// do not allow
					$result->set($action->name, false);
				}
				// Finally remember the global settings have the final say. (even if item allow)
				// The local item permissions can block, but it can't open and override of global permissions.
				// Since items are created by users and global permissions is set by system admin.
				else
				{
					$result->set($action->name, $user->authorise($action->name, 'com_' . $component));
				}
			}
		}
		return $result;
	}

	/**
	* Filter the action permissions
	*
	* @param  string   $action   The action to check
	* @param  array    $targets  The array of target actions
	*
	* @return  boolean   true if action should be filtered out
	* 
	**/
	protected static function filterActions(&$view, &$action, &$targets)
	{
		foreach ($targets as $target)
		{
			if (strpos($action, $view . '.' . $target) !== false ||
				strpos($action, 'core.' . $target) !== false)
			{
				return false;
				break;
			}
		}
		return true;
	}

	/**
	* Check if have an json string
	*
	* @input	string   The json string to check
	*
	* @returns bool true on success
	**/
	public static function checkJson($string)
	{
		if (self::checkString($string))
		{
			json_decode($string);
			return (json_last_error() === JSON_ERROR_NONE);
		}
		return false;
	}

	/**
	* Check if have an object with a length
	*
	* @input	object   The object to check
	*
	* @returns bool true on success
	**/
	public static function checkObject($object)
	{
		if (isset($object) && is_object($object))
		{
			return count((array)$object) > 0;
		}
		return false;
	}

	/**
	* Check if have an array with a length
	*
	* @input	array   The array to check
	*
	* @returns bool/int  number of items in array on success
	**/
	public static function checkArray($array, $removeEmptyString = false)
	{
		if (isset($array) && is_array($array) && ($nr = count((array)$array)) > 0)
		{
			// also make sure the empty strings are removed
			if ($removeEmptyString)
			{
				foreach ($array as $key => $string)
				{
					if (empty($string))
					{
						unset($array[$key]);
					}
				}
				return self::checkArray($array, false);
			}
			return $nr;
		}
		return false;
	}

	/**
	* Check if have a string with a length
	*
	* @input	string   The string to check
	*
	* @returns bool true on success
	**/
	public static function checkString($string)
	{
		if (isset($string) && is_string($string) && strlen($string) > 0)
		{
			return true;
		}
		return false;
	}

	/**
	* Check if we are connected
	* Thanks https://stackoverflow.com/a/4860432/1429677
	*
	* @returns bool true on success
	**/
	public static function isConnected()
	{
		// If example.com is down, then probably the whole internet is down, since IANA maintains the domain. Right?
		$connected = @fsockopen("www.example.com", 80); 
			// website, port  (try 80 or 443)
		if ($connected)
		{
			//action when connected
			$is_conn = true;
			fclose($connected);
		}
		else
		{
			//action in connection failure
			$is_conn = false;
		}
		return $is_conn;
	}

	/**
	* Merge an array of array's
	*
	* @input	array   The arrays you would like to merge
	*
	* @returns array on success
	**/
	public static function mergeArrays($arrays)
	{
		if(self::checkArray($arrays))
		{
			$arrayBuket = array();
			foreach ($arrays as $array)
			{
				if (self::checkArray($array))
				{
					$arrayBuket = array_merge($arrayBuket, $array);
				}
			}
			return $arrayBuket;
		}
		return false;
	}

	// typo sorry!
	public static function sorten($string, $length = 40, $addTip = true)
	{
		return self::shorten($string, $length, $addTip);
	}

	/**
	* Shorten a string
	*
	* @input	string   The you would like to shorten
	*
	* @returns string on success
	**/
	public static function shorten($string, $length = 40, $addTip = true)
	{
		if (self::checkString($string))
		{
			$initial = strlen($string);
			$words = preg_split('/([\s\n\r]+)/', $string, null, PREG_SPLIT_DELIM_CAPTURE);
			$words_count = count((array)$words);

			$word_length = 0;
			$last_word = 0;
			for (; $last_word < $words_count; ++$last_word)
			{
				$word_length += strlen($words[$last_word]);
				if ($word_length > $length)
				{
					break;
				}
			}

			$newString	= implode(array_slice($words, 0, $last_word));
			$final	= strlen($newString);
			if ($initial != $final && $addTip)
			{
				$title = self::shorten($string, 400 , false);
				return '<span class="hasTip" title="'.$title.'" style="cursor:help">'.trim($newString).'...</span>';
			}
			elseif ($initial != $final && !$addTip)
			{
				return trim($newString).'...';
			}
		}
		return $string;
	}

	/**
	* Making strings safe (various ways)
	*
	* @input	string   The you would like to make safe
	*
	* @returns string on success
	**/
	public static function safeString($string, $type = 'L', $spacer = '_', $replaceNumbers = true, $keepOnlyCharacters = true)
	{
		if ($replaceNumbers === true)
		{
			// remove all numbers and replace with english text version (works well only up to millions)
			$string = self::replaceNumbers($string);
		}
		// 0nly continue if we have a string
		if (self::checkString($string))
		{
			// create file name without the extention that is safe
			if ($type === 'filename')
			{
				// make sure VDM is not in the string
				$string = str_replace('VDM', 'vDm', $string);
				// Remove anything which isn't a word, whitespace, number
				// or any of the following caracters -_()
				// If you don't need to handle multi-byte characters
				// you can use preg_replace rather than mb_ereg_replace
				// Thanks @Łukasz Rysiak!
				// $string = mb_ereg_replace("([^\w\s\d\-_\(\)])", '', $string);
				$string = preg_replace("([^\w\s\d\-_\(\)])", '', $string);
				// http://stackoverflow.com/a/2021729/1429677
				return preg_replace('/\s+/', ' ', $string);
			}
			// remove all other characters
			$string = trim($string);
			$string = preg_replace('/'.$spacer.'+/', ' ', $string);
			$string = preg_replace('/\s+/', ' ', $string);
			// remove all and keep only characters
			if ($keepOnlyCharacters)
			{
				$string = preg_replace("/[^A-Za-z ]/", '', $string);
			}
			// keep both numbers and characters
			else
			{
				$string = preg_replace("/[^A-Za-z0-9 ]/", '', $string);
			}
			// select final adaptations
			if ($type === 'L' || $type === 'strtolower')
			{
				// replace white space with underscore
				$string = preg_replace('/\s+/', $spacer, $string);
				// default is to return lower
				return strtolower($string);
			}
			elseif ($type === 'W')
			{
				// return a string with all first letter of each word uppercase(no undersocre)
				return ucwords(strtolower($string));
			}
			elseif ($type === 'w' || $type === 'word')
			{
				// return a string with all lowercase(no undersocre)
				return strtolower($string);
			}
			elseif ($type === 'Ww' || $type === 'Word')
			{
				// return a string with first letter of the first word uppercase and all the rest lowercase(no undersocre)
				return ucfirst(strtolower($string));
			}
			elseif ($type === 'WW' || $type === 'WORD')
			{
				// return a string with all the uppercase(no undersocre)
				return strtoupper($string);
			}
			elseif ($type === 'U' || $type === 'strtoupper')
			{
				// replace white space with underscore
				$string = preg_replace('/\s+/', $spacer, $string);
				// return all upper
				return strtoupper($string);
			}
			elseif ($type === 'F' || $type === 'ucfirst')
			{
				// replace white space with underscore
				$string = preg_replace('/\s+/', $spacer, $string);
				// return with first caracter to upper
				return ucfirst(strtolower($string));
			}
			elseif ($type === 'cA' || $type === 'cAmel' || $type === 'camelcase')
			{
				// convert all words to first letter uppercase
				$string = ucwords(strtolower($string));
				// remove white space
				$string = preg_replace('/\s+/', '', $string);
				// now return first letter lowercase
				return lcfirst($string);
			}
			// return string
			return $string;
		}
		// not a string
		return '';
	}

	public static function htmlEscape($var, $charset = 'UTF-8', $shorten = false, $length = 40)
	{
		if (self::checkString($var))
		{
			$filter = new JFilterInput();
			$string = $filter->clean(html_entity_decode(htmlentities($var, ENT_COMPAT, $charset)), 'HTML');
			if ($shorten)
			{
           		return self::shorten($string,$length);
			}
			return $string;
		}
		else
		{
			return '';
		}
	}

	public static function replaceNumbers($string)
	{
		// set numbers array
		$numbers = array();
		// first get all numbers
		preg_match_all('!\d+!', $string, $numbers);
		// check if we have any numbers
		if (isset($numbers[0]) && self::checkArray($numbers[0]))
		{
			foreach ($numbers[0] as $number)
			{
				$searchReplace[$number] = self::numberToString((int)$number);
			}
			// now replace numbers in string
			$string = str_replace(array_keys($searchReplace), array_values($searchReplace),$string);
			// check if we missed any, strange if we did.
			return self::replaceNumbers($string);
		}
		// return the string with no numbers remaining.
		return $string;
	}

	/**
	* Convert an integer into an English word string
	* Thanks to Tom Nicholson <http://php.net/manual/en/function.strval.php#41988>
	*
	* @input	an int
	* @returns a string
	**/
	public static function numberToString($x)
	{
		$nwords = array( "zero", "one", "two", "three", "four", "five", "six", "seven",
			"eight", "nine", "ten", "eleven", "twelve", "thirteen",
			"fourteen", "fifteen", "sixteen", "seventeen", "eighteen",
			"nineteen", "twenty", 30 => "thirty", 40 => "forty",
			50 => "fifty", 60 => "sixty", 70 => "seventy", 80 => "eighty",
			90 => "ninety" );

		if(!is_numeric($x))
		{
			$w = $x;
		}
		elseif(fmod($x, 1) != 0)
		{
			$w = $x;
		}
		else
		{
			if($x < 0)
			{
				$w = 'minus ';
				$x = -$x;
			}
			else
			{
				$w = '';
				// ... now $x is a non-negative integer.
			}

			if($x < 21)   // 0 to 20
			{
				$w .= $nwords[$x];
			}
			elseif($x < 100)  // 21 to 99
			{ 
				$w .= $nwords[10 * floor($x/10)];
				$r = fmod($x, 10);
				if($r > 0)
				{
					$w .= ' '. $nwords[$r];
				}
			}
			elseif($x < 1000)  // 100 to 999
			{
				$w .= $nwords[floor($x/100)] .' hundred';
				$r = fmod($x, 100);
				if($r > 0)
				{
					$w .= ' and '. self::numberToString($r);
				}
			}
			elseif($x < 1000000)  // 1000 to 999999
			{
				$w .= self::numberToString(floor($x/1000)) .' thousand';
				$r = fmod($x, 1000);
				if($r > 0)
				{
					$w .= ' ';
					if($r < 100)
					{
						$w .= 'and ';
					}
					$w .= self::numberToString($r);
				}
			} 
			else //  millions
			{    
				$w .= self::numberToString(floor($x/1000000)) .' million';
				$r = fmod($x, 1000000);
				if($r > 0)
				{
					$w .= ' ';
					if($r < 100)
					{
						$w .= 'and ';
					}
					$w .= self::numberToString($r);
				}
			}
		}
		return $w;
	}

	/**
	* Random Key
	*
	* @returns a string
	**/
	public static function randomkey($size)
	{
		$bag = "abcefghijknopqrstuwxyzABCDDEFGHIJKLLMMNOPQRSTUVVWXYZabcddefghijkllmmnopqrstuvvwxyzABCEFGHIJKNOPQRSTUWXYZ";
		$key = array();
		$bagsize = strlen($bag) - 1;
		for ($i = 0; $i < $size; $i++)
		{
			$get = rand(0, $bagsize);
			$key[] = $bag[$get];
		}
		return implode($key);
	}

	/**
	 *	Get The Encryption Keys
	 *
	 *	@param  string        $type     The type of key
	 *	@param  string/bool   $default  The return value if no key was found
	 *
	 *	@return  string   On success
	 *
	 **/
	public static function getCryptKey($type, $default = false)
	{
		// Get the global params
		$params = JComponentHelper::getParams('com_componentbuilder', true);
		// Basic Encryption Type
		if ('basic' === $type)
		{
			$basic_key = $params->get('basic_key', $default);
			if (self::checkString($basic_key))
			{
				return $basic_key;
			}
		}

		return $default;
	}
}
