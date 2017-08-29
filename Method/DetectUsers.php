<?php
namespace GDO\IP2Country\Method;

use GDO\Form\GDT_AntiCSRF;
use GDO\Form\GDT_Form;
use GDO\Form\GDT_Submit;
use GDO\Form\MethodForm;
use GDO\IP2Country\GDO_IPCountry;
use GDO\User\GDO_User;

final class DetectUsers extends MethodForm
{
	public function createForm(GDT_Form $form)
	{
		$form->addField(GDT_AntiCSRF::make());
		$form->addField(GDT_Submit::make());
	}

	public function formValidated(GDT_Form $form)
	{
		$table = GDO_User::table();
		$result = $table->select()->where('user_country IS NULL AND user_register_ip IS NOT NULL')->exec();
		$rows = 0;
		while ($user = $table->fetch($result))
		{
			if ($country = GDO_IPCountry::detect($user->getRegisterIP()))
			{
				$user->saveValue('user_country', $country);
				$rows++;
			}
		}
		return $this->message('msg_ip2country_detection', [$rows]);
	}
}
