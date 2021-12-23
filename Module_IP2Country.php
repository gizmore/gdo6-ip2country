<?php
namespace GDO\IP2Country;

use GDO\Core\GDO_Module;
use GDO\DB\GDT_Checkbox;
use GDO\Register\GDO_UserActivation;
use GDO\UI\GDT_Link;
use GDO\User\GDO_User;
/**
 * IP2Country detection.
 * 
 * @author gizmore
 * @since 2.0
 * @version 5.0
 *
 */
final class Module_IP2Country extends GDO_Module
{
	public $module_priority = 80; # Install and load late :)
	
	##############
	### Module ###
	##############
	public function getClasses() { return ['GDO\IP2Country\GDO_IPCountry']; }
	public function onLoadLanguage() { $this->loadLanguage('lang/ip2country'); }
	public function href_administrate_module() { return href('IP2Country', 'InstallIP2C'); }
	
	##############
	### Config ###
	##############
	public function getConfig()
	{
		return array(
			GDT_Checkbox::make('autodetect_signup')->initial('1'),
			GDT_Link::make('detect_users')->href(href('IP2Country', 'DetectUsers')),
		);
	}
	public function cfgAutodetectSignup() { return $this->getConfigValue('autodetect_signup'); }
	
	#############
	### Hooks ###
	#############
	public function hookUserActivated(GDO_User $user, GDO_UserActivation $activation=null)
	{
		if ($this->cfgAutodetectSignup())
		{
			$this->autodetectForUser($user);
		}
	}
	private static function autodetectForUser(GDO_User $user)
	{
		if (!$user->getCountryISO())
		{
		    $user->saveVar('user_country', GDO_IPCountry::detectISO($user->getRegisterIP()));
		}
	}
}
