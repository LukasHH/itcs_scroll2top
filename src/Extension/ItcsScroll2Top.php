<?php
/**
* Joomla.Plugin - itcs Scroll to Top Button
* ------------------------------------------------------------------------
* @package     itcs Scroll to Top Button
* @author      it-conserv.de
* @copyright   2023 it-conserv.de
* @license     GNU/GPLv3 <http://www.gnu.org/licenses/gpl-3.0.de.html>
* @link        https://it-conserv.de
* ------------------------------------------------------------------------
*/

namespace ITCS\Plugin\System\ItcsScroll2Top\Extension;

// no direct access
defined('_JEXEC') or die;

use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\HTML\HTMLHelper;

/**
 * plg_itcs_scroll2top
 */
final class ItcsScroll2Top extends CMSPlugin
{

	/**
	 * Application object
	 *
	 * @var    \Joomla\CMS\Application\CMSApplication
	 * @since  4.0.0
	 */
	protected $app;

	// set the size of button
	protected $size;

	// set the color of button
	protected $color;

	// set the icon of button
	protected $icon;

	// set the image of button
	protected $img;

    /**
     * Method to catch the onAfterDispatch event.
     *
     * @return  void
     *
     * @since   4.0
     */
	public function onAfterDispatch()	
	{

		//check client
		if ( ! $this->app->isClient('site')) return;
		
		// get document
		$document = $this->app->getDocument();

		if (!($document instanceof \Joomla\CMS\Document\HtmlDocument))
		{
		   return;
		}		

		//size
		$this->size = $this->params->get('size','medium');

		//color
		$cust_color = $this->params->get('s2t_color');
		$this->color = $this->params->get('color','blue');
		
		$bg_color = $this->params->get('bg_color','');
		$bg_color = ($bg_color == '') ? 'transparent' : $bg_color;

		//icon
		$this->icon = $this->params->get('icon','ion-chevron-up');
		//$this->img = $this->params->get('s2t_image','');

		// image
		$img = $this->params->get('s2t_image','');

		if( !empty( $img ) )
		{
			$image_info = HTMLHelper::_('cleanImageURL', $img);
			$this->img	= $image_info->url;
		}
		else{
			$this->img = '';
		}




		// load CSS
		$wa = $document->getWebAssetManager();
		$wa->getRegistry()->addRegistryFile('media/plg_system_itcs_scroll2top/joomla.asset.json');
		
		$wa->useStyle('plg_system_itcs_scroll2top.scroll2top')
			->useScript('plg_system_itcs_scroll2top.scroll2top');

		if (stripos($this->icon,"ion") !== false & $this->img == ''){
			$wa->useStyle('plg_system_itcs_scroll2top.ion');
		}
		if (stripos($this->icon,"fa-") !== false & $this->img == ''){
			$wa->useStyle('plg_system_itcs_scroll2top.fa');
		}

		// custom Styles
		// margins
		$mr	=	$this->params->get('s2t_right', 20);
		$mb	=	$this->params->get('s2t_bottom', 20);

		// add custom style
		$wa->addInlineStyle('
		.snip1452.custom:hover,.scrollToTop.snip1452.custom:hover [class^="fa-"]::before,.scrollToTop.snip1452.custom:hover [class*="fa-"]::before{color: '.$cust_color.';}
		.snip1452.custom:hover:after{border-color: '.$cust_color.';}
		.scrollToTop{right: '.$mr.'px;bottom: '.$mb.'px;}
		.scrollToTop.snip1452::after{background-color: ' . $bg_color . ';}		
		');
	}
	
	/**
	 * Do something onAfterRender
	 */
	public function onAfterRender()
	{

		//check client
		if ( ! $this->app->isClient('site')) return;

		$body = $this->app->getBody();
		
		$Scroll2top = "\n";
		
		// create Scroll2Top Button
		$Scroll2top .= '<div id="scroll2top" class="scrollToTop snip1452 '.$this->size.' '.$this->color.'" data-scroll="top">';
			if ( $this->img !='' ){
				$Scroll2top .= '<img src="' . $this->img . '" alt="top" />';
			}
			else{
				$Scroll2top .= '<i class="' . $this->icon . '"></i>';
			}
		$Scroll2top .= '</div>'."\n";

		$pos = strrpos($body, "</body>");

		if($pos > 0)
		{
			$body = substr($body, 0, $pos)."\n".'<!-- Scroll to Top -->'.$Scroll2top.'<!-- End Scroll to Top -->'."\n".substr($body, $pos);
			$this->app->setBody($body);
		}

		return true;
	
	}

}
