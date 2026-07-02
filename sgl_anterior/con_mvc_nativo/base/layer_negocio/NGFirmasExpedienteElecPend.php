<?php

/**
 * Capa de negocio de Firmas de Expediente Electronico Pendiente.
 *
 * @author XXXX, XXXX
 *
 */
class NGFirmasExpedienteElecPend extends NGBaseClass {

    /**
     * NGFirmasExpedienteElecPend: Obtiene una coleccion de elementos tipo FirmaExpedienteElecPend en base a diferentes criterios de selección.
     * GenerateClass 0.97.7 beta @ 2022-11-17 09:07:53
     * @param  integer (PK) anio
     * @param  string (PK) tipo
     * @param  float (PK) numero
     * @param  integer (PK) cuerpo
     * @param  integer (PK) alcance
     * @param  integer (PK) orden
     * @param  integer (PK) id_firma
     * @param  integer id_usuario
     * @param  integer id_usuario_solicitante
     * @param  string observaciones
     * @param  array|null $pOrdenColumnas Array de strings donde cada elemento define un criterio de ordenamiento para el resultado obtenido. Puede determinarse el sentido del ordenamiento, por ejemplo 'edad asc' o 'identificador desc'.
     * @param  integer $pLimiteCantidad Determina la cantidad maxima de resultados permitidos para una consulta (el resultado se trunca a esta cantidad si fuera mayor). Es equivalente al modificador LIMIT de MySQL, por ejemplo 'LIMIT 10'.
     * @return array<FirmaExpedienteElecPend>
     */
    public function obtenerFirmasExpedienteElecPend(
        // Parametros
        $panio = null,
        $ptipo = null,
        $pnumero = null,
        $pcuerpo = null,
        $palcance = null,
        $porden = null,
        $pid_firma = null,
        $pid_usuario = null,
        $pid_usuario_solicitante = null,
        $pobservaciones = null,
        // Control de consulta
        array $pOrdenColumnas = null,
        $pLimiteCantidad = null,
        $pLimiteOffset = null)
    {
        DB::getInstanceDBFirmasExpedienteElecPend()->conectar();

        try {
            // Obtengo los datos desde la capa de datos
            $filas = DB::getInstanceDBFirmasExpedienteElecPend()->obtenerFirmasExpedienteElecPend($panio, $ptipo, $pnumero, $pcuerpo, $palcance, $porden, $pid_firma, $pid_usuario, $pid_usuario_solicitante, $pobservaciones,
                $pOrdenColumnas, $pLimiteCantidad, $pLimiteOffset);
        } catch (Exception $e) {
            DB::getInstanceDBFirmasExpedienteElecPend()->desconectar();
            throw new Exception(sprintf("Error en %s.obtenerFirmasExpedienteElecPend: %s", get_class($this), $e->getMessage()));
        }

        // Transformo el array de resultados en una coleccion de elementos tipo FirmaExpedienteElecPend
        $resultado = $this->arrayResultToInstance($filas, 'FirmaExpedienteElecPend');

        DB::getInstanceDBFirmasExpedienteElecPend()->desconectar();

        return $resultado;
    }

    /**
     * NGFirmasExpedienteElecPend: Determina la cantidad de elementos tipo FirmaExpedienteElecPend obtenidos en base a diferentes criterios de selección.
     * GenerateClass 0.97.7 beta @ 2022-11-17 09:07:53
     * @param  integer (PK) anio
     * @param  string (PK) tipo
     * @param  float (PK) numero
     * @param  integer (PK) cuerpo
     * @param  integer (PK) alcance
     * @param  integer (PK) orden
     * @param  integer (PK) id_firma
     * @param  integer id_usuario
     * @param  integer id_usuario_solicitante
     * @param  string observaciones
     * @return int
     */
    public function obtenerFirmasExpedienteElecPendCantidad(
        // Parametros
        $panio = null,
        $ptipo = null,
        $pnumero = null,
        $pcuerpo = null,
        $palcance = null,
        $porden = null,
        $pid_firma = null,
        $pid_usuario = null,
        $pid_usuario_solicitante = null,
        $pobservaciones = null)
    {
        DB::getInstanceDBFirmasExpedienteElecPend()->conectar();

        try {
            // Obtengo los datos desde la capa de datos
            $cantidad_resultados = DB::getInstanceDBFirmasExpedienteElecPend()->obtenerFirmasExpedienteElecPendCantidad($panio, $ptipo, $pnumero, $pcuerpo, $palcance, $porden, $pid_firma, $pid_usuario, $pid_usuario_solicitante, $pobservaciones);
        } catch (Exception $e) {
            DB::getInstanceDBFirmasExpedienteElecPend()->desconectar();
            throw new Exception(sprintf("Error en %s.obtenerFirmasExpedienteElecPendCantidad: %s", get_class($this), $e->getMessage()));
        }

        DB::getInstanceDBFirmasExpedienteElecPend()->desconectar();

        return $cantidad_resultados;
    }

    /**
     * NGFirmasExpedienteElecPend: Obtiene una instancia de tipo FirmaExpedienteElecPend en base a su identificador.
     * Si el elemento no se encuentra, devuelve 'null'.
     * GenerateClass 0.97.7 beta @ 2022-11-17 09:07:53
     * @param  integer (PK) anio
     * @param  string (PK) tipo
     * @param  float (PK) numero
     * @param  integer (PK) cuerpo
     * @param  integer (PK) alcance
     * @param  integer (PK) orden
     * @param  integer (PK) id_firma
     * @return FirmaExpedienteElecPend Instancia de FirmaExpedienteElecPend buscada, o 'null' en caso de que no exista.
     */
    public function obtenerFirmaExpedienteElecPend(
        // Parametros
        $panio, $ptipo, $pnumero, $pcuerpo, $palcance, $porden, $pid_firma)
    {
        if (is_null($panio) || is_null($ptipo) || is_null($pnumero) || is_null($pcuerpo) || is_null($palcance) || is_null($porden) || is_null($pid_firma))
            throw new Exception(sprintf("Error en %s.obtenerFirmaExpedienteElecPend: los campos clave no pueden ser nulos.", get_class($this)));

        $resultado = $this->obtenerFirmasExpedienteElecPend($panio, $ptipo, $pnumero, $pcuerpo, $palcance, $porden, $pid_firma);

        if (count($resultado) == 0)
            return null;
        else if (count($resultado) == 1)
            return $resultado[0];
        else
            throw new Exception(sprintf("Error en %s.obtenerFirmaExpedienteElecPend: se encontr&oacute; m&aacute;s de una ocurrencia para una b&uacute;squeda de resultado &uacute;nico.", get_class($this)));
    }

    /**
     * Guarda una instancia de tipo FirmaExpedienteElecPend. Devuelve la instancia guardada, recargando las propiedades autocalculadas.
     * GenerateClass 0.97.7 beta @ 2022-11-17 09:07:53
     * @param  FirmaExpedienteElecPend $pFirmaExpedienteElecPend    Instancia a guardar.
     * @param  boolean $pRecargar       Recargar la clase despues de ser guardada, para actualizar su estado.
     * @return FirmaExpedienteElecPend               Instancia guardada.
     */
    public function guardarFirmaExpedienteElecPend(FirmaExpedienteElecPend $pFirmaExpedienteElecPend, $pRecargar = true)
    {
        if (is_null($pFirmaExpedienteElecPend))
            throw new Exception(sprintf("Error en %s.guardarFirmaExpedienteElecPend: la instancia a guardar no puede ser nula.",get_class($this)));

        DB::getInstanceDBFirmasExpedienteElecPend()->conectar(false); // AutoCommit: false
        DB::getInstanceDBFirmasExpedienteElecPend()->iniciarTransaccion(false); // SoloLectura: false

        try {
            $id = DB::getInstanceDBFirmasExpedienteElecPend()->guardarFirmaExpedienteElecPend(
                $pFirmaExpedienteElecPend->anio,
                $pFirmaExpedienteElecPend->tipo,
                $pFirmaExpedienteElecPend->numero,
                $pFirmaExpedienteElecPend->cuerpo,
                $pFirmaExpedienteElecPend->alcance,
                $pFirmaExpedienteElecPend->orden,
                $pFirmaExpedienteElecPend->id_firma,
                $pFirmaExpedienteElecPend->id_usuario,
                $pFirmaExpedienteElecPend->id_usuario_solicitante,
                $pFirmaExpedienteElecPend->observaciones);

            DB::getInstanceDBFirmasExpedienteElecPend()->guardarTransaccion();

        } catch (Exception $e) {
            DB::getInstanceDBFirmasExpedienteElecPend()->cancelarTransaccion();
            DB::getInstanceDBFirmasExpedienteElecPend()->desconectar();
            throw new Exception(sprintf("Error en %s.guardarFirmaExpedienteElecPend: transacci&oacute;n no finalizada, causa: %s", get_class($this), $e->getMessage()));
        }

        // recargo el contenido
        if ($pRecargar) {
            $resultado = $this->obtenerFirmaExpedienteElecPend($pFirmaExpedienteElecPend->anio, $pFirmaExpedienteElecPend->tipo, $pFirmaExpedienteElecPend->numero, $pFirmaExpedienteElecPend->cuerpo, $pFirmaExpedienteElecPend->alcance, $pFirmaExpedienteElecPend->orden, $pFirmaExpedienteElecPend->id_firma);
        }
        else
            $resultado = $pFirmaExpedienteElecPend;

        DB::getInstanceDBFirmasExpedienteElecPend()->desconectar();

        if (is_null($resultado))
            throw new Exception(sprintf("Error grave en %s.guardarFirmaExpedienteElecPend: no se encuentra el contenido actualizado.",get_class($this)));

        return $resultado;
    }

    /**
     * NGFirmasExpedienteElecPend: Elimina un conjunto de FirmasExpedienteElecPend en base a diferentes criterios de selección.
     * GenerateClass 0.97.7 beta @ 2022-11-17 09:07:53
     * @param  integer (PK) anio
     * @param  string (PK) tipo
     * @param  float (PK) numero
     * @param  integer (PK) cuerpo
     * @param  integer (PK) alcance
     * @param  integer (PK) orden
     * @param  integer (PK) id_firma
     * @param  integer id_usuario
     * @param  integer id_usuario_solicitante
     * @param  string observaciones
     * @return integer Cantidad de entidades afectadas.
     */
    public function eliminarFirmasExpedienteElecPend(
        // Parametros
        $panio = null,
        $ptipo = null,
        $pnumero = null,
        $pcuerpo = null,
        $palcance = null,
        $porden = null,
        $pid_firma = null,
        $pid_usuario = null,
        $pid_usuario_solicitante = null,
        $pobservaciones = null)
    {
        DB::getInstanceDBFirmasExpedienteElecPend()->conectar(false); // AutoCommit: false
        DB::getInstanceDBFirmasExpedienteElecPend()->iniciarTransaccion(false); // SoloLectura: false

        try {
            // Obtengo los datos desde la capa de datos
            $resultado = DB::getInstanceDBFirmasExpedienteElecPend()->eliminarFirmasExpedienteElecPend($panio, $ptipo, $pnumero, $pcuerpo, $palcance, $porden, $pid_firma, $pid_usuario, $pid_usuario_solicitante, $pobservaciones);

            DB::getInstanceDBFirmasExpedienteElecPend()->guardarTransaccion();

        } catch (Exception $e) {
            DB::getInstanceDBFirmasExpedienteElecPend()->cancelarTransaccion();
            DB::getInstanceDBFirmasExpedienteElecPend()->desconectar();
            throw new Exception(sprintf("Error en %s.eliminarFirmasExpedienteElecPend: transacci&oacute;n no finalizada, causa: %s", get_class($this), $e->getMessage()));
        }

        DB::getInstanceDBFirmasExpedienteElecPend()->desconectar();

        return $resultado;
    }

    /**
     * NGFirmasExpedienteElecPend: Elimina una instancia de tipo FirmaExpedienteElecPend en base a su identificador.
     * GenerateClass 0.97.7 beta @ 2022-11-17 09:07:53
     * @param  FirmaExpedienteElecPend $pFirmaExpedienteElecPend    Instancia a guardar.
     * @return boolean TRUE en caso de que se haya eliminado una instancia, FALSE en caso contrario.
     */
    public function eliminarFirmaExpedienteElecPend(FirmaExpedienteElecPend $pFirmaExpedienteElecPend)
    {
        if (is_null($pFirmaExpedienteElecPend))
            throw new Exception(sprintf("Error en %s.eliminarFirmaExpedienteElecPend: la instancia a eliminar no puede ser nula.",get_class($this)));

        DB::getInstanceDBFirmasExpedienteElecPend()->conectar(false); // AutoCommit: false
        DB::getInstanceDBFirmasExpedienteElecPend()->iniciarTransaccion(false); // SoloLectura: false

        try {
            $resultado = $this->eliminarFirmasExpedienteElecPend($pFirmaExpedienteElecPend->anio, $pFirmaExpedienteElecPend->tipo, $pFirmaExpedienteElecPend->numero, $pFirmaExpedienteElecPend->cuerpo, $pFirmaExpedienteElecPend->alcance, $pFirmaExpedienteElecPend->orden, $pFirmaExpedienteElecPend->id_firma);

            if ($resultado > 1)
                throw new Exception(sprintf("Error en %s.eliminarFirmaExpedienteElecPend: se quiso eliminar m&aacute;s de una ocurrecia para una b&uacute;squeda de resultado &uacute;nico.", get_class($this)));

            DB::getInstanceDBFirmasExpedienteElecPend()->guardarTransaccion();

        } catch (Exception $e) {
            DB::getInstanceDBFirmasExpedienteElecPend()->cancelarTransaccion();
            DB::getInstanceDBFirmasExpedienteElecPend()->desconectar();
            throw new Exception(sprintf("Error en %s.eliminarFirmaExpedienteElecPend: transacci&oacute;n no finalizada, causa: %s", get_class($this), $e->getMessage()));
        }

        DB::getInstanceDBFirmasExpedienteElecPend()->desconectar();

        return ($resultado == 1);
    }

    /**
     * [obtenerFirmaExpedienteElecPendIdSiguiente description]
     * @param  FirmaExpedienteElecPend $pFirmaExpedienteElecPend [description]
     * @return [type]                                            [description]
     */
    public function obtenerFirmaExpedienteElecPendIdSiguiente(FirmaExpedienteElecPend $pFirmaExpedienteElecPend)
    {
        DB::getInstanceDBFirmasExpedienteElecPend()->conectar();

        try {
            // Obtengo los datos desde la capa de datos
            $nuevo_id_firma = DB::getInstanceDBFirmasExpedienteElecPend()->obtenerFirmaExpedienteElecPendIdSiguiente(
                $pFirmaExpedienteElecPend->anio,
                $pFirmaExpedienteElecPend->tipo,
                $pFirmaExpedienteElecPend->numero,
                $pFirmaExpedienteElecPend->cuerpo,
                $pFirmaExpedienteElecPend->alcance,
                $pFirmaExpedienteElecPend->orden
            );
        } catch (Exception $e) {
            DB::getInstanceDBFirmasExpedienteElecPend()->desconectar();
            throw new Exception(sprintf("Error en %s.obtenerFirmaExpedienteElecPendIdSiguiente: %s", get_class($this), $e->getMessage()));
        }

        DB::getInstanceDBFirmasExpedienteElecPend()->desconectar();

        return $nuevo_id_firma;
    }

    /**
     * Agrega una firma única a un documento del expediente electronico.
     * @param  FirmaExpedienteElecPend $pFirmaExpedienteElecPend [description]
     * @return [type]                                            [description]
     */
    public function agregarFirmaExpedienteElecPend(FirmaExpedienteElecPend $pFirmaExpedienteElecPend)
    {
        // En una única transacción, obtengo el nuevo ID y lo guardo en la DB.
        DB::getInstanceDBFirmasExpedienteElecPend()->conectar(false); // AutoCommit: false
        DB::getInstanceDBFirmasExpedienteElecPend()->iniciarTransaccion(false); // SoloLectura: false

        try {
            // Piso id de firma
            $pFirmaExpedienteElecPend->id_firma = $this->obtenerFirmaExpedienteElecPendIdSiguiente($pFirmaExpedienteElecPend);

            // Guardo
            $pFirmaExpedienteElecPend = $this->guardarFirmaExpedienteElecPend($pFirmaExpedienteElecPend, true);

            // Ejecuto transaccion
            DB::getInstanceDBFirmasExpedienteElecPend()->guardarTransaccion();

        } catch (Exception $e) {
            DB::getInstanceDBFirmasExpedienteElecPend()->cancelarTransaccion();
            DB::getInstanceDBFirmasExpedienteElecPend()->desconectar();
            throw new Exception(sprintf("Error en %s.agregarFirma: transacci&oacute;n no finalizada, causa: %s", get_class($this), $e->getMessage()));
        }

        DB::getInstanceDBFirmasExpedienteElecPend()->desconectar();

        // Devolvemos el expediente electronico actualizado
        return $pFirmaExpedienteElecPend;
    }

    /**
     * [agregarFirmasDocumentoElectronicoPend description]
     * @param  ExpedienteElecPend $pExpedienteElecPend  [description]
     * @param  Usuario            $pusuario_solicitante [description]
     * @param  array              $pfirmantes           [description]
     * @param  string             $pobservaciones       [description]
     * @return [type]                                   [description]
     */
    public function agregarFirmasDocumentoElectronicoPend(
        ExpedienteElecPend $pExpedienteElecPend,
        Usuario $pusuario_solicitante,
        $pfirmantes = [],
        $pobservaciones = ''
    ) {
        if (is_null($pExpedienteElecPend))
            throw new Exception(sprintf("Error en %s.agregarFirmasDocumentoElectronicoPend: la instancia a del expediente electrónico no puede ser nula.",get_class($this)));

        if (is_null($pusuario_solicitante))
            throw new Exception(sprintf("Error en %s.agregarFirmasDocumentoElectronicoPend: el usuario solicitante no puede ser nulo.",get_class($this)));

        if (count($pfirmantes) == 0)
            throw new Exception(sprintf("Error en %s.agregarFirmasDocumentoElectronicoPend: debe especificar al menos un usuario firmante.",get_class($this)));

        $lista_id_usuario = array_map(function ($u) {
            return $u->id_usuario;
        }, $pfirmantes);

        $usuarios_firma_count = NG::seguridad()->obtenerUsuariosFirmantesCantidad(
            $lista_id_usuario, // $pid_usuario
            null, // $pcodigo_usuario
            null, // $pnombre_usuario
            null, // $piniciales_usuario
            null, // $ppassword_usuario
            true, // $phabilitado_usuario
            null, // $pconfirma_giros
            null, // $pobservaciones_usuario
            null  // $pu_legajo
        );

        if (count($pfirmantes) != $usuarios_firma_count)
            throw new Exception(sprintf("Error en %s.agregarFirmasDocumentoElectronicoPend: algunos usuarios firmantes no tienen capacidad de firmar electrónicamente un documento.",get_class($this)));

        if ($pobservaciones == '')
            $pobservaciones = sprintf('En relación a entrada de expediente electrónico pendiente: %d-%s-%d cpo %d alc %d, orden %d', $pExpedienteElecPend->anio, $pExpedienteElecPend->tipo, $pExpedienteElecPend->numero, $pExpedienteElecPend->cuerpo, $pExpedienteElecPend->alcance, $pExpedienteElecPend->orden);

        foreach ($pfirmantes as $f) {
            $this->agregarFirmaExpedienteElecPend(
                new FirmaExpedienteElecPend(
                    $pExpedienteElecPend->anio,
                    $pExpedienteElecPend->tipo,
                    $pExpedienteElecPend->numero,
                    $pExpedienteElecPend->cuerpo,
                    $pExpedienteElecPend->alcance,
                    $pExpedienteElecPend->orden,
                    null, // id_firma calculado automaticamente
                    $f->id_usuario,                    // signatario
                    $pusuario_solicitante->id_usuario, // solicitante
                    $pobservaciones
                )
            );
        }
    }

}
?>
