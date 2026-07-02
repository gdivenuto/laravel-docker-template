<?php

/**
 * Capa de negocio de Expedientes Electronicos Pendientes.
 *
 * @author XXXX, XXXX
 *
 */
class NGExpedientesElecPend extends NGBaseClass {

    /**
     * NGExpedientesElecPend: Obtiene una coleccion de elementos tipo ExpedienteElecPend en base a diferentes criterios de selección.
     * GenerateClass 0.97.7 beta @ 2022-11-16 12:10:39
     * @param  integer (PK) anio
     * @param  string (PK) tipo
     * @param  float (PK) numero
     * @param  integer (PK) cuerpo
     * @param  integer (PK) alcance
     * @param  integer (PK) orden
     * @param  string tipo_actuacion
     * @param  string detalle
     * @param  string documento
     * @param  string documento_hash
     * @param  string texto_original
     * @param  bool dec1404
     * @param  bool embebido
     * @param  bool es_caratula
     * @param  string fecha_hora
     * @param  integer id_usuario
     * @param  string observaciones
     * @param  array|null $pOrdenColumnas Array de strings donde cada elemento define un criterio de ordenamiento para el resultado obtenido. Puede determinarse el sentido del ordenamiento, por ejemplo 'edad asc' o 'identificador desc'.
     * @param  integer $pLimiteCantidad Determina la cantidad maxima de resultados permitidos para una consulta (el resultado se trunca a esta cantidad si fuera mayor). Es equivalente al modificador LIMIT de MySQL, por ejemplo 'LIMIT 10'.
     * @return array<ExpedienteElecPend>
     */
    public function obtenerExpedientesElecPend(
        // Parametros
        $panio = null,
        $ptipo = null,
        $pnumero = null,
        $pcuerpo = null,
        $palcance = null,
        $porden = null,
        $ptipo_actuacion = null,
        $pdetalle = null,
        $pdocumento = null,
        $pdocumento_hash = null,
        $ptexto_original = null,
        $pdec1404 = null,
        $pembebido = null,
        $pes_caratula = null,
        $pfecha_hora = null,
        $pid_usuario = null,
        $pobservaciones = null,
        // Control de consulta
        array $pOrdenColumnas = null,
        $pLimiteCantidad = null,
        $pLimiteOffset = null)
    {
        DB::getInstanceDBExpedientesElecPend()->conectar();

        try {
            // Obtengo los datos desde la capa de datos
            $filas = DB::getInstanceDBExpedientesElecPend()->obtenerExpedientesElecPend($panio, $ptipo, $pnumero, $pcuerpo, $palcance, $porden, $ptipo_actuacion, $pdetalle, $pdocumento, $pdocumento_hash, $ptexto_original, $pdec1404, $pembebido, $pes_caratula, $pfecha_hora, $pid_usuario, $pobservaciones,
                $pOrdenColumnas, $pLimiteCantidad, $pLimiteOffset);
        } catch (Exception $e) {
            DB::getInstanceDBExpedientesElecPend()->desconectar();
            throw new Exception(sprintf("Error en %s.obtenerExpedientesElecPend: %s", get_class($this), $e->getMessage()));
        }

        // Transformo el array de resultados en una coleccion de elementos tipo ExpedienteElecPend
        $resultado = $this->arrayResultToInstance($filas, 'ExpedienteElecPend');

        DB::getInstanceDBExpedientesElecPend()->desconectar();

        return $resultado;
    }

    /**
     * NGExpedientesElecPend: Determina la cantidad de elementos tipo ExpedienteElecPend obtenidos en base a diferentes criterios de selección.
     * GenerateClass 0.97.7 beta @ 2022-11-16 12:10:39
     * @param  integer (PK) anio
     * @param  string (PK) tipo
     * @param  float (PK) numero
     * @param  integer (PK) cuerpo
     * @param  integer (PK) alcance
     * @param  integer (PK) orden
     * @param  string tipo_actuacion
     * @param  string detalle
     * @param  string documento
     * @param  string documento_hash
     * @param  string texto_original
     * @param  bool dec1404
     * @param  bool embebido
     * @param  bool es_caratula
     * @param  string fecha_hora
     * @param  integer id_usuario
     * @param  string observaciones
     * @return int
     */
    public function obtenerExpedientesElecPendCantidad(
        // Parametros
        $panio = null,
        $ptipo = null,
        $pnumero = null,
        $pcuerpo = null,
        $palcance = null,
        $porden = null,
        $ptipo_actuacion = null,
        $pdetalle = null,
        $pdocumento = null,
        $pdocumento_hash = null,
        $ptexto_original = null,
        $pdec1404 = null,
        $pembebido = null,
        $pes_caratula = null,
        $pfecha_hora = null,
        $pid_usuario = null,
        $pobservaciones = null)
    {
        DB::getInstanceDBExpedientesElecPend()->conectar();

        try {
            // Obtengo los datos desde la capa de datos
            $cantidad_resultados = DB::getInstanceDBExpedientesElecPend()->obtenerExpedientesElecPendCantidad($panio, $ptipo, $pnumero, $pcuerpo, $palcance, $porden, $ptipo_actuacion, $pdetalle, $pdocumento, $pdocumento_hash, $ptexto_original, $pdec1404, $pembebido, $pes_caratula, $pfecha_hora, $pid_usuario, $pobservaciones);
        } catch (Exception $e) {
            DB::getInstanceDBExpedientesElecPend()->desconectar();
            throw new Exception(sprintf("Error en %s.obtenerExpedientesElecPendCantidad: %s", get_class($this), $e->getMessage()));
        }

        DB::getInstanceDBExpedientesElecPend()->desconectar();

        return $cantidad_resultados;
    }

    /**
     * NGExpedientesElecPend: Obtiene una instancia de tipo ExpedienteElecPend en base a su identificador.
     * Si el elemento no se encuentra, devuelve 'null'.
     * GenerateClass 0.97.7 beta @ 2022-11-16 12:10:39
     * @param  integer (PK) anio
     * @param  string (PK) tipo
     * @param  float (PK) numero
     * @param  integer (PK) cuerpo
     * @param  integer (PK) alcance
     * @param  integer (PK) orden
     * @return ExpedienteElecPend Instancia de ExpedienteElecPend buscada, o 'null' en caso de que no exista.
     */
    public function obtenerExpedienteElecPend(
        // Parametros
        $panio, $ptipo, $pnumero, $pcuerpo, $palcance, $porden)
    {
        if (is_null($panio) || is_null($ptipo) || is_null($pnumero) || is_null($pcuerpo) || is_null($palcance) || is_null($porden))
            throw new Exception(sprintf("Error en %s.obtenerExpedienteElecPend: los campos clave no pueden ser nulos.", get_class($this)));

        $resultado = $this->obtenerExpedientesElecPend($panio, $ptipo, $pnumero, $pcuerpo, $palcance, $porden);

        if (count($resultado) == 0)
            return null;
        else if (count($resultado) == 1)
            return $resultado[0];
        else
            throw new Exception(sprintf("Error en %s.obtenerExpedienteElecPend: se encontr&oacute; m&aacute;s de una ocurrecia para una b&uacute;squeda de resultado &uacute;nico.", get_class($this)));
    }

    /**
     * Guarda una instancia de tipo ExpedienteElecPend. Devuelve la instancia guardada, recargando las propiedades autocalculadas.
     * GenerateClass 0.97.7 beta @ 2022-11-16 12:10:39
     * @param  ExpedienteElecPend $pExpedienteElecPend  Instancia a guardar.
     * @param  boolean $pRecargar       Recargar la clase despues de ser guardada, para actualizar su estado.
     * @return ExpedienteElecPend               Instancia guardada.
     */
    public function guardarExpedienteElecPend(ExpedienteElecPend $pExpedienteElecPend, $pRecargar = true)
    {
        if (is_null($pExpedienteElecPend))
            throw new Exception(sprintf("Error en %s.guardarExpedienteElecPend: la instancia a guardar no puede ser nula.",get_class($this)));

        DB::getInstanceDBExpedientesElecPend()->conectar(false); // AutoCommit: false
        DB::getInstanceDBExpedientesElecPend()->iniciarTransaccion(false); // SoloLectura: false

        try {
            $id = DB::getInstanceDBExpedientesElecPend()->guardarExpedienteElecPend(
                $pExpedienteElecPend->anio,
                $pExpedienteElecPend->tipo,
                $pExpedienteElecPend->numero,
                $pExpedienteElecPend->cuerpo,
                $pExpedienteElecPend->alcance,
                $pExpedienteElecPend->orden,
                $pExpedienteElecPend->tipo_actuacion,
                $pExpedienteElecPend->detalle,
                $pExpedienteElecPend->documento,
                $pExpedienteElecPend->documento_hash,
                $pExpedienteElecPend->texto_original,
                $pExpedienteElecPend->dec1404,
                $pExpedienteElecPend->embebido,
                $pExpedienteElecPend->es_caratula,
                $pExpedienteElecPend->fecha_hora,
                $pExpedienteElecPend->id_usuario,
                $pExpedienteElecPend->observaciones);

            DB::getInstanceDBExpedientesElecPend()->guardarTransaccion();

        } catch (Exception $e) {
            DB::getInstanceDBExpedientesElecPend()->cancelarTransaccion();
            DB::getInstanceDBExpedientesElecPend()->desconectar();
            throw new Exception(sprintf("Error en %s.guardarExpedienteElecPend: transacci&oacute;n no finalizada, causa: %s", get_class($this), $e->getMessage()));
        }

        // recargo el contenido
        if ($pRecargar) {
            $resultado = $this->obtenerExpedienteElecPend($pExpedienteElecPend->anio, $pExpedienteElecPend->tipo, $pExpedienteElecPend->numero, $pExpedienteElecPend->cuerpo, $pExpedienteElecPend->alcance, $pExpedienteElecPend->orden);
        }
        else
            $resultado = $pExpedienteElecPend;

        DB::getInstanceDBExpedientesElecPend()->desconectar();

        if (is_null($resultado))
            throw new Exception(sprintf("Error grave en %s.guardarExpedienteElecPend: no se encuentra el contenido actualizado.",get_class($this)));

        return $resultado;
    }

    /**
     * NGExpedientesElecPend: Elimina un conjunto de ExpedientesElecPend en base a diferentes criterios de selección.
     * GenerateClass 0.97.7 beta @ 2022-11-16 12:10:39
     * @param  integer (PK) anio
     * @param  string (PK) tipo
     * @param  float (PK) numero
     * @param  integer (PK) cuerpo
     * @param  integer (PK) alcance
     * @param  integer (PK) orden
     * @param  string tipo_actuacion
     * @param  string detalle
     * @param  string documento
     * @param  string documento_hash
     * @param  string texto_original
     * @param  bool dec1404
     * @param  bool embebido
     * @param  bool es_caratula
     * @param  string fecha_hora
     * @param  integer id_usuario
     * @param  string observaciones
     * @return integer Cantidad de entidades afectadas.
     */
    public function eliminarExpedientesElecPend(
        // Parametros
        $panio = null,
        $ptipo = null,
        $pnumero = null,
        $pcuerpo = null,
        $palcance = null,
        $porden = null,
        $ptipo_actuacion = null,
        $pdetalle = null,
        $pdocumento = null,
        $pdocumento_hash = null,
        $ptexto_original = null,
        $pdec1404 = null,
        $pembebido = null,
        $pes_caratula = null,
        $pfecha_hora = null,
        $pid_usuario = null,
        $pobservaciones = null)
    {
        DB::getInstanceDBExpedientesElecPend()->conectar(false); // AutoCommit: false
        DB::getInstanceDBExpedientesElecPend()->iniciarTransaccion(false); // SoloLectura: false

        try {
            // Obtengo los datos desde la capa de datos
            $resultado = DB::getInstanceDBExpedientesElecPend()->eliminarExpedientesElecPend($panio, $ptipo, $pnumero, $pcuerpo, $palcance, $porden, $ptipo_actuacion, $pdetalle, $pdocumento, $pdocumento_hash, $ptexto_original, $pdec1404, $pembebido, $pes_caratula, $pfecha_hora, $pid_usuario, $pobservaciones);

            DB::getInstanceDBExpedientesElecPend()->guardarTransaccion();

        } catch (Exception $e) {
            DB::getInstanceDBExpedientesElecPend()->cancelarTransaccion();
            DB::getInstanceDBExpedientesElecPend()->desconectar();
            throw new Exception(sprintf("Error en %s.eliminarExpedientesElecPend: transacci&oacute;n no finalizada, causa: %s", get_class($this), $e->getMessage()));
        }

        DB::getInstanceDBExpedientesElecPend()->desconectar();

        return $resultado;
    }

    /**
     * NGExpedientesElecPend: Elimina una instancia de tipo ExpedienteElecPend en base a su identificador.
     * GenerateClass 0.97.7 beta @ 2022-11-16 12:10:39
     * @param  ExpedienteElecPend $pExpedienteElecPend  Instancia a guardar.
     * @return boolean TRUE en caso de que se haya eliminado una instancia, FALSE en caso contrario.
     */
    public function eliminarExpedienteElecPend(ExpedienteElecPend $pExpedienteElecPend)
    {
        if (is_null($pExpedienteElecPend))
            throw new Exception(sprintf("Error en %s.eliminarExpedienteElecPend: la instancia a eliminar no puede ser nula.",get_class($this)));

        DB::getInstanceDBExpedientesElecPend()->conectar(false); // AutoCommit: false
        DB::getInstanceDBExpedientesElecPend()->iniciarTransaccion(false); // SoloLectura: false

        try {
            $resultado = $this->eliminarExpedientesElecPend($pExpedienteElecPend->anio, $pExpedienteElecPend->tipo, $pExpedienteElecPend->numero, $pExpedienteElecPend->cuerpo, $pExpedienteElecPend->alcance, $pExpedienteElecPend->orden);

            if ($resultado > 1)
                throw new Exception(sprintf("Error en %s.eliminarExpedienteElecPend: se quiso eliminar m&aacute;s de una ocurrecia para una b&uacute;squeda de resultado &uacute;nico.", get_class($this)));

            DB::getInstanceDBExpedientesElecPend()->guardarTransaccion();

        } catch (Exception $e) {
            DB::getInstanceDBExpedientesElecPend()->cancelarTransaccion();
            DB::getInstanceDBExpedientesElecPend()->desconectar();
            throw new Exception(sprintf("Error en %s.eliminarExpedienteElecPend: transacci&oacute;n no finalizada, causa: %s", get_class($this), $e->getMessage()));
        }

        DB::getInstanceDBExpedientesElecPend()->desconectar();

        return ($resultado == 1);
    }

    /**
     * [obtenerExpedienteElecPendOrdenSiguiente description]
     * @param  ExpedienteElecPend $pExpedienteElecPend [description]
     * @return [type]                                  [description]
     */
    public function obtenerExpedienteElecPendOrdenSiguiente(ExpedienteElecPend $pExpedienteElecPend)
    {
        DB::getInstanceDBExpedientesElecPend()->conectar();

        try {
            // Obtengo los datos desde la capa de datos
            $nuevo_orden = DB::getInstanceDBExpedientesElecPend()->obtenerExpedienteElecPendOrdenSiguiente(
                $pExpedienteElecPend->anio,
                $pExpedienteElecPend->tipo,
                $pExpedienteElecPend->numero,
                $pExpedienteElecPend->cuerpo,
                $pExpedienteElecPend->alcance
            );
        } catch (Exception $e) {
            DB::getInstanceDBExpedientesElecPend()->desconectar();
            throw new Exception(sprintf("Error en %s.obtenerExpedienteElecPendOrdenSiguiente: %s", get_class($this), $e->getMessage()));
        }

        DB::getInstanceDBExpedientesElecPend()->desconectar();

        return $nuevo_orden;
    }

    /**
     * Crea una nueva entrada de Expediente Electronico Pendiente, y devuelve la instancia
     * actualizada.
     * @param  ExpedienteElecPend $pExpedienteElecPend [description]
     * @return [type]                              [description]
     */
    public function agregarDocumentoElectronicoPend(ExpedienteElecPend $pExpedienteElecPend)
    {
        // En una única transacción, obtengo el nuevo ID y lo guardo en la DB.
        DB::getInstanceDBExpedientesElecPend()->conectar(false); // AutoCommit: false
        DB::getInstanceDBExpedientesElecPend()->iniciarTransaccion(false); // SoloLectura: false

        try {
            // Piso orden y fecha/hora
            $pExpedienteElecPend->orden = $this->obtenerExpedienteElecPendOrdenSiguiente($pExpedienteElecPend);
            $pExpedienteElecPend->fecha_hora = date('Y-m-d H:i:s');

            // Guardo
            $pExpedienteElecPend = $this->guardarExpedienteElecPend($pExpedienteElecPend, true);

            // Ejecuto transaccion
            DB::getInstanceDBExpedientesElecPend()->guardarTransaccion();

        } catch (Exception $e) {
            DB::getInstanceDBExpedientesElecPend()->cancelarTransaccion();
            DB::getInstanceDBExpedientesElecPend()->desconectar();
            throw new Exception(sprintf("Error en %s.agregarDocumentoElectronicoPend: transacci&oacute;n no finalizada, causa: %s", get_class($this), $e->getMessage()));
        }

        DB::getInstanceDBExpedientesElecPend()->desconectar();

        // Devolvemos el expediente electronico actualizado
        return $pExpedienteElecPend;
    }

    /**
     * Obtiene una instancia de tipo Expediente en base a un expediente electrónico pendiente.
     * @param  ExpedienteElecPend $pExpedienteElecPend [description]
     * @return [type]                                  [description]
     */
    public function obtenerExpedienteDeExpedienteElecPend(ExpedienteElecPend $pExpedienteElecPend)
    {
        return NG::expedientes()->obtenerExpediente(
            $pExpedienteElecPend->anio,
            $pExpedienteElecPend->tipo,
            $pExpedienteElecPend->numero,
            $pExpedienteElecPend->cuerpo,
            $pExpedienteElecPend->alcance
        );
    }
}
?>
