<?php
/**
 * Auto generated by prado-cli.php on 2011-05-06 05:18:01.
 */
class BarriosRecord extends TActiveRecord
{
	const TABLE='barrios';

	public $CodBarrio;
	public $NmBarrio;
	public $CodCiudad;

	public static function finder($className=__CLASS__)
	{
		return parent::finder($className);
	}
        
        public static function Transaccion() {

        $Finder = BarriosRecord::finder();
        $Finder->DbConnection->Active = true; //open if necessary
        $Transaction = $Finder->DbConnection->beginTransaction();

        return $Transaction;
    }
}
?>