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

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('formbehavior.chosen', 'select');
JHtml::_('behavior.keepalive');
$componentParams = $this->params; // will be removed just use $this->params instead
?>
<script type="text/javascript">
	// waiting spinner
	var outerDiv = jQuery('body');
	jQuery('<div id="loading"></div>')
		.css("background", "rgba(255, 255, 255, .8) url('components/com_componentbuilder/assets/images/import.gif') 50% 15% no-repeat")
		.css("top", outerDiv.position().top - jQuery(window).scrollTop())
		.css("left", outerDiv.position().left - jQuery(window).scrollLeft())
		.css("width", outerDiv.width())
		.css("height", outerDiv.height())
		.css("position", "fixed")
		.css("opacity", "0.80")
		.css("-ms-filter", "progid:DXImageTransform.Microsoft.Alpha(Opacity = 80)")
		.css("filter", "alpha(opacity = 80)")
		.css("display", "none")
		.appendTo(outerDiv);
	jQuery('#loading').show();
	// when page is ready remove and show
	jQuery(window).load(function() {
		jQuery('#componentbuilder_loader').fadeIn('fast');
		jQuery('#loading').hide();
	});
</script>
<div id="componentbuilder_loader" style="display: none;">
<form action="<?php echo JRoute::_('index.php?option=com_componentbuilder&layout=edit&id='. (int) $this->item->id . $this->referral); ?>" method="post" name="adminForm" id="adminForm" class="form-validate" enctype="multipart/form-data">

	<?php echo JLayoutHelper::render('server.details_above', $this); ?>
<div class="form-horizontal">

	<?php echo JHtml::_('bootstrap.startTabSet', 'serverTab', array('active' => 'details')); ?>

	<?php echo JHtml::_('bootstrap.addTab', 'serverTab', 'details', JText::_('COM_COMPONENTBUILDER_SERVER_DETAILS', true)); ?>
		<div class="row-fluid form-horizontal-desktop">
			<div class="span6">
				<?php echo JLayoutHelper::render('server.details_left', $this); ?>
			</div>
			<div class="span6">
				<?php echo JLayoutHelper::render('server.details_right', $this); ?>
			</div>
		</div>
		<div class="row-fluid form-horizontal-desktop">
			<div class="span12">
				<?php echo JLayoutHelper::render('server.details_fullwidth', $this); ?>
			</div>
		</div>
	<?php echo JHtml::_('bootstrap.endTab'); ?>

	<?php if ($this->canDo->get('joomla_component.access')) : ?>
	<?php echo JHtml::_('bootstrap.addTab', 'serverTab', 'linked_components', JText::_('COM_COMPONENTBUILDER_SERVER_LINKED_COMPONENTS', true)); ?>
		<div class="row-fluid form-horizontal-desktop">
		</div>
		<div class="row-fluid form-horizontal-desktop">
			<div class="span12">
				<?php echo JLayoutHelper::render('server.linked_components_fullwidth', $this); ?>
			</div>
		</div>
	<?php echo JHtml::_('bootstrap.endTab'); ?>
	<?php endif; ?>

	<?php $this->ignore_fieldsets = array('details','metadata','vdmmetadata','accesscontrol'); ?>
	<?php $this->tab_name = 'serverTab'; ?>
	<?php echo JLayoutHelper::render('joomla.edit.params', $this); ?>

	<?php if ($this->canDo->get('server.delete') || $this->canDo->get('server.edit.created_by') || $this->canDo->get('server.edit.state') || $this->canDo->get('server.edit.created')) : ?>
	<?php echo JHtml::_('bootstrap.addTab', 'serverTab', 'publishing', JText::_('COM_COMPONENTBUILDER_SERVER_PUBLISHING', true)); ?>
		<div class="row-fluid form-horizontal-desktop">
			<div class="span6">
				<?php echo JLayoutHelper::render('server.publishing', $this); ?>
			</div>
			<div class="span6">
				<?php echo JLayoutHelper::render('server.publlshing', $this); ?>
			</div>
		</div>
	<?php echo JHtml::_('bootstrap.endTab'); ?>
	<?php endif; ?>

	<?php if ($this->canDo->get('core.admin')) : ?>
	<?php echo JHtml::_('bootstrap.addTab', 'serverTab', 'permissions', JText::_('COM_COMPONENTBUILDER_SERVER_PERMISSION', true)); ?>
		<div class="row-fluid form-horizontal-desktop">
			<div class="span12">
				<fieldset class="adminform">
					<div class="adminformlist">
					<?php foreach ($this->form->getFieldset('accesscontrol') as $field): ?>
						<div>
							<?php echo $field->label; echo $field->input;?>
						</div>
						<div class="clearfix"></div>
					<?php endforeach; ?>
					</div>
				</fieldset>
			</div>
		</div>
	<?php echo JHtml::_('bootstrap.endTab'); ?>
	<?php endif; ?>

	<?php echo JHtml::_('bootstrap.endTabSet'); ?>

	<div>
		<input type="hidden" name="task" value="server.edit" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
	</div>
</div>
</form>
</div>

<script type="text/javascript">

// #jform_protocol listeners for protocol_vvvvwbm function
jQuery('#jform_protocol').on('keyup',function()
{
	var protocol_vvvvwbm = jQuery("#jform_protocol").val();
	vvvvwbm(protocol_vvvvwbm);

});
jQuery('#adminForm').on('change', '#jform_protocol',function (e)
{
	e.preventDefault();
	var protocol_vvvvwbm = jQuery("#jform_protocol").val();
	vvvvwbm(protocol_vvvvwbm);

});

// #jform_protocol listeners for protocol_vvvvwbn function
jQuery('#jform_protocol').on('keyup',function()
{
	var protocol_vvvvwbn = jQuery("#jform_protocol").val();
	vvvvwbn(protocol_vvvvwbn);

});
jQuery('#adminForm').on('change', '#jform_protocol',function (e)
{
	e.preventDefault();
	var protocol_vvvvwbn = jQuery("#jform_protocol").val();
	vvvvwbn(protocol_vvvvwbn);

});

// #jform_protocol listeners for protocol_vvvvwbo function
jQuery('#jform_protocol').on('keyup',function()
{
	var protocol_vvvvwbo = jQuery("#jform_protocol").val();
	var authentication_vvvvwbo = jQuery("#jform_authentication").val();
	vvvvwbo(protocol_vvvvwbo,authentication_vvvvwbo);

});
jQuery('#adminForm').on('change', '#jform_protocol',function (e)
{
	e.preventDefault();
	var protocol_vvvvwbo = jQuery("#jform_protocol").val();
	var authentication_vvvvwbo = jQuery("#jform_authentication").val();
	vvvvwbo(protocol_vvvvwbo,authentication_vvvvwbo);

});

// #jform_authentication listeners for authentication_vvvvwbo function
jQuery('#jform_authentication').on('keyup',function()
{
	var protocol_vvvvwbo = jQuery("#jform_protocol").val();
	var authentication_vvvvwbo = jQuery("#jform_authentication").val();
	vvvvwbo(protocol_vvvvwbo,authentication_vvvvwbo);

});
jQuery('#adminForm').on('change', '#jform_authentication',function (e)
{
	e.preventDefault();
	var protocol_vvvvwbo = jQuery("#jform_protocol").val();
	var authentication_vvvvwbo = jQuery("#jform_authentication").val();
	vvvvwbo(protocol_vvvvwbo,authentication_vvvvwbo);

});

// #jform_protocol listeners for protocol_vvvvwbq function
jQuery('#jform_protocol').on('keyup',function()
{
	var protocol_vvvvwbq = jQuery("#jform_protocol").val();
	var authentication_vvvvwbq = jQuery("#jform_authentication").val();
	vvvvwbq(protocol_vvvvwbq,authentication_vvvvwbq);

});
jQuery('#adminForm').on('change', '#jform_protocol',function (e)
{
	e.preventDefault();
	var protocol_vvvvwbq = jQuery("#jform_protocol").val();
	var authentication_vvvvwbq = jQuery("#jform_authentication").val();
	vvvvwbq(protocol_vvvvwbq,authentication_vvvvwbq);

});

// #jform_authentication listeners for authentication_vvvvwbq function
jQuery('#jform_authentication').on('keyup',function()
{
	var protocol_vvvvwbq = jQuery("#jform_protocol").val();
	var authentication_vvvvwbq = jQuery("#jform_authentication").val();
	vvvvwbq(protocol_vvvvwbq,authentication_vvvvwbq);

});
jQuery('#adminForm').on('change', '#jform_authentication',function (e)
{
	e.preventDefault();
	var protocol_vvvvwbq = jQuery("#jform_protocol").val();
	var authentication_vvvvwbq = jQuery("#jform_authentication").val();
	vvvvwbq(protocol_vvvvwbq,authentication_vvvvwbq);

});

// #jform_protocol listeners for protocol_vvvvwbs function
jQuery('#jform_protocol').on('keyup',function()
{
	var protocol_vvvvwbs = jQuery("#jform_protocol").val();
	var authentication_vvvvwbs = jQuery("#jform_authentication").val();
	vvvvwbs(protocol_vvvvwbs,authentication_vvvvwbs);

});
jQuery('#adminForm').on('change', '#jform_protocol',function (e)
{
	e.preventDefault();
	var protocol_vvvvwbs = jQuery("#jform_protocol").val();
	var authentication_vvvvwbs = jQuery("#jform_authentication").val();
	vvvvwbs(protocol_vvvvwbs,authentication_vvvvwbs);

});

// #jform_authentication listeners for authentication_vvvvwbs function
jQuery('#jform_authentication').on('keyup',function()
{
	var protocol_vvvvwbs = jQuery("#jform_protocol").val();
	var authentication_vvvvwbs = jQuery("#jform_authentication").val();
	vvvvwbs(protocol_vvvvwbs,authentication_vvvvwbs);

});
jQuery('#adminForm').on('change', '#jform_authentication',function (e)
{
	e.preventDefault();
	var protocol_vvvvwbs = jQuery("#jform_protocol").val();
	var authentication_vvvvwbs = jQuery("#jform_authentication").val();
	vvvvwbs(protocol_vvvvwbs,authentication_vvvvwbs);

});

// #jform_protocol listeners for protocol_vvvvwbu function
jQuery('#jform_protocol').on('keyup',function()
{
	var protocol_vvvvwbu = jQuery("#jform_protocol").val();
	var authentication_vvvvwbu = jQuery("#jform_authentication").val();
	vvvvwbu(protocol_vvvvwbu,authentication_vvvvwbu);

});
jQuery('#adminForm').on('change', '#jform_protocol',function (e)
{
	e.preventDefault();
	var protocol_vvvvwbu = jQuery("#jform_protocol").val();
	var authentication_vvvvwbu = jQuery("#jform_authentication").val();
	vvvvwbu(protocol_vvvvwbu,authentication_vvvvwbu);

});

// #jform_authentication listeners for authentication_vvvvwbu function
jQuery('#jform_authentication').on('keyup',function()
{
	var protocol_vvvvwbu = jQuery("#jform_protocol").val();
	var authentication_vvvvwbu = jQuery("#jform_authentication").val();
	vvvvwbu(protocol_vvvvwbu,authentication_vvvvwbu);

});
jQuery('#adminForm').on('change', '#jform_authentication',function (e)
{
	e.preventDefault();
	var protocol_vvvvwbu = jQuery("#jform_protocol").val();
	var authentication_vvvvwbu = jQuery("#jform_authentication").val();
	vvvvwbu(protocol_vvvvwbu,authentication_vvvvwbu);

});

</script>
