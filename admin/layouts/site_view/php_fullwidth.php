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

$form = $displayData->getForm();

$fields = $displayData->get('fields') ?: array(
	'add_php_ajax',
	'php_ajaxmethod',
	'ajax_input',
	'add_php_document',
	'php_document',
	'add_php_view',
	'php_view',
	'add_php_jview_display',
	'php_jview_display',
	'add_php_jview',
	'php_jview'
);

$hiddenFields = $displayData->get('hidden_fields') ?: array();

?>
<div class="form-vertical">
	<?php foreach($fields as $field): ?>
		<?php if (in_array($field, $hiddenFields)) : ?>
			<?php $form->setFieldAttribute($field, 'type', 'hidden'); ?>
		<?php endif; ?>
		<?php echo $form->renderField($field, null, null, array('class' => 'control-wrapper-' . $field)); ?>
	<?php endforeach; ?>
</div>
