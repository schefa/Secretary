<?php

// No direct access
defined('JPATH_BASE') or die;

\Joomla\CMS\Form\FormHelper::loadFieldClass('list');

class JFormFieldSubjects extends \Joomla\CMS\Form\Field\ListField
{

	protected $type = 'subjects';

	public function getOptions()
	{
		$html = array();

		$user = \Secretary\Joomla::getUser();
		$db = \Secretary\Database::getDBO();
		$query = $db->getQuery(true);

		$query->select("id,CONCAT( lastname ,', ', firstname, ' aus ', location, ' (', street, ')') AS username")
			->from($db->qn('#__secretary_subjects'))
			->order('lastname ASC');

		$db->setQuery($query);
		$items = $db->loadObjectList();

		foreach ($items as $subject)
		{
			if (
				$user->authorise('core.show', 'com_secretary.subject.' . $subject->id)
				|| $user->authorise('core.show.other', 'com_secretary.subject.' . $subject->id)
			)
			{
				$html[] = \Joomla\CMS\HTML\HTMLHelper::_('select.option', $subject->id, $subject->username);
			}
		}

		return $html;
	}

	protected function getInput()
	{

		// Load the javascript
		\Joomla\CMS\HTML\HTMLHelper::_('bootstrap.tooltip');

		\Joomla\CMS\Factory::getLanguage()->load('com_secretary', JPATH_ADMINISTRATOR);
		$html = array();

		// Note: class='required' for client side validation.
		$class = '';
		
        if ($this->required)
		{
			$class = ' class="required modal-value"';
		}

		// The active contact id field.
		if (0 == (int) $this->value)
		{
			$value = '';
		}
        else
		{
			$value = (int) $this->value;
		}

		// Get the title of the linked chart
		if ((int) $this->value > 0)
		{
			$fieldStr = (Secretary\Database::getDbType() == 'postgresql') ? "firstname || ' ' || lastname" : 'CONCAT_WS(" ",firstname,lastname)';

			$db = \Secretary\Database::getDBO();
			$query = $db->getQuery(true)
				->select($fieldStr)
				->from($db->qn('#__secretary_subjects'))
				->where($db->qn('id') . '=' . (int) $this->value);
			$db->setQuery($query);

			try
			{
				$title = $db->loadResult();
			}
            catch (RuntimeException $e)
			{
				\Joomla\CMS\Factory::getApplication()->enqueueMessage($e->getMessage(), 'warning');
			}
		}

		if (empty($title))
		{
			$title = \Joomla\CMS\Language\Text::_('COM_SECRETARY_SELECT_A_CONTACT');
		}
        else
		{
			// $title came straight from the DB, still in cleaner()'s stored, HTML-entity-encoded
			// form (e.g. "M&uuml;ller") - decode back to real characters before re-escaping for
			// this specific HTML-attribute context, otherwise the entity's own "&" gets escaped
			// again into "&amp;" and the attribute ends up literally reading "M&amp;uuml;ller".
			$title = \Secretary\Utilities::cleaner($title, true);
		}

		$title = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');

		// Build the script.
		$script = array();

		// Select button script
		$script[] = '	function jSelectUser( name, street, zip, location, phone, email, gender, id ) {';
		$script[] = '		document.getElementById("subject_name").value = name;';
		$script[] = '		document.getElementById("subject_id").value = id;';
		$script[] = '		SqueezeBox.close();return false;';
		$script[] = '	}';

		// Add the script to the document head.
		\Joomla\CMS\Factory::getDocument()->addScriptDeclaration(implode("\n", $script));

		$link = 'index.php?option=com_secretary&amp;view=subjects&amp;layout=modal&amp;tmpl=component&amp;' . \Joomla\CMS\Session\Session::getFormToken() . '=1';

		if (isset($this->element['language']))
		{
			$link .= '&amp;forcedLanguage=' . $this->element['language'];
		}

		if (isset($this->element['email']))
		{
			$link .= '&amp;email=1';
		}

		// The current contact display field.
		$html[] = '<span class="input-append">';
		$html[] = '<input type="text" class="input-medium" id="subject_name" value="' . $title . '" disabled="disabled" size="35" />';
		$html[] = '<input type="hidden" id="subject_id"' . $class . ' name="' . $this->name . '" value="' . $value . '" />';

		$html[] = '<a class="modal btn btn-default hasTooltip" title="' . \Joomla\CMS\HTML\HTMLHelper::tooltipText('COM_SECRETARY_CONTACTS_MODAL_DIALOG') . '"  href="' . $link . '" rel="{handler: \'iframe\', size: {x: 800, y: 450}}">' . \Joomla\CMS\Language\Text::_('COM_SECRETARY_SELECT') . '</a>';

		// Edit contact
		$html[] = '<a class="btn hasTooltip' . ($value ? '' : ' hidden') . '" target="_blank"'
			. ' href="index.php?option=com_secretary&layout=modal&tmpl=component&task=subject.edit&id=' . $value . '"'
			. ' title="' . \Joomla\CMS\HTML\HTMLHelper::tooltipText('COM_SECRETARY_EDIT') . '" >'
			. '<span class="icon-edit"></span>' . \Joomla\CMS\Language\Text::_('JACTION_EDIT') . '</a>';

		// Clear contact
		$html[] = '<button id="' . $this->id . '_clear" class="btn' . ($value ? '' : ' hidden') . '"'
			. " onclick=\"return jSelectUser('','','','','','','','')\">"
			. '<span class="icon-remove"></span>' . \Joomla\CMS\Language\Text::_('COM_SECRETARY_CLEAR')
			. '</button>';

		// The current contact display field.
		$html[] = \Joomla\CMS\HTML\HTMLHelper::_(
			'bootstrap.renderModal',
			$this->id,
			array(
				'url' => $link . '&amp;' . \Joomla\CMS\Session\Session::getFormToken() . '=1"',
				'title' => \Joomla\CMS\Language\Text::_('COM_SECRETARY_CHANGE_CONTACT'),
				'width' => '800px',
				'height' => '300px',
				'footer' => '<button class="btn" data-bs-dismiss="modal" aria-hidden="true">' . \Joomla\CMS\Language\Text::_("JLIB_HTML_BEHAVIOR_CLOSE") . '</button>'
			)
		);
		$html[] = '</span>';

		return implode("\n", $html);
	}
}