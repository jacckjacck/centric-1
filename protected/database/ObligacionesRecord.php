<?php

/**
 * Auto generated by prado-cli.php on 2011-03-08 09:15:50.
 * */
class ObligacionesRecord extends TActiveRecord {

    const TABLE = 'obligaciones';

    public $CodObligacion;
    public $NrObligacion;
    public $FhObligacion;
    public $FhVencimientoObligacion;
    public $FhUltimoPago;
    public $FhReporte;
    public $IdTercero;
    public $ValorCuota;
    public $ValorFactura;
    public $ValorReporte;
    public $Estado;
    public $Saldo;
    public $Intereses;
    public $Asignada;
    public $FechaUltimaGestion;

    //public $TotalDeuda;

    public static function finder($className = __CLASS__) {
        return parent::finder($className);
    }
    
     /**
     * Devuelve todas la obligaciones de un moroso de un cliente
     * @param integer $CodigoTercero La identificacion del moroso
     * @return ObligacionesRecord/boolean objeto del tipo obligaciones si encuentra obligaciones / falso si no encuentra
     */
    public static function ObligacionesXMorosoXCliente($IdentificacionMoroso,$IdentificacionCliente) {
        $strSql = "SELECT obligaciones.*
            FROM obligaciones LEFT JOIN terceros ON obligaciones.IdTercero = terceros.Identificacion 
            WHERE obligaciones.IdTercero = $IdentificacionMoroso AND terceros.IdTerceroPertenece = $IdentificacionCliente AND Saldo > 0";
        $arObligaciones = ObligacionesRecord::finder()->findAllBySql($strSql);

        if (Count($arObligaciones) != 0) {
            return $arObligaciones;
        } else {
            return false;
        }
    }

    /** 
     * Bloquea una obligacion para que no pueda ser asignada dos veces
     * @param integer $CodigoObligacion El codigo de la obligacion
     */
    public static function BloquearObligacion($CodigoObligacion) {
        // Establecemos la obligacion como asignada para que no se le asigne a ningun otro usuario;
        $Obligacion = new ObligacionesRecord();
        $Obligacion = ObligacionesRecord::DevObligacionPk($CodigoObligacion);
        $Obligacion->Asignada = 1;
        if($Obligacion->save())
            return true;
        else
            return false;
    }

    /**
     * Retorna todas las obligaciones
     * @return <objeto Tipo obligacionesRecord>
     * */
    public static function Obligaciones() {
        $sql = "SELECT obligaciones.*, CONCAT(terceros.Nombre,' ',terceros.Nombre2) as Nombres, terceros.NombreCorto,
                       CONCAT(terceros.Apellido1,' ',terceros.Apellido2) as Apellidos, FhObligacion,FhVencimientoObligacion
                FROM obligaciones LEFT JOIN terceros ON terceros.Identificacion = obligaciones.IdTercero
                WHERE Asignada = 0";

        $Obligaciones = new ObligacionesRecord();
        $Obligaciones = ObligacionesRecord::finder('ObligacionesExtRecord')->findAllBySql($sql);

        return $Obligaciones;
    }

    /**
     * REtorna todas las obligaciones de un tercero
     * @return <objeto Tipo obligacionesRecord>
     * */
    public static function ObligacionesTercero($intIdTercero) {
        $sql = "SELECT obligaciones.*, terceros.NombreCorto
                FROM obligaciones LEFT JOIN terceros ON terceros.Identificacion = obligaciones.IdTercero
                WHERE terceros.IdTerceroPertenece = $intIdTercero";

        $Obligaciones = new ObligacionesRecord();
        $Obligaciones = ObligacionesRecord::finder('ObligacionesExtRecord')->findAllBySql($sql);

        return $Obligaciones;
    }

    /**
     * 
     * */
    public static function FiltrosObligaciones($page) {
//      Mostrar todas las obligaciones segun parametros.
        if (!$page->ChkCompromisos->Checked) {

            $sql = "SELECT obligaciones.*, CONCAT(terceros.Nombre,'',terceros.Nombre2) as Nombres, CONCAT(terceros.Apellido1,'',terceros.Apellido2) as Apellidos, terceros.NombreCorto
                     FROM terceros INNER JOIN obligaciones ON terceros.Identificacion = obligaciones.IdTercero
                     WHERE Asignada = 0";

//        Rangos de saldos de las obligaciones
            if ($page->CboRangos->SelectedValue != '' && $page->TxtRangos->Text != '' && is_numeric($page->TxtRangos->Text))
                $sql = $sql . " AND obligaciones.Saldo  " . $page->CboRangos->SelectedValue . $page->TxtRangos->Text;

//        Rangos de empresa a  la que pertenece la cartera
            if ($page->CboEmpresas->SelectedValue != '') {
                $sql = $sql . " AND terceros.IdTerceroPertenece = " . $page->CboEmpresas->SelectedValue;
            }

//        Acuerdos de pagos incumplidos
            if ($page->ChkAcuerdosIncumplidos->Checked)
                $sql = $sql . " AND compromisospago.Incumplido = 1";

//        Orden ascendente para los saldos
//            if ($page->OptAscendentes->Checked)
//                $sql = $sql . " Order BY obligaciones.Saldo ASC";
////         Orden descendente para los saldos
//            if ($page->OptDescendentes->Checked)
//                $sql = $sql . " Order BY obligaciones.Saldo DESC";
        }

//         Mostrar solo las obligaciones que cuentan con un acuerdo de pago
        else {
            $sql = "SELECT obligaciones.*, CONCAT(terceros.Nombre,'',terceros.Nombre2) as Nombres, CONCAT(terceros.Apellido1,'',terceros.Apellido2) as Apellidos
                    FROM ((compromisospago 
                    INNER JOIN `obligaciones` ON obligaciones.CodObligacion = compromisospago.CodObligacion)
                    INNER JOIN terceros ON terceros.Identificacion = obligaciones.IdTercero)
                    WHERE compromisospago.Activo = 1 AND Asignada = 1";
        }

        $Obligaciones = new ObligacionesRecord();
        $Obligaciones = ObligacionesRecord::finder('ObligacionesExtRecord')->findAllBySql($sql);

        return $Obligaciones;
    }

    /**
     * Obtiene una obligacin consulta por Primary Key y la retorna
     * @param integer $CodObligacion El codigo de la obligacion
     * @return object $Obligacion Un obejto del tipo obligacionesRecord con los 
     * datos de una obligacion especificada por parametro
     * */
    public static function DevObligacionPk($CodObligacion) {
        $Obligacion = new ObligacionesRecord();
        $Obligacion = ObligacionesRecord::finder()->findByPk($CodObligacion);

        return $Obligacion;
    }
    
    /**
     * Determina si una obligacion esta asignada.
     * @param integer $CodObligacion El codigo de la obligacion consultada
     * @return boolean
     */

    public static function DevObligacionAsignada($CodObligacion)
    {
        $Obligacion = new ObligacionesRecord();
        $Obligacion = ObligacionesRecord::finder()->findByPk($CodObligacion);
        if($Obligacion->Asignada == 0)
            return false;
        else 
            return true;
    }
    

    /**
     * Actualiza la fecha de la ultima gestin de una obligacion
     */
    public static function ActualizarFechaUltimaGestion($CodObligacion)
    {
        $Obligacion = new ObligacionesRecord();
        $Obligacion = ObligacionesRecord::finder()->findByPk($CodObligacion);
        $Obligacion->FechaUltimaGestion = date('Y-m-d H:i:s');
        $Obligacion->save();
    }
    
    public static function Transaccion() {

        $Finder = ObligacionesRecord::finder();
        $Finder->DbConnection->Active = true; //open if necessary
        $Transaction = $Finder->DbConnection->beginTransaction();

        return $Transaction;
    }

}

class ObligacionesExtRecord extends ObligacionesRecord {

    public $NombreCorto;
    public $Nombres;
    public $Apellidos;
    public $NombreCortoPertenece;
    public $Saldo;

}

?>