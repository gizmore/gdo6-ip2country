<?php
namespace GDO\IP2Country;

use GDO\Core\GDO;
use GDO\DB\GDT_UInt;
use GDO\Country\GDO_Country;
use GDO\Country\GDT_Country;
use GDO\DB\GDT_Index;
/**
 * IPCountry GDO table
 * 
 * @author gizmore
 * @since 3.0
 * @version 6.05
 * @see GDO_Country
 */
final class GDO_IPCountry extends GDO
{
	###########
	### GDO ###
	###########
	public function gdoEngine() { return self::MYISAM; }
	public function gdoCached() { return false; }
	public function gdoColumns()
	{
		return array(
			GDT_UInt::make('ipc_lo')->notNull(),
		    GDT_UInt::make('ipc_hi')->notNull(),
		    GDT_Country::make('ip_country')->notNull(),
		    GDT_Index::make()->indexColumns('ipc_lo', 'ipc_hi'),
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
	public static function detectISO($ip)
	{
		if ($ip = ip2long($ip))
		{
			return self::table()->select('ip_country')->where("ipc_lo <= $ip AND ipc_hi >= $ip")->limit(1)->exec()->fetchValue();
		}
	}
	
	/**
	 * 
	 * @param string $ip
	 * @return GDO_Country
	 */
	public static function detect($ip)
	{
		if ($iso = self::detectISO($ip))
		{
		    return GDO_Country::getById($iso);
		}
	}
}
