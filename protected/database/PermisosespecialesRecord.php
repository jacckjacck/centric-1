<?php
/**
 * Auto generated by prado-cli.php on 2011-07-24 07:34:23.
 */
class PermisosespecialesRecord extends TActiveRecord
{
	const TABLE='permisosespeciales';

	public $CodPermisoEspecial;
	public $Descripcion;
	public $Tipo;

	public static function finder($className=__CLASS__)
	{
		return parent::finder($className);
	}
}
?>