<?php
/**
 * MailHelper
 *
 * Clase que permite la gestión de correos electrónicos.
 *
 * Requiere:
 *    PHPMailer (https://github.com/PHPMailer/PHPMailer): A full-featured email creation and transfer class for PHP.
 *
 * Opcionales:
 *    Ninguno.
 *
 * @author XXXX
 */
require_once(PATH_KRAKEN_LIBRERIAS_PHPMAILER.'/src/Exception.php');
require_once(PATH_KRAKEN_LIBRERIAS_PHPMAILER.'/src/PHPMailer.php');
require_once(PATH_KRAKEN_LIBRERIAS_PHPMAILER.'/src/SMTP.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// ---- Clase MailHelper ------------------------------------------------------
class MailHelper
{
    private static $instance;
    private $defaultOptions;      //!< Default option array values.
    private $debugMode;           //!< Debug mode enabled (loguea en vez de enviar correo)

    /**
     * Constructor privado, parte funcional del patron Singleton.
     */
    private function __construct()
    {
        $this->defaultOptions = [
            'smtp' => [
                'host' => SGL_MAIL_SMTP_HOST,
                'auth' => SGL_MAIL_SMTP_AUTH,
                'username' => SGL_MAIL_SMTP_USERNAME,
                'password' => SGL_MAIL_SMTP_PASSWORD,
                'secure_type' => PHPMailer::ENCRYPTION_STARTTLS,
                'port' => SGL_MAIL_SMTP_PORT
            ],
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            ],
           'sender' => [
                'from' => SGL_MAIL_FROM,
                'from_name' => SGL_MAIL_FROM_NAME,
                'reply' => SGL_MAIL_REPLY,
                'reply_name' => SGL_MAIL_REPLY_NAME,
            ],
            'recipients' => [
                'address' => [],
                'cc' => [],
                'bcc' => []
            ],
            'message' => [
                'is_html' => true,
                'subject' => '',
                'body' => '',
                'body_signature' => SGL_MAIL_TEXT_SIGNATURE,
                'body_alt' => '',
                'body_signature_alt' => SGL_MAIL_TEXT_SIGNATURE_ALT
            ],
            'attachments' => [],
            'language' => 'es',
            'charset' => 'UTF-8',
            'encoding' => 'base64'
        ];

        $this->debugMode = SGL_MAIL_DEBUG_MODE;
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
     * Configura el set de opciones del gestor de correo.
     * @param array $default [description]
     * @param array $options [description]
     */
    private function mergeOptions($default = [], $options = [])
    {
        // Preset de opciones
        $new_options = $default;

        // Piso / Agrego opciones a las opciones default
        foreach ($options as $key => $value) {
            $new_options[$key] = (is_array($value))
                ? $this->mergeOptions($new_options[$key], $value)
                : $value;
        }

        return $new_options;
    }

    /**
     * Envía un correo electrónico.
     * @param  array  $options Opciones de envío de correo.
     * @return [type]          [description]
     */
    public function sendMail($options = [])
    {
        $op = $this->mergeOptions($this->defaultOptions, $options);

        // Si el modo de debug esta activo, loguea los parametros y finaliza
        if ($this->debugMode) {
            Logger::get()->Log('sgl_mail', $op);
            return false;
        }

        $mail = new PHPMailer(true);
        $mail->CharSet = $op['charset'];
        $mail->Encoding = $op['encoding'];

        $mail->setLanguage($op['language'], PATH_KRAKEN_LIBRERIAS_PHPMAILER.'/language/');

        // SMTP settings
        //$mail->SMTPDebug  = SMTP::DEBUG_SERVER;           //Enable verbose debug output
        $mail->isSMTP();                                    //Send using SMTP
        $mail->Host     = $op['smtp']['host'];              //Set the SMTP server to send through
        $mail->Port     = $op['smtp']['port'];              //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
        $mail->SMTPAuth = $op['smtp']['auth'];              //Enable SMTP authentication
        if ($op['smtp']['auth']) {
            $mail->SMTPSecure = $op['smtp']['secure_type']; //Enable implicit TLS encryption
            $mail->Username   = $op['smtp']['username'];    //SMTP username
            $mail->Password   = $op['smtp']['password'];    //SMTP password
        }

        // Si se dejan las opciones por defecto, permite certificado de servidor autofirmado.
        $mail->SMTPOptions = [
            'ssl' => $op['ssl']
        ];

        // Remitente
        $mail->setFrom($op['sender']['from'], $op['sender']['from_name']);
        if ($op['sender']['reply'] != '')
            $mail->addReplyTo($op['sender']['reply'], $op['sender']['reply_name']);

        // Destinatarios
        foreach ($op['recipients']['address'] as $address)
            $mail->addAddress($address);

        foreach ($op['recipients']['cc'] as $address)
            $mail->addCC($address);

        foreach ($op['recipients']['bcc'] as $address)
            $mail->addBCC($address);

        // Adjuntos
        foreach ($op['attachments'] as $attachment)
            $mail->addAttachment($attachment, basename($attachment));

        // Contenido
        $mail->isHTML($op['message']['is_html']); //Set email format to HTML
        $mail->Subject = $op['message']['subject'];
        $mail->Body    = $op['message']['body'] . $op['message']['body_signature'];
        $mail->AltBody = $op['message']['body_alt'] . $op['message']['body_signature_alt'];

        // Enviar correo (puede lanzar una Excepción)
        $mail->send();

        return true;
    }
}
?>
