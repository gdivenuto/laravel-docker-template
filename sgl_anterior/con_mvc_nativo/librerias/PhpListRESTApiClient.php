<?php
/**
 * Class phpListRESTApiClient
 *
 * Wrapper que permite conectar a un servidor con phpList y controlar las listas
 * de distribución de correo de forma programática.
 *
 * Editado (2019-04-05): se agregaron nuevos métodos.
 * Editado (2020-09-07): se agregaron nuevos métodos.
 *
 * XXXX - 2019-04-05
 */
class PhpListRESTApiClient
{
    // ------------------------------------------------------------------------
    // ---- CONFIGURACION -----------------------------------------------------
    // ------------------------------------------------------------------------

    public static $DEFAULT_API_URL               = 'http://localhost/lists/admin/?page=call&pi=restapi';
    public static $DEFAULT_API_USER              = 'admin';
    public static $DEFAULT_API_PASSWORD          = 'phplist';
    public static $DEFAULT_API_SECRET_KEY        = '';
    public static $DEFAULT_TMP_PATH              = '/tmp';
    public static $DEFAULT_COOKIE_FILE_TEMPLATE  = 'phpListRESTApiClient_user_%s_cookiejar.txt';
    public static $DEFAULT_KEEP_ALIVE            = 60;
    public static $DEFAULT_CONNECT_TIMEOUT       = 15;
    public static $DEFAULT_TIMEOUT               = 15;
    public static $DEFAULT_API_LOGIN_RETRY_DELAY = 5;

    // ------------------------------------------------------------------------
    // ---- ATRIBUTOS ---------------------------------------------------------
    // ------------------------------------------------------------------------

    // URL de la API, algo similar a: https://website.com/lists/admin/?pi=restapi&page=call
    private $apiUrl;

    // Credenciales de usuario para conectar a la API.
    private $apiUser;
    private $apiPassword;
    private $apiSecretKey; // Opcional, depende de la configuración de PHPList.

    // Ruta donde se almacenarán los archivos temporales de cookies.
    public $tmpPath;

    // ------------------------------------------------------------------------
    // ---- METODOS -----------------------------------------------------------
    // ------------------------------------------------------------------------

    /**
     * Constructor de clase.
     * @param string $api_url        URL del endpoint de la API.
     * @param string $api_user       Usuario de la API.
     * @param string $api_password   Contraseña de la API.
     * @param string $api_secret_key Clave secreta de la API (opcional).
     */
    public function __construct($api_url = '', $api_user = '', $api_password = '', $api_secret_key = '')
    {
        $this->apiUrl = ( $api_url != '' ) ? $api_url : self::$DEFAULT_API_URL;
        $this->apiUser = ( $api_user != '' ) ? $api_user : self::$DEFAULT_API_USER;
        $this->apiPassword = ( $api_password != '' ) ? $api_password : self::$DEFAULT_API_PASSWORD;
        $this->apiSecretKey = ( $api_secret_key != '' ) ? $api_secret_key : self::$DEFAULT_API_SECRET_KEY;

        $this->tmpPath = self::$DEFAULT_TMP_PATH;
    }

    /**
     * Hacer una llamada a la API utilizando cURL.
     * @param  string  $action Acción (cmd) de la API a ser ejecutada.
     * @param  array   $params Colección de parámetros para la petición.
     * @param  boolean $decode Si es TRUE, devuelve la respuesta decodificada, sino devuelve la respuesta en json plano (string).
     * @return mixed           Resultado de la ejecución de llamada a la API.
     */
    private function apiCall($action, $params, $decode = true)
    {
        // Seteo de parametros
        $params['cmd'] = $action;
        if (! empty($this->apiSecretKey) ) $params['secret'] = $this->apiSecretKey; // apiSecretKey es opcional

        $params = http_build_query($params);

        // Inicializacion de cURL
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,            $this->apiUrl);
        curl_setopt($ch, CURLOPT_HEADER,         0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST,           1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,     $params);
        curl_setopt($ch, CURLOPT_COOKIEFILE,     sprintf('%s/%s', $this->tmpPath, sprintf(self::$DEFAULT_COOKIE_FILE_TEMPLATE, $this->apiUser)));
        curl_setopt($ch, CURLOPT_COOKIEJAR,      sprintf('%s/%s', $this->tmpPath, sprintf(self::$DEFAULT_COOKIE_FILE_TEMPLATE, $this->apiUser)));
        curl_setopt($ch, CURLOPT_HTTPHEADER,     array('Connection: Keep-Alive', sprintf('Keep-Alive: %d', self::$DEFAULT_KEEP_ALIVE)));
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, self::$DEFAULT_CONNECT_TIMEOUT);
        curl_setopt($ch, CURLOPT_TIMEOUT,        self::$DEFAULT_TIMEOUT);

        // Ejecutar la llamada
        $resultRaw = curl_exec($ch);

        // Verificación de resultados
        $error_msg = '';
        if (curl_errno($ch))
            $error_msg = sprintf('Error en %s: %s', get_class($this), curl_error($ch));
        else {
            $result_http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if ( $result_http_code != 200 )
                $error_msg = sprintf('Error en %s: esperado HTTP_CODE 200, recibido %d', get_class($this), $result_http_code);
            else {
                $result_content_type = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
                if (strtolower($result_content_type) !== 'application/json')
                    $error_msg = sprintf("Error en %s: esperado Content-Type 'application/json', recibido '%s'", get_class($this), $result_content_type);
            }
        }

        // Cierro la conexión
        curl_close($ch);

        // Lanzo excepción, si corresponde
        if ($error_msg != '') throw new Exception($error_msg);

        // Retorno resultados
        if ($decode) {
            try { $result = json_decode($resultRaw); }
            catch (Exception $e) {
                throw new Exception(sprintf("Error en %s: no se ha podido decodificar la respuesta json (%s)", get_class($this), $e->getMessage()));
            }

            if (strtolower($result->status) != 'success')
                throw new Exception(sprintf("Error en %s: se esperaba status 'success', se obtuvo '%s'. JSON: %s", get_class($this), $result->status, $resultRaw));

            return $result;
        }
        else
            return $resultRaw;
    }

    /**
     * Inicia una sesión contra la API.
     * @param  boolean $retryOnFailure Si falla un intento de login, reintenta conectar.
     * @return boolean Devuelve TRUE si pudo iniciar una sesión, FALSE en caso contrario.
     */
    public function login($retryOnFailure = true)
    {
        // Parametros de la llamada
        $params = array(
            'login' => $this->apiUser,
            'password' => $this->apiPassword,
        );

        // Ejecuto la llamada a la api
        try {
            $result = $this->apiCall('login', $params);
        } catch (Exception $e) {
            // Hay reintento si falla?
            if ($retryOnFailure) {
                sleep(self::$DEFAULT_API_LOGIN_RETRY_DELAY);
                $result = $this->apiCall('login', $params);
            }
            else
                throw $e; // Si no hay reintento, lanzo la misma excepción.
        }

        // Devuelvo resultados
        return strtolower($result->status) == 'success';
    }

    /**
     * Obtiene todas las listas de distribución del usuario.
     * @return array Colección de listas de distribución.
     */
    public function listsGet()
    {
        // Parametros de la llamada
        $params = array();

        // Ejecuto la llamada a la api
        $result = $this->apiCall('listsGet', $params);

        // Devuelvo resultados
        return $result->data;
    }

    /**
     * Obtiene una lista de distribución determinada del usuario.
     * @param  integer  $list_id ID de la lista de distribución.
     * @return stdClass          Lista de distribución.
     */
    public function listGet($list_id)
    {
        // Parametros de la llamada
        $params = array(
            'id' => $list_id
        );

        // Ejecuto la llamada a la api
        $result = $this->apiCall('listGet', $params);

        // Devuelvo resultados
        return $result->data;
    }

    /**
     * Agrega una nueva lista de distribución.
     * @param  string  $name        El nombre de la lista.
     * @param  string  $description La descripcion de la lista.
     * @param  integer $listorder   El 'sort order' de la lista, por ejemplo, 100.
     * @param  string  $prefix      Agrega un prefijo a la lista (?).
     * @param  string  $rssfeed     El url del RSS feed de la lista (?).
     * @param  integer $active      Si la lista esta activa, es 1. Si esta inactiva, es 0.
     * @return stdClass          Lista de distribución.
     */
    public function listAdd($name, $description, $listorder, $prefix, $rssfeed, $active)
    {
        // Parametros de la llamada
        $params = array(
            'name' => $name,
            'description' => $description,
            'listorder' => $listorder,
            'prefix' => $prefix,
            'rssfeed' => $rssfeed,
            'active' => $active
        );

        // Ejecuto la llamada a la api
        $result = $this->apiCall('listAdd', $params);

        // Devuelvo resultados
        return $result->data;
    }

    /**
     * Elimina una lista de distribución existente.
     * @param  integer $id          ID de la lista de distribución.
     * @return Boolean          True si se eliminó con exito, false en caso contrario.
     */
    public function listDelete($id)
    {
        // Parametros de la llamada
        $params = array('id' => $id);

        // Ejecuto la llamada a la api
        $result = $this->apiCall('listDelete', $params);

        // Devuelvo resultados
        return strtolower($result->status) == 'success';
    }

    /**
     * Actualiza una lista de distribución existente. $id y $name son parametros obligatorios.
     * @param  integer $id          ID de la lista de distribución.
     * @param  string  $name        El nombre de la lista.
     * @param  string  $description La descripcion de la lista.
     * @param  integer $listorder   El 'sort order' de la lista, por ejemplo, 100.
     * @param  string  $prefix      Agrega un prefijo a la lista (?).
     * @param  string  $rssfeed     El url del RSS feed de la lista (?).
     * @param  integer $active      Si la lista esta activa, es 1. Si esta inactiva, es 0.
     * @return stdClass          Lista de distribución ANTES de ser editada.
     */
    public function listUpdate($id, $name, $description, $listorder, $prefix, $rssfeed, $active)
    {
        // Parametros de la llamada
        $params = array(
            'id' => $id,
            'name' => $name,
            'description' => $description,
            'listorder' => $listorder,
            'prefix' => $prefix,
            'rssfeed' => $rssfeed,
            'active' => $active
        );

        // Ejecuto la llamada a la api
        $result = $this->apiCall('listUpdate', $params);

        // Devuelvo resultados
        return $result->data;
    }

    /**
     * Devuelve las listas de distribución a las que el suscriptor esta adherido.
     * @param  integer  $subscriber_id  ID del suscriptor.
     * @return array                    Listas de distribución a las que el suscriptor esta adherido.
     */
    public function listsSubscriber($subscriber_id)
    {
        // Parametros de la llamada
        $params = array(
            'subscriber_id' => $subscriber_id   // esta mal el nombre del parametro en la API (?)
        );

        // Ejecuto la llamada a la api
        $result = $this->apiCall('listsSubscriber', $params);

        // Devuelvo resultados
        return $result->data;
    }

    /**
     * Agrega un suscriptor existente a una lista de distribución.
     * @param  integer  $list_id        ID de la lista de distribución.
     * @param  integer  $subscriber_id  ID del suscriptor a agregar.
     * @return array                    Listas de distribución a las que el suscriptor esta adherido.
     */
    public function listSubscriberAdd($list_id, $subscriber_id)
    {
        // Parametros de la llamada
        $params = array(
            'list_id'       => $list_id,
            'subscriber_id' => $subscriber_id
        );

        // Ejecuto la llamada a la api
        $result = $this->apiCall('listSubscriberAdd', $params);

        // Devuelvo resultados
        return $result->data;
    }

    /**
     * Elimina un suscriptor de una lista de distribución.
     * @param  integer  $list_id        ID de la lista de distribución.
     * @param  integer  $subscriber_id  ID del suscriptor a eliminar.
     * @return boolean                  True si se eliminó con exito, false en caso contrario.
     */
    public function listSubscriberDelete($list_id, $subscriber_id)
    {
        // Parametros de la llamada
        $params = array(
            'list_id'       => $list_id,
            'subscriber_id' => $subscriber_id
        );

        // Ejecuto la llamada a la api
        $result = $this->apiCall('listSubscriberDelete', $params);

        // Devuelvo resultados
        return strtolower($result->status) == 'success';
    }

    /**
     * Devuelve la lista de TODOS los suscriptores del sistema.
     * @param  string $order_by Criterio por el cual ordenar los suscriptores; por defecto es 'id'.
     * @param  string $order    Criterio de ordenamiento; por defecto 'asc'.
     * @param  integer $limit   Cantidad de resultados a devolver, por defecto 100.
     * @return array            Suscriptores del sistema.
     */
    public function subscribersGet($order_by = 'id', $order = 'asc', $limit = 100)
    {
        // Parametros de la llamada
        $params = array(
            'order_by' => $order_by,
            'order' => $order,
            'limit' => $limit
        );

        // Ejecuto la llamada a la api
        $result = $this->apiCall('subscribersGet', $params);

        // Devuelvo resultados
        return $result->data;
    }

    /**
     * Devuelve los suscriptores de una determinada lista.
     * @param  string $list_id  Id de la lista de la cual se desea obtener los subscriptores.
     * @return array            Suscriptores del sistema.
     */
    public function subscribersGetByList($list_id)
    {
        // Parametros de la llamada
        $params = array(
            'list_id' => $list_id
        );

        // Ejecuto la llamada a la api
        $result = $this->apiCall('subscribersGetByList', $params);

        // Devuelvo resultados
        return $result->data;
    }

    /**
     * Devuelve un suscriptor por id.
     * @param  integer $subscriber_id  ID del suscriptor a obtener.
     * @return mixed        Suscriptor buscado.
     */
    public function subscriberGet($subscriber_id)
    {
        // Parametros de la llamada
        $params = array(
            'id' => $subscriber_id
        );

        // Ejecuto la llamada a la api
        $result = $this->apiCall('subscriberGet', $params);

        // Devuelvo resultados
        return $result->data;
    }

    /**
     * Devuelve la cantidad de TODOS los suscriptores del sistema.
     * @return integer    Cantidad de suscriptores del sistema.
     */
    public function subscribersCount()
    {
        // Parametros de la llamada
        $params = array();

        // Ejecuto la llamada a la api
        $result = $this->apiCall('subscribersCount', $params);

        // Devuelvo resultados
        return $result->data->total;
    }

    /**
     * Crea un nuevo suscriptor. NOTA: la creación NO implica que pertenezca a alguna lista de distribución.
     * @param  [string]  $email         La dirección de correo del suscriptor.
     * @param  [integer] $confirmed     1: confirmado, 0: no confirmado
     * @param  [integer] $htmlemail     1: correos html, 0: no correos html
     * @param  [integer] $rssfrequency  ???
     * @param  [string]  $password      Contraseña del suscriptor
     * @param  [integer] $disabled      1: deshabilitado, 0: habilitado
     * @return [mixed]                  El suscriptor agregado.
     */
    public function subscriberAdd($email, $confirmed, $htmlemail, $rssfrequency, $password, $disabled )
    {
         // Parametros de la llamada
        $params = array(
            'email'        => $email,
            'confirmed'    => $confirmed,
            'htmlemail'    => $htmlemail,
            'rssfrequency' => $rssfrequency,
            'password'     => $password,
            'disabled'     => $disabled
        );

        // Ejecuto la llamada a la api
        $result = $this->apiCall('subscriberAdd', $params);

        // Devuelvo resultados
        return $result->data;
    }

    /**
     * Actualiza un suscriptor.
     * @param  [integer] $id            ID del suscriptor a modificar.
     * @param  [string]  $email         La dirección de correo del suscriptor.
     * @param  [integer] $confirmed     1: confirmado, 0: no confirmado
     * @param  [integer] $htmlemail     1: correos html, 0: no correos html
     * @param  [integer] $rssfrequency  ???
     * @param  [string]  $password      Contraseña del suscriptor
     * @param  [integer] $disabled      1: deshabilitado, 0: habilitado
     * @return [mixed]                  El suscriptor modificado.
     */
    public function subscriberUpdate($id, $email, $confirmed, $htmlemail, $rssfrequency, $password, $disabled)
    {
         // Parametros de la llamada
        $params = array(
            'id'           => $id,
            'email'        => $email,
            'confirmed'    => $confirmed,
            'htmlemail'    => $htmlemail,
            'rssfrequency' => $rssfrequency,
            'password'     => $password,
            'disabled'     => $disabled
        );

        // Ejecuto la llamada a la api
        $result = $this->apiCall('subscriberUpdate', $params);

        // Devuelvo resultados
        return $result->data;
    }

    /**
     * Elimina un suscriptor.
     * @param  [integer] $id            ID del suscriptor a eliminar.
     * @return [boolean]                True si se elimino con éxito, false en caso contrario.
     */
    public function subscriberDelete($id)
    {
         // Parametros de la llamada
        $params = array('id' => $id);

        // Ejecuto la llamada a la api
        $result = $this->apiCall('subscriberDelete', $params);

        // Devuelvo resultados
        return strtolower($result->status) == 'success';
    }

    /**
     * Devuelve los templates existentes.
     * @return array Colección de templates.
     */
    public function templatesGet()
    {
        // Parametros de la llamada
        $params = array();

        // Ejecuto la llamada a la api
        $result = $this->apiCall('templatesGet', $params);

        // Devuelvo resultados
        return $result->data;
    }

    /**
     * Crea una nueva campaña (mensaje).
     * @param  string $subject       Asunto de la campaña
     * @param  string $fromfield     Remitente de la campaña (email)
     * @param  string $replyto       Receptor de respuestas (email)
     * @param  string $message       Cuerpo HTML del mensaje a ser incrustado en la plantilla.
     * @param  string $textmessage   Cuerpo en texto plano del mensaje
     * @param  string $footer        Pie del mensaje
     * @param  string $status        Estado del mensaje: submitted, draft, (faltan mas?)
     * @param  string $sendformat    Formato de envio del mensaje: both, (y los otros?)
     * @param  string $template      ID de la plantilla a utilizar con el mensaje
     * @param  string $embargo       Fecha a partir de la cual poner en cola el mensaje. Si es menor a la fecha actual, se pone en cola instantáneamente.
     * @param  string $rsstemplate   Plantilla RSS (?)
     * @param  string $owner         ID del usuario dueño de la campaña (administrador)
     * @param  string $htmlformatted 1: mensaje formateado en HTML, 0: ???
     * @return stdClass              El mensaje enviado.
     */
    public function messageAdd($subject, $fromfield, $replyto, $message,
        $textmessage, $footer, $status, $sendformat, $template, $embargo,
        $rsstemplate, $owner, $htmlformatted)
    {

        // Parametros de la llamada
        $params = array(
            'subject'       => $subject,
            'fromfield'     => $fromfield,
            'replyto'       => $replyto,
            'message'       => $message,
            'textmessage'   => $textmessage,
            'footer'        => $footer,
            'status'        => $status,
            'sendformat'    => $sendformat,
            'template'      => $template,
            'embargo'       => $embargo,
            'rsstemplate'   => $rsstemplate,
            'owner'         => $owner,
            'htmlformatted' => $htmlformatted
        );

        // Ejecuto la llamada a la api
        $result = $this->apiCall('messageAdd', $params);

        // Devuelvo resultados
        return $result->data;
    }

    /**
     * Devuelve el chequeo de visto para un determinado mensaje.
     * @param  integer $message_id  ID del mensaje a verificar.
     * @return mixed                Chequeo de mensaje.
     */
    public function messageGetViewStatus($message_id)
    {
        // Parametros de la llamada
        $params = array(
            'id' => $message_id
        );

        // Ejecuto la llamada a la api
        $result = $this->apiCall('messageGetViewStatus', $params);

        // Devuelvo resultados
        return $result->data;
    }

    /**
     * Asigna una lista a una campaña (mensaje).
     * @param  integer $list_id    ID de la lista de distribución.
     * @param  integer $message_id ID de la campaña (mensaje).
     * @return stdClass            La lista asignada.
     */
    public function listMessageAdd($list_id, $message_id)
    {
        // Parametros de la llamada
        $params = array(
            'list_id' => $list_id,
            'message_id' => $message_id
        );

        // Ejecuto la llamada a la api
        $result = $this->apiCall('listMessageAdd', $params);

        // Devuelvo resultados
        return $result->data;
    }

}

