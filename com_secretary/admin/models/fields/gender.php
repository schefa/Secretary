<?php

// No direct access
defined('JPATH_BASE') or die;

\Joomla\CMS\Form\FormHelper::loadFieldClass('list');

class JFormFieldGender extends \Joomla\CMS\Form\Field\ListField
{

	protected $type = 'gender';
	private $_list = array();

	public function getOptions($key = false)
	{

		$options = array();

		if (empty($this->_list))
		{
			$db = \Secretary\Database::getDBO();
			$query = $db->getQuery(true);
			$query->select('*')
				->from($db->qn('#__secretary_fields'))
				->where($db->qn('hard') . '=' . $db->quote('anrede'))
				->where($db->qn('extension') . "=" . $db->quote('system'))
				->order('id');
			$db->setQuery($query);
			$this->_list = $db->loadObject();
		}

		$object = $this->_list;
		
        if ($fieldvalues = json_decode($object->values, true))
		{
			foreach ($fieldvalues as $pos => $val)
			{
				$fieldvalues[$pos] = \Joomla\CMS\Language\Text::_($val);
				
                if (strlen($val) >= 1)
				{
					$val = \Joomla\CMS\Language\Text::_($val);
				}
                else
				{
					$val = \Joomla\CMS\Language\Text::_('COM_SECRETARY_NONE');
				}
				$options[$pos] = \Joomla\CMS\HTML\HTMLHelper::_('select.option', $pos, $val);
            }
			
            if ($key === false)
			{
				return $options;
            }
            elseif (isset($options[$key]))
			{
                return \Joomla\CMS\Language\Text::_($fieldvalues[$key]);
            }
		}
		
        return false;
	}

	public function getList($default, $name = 'jform[subject][]', $id = 'jform_subject_gender')
	{
		$options = $this->getOptions();
		
        if ($options)
		{
			return '<select name="' . $name . '" id="' . $id . '" class="form-control inputbox">' . \Joomla\CMS\HTML\HTMLHelper::_('select.options', $options, 'value', 'text', $default) . '</select>';
		}
		
        return false;
	}
}