<?php
namespace GDO\IP2Country;

use GDO\DB\GDO;
use GDO\Type\GDO_Int;
use GDO\Country\Country;
use GDO\Country\GDO_Country;
use GDO\DB\GDO_Index;
/**
 * IPCountry GDO table
 * 
 * @author gizmore
 * @since 3.0
 * @version 5.0
 *
 * @see Country
 * 
 */
final class IPCountry extends GDO
{
	###########
	### GDO ###
	###########
	public function gdoEngine() { return self::MYISAM; }
	public function gdoCached() { return false; }
	public function gdoColumns()
	{
		return array(
			GDO_Int::make('ipc_lo')->unsigned()->notNull(),
		    GDO_Int::make('ipc_hi')->unsigned()->notNull(),
		    GDO_Country::make('ip_country')->notNull(),
		    GDO_Index::make()->indexColumns('ipc_lo', 'ipc_hi'),
		);
	}
	
	###########
	### API ###
	###########
	/**
	 * Detect a country by IP. Return it's ISO2 code.
	 * @param string $ip
	 * @return string country iso
	 */
	public static function detectISO(string $ip)
	{
		if ($ip = ip2long($ip))
		{
			return self::table()->select('ip_country')->where("ipc_lo <= $ip AND ipc_hi >= $ip")->limit(1)->exec()->fetchValue();
		}
	}
	
	/**
	 * 
	 * @param string $ip
	 * @return Country
	 */
	public static function detect(string $ip)
	{
		if ($iso = self::detectISO($ip))
		{
			return Country::getById($iso);
		}
	}
}
