<?php

/**
 * this class configures:
 * installment, uninstallment, updates, hooks, events, payment methods
 */
class Shopware_Plugins_Frontend_MoptPaymentPayone_Bootstrap extends Shopware_Components_Plugin_Bootstrap
{

    /**
     * Payone helper
     * @var Mopt_PayoneInstallHelper
     */
    protected $moptPayoneInstallHelper = null;

    /**
     * Payone payment helper
     * @var Mopt_PayonePaymentHelper
     */
    protected $moptPayonePaymentHelper = null;

    /**
     * registers the custom plugin models and plugin namespaces
     */
    public function afterInit()
    {
        $this->registerCustomModels();
        $this->Application()->Loader()->registerNamespace(
            'Payone',
            $this->Path() . 'Components/Payone/'
        );
        $this->Application()->Snippets()->addConfigDir($this->Path() . 'snippets/');
        $this->Application()->Loader()->registerNamespace('Mopt', $this->Path() . 'Components/Classes/');
    }

    /**
     * perform all neccessary install tasks
     *
     * @return boolean
     */
    public function install()
    {
        $this->moptPayoneInstallHelper = new Mopt_PayoneInstallHelper();

        $this->createEvents();
        $this->createPayments();
        $this->createDatabase();
        $this->addAttributes();
        $this->createMenu();
        $this->removePayment('mopt_payone__fin_klarna_installment');

        return array('success' => true, 'invalidateCache' => array('backend', 'proxy'));
    }

    /**
     * perform all neccessary uninstall tasks and return true if successful
     *
     * @return boolean
     */
    public function uninstall($deleteModels = false, $removeAttributes = false)
    {
        if ($deleteModels) {
            $em = $this->Application()->Models();
            $platform = $em->getConnection()->getDatabasePlatform();
            $platform->registerDoctrineTypeMapping('enum', 'string');
            $tool = new \Doctrine\ORM\Tools\SchemaTool($em);

            $tool->dropSchema(array(
                $em->getClassMetadata('Shopware\CustomModels\MoptPayoneTransactionLog\MoptPayoneTransactionLog')
            ));
            $tool->dropSchema(array($em->getClassMetadata('Shopware\CustomModels\MoptPayoneApiLog\MoptPayoneApiLog')));
            $tool->dropSchema(array($em->getClassMetadata('Shopware\CustomModels\MoptPayoneConfig\MoptPayoneConfig')));
        }

        if ($removeAttributes) {
            Shopware()->Models()->removeAttribute('s_user_attributes', 'mopt_payone', 'consumerscore_result');
            Shopware()->Models()->removeAttribute('s_user_attributes', 'mopt_payone', 'consumerscore_date');
            Shopware()->Models()->removeAttribute('s_user_attributes', 'mopt_payone', 'consumerscore_color');
            Shopware()->Models()->removeAttribute('s_user_attributes', 'mopt_payone', 'consumerscore_value');
            Shopware()->Models()->removeAttribute('s_user_billingaddress_attributes', 'mopt_payone', 'addresscheck_result');
            Shopware()->Models()->removeAttribute('s_user_billingaddress_attributes', 'mopt_payone', 'addresscheck_date');
            Shopware()->Models()->removeAttribute('s_user_billingaddress_attributes', 'mopt_payone', 'addresscheck_personstatus');
            Shopware()->Models()->removeAttribute('s_user_billingaddress_attributes', 'mopt_payone', 'consumerscore_result');
            Shopware()->Models()->removeAttribute('s_user_billingaddress_attributes', 'mopt_payone', 'consumerscore_date');
            Shopware()->Models()->removeAttribute('s_user_billingaddress_attributes', 'mopt_payone', 'consumerscore_color');
            Shopware()->Models()->removeAttribute('s_user_billingaddress_attributes', 'mopt_payone', 'consumerscore_value');
            Shopware()->Models()->removeAttribute('s_user_shippingaddress_attributes', 'mopt_payone', 'addresscheck_result');
            Shopware()->Models()->removeAttribute('s_user_shippingaddress_attributes', 'mopt_payone', 'addresscheck_date');
            Shopware()->Models()->removeAttribute('s_user_shippingaddress_attributes', 'mopt_payone', 'addresscheck_personstatus');
            Shopware()->Models()->removeAttribute('s_user_shippingaddress_attributes', 'mopt_payone', 'consumerscore_color');
            Shopware()->Models()->removeAttribute('s_user_shippingaddress_attributes', 'mopt_payone', 'consumerscore_value');
            Shopware()->Models()->removeAttribute('s_order_attributes', 'mopt_payone', 'txid');
            Shopware()->Models()->removeAttribute('s_order_attributes', 'mopt_payone', 'status');
            Shopware()->Models()->removeAttribute('s_order_attributes', 'mopt_payone', 'sequencenumber');
            Shopware()->Models()->removeAttribute('s_order_attributes', 'mopt_payone', 'is_authorized');
            Shopware()->Models()->removeAttribute('s_order_attributes', 'mopt_payone', 'is_finally_captured');
            Shopware()->Models()->removeAttribute('s_order_attributes', 'mopt_payone', 'clearing_data', 'text');
            Shopware()->Models()->removeAttribute('s_order_details_attributes', 'mopt_payone', 'payment_status');
            Shopware()->Models()->removeAttribute('s_order_details_attributes', 'mopt_payone', 'shipment_date');
            Shopware()->Models()->removeAttribute('s_order_details_attributes', 'mopt_payone', 'captured');
            Shopware()->Models()->removeAttribute('s_order_details_attributes', 'mopt_payone', 'debit');
            Shopware()->Models()->removeAttribute('s_order_attributes', 'mopt_payone', 'payolution_workorder_id');
            Shopware()->Models()->removeAttribute('s_order_attributes', 'mopt_payone', 'payolution_clearing_reference');

            Shopware()->Models()->generateAttributeModels(
                array(
                        's_user_attributes',
                        's_core_paymentmeans_attributes',
                        's_user_billingaddress_attributes',
                        's_user_shippingaddress_attributes',
                        's_order_attributes',
                        's_order_details_attributes',
                    )
            );
        }

        return true;
    }

    /**
     * Remove payment instance
     *
     * @param string $paymentName
     *
     */
    public function removePayment($paymentName)
    {
        $payment = $this->Payments()->findOneBy(
            array(
                'name' => $paymentName
            )
        );
        if ($payment === null) {
        // do nothing

        } else {
            Shopware()->Models()->remove($payment);
            Shopware()->Models()->flush();
        }
    }

    /**
     * update plugin, check previous versions
     *
     * @param type $oldVersion
     */
    public function update($oldVersion)
    {
        //extra handling for early beta version
        if (strpos($oldVersion, '0.0.') === 0) {
            $this->uninstall(true);
            $this->install();

            return true;
        }

        $this->checkAndDeleteOldLogs();
        $this->install();

        return true;
    }

    /**
     * @return boolean
     */
    public function enable()
    {
        return true;
    }

    /**
     * @return boolean
     */
    public function disable()
    {
        return true;
    }

    public function getCapabilities()
    {
        return array(
            'install' => true,
            'update' => true,
            'enable' => true
        );
    }

    /**
     * Returns the informations of plugin as array.
     *
     * @return array
     */
    public function getInfo()
    {
        $logo = base64_encode(file_get_contents(dirname(__FILE__) . '/logo.png'));
        $info = json_decode(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'plugin.json'), true);
        return array(
            'label' => $this->getLabel(),
            'author' => $info['author'],
            'copyright' => $info['copyright'],
            'link' => $info['link'],
            'support' => $info['support'],
            'version' => $this->getVersion(),
            'description' => '<p><img src="data:image/png;base64,' . $logo . '" /></p> '
            . file_get_contents(__DIR__ . '/description.txt'),
            'solution_name' => $this->getSolutionName()
        );
    }

    /**
     * Returns the version of plugin as string.
     *
     * @return string
     */
    public function getVersion()
    {
        $info = json_decode(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'plugin.json'), true);

        if ($info) {
            return $info['currentVersion'];
        } else {
            throw new Exception('The plugin has an invalid version file.');
        }
    }

    public function getLabel()
    {
        $info = json_decode(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'plugin.json'), true);

        if ($info) {
            return $info['label']['de'];
        } else {
            throw new Exception('The plugin has an invalid version file.');
        }
    }
    
    public function getSolutionName()
    {
        $info = json_decode(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'plugin.json'), true);

        if ($info) {
            return $info['solution_name'];
        } else {
            throw new Exception('The plugin has an invalid version file.');
        }
    }

    /**
     * register all subscriber classes for event subscriptions
     */
    protected function createEvents()
    {
        $this->moptRegisterControllers();

        $this->subscribeEvent(
            'Enlight_Controller_Action_PostDispatch',
            'onPostDispatch',
            110
        );

        $this->subscribeEvent(
            'Enlight_Bootstrap_InitResource_PayoneBuilder',
            'onInitResourcePayoneBuilder'
        );

        $this->subscribeEvent(
            'Enlight_Bootstrap_InitResource_PayoneMain',
            'onInitResourcePayoneMain'
        );

        //risk management:Backend options
        $this->subscribeEvent(
            'Enlight_Controller_Action_PostDispatch_Backend_RiskManagement',
            'onBackendRiskManagementPostDispatch'
        );

        //risk management:Frontend extend sAdmin - implement sRisk<MOPT_RISK> methods called by sManageRisks
        $this->subscribeEvent('sAdmin::sManageRisks::replace', 'sAdmin__sManageRisks');

        // group creditcard payments
        $this->subscribeEvent(
            'sAdmin::sGetPaymentMeans::after',
            'onGetPaymentMeans'
        );

        // hook for addresscheck
        $this->subscribeEvent(
            'sAdmin::sValidateStep2::after',
            'onValidateStep2'
        );

        // hook for saving addresscheck result
        $this->subscribeEvent(
            'sAdmin::sUpdateBilling::after',
            'onUpdateBilling'
        );

        // hook for saving addresscheck result during registration process
        $this->subscribeEvent(
            'Shopware_Controllers_Frontend_Register::saveRegister::after',
            'onUpdateBilling'
        );

        // hook for shipmentaddresscheck
        $this->subscribeEvent(
            'sAdmin::sValidateStep2ShippingAddress::after',
            'onValidateStep2ShippingAddress'
        );

        // hook for saving shipmentaddresscheck result
        $this->subscribeEvent(
            'sAdmin::sUpdateShipping::after',
            'onUpdateShipping'
        );

        // hook for saving shipping ddresscheck result during registration process
        $this->subscribeEvent(
            'Shopware_Controllers_Frontend_Register::saveRegister::after',
            'onUpdateShipping'
        );

        // save paymentdata
        $this->subscribeEvent(
            'sAdmin::sValidateStep3::after',
            'onValidateStep3'
        );

        // hook for saving consumerscorecheck result
        $this->subscribeEvent(
            'sAdmin::sUpdatePayment::after',
            'onUpdatePayment'
        );

        // hook for getting dispatch basket, used to calculate correct shipment costs for credit card payments
        $this->subscribeEvent(
            'sAdmin::sGetDispatchBasket::after',
            'onGetDispatchBasket'
        );

        // load payment data from db to use for payment
        $this->subscribeEvent(
            'Shopware_Controllers_Frontend_Account::paymentAction::after',
            'onPaymentAction'
        );

        // load stored payment data for payment method overview
        $this->subscribeEvent(
            'Shopware_Controllers_Frontend_Checkout::getSelectedPayment::after',
            'onGetSelectedPaymentAfter'
        );
        // load stored payment data for payment method overview
        $this->subscribeEvent(
            'Shopware_Controllers_Frontend_Checkout::getSelectedPayment::before',
            'onGetSelectedPaymentBefore'
        );

        // check if addresscheck and consumerscore are valid if activated
        $this->subscribeEvent(
            'Shopware_Controllers_Frontend_Checkout::confirmAction::after',
            'onConfirmAction'
        );

        // extend backend order-overview
        $this->subscribeEvent('Enlight_Controller_Action_PostDispatch_Backend_Order', 'moptExtendController_Backend_Order');

        // extend backend payment configuration
        $this->subscribeEvent('Enlight_Controller_Action_PostDispatch_Backend_Payment', 'moptExtendController_Backend_Payment');

        //add payone fields to list results
        $this->subscribeEvent('Shopware_Controllers_Backend_Order::getList::after', 'Order__getList__after');

        // add PAYONE data to pdf
        $this->subscribeEvent(
            'Shopware_Components_Document::assignValues::after',
            'onBeforeRenderDocument'
        );

        //add clearing data to email
        $this->subscribeEvent(
            'Shopware_Modules_Order_SendMail_FilterVariables',
            'onSendMailFilterVariablesFilter'
        );

        // save terms agreement handling
        $this->subscribeEvent('Enlight_Controller_Action_PostDispatch_Frontend_Checkout', 'moptExtendController_Frontend_Checkout');
    }

    /**
     * register all needed frontend and backend controllers
     */
    protected function moptRegisterControllers()
    {
        //Frontend
        $this->subscribeEvent(
            'Enlight_Controller_Dispatcher_ControllerPath_Frontend_MoptPaymentPayone',
            'onGetControllerPathFrontendMoptPaymentPayone'
        );

        $this->subscribeEvent(
            'Enlight_Controller_Dispatcher_ControllerPath_Frontend_MoptShopNotification',
            'moptRegisterController_Frontend_MoptShopNotification'
        );

        $this->subscribeEvent(
            'Enlight_Controller_Dispatcher_ControllerPath_Frontend_MoptPaymentEcs',
            'moptRegisterController_Frontend_MoptPaymentEcs'
        );

        //Backend
        $this->subscribeEvent(
            'Enlight_Controller_Dispatcher_ControllerPath_Backend_MoptConfigPayone',
            'onGetConfigControllerBackend'
        );

        $this->subscribeEvent(
            'Enlight_Controller_Dispatcher_ControllerPath_Backend_MoptApilogPayone',
            'onGetApilogControllerBackend'
        );

        $this->subscribeEvent(
            'Enlight_Controller_Dispatcher_ControllerPath_Backend_MoptPayoneTransactionLog',
            'onGetTransactionLogControllerBackend'
        );

        $this->subscribeEvent(
            'Enlight_Controller_Dispatcher_ControllerPath_Backend_MoptSupportPayone',
            'onGetSupportControllerBackend'
        );

        $this->subscribeEvent(
            'Enlight_Controller_Dispatcher_ControllerPath_Backend_MoptPayoneOrder',
            'moptRegisterController_Backend_MoptPayoneOrder'
        );

        $this->subscribeEvent(
            'Enlight_Controller_Dispatcher_ControllerPath_Backend_MoptPayonePayment',
            'moptRegisterController_Backend_MoptPayonePayment'
        );

        $this->subscribeEvent(
            'Enlight_Controller_Dispatcher_ControllerPath_Backend_MoptExportPayone',
            'onGetBackendExportController'
        );

        $this->subscribeEvent(
            'Enlight_Controller_Dispatcher_ControllerPath_Backend_MoptPayonePaypal',
            'onGetBackendControllerPaypal'
        );

        $this->subscribeEvent(
            'Enlight_Controller_Dispatcher_ControllerPath_Backend_MoptPayonePaypalOld',
            'onGetBackendControllerPaypalOld'
        );

        $this->subscribeEvent(
            'Enlight_Controller_Dispatcher_ControllerPath_Backend_MoptPayoneCreditcardConfig',
            'onGetBackendControllerCreditcardConfig'
        );

        $this->subscribeEvent(
            'Enlight_Controller_Dispatcher_ControllerPath_Backend_MoptPayoneCreditcardConfigOld',
            'onGetBackendControllerCreditcardConfigOld'
        );
    }

    /**
     * controller callback, return path to controller file
     *
     * @return string
     */
    public function moptRegisterController_Backend_MoptPayoneOrder()
    {
        $this->Application()->Snippets()->addConfigDir($this->Path() . 'snippets/');
        return $this->Path() . 'Controllers/Backend/MoptPayoneOrder.php';
    }

    /**
     * controller callback, return path to controller file
     *
     * @return string
     */
    public function moptRegisterController_Backend_MoptPayonePayment()
    {
        $this->Application()->Snippets()->addConfigDir($this->Path() . 'snippets/');
        return $this->Path() . 'Controllers/Backend/MoptPayonePayment.php';
    }

    /**
     * controller callback, return path to controller file
     *
     * @return string
     */
    public function moptRegisterController_Frontend_MoptShopNotification()
    {
        $this->Application()->Snippets()->addConfigDir($this->Path() . 'snippets/');
        return $this->Path() . 'Controllers/Frontend/MoptShopNotification.php';
    }

    /**
     * controller callback, return path to controller file
     *
     * @return string
     */
    public function moptRegisterController_Frontend_MoptPaymentEcs()
    {
        $this->Application()->Snippets()->addConfigDir($this->Path() . 'snippets/');
        return $this->Path() . 'Controllers/Frontend/MoptPaymentEcs.php';
    }

    /**
     * complete replacement - refactor when shopware has a better implementation
     * @parent-arguments: $paymentID, $basket, $user
     * maybe: generally call parent for non-payone paymentIDs
     *
     * @param Enlight_Hook_HookArgs $arguments
     * @return boolean
     */
    public function sAdmin__sManageRisks(Enlight_Hook_HookArgs $arguments)
    {
        $me = $arguments->getSubject();

        $paymentID = $arguments->get('paymentID');
        $basket = $arguments->get('basket');
        $user = $arguments->get('user');
        $moptPayoneMain = $this->Application()->PayoneMain();
        $paymentName = $moptPayoneMain->getPaymentHelper()->getPaymentNameFromId($paymentID);

        if ($moptPayoneMain->getPaymentHelper()->isPayonePaymentMethod($paymentName)) {
            $isPayoneMethod = true;
        } else {
            $isPayoneMethod = false;
        }

        // disable consumerscore check if user is not logged in, payments are already loaded at addArticle (to basket)
        if (!$user['additional']['user']['id']) {
            $isPayoneMethod = false;
        }

        //perform consumerScoreCheck if configured
        $config = $moptPayoneMain->getPayoneConfig($paymentID);

        if (!$config['adresscheckActive'] && !$config['consumerscoreActive']) {
            $payoneRiskChecksActive = false;
        } else {
            $payoneRiskChecksActive = true;
        }

        if ($isPayoneMethod && $config['consumerscoreActive'] && $config['consumerscoreCheckMoment'] == 0) {
            //get user data
            $userData = $user['additional']['user'];

            $billingAddressData = $user['billingaddress'];

            if ($isPayoneMethod) {
                $userCconsumerScoreData = $moptPayoneMain->getHelper()->getConsumerScoreDataFromUserId($user['additional']['user']['id']);
            }

            if ($isPayoneMethod && !$moptPayoneMain->getHelper()->isCosumerScoreCheckValid($config['consumerscoreLifetime'], $userCconsumerScoreData['moptPayoneConsumerscoreResult'], $userCconsumerScoreData['moptPayoneConsumerscoreDate'])) {
                $params = $moptPayoneMain->getParamBuilder()->getConsumerscoreCheckParams($billingAddressData, $paymentID);
                $response = $this->performConsumerScoreCheck($config, $params, $this->Application()->PayoneBuilder());
                //set result to userdata
                $userData['moptPayoneConsumerscoreResult'] = $response->getStatus();
                $userData['moptPayoneConsumerscoreDate'] = date('Y-m-d');

                if (!$this->handleConsumerScoreCheckResult($response, $config, $userData['id'])) {
                    //abort
                    $arguments->setReturn(true);
                    return;
                }
            }
        }

        // Get all assigned rules
        $queryRules = $me->sSYSTEM->sDB_CONNECTION->GetAll("
            SELECT rule1, value1, rule2, value2
            FROM s_core_rulesets
            WHERE paymentID = ?
            ORDER BY id ASC
		", array($paymentID));

        if (empty($queryRules)) {
            $result = $me->executeParent('sManageRisks', array($paymentID, $basket, $user));
            $arguments->setReturn($result);
            return;
        }

        if (empty($basket)) {
            $session = Shopware()->Session();
            $basket = array(
                'content' => $session->sBasketQuantity,
                'AmountNumeric' => $session->sBasketAmount
            );
        }

        foreach ($queryRules as $rule) {
            if ($rule["rule1"] && !$rule["rule2"]) {
                $rule["rule1"] = "sRisk" . $rule["rule1"];

                if (strpos($rule["rule1"], 'sRiskMOPT_PAYONE__') === 0) {
                    if ($payoneRiskChecksActive && $this->$rule["rule1"]($user, $basket, $rule["value1"], $paymentID)) {
                        $arguments->setReturn(true);
                        return;
                    }
                } elseif ($me->$rule["rule1"]($user, $basket, $rule["value1"])) {
                    $arguments->setReturn(true);
                    return;
                }
            } elseif ($rule["rule1"] && $rule["rule2"]) {
                $rule["rule1"] = "sRisk" . $rule["rule1"];
                $rule["rule2"] = "sRisk" . $rule["rule2"];

                if (strpos($rule["rule1"], 'sRiskMOPT_PAYONE__') === 0) {
                    $result1 = $payoneRiskChecksActive && $this->$rule["rule1"]($user, $basket, $rule["value1"], $paymentID);
                } else {
                    $result1 = $me->$rule["rule1"]($user, $basket, $rule["value1"]);
                }

                if (strpos($rule["rule2"], 'sRiskMOPT_PAYONE__') === 0) {
                    $result2 = $payoneRiskChecksActive && $this->$rule["rule2"]($user, $basket, $rule["value2"], $paymentID);
                } else {
                    $result2 = $me->$rule["rule2"]($user, $basket, $rule["value2"]);
                }

                // AND
                if ($result1 && $result2) {
                    $arguments->setReturn(true);
                    return;
                }
            }
        }

        $arguments->setReturn(false);
        return;
    }

    /**
     * @parent-arguments $user,$order,$value
     *
     * @param Enlight_Hook_HookArgs $arguments
     */
    public function sRiskMOPT_PAYONE__TRAFFIC_LIGHT_IS($user, $order, $value, $paymentID)
    {
        $payoneMain = $this->Application()->PayoneMain();
        $config = $payoneMain->getPayoneConfig($paymentID);
        $scoring = $payoneMain->getHelper()->getScoreFromUserAccordingToPaymentConfig($user, $config);

        return $scoring == $value; //return true if payment has to be denied
    }

    /**
     * @parent-arguments $user,$order,$value
     *
     * @param Enlight_Hook_HookArgs $arguments
     */
    public function sRiskMOPT_PAYONE__TRAFFIC_LIGHT_IS_NOT($user, $order, $value, $paymentID)
    {
        $payoneMain = $this->Application()->PayoneMain();
        $config = $payoneMain->getPayoneConfig($paymentID);
        $scoring = $payoneMain->getHelper()->getScoreFromUserAccordingToPaymentConfig($user, $config);

        return $scoring != $value;
    }

    /**
     * group creditcard payments
     *
     * @param Enlight_Hook_HookArgs $arguments
     */
    public function onGetPaymentMeans(Enlight_Hook_HookArgs $arguments)
    {
        $dontGroupCreditCardActions = array('addArticle', 'cart', 'changeQuantity', 'confirm');
        $request = Shopware()->Front()->Request();
        $actionName = $request->getActionName();
        $controllerName = $request->getControllerName();
        $targetAction = $request->getParam('sTargetAction');

        if ($controllerName === 'checkout') {
            return;
        }

        if (in_array($actionName, $dontGroupCreditCardActions)) {
            return;
        }

        if ($actionName == 'calculateShippingCosts' && $targetAction == 'cart') {
            return;
        }

        $paymentMeansWithGroupedCreditcard = $this->groupCreditcards($arguments->getReturn());

        if (!$paymentMeansWithGroupedCreditcard) {
            return;
        }

        $arguments->setReturn($paymentMeansWithGroupedCreditcard);
    }

    /**
     * assign saved paymend data to view
     *
     * @param Enlight_Hook_HookArgs $arguments
     */
    public function onPaymentAction(Enlight_Hook_HookArgs $arguments)
    {
        $subject = $arguments->getSubject();
        $userId = Shopware()->Session()->sUserId;

        $sql = 'SELECT `moptPaymentData` FROM s_plugin_mopt_payone_payment_data WHERE userId = ?';
        $paymentData = unserialize(Shopware()->Db()->fetchOne($sql, $userId));

        if (!empty($paymentData)) {
            //get array of creditcard payment ids
            $sql = "SELECT `id` FROM s_core_paymentmeans WHERE name LIKE '%mopt_payone__cc_%' ";
            $creditcardIds = Shopware()->Db()->fetchAll($sql);

            foreach ($creditcardIds as $creditcardId) {
                // check if active id is in array
                if ($creditcardId['id'] == $subject->View()->sFormData['payment']) {
                    // set creditcard active
                    $paymentData['payment'] = 'mopt_payone_creditcard';
                    $subject->View()->sFormData = $paymentData;
                    break;
                } else {
                    $subject->View()->sFormData += $paymentData;
                }
            }
        }
    }

    /**
     * prevent payment method reset
     *
     * @param Enlight_Hook_HookArgs $arguments
     * @return type
     */
    public function onGetSelectedPaymentBefore(Enlight_Hook_HookArgs $arguments)
    {
        $action = Shopware()->Modules()->Admin()->sSYSTEM->_GET['action'];
        $sTargetAction = Shopware()->Modules()->Admin()->sSYSTEM->_GET['sTargetAction'];
        $subject = $arguments->getSubject();

        if ($action == 'addArticle' || $action == 'cart' || $action == 'changeQuantity') {
            return;
        }

        if ($action == 'calculateShippingCosts' && $sTargetAction == 'cart') {
            return;
        }

        $paymentHelper = $this->Application()->PayoneMain()->getPaymentHelper();

        $view = $subject->View();
        if (!empty($view->sUserData['additional']['payment'])) {
            $payment = $view->sUserData['additional']['payment'];

            if (!$paymentHelper->isPayonePaymentMethod($payment['name'])) {
                return;
            }

            if ($payment['id'] === 'mopt_payone_creditcard') {
                $userData = $view->sUserData;
                $userData['additional']['payment']['id'] = $view->sUserData['additional']['payment']['data']['mopt_payone__cc_paymentid'];
                $view->assign('sUserData', $userData);
            }
        }
    }

    /**
     * assign saved payment data to view
     *
     * @param Enlight_Hook_HookArgs $arguments
     * @return type
     */
    public function onGetSelectedPaymentAfter(Enlight_Hook_HookArgs $arguments)
    {
        $action = Shopware()->Modules()->Admin()->sSYSTEM->_GET['action'];
        $sTargetAction = Shopware()->Modules()->Admin()->sSYSTEM->_GET['sTargetAction'];

        if ($action == 'addArticle' || $action == 'cart' || $action == 'changeQuantity') {
            return;
        }

        if ($action == 'calculateShippingCosts' && $sTargetAction == 'cart') {
            return;
        }

        $ret = $arguments->getReturn();
        $paymentHelper = $this->Application()->PayoneMain()->getPaymentHelper();

        if (!$paymentHelper->isPayonePaymentMethod($ret['name'])) {
            return;
        }

        $userId = Shopware()->Session()->sUserId;

        $sql = 'SELECT `moptPaymentData` FROM s_plugin_mopt_payone_payment_data WHERE userId = ?';
        $paymentData = unserialize(Shopware()->Db()->fetchOne($sql, $userId));

        $ret['data'] = $paymentData;

        //save payment data to session for later use during actual payment process
        Shopware()->Session()->moptPayment = $paymentData;

        //special handling for creditCards
        //registerFinished means the user hat just finished his registration, thats why the payment form is displayed
        //to choose a payment method
        $registerFinished = !empty(Shopware()->Session()->sRegisterFinished);
        if ($registerFinished && $paymentHelper->isPayoneCreditcardNotGrouped($ret['name'])) {
            $ret['id'] = 'mopt_payone_creditcard';
        }

        $arguments->setReturn($ret);
    }

    /**
     * perform risk checks
     *
     * @param Enlight_Hook_HookArgs $arguments
     * @return type
     */
    public function onConfirmAction(Enlight_Hook_HookArgs $arguments)
    {
        $subject = $arguments->getSubject();

        //group credit cards for payment form
        $groupedPaymentMeans = $this->groupCreditcards($subject->View()->sPayments);
        if ($groupedPaymentMeans) {
            $subject->View()->sPayments = $groupedPaymentMeans;
        }

        //return if non payone method is choosen
        if (!$this->Application()->PayoneMain()->getPaymentHelper()->isPayonePaymentMethod($subject->View()->sPayment['name'])) {
            return;
        }
        //get config by payment id
        $moptPayoneMain = $this->Application()->PayoneMain();
        $paymentId = $subject->View()->sPayment['id'];
        $config = $moptPayoneMain->getPayoneConfig($paymentId);
        $basketValue = $subject->View()->sAmount;
        $userData = $subject->View()->sUserData;
        $billingAddressData = $userData['billingaddress'];
        $billingAddressData['country'] = $billingAddressData['countryID'];
        $shippingAddressData = $userData['shippingaddress'];
        $shippingAddressData['country'] = $shippingAddressData['countryID'];
        $session = Shopware()->Session();
        $userId = $session->sUserId;

        //check if addresscheck is active for billingadress and check
        $billingAddressChecktype = $moptPayoneMain->getHelper()->isBillingAddressToBeCheckedWithBasketValue($config, $basketValue, $billingAddressData['country']);

        if ($session->moptAddressCheckNeedsUserVerification) {
            $billingAddressChecktype = false;
        }
        $userBillingAddressCheckData = $moptPayoneMain->getHelper()->getBillingAddresscheckDataFromUserId($userId);
        if ($billingAddressChecktype && !$moptPayoneMain->getHelper()->isBillingAddressCheckValid($config['adresscheckLifetime'], $userBillingAddressCheckData['moptPayoneAddresscheckResult'], $userBillingAddressCheckData['moptPayoneAddresscheckDate'])) {
            //perform check
            $params = $moptPayoneMain->getParamBuilder()->getAddressCheckParams($billingAddressData, $billingAddressData, $paymentId);
            $response = $this->performAddressCheck($config, $params, $this->Application()->PayoneBuilder(), $moptPayoneMain, $billingAddressChecktype);

            //handle result
            $errors = $this->handleBillingAddressCheckResult($response, $config, $userId, $subject, $billingAddressData);
            if (!empty($errors['sErrorFlag'])) {
                $ret['sErrorFlag'] = $errors['sErrorFlag'];
                $ret['sErrorMessages'] = $errors['sErrorMessages'];

                $arguments->setReturn($ret);
                return;
            }
        }

        //get shippingaddress attributes
        $shippingAttributes = $moptPayoneMain->getHelper()->getShippingAddressAttributesFromUserId($userId);
        //check if addresscheck is active for shippingadress and data is valid and check if necessary
        $shippingAddressChecktype = $moptPayoneMain->getHelper()->isShippingAddressToBeCheckedWithBasketValue($config, $basketValue, $shippingAddressData['country']);

        if ($session->moptAddressCheckNeedsUserVerification) {
            $shippingAddressChecktype = false;
        }

        if ($shippingAddressChecktype && !$moptPayoneMain->getHelper()->isShippingAddressCheckValid($config['adresscheckLifetime'], $shippingAttributes['moptPayoneAddresscheckResult'], $shippingAttributes['moptPayoneAddresscheckDate'])) {
            //perform check
            $params = $moptPayoneMain->getParamBuilder()->getAddressCheckParams($shippingAddressData, $shippingAddressData, $paymentId);
            $response = $this->performAddressCheck($config, $params, $this->Application()->PayoneBuilder(), $moptPayoneMain, $shippingAddressChecktype);

            //handle result
            $errors = $this->handleShippingAddressCheckResult($response, $config, $userId, $subject, $shippingAddressData);
            if (!empty($errors['sErrorFlag'])) {
                $ret['sErrorFlag'] = $errors['sErrorFlag'];
                $ret['sErrorMessages'] = $errors['sErrorMessages'];

                $arguments->setReturn($ret);
                return;
            }
        }

        //check if consumerscore is active and check if necessary
        $cosumerScoreChecktype = $moptPayoneMain->getHelper()->isConsumerScoreToBeCheckedWithBasketValue($config, $basketValue);
        $userConsumerScoreData = $moptPayoneMain->getHelper()->getConsumerScoreDataFromUserId($userId);
        if ($cosumerScoreChecktype && !$moptPayoneMain->getHelper()->isCosumerScoreCheckValid($config['consumerscoreLifetime'], $userConsumerScoreData['moptPayoneConsumerscoreResult'], $userConsumerScoreData['moptPayoneConsumerscoreDate'])) {

            //perform check if prechoice is configured
            if ($config['consumerscoreCheckMoment'] == 0) {
                $params = $moptPayoneMain->getParamBuilder()->getConsumerscoreCheckParams($billingAddressData, $paymentId);
                $response = $this->performConsumerScoreCheck($config, $params, $this->Application()->PayoneBuilder());
                if (!$this->handleConsumerScoreCheckResult($response, $config, $userId)) {
                    //cancel, redirect to payment choice
                    $subject->forward('payment', 'account', null, array('sTarget' => 'checkout'));
                }
            } else {
                //set sessionflag if after paymentchoice is configured
                $session->moptConsumerScoreCheckNeedsUserAgreement = true;
                $session->moptPaymentId = $subject->View()->sPayment['id'];
            }
        }
    }
    
    /**
     * billingaddress addresscheck
     *
     * @param Enlight_Hook_HookArgs $arguments
     */
    public function onValidateStep2(Enlight_Hook_HookArgs $arguments)
    {
        $ret = $arguments->getReturn();

        if (!empty($ret['sErrorMessages'])) {
            $arguments->setReturn($ret);
            return;
        }

        //get config data from main
        $moptPayoneMain = $this->Application()->PayoneMain();
        $config = $moptPayoneMain->getPayoneConfig();
        $session = Shopware()->Session();
        $postData = Shopware()->Front()->Request()->getPost();

        //get basket value
        $basket = Shopware()->Modules()->Basket()->sGetBasket();
        $basketValue = $basket['AmountNumeric'];

        //perform check if addresscheck is enabled
        $billingAddressChecktype = $moptPayoneMain->getHelper()->isBillingAddressToBeChecked($config, $postData['register']['billing']['country']);

        if ($session->moptPayoneBillingAddresscheckResult) {
            $billingAddressChecktype = false;
        }

        if ($billingAddressChecktype) {
            //if nothing in basket, don't check and  just reset the validation date and result
            if (!$basketValue) {
                $userId = $session->sUserId;
                $moptPayoneMain->getHelper()->resetAddressCheckData($userId);
                return;
            }

            //no check when basket value outside configured values
            if ($basketValue < $config['adresscheckMinBasket'] || $basketValue > $config['adresscheckMaxBasket']) {
                return;
            } else {
                $billingFormData = $postData['register']['billing'];
                $personalFormData = $postData['register']['personal'];

                $params = $moptPayoneMain->getParamBuilder()->getAddressCheckParams($billingFormData, $personalFormData);
                $response = $this->performAddressCheck($config, $params, $this->Application()->PayoneBuilder(), $moptPayoneMain, $billingAddressChecktype);

                if ($response->getStatus() == 'VALID') {
                    $session = Shopware()->Session();
                    $secStatus = (int) $response->getSecstatus();
                    //check secstatus and config
                    if ($secStatus == 10) {
                        //valid address returned -> save result to session
                        $session->moptPayoneBillingAddresscheckResult = serialize($response);
                    } else {
                        //secstatus must be 20 - corrected address returned
                        switch ($config['adresscheckAutomaticCorrection']) {
                            case 0: //auto correction
                                {
                                    //this works only for address changes via account controller
                                    Shopware()->Modules()->Admin()->sSYSTEM->_POST['street'] = $response->getStreetname();
                                    Shopware()->Modules()->Admin()->sSYSTEM->_POST['streetnumber'] = $response->getStreetnumber();
                                    Shopware()->Modules()->Admin()->sSYSTEM->_POST['zipcode'] = $response->getZip();
                                    Shopware()->Modules()->Admin()->sSYSTEM->_POST['city'] = $response->getCity();

                                    $session->moptPayoneBillingAddresscheckResult = serialize($response);
                            }
                                break;
                            case 1: // no correction
                                {
                                    $session->moptPayoneBillingAddresscheckResult = serialize($response);
                            }
                                break;
                            case 2: //depends on user
                                {
                                    // add addressdata to template
                                    $session->moptAddressCheckNeedsUserVerification = true;
                                    $session->moptAddressCheckOriginalAddress = $billingFormData;
                                    $session->moptAddressCheckCorrectedAddress = serialize($response);
                            }
                                break;
                        }
                    }

                    $arguments->setReturn($ret);
                    return;
                }
                if ($response->getStatus() == 'INVALID' || $response->getStatus() == 'ERROR') {
                    $moptPayoneMain->getHelper()->saveBillingAddressError($userId, $response);
                    $session->moptPayoneBillingAddresscheckResult = serialize($response);

                    $request = $this->Application()->Front()->Request(); // used to forward user
                    switch ($config['adresscheckFailureHandling']) {
                        case 0: //cancel transaction -> redirect to address input
                            {
                                $this->forward($request, 'billing', 'account', null, array('sTarget' => 'checkout'));
                                $arguments->setReturn($ret);
                                return;
                        }
                            break;
                        case 1: // reenter address -> redirect to address form
                            {
                                $ret['sErrorFlag']['mopt_payone_configured_message'] = true;
                                $ret['sErrorMessages']['mopt_payone_configured_message'] = $moptPayoneMain->getPaymentHelper()
                                        ->moptGetErrorMessageFromErrorCodeViaSnippet('addresscheck', $response->getErrorcode());

                            if (Shopware()->Modules()->Admin()->sCheckUser()) {
                                $this->forward($request, 'billing', 'account', null, array('sTarget' => 'checkout'));
                                $arguments->setReturn($ret);
                                return;
                            } else {
                                $this->forward($request, 'index', 'register', null, array('sTarget' => 'checkout'));
                                $arguments->setReturn($ret);
                                return;
                            }
                        }
                        break;
                        case 2: // perform consumerscore check
                            {
                                $billingFormData['countryID'] = $billingFormData['country'];
                                $params = $moptPayoneMain->getParamBuilder()->getConsumerscoreCheckParams($billingFormData);
                                $response = $this->performConsumerScoreCheck($config, $params, $this->Application()->PayoneBuilder());
                            if (!$this->handleConsumerScoreCheckResult($response, $config, $userId)) {
                                $this->forward($request, 'billing', 'account', null, array('sTarget' => 'checkout'));
                                return;
                            }
                        }
                        break;
                        case 3: // proceed
                            {
                                return;
                        }
                            break;
                    }

                    $arguments->setReturn($ret);
                    return;
                }
            }
        }

        $arguments->setReturn($ret);
    }

    /**
     * save addresscheck result
     *
     * @param Enlight_Hook_HookArgs $arguments
     */
    public function onUpdateBilling(Enlight_Hook_HookArgs $arguments)
    {
        $session = Shopware()->Session();

        if (!($result = unserialize($session->moptPayoneBillingAddresscheckResult))) {
            return;
        }

        $userId = $session->sUserId;
        $moptPayoneMain = $this->Application()->PayoneMain();
        $config = $moptPayoneMain->getPayoneConfig();

        if ($result->getStatus() === 'INVALID' || $result->getStatus() === 'ERROR') {
            $moptPayoneMain->getHelper()->saveBillingAddressError($userId, $result);
        } else {
            if ($result->getStatus() === 'VALID' && $result->getSecstatus() === '20' && $config['adresscheckAutomaticCorrection'] === 0 && Shopware()->Modules()->Admin()->sSYSTEM->_GET['action'] === 'saveRegister') {
                $moptPayoneMain->getHelper()->saveCorrectedBillingAddress($userId, $result);
            }
            $mappedPersonStatus = $moptPayoneMain->getHelper()->getUserScoringValue($result->getPersonstatus(), $config);
            $mappedPersonStatus = $moptPayoneMain->getHelper()->getUserScoringColorFromValue($mappedPersonStatus);
            $moptPayoneMain->getHelper()->saveBillingAddressCheckResult($userId, $result, $mappedPersonStatus);
        }

        unset($session->moptPayoneBillingAddresscheckResult);
    }

    /**
     *
     * shipmentaddress addresscheck
     *
     * @param Enlight_Hook_HookArgs $arguments
     */
    public function onValidateStep2ShippingAddress(Enlight_Hook_HookArgs $arguments)
    {
        $returnValues = $arguments->getReturn();

        if (!empty($returnValues['sErrorMessages'])) {
            return;
        }

        //get config data from main
        $moptPayoneMain = $this->Application()->PayoneMain();
        $config = $moptPayoneMain->getPayoneConfig();
        $postData = Shopware()->Front()->Request()->getPost();
        $shippingAddressCountry = $moptPayoneMain->getHelper()->getAddressCountryFromUserData($postData, true);

        //check if addresscheck is enabled
        if ($config['adresscheckActive']) {
            $shippingAddressChecktype = $moptPayoneMain->getHelper()
                    ->getAddressChecktypeFromId($config['adresscheckShippingAdress'], $config['adresscheckShippingCountries'], $shippingAddressCountry);

            //return if shipping address checkmode is set to "no check"
            if (!$shippingAddressChecktype) {
                return;
            }

            if (isset($postData['sSelectAddress'])) {
                return;
            }

            $session = Shopware()->Session();
            $userId = $session->sUserId;
            $shippingFormData = $postData['register']['shipping'];
            $params = $moptPayoneMain->getParamBuilder()
                    ->getAddressCheckParams($shippingFormData, $shippingFormData);
            $response = $this->performAddressCheck($config, $params, $this->Application()->PayoneBuilder(), $moptPayoneMain, $shippingAddressChecktype);

            if ($response->getStatus() == 'VALID') {
                $secStatus = (int) $response->getSecstatus();
                if ($secStatus == 10) {
                    //valid address returned, save result to session
                    $session->moptPayoneShippingAddresscheckResult = serialize($response);
                } else {
                    //secstatus must be 20 - corrected address returned
                    switch ($config['adresscheckAutomaticCorrection']) {
                        case 0: //auto correction
                            {
                                $session->moptPayoneShippingAddresscheckResult = serialize($response);
                                Shopware()->Modules()->Admin()->sSYSTEM->_POST['street'] = $response->getStreetname();
                                Shopware()->Modules()->Admin()->sSYSTEM->_POST['streetnumber'] = $response->getStreetnumber();
                                Shopware()->Modules()->Admin()->sSYSTEM->_POST['zipcode'] = $response->getZip();
                                Shopware()->Modules()->Admin()->sSYSTEM->_POST['city'] = $response->getCity();
                        }
                            break;
                        case 1: // no correction
                            {
                                $session->moptPayoneShippingAddresscheckResult = serialize($response);
                        }
                            break;
                        case 2: //depends on user
                            {
                                // add addressdata to template
                                $session->moptShippingAddressCheckNeedsUserVerification = true;
                                $session->moptShippingAddressCheckOriginalAddress = $shippingFormData;
                                $session->moptShippingAddressCheckCorrectedAddress = serialize($response);
                        }
                            break;
                    }
                }

                //save corrected address or status to session in onUpdateShipping
                $arguments->setReturn($returnValues);
                return;
            }
            if ($response->getStatus() == 'INVALID' || $response->getStatus() == 'ERROR') {
                $returnValues['sErrorFlag']['mopt_payone_configured_message'] = true;
                $returnValues['sErrorMessages']['mopt_payone_configured_message'] = $moptPayoneMain->getPaymentHelper()
                        ->moptGetErrorMessageFromErrorCodeViaSnippet('addresscheck', $response->getErrorcode());

                $request = $this->Application()->Front()->Request(); // used to forward user
                $session->moptPayoneShippingAddresscheckResult = serialize($response);

                switch ($config['adresscheckFailureHandling']) {
                    case 0: //cancel transaction -> redirect to payment choice
                        {
                            $arguments->setReturn($returnValues);
                            $this->forward($request, 'index', 'account', null, array('sTarget' => 'checkout'));
                            return;
                    }
                        break;
                    case 1: // reenter address -> redirect to address form
                        {
                        if (Shopware()->Modules()->Admin()->sCheckUser()) {
                            $this->forward($request, 'billing', 'account', null, array('sTarget' => 'checkout'));
                            $arguments->setReturn($returnValues);
                            return;
                        } else {
                            $this->forward($request, 'index', 'register', null, array('sTarget' => 'checkout'));
                            $arguments->setReturn($returnValues);
                            return;
                        }
                    }
                    break;
                    case 2: // perform consumerscore check
                        {
                            $shippingFormData['countryID'] = $shippingFormData['country'];
                            $params = $moptPayoneMain->getParamBuilder()
                                    ->getConsumerscoreCheckParams($shippingFormData);
                            $response = $this->performConsumerScoreCheck($config, $params, $this->Application()->PayoneBuilder());
                        if (!$this->handleConsumerScoreCheckResult($response, $config, $userId)) {
                            //cancel transaction
                            $arguments->setReturn($returnValues);
                            $this->forward($request, 'index', 'account', null, array('sTarget' => 'checkout'));
                            return;
                        }
                            unset($returnValues['sErrorFlag']['mopt_payone_addresscheck']);
                            unset($returnValues['sErrorMessages']['mopt_payone_addresscheck']);
                        return;
                    }
                        break;
                    case 3: // proceed
                        {
                            return;
                    }
                        break;
                }


                $arguments->setReturn($returnValues);
                return;
            }
        }

        $arguments->setReturn($returnValues);
        return;
    }

    /**
     * save addresscheck result
     *
     * @param Enlight_Hook_HookArgs $arguments
     */
    public function onUpdateShipping(Enlight_Hook_HookArgs $arguments)
    {
        $session = Shopware()->Session();

        if (!($result = unserialize($session->moptPayoneShippingAddresscheckResult))) {
            return;
        }

        $userId = $session->sUserId;
        $moptPayoneMain = $this->Application()->PayoneMain();
        $config = $moptPayoneMain->getPayoneConfig();

        if ($result->getStatus() == 'INVALID' || $result->getStatus() == 'ERROR') {
            $moptPayoneMain->getHelper()->saveShippingAddressError($userId, $result);
        } else {
            if ($result->getStatus() === 'VALID' && $result->getSecstatus() === '20' && $config['adresscheckAutomaticCorrection'] === 0 && Shopware()->Modules()->Admin()->sSYSTEM->_GET['action'] === 'saveRegister') {
                $moptPayoneMain->getHelper()->saveCorrectedShippingAddress($userId, $result);
            }

            $mappedPersonStatus = $moptPayoneMain->getHelper()->getUserScoringValue($result->getPersonstatus(), $config);
            $mappedPersonStatus = $moptPayoneMain->getHelper()->getUserScoringColorFromValue($mappedPersonStatus);
            $moptPayoneMain->getHelper()->saveShippingAddressCheckResult($userId, $result, $mappedPersonStatus);
        }
        unset($session->moptPayoneShippingAddresscheckResult);
    }

    /**
     * consumerscore check after choice if payment method
     *
     * @param Enlight_Hook_HookArgs $arguments
     */
    public function onValidateStep3(Enlight_Hook_HookArgs $arguments)
    {
        $returnValues = $arguments->getReturn();
        if (!empty($returnValues['sErrorMessages'])) {
            return;
        }

        $userId = Shopware()->Session()->sUserId;
        $postData = Shopware()->Front()->Request()->getPost();
        $post = $postData['moptPaymentData'];
        $post['mopt_payone__cc_Year'] = $postData['mopt_payone__cc_Year'];
        $post['mopt_payone__klarna_Year'] = $postData['mopt_payone__klarna_Year'];
        $post['mopt_payone__klarna_Month'] = $postData['mopt_payone__klarna_Month'];
        $post['mopt_payone__klarna_Day'] = $postData['mopt_payone__klarna_Day'];
        $paymentName = $returnValues['paymentData']['name'];
        $paymentId = $postData['register']['payment'];
        $moptPayoneMain = $this->Application()->PayoneMain();
        $config = $moptPayoneMain->getPayoneConfig($paymentId);
        $session = Shopware()->Session();

        if ($config['saveTerms'] !== 0) {
            if (Shopware()->Front()->Request()->getParam('sAGB') === '1') {
                $session->moptAgbChecked = true;
            }
            if (Shopware()->Front()->Request()->getParam('sAGB') === '0') {
                $session->moptAgbChecked = false;
            }
        }

        //check if payone payment method, exit if not and delete pament data
        if (!$moptPayoneMain->getPaymentHelper()->isPayonePaymentMethod($paymentName)) {
            $moptPayoneMain->getPaymentHelper()->deletePaymentData($userId);
            unset($session->moptMandateData);
            return;
        }

        //@TODO check if still used
        if ($postData['register']['payment'] === 'mopt_payone_creditcard') {
            $paymentName = $postData['register']['payment'];
        }

        $paymentData = $moptPayoneMain->getFormHandler()
                ->processPaymentForm($paymentName, $post, $moptPayoneMain->getPaymentHelper());

        if (isset($paymentData['formData']['mopt_save_birthday_and_phone']) && $paymentData['formData']['mopt_save_birthday_and_phone']) {
            $moptPayoneMain->getPaymentHelper()->moptUpdateUserInformation($userId, $paymentData);
        }

        if (isset($paymentData['formData']['mopt_save_birthday']) && $paymentData['formData']['mopt_save_birthday']) {
            $moptPayoneMain->getPaymentHelper()->moptUpdateUserInformation($userId, $paymentData);
        }

        if (count($paymentData['sErrorFlag'])) {
            $error = true;
            $moptPayoneMain->getPaymentHelper()->deletePaymentData($userId);
        }

        if ($error) {
            $sErrorMessages[] = Shopware()->Snippets()
                            ->getNamespace('frontend/account/internalMessages')->get('ErrorFillIn', 'Please fill in all red fields');
            $returnValues['checkPayment']['sErrorFlag'] = $paymentData['sErrorFlag'];
            $returnValues['checkPayment']['sErrorMessages'] = $sErrorMessages;
        } else {
            //cleanup session
            unset($session->moptMandateData);

            //get user data
            $user = Shopware()->Modules()->Admin()->sGetUserData();
            $userData = $user['additional']['user'];
            $billingFormData = $user['billingaddress'];

            if ($moptPayoneMain->getPaymentHelper()->isPayoneDebitnote($returnValues['paymentData']['name'])) {
                //check if bankaccountcheck is enabled
                $bankAccountChecktype = $moptPayoneMain->getHelper()->getBankAccountCheckType($config);

                //check if manage mandate is enabled
                if ($config['mandateActive']) {
                    //perform bankaccountcheck
                    $params = $moptPayoneMain->getParamBuilder()->buildManageMandate($paymentId, $user, $paymentData['formData']);

                    $payoneServiceBuilder = $this->Application()->PayoneBuilder();
                    $service = $payoneServiceBuilder->buildServiceManagementManageMandate();
                    $service->getServiceProtocol()->addRepository(Shopware()->Models()->getRepository(
                        'Shopware\CustomModels\MoptPayoneApiLog\MoptPayoneApiLog'
                    ));

                    $request = new Payone_Api_Request_ManageMandate($params);
                    $response = $service->managemandate($request);

                    if ($response->getStatus() == 'APPROVED') {
                        $moptMandateData = array();
                        $moptMandateData['mopt_payone__showMandateText'] = false;

                        if ($response->getMandateStatus() === 'pending') {
                            $moptMandateData['mopt_payone__showMandateText'] = true;
                            $moptMandateData['mopt_payone__mandateText'] = urldecode($response->getMandateText());
                        }

                        $moptMandateData['mopt_payone__mandateStatus'] = $response->getMandateStatus();
                        $moptMandateData['mopt_payone__mandateIdentification'] = $response->getMandateIdentification();
                        $moptMandateData['mopt_payone__creditorIdentifier'] = $response->getCreditorIdentifier();

                        $session->moptMandateData = $moptMandateData;
                    }

                    if ($response->getStatus() == 'ERROR') {
                        $returnValues['checkPayment']['sErrorFlag']['mopt_payone__account_invalid'] = true;
                        $returnValues['checkPayment']['sErrorMessages']['mopt_payone__account_invalid'] = $moptPayoneMain
                                        ->getPaymentHelper()->moptGetErrorMessageFromErrorCodeViaSnippet('bankaccountcheck', $response->getErrorcode());
                        $session->moptPayment = $post;
                        $arguments->setReturn($returnValues);
                        return;
                    }
                } elseif ($bankAccountChecktype === 0 || $bankAccountChecktype === 1) {
                    //perform bankaccountcheck
                    $params = $moptPayoneMain->getParamBuilder()->buildBankaccountcheck($paymentId, $bankAccountChecktype, $billingFormData['countryID'], $paymentData['formData']);

                    $payoneServiceBuilder = $this->Application()->PayoneBuilder();
                    $service = $payoneServiceBuilder->buildServiceVerificationBankAccountCheck();
                    $service->getServiceProtocol()->addRepository(Shopware()->Models()->getRepository(
                        'Shopware\CustomModels\MoptPayoneApiLog\MoptPayoneApiLog'
                    ));

                    $request = new Payone_Api_Request_BankAccountCheck($params);
                    $response = $service->check($request);

                    if ($response->getStatus() == 'ERROR' || $response->getStatus() == 'INVALID') {
                        $returnValues['checkPayment']['sErrorFlag']['mopt_payone__account_invalid'] = true;
                        $returnValues['checkPayment']['sErrorMessages']['mopt_payone__account_invalid'] = $moptPayoneMain
                                ->getPaymentHelper()
                                ->moptGetErrorMessageFromErrorCodeViaSnippet('bankaccountcheck', $response->getErrorcode());

                        $session->moptPayment = $post;
                        $arguments->setReturn($returnValues);
                        return;
                    }

                    if ($response->getStatus() == 'BLOCKED') {
                        $returnValues['checkPayment']['sErrorFlag']['mopt_payone__account_invalid'] = true;
                        $returnValues['checkPayment']['sErrorMessages']['mopt_payone__account_invalid'] = $moptPayoneMain
                                ->getPaymentHelper()
                                ->moptGetErrorMessageFromErrorCodeViaSnippet('bankaccountcheck', 'blocked');
                        $session->moptPayment = $post;
                        $arguments->setReturn($returnValues);
                        return;
                    }
                }
            }

            if ($config['consumerscoreActive'] && $config['consumerscoreCheckMoment'] == 1) {
                //check if consumerscore is still valid or needs to be checked
                if (!$moptPayoneMain->getHelper()->isCosumerScoreCheckValid($config['consumerscoreLifetime'], $userData['moptPayoneConsumerscoreResult'], $userData['moptPayoneConsumerscoreDate'])) {
                    // add flag and data to session
                    $session->moptConsumerScoreCheckNeedsUserAgreement = true;
                    $session->moptPaymentData = $paymentData;
                    $session->moptPaymentId = $paymentId;
                    //@TODO submit target
                }
            }

            //save data to table and session
            $session->moptPayment = $post;
            if (!$moptPayoneMain->getPaymentHelper()->isPayoneCreditcard($paymentId)) {
                $moptPayoneMain->getPaymentHelper()->savePaymentData($userId, $paymentData);
            }
        }

        $arguments->setReturn($returnValues);
    }

    public function onUpdatePayment(Enlight_Hook_HookArgs $arguments)
    {
        $session = Shopware()->Session();

        if (!($result = unserialize($session->moptPayoneConsumerscorecheckResult))) {
            return;
        }

        $mopt_payone__main = $this->Application()->PayoneMain();
        $mopt_payone__main->getHelper()->saveConsumerScoreCheckResult($session->sUserId, $result);

        unset($session->moptPayoneConsumerscorecheckResult);
    }

    /**
     * special handling for grouped credit cards to calculate correct shipment costs
     *
     * @param Enlight_Hook_HookArgs $arguments
     */
    public function onGetDispatchBasket(Enlight_Hook_HookArgs $arguments)
    {
        $returnValues = $arguments->getReturn();

        $user = $arguments->getSubject()->sGetUserData();

        if (empty($user['additional']['payment']['id'])) {
            return;
        }

        if ($this->Application()->PayoneMain()->getPaymentHelper()->isPayoneCreditcardNotGrouped($user['additional']['payment']['name'])) {
            $returnValues['paymentID'] = $user['additional']['payment']['id'];
            $arguments->setReturn($returnValues);
        }
    }

    protected function performAddressCheck($config, $params, $payoneServiceBuilder, $mopt_payone__main, $billingAddressChecktype)
    {
        $service = $payoneServiceBuilder->buildServiceVerificationAddressCheck();
        $service->getServiceProtocol()->addRepository(Shopware()->Models()->getRepository(
            'Shopware\CustomModels\MoptPayoneApiLog\MoptPayoneApiLog'
        ));

        $request = new Payone_Api_Request_AddressCheck($params);

        $request->setAddresschecktype($billingAddressChecktype);
        $request->setAid($config['subaccountId']);
        $request->setMode($mopt_payone__main->getHelper()->getApiModeFromId($config['adresscheckLiveMode']));

        $response = $service->check($request);
        return $response;
    }

    protected function performConsumerScoreCheck($config, $params, $payoneServiceBuilder)
    {
        $service = $payoneServiceBuilder->buildServiceVerificationConsumerscore();
        $service->getServiceProtocol()->addRepository(Shopware()->Models()->getRepository(
            'Shopware\CustomModels\MoptPayoneApiLog\MoptPayoneApiLog'
        ));

        $request = new Payone_Api_Request_Consumerscore($params);

        $billingAddressChecktype = Payone_Api_Enum_AddressCheckType::NONE;
        $request->setAddresschecktype($billingAddressChecktype);
        $request->setConsumerscoretype($config['consumerscoreCheckMode']);

        $response = $service->score($request);
        return $response;
    }

    protected function handleBillingAddressCheckResult($response, $config, $userId, $caller, $billingAddressData)
    {
        $ret = array();
        $moptPayoneMain = $this->Application()->PayoneMain();

        if ($response->getStatus() == Payone_Api_Enum_ResponseType::VALID) {
            $secStatus = (int) $response->getSecstatus();
            $mappedPersonStatus = $moptPayoneMain->getHelper()->getUserScoringValue($response->getPersonstatus(), $config);
            $mappedPersonStatus = $moptPayoneMain->getHelper()->getUserScoringColorFromValue($mappedPersonStatus);
            //check secstatus and config
            if ($secStatus == Payone_Api_Enum_AddressCheckSecstatus::ADDRESS_CORRECT) {
                //valid address returned -> save result to db
                $moptPayoneMain->getHelper()->saveBillingAddressCheckResult($userId, $response, $mappedPersonStatus);
            } else {
                //secstatus must be 20 - corrected address returned
                switch ($config['adresscheckAutomaticCorrection']) {
                    case 0: //auto correction
                        {
                            //save result to db
                            $moptPayoneMain->getHelper()->saveCorrectedBillingAddress($userId, $response);
                            $moptPayoneMain->getHelper()->saveBillingAddressCheckResult($userId, $response, $mappedPersonStatus);
                    }
                        break;
                    case 1: // no correction
                        {
                            //save result to db
                            $moptPayoneMain->getHelper()->saveBillingAddressCheckResult($userId, $response, $mappedPersonStatus);
                    }
                        break;
                    case 2: //depends on user
                        {
                            // add errormessage
                            $ret['sErrorFlag']['mopt_payone_configured_message'] = true;
                            $ret['sErrorMessages']['mopt_payone_configured_message'] = $moptPayoneMain
                                            ->getPaymentHelper()->moptGetErrorMessageFromErrorCodeViaSnippet('addresscheck');
                            $moptPayoneMain->getHelper()->saveBillingAddressCheckResult($userId, $response, $mappedPersonStatus);
                            $session->moptAddressCheckNeedsUserVerification = true;
                            $session->moptAddressCheckOriginalAddress = $billingAddressData;
                            $session->moptAddressCheckCorrectedAddress = serialize($response);
                            $caller->forward('confirm', 'checkout', null, array('moptAddressCheckNeedsUserVerification' => true, 'moptAddressCheckOriginalAddress' => $billingAddressData, 'moptAddressCheckCorrectedAddress' => serialize($response), 'moptAddressCheckTarget' => 'checkout'));
                    }
                        break;
                }
            }
        } else {
            $moptPayoneMain->getHelper()->saveBillingAddressError($userId, $response);

            switch ($config['adresscheckFailureHandling']) {
                case 0: //cancel transaction -> redirect to payment choice
                    {
                        $caller->forward('payment', 'account', null, array('sTarget' => 'checkout'));
                }
                    break;
                case 1: // reenter address -> redirect to address form
                    {
                        $caller->forward('billing', 'account', null, array('sTarget' => 'checkout'));
                }
                    break;
                case 2: // perform consumerscore check
                    {
                        $params = $moptPayoneMain->getParamBuilder()->getConsumerscoreCheckParams($billingAddressData, $config['paymentId']);
                        $response = $this->performConsumerScoreCheck($config, $params, $this->Application()->PayoneBuilder());
                        $this->handleConsumerScoreCheckResult($response);
                }
                    break;
            }
        }

        return $ret;
    }

    protected function handleShippingAddressCheckResult($response, $config, $userId, $subject, $shippingAddressData)
    {
        $ret = array();
        $moptPayoneMain = $this->Application()->PayoneMain();
        if ($response->getStatus() == Payone_Api_Enum_ResponseType::VALID) {
            $secStatus = (int) $response->getSecstatus();
            $mappedPersonStatus = $moptPayoneMain->getHelper()->getUserScoringValue($response->getPersonstatus(), $config);
            $mappedPersonStatus = $moptPayoneMain->getHelper()->getUserScoringColorFromValue($mappedPersonStatus);
            //check secstatus and config
            if ($secStatus == Payone_Api_Enum_AddressCheckSecstatus::ADDRESS_CORRECT) {
                //valid address returned -> save result to db
                $moptPayoneMain->getHelper()->saveShippingAddressCheckResult($userId, $response, $mappedPersonStatus);
            } else {
                //secstatus must be 20 - corrected address returned
                switch ($config['adresscheckAutomaticCorrection']) {
                    case 0: //auto correction
                        {
                            //save result to db
                            $moptPayoneMain->getHelper()->saveCorrectedShippingAddress($userId, $response);
                            $moptPayoneMain->getHelper()->saveShippingAddressCheckResult($userId, $response, $mappedPersonStatus);
                    }
                        break;
                    case 1: // no correction
                        {
                            //save result to db
                            $moptPayoneMain->getHelper()->saveShippingAddressCheckResult($userId, $response, $mappedPersonStatus);
                    }
                        break;
                    case 2: //depends on user
                        {
                            // add errormessage
                            $ret['sErrorFlag']['mopt_payone_configured_message'] = true;
                            $ret['sErrorFlag']['mopt_payone_corrected_message'] = true;
                            $ret['sErrorMessages']['mopt_payone_configured_message'] = $moptPayoneMain
                                            ->getPaymentHelper()->moptGetErrorMessageFromErrorCodeViaSnippet('addresscheck');
                            $ret['sErrorMessages']['mopt_payone_corrected_message'] = $moptPayoneMain
                                            ->getPaymentHelper()->moptGetErrorMessageFromErrorCodeViaSnippet('addresscheck', 'corrected');
                            //add decisionbox to template
                            $session->moptShippingAddressCheckNeedsUserVerification = true;
                            $session->moptShippingAddressCheckOriginalAddress = $shippingAddressData;
                            $session->moptShippingAddressCheckCorrectedAddress = serialize($response);
                            $subject->forward('confirm', 'checkout', null, array('moptShippingAddressCheckNeedsUserVerification' => true,
                                'moptShippingAddressCheckOriginalAddress' => $shippingAddressData,
                                'moptShippingAddressCheckCorrectedAddress' => serialize($response),
                                'moptShippingAddressCheckTarget' => 'checkout'));
                    }
                        break;
                }
            }
        } else {
            $moptPayoneMain->getHelper()->saveShippingAddressError($userId, $response);

            switch ($config['adresscheckFailureHandling']) {
                case 0: //cancel transaction -> redirect to payment choice
                    {
                        $subject->forward('payment', 'account', null, array('sTarget' => 'checkout'));
                }
                    break;
                case 1: // reenter address -> redirect to address form
                    {
                        $subject->forward('shipping', 'account', null, array('sTarget' => 'checkout'));
                }
                    break;
                case 2: // perform consumerscore check
                    {
                        $params = $moptPayoneMain->getParamBuilder()
                                ->getConsumerscoreCheckParams($shippingAddressData, $config['paymentId']);
                        $response = $this->performConsumerScoreCheck($config, $params, $this->Application()->PayoneBuilder());
                    if (!$this->handleConsumerScoreCheckResult($response, $config, $userId)) {
                        $subject->forward('payment', 'account', null, array('sTarget' => 'checkout'));
                    }
                }
                break;
                case 3: //proceed
                    {
                        return;
                }
                    break;
            }
        }

        return $ret;
    }

    protected function handleConsumerScoreCheckResult($response, $config, $userId)
    {
        $moptPayoneMain = $this->Application()->PayoneMain();

        // handle ERROR, VALID, INVALID
        if ($response->getStatus() == Payone_Api_Enum_ResponseType::VALID) {
            //save result
            $moptPayoneMain->getHelper()->saveConsumerScoreCheckResult($userId, $response);
            return true;
        } else {
            //save error
            $moptPayoneMain->getHelper()->saveConsumerScoreError($userId, $response);
            //choose next action according to config
            if ($config['consumerscoreFailureHandling'] == 0) {
                //cancel
                return false;
            } else {
                //proceed
                return true;
            }
        }

        return true;
    }

    /**
     * create payment methods
     */
    protected function createPayments()
    {
        $mopt_payone__paymentMethods = $this->moptPayoneInstallHelper->mopt_payone__getPaymentMethods();

        foreach ($mopt_payone__paymentMethods as $paymentMethod) {
            if ($this->Payments()->findOneBy(array('name' => $paymentMethod['name']))) {
                continue;
            }

            $payment = array(
                'name' => $paymentMethod['name'],
                'description' => $paymentMethod['description'],
                'action' => 'mopt_payment_payone',
                'active' => 0,
                'position' => $paymentMethod['position'],
                'additionalDescription' => 'Pay save and secured through our payment service.',
            );

            if (!is_null($paymentMethod['template'])) {
                $payment['template'] = $paymentMethod['template'];
            }
            if (isset($paymentMethod['additionalDescription']) && !is_null($paymentMethod['additionalDescription'])) {
                $payment['additionalDescription'] = $paymentMethod['additionalDescription'];
            }
            $this->createPayment($payment);
        }
    }

    /**
     * create tables, add coloumns
     */
    protected function createDatabase()
    {
        $em = $this->Application()->Models();
        $platform = $em->getConnection()->getDatabasePlatform();
        $platform->registerDoctrineTypeMapping('enum', 'string');
        $schemaTool = new \Doctrine\ORM\Tools\SchemaTool($em);

        try {
            $schemaTool->createSchema(array(
                $em->getClassMetadata('Shopware\CustomModels\MoptPayoneTransactionLog\MoptPayoneTransactionLog'),
            ));
        } catch (\Doctrine\ORM\Tools\ToolsException $e) {
            
        }

        try {
            $schemaTool->createSchema(array(
                $em->getClassMetadata('Shopware\CustomModels\MoptPayoneApiLog\MoptPayoneApiLog'),
            ));
        } catch (\Doctrine\ORM\Tools\ToolsException $e) {
            
        }

        try {
            $schemaTool->createSchema(array(
                $em->getClassMetadata('Shopware\CustomModels\MoptPayoneConfig\MoptPayoneConfig'),
            ));
        } catch (\Doctrine\ORM\Tools\ToolsException $e) {
            
        }

        try {
            $schemaTool->createSchema(array(
                $em->getClassMetadata('Shopware\CustomModels\MoptPayonePaypal\MoptPayonePaypal'),
            ));
        } catch (\Doctrine\ORM\Tools\ToolsException $e) {
            
        }

        try {
            $schemaTool->createSchema(array(
                $em->getClassMetadata('Shopware\CustomModels\MoptPayoneCreditcardConfig\MoptPayoneCreditcardConfig'),
            ));
        } catch (\Doctrine\ORM\Tools\ToolsException $e) {
            
        }

        $this->moptPayoneInstallHelper->moptCreatePaymentDataTable();
        $this->moptPayoneInstallHelper->moptInsertDocumentsExtensionIntoDatabaseIfNotExist();

        // payone config sepa extension
        if (!$this->moptPayoneInstallHelper->moptPayoneConfigExtensionExist()) {
            $this->moptPayoneInstallHelper->moptExtendConfigDataTable();
        }

        // payone config klarna extension
        if (!$this->moptPayoneInstallHelper->moptPayoneConfigKlarnaExtensionExist()) {
            $this->moptPayoneInstallHelper->moptExtendConfigKlarnaDataTable();
        }

        // payone save terms acceptance extension
        if (!$this->moptPayoneInstallHelper->moptPayoneConfigsaveTermsExtensionExist()) {
            $this->moptPayoneInstallHelper->moptExtendConfigSaveTermsDataTable();
        }

        // payone paypal ecs extension
        if (!$this->moptPayoneInstallHelper->moptPayoneConfigPaypalEcsExtensionExist()) {
            $this->moptPayoneInstallHelper->moptExtendConfigPaypalEcsDataTable();
        }

        // payone creditcard min days and riskcheck country config extension
        if (!$this->getInstallHelper()->moptPayoneConfigRiskCountryExtensionExist()) {
            $this->getInstallHelper()->moptExtendConfigCreditcardMinValidDays();
            $this->getInstallHelper()->moptExtendConfigAddressCheckCountries();
        }

        // check if transaction log model is updated
        if (!$this->getInstallHelper()->isTransactionLogModelUpdated()) {
            $this->getInstallHelper()->updateTransactionLogModel();
        }

        // check if creditcard configuration model is updated and update if needed
        if (!$this->getInstallHelper()->isCreditCardConfigModelUpdated()) {
            $this->getInstallHelper()->updateCreditCardConfigModel();
        }

        // config option for ELV showBic
        if (!$this->getInstallHelper()->fcPayoneConfigShowBicExtensionExist()) {
            $this->getInstallHelper()->fcExtendConfigShowBicDataTable();
        }

        // config option for Sofortüberweisung
        if (!$this->getInstallHelper()->fcPayoneConfigShowSofortIbanBicExtensionExist()) {
            $this->getInstallHelper()->fcExtendConfigShowSofortIbanBicDataTable();
        }

        // payone config payolution installment extension
        if (!$this->getInstallHelper()->moptPayoneConfigPayolutionExtensionExist()) {
            $this->getInstallHelper()->moptExtendConfigPayolutionDataTable();
        }
        
        $this->moptPayoneInstallHelper->moptInsertEmptyConfigIfNotExists();
    }

    /**
     * extend shpoware models with PAYONE specific attributes
     */
    protected function addAttributes()
    {
        $models = array();

        // user extension
        if (!$this->moptPayoneInstallHelper->moptUserAttributesExist()) {
            Shopware()->Models()->addAttribute('s_user_attributes', 'mopt_payone', 'consumerscore_result', 'VARCHAR(100)', true, null);
            Shopware()->Models()->addAttribute('s_user_attributes', 'mopt_payone', 'consumerscore_date', 'date', true, null);
            Shopware()->Models()->addAttribute('s_user_attributes', 'mopt_payone', 'consumerscore_color', 'VARCHAR(1)', true, null);
            Shopware()->Models()->addAttribute('s_user_attributes', 'mopt_payone', 'consumerscore_value', 'integer', true, null);

            $models[] = 's_user_attributes';
        }

        // billing adress extension user
        if (!$this->moptPayoneInstallHelper->moptBillingAddressAttributesExist()) {
            Shopware()->Models()->addAttribute('s_user_billingaddress_attributes', 'mopt_payone', 'addresscheck_result', 'VARCHAR(100)', true, null);
            Shopware()->Models()->addAttribute('s_user_billingaddress_attributes', 'mopt_payone', 'addresscheck_date', 'date', true, null);
            Shopware()->Models()->addAttribute('s_user_billingaddress_attributes', 'mopt_payone', 'addresscheck_personstatus', 'VARCHAR(100)', true, null);
            Shopware()->Models()->addAttribute('s_user_billingaddress_attributes', 'mopt_payone', 'consumerscore_result', 'VARCHAR(100)', true, null);
            Shopware()->Models()->addAttribute('s_user_billingaddress_attributes', 'mopt_payone', 'consumerscore_date', 'date', true, null);
            Shopware()->Models()->addAttribute('s_user_billingaddress_attributes', 'mopt_payone', 'consumerscore_color', 'VARCHAR(1)', true, null);
            Shopware()->Models()->addAttribute('s_user_billingaddress_attributes', 'mopt_payone', 'consumerscore_value', 'integer', true, null);

            $models[] = 's_user_billingaddress_attributes';
        }

        // shipping adress extension
        if (!$this->moptPayoneInstallHelper->moptShippingAddressAttributesExist()) {
            Shopware()->Models()->addAttribute('s_user_shippingaddress_attributes', 'mopt_payone', 'addresscheck_result', 'VARCHAR(100)', true, null);
            Shopware()->Models()->addAttribute('s_user_shippingaddress_attributes', 'mopt_payone', 'addresscheck_date', 'date', true, null);
            Shopware()->Models()->addAttribute('s_user_shippingaddress_attributes', 'mopt_payone', 'addresscheck_personstatus', 'VARCHAR(100)', true, null);
            Shopware()->Models()->addAttribute('s_user_shippingaddress_attributes', 'mopt_payone', 'consumerscore_color', 'VARCHAR(1)', true, null);
            Shopware()->Models()->addAttribute('s_user_shippingaddress_attributes', 'mopt_payone', 'consumerscore_value', 'integer', true, null);

            $models[] = 's_user_shippingaddress_attributes';
        }

        // order extension
        if (!$this->moptPayoneInstallHelper->moptOrderAttributesExist()) {
            Shopware()->Models()->addAttribute('s_order_attributes', 'mopt_payone', 'txid', 'integer', true, null);
            Shopware()->Models()->addAttribute('s_order_attributes', 'mopt_payone', 'status', 'VARCHAR(100)', true, null);
            Shopware()->Models()->addAttribute('s_order_attributes', 'mopt_payone', 'sequencenumber', 'int(11)', true, null);
            Shopware()->Models()->addAttribute('s_order_attributes', 'mopt_payone', 'is_authorized', 'TINYINT(1)', true, null);
            Shopware()->Models()->addAttribute('s_order_attributes', 'mopt_payone', 'is_finally_captured', 'TINYINT(1)', true, null);
            Shopware()->Models()->addAttribute('s_order_attributes', 'mopt_payone', 'clearing_data', 'text', true, null);

            $models[] = 's_order_attributes';
        }

        // orderdetails(order articles) extension
        if (!$this->moptPayoneInstallHelper->moptOrderDetailsAttributesExist()) {
            Shopware()->Models()->addAttribute('s_order_details_attributes', 'mopt_payone', 'payment_status', 'VARCHAR(100)', true, null);
            Shopware()->Models()->addAttribute('s_order_details_attributes', 'mopt_payone', 'shipment_date', 'date', true, null);
            Shopware()->Models()->addAttribute('s_order_details_attributes', 'mopt_payone', 'captured', 'double', true, null);
            Shopware()->Models()->addAttribute('s_order_details_attributes', 'mopt_payone', 'debit', 'double', true, null);

            $models[] = 's_order_details_attributes';
        }

        if (!empty($models)) {
            Shopware()->Models()->generateAttributeModels($models);
        }

        // 2nd order extension since 2.1.4 - save shipping cost with order
        if (!$this->moptPayoneInstallHelper->moptOrderAttributesShippingCostsExist()) {
            Shopware()->Models()->addAttribute('s_order_attributes', 'mopt_payone', 'ship_captured', 'double', true, 0.00);
            Shopware()->Models()->addAttribute('s_order_attributes', 'mopt_payone', 'ship_debit', 'double', true, 0.00);

            Shopware()->Models()->generateAttributeModels(array('s_order_attributes'));
        }

        // 3rd order extension since 2.3.0 - save payment data for abo commerce support
        if (!$this->moptPayoneInstallHelper->moptOrderAttributesPaymentDataExist()) {
            Shopware()->Models()->addAttribute('s_order_attributes', 'mopt_payone', 'payment_data', 'text', true, null);

            Shopware()->Models()->generateAttributeModels(array('s_order_attributes'));
        }

        // 4th order extension since 2.5.2 - save order hash and payment reference
        if (!$this->moptPayoneInstallHelper->moptOrderAttributesOrderHashExist()) {
            Shopware()->Models()->addAttribute('s_order_attributes', 'mopt_payone', 'payment_reference', 'VARCHAR(100)', true, null);
            Shopware()->Models()->addAttribute('s_order_attributes', 'mopt_payone', 'order_hash', 'VARCHAR(255)', true, null);

            Shopware()->Models()->generateAttributeModels(array('s_order_attributes'));
        }
        
        // 5th order extension since 3.3.8 - Payolution Payment Order extensions

        if (!$this->getInstallHelper()->moptPayolutionWorkOrderIdAttributeExist()) {
            Shopware()->Models()->addAttribute(
                's_order_attributes',
                'mopt_payone',
                'payolution_workorder_id',
                'VARCHAR(64)',
                true,
                null
            );
            Shopware()->Models()->generateAttributeModels(array('s_order_attributes'));
        }

        if (!$this->getInstallHelper()->moptPayolutionClearingReferenceAttributeExist()) {
            Shopware()->Models()->addAttribute(
                's_order_attributes',
                'mopt_payone',
                'payolution_clearing_reference',
                'VARCHAR(64)',
                true,
                null
            );
            Shopware()->Models()->generateAttributeModels(array('s_order_attributes'));
        }

    }

    /**
     * Create menu items to access configuration, logs and support page
     */
    protected function createMenu()
    {
        $configurationLabelName = $this->moptPayoneInstallHelper->moptGetConfigurationLabelName();

        if (version_compare(Shopware::VERSION, '4.1.3', '<')) {
            $paypalController = 'MoptPayonePaypalOld';
            $creditcardConfigController = 'MoptPayoneCreditcardConfigOld';
        } else {
            $paypalController = 'MoptPayonePaypal';
            $creditcardConfigController = 'MoptPayoneCreditcardConfig';
        }

        $parent = $this->Menu()->findOneBy('label', 'Zahlungen');
        $item = $this->createMenuItem(array(
            'label' => 'PAYONE',
            'class' => 'payoneicon',
            'active' => 1,
            'parent' => $parent,
        ));

        $this->createMenuItem(array(
            'label' => $configurationLabelName,
            'controller' => 'MoptConfigPayone',
            'action' => 'Index',
            'class' => 'sprite-wrench-screwdriver',
            'active' => 1,
            'parent' => $item,
        ));
        $this->createMenuItem(array(
            'label' => 'Payone PayPal',
            'controller' => $paypalController,
            'action' => 'Index',
            'class' => 'sprite-locale',
            'active' => 1,
            'parent' => $item,
        ));
        $this->createMenuItem(array(
            'label' => 'Payone Kreditkartenkonfiguration',
            'controller' => $creditcardConfigController,
            'action' => 'Index',
            'class' => 'sprite-wrench-screwdriver',
            'active' => 1,
            'parent' => $item,
        ));
        $this->createMenuItem(array(
            'label' => 'API-Log',
            'controller' => 'MoptApilogPayone',
            'action' => 'Index',
            'class' => 'sprite-cards-stack',
            'active' => 1,
            'parent' => $item,
        ));
        $this->createMenuItem(array(
            'label' => 'Transaktionsstatus-Log',
            'controller' => 'MoptPayoneTransactionLog',
            'action' => 'Index',
            'class' => 'sprite-cards-stack',
            'active' => 1,
            'parent' => $item,
        ));
        $this->createMenuItem(array(
            'label' => 'Hilfe & Support',
            'controller' => 'MoptSupportPayone',
            'action' => 'Index',
            'class' => 'sprite-lifebuoy',
            'active' => 1,
            'parent' => $item,
        ));
        $this->createMenuItem(array(
            'label' => 'Konfigurationsexport',
            'controller' => 'MoptExportPayone',
            'action' => 'Index',
            'class' => 'sprite-script-export',
            'active' => 1,
            'parent' => $item,
        ));
    }

    /**
     * Returns the path to a frontend controller for an event.
     *
     * @param Enlight_Event_EventArgs $args
     * @return string
     */
    public static function onGetControllerPathFrontendMoptPaymentPayone()
    {
        return dirname(__FILE__) . '/Controllers/Frontend/MoptPaymentPayone.php';
    }

    /**
     * Returns the path to a backend controller for an event.
     *
     * @return string
     */
    public function onGetConfigControllerBackend()
    {
        $this->Application()->Template()->addTemplateDir(
            $this->Path() . 'Views/'
        );
        $this->Application()->Snippets()->addConfigDir($this->Path() . 'snippets/');
        return $this->Path() . 'Controllers/Backend/MoptConfigPayone.php';
    }

    /**
     * Returns the path to a backend controller for an event.
     *
     * @return string
     */
    public function onGetApilogControllerBackend()
    {
        $this->Application()->Template()->addTemplateDir(
            $this->Path() . 'Views/'
        );
        $this->Application()->Snippets()->addConfigDir($this->Path() . 'snippets/');
        return $this->Path() . 'Controllers/Backend/MoptApilogPayone.php';
    }

    public function onGetTransactionLogControllerBackend()
    {
        $this->Application()->Template()->addTemplateDir(
            $this->Path() . 'Views/'
        );
        $this->Application()->Snippets()->addConfigDir($this->Path() . 'snippets/');
        return $this->Path() . 'Controllers/Backend/MoptPayoneTransactionLog.php';
    }

    /**
     * Returns the path to a backend controller for an event.
     *
     * @return string
     */
    public function onGetSupportControllerBackend()
    {
        $this->Application()->Template()->addTemplateDir(
            $this->Path() . 'Views/'
        );
        $this->Application()->Snippets()->addConfigDir($this->Path() . 'snippets/');
        return $this->Path() . 'Controllers/Backend/MoptSupportPayone.php';
    }

    /**
     * Returns the path to a backend controller for an event.
     *
     * @return string
     */
    public function onGetBackendExportController()
    {
        $this->Application()->Template()->addTemplateDir(
            $this->Path() . 'Views/'
        );
        $this->Application()->Snippets()->addConfigDir($this->Path() . 'snippets/');
        return $this->Path() . 'Controllers/Backend/MoptExportPayone.php';
    }

    /**
     * Returns the path to a backend controller for an event.
     *
     * @return string
     */
    public function onGetBackendControllerPaypal()
    {
        $this->Application()->Template()->addTemplateDir(
            $this->Path() . 'Views/'
        );
        $this->Application()->Snippets()->addConfigDir($this->Path() . 'snippets/');
        return $this->Path() . 'Controllers/Backend/MoptPayonePaypal.php';
    }

    /**
     * Returns the path to a backend controller for an event.
     *
     * @return string
     */
    public function onGetBackendControllerPaypalOld()
    {
        $this->Application()->Template()->addTemplateDir(
            $this->Path() . 'Views/'
        );
        $this->Application()->Snippets()->addConfigDir($this->Path() . 'snippets/');
        return $this->Path() . 'Controllers/Backend/MoptPayonePaypalOld.php';
    }

    /**
     * Returns the path to a backend controller for an event.
     *
     * @return string
     */
    public function onGetBackendControllerCreditcardConfig()
    {
        $this->Application()->Template()->addTemplateDir(
            $this->Path() . 'Views/'
        );
        $this->Application()->Snippets()->addConfigDir($this->Path() . 'snippets/');
        return $this->Path() . 'Controllers/Backend/MoptPayoneCreditcardConfig.php';
    }

    /**
     * Returns the path to a backend controller for an event.
     *
     * @return string
     */
    public function onGetBackendControllerCreditcardConfigOld()
    {
        $this->Application()->Template()->addTemplateDir(
            $this->Path() . 'Views/'
        );
        $this->Application()->Snippets()->addConfigDir($this->Path() . 'snippets/');
        return $this->Path() . 'Controllers/Backend/MoptPayoneCreditcardConfigOld.php';
    }

    /**
     * choose correct tpl folder and extend shopware templates
     *
     * @param Enlight_Event_EventArgs $args
     */
    public function onPostDispatch(Enlight_Event_EventArgs $args)
    {
        $request = $args->getSubject()->Request();
        $response = $args->getSubject()->Response();
        $view = $args->getSubject()->View();

        if (!$request->isDispatched() || $response->isException() || $request->getModuleName() != 'frontend') {
            return;
        }
        
        $moptPayoneMain = $this->Application()->PayoneMain();
        $config = $moptPayoneMain->getPayoneConfig();

        $this->Application()->Snippets()->addConfigDir($this->Path() . 'snippets/');
        $this->setCorrectViewsFolder();
        $session = Shopware()->Session();

        $templateSuffix = '';
        if ($this->isResponsive()) {
            $templateSuffix = '_responsive';
        }

        $controllerName = $request->getControllerName();
        $actionName = $request->getActionName();

        if ($session->moptMandateData) {
            $view->assign('moptMandateData', $session->moptMandateData);
        }

        $view->extendsTemplate('frontend/checkout/mopt_confirm_payment' . $templateSuffix . '.tpl');
        $view->extendsTemplate('frontend/checkout/mopt_confirm' . $templateSuffix . '.tpl');
        $view->extendsTemplate('frontend/checkout/mopt_finish' . $templateSuffix . '.tpl');

        unset($session->moptMandateAgreement);
        if ($request->getParam('mandate_status')) {
            $session->moptMandateAgreement = $request->getParam('mandate_status');
        }

        if (in_array($controllerName, array('account', 'checkout', 'register'))) {
            $view->assign('moptCreditCardCheckEnvironment', $this->moptPayoneCheckEnvironment($controllerName));
            $view->assign('moptPaymentConfigParams', $this->moptPaymentConfigParams($session->moptMandateDataDownload));
            $view->assign('moptMandateAgreementError', $session->moptMandateAgreementError);
            unset($session->moptMandateAgreementError);
        }

        if ($controllerName == 'account' && $actionName == 'index') {
            if ($session->moptAddressCheckNeedsUserVerification) {
                $view->assign('moptAddressCheckNeedsUserVerification', $session->moptAddressCheckNeedsUserVerification);
                $view->extendsTemplate('frontend/account/mopt_billing' . $templateSuffix . '.tpl');
            }
            if ($session->moptShippingAddressCheckNeedsUserVerification) {
                $view->assign('moptShippingAddressCheckNeedsUserVerification', $session->moptShippingAddressCheckNeedsUserVerification);
                $view->extendsTemplate('frontend/account/mopt_shipping' . $templateSuffix . '.tpl');
            }
        }
            
        $cname = $controllerName;
        $aname = $actionName;
        
        if ($controllerName == 'account' && $actionName == 'payment') {
            if ($_SESSION['moptConsumerScoreCheckNeedsUserAgreement']) {
                $view->assign('moptConsumerScoreCheckNeedsUserAgreement', $session->moptConsumerScoreCheckNeedsUserAgreement);
                $view->extendsTemplate('frontend/account/mopt_consumescore' . $templateSuffix . '.tpl');
            }
        }

        if (($controllerName == 'checkout' && $actionName == 'confirm')) {
            if ($session->moptAddressCheckNeedsUserVerification) {
                $view->assign('moptAddressCheckNeedsUserVerification', $session->moptAddressCheckNeedsUserVerification);
                $view->extendsTemplate('frontend/checkout/mopt_confirm' . $templateSuffix . '.tpl');
            }
            if ($session->moptShippingAddressCheckNeedsUserVerification) {
                $view->assign('moptShippingAddressCheckNeedsUserVerification', $session->moptShippingAddressCheckNeedsUserVerification);
                $view->extendsTemplate('frontend/checkout/mopt_shipping_confirm' . $templateSuffix . '.tpl');
            }
            $request = $args->getSubject()->Request();

            if ($request->getParam('moptAddressCheckNeedsUserVerification')) {
                $view->assign('moptAddressCheckNeedsUserVerification', $request->getParam('moptAddressCheckNeedsUserVerification'));
                $session->moptAddressCheckOriginalAddress = $request->getParam('moptAddressCheckOriginalAddress');
                $session->moptAddressCheckCorrectedAddress = $request->getParam('moptAddressCheckCorrectedAddress');
                $session->moptAddressCheckTarget = $request->getParam('moptAddressCheckTarget');
                $view->extendsTemplate('frontend/checkout/mopt_confirm' . $templateSuffix . '.tpl');
            }

            if ($request->getParam('moptShippingAddressCheckNeedsUserVerification')) {
                $view->assign('moptShippingAddressCheckNeedsUserVerification', $request->getParam('moptShippingAddressCheckNeedsUserVerification'));
                $session->moptShippingAddressCheckOriginalAddress = $request->getParam('moptShippingAddressCheckOriginalAddress');
                $session->moptShippingAddressCheckCorrectedAddress = $request->getParam('moptShippingAddressCheckCorrectedAddress');
                $session->moptShippingAddressCheckTarget = $request->getParam('moptShippingAddressCheckTarget');
                $view->extendsTemplate('frontend/checkout/mopt_shipping_confirm' . $templateSuffix . '.tpl');
            }

            if ($session->moptConsumerScoreCheckNeedsUserAgreement) {
                $view->assign('moptConsumerScoreCheckNeedsUserAgreement', $session->moptConsumerScoreCheckNeedsUserAgreement);
                $view->extendsTemplate('frontend/account/mopt_consumescore' . $templateSuffix . '.tpl');
            }
        }
        if (($controllerName == 'checkout' && $actionName == 'payment')) {
            unset($session->moptBarzahlenCode);
        }
        if (($controllerName == 'checkout' && $actionName == 'finish')) {
            if ($session->moptBarzahlenCode) {
                $view->assign('moptBarzahlenCode', $session->moptBarzahlenCode);
            }
        }
    }

    /**
     * Creates and returns the payone builder for an event.
     *
     * @param Enlight_Event_EventArgs $args
     * @return \Shopware_Components_Payone_Builder
     */
    public function onInitResourcePayoneBuilder(Enlight_Event_EventArgs $args)
    {
        $payoneConfig = new Payone_Config();
        $logger = array('Payone_Protocol_Logger_Log4php' => null);

        $payoneConfig->setValue('api/default/protocol/loggers', $logger);
        $payoneConfig->setValue('transaction_status/default/protocol/loggers', $logger);
        $payoneConfig->setValue('session_status/default/protocol/loggers', $logger);

        //$payoneConfig->setValue('api/default/protocol/filter/mask_value/percent', 50);

        $builder = new Payone_Builder($payoneConfig);
        return $builder;
    }

    /**
     * Creates and returns the payone builder for an event.
     *
     * @param Enlight_Event_EventArgs $args
     * @return \Shopware_Components_Payone_Builder
     */
    public function onInitResourcePayoneMain(Enlight_Event_EventArgs $args)
    {
        $moptPayoneMain = Mopt_PayoneMain::getInstance();
        return $moptPayoneMain;
    }

    public function onBackendRiskManagementPostDispatch(Enlight_Event_EventArgs $args)
    {
        $view = $args->getSubject()->View();

        // Add template directory
        $args->getSubject()->View()->addTemplateDir(
            $this->Path() . 'Views/'
        );
        $this->Application()->Snippets()->addConfigDir($this->Path() . 'snippets/');

        $view->extendsTemplate('backend/mopt_risk_management/controller/main.js');
        $view->extendsTemplate('backend/mopt_risk_management/controller/risk_management.js');

        $view->extendsTemplate('backend/mopt_risk_management/store/risks.js');
        $view->extendsTemplate('backend/mopt_risk_management/store/trafficLights.js');

        $view->extendsTemplate('backend/mopt_risk_management/view/risk_management/container.js');
    }

    public function moptExtendController_Backend_Order(Enlight_Event_EventArgs $args)
    {
        $view = $args->getSubject()->View();
        $args->getSubject()->View()->addTemplateDir($this->Path() . 'Views/');
        $this->Application()->Snippets()->addConfigDir($this->Path() . 'snippets/');
        $view->extendsTemplate('backend/mopt_payone_order/controller/detail.js');
        $view->extendsTemplate('backend/mopt_payone_order/model/position.js');
        $view->extendsTemplate('backend/mopt_payone_order/view/detail/overview.js');
        $view->extendsTemplate('backend/mopt_payone_order/view/detail/position.js');
    }

    public function moptExtendController_Backend_Payment(Enlight_Event_EventArgs $args)
    {
        $view = $args->getSubject()->View();
        $args->getSubject()->View()->addTemplateDir($this->Path() . 'Views/');
        $this->Application()->Snippets()->addConfigDir($this->Path() . 'snippets/');
        $view->extendsTemplate('backend/mopt_payone_payment/controller/payment.js');
        $view->extendsTemplate('backend/mopt_payone_payment/view/main/window.js');
    }

    /**
     * add attribute data to detail-data
     * @parent fnc head: protected function getList($filter, $sort, $offset, $limit)
     *
     * @param Enlight_Event_EventArgs $args
     */
    public function Order__getList__after(Enlight_Event_EventArgs $args)
    {
        $return = $args->getReturn();
        $helper = $this->Application()->PayoneMain()->getHelper();

        if (empty($return['success']) || empty($return['data'])) {
            return;
        }

        foreach ($return['data'] as &$order) {
            foreach ($order["details"] as &$orderDetail) {
                //get detail attribute
                $detailObj = Shopware()->Models()->getRepository('Shopware\Models\Order\Detail')
                        ->find($orderDetail['id']);
                $attribute = $helper->getOrCreateAttribute($detailObj);
                $orderDetail['moptPayoneCaptured'] = $attribute->getMoptPayoneCaptured();
                $orderDetail['moptPayoneDebit'] = $attribute->getMoptPayoneDebit();
            }
        }

        $args->setReturn($return);
    }

    /**
     * add payone clearing information to document
     *
     * @param Enlight_Hook_HookArgs $args
     */
    public function onBeforeRenderDocument(Enlight_Hook_HookArgs $args)
    {
        $document = $args->getSubject();

        if (!$this->Application()->PayoneMain()->getPaymentHelper()->isPayoneBillsafe($document->_order->payment['name'])) {
            return;
        }

        // get PAYONE data from log
        $moptPayoneMain = $this->Application()->PayoneMain();
        $payoneData = $moptPayoneMain->getPaymentHelper()->getClearingDataFromOrderId($document->_order->order->id);

        if (empty($payoneData)) {
            return;
        }

        $payoneData['amount'] = $document->_order->order->invoice_amount;

        $view = $document->_view;
        //@TODO check if additional treatment for responsive theme is needed here
        $document->_template->addTemplateDir(dirname(__FILE__) . '/Views/');
        $document->_template->assign('instruction', (array) $payoneData);
        $containerData = $view->getTemplateVars('Containers');
        $containerData['Footer'] = $containerData['PAYONE_Footer'];
        $containerData['Content_Info'] = $containerData['PAYONE_Content_Info'];
        $containerData['Content_Info']['value'] = $document->_template->fetch('string:'
                . $containerData['Content_Info']['value']);
        $containerData['Content_Info']['style'] = '}' . $containerData['Content_Info']['style'] . ' #info {';
        $view->assign('Containers', $containerData);
    }

    /**
     * add clearing data to email variables
     *
     * @param \Shopware\Plugins\MoptPaymentPayone\Subscribers\Enlight_Hook_HookArgs $args
     * @return array
     */
    public function onSendMailFilterVariablesFilter(Enlight_Hook_HookArgs $args)
    {
        $variables = $args->getReturn();
        $session = Shopware()->Session();

        if ($session->moptClearingData) {
            $variables['additional']['moptPayoneClearingData'] = $session->moptClearingData;
            $args->setReturn($variables);
        }
    }

    /**
     * Forward the request to the given controller, module and action with the given parameters.
     * copied from Enlight_Controller_Action
     * and customized
     *
     * @param object $request
     * @param string $action
     * @param string $controller
     * @param string $module
     * @param array  $params
     */
    public function forward($request, $action, $controller = null, $module = null, array $params = null)
    {
        if ($params !== null) {
            $request->setParams($params);
        }
        if ($controller !== null) {
            $request->setControllerName($controller);
            if ($module !== null) {
                $request->setModuleName($module);
            }
        }

        $request->setActionName($action)->setDispatched(false);
    }

    /**
     * prepare additional data for payment choice
     *
     * @param string $controllerName
     * @return array
     */
    protected function moptPayoneCheckEnvironment($controllerName = false)
    {
        $data = array();
        $moptPayoneMain = $this->Application()->PayoneMain();
        $userId = Shopware()->Session()->sUserId;
        $shopLanguage = explode('_', Shopware()->Shop()->getLocale()->getLocale());

        $sql = 'SELECT `moptPaymentData` FROM s_plugin_mopt_payone_payment_data WHERE userId = ?';
        $paymentData = unserialize(Shopware()->Db()->fetchOne($sql, $userId));

        $paymentMeans = Shopware()->Modules()->Admin()->sGetPaymentMeans();
        $groupedPaymentMeans = false;

        if ($controllerName && $controllerName === 'checkout') {
            $groupedPaymentMeans = $this->groupCreditcards($paymentMeans);
        }

        if ($groupedPaymentMeans) {
            $paymentMeans = $groupedPaymentMeans;
        }

        foreach ($paymentMeans as $paymentMean) {
            if ($paymentMean['id'] == 'mopt_payone_creditcard') {
                $paymentMean['mopt_payone_credit_cards'] = $moptPayoneMain->getPaymentHelper()
                        ->mapCardLetter($paymentMean['mopt_payone_credit_cards']);
                $data['payment_mean'] = $paymentMean;
            }

            //prepare additional Klarna information and retrieve birthday and phone nr from user data
            if ($moptPayoneMain->getPaymentHelper()->isPayoneKlarna($paymentMean['name'])) {
                $klarnaConfig = $moptPayoneMain->getPayoneConfig($paymentMean['id']);
                $data['moptKlarnaInformation'] = $moptPayoneMain->getPaymentHelper()
                        ->moptGetKlarnaAdditionalInformation($shopLanguage[1], $klarnaConfig['klarnaStoreId']);
                $userData = Shopware()->Modules()->Admin()->sGetUserData();
                $birthday = explode('-', $userData['billingaddress']['birthday']);
                $data['mopt_payone__klarna_birthday'] = $birthday[2];
                $data['mopt_payone__klarna_birthmonth'] = $birthday[1];
                $data['mopt_payone__klarna_birthyear'] = $birthday[0];
                $data['mopt_payone__klarna_telephone'] = $userData['billingaddress']['phone'];
            }
            
            //prepare additional Payolution information and retrieve birthday from user data
            if ($moptPayoneMain->getPaymentHelper()->isPayonePayolutionDebitNote($paymentMean['name'])
                    || $moptPayoneMain->getPaymentHelper()->isPayonePayolutionInvoice($paymentMean['name'])) {
                $data['payolutionConfig'] = $moptPayoneMain->getPayoneConfig($paymentMean['id']);

                $data['moptPayolutionInformation'] = $moptPayoneMain->getPaymentHelper()
                        ->moptGetPayolutionAdditionalInformation($shopLanguage[1], $data['payolutionConfig']['payolutionCompanyName']);
                $userData = Shopware()->Modules()->Admin()->sGetUserData();
                $birthday = explode('-', $userData['billingaddress']['birthday']);
                $data['mopt_payone__payolution_debitnote_birthday'] = $birthday[2];
                $data['mopt_payone__payolution_debitnote_birthmonth'] = $birthday[1];
                $data['mopt_payone__payolution_debitnote_birthyear'] = $birthday[0];
                $data['mopt_payone__payolution_invoice_birthday'] = $birthday[2];
                $data['mopt_payone__payolution_invoice_birthmonth'] = $birthday[1];
                $data['mopt_payone__payolution_invoice_birthyear'] = $birthday[0];
            }
        }

        $payoneParams = $moptPayoneMain->getParamBuilder()->getBasicParameters();
        $creditCardConfig = $this->getCreditcardConfig(); //retrieve additional creditcardconfig

        $payoneParams['mid'] = $creditCardConfig['merchant_id'];
        $payoneParams['portalid'] = $creditCardConfig['portal_id'];
        $payoneParams['key'] = $creditCardConfig['api_key'];
        $payoneParams['aid'] = $creditCardConfig['subaccount_id'];
        if ($creditCardConfig['live_mode']) {
            $payoneParams['mode'] = 'live';
        } else {
            $payoneParams['mode'] = 'test';
        }
        $payoneParams['language'] = $shopLanguage[0];
        $payoneParams['errorMessages'] = json_encode($moptPayoneMain->getPaymentHelper()
                        ->getCreditCardCheckErrorMessages());

        $generateHashService = $this->Application()->PayoneBuilder()->buildServiceClientApiGenerateHash();

        $request = new Payone_ClientApi_Request_CreditCardCheck();
        $params = array(
            'aid' => $payoneParams['aid'],
            'mid' => $payoneParams['mid'],
            'portalid' => $payoneParams['portalid'],
            'mode' => $payoneParams['mode'],
            'encoding' => 'UTF-8',
            'language' => $payoneParams['language'],
            'solution_version' => Shopware_Plugins_Frontend_MoptPaymentPayone_Bootstrap::getVersion(),
            'solution_name' => Shopware_Plugins_Frontend_MoptPaymentPayone_Bootstrap::getSolutionName(),
            'integrator_version' => Shopware()->Config()->Version,
            'integrator_name' => 'Shopware',
            'storecarddata' => 'yes',
        );
        $request->init($params);
        $request->setResponsetype('JSON');

        $payoneParams['hash'] = $generateHashService->generate($request, $creditCardConfig['api_key']);

        $data['moptPayoneCheckCc'] = $creditCardConfig['check_cc'];
        $data['moptCreditcardMinValid'] = (int) $creditCardConfig['creditcard_min_valid'];
       

        // delete the api key; only ['hash'] ist used
        $creditCardConfig['api_key'] = "";
        // to be safe also remove key in $payoneParams
        $payoneParams['key'] = "";
        // also remove key from array [jsonconfig]
        $json_tmp = json_decode($creditCardConfig['jsonConfig'], true);
        unset($json_tmp['api_key']);
        // Also remove nesten jsonConfig Objekt in json_tmp
        unset($json_tmp['jsonConfig']);
        $creditCardConfig['jsonConfig'] = json_encode($json_tmp);

        $data['sFormData'] = $paymentData;
        $data['moptCreditcardConfig'] = $creditCardConfig;
        $data['moptPayoneParams'] = $payoneParams;

        return $data;
    }

    protected function moptPaymentConfigParams($mandateData)
    {
        $data = array();
        $moptPayoneMain = $this->Application()->PayoneMain();
        $config = $moptPayoneMain->getPayoneConfig();

        $paymentMeans = Shopware()->Modules()->Admin()->sGetPaymentMeans();
        foreach ($paymentMeans as $paymentMean) {
            if ($moptPayoneMain->getPaymentHelper()->isPayoneDebitnote($paymentMean['name'])) {
                $data['moptDebitCountries'] = $moptPayoneMain->getPaymentHelper()
                        ->moptGetCountriesAssignedToPayment($paymentMean['id']);
                $debitConfig = $moptPayoneMain->getPayoneConfig($paymentMean['id']);
                $data['moptShowBic'] = $debitConfig['showBic'];
            }
            if ($moptPayoneMain->getPaymentHelper()->isPayoneSofortuerberweisung($paymentMean['name'])) {
                $sofortConfig = $moptPayoneMain->getPayoneConfig($paymentMean['id']);
                $data['moptShowSofortIbanBic'] = $sofortConfig['showSofortIbanBic'];
            }
        }

        //get country via user object
        $userData = Shopware()->Modules()->Admin()->sGetUserData();

        $data['moptShowAccountnumber'] = (bool) ($config['showAccountnumber'] && $userData['additional']['country']['countryiso'] === 'DE');
        if (Shopware()->Config()->currency === 'CHF' && $userData['additional']['country']['countryiso'] === 'CH') {
            $data['moptIsSwiss'] = true;
        } else {
            $data['moptIsSwiss'] = false;
        }

        if ($mandateData) {
            $data['moptMandateDownloadEnabled'] = (bool) ($config['mandateDownloadEnabled']);
        } else {
            $data['moptMandateDownloadEnabled'] = false;
        }

        return $data;
    }

    /**
     * call responsive check method and set views folder according to result
     */
    public function setCorrectViewsFolder()
    {
        if ($this->isResponsive()) {
            $this->Application()->Template()->addTemplateDir($this->Path() . 'ViewsResponsive/');
        } else {
            $this->Application()->Template()->addTemplateDir($this->Path() . 'Views/');
        }
    }

    /**
     * check if Responsive Template is installed and activated for current subshop
     * snippet provided by Conexco
     *
     * @return bool
     */
    protected function isResponsive()
    {
        //Is Responsive Template installed and activated?
        $sql = "SELECT 1 FROM s_core_plugins WHERE name='SwfResponsiveTemplate' AND active=1";

        $result = Shopware()->Db()->fetchOne($sql);
        if ($result != 1) {
            // Plugin is not installed
            return false;
        }

        //activated for current subshop?
        $shop = Shopware()->Shop()->getId();
        $sql = "SELECT 1 FROM s_core_config_elements scce, s_core_config_values sccv WHERE "
                . "scce.name='SwfResponsiveTemplateActive' AND scce.id=sccv.element_id AND sccv.shop_id='"
                . (int) $shop . "' AND sccv.value='b:0;'";

        $result = Shopware()->Db()->fetchOne($sql);
        if ($result == 1) {
            //deactivated
            return false;
        }
        //not deactivated => activated
        return true;
    }

    public function moptExtendController_Frontend_Checkout(Enlight_Event_EventArgs $args)
    {
        $subject = $args->getSubject();
        $view = $subject->View();
        $request = $subject->Request();
        $response = $subject->Response();

        if (!$request->isDispatched() || $response->isException() || $request->getModuleName() != 'frontend') {
            return;
        }

        $session = Shopware()->Session();
        $userData = Shopware()->Modules()->Admin()->sGetUserData();

        if ($request->getActionName() === 'cart') {
            $templateSuffix = '';
            if ($this->isResponsive()) {
                $templateSuffix = '_responsive';
            }

            if ($session->moptPayPalEcsError) {
                unset($session->moptPayPalEcsError);
                $view->assign('sBasketInfo', Shopware()->Snippets()->getNamespace('frontend/MoptPaymentPayone/errorMessages')
                                ->get('generalErrorMessage', 'Es ist ein Fehler aufgetreten', true));
            }

            if ($templateSuffix === '' && $this->isPayPalEcsActive($subject) && ($imageUrl = $this->moptPayoneShortcutImgURL())) {
                $view->assign('moptPaypalShortcutImgURL', $imageUrl);
                $view->extendsTemplate('frontend/checkout/mopt_cart' . $templateSuffix . '.tpl');
            }
        }

        if (!empty($userData['additional']['payment']['id'])) {
            $paymentId = $userData['additional']['payment']['id'];
        } else {
            $paymentId = 0;
        }

        $config = $this->Application()->PayoneMain()->getPayoneConfig($paymentId);
        $confirmActions = array('confirm', 'index', 'payment');

        if ($config['saveTerms'] !== 0) {
            if ($request->getParam('sAGB') === '1') {
                $session->moptAgbChecked = true;
            }
            if ($request->getParam('sAGB') === '0') {
                $session->moptAgbChecked = false;
            }
        }

        if ($config['saveTerms'] === 1 && !in_array($request->getActionName(), $confirmActions)) {
            $session->moptAgbChecked = false;
        }

        $view->assign('moptAgbChecked', $session->moptAgbChecked);
    }

    /**
     * get url to configured and uploaded paypal ecs button
     *
     * @return boolean|string
     */
    protected function moptPayoneShortcutImgURL()
    {
        $localeId = Shopware()->Shop()->getLocale()->getId();

        $builder = Shopware()->Models()->createQueryBuilder();
        $builder->select('button.image')
                ->from('Shopware\CustomModels\MoptPayonePaypal\MoptPayonePaypal button')
                ->where('button.localeId = ?1')
                ->setParameter(1, $localeId);

        $result = $builder->getQuery()->getOneOrNullResult();

        if (!$result) {
            $builder->resetDQLParts();
            $builder->select('button.image')
                    ->from('Shopware\CustomModels\MoptPayonePaypal\MoptPayonePaypal button')
                    ->where('button.isDefault = ?1')
                    ->setParameter(1, true);

            $result = $builder->getQuery()->getOneOrNullResult();
        }

        if (!$result) {
            return false;
        }

        return $result['image'];
    }

    protected function checkAndDeleteOldLogs()
    {
        $path = $this->Path() . '../../../../../../';

        foreach (glob($path . 'payone_*.lo*') as $file) {
            if (file_exists($file)) {
                file_put_contents($file, '');
                unlink($file);
            }
        }
    }

    /**
     * if paypal is available, check if paypal ecs is activated
     *
     * @param mixed $checkoutController
     * @return boolean
     */
    protected function isPayPalEcsActive($checkoutController)
    {
        $payments = $checkoutController->getPayments();
        $payoneMain = $this->Application()->PayoneMain();
        $payonePaymentHelper = $payoneMain->getPaymentHelper();

        if ($payoneMain->getHelper()->isAboCommerceArticleInBasket()) {
            return false;
        }

        foreach ($payments as $paymentMethod) {
            if ($payonePaymentHelper->isPayPalEcsActive($payoneMain, $paymentMethod)) {
                Shopware()->Session()->moptPaypayEcsPaymentId = $paymentMethod['id'];
                return true;
            }
        }

        return false;
    }

    protected function getInstallHelper()
    {
        if (is_null($this->moptPayoneInstallHelper)) {
            $this->moptPayoneInstallHelper = new Mopt_PayoneInstallHelper();
        }

        return $this->moptPayoneInstallHelper;
    }

    protected function getPaymentHelper()
    {
        if (is_null($this->moptPayonePaymentHelper)) {
            $this->moptPayonePaymentHelper = new Mopt_PayonePaymentHelper();
        }

        return $this->moptPayonePaymentHelper;
    }

    /**
     * group credit cards to single payment method creditcard
     *
     * @param array $paymentMeans
     * @return bool|array
     */
    protected function groupCreditcards($paymentMeans)
    {
        $firstHit = 'not_set';
        $creditCardData = array();
        $moptPayonePaymentHelper = $this->getPaymentHelper();

        foreach ($paymentMeans as $key => $paymentmean) {
            if ($moptPayonePaymentHelper->isPayoneCreditcardNotGrouped($paymentmean['name'])) {
                if ($firstHit === 'not_set') {
                    $firstHit = $key;
                }

                $creditCard = array();
                $creditCard['id'] = $paymentmean['id'];
                $creditCard['name'] = $paymentmean['name'];
                $creditCard['description'] = $paymentmean['description'];

                $creditCardData[] = $creditCard;

                if ($firstHit != $key) {
                    unset($paymentMeans[$key]);
                }
            }
        }

        // don't assign anything if no creditcard was found
        if ($firstHit === 'not_set') {
            return false;
        }

        $snippetObject = Shopware()->Snippets()->getNamespace('frontend/MoptPaymentPayone/payment');
        $paymentMeans[$firstHit]['id'] = 'mopt_payone_creditcard';
        $paymentMeans[$firstHit]['name'] = 'mopt_payone_creditcard';
        $paymentMeans[$firstHit]['description'] = $snippetObject->get('PaymentMethodCreditCard', 'Kreditkarte', true);
        $paymentMeans[$firstHit]['mopt_payone_credit_cards'] = $creditCardData;

        return $paymentMeans;
    }

    protected function getCreditcardConfig()
    {
        $shopId = Shopware()->Shop()->getId();

        $sql = 'SELECT * FROM s_plugin_mopt_payone_creditcard_config WHERE shop_id = ?';
        $configData = Shopware()->Db()->fetchRow($sql, $shopId);

        $configData['jsonConfig'] = json_encode($configData);

        if ($configData['show_errors']) {

            $langSql = 'SELECT locale FROM s_core_locales WHERE id = ?';
            $locale = Shopware()->Db()->fetchOne($langSql, $configData['error_locale_id']);
            $locale = explode('_', $locale);
            $configData['error_locale_id'] = $locale[0];
        }

        $configData['jsonConfig'] = json_encode($configData);
        return $configData;
    }
}
