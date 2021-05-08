<?php
namespace GDO\IP2Country\Method;

use GDO\Form\GDT_AntiCSRF;
use GDO\Form\GDT_Form;
use GDO\Form\GDT_Submit;
use GDO\Form\MethodForm;
use GDO\IP2Country\GDO_IPCountry;
use GDO\IP2Country\Module_IP2Country;
use GDO\Core\MethodAdmin;

final class InstallIP2C extends MethodForm
{
    use MethodAdmin;
    
    public function getPermission() { return 'admin'; }
    
    public function execute()
    {
        return $this->renderNavBar('IP2Country')->addField(parent::execute());
    }
    
	public function createForm(GDT_Form $form)
	{
	    $form->title(t('ft_ip2c_install'));
		$form->addField(GDT_AntiCSRF::make());
		$form->actions()->addField(GDT_Submit::make());
	}

	public function formValidated(GDT_Form $form)
	{
		GDO_IPCountry::table()->truncate();
		$module = Module_IP2Country::instance();
		$filename = $module->filePath('data/IpToCountry.csv');
		$fh = fopen($filename, 'r');
		$noCountry = ['ZZ','EU', 'AP', 'BX', 'EF', 'EM', 'EP', 'EV', 'GC', 'IB', 'OA', 'WO'];
		$bulkData = [];
		$fields = GDO_IPCountry::table()->getGDOColumns(['ipc_lo', 'ipc_hi', 'ip_country']);
		while ($row = fgetcsv($fh))
		{
			list($lo, $hi, $registrar, $timestamp, $iso2, $iso3, $country) = $row;
			if (!in_array($iso2, $noCountry, true))
			{
			    $bulkData[] = [$lo, $hi, strtolower($iso2)];
			}
			if (count($bulkData) >= 500)
			{
			    GDO_IPCountry::bulkReplace($fields, $bulkData);
			    $bulkData = [];
			}
		}
		
		GDO_IPCountry::bulkReplace($fields, $bulkData);
		$rows = GDO_IPCountry::table()->countWhere();
		
		return $this->message('msg_ip2country_installed', [$rows]);
	}
}
