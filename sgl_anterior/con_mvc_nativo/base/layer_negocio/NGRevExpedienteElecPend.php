<?php

/**
 * Capa de negocio de Revisiones de Expediente Electronico Pendiente.
 *
 * @author XXXX, XXXX
 *
 */
class NGRevExpedienteElecPend extends NGBaseClass {

    /**
     * NGRevExpedienteElecPend: Obtiene una coleccion de elementos tipo RevExpedienteElecPend en base a diferentes criterios de selección.
     * GenerateClass 0.97.7 beta @ 2022-11-16 12:41:32
     * @param  integer (PK) anio
     * @param  string (PK) tipo
     * @param  float (PK) numero
     * @param  integer (PK) cuerpo
     * @param  integer (PK) alcance
     * @param  integer (PK) orden
     * @param  integer (PK) id_revision
     * @param  integer id_usuario
     * @param  string estado
     * @param  string fecha_hora_entrada
     * @param  string fecha_hora_salida
     * @param  string observaciones
     * @param  array|null $pOrdenColumnas Array de strings donde cada elemento define un criterio de ordenamiento para el resultado obtenido. Puede determinarse el sentido del ordenamiento, por ejemplo 'edad asc' o 'identificador desc'.
     * @param  integer $pLimiteCantidad Determina la cantidad maxima de resultados permitidos para una consulta (el resultado se trunca a esta cantidad si fuera mayor). Es equivalente al modificador LIMIT de MySQL, por ejemplo 'LIMIT 10'.
     * @return array<RevExpedienteElecPend>
     */
    public function obtenerRevsExpedienteElecPend(
        // Parametros
        $panio = null,
        $ptipo = null,
        $pnumero = null,
        $pcuerpo = null,
        $palcance = null,
        $porden = null,
        $pid_revision = null,
        $pid_usuario = null,
        $pestado = null,
        $pfecha_hora_entrada = null,
        $pfecha_hora_salida = null,
        $pobservaciones = null,
        // Control de consulta
        array $pOrdenColumnas = null,
        $pLimiteCantidad = null,
        $pLimiteOffset = null)
    {
        DB::getInstanceDBRevExpedienteElecPend()->conectar();

        try {
            // Obtengo los datos desde la capa de datos
            $filas = DB::getInstanceDBRevExpedienteElecPend()->obtenerRevsExpedienteElecPend($panio, $ptipo, $pnumero, $pcuerpo, $palcance, $porden, $pid_revision, $pid_usuario, $pestado, $pfecha_hora_entrada, $pfecha_hora_salida, $pobservaciones,
                $pOrdenColumnas, $pLimiteCantidad, $pLimiteOffset);
        } catch (Exception $e) {
            DB::getInstanceDBRevExpedienteElecPend()->desconectar();
            throw new Exception(sprintf("Error en %s.obtenerRevsExpedienteElecPend: %s", get_class($this), $e->getMessage()));
        }

        // Transformo el array de resultados en una coleccion de elementos tipo RevExpedienteElecPend
        $resultado = $this->arrayResultToInstance($filas, 'RevExpedienteElecPend');

        DB::getInstanceDBRevExpedienteElecPend()->desconectar();

        return $resultado;
    }

    /**
     * NGRevExpedienteElecPend: Determina la cantidad de elementos tipo RevExpedienteElecPend obtenidos en base a diferentes criterios de selección.
     * GenerateClass 0.97.7 beta @ 2022-11-16 12:41:32
     * @param  integer (PK) anio
     * @param  string (PK) tipo
     * @param  float (PK) numero
     * @param  integer (PK) cuerpo
     * @param  integer (PK) alcance
     * @param  integer (PK) orden
     * @param  integer (PK) id_revision
     * @param  integer id_usuario
     * @param  string estado
     * @param  string fecha_hora_entrada
     * @param  string fecha_hora_salida
     * @param  string observaciones
     * @return int
     */
    public function obtenerRevsExpedienteElecPendCantidad(
        // Parametros
        $panio = null,
        $ptipo = null,
        $pnumero = null,
        $pcuerpo = null,
        $palcance = null,
        $porden = null,
        $pid_revision = null,
        $pid_usuario = null,
        $pestado = null,
        $pfecha_hora_entrada = null,
        $pfecha_hora_salida = null,
        $pobservaciones = null)
    {
        DB::getInstanceDBRevExpedienteElecPend()->conectar();

        try {
            // Obtengo los datos desde la capa de datos
            $cantidad_resultados = DB::getInstanceDBRevExpedienteElecPend()->obtenerRevsExpedienteElecPendCantidad($panio, $ptipo, $pnumero, $pcuerpo, $palcance, $porden, $pid_revision, $pid_usuario, $pestado, $pfecha_hora_entrada, $pfecha_hora_salida, $pobservaciones);
        } catch (Exception $e) {
            DB::getInstanceDBRevExpedienteElecPend()->desconectar();
            throw new Exception(sprintf("Error en %s.obtenerRevsExpedienteElecPendCantidad: %s", get_class($this), $e->getMessage()));
        }

        DB::getInstanceDBRevExpedienteElecPend()->desconectar();

        return $cantidad_resultados;
    }

    /**
     * NGRevExpedienteElecPend: Obtiene una instancia de tipo RevExpedienteElecPend en base a su identificador.
     * Si el elemento no se encuentra, devuelve 'null'.
     * GenerateClass 0.97.7 beta @ 2022-11-16 12:41:32
     * @param  integer (PK) anio
     * @param  string (PK) tipo
     * @param  float (PK) numero
     * @param  integer (PK) cuerpo
     * @param  integer (PK) alcance
     * @param  integer (PK) orden
     * @param  integer (PK) id_revision
     * @return RevExpedienteElecPend Instancia de RevExpedienteElecPend buscada, o 'null' en caso de que no exista.
     */
    public function obtenerRevExpedienteElecPend(
        // Parametros
        $panio, $ptipo, $pnumero, $pcuerpo, $palcance, $porden, $pid_revision)
    {
        if (is_null($panio) || is_null($ptipo) || is_null($pnumero) || is_null($pcuerpo) || is_null($palcance) || is_null($porden) || is_null($pid_revision))
            throw new Exception(sprintf("Error en %s.obtenerRevExpedienteElecPend: los campos clave no pueden ser nulos.", get_class($this)));

        $resultado = $this->obtenerRevsExpedienteElecPend($panio, $ptipo, $pnumero, $pcuerpo, $palcance, $porden, $pid_revision);

        if (count($resultado) == 0)
            return null;
        else if (count($resultado) == 1)
            return $resultado[0];
        else
            throw new Exception(sprintf("Error en %s.obtenerRevExpedienteElecPend: se encontr&oacute; m&aacute;s de una ocurrecia para una b&uacute;squeda de resultado &uacute;nico.", get_class($this)));
    }

    /**
     * Guarda una instancia de tipo RevExpedienteElecPend. Devuelve la instancia guardada, recargando las propiedades autocalculadas.
     * GenerateClass 0.97.7 beta @ 2022-11-16 12:41:32
     * @param  RevExpedienteElecPend $pRevExpedienteElecPend    Instancia a guardar.
     * @param  boolean $pRecargar       Recargar la clase despues de ser guardada, para actualizar su estado.
     * @return RevExpedienteElecPend               Instancia guardada.
     */
    public function guardarRevExpedienteElecPend(RevExpedienteElecPend $pRevExpedienteElecPend, $pRecargar = true)
    {
        if (is_null($pRevExpedienteElecPend))
            throw new Exception(sprintf("Error en %s.guardarRevExpedienteElecPend: la instancia a guardar no puede ser nula.",get_class($this)));

        DB::getInstanceDBRevExpedienteElecPend()->conectar(false); // AutoCommit: false
        DB::getInstanceDBRevExpedienteElecPend()->iniciarTransaccion(false); // SoloLectura: false

        try {
            $id = DB::getInstanceDBRevExpedienteElecPend()->guardarRevExpedienteElecPend(
                $pRevExpedienteElecPend->anio,
                $pRevExpedienteElecPend->tipo,
                $pRevExpedienteElecPend->numero,
                $pRevExpedienteElecPend->cuerpo,
                $pRevExpedienteElecPend->alcance,
                $pRevExpedienteElecPend->orden,
                $pRevExpedienteElecPend->id_revision,
                $pRevExpedienteElecPend->id_usuario,
                $pRevExpedienteElecPend->estado,
                $pRevExpedienteElecPend->fecha_hora_entrada,
                $pRevExpedienteElecPend->fecha_hora_salida,
                $pRevExpedienteElecPend->observaciones);

            DB::getInstanceDBRevExpedienteElecPend()->guardarTransaccion();

        } catch (Exception $e) {
            DB::getInstanceDBRevExpedienteElecPend()->cancelarTransaccion();
            DB::getInstanceDBRevExpedienteElecPend()->desconectar();
            throw new Exception(sprintf("Error en %s.guardarRevExpedienteElecPend: transacci&oacute;n no finalizada, causa: %s", get_class($this), $e->getMessage()));
        }

        // recargo el contenido
        if ($pRecargar) {
            $resultado = $this->obtenerRevExpedienteElecPend($pRevExpedienteElecPend->anio, $pRevExpedienteElecPend->tipo, $pRevExpedienteElecPend->numero, $pRevExpedienteElecPend->cuerpo, $pRevExpedienteElecPend->alcance, $pRevExpedienteElecPend->orden, $pRevExpedienteElecPend->id_revision);
        }
        else
            $resultado = $pRevExpedienteElecPend;

        DB::getInstanceDBRevExpedienteElecPend()->desconectar();

        if (is_null($resultado))
            throw new Exception(sprintf("Error grave en %s.guardarRevExpedienteElecPend: no se encuentra el contenido actualizado.",get_class($this)));

        return $resultado;
    }

    /**
     * NGRevExpedienteElecPend: Elimina un conjunto de RevsExpedienteElecPend en base a diferentes criterios de selección.
     * GenerateClass 0.97.7 beta @ 2022-11-16 12:41:32
     * @param  integer (PK) anio
     * @param  string (PK) tipo
     * @param  float (PK) numero
     * @param  integer (PK) cuerpo
     * @param  integer (PK) alcance
     * @param  integer (PK) orden
     * @param  integer (PK) id_revision
     * @param  integer id_usuario
     * @param  string estado
     * @param  string fecha_hora_entrada
     * @param  string fecha_hora_salida
     * @param  string observaciones
     * @return integer Cantidad de entidades afectadas.
     */
    public function eliminarRevsExpedienteElecPend(
        // Parametros
        $panio = null,
        $ptipo = null,
        $pnumero = null,
        $pcuerpo = null,
        $palcance = null,
        $porden = null,
        $pid_revision = null,
        $pid_usuario = null,
        $pestado = null,
        $pfecha_hora_entrada = null,
        $pfecha_hora_salida = null,
        $pobservaciones = null)
    {
        DB::getInstanceDBRevExpedienteElecPend()->conectar(false); // AutoCommit: false
        DB::getInstanceDBRevExpedienteElecPend()->iniciarTransaccion(false); // SoloLectura: false

        try {
            // Obtengo los datos desde la capa de datos
            $resultado = DB::getInstanceDBRevExpedienteElecPend()->eliminarRevsExpedienteElecPend($panio, $ptipo, $pnumero, $pcuerpo, $palcance, $porden, $pid_revision, $pid_usuario, $pestado, $pfecha_hora_entrada, $pfecha_hora_salida, $pobservaciones);

            DB::getInstanceDBRevExpedienteElecPend()->guardarTransaccion();

        } catch (Exception $e) {
            DB::getInstanceDBRevExpedienteElecPend()->cancelarTransaccion();
            DB::getInstanceDBRevExpedienteElecPend()->desconectar();
            throw new Exception(sprintf("Error en %s.eliminarRevsExpedienteElecPend: transacci&oacute;n no finalizada, causa: %s", get_class($this), $e->getMessage()));
        }

        DB::getInstanceDBRevExpedienteElecPend()->desconectar();

        return $resultado;
    }

    /**
     * NGRevExpedienteElecPend: Elimina una instancia de tipo RevExpedienteElecPend en base a su identificador.
     * GenerateClass 0.97.7 beta @ 2022-11-16 12:41:32
     * @param  RevExpedienteElecPend $pRevExpedienteElecPend    Instancia a guardar.
     * @return boolean TRUE en caso de que se haya eliminado una instancia, FALSE en caso contrario.
     */
    public function eliminarRevExpedienteElecPend(RevExpedienteElecPend $pRevExpedienteElecPend)
    {
        if (is_null($pRevExpedienteElecPend))
            throw new Exception(sprintf("Error en %s.eliminarRevExpedienteElecPend: la instancia a eliminar no puede ser nula.",get_class($this)));

        DB::getInstanceDBRevExpedienteElecPend()->conectar(false); // AutoCommit: false
        DB::getInstanceDBRevExpedienteElecPend()->iniciarTransaccion(false); // SoloLectura: false

        try {
            $resultado = $this->eliminarRevsExpedienteElecPend($pRevExpedienteElecPend->anio, $pRevExpedienteElecPend->tipo, $pRevExpedienteElecPend->numero, $pRevExpedienteElecPend->cuerpo, $pRevExpedienteElecPend->alcance, $pRevExpedienteElecPend->orden, $pRevExpedienteElecPend->id_revision);

            if ($resultado > 1)
                throw new Exception(sprintf("Error en %s.eliminarRevExpedienteElecPend: se quiso eliminar m&aacute;s de una ocurrecia para una b&uacute;squeda de resultado &uacute;nico.", get_class($this)));

            DB::getInstanceDBRevExpedienteElecPend()->guardarTransaccion();

        } catch (Exception $e) {
            DB::getInstanceDBRevExpedienteElecPend()->cancelarTransaccion();
            DB::getInstanceDBRevExpedienteElecPend()->desconectar();
            throw new Exception(sprintf("Error en %s.eliminarRevExpedienteElecPend: transacci&oacute;n no finalizada, causa: %s", get_class($this), $e->getMessage()));
        }

        DB::getInstanceDBRevExpedienteElecPend()->desconectar();

        return ($resultado == 1);
    }

    /**
     * [obtenerRevExpedienteElecPendIdSiguiente description]
     * @param  RevExpedienteElecPend $pRevExpedienteElecPend [description]
     * @return [type]                                        [description]
     */
    public function obtenerRevExpedienteElecPendIdSiguiente(RevExpedienteElecPend $pRevExpedienteElecPend)
    {
        DB::getInstanceDBRevExpedienteElecPend()->conectar();

        try {
            // Obtengo los datos desde la capa de datos
            $nuevo_id_revision = DB::getInstanceDBRevExpedienteElecPend()->obtenerRevExpedienteElecPendIdSiguiente(
                $pRevExpedienteElecPend->anio,
                $pRevExpedienteElecPend->tipo,
                $pRevExpedienteElecPend->numero,
                $pRevExpedienteElecPend->cuerpo,
                $pRevExpedienteElecPend->alcance,
                $pRevExpedienteElecPend->orden
            );
        } catch (Exception $e) {
            DB::getInstanceDBRevExpedienteElecPend()->desconectar();
            throw new Exception(sprintf("Error en %s.obtenerRevExpedienteElecPendIdSiguiente: %s", get_class($this), $e->getMessage()));
        }

        DB::getInstanceDBRevExpedienteElecPend()->desconectar();

        return $nuevo_id_revision;
    }

    /**
     * Agrega una revision única a un documento pendiente del expediente electronico.
     * @param  RevExpedienteElecPend $pRevExpedienteElecPend [description]
     * @return [type]                                        [description]
     */
    public function agregarRevExpedienteElecPend(RevExpedienteElecPend $pRevExpedienteElecPend)
    {
        // En una única transacción, obtengo el nuevo ID y lo guardo en la DB.
        DB::getInstanceDBRevExpedienteElecPend()->conectar(false); // AutoCommit: false
        DB::getInstanceDBRevExpedienteElecPend()->iniciarTransaccion(false); // SoloLectura: false

        try {
            // Piso id de revision y fecha/hora
            $fecha_hora = date('Y-m-d H:i:s');
            $pRevExpedienteElecPend->id_revision = $this->obtenerRevExpedienteElecPendIdSiguiente($pRevExpedienteElecPend);
            $pRevExpedienteElecPend->fecha_hora_entrada = $fecha_hora;
            $pRevExpedienteElecPend->fecha_hora_salida = ($pRevExpedienteElecPend->estado == 'pendiente')
                ? null
                : $fecha_hora;

            // Guardo
            $pRevExpedienteElecPend = $this->guardarRevExpedienteElecPend($pRevExpedienteElecPend, true);

            // Ejecuto transaccion
            DB::getInstanceDBRevExpedienteElecPend()->guardarTransaccion();

        } catch (Exception $e) {
            DB::getInstanceDBRevExpedienteElecPend()->cancelarTransaccion();
            DB::getInstanceDBRevExpedienteElecPend()->desconectar();
            throw new Exception(sprintf("Error en %s.agregarRevExpedienteElecPend: transacci&oacute;n no finalizada, causa: %s", get_class($this), $e->getMessage()));
        }

        DB::getInstanceDBRevExpedienteElecPend()->desconectar();

        // Devolvemos el expediente electronico actualizado
        return $pRevExpedienteElecPend;
    }

    /**
     * [agregarRevsDocumentoElectronicoPend description]
     * @param  ExpedienteElecPend $pExpedienteElecPend  [description]
     * @param  Usuario            $pusuario_solicitante [description]
     * @param  array              $previsores           [description]
     * @param  string             $pestado              [description]
     * @param  string             $pobservaciones       [description]
     * @return [type]                                   [description]
     */
    public function agregarRevsDocumentoElectronicoPend(
        ExpedienteElecPend $pExpedienteElecPend,
        Usuario $pusuario_solicitante,
        $previsores = [],
        $pestado = 'pendiente',
        $pobservaciones = ''
    ) {
        if (is_null($pExpedienteElecPend))
            throw new Exception(sprintf("Error en %s.agregarRevsDocumentoElectronicoPend: la instancia a del expediente electrónico no puede ser nula.",get_class($this)));

        if (is_null($pusuario_solicitante))
            throw new Exception(sprintf("Error en %s.agregarRevsDocumentoElectronicoPend: el usuario solicitante no puede ser nulo.",get_class($this)));

        if (count($previsores) == 0)
            throw new Exception(sprintf("Error en %s.agregarRevsDocumentoElectronicoPend: debe especificar al menos un usuario firmante.",get_class($this)));

        $lista_id_usuario = array_map(function ($u) {
            return $u->id_usuario;
        }, $previsores);

        $usuarios_revisores_count = NG::seguridad()->obtenerUsuariosFirmantesCantidad(
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

        if (count($previsores) != $usuarios_revisores_count)
            throw new Exception(sprintf("Error en %s.agregarRevsDocumentoElectronicoPend: algunos usuarios firmantes no tienen capacidad de firmar electrónicamente un documento.",get_class($this)));

//        if ($pobservaciones == '')
//            $pobservaciones = sprintf('En relación a documento pendiente de revision: %d-%s-%d cpo %d alc %d, orden %d', $pExpedienteElecPend->anio, $pExpedienteElecPend->tipo, $pExpedienteElecPend->numero, $pExpedienteElecPend->cuerpo, $pExpedienteElecPend->alcance, $pExpedienteElecPend->orden);

        foreach ($previsores as $r) {
            $this->agregarRevExpedienteElecPend(
                new RevExpedienteElecPend(
                    $pExpedienteElecPend->anio,
                    $pExpedienteElecPend->tipo,
                    $pExpedienteElecPend->numero,
                    $pExpedienteElecPend->cuerpo,
                    $pExpedienteElecPend->alcance,
                    $pExpedienteElecPend->orden,
                    null, // id_revision calculado automaticamente
                    $r->id_usuario, // revisor
                    $pestado,
                    null, // fecha_hora_entrada automatica segun estado
                    null, // fecha_hora_salida automatica segun estado
                    $pobservaciones
                )
            );
        }
    }

    /**
     * Obtiene las revisiones pendientes para un determinado usuario.
     *
     * @param  Usuario $pUsuario            Usuario actual
     * @param  array   $pOrdenColumnas      Array de strings con nombres de campos y su orden respectivo
     * @param  integer $pLimiteCantidad     Cantidad de registros (paginación)
     * @param  integer $pLimiteOffset       Corrimiento de registros (paginación)
     *
     * @return RevExpedienteElecPend|null   Coleccion de elementos tipo RevExpedienteElecPend
     */
    public function obtenerRevisionesPendientesUsuario(
        Usuario $pUsuario,
        // Control de consulta
        array $pOrdenColumnas = null,
        $pLimiteCantidad = null,
        $pLimiteOffset = null
    ) {
        $orden = (is_null($pOrdenColumnas))
            ? ['T.`fecha_hora_entrada` DESC']
            : $pOrdenColumnas;

        DB::getInstanceDBRevExpedienteElecPend()->conectar();

        try {
            // Obtengo los datos desde la capa de datos
            $filas = DB::getInstanceDBRevExpedienteElecPend()->obtenerRevsExpedienteElecPendUsuario(
                // Parametros
                null, // $panio
                null, // $ptipo
                null, // $pnumero
                null, // $pcuerpo
                null, // $palcance
                null, // $porden
                null, // $pid_revision
                $pUsuario->id_usuario, // $pid_usuario
                'pendiente', // $pestado
                null, // $pfecha_hora_entrada
                null, // $pfecha_hora_salida
                null, // $pobservaciones
                // Control de consulta
                $orden,
                $pLimiteCantidad,
                $pLimiteOffset);
        } catch (Exception $e) {
            DB::getInstanceDBRevExpedienteElecPend()->desconectar();
            throw new Exception(sprintf("Error en %s.obtenerRevisionesPendientesUsuario: %s", get_class($this), $e->getMessage()));
        }

        // Transformo el array de resultados en una coleccion de elementos tipo RevExpedienteElecPend
        $resultado = $this->arrayResultToInstance($filas, 'RevExpedienteElecPend');

        DB::getInstanceDBRevExpedienteElecPend()->desconectar();

        return $resultado;
    }

    /**
     * Obtiene la cantidad de revisiones pendientes para un determinado usuario.
     * @param  Usuario $pOrdenColumnas  [description]
     * @param  [type]  $pLimiteCantidad [description]
     * @param  [type]  $pLimiteOffset   [description]
     * @return [type]                   [description]
     */
    public function obtenerRevisionesPendientesUsuarioCantidad(Usuario $pUsuario = null)
    {
        DB::getInstanceDBRevExpedienteElecPend()->conectar();

        try {
            // Obtengo los datos desde la capa de datos
            $cantidad_resultados = DB::getInstanceDBRevExpedienteElecPend()->obtenerRevsExpedienteElecPendUsuarioCantidad(
                // Parametros
                null, // $panio
                null, // $ptipo
                null, // $pnumero
                null, // $pcuerpo
                null, // $palcance
                null, // $porden
                null, // $pid_revision
                $pUsuario->id_usuario, // $pid_usuario
                'pendiente', // $pestado
                null, // $pfecha_hora_entrada
                null, // $pfecha_hora_salida
                null); // $pobservaciones
        } catch (Exception $e) {
            DB::getInstanceDBRevExpedienteElecPend()->desconectar();
            throw new Exception(sprintf("Error en %s.obtenerRevisionesPendientesUsuarioCantidad: %s", get_class($this), $e->getMessage()));
        }

        DB::getInstanceDBRevExpedienteElecPend()->desconectar();

        return $cantidad_resultados;
    }

    /**
     * Obtiene las revisiones pendientes de todos los usuarios.
     *
     * @param  array|Usuario $pUsuario
     * @param  array|null    $pOrdenColumnas   Columnas con su orden respectivo
     * @param  integer       $pLimiteCantidad  Cantidad de registros (paginación)
     * @param  integer       $pLimiteOffset    Corrimiento de registros (paginación)
     *
     * @return array|null    $resultado Coleccion de elementos tipo RevExpedienteElecPend
     */
    public function obtenerRevisionesPendientesParaSupervisores(
        $pUsuario,
        // Control de consulta
        array $pOrdenColumnas = null,
        $pLimiteCantidad = null,
        $pLimiteOffset = null
    ) {
        // Validacion dura de parametros variables
        if ( !(is_null($pUsuario) || get_class($pUsuario) == "Usuario" || is_array($pUsuario)) )
            throw new Exception(sprintf("Error en %s.obtenerRevisionesPendientesParaSupervisores: el usuario solamente puede ser nulo, tipo 'Usuario' o un array.", get_class($this)));

        $orden = (is_null($pOrdenColumnas))
            ? ['T.`fecha_hora_entrada` DESC']
            : $pOrdenColumnas;

        DB::getInstanceDBRevExpedienteElecPend()->conectar();

        try {
            // Obtengo los datos desde la capa de datos
            $filas = DB::getInstanceDBRevExpedienteElecPend()->obtenerRevsExpedienteElecPendUsuario(
                // Parametros
                null, // $panio
                null, // $ptipo
                null, // $pnumero
                null, // $pcuerpo
                null, // $palcance
                null, // $porden
                null, // $pid_revision
                null, // $pUsuario->id_usuario
                'pendiente', // $pestado
                null, // $pfecha_hora_entrada
                null, // $pfecha_hora_salida
                null, // $pobservaciones
                // Control de consulta
                $orden,
                $pLimiteCantidad,
                $pLimiteOffset);
        } catch (Exception $e) {
            DB::getInstanceDBRevExpedienteElecPend()->desconectar();
            throw new Exception(sprintf("Error en %s.obtenerRevisionesPendientesParaSupervisores: %s", get_class($this), $e->getMessage()));
        }

        // Transformo el array de resultados en una coleccion de elementos tipo RevExpedienteElecPend
        $resultado = $this->arrayResultToInstance($filas, 'RevExpedienteElecPend');

        DB::getInstanceDBRevExpedienteElecPend()->desconectar();

        return $resultado;
    }

    /**
     * Obtiene la cantidad de revisiones pendientes de todos los usuarios.
     * @return integer  Cantidad de revisiones Pendientes.
     */
    public function obtenerRevisionesPendientesParaSupervisoresCantidad($pUsuario)
    {
        // Validacion dura de parametros variables
        if ( !(is_null($pUsuario) || get_class($pUsuario) == "Usuario" || is_array($pUsuario)) )
            throw new Exception(sprintf("Error en %s.obtenerFirmasPendientesParaSupervisoresCantidad: el usuario solamente puede ser nulo, tipo 'Usuario' o un array.", get_class($this)));

        return $this->obtenerRevisionesPendientesUsuarioCantidad(null);
    }
}
?>
