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

	<?php echo JLayoutHelper::render('dynamic_get.main_above', $this); ?>
<div class="form-horizontal">

	<?php echo JHtml::_('bootstrap.startTabSet', 'dynamic_getTab', array('active' => 'main')); ?>

	<?php echo JHtml::_('bootstrap.addTab', 'dynamic_getTab', 'main', JText::_('COM_COMPONENTBUILDER_DYNAMIC_GET_MAIN', true)); ?>
		<div class="row-fluid form-horizontal-desktop">
			<div class="span6">
				<?php echo JLayoutHelper::render('dynamic_get.main_left', $this); ?>
			</div>
			<div class="span6">
				<?php echo JLayoutHelper::render('dynamic_get.main_right', $this); ?>
			</div>
		</div>
		<div class="row-fluid form-horizontal-desktop">
			<div class="span12">
				<?php echo JLayoutHelper::render('dynamic_get.main_fullwidth', $this); ?>
			</div>
		</div>
	<?php echo JHtml::_('bootstrap.endTab'); ?>

	<?php echo JHtml::_('bootstrap.addTab', 'dynamic_getTab', 'tweak', JText::_('COM_COMPONENTBUILDER_DYNAMIC_GET_TWEAK', true)); ?>
		<div class="row-fluid form-horizontal-desktop">
		</div>
		<div class="row-fluid form-horizontal-desktop">
			<div class="span12">
				<?php echo JLayoutHelper::render('dynamic_get.tweak_fullwidth', $this); ?>
			</div>
		</div>
	<?php echo JHtml::_('bootstrap.endTab'); ?>

	<?php echo JHtml::_('bootstrap.addTab', 'dynamic_getTab', 'joint', JText::_('COM_COMPONENTBUILDER_DYNAMIC_GET_JOINT', true)); ?>
		<div class="row-fluid form-horizontal-desktop">
		</div>
		<div class="row-fluid form-horizontal-desktop">
			<div class="span12">
				<?php echo JLayoutHelper::render('dynamic_get.joint_fullwidth', $this); ?>
			</div>
		</div>
	<?php echo JHtml::_('bootstrap.endTab'); ?>

	<?php echo JHtml::_('bootstrap.addTab', 'dynamic_getTab', 'custom_script', JText::_('COM_COMPONENTBUILDER_DYNAMIC_GET_CUSTOM_SCRIPT', true)); ?>
		<div class="row-fluid form-horizontal-desktop">
		</div>
		<div class="row-fluid form-horizontal-desktop">
			<div class="span12">
				<?php echo JLayoutHelper::render('dynamic_get.custom_script_fullwidth', $this); ?>
			</div>
		</div>
	<?php echo JHtml::_('bootstrap.endTab'); ?>

	<?php echo JHtml::_('bootstrap.addTab', 'dynamic_getTab', 'abacus', JText::_('COM_COMPONENTBUILDER_DYNAMIC_GET_ABACUS', true)); ?>
		<div class="row-fluid form-horizontal-desktop">
			<div class="span12">
				<?php echo JLayoutHelper::render('dynamic_get.abacus_left', $this); ?>
			</div>
		</div>
		<div class="row-fluid form-horizontal-desktop">
			<div class="span12">
				<?php echo JLayoutHelper::render('dynamic_get.abacus_fullwidth', $this); ?>
			</div>
		</div>
	<?php echo JHtml::_('bootstrap.endTab'); ?>

	<?php $this->ignore_fieldsets = array('details','metadata','vdmmetadata','accesscontrol'); ?>
	<?php $this->tab_name = 'dynamic_getTab'; ?>
	<?php echo JLayoutHelper::render('joomla.edit.params', $this); ?>

	<?php if ($this->canDo->get('dynamic_get.delete') || $this->canDo->get('core.edit.created_by') || $this->canDo->get('dynamic_get.edit.state') || $this->canDo->get('core.edit.created')) : ?>
	<?php echo JHtml::_('bootstrap.addTab', 'dynamic_getTab', 'publishing', JText::_('COM_COMPONENTBUILDER_DYNAMIC_GET_PUBLISHING', true)); ?>
		<div class="row-fluid form-horizontal-desktop">
			<div class="span6">
				<?php echo JLayoutHelper::render('dynamic_get.publishing', $this); ?>
			</div>
			<div class="span6">
				<?php echo JLayoutHelper::render('dynamic_get.publlshing', $this); ?>
			</div>
		</div>
	<?php echo JHtml::_('bootstrap.endTab'); ?>
	<?php endif; ?>

	<?php if ($this->canDo->get('core.admin')) : ?>
	<?php echo JHtml::_('bootstrap.addTab', 'dynamic_getTab', 'permissions', JText::_('COM_COMPONENTBUILDER_DYNAMIC_GET_PERMISSION', true)); ?>
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
		<input type="hidden" name="task" value="dynamic_get.edit" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
	</div>
</div>

<div class="clearfix"></div>
<?php echo JLayoutHelper::render('dynamic_get.main_under', $this); ?>
</form>
</div>

<script type="text/javascript">

// #jform_gettype listeners for gettype_vvvvvzd function
jQuery('#jform_gettype').on('keyup',function()
{
	var gettype_vvvvvzd = jQuery("#jform_gettype").val();
	vvvvvzd(gettype_vvvvvzd);

});
jQuery('#adminForm').on('change', '#jform_gettype',function (e)
{
	e.preventDefault();
	var gettype_vvvvvzd = jQuery("#jform_gettype").val();
	vvvvvzd(gettype_vvvvvzd);

});

// #jform_main_source listeners for main_source_vvvvvze function
jQuery('#jform_main_source').on('keyup',function()
{
	var main_source_vvvvvze = jQuery("#jform_main_source").val();
	vvvvvze(main_source_vvvvvze);

});
jQuery('#adminForm').on('change', '#jform_main_source',function (e)
{
	e.preventDefault();
	var main_source_vvvvvze = jQuery("#jform_main_source").val();
	vvvvvze(main_source_vvvvvze);

});

// #jform_main_source listeners for main_source_vvvvvzf function
jQuery('#jform_main_source').on('keyup',function()
{
	var main_source_vvvvvzf = jQuery("#jform_main_source").val();
	vvvvvzf(main_source_vvvvvzf);

});
jQuery('#adminForm').on('change', '#jform_main_source',function (e)
{
	e.preventDefault();
	var main_source_vvvvvzf = jQuery("#jform_main_source").val();
	vvvvvzf(main_source_vvvvvzf);

});

// #jform_main_source listeners for main_source_vvvvvzg function
jQuery('#jform_main_source').on('keyup',function()
{
	var main_source_vvvvvzg = jQuery("#jform_main_source").val();
	vvvvvzg(main_source_vvvvvzg);

});
jQuery('#adminForm').on('change', '#jform_main_source',function (e)
{
	e.preventDefault();
	var main_source_vvvvvzg = jQuery("#jform_main_source").val();
	vvvvvzg(main_source_vvvvvzg);

});

// #jform_main_source listeners for main_source_vvvvvzh function
jQuery('#jform_main_source').on('keyup',function()
{
	var main_source_vvvvvzh = jQuery("#jform_main_source").val();
	vvvvvzh(main_source_vvvvvzh);

});
jQuery('#adminForm').on('change', '#jform_main_source',function (e)
{
	e.preventDefault();
	var main_source_vvvvvzh = jQuery("#jform_main_source").val();
	vvvvvzh(main_source_vvvvvzh);

});

// #jform_main_source listeners for main_source_vvvvvzi function
jQuery('#jform_main_source').on('keyup',function()
{
	var main_source_vvvvvzi = jQuery("#jform_main_source").val();
	vvvvvzi(main_source_vvvvvzi);

});
jQuery('#adminForm').on('change', '#jform_main_source',function (e)
{
	e.preventDefault();
	var main_source_vvvvvzi = jQuery("#jform_main_source").val();
	vvvvvzi(main_source_vvvvvzi);

});

// #jform_addcalculation listeners for addcalculation_vvvvvzj function
jQuery('#jform_addcalculation').on('keyup',function()
{
	var addcalculation_vvvvvzj = jQuery("#jform_addcalculation input[type='radio']:checked").val();
	vvvvvzj(addcalculation_vvvvvzj);

});
jQuery('#adminForm').on('change', '#jform_addcalculation',function (e)
{
	e.preventDefault();
	var addcalculation_vvvvvzj = jQuery("#jform_addcalculation input[type='radio']:checked").val();
	vvvvvzj(addcalculation_vvvvvzj);

});

// #jform_addcalculation listeners for addcalculation_vvvvvzk function
jQuery('#jform_addcalculation').on('keyup',function()
{
	var addcalculation_vvvvvzk = jQuery("#jform_addcalculation input[type='radio']:checked").val();
	var gettype_vvvvvzk = jQuery("#jform_gettype").val();
	vvvvvzk(addcalculation_vvvvvzk,gettype_vvvvvzk);

});
jQuery('#adminForm').on('change', '#jform_addcalculation',function (e)
{
	e.preventDefault();
	var addcalculation_vvvvvzk = jQuery("#jform_addcalculation input[type='radio']:checked").val();
	var gettype_vvvvvzk = jQuery("#jform_gettype").val();
	vvvvvzk(addcalculation_vvvvvzk,gettype_vvvvvzk);

});

// #jform_gettype listeners for gettype_vvvvvzk function
jQuery('#jform_gettype').on('keyup',function()
{
	var addcalculation_vvvvvzk = jQuery("#jform_addcalculation input[type='radio']:checked").val();
	var gettype_vvvvvzk = jQuery("#jform_gettype").val();
	vvvvvzk(addcalculation_vvvvvzk,gettype_vvvvvzk);

});
jQuery('#adminForm').on('change', '#jform_gettype',function (e)
{
	e.preventDefault();
	var addcalculation_vvvvvzk = jQuery("#jform_addcalculation input[type='radio']:checked").val();
	var gettype_vvvvvzk = jQuery("#jform_gettype").val();
	vvvvvzk(addcalculation_vvvvvzk,gettype_vvvvvzk);

});

// #jform_addcalculation listeners for addcalculation_vvvvvzl function
jQuery('#jform_addcalculation').on('keyup',function()
{
	var addcalculation_vvvvvzl = jQuery("#jform_addcalculation input[type='radio']:checked").val();
	var gettype_vvvvvzl = jQuery("#jform_gettype").val();
	vvvvvzl(addcalculation_vvvvvzl,gettype_vvvvvzl);

});
jQuery('#adminForm').on('change', '#jform_addcalculation',function (e)
{
	e.preventDefault();
	var addcalculation_vvvvvzl = jQuery("#jform_addcalculation input[type='radio']:checked").val();
	var gettype_vvvvvzl = jQuery("#jform_gettype").val();
	vvvvvzl(addcalculation_vvvvvzl,gettype_vvvvvzl);

});

// #jform_gettype listeners for gettype_vvvvvzl function
jQuery('#jform_gettype').on('keyup',function()
{
	var addcalculation_vvvvvzl = jQuery("#jform_addcalculation input[type='radio']:checked").val();
	var gettype_vvvvvzl = jQuery("#jform_gettype").val();
	vvvvvzl(addcalculation_vvvvvzl,gettype_vvvvvzl);

});
jQuery('#adminForm').on('change', '#jform_gettype',function (e)
{
	e.preventDefault();
	var addcalculation_vvvvvzl = jQuery("#jform_addcalculation input[type='radio']:checked").val();
	var gettype_vvvvvzl = jQuery("#jform_gettype").val();
	vvvvvzl(addcalculation_vvvvvzl,gettype_vvvvvzl);

});

// #jform_main_source listeners for main_source_vvvvvzo function
jQuery('#jform_main_source').on('keyup',function()
{
	var main_source_vvvvvzo = jQuery("#jform_main_source").val();
	vvvvvzo(main_source_vvvvvzo);

});
jQuery('#adminForm').on('change', '#jform_main_source',function (e)
{
	e.preventDefault();
	var main_source_vvvvvzo = jQuery("#jform_main_source").val();
	vvvvvzo(main_source_vvvvvzo);

});

// #jform_main_source listeners for main_source_vvvvvzp function
jQuery('#jform_main_source').on('keyup',function()
{
	var main_source_vvvvvzp = jQuery("#jform_main_source").val();
	vvvvvzp(main_source_vvvvvzp);

});
jQuery('#adminForm').on('change', '#jform_main_source',function (e)
{
	e.preventDefault();
	var main_source_vvvvvzp = jQuery("#jform_main_source").val();
	vvvvvzp(main_source_vvvvvzp);

});

// #jform_add_php_before_getitem listeners for add_php_before_getitem_vvvvvzq function
jQuery('#jform_add_php_before_getitem').on('keyup',function()
{
	var add_php_before_getitem_vvvvvzq = jQuery("#jform_add_php_before_getitem input[type='radio']:checked").val();
	var gettype_vvvvvzq = jQuery("#jform_gettype").val();
	vvvvvzq(add_php_before_getitem_vvvvvzq,gettype_vvvvvzq);

});
jQuery('#adminForm').on('change', '#jform_add_php_before_getitem',function (e)
{
	e.preventDefault();
	var add_php_before_getitem_vvvvvzq = jQuery("#jform_add_php_before_getitem input[type='radio']:checked").val();
	var gettype_vvvvvzq = jQuery("#jform_gettype").val();
	vvvvvzq(add_php_before_getitem_vvvvvzq,gettype_vvvvvzq);

});

// #jform_gettype listeners for gettype_vvvvvzq function
jQuery('#jform_gettype').on('keyup',function()
{
	var add_php_before_getitem_vvvvvzq = jQuery("#jform_add_php_before_getitem input[type='radio']:checked").val();
	var gettype_vvvvvzq = jQuery("#jform_gettype").val();
	vvvvvzq(add_php_before_getitem_vvvvvzq,gettype_vvvvvzq);

});
jQuery('#adminForm').on('change', '#jform_gettype',function (e)
{
	e.preventDefault();
	var add_php_before_getitem_vvvvvzq = jQuery("#jform_add_php_before_getitem input[type='radio']:checked").val();
	var gettype_vvvvvzq = jQuery("#jform_gettype").val();
	vvvvvzq(add_php_before_getitem_vvvvvzq,gettype_vvvvvzq);

});

// #jform_add_php_after_getitem listeners for add_php_after_getitem_vvvvvzr function
jQuery('#jform_add_php_after_getitem').on('keyup',function()
{
	var add_php_after_getitem_vvvvvzr = jQuery("#jform_add_php_after_getitem input[type='radio']:checked").val();
	var gettype_vvvvvzr = jQuery("#jform_gettype").val();
	vvvvvzr(add_php_after_getitem_vvvvvzr,gettype_vvvvvzr);

});
jQuery('#adminForm').on('change', '#jform_add_php_after_getitem',function (e)
{
	e.preventDefault();
	var add_php_after_getitem_vvvvvzr = jQuery("#jform_add_php_after_getitem input[type='radio']:checked").val();
	var gettype_vvvvvzr = jQuery("#jform_gettype").val();
	vvvvvzr(add_php_after_getitem_vvvvvzr,gettype_vvvvvzr);

});

// #jform_gettype listeners for gettype_vvvvvzr function
jQuery('#jform_gettype').on('keyup',function()
{
	var add_php_after_getitem_vvvvvzr = jQuery("#jform_add_php_after_getitem input[type='radio']:checked").val();
	var gettype_vvvvvzr = jQuery("#jform_gettype").val();
	vvvvvzr(add_php_after_getitem_vvvvvzr,gettype_vvvvvzr);

});
jQuery('#adminForm').on('change', '#jform_gettype',function (e)
{
	e.preventDefault();
	var add_php_after_getitem_vvvvvzr = jQuery("#jform_add_php_after_getitem input[type='radio']:checked").val();
	var gettype_vvvvvzr = jQuery("#jform_gettype").val();
	vvvvvzr(add_php_after_getitem_vvvvvzr,gettype_vvvvvzr);

});

// #jform_gettype listeners for gettype_vvvvvzt function
jQuery('#jform_gettype').on('keyup',function()
{
	var gettype_vvvvvzt = jQuery("#jform_gettype").val();
	vvvvvzt(gettype_vvvvvzt);

});
jQuery('#adminForm').on('change', '#jform_gettype',function (e)
{
	e.preventDefault();
	var gettype_vvvvvzt = jQuery("#jform_gettype").val();
	vvvvvzt(gettype_vvvvvzt);

});

// #jform_add_php_getlistquery listeners for add_php_getlistquery_vvvvvzu function
jQuery('#jform_add_php_getlistquery').on('keyup',function()
{
	var add_php_getlistquery_vvvvvzu = jQuery("#jform_add_php_getlistquery input[type='radio']:checked").val();
	var gettype_vvvvvzu = jQuery("#jform_gettype").val();
	vvvvvzu(add_php_getlistquery_vvvvvzu,gettype_vvvvvzu);

});
jQuery('#adminForm').on('change', '#jform_add_php_getlistquery',function (e)
{
	e.preventDefault();
	var add_php_getlistquery_vvvvvzu = jQuery("#jform_add_php_getlistquery input[type='radio']:checked").val();
	var gettype_vvvvvzu = jQuery("#jform_gettype").val();
	vvvvvzu(add_php_getlistquery_vvvvvzu,gettype_vvvvvzu);

});

// #jform_gettype listeners for gettype_vvvvvzu function
jQuery('#jform_gettype').on('keyup',function()
{
	var add_php_getlistquery_vvvvvzu = jQuery("#jform_add_php_getlistquery input[type='radio']:checked").val();
	var gettype_vvvvvzu = jQuery("#jform_gettype").val();
	vvvvvzu(add_php_getlistquery_vvvvvzu,gettype_vvvvvzu);

});
jQuery('#adminForm').on('change', '#jform_gettype',function (e)
{
	e.preventDefault();
	var add_php_getlistquery_vvvvvzu = jQuery("#jform_add_php_getlistquery input[type='radio']:checked").val();
	var gettype_vvvvvzu = jQuery("#jform_gettype").val();
	vvvvvzu(add_php_getlistquery_vvvvvzu,gettype_vvvvvzu);

});

// #jform_add_php_before_getitems listeners for add_php_before_getitems_vvvvvzv function
jQuery('#jform_add_php_before_getitems').on('keyup',function()
{
	var add_php_before_getitems_vvvvvzv = jQuery("#jform_add_php_before_getitems input[type='radio']:checked").val();
	var gettype_vvvvvzv = jQuery("#jform_gettype").val();
	vvvvvzv(add_php_before_getitems_vvvvvzv,gettype_vvvvvzv);

});
jQuery('#adminForm').on('change', '#jform_add_php_before_getitems',function (e)
{
	e.preventDefault();
	var add_php_before_getitems_vvvvvzv = jQuery("#jform_add_php_before_getitems input[type='radio']:checked").val();
	var gettype_vvvvvzv = jQuery("#jform_gettype").val();
	vvvvvzv(add_php_before_getitems_vvvvvzv,gettype_vvvvvzv);

});

// #jform_gettype listeners for gettype_vvvvvzv function
jQuery('#jform_gettype').on('keyup',function()
{
	var add_php_before_getitems_vvvvvzv = jQuery("#jform_add_php_before_getitems input[type='radio']:checked").val();
	var gettype_vvvvvzv = jQuery("#jform_gettype").val();
	vvvvvzv(add_php_before_getitems_vvvvvzv,gettype_vvvvvzv);

});
jQuery('#adminForm').on('change', '#jform_gettype',function (e)
{
	e.preventDefault();
	var add_php_before_getitems_vvvvvzv = jQuery("#jform_add_php_before_getitems input[type='radio']:checked").val();
	var gettype_vvvvvzv = jQuery("#jform_gettype").val();
	vvvvvzv(add_php_before_getitems_vvvvvzv,gettype_vvvvvzv);

});

// #jform_add_php_after_getitems listeners for add_php_after_getitems_vvvvvzw function
jQuery('#jform_add_php_after_getitems').on('keyup',function()
{
	var add_php_after_getitems_vvvvvzw = jQuery("#jform_add_php_after_getitems input[type='radio']:checked").val();
	var gettype_vvvvvzw = jQuery("#jform_gettype").val();
	vvvvvzw(add_php_after_getitems_vvvvvzw,gettype_vvvvvzw);

});
jQuery('#adminForm').on('change', '#jform_add_php_after_getitems',function (e)
{
	e.preventDefault();
	var add_php_after_getitems_vvvvvzw = jQuery("#jform_add_php_after_getitems input[type='radio']:checked").val();
	var gettype_vvvvvzw = jQuery("#jform_gettype").val();
	vvvvvzw(add_php_after_getitems_vvvvvzw,gettype_vvvvvzw);

});

// #jform_gettype listeners for gettype_vvvvvzw function
jQuery('#jform_gettype').on('keyup',function()
{
	var add_php_after_getitems_vvvvvzw = jQuery("#jform_add_php_after_getitems input[type='radio']:checked").val();
	var gettype_vvvvvzw = jQuery("#jform_gettype").val();
	vvvvvzw(add_php_after_getitems_vvvvvzw,gettype_vvvvvzw);

});
jQuery('#adminForm').on('change', '#jform_gettype',function (e)
{
	e.preventDefault();
	var add_php_after_getitems_vvvvvzw = jQuery("#jform_add_php_after_getitems input[type='radio']:checked").val();
	var gettype_vvvvvzw = jQuery("#jform_gettype").val();
	vvvvvzw(add_php_after_getitems_vvvvvzw,gettype_vvvvvzw);

});

// #jform_gettype listeners for gettype_vvvvvzy function
jQuery('#jform_gettype').on('keyup',function()
{
	var gettype_vvvvvzy = jQuery("#jform_gettype").val();
	vvvvvzy(gettype_vvvvvzy);

});
jQuery('#adminForm').on('change', '#jform_gettype',function (e)
{
	e.preventDefault();
	var gettype_vvvvvzy = jQuery("#jform_gettype").val();
	vvvvvzy(gettype_vvvvvzy);

});

// #jform_gettype listeners for gettype_vvvvvzz function
jQuery('#jform_gettype').on('keyup',function()
{
	var gettype_vvvvvzz = jQuery("#jform_gettype").val();
	vvvvvzz(gettype_vvvvvzz);

});
jQuery('#adminForm').on('change', '#jform_gettype',function (e)
{
	e.preventDefault();
	var gettype_vvvvvzz = jQuery("#jform_gettype").val();
	vvvvvzz(gettype_vvvvvzz);

});

// #jform_gettype listeners for gettype_vvvvwaa function
jQuery('#jform_gettype').on('keyup',function()
{
	var gettype_vvvvwaa = jQuery("#jform_gettype").val();
	vvvvwaa(gettype_vvvvwaa);

});
jQuery('#adminForm').on('change', '#jform_gettype',function (e)
{
	e.preventDefault();
	var gettype_vvvvwaa = jQuery("#jform_gettype").val();
	vvvvwaa(gettype_vvvvwaa);

});

// #jform_gettype listeners for gettype_vvvvwab function
jQuery('#jform_gettype').on('keyup',function()
{
	var gettype_vvvvwab = jQuery("#jform_gettype").val();
	var add_php_router_parse_vvvvwab = jQuery("#jform_add_php_router_parse input[type='radio']:checked").val();
	vvvvwab(gettype_vvvvwab,add_php_router_parse_vvvvwab);

});
jQuery('#adminForm').on('change', '#jform_gettype',function (e)
{
	e.preventDefault();
	var gettype_vvvvwab = jQuery("#jform_gettype").val();
	var add_php_router_parse_vvvvwab = jQuery("#jform_add_php_router_parse input[type='radio']:checked").val();
	vvvvwab(gettype_vvvvwab,add_php_router_parse_vvvvwab);

});

// #jform_add_php_router_parse listeners for add_php_router_parse_vvvvwab function
jQuery('#jform_add_php_router_parse').on('keyup',function()
{
	var gettype_vvvvwab = jQuery("#jform_gettype").val();
	var add_php_router_parse_vvvvwab = jQuery("#jform_add_php_router_parse input[type='radio']:checked").val();
	vvvvwab(gettype_vvvvwab,add_php_router_parse_vvvvwab);

});
jQuery('#adminForm').on('change', '#jform_add_php_router_parse',function (e)
{
	e.preventDefault();
	var gettype_vvvvwab = jQuery("#jform_gettype").val();
	var add_php_router_parse_vvvvwab = jQuery("#jform_add_php_router_parse input[type='radio']:checked").val();
	vvvvwab(gettype_vvvvwab,add_php_router_parse_vvvvwab);

});

// #jform_gettype listeners for gettype_vvvvwad function
jQuery('#jform_gettype').on('keyup',function()
{
	var gettype_vvvvwad = jQuery("#jform_gettype").val();
	vvvvwad(gettype_vvvvwad);

});
jQuery('#adminForm').on('change', '#jform_gettype',function (e)
{
	e.preventDefault();
	var gettype_vvvvwad = jQuery("#jform_gettype").val();
	vvvvwad(gettype_vvvvwad);

});



<?php $fieldNrs = range(0,50,1); ?>
<?php $fieldNames = array('db' => 'Db','view' => 'View'); ?>
// for the vlaues already set
jQuery(document).ready(function(){
<?php foreach($fieldNames as $fieldName => $funcName): ?>
 	<?php foreach($fieldNrs as $fieldNr): ?>
		updateSubItems('<?php echo $fieldName ?>', <?php echo $fieldNr ?>, '_', '_');
	<?php endforeach; ?>
<?php endforeach; ?>
});
// for the values the will still be set
jQuery(document).ready(function(){
	jQuery(document).on('subform-row-add', function(event, row){
		var groupName = jQuery(row).data('group');
		var fieldName = groupName.replace('join_', '').replace('_table', '').replace(/([0-9])/g, '');
		var fieldNr = groupName.replace(/([A-z_])/g, '');
		updateSubItems(fieldName, fieldNr, '_', '_');
	});

});

<?php foreach($fieldNames as $fieldName => $funcName): ?>jQuery('#adminForm').on('change', '#jform_<?php echo $fieldName ?>_table_main',function (e) {
	// get options
	var value_<?php echo $fieldName ?> = jQuery("#jform_<?php echo $fieldName ?>_table_main option:selected").val();
	get<?php echo $funcName; ?>TableColumns(value_<?php echo $fieldName ?>, 'a', '<?php echo $fieldName ?>', 3, true, '', '');
});
<?php endforeach; ?>

// #jform_add_php_router_parse listeners
jQuery('#jform_add_php_router_parse').on('change',function() {
	var valueSwitch = jQuery("#jform_add_php_router_parse input[type='radio']:checked").val();
	getDynamicScripts(valueSwitch);
});

jQuery('#adminForm').on('change', '#jform_select_all',function (e)
{
	e.preventDefault();
	// get the selected value
	var select_all =  jQuery("#jform_select_all input[type='radio']:checked").val();
	setSelectAll(select_all);

});


// nice little dot trick :)
jQuery(document).ready( function($) {
  var x=0;
  setInterval(function() {
	var dots = "";
	x++;
	for (var y=0; y < x%8; y++) {
		dots+=".";
	}
	$(".loading-dots").text(dots);
  } , 500);
}); 
</script>
