<?php
/**
 * @package    Joomla.Component.Builder
 *
 * @created    30th April, 2015
 * @author     Llewellyn van der Merwe <http://www.joomlacomponentbuilder.com>
 * @github     Joomla Component Builder <https://github.com/vdm-io/Joomla-Component-Builder>
 * @copyright  Copyright (C) 2015 - 2018 Vast Development Method. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');
?>
###BOM###

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

JHTML::_('behavior.modal');

/**
 * Script File of ###Component### Component
 */
class com_###component###InstallerScript
{
	/**
	 * method to install the component
	 *
	 * @return void
	 */
	function install($parent)
	{

	}

	/**
	 * method to uninstall the component
	 *
	 * @return void
	 */
	function uninstall($parent)
	{###UNINSTALLSCRIPT###
		// little notice as after service, in case of bad experience with component.
		echo '<h2>Did something go wrong? Are you disappointed?</h2>
		<p>Please let me know at <a href="mailto:###AUTHOREMAIL###">###AUTHOREMAIL###</a>.
		<br />We at ###COMPANYNAME### are committed to building extensions that performs proficiently! You can help us, really!
		<br />Send me your thoughts on improvements that is needed, trust me, I will be very grateful!
		<br />Visit us at <a href="###AUTHORWEBSITE###" target="_blank">###AUTHORWEBSITE###</a> today!</p>';
	}

	/**
	 * method to update the component
	 *
	 * @return void
	 */
	function update($parent)
	{
		
	}

	/**
	 * method to run before an install/update/uninstall method
	 *
	 * @return void
	 */
	function preflight($type, $parent)
	{
		// get application
		$app = JFactory::getApplication();
		// is redundant ...hmmm
		if ($type == 'uninstall')
		{
			return true;
		}
		// the default for both install and update
		$jversion = new JVersion();
		if (!$jversion->isCompatible('3.6.0'))
		{
			$app->enqueueMessage('Please upgrade to at least Joomla! 3.6.0 before continuing!', 'error');
			return false;
		}
		// do any updates needed
		if ($type == 'update')
		{###PREUPDATESCRIPT###
		}
		// do any install needed
		if ($type == 'install')
		{###PREINSTALLSCRIPT###
		}
	}

	/**
	 * method to run after an install/update/uninstall method
	 *
	 * @return void
	 */
	function postflight($type, $parent)
	{
		// get application
		$app = JFactory::getApplication();###MOVEFOLDERSSCRIPT###
		// set the default component settings
		if ($type == 'install')
		{###POSTINSTALLSCRIPT###
		}
		// do any updates needed
		if ($type == 'update')
		{###POSTUPDATESCRIPT###
		}
	}###MOVEFOLDERSMETHOD###
}
