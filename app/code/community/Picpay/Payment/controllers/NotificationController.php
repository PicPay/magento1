<?php
class Picpay_Payment_NotificationController extends Mage_Core_Controller_Front_Action{

    /**
     * Retrieves the helper
     *
     * @param string Helper alias
     * @return Picpay_Payment_Helper_Data
     */
    public function getHelper($type = 'picpay_payment')
    {
        return Mage::helper($type);
    }

    /**
     * Protected toJson response
     *
     * @param array $data Data to be json encoded
     * @param int $statusCode HTTP response status code
     * @return Zend_Controller_Response_Abstract
     */
    protected function _toJson($data = array(), $statusCode = 200)
    {
        return $this
            ->getResponse()
            ->setHeader('Content-type', 'application/json')
            ->setBody(Zend_Json::encode($data))
            ->setHttpResponseCode($statusCode);
    }

    /**
     * Public toJson response
     *
     * @param array $data
     * @param int $statusCode
     * @return Zend_Controller_Response_Abstract
     */
    public function toJson($data = array(), $statusCode = 200)
    {
        return $this->_toJson($data, $statusCode);
    }

    /**
     * Validate basic authorization before dispatching
     *
     * @return Picpay_Payment_NotificationController $this
     */
    public function preDispatch()
    {
        parent::preDispatch();

        // Make sure to run if module is enabled and active on system config
        if (!$this->getHelper()->isModuleEnabled()) {
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
            return $this->toJson(array('message' => 'Module disabled'), 400);
        }

        // Check HTTP method
        if(!$this->getRequest()->isPost()) {
            return $this->toJson(array('message' => 'Invalid HTTP Method'), 400);
        }

        // Notification Disabled
        if (!$this->getHelper()->isNotificationEnabled()) {
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
            return $this->toJson(array('message' => 'Notifications disabled'), 403);
        }

        // Validate authorization
        if (!$this->getHelper()->validateAuth($this->getRequest())) {
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
            return $this->toJson(array('message' => 'Authentication failed'), 403);
        }

        return $this;
    }

    /**
     * Normalize a request params based on content-type and methods
     *
     * @param Zend_Controller_Request_Http $request Request with data (raw body, json, form data, etc)
     * @param array $methods Accepted methods to normalize data
     * @return Zend_Controller_Request_Http
     * @throws Zend_Controller_Request_Exception
     * @throws Zend_Json_Exception
     */
    protected function _normalizeParams(Zend_Controller_Request_Http $request, $methods = array('PUT', 'POST'))
    {
        if (in_array($request->getMethod(), $methods) && 'application/json' == $request->getHeader('Content-Type')) {
            if (false !== ($body = $request->getRawBody())) {
                $this->getHelper()->log($body);

                try {
                    $body = str_replace("\t","",$body);
                    $body = str_replace("\r","",$body);
                    $body = str_replace("\n","",$body);
                    $data = Zend_Json::decode( $body );
                }
                catch (Exception $exception) {
                    Mage::logException($exception);
                    throw new Zend_Json_Exception($exception->getMessage());
                }

                $request->setParams($data);
            }
        }

        return $request;
    }

    /**
     * Action to handling notifications from PicPay
     *
     * @throws Zend_Controller_Request_Exception
     * @throws Zend_Json_Exception
     */
    public function indexAction()
    {
        $request = $this->_normalizeParams($this->getRequest());

        $referenceId = $request->get("referenceId");
        $referenceId = $request->get("authorizationId");
    }
}