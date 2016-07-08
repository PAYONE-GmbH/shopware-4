<?php

class Shopware_Controllers_Frontend_MoptPaymentEcs extends Shopware_Controllers_Frontend_Payment
{

    protected $moptPayone__serviceBuilder = null;
    protected $moptPayone__main = null;
    protected $moptPayone__helper = null;
    protected $moptPayone__paymentHelper = null;
    protected $admin;

    /**
     * init notification controller for processing status updates
     */
    public function init()
    {
        $this->moptPayone__serviceBuilder = $this->Plugin()->Application()->PayoneBuilder();
        $this->moptPayone__main = $this->Plugin()->Application()->PayoneMain();
        $this->moptPayone__helper = $this->moptPayone__main->getHelper();
        $this->moptPayone__paymentHelper = $this->moptPayone__main->getPaymentHelper();
        $this->admin = Shopware()->Modules()->Admin();

        $this->Front()->Plugins()->ViewRenderer()->setNoRender();
    }

    public function initPaymentAction()
    {
        $session = Shopware()->Session();
        $paymentId = $session->moptPaypayEcsPaymentId;
        $paramBuilder = $this->moptPayone__main->getParamBuilder();

        $userData = $this->getUserData();
        $amount = $this->getBasketAmount($userData);

        $expressCheckoutRequestData = $paramBuilder->buildPayPalExpressCheckout(
            $paymentId,
            $this->Front()->Router(),
            $amount,
            $this->getCurrencyShortName(),
            $userData
        );

        $request = new Payone_Api_Request_Genericpayment($expressCheckoutRequestData);

        $builder = $this->moptPayone__serviceBuilder;
        $service = $builder->buildServicePaymentGenericpayment();
        $service->getServiceProtocol()->addRepository(Shopware()->Models()->getRepository(
            'Shopware\CustomModels\MoptPayoneApiLog\MoptPayoneApiLog'
        ));

        // Response with new workorderid and redirect-url to paypal
        $response = $service->request($request);

        if ($response->getStatus() === Payone_Api_Enum_ResponseType::REDIRECT) {
            $session->moptPaypalEcsWorkerId = $response->getWorkorderId();
            $this->redirect($response->getRedirecturl());
        } else {
            return $this->forward('ecsAbort');
        }
    }

    /**
     * get plugin bootstrap
     *
     * @return plugin
     */
    protected function Plugin()
    {
        return Shopware()->Plugins()->Frontend()->MoptPaymentPayone();
    }

    /**
     * user returns succesfully from paypal
     * retrieve userdata now
     */
    public function ecsAction()
    {
        $session = Shopware()->Session();
        $paymentId = $session->moptPaypayEcsPaymentId;
        $paramBuilder = $this->moptPayone__main->getParamBuilder();


        $userData = $this->getUserData();
        $amount = $this->getBasketAmount($userData);

        $expressCheckoutRequestData = $paramBuilder->buildPayPalExpressCheckoutDetails(
            $paymentId,
            $this->Front()->Router(),
            $amount,
            $this->getCurrencyShortName(),
            $userData,
            $session->moptPaypalEcsWorkerId
        );

        $request = new Payone_Api_Request_Genericpayment($expressCheckoutRequestData);

        $builder = $this->moptPayone__serviceBuilder;
        $service = $builder->buildServicePaymentGenericpayment();
        $service->getServiceProtocol()->addRepository(Shopware()->Models()->getRepository(
            'Shopware\CustomModels\MoptPayoneApiLog\MoptPayoneApiLog'
        ));

        $response = $service->request($request);

        if ($response->getStatus() === Payone_Api_Enum_ResponseType::OK) {
            $this->createrOrUpdateAndForwardUser($response, $paymentId, $session);
        } elseif ($response->getStatus() === Payone_Api_Enum_ResponseType::REDIRECT) {
            $session->moptPaypalEcsWorkerId = $response->getWorkorderId();
            $this->redirect($response->getRedirecturl());
        } else {
            return $this->forward('ecsAbort');
        }
    }

    public function ecsAbortAction()
    {
        Shopware()->Session()->moptPayPalEcsError = true;
        return $this->redirect(array('controller' => 'checkout', 'action' => 'cart'));
    }

    /**
     * get complete user-data as array to use in view
     *
     * @return array
     */
    protected function getUserData()
    {
        $system = Shopware()->System();
        $userData = $this->admin->sGetUserData();
        if (!empty($userData['additional']['countryShipping'])) {
            $sTaxFree = false;
            if (!empty($userData['additional']['countryShipping']['taxfree'])) {
                $sTaxFree = true;
            } elseif (!empty($userData['additional']['countryShipping']['taxfree_ustid']) && !empty($userData['billingaddress']['ustid'])
            ) {
                $sTaxFree = true;
            }

            $system->sUSERGROUPDATA = Shopware()->Db()->fetchRow("
                SELECT * FROM s_core_customergroups
                WHERE groupkey = ?
            ", array($system->sUSERGROUP));

            if (!empty($sTaxFree)) {
                $system->sUSERGROUPDATA['tax'] = 0;
                $system->sCONFIG['sARTICLESOUTPUTNETTO'] = 1; //Old template
                Shopware()->Session()->sUserGroupData = $system->sUSERGROUPDATA;
                $userData['additional']['charge_vat'] = false;
                $userData['additional']['show_net'] = false;
                Shopware()->Session()->sOutputNet = true;
            } else {
                $userData['additional']['charge_vat'] = true;
                $userData['additional']['show_net'] = !empty($system->sUSERGROUPDATA['tax']);
                Shopware()->Session()->sOutputNet = empty($system->sUSERGROUPDATA['tax']);
            }
        }

        return $userData;
    }

    /**
     * Return the full amount to pay.
     *
     * @return float
     */
    protected function getBasketAmount($userData)
    {
        $basket = Shopware()->Modules()->Basket()->sGetBasket();

        if (!empty($userData['additional']['charge_vat'])) {
            return empty($basket['AmountWithTaxNumeric']) ? $basket['AmountNumeric'] : $basket['AmountWithTaxNumeric'];
        } else {
            return $basket['AmountNetNumeric'];
        }
    }

    protected function createrOrUpdateAndForwardUser($apiResponse, $paymentId, $session)
    {
        $payData = $apiResponse->getPaydata()->toAssocArray();

        if (!$this->isUserLoggedIn($session)) {
            $this->createUserWithoutAccount($payData, $session, $paymentId);
        } else {
            $user = $this->updateUserAddresses($payData, $session, $paymentId);
            if ($user === null) {
                return $this->ecsAbortAction();
            }
            $this->updatePaymentMethod($session, $paymentId);
        }
        $user = $this->getUserData();

        $user['sUserData']['additional']['charge_vat'] = true;
        $user['sUserData']["additional"]["user"]["paymentID"] = $paymentId;

        return $this->redirect(array('controller' => 'checkout', 'action' => 'confirm'));
    }

    protected function isUserLoggedIn($session)
    {
        return (isset($session->sUserId) && !empty($session->sUserId));
    }

    /**
     * create / register user without login
     */
    protected function createUserWithoutAccount($personalData, $session, $paymentId)
    {
        $register = $this->extractData($personalData);
        $register["payment"]["object"]["id"] = $paymentId;

        $session['sRegister'] = $register;
        $session['sRegisterFinished'] = false;

        $this->admin->sSaveRegister();
    }

    protected function updateUserAddresses($personalData, $session, $paymentId)
    {
        $personalData = $this->extractData($personalData);
        // use old phone number in case phone number is required
        if (Shopware()->Config()->get('requirePhoneField')) {
            $oldUserData = $this->admin->sGetUserData();
            $personalData['billing']['phone'] = $oldUserData['billingaddress']['phone'];
        }
        $updated = $this->updateBillingAddress($personalData, $session, $paymentId);
        if (!$updated) {
            return null;
        }
        $updated = $checkData = $this->updateShippingAddress($personalData, $session, $paymentId);
        if (!$updated) {
            return null;
        }
        return $personalData;
    }

    protected function updateBillingAddress($personalData, $session, $paymentId)
    {
        $countryData = $this->admin->sGetCountryList();
        $countryIds = array();
        foreach ($countryData as $key => $country) {
            $countryIds[$key] = $country['id'];
        }
        $this->admin->sSYSTEM->_POST = $personalData['billing'];
        $rules = array(
            'salutation' => array('required' => 1),
            'firstname' => array('required' => 1),
            'lastname' => array('required' => 1),
            'street' => array('required' => 1),
            'streetnumber' => array('required' => 1),
            'zipcode' => array('required' => 1),
            'city' => array('required' => 1),
            'phone' => array('required' => intval(Shopware()->Config()->get('requirePhoneField'))),
            'country' => array('required' => 1, 'in' => $countryIds)
        );
        $checkData = $this->admin->sValidateStep2($rules, true);
        if (!empty($checkData['sErrorMessages'])) {
            $this->View()->sErrorFlag = $checkData['sErrorFlag'];
            $this->View()->sErrorMessages = $checkData['sErrorMessages'];
            return false;
        } else {
            $this->admin->sUpdateBilling();
            return true;
        }
    }

    protected function updateShippingAddress($personalData, $session, $paymentId)
    {
        $rules = array(
            'salutation' => array('required' => 1),
            'firstname' => array('required' => 1),
            'lastname' => array('required' => 1),
            'street' => array('required' => 1),
            'streetnumber' => array('required' => 1),
            'zipcode' => array('required' => 1),
            'city' => array('required' => 1)
        );
        $this->admin->sSYSTEM->_POST = $personalData['shipping'];
        $checkData = $this->admin->sValidateStep2ShippingAddress($rules, true);
        if (!empty($checkData['sErrorMessages'])) {
            $this->View()->sErrorFlag = $checkData['sErrorFlag'];
            $this->View()->sErrorMessages = $checkData['sErrorMessages'];
            return false;
        } else {
            $this->admin->sUpdateShipping();
            return true;
        }
    }

    /**
     * get user-data as array from response
     *
     * @param array $personalData
     * @return array
     */
    protected function extractData($personalData)
    {
        $address = $this->moptPayone__helper->getSplittedAddress($personalData['shipping_street']);
        $register = array();
        $register['billing']['city'] = $personalData['shipping_city'];
        $register['billing']['country'] = $this->moptPayone__helper->getCountryIdFromIso($personalData['shipping_country']);
        if ($personalData['shipping_state'] !== 'Empty') {
            $register['billing']['stateID'] = $this->moptPayone__helper->getStateFromId($register['billing']['country'], $personalData['shipping_state']);
        }
        $register['billing']['street'] = $address[1];
        $register['billing']['streetnumber'] = $address[2];
        $register['billing']['zipcode'] = $personalData['shipping_zip'];
        $register['billing']['firstname'] = $personalData['shipping_firstname'];
        $register['billing']['lastname'] = $personalData['shipping_lastname'];
        $register['billing']['salutation'] = 'mr';
        if (isset($personalData['shipping_company']) && !empty($personalData['shipping_company'])) {
            $register['billing']['company'] = $personalData['shipping_company'];
        } else {
            $register['billing']['company'] = '';
            $register['personal']['customer_type'] = 'private';
        }
        $register['personal']['email'] = $personalData['email'];
        $register['personal']['firstname'] = $personalData['shipping_firstname'];
        $register['personal']['lastname'] = $personalData['shipping_lastname'];
        $register['personal']['salutation'] = 'mr';
        $register['personal']['skipLogin'] = 1;
        $register['shipping']['salutation'] = 'mr';
        $register['shipping']['firstname'] = $register['billing']['firstname'];
        $register['shipping']['lastname'] = $register['billing']['lastname'];
        $register['shipping']['street'] = $register['billing']['street'];
        $register['shipping']['streetnumber'] = $register['billing']['streetnumber'];
        $register['shipping']['zipcode'] = $register['billing']['zipcode'];
        $register['shipping']['city'] = $register['billing']['city'];
        $register['shipping']['country'] = $register['billing']['country'];
        if ($personalData['shipping_state'] !== 'Empty') {
            $register['shipping']['stateID'] = $register['billing']['stateID'];
        }
        $register['shipping']['company'] = $register['billing']['company'];
        $register['shipping']['department'] = '';
        $register['auth']['email'] = $personalData['email'];
        $register['auth']['password'] = md5(uniqid('', true));
        $register['auth']['accountmode'] = 1;
        $register['auth']['encoderName'] = '';
        return $register;
    }

    protected function updatePaymentMethod($session, $paymentId)
    {
        $userId = $session->offsetGet('sUserId');
        
        $sqlPayment = "UPDATE s_user SET paymentID = ? WHERE id = ?";

        Shopware()->Db()->query(
            $sqlPayment,
            array(
            $paymentId,
            $userId
                )
        );
    }
}
