<?php
/**
 * @version		$Id$
 * @category	Koowa
 * @package		Koowa_Template
 * @subpackage	Helper
 * @copyright	Copyright (C) 2007 - 2009 Johan Janssens and Mathias Verraes. All rights reserved.
 * @license		GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 * @link     	http://www.koowa.org
 */

/**
 * Template Grid Helper
 *
 * @author		Mathias Verraes <mathias@koowa.org>
 * @category	Koowa
 * @package		Koowa_Template
 * @subpackage	Helper
 */
class KTemplateHelperGrid extends KObject
{

	/**
	 * Shows a true/false graphics
	 *
	 * @param	bool	Value
	 * @param 	string	Image for true
	 * @param 	string	Image for false
	 * @param 	string 	Text for true
	 * @param 	string	Text for false
	 * @return 	string	Html img
	 */
	public function boolean( $bool, $true_img = null, $false_img = null, $true_text = null, $false_text = null)
	{
		$true_img 	= $true_img 	? $true_img 	: 'tick.png';
		$false_img 	= $false_img	? $false_img	: 'publish_x.png';
		$true_text 	= $true_text 	? $true_text 	: 'Yes';
		$false_text = $false_text 	? $false_text 	: 'No';
		
		return '<img src="images/'. ($bool ? $true_img : $false_img) .'" border="0" alt="'. JText::_($bool ? $true_text : $false_text) .'" />';
	}
	
	/**
	 * @param	string	The link title
	 * @param	string	The order field for the column
	 * @param	string	The current direction
	 * @param	string	The selected ordering
	 */
	public function sort( $title, $order, $direction = 'asc', $selected = 0)
	{
		//Load koowa javascript
		KTemplateDefault::loadHelper('script', 'koowa.js', Koowa::getURL('js'));
		
		$direction	= strtolower( $direction );
		$images		= array( 'sort_asc.png', 'sort_desc.png' );
		$index		= intval( $direction == 'desc' );
		$direction	= ($direction == 'desc') ? 'asc' : 'desc';

		$html = '<a href="javascript:Koowa.Table.sorting(\''.$order.'\',\''.$direction.'\');" title="'.JText::_( 'Click to sort this column' ).'">';
		$html .= JText::_( $title );
		if ($order == $selected ) {
			$html .= KTemplateDefault::loadHelper('image.template',  $images[$index], '/images/', NULL, NULL);
		}
		$html .= '</a>';
		return $html;
	}

	/**
	* @param int The row index
	* @param int The record id
	* @param boolean
	* @param string The name of the form element
	* @return string
	*/
	public function id( $rowNum, $recId, $checkedOut = false, $name = 'cid' )
	{
		if ( $checkedOut ) {
			return '';
		} else {
			return '<input type="checkbox" id="cb'.$rowNum.'" name="'.$name.'[]" value="'.$recId.'" onclick="isChecked(this.checked);" />';
		}
	}

	public function access( $row, $i, $archived = NULL )
	{
		if ( !$row->access )  {
			$color_access = 'style="color: green;"';
			$action_access = 'accessregistered';
		} else if ( $row->access == 1 ) {
			$color_access = 'style="color: red;"';
			$action_access = 'accessspecial';
		} else {
			$color_access = 'style="color: black;"';
			$action_access = 'accesspublic';
		}

		if ($archived == -1)
		{
			$href = JText::_( $row->groupname );
		}
		else
		{
			$href = '
			<a href="javascript:void(0);" onclick="return listItemTask(\'cb'. $i .'\',\''. $action_access .'\')" '. $color_access .'>
			'. JText::_( $row->groupname ) .'</a>'
			;
		}

		return $href;
	}

	public function checkedOut( $row, $i, $identifier = 'id' )
	{
		$user   = KFactory::get('lib.joomla.user');
		$userid = $user->get('id');

		$result = false;
		if(is_a($row, 'JTable')) {
			$result = $row->isCheckedOut($userid);
		} else {
			$result = JTable::isCheckedOut($userid, $row->checked_out);
		}

		$checked = '';
		if ( $result ) {
			$checked = self::_checkedOut( $row );
		} else {
			$checked = KTemplateDefault::loadHelper('grid.id', $i, $row->$identifier );
		}

		return $checked;
	}

	public function published( $row, $i, $imgY = 'tick.png', $imgX = 'publish_x.png', $prefix='' )
	{
		$img 	= $row->published ? $imgY : $imgX;
		$action	= $row->published ? 'unpublish' : 'publish';
		$alt 	= $row->published ? JText::_( 'Published' ) : JText::_( 'Unpublished' );
		$text 	= $row->published ? JText::_( 'Unpublish Item' ) : JText::_( 'Publish item' );

		$href = '
		<a href="javascript:void(0);" onclick="return listItemTask(\'cb'. $i .'\',\''. $prefix.$action .'\')" title="'. $text .'">
		<img src="images/'. $img .'" border="0" alt="'. $alt .'" /></a>'
		;

		return $href;
	}
	
	public function enable( $enable, $i, $imgY = 'tick.png', $imgX = 'publish_x.png', $prefix = '' )
	{
		$img 	= $enable ? $imgY : $imgX;
		$action	= $enable ? 'disable' : 'enable';
		$alt 	= $enable ? JText::_( 'Enabled' ) : JText::_( 'Disabled' );
		$text 	= $enable ? JText::_( 'Disable Item' ) : JText::_( 'Enable Item' );

		$href = '
		<a href="javascript:void(0);" onclick="return listItemTask(\'cb'. $i .'\',\''. $prefix.$action .'\')" title="'. $text .'">
		<img src="images/'. $img .'" border="0" alt="'. $alt .'" />
		</a>'
		;

		return $href;
	}

	public function order($row_id)
	{
		//Load koowa javascript
		KTemplateDefault::loadHelper('script', 'koowa.js', Koowa::getURL('js'));
		
		$up   = Koowa::getURL('images').'/arrow_up.png';
		$down = Koowa::getURL('images').'/arrow_down.png';

		$result =
			 '<a href="javascript:Koowa.Grid.order('.$row_id.', -1)" >'
			.'<img src="'.$up.'" border="0" alt="'.JText::_('Move up').'" />'
			.'</a>'
			.'<a href="javascript:Koowa.Grid.order('.$row_id.', 1)" >'
			.'<img src="'.$down.'" border="0" alt="'.JText::_('Move down').'" />'
			.'</a>';
			
		return $result;
	}

	protected function _checkedOut( &$row, $overlib = 1 )
	{
		$hover = '';
		if ( $overlib )
		{
			$text = addslashes(htmlspecialchars($row->editor));

			$date 	= KTemplateDefault::loadHelper('date',  $row->checked_out_time, '%A, %d %B %Y' );
			$time	= KTemplateDefault::loadHelper('date',  $row->checked_out_time, '%H:%M' );

			$hover = '<span class="editlinktip hasTip" title="'. JText::_( 'Checked Out' ) .'::'. $text .'<br />'. $date .'<br />'. $time .'">';
		}
		$checked = $hover .'<img src="images/checked_out.png"/></span>';

		return $checked;
	}
}
