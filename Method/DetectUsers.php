<?php
namespace GDO\IP2Country\Method;

use GDO\Form\GDO_AntiCSRF;
use GDO\Form\GDO_Form;
use GDO\Form\GDO_Submit;
use GDO\Form\MethodForm;
use GDO\IP2Country\IPCountry;
use GDO\User\User;

final class DetectUsers extends MethodForm
{
	public function createForm(GDO_Form $form)
	{
		$form->addField(GDO_AntiCSRF::make());
		$form->addField(GDO_Submit::make());
	}

	public function formValidated(GDO_Form $form)
	{
		$table = User::table();
		$result = $table->select()->where('user_country IS NULL AND user_register_ip IS NOT NULL')->exec();
		$rows = 0;
		while ($user = $table->fetch($result))
		{
			if ($country = IPCountry::detect($user->getRegisterIP()))
			{
				$user->saveValue('user_country', $country);
				$rows++;
			}
		}
		return $this->message('msg_ip2country_detection', [$rows]);
	}
}
