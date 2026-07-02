<?php
/**
 * DateTimeHelper
 *
 * Clase que permite la gestión de fechas/horas y la generación de timestamps.
 *
 * Requiere:
 *    Nada.
 *
 * Opcionales:
 *    Ninguno.
 *
 */
// ---- Clase DateTimeHelper --------------------------------------------------
class DateTimeHelper
{
    private static $instance;

    /**
     * Constructor privado, parte funcional del patron Singleton.
     */
    private function __construct()
    {
    }

    /**
     * Se implementa el patrón Singleton para mantener una única instancia y poder acceder a sus
     * valores desde cualquier script.
     * @return Logger Instancia de la clase.
     */
    public static function GetInstance()
    {
        // Si la instancia no esta definida la creo, sino devuelvo la existente
        if (!isset(self::$instance))
        {
            $claseActual = __CLASS__;           // Obtengo la clase actual
            self::$instance = new $claseActual; // Creo una instancia
        }

        // Devuelvo la instancia existente.
        return self::$instance;
    }

    /**
     * Alias de GetInstance()
     * @return Logger Instancia de la clase.
     */
    public static function get()
    {
        return self::GetInstance();
    }

    /**
     * Es invocado cuando se clona un instancia.
     * Con este método podemos emitir un mensaje de error y proceder a detener la ejecución del
     * script por operación inválida al intentar clonar una instancia de Singleton.
     *
     * E_USER_ERROR: constante que contiene el mensaje de error generado por el usuario
     */
    public function __clone()
    {
        trigger_error("Operación Inválida: No se puede clonar una instancia de ". get_class($this) .".", E_USER_ERROR );
    }

    /**
     * __sleep es invocado cuando un objeto es serializado se evita serializar una instancia de
     * Singleton
     */
    public function __sleep()
    {
        trigger_error("No se puede serializar una instancia de ". get_class($this) .".");
    }

    /**
     * __wakeup es invocado cuando un objeto es deserializado se evita deserializar una instancia
     * de Singleton
     */
    public function __wakeup()
    {
        trigger_error("No se puede deserializar una instancia de ". get_class($this) .".");
    }

    /**
     * Obtiene una instancia de DateTime generada a partir de un timestamp con
     * microsegundos.
     * @param  string $datetimezone DateTimeZone (ver https://www.php.net/manual/es/timezones.america.php)
     * @return DateTime             Instancia de DateTime que contiene el Timestamp.
     */
    public function getTimestampInstance($datetimezone = SGL_TIMEZONE)
    {
        $timestamp = DateTime::createFromFormat('U.u', microtime(true));
        // --- Fix para cuando microtime devuelve valores con el float roto ---
        if (!$timestamp)
            $timestamp = new Datetime("now");
        // --------------------------------------------------------------------
        $timestamp->setTimeZone(new DateTimeZone($datetimezone));
        return $timestamp;
    }

    /**
     * Alias de DateTimeHelper->getTimestampInstance().
     * @param  string $datetimezone DateTimeZone (ver https://www.php.net/manual/es/timezones.america.php)
     * @return DateTime             Instancia de DateTime que contiene el Timestamp.
     */
    public function timestampInstance($datetimezone = SGL_TIMEZONE)
    {
        return $this->getTimestampInstance($datetimezone);
    }

    /**
     * Obtiene un timestamp con microsegundos, permitiendo una salida formateada
     * y ajustada a un determinado DateTimeZone.
     * @param  string $format       Formato de salida del timestamp.
     * @param  string $datetimezone DateTimeZone (ver https://www.php.net/manual/es/timezones.america.php)
     * @return string               Timestamp con formato $format.
     */
    public function getTimestampStr($format = 'Y-m-d H:i:s.u', $datetimezone = SGL_TIMEZONE)
    {
        $timestamp = $this->getTimestampInstance($datetimezone);
        return $timestamp->format($format);
    }

    /**
     * Alias de DateTimeHelper->getTimestampStr().
     * @param  string $format       Formato de salida del timestamp.
     * @param  string $datetimezone DateTimeZone (ver https://www.php.net/manual/es/timezones.america.php)
     * @return string               Timestamp con formato $format.
     */
    public function timestampStr($format = 'Y-m-d H:i:s.u', $datetimezone = SGL_TIMEZONE)
    {
        return $this->getTimestampStr($format, $datetimezone);
    }

    /**
     * Devuelve el nombre del mes según su número
     * @param integer $nro_mes
     * @return Ambigous <string>
     */
    public static function mostrarNombreMes($nro_mes)
    {
        $meses = Array("Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");

        return $meses[$nro_mes - 1];
    }

    /**
     * Muestra la fecha en formato: [nro del día] de [nombre del mes] de [nro del año]
     * El formato de entrada de la fecha debe ser Y-m-d o Y/m/d
     * @param string $fecha
     */
    public static function mostrarFechaLetras($fecha)
    {
        // Se divide la fecha en partes
        $divisor = (strpos($fecha, '/') === false) ? '-' : '/';
        $partes_fecha = explode($divisor, $fecha);

        // Se establece el número del día
        $dia = ($partes_fecha[2] < 10) ? substr($partes_fecha[2], 1, 1) : $partes_fecha[2];

        // Devuelve la fecha en formato [nro del día] de [nombre del mes] de [nro del año]
        return $dia . " de " . self::mostrarNombreMes($partes_fecha[1]) . " de " . $partes_fecha[0];
    }

}
?>
