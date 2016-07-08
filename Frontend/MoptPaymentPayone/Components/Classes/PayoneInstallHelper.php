<?php

/**
 * $Id: $
 */
class Mopt_PayoneInstallHelper
{

  /**
   * returns array of PAYONE payment methods
   *
   * payment types are grouped
   * mopt_payone__[group]_[brand]
   *
   * @return array
   */
    public function mopt_payone__getPaymentMethods()
    {
        return array(
        array(
            'name'        => 'mopt_payone__cc_visa',
            'description' => 'PAYONE Visa',
            'template'    => 'mopt_paymentmean_creditcard.tpl',
            'position'    => 1,),
        array(
            'name'        => 'mopt_payone__cc_mastercard',
            'description' => 'PAYONE Mastercard',
            'template'    => 'mopt_paymentmean_creditcard.tpl',
            'position'    => 2,),
        array(
            'name'        => 'mopt_payone__cc_american_express',
            'description' => 'PAYONE American Express',
            'template'    => 'mopt_paymentmean_creditcard.tpl',
            'position'    => 3,),
        array(
            'name'        => 'mopt_payone__cc_carte_blue',
            'description' => 'PAYONE Carte Blue',
            'template'    => 'mopt_paymentmean_creditcard.tpl',
            'position'    => 4,),
        array(
            'name'        => 'mopt_payone__cc_diners_club',
            'description' => 'PAYONE Diners Club',
            'template'    => 'mopt_paymentmean_creditcard.tpl',
            'position'    => 5,),
        array(
            'name'        => 'mopt_payone__cc_discover',
            'description' => 'PAYONE Discover',
            'template'    => 'mopt_paymentmean_creditcard.tpl',
            'position'    => 6,),
        array(
            'name'        => 'mopt_payone__cc_jcb',
            'description' => 'PAYONE JCB',
            'template'    => 'mopt_paymentmean_creditcard.tpl',
            'position'    => 7,),
        array(
            'name'        => 'mopt_payone__cc_maestro_international',
            'description' => 'PAYONE Maestro International',
            'template'    => 'mopt_paymentmean_creditcard.tpl',
            'position'    => 8,),
        array(
            'name'        => 'mopt_payone__ibt_sofortueberweisung',
            'description' => 'PAYONE Sofortüberweisung',
            'template'    => 'mopt_paymentmean_sofort.tpl',
            'position'    => 9,),
        array(
            'name'        => 'mopt_payone__ibt_giropay',
            'description' => 'PAYONE Giropay',
            'template'    => 'mopt_paymentmean_giropay.tpl',
            'position'    => 10,),
        array(
            'name'        => 'mopt_payone__ibt_eps',
            'description' => 'PAYONE eps',
            'template'    => 'mopt_paymentmean_eps.tpl',
            'position'    => 11,),
        array(
            'name'        => 'mopt_payone__ibt_post_efinance',
            'description' => 'PAYONE Post-Finance EFinance',
            'template'    => null,
            'position'    => 12,),
        array(
            'name'        => 'mopt_payone__ibt_post_finance_card',
            'description' => 'PAYONE Post-Finance Card',
            'template'    => null,
            'position'    => 13,),
        array(
            'name'        => 'mopt_payone__ibt_ideal',
            'description' => 'PAYONE iDeal',
            'template'    => 'mopt_paymentmean_ideal.tpl',
            'position'    => 14,),
        array(
            'name'        => 'mopt_payone__ewallet_paypal',
            'description' => 'PAYONE PayPal',
            'template'    => null,
            'position'    => 15,),
        array(
            'name'        => 'mopt_payone__acc_debitnote',
            'description' => 'PAYONE Lastschrift',
            'template'    => 'mopt_paymentmean_debit.tpl',
            'position'    => 16,),
        array(
            'name'        => 'mopt_payone__acc_invoice',
            'description' => 'PAYONE Offene Rechnung',
            'template'    => null,
            'position'    => 17,),
        array(
            'name'        => 'mopt_payone__acc_payinadvance',
            'description' => 'PAYONE Vorkasse',
            'template'    => null,
            'position'    => 18,),
        array(
            'name'        => 'mopt_payone__acc_cashondel',
            'description' => 'PAYONE Nachnahme',
            'template'    => null,
            'position'    => 19,),
        array(
            'name'        => 'mopt_payone__fin_billsafe',
            'description' => 'PAYONE BillSAFE',
            'template'    => null,
            'position'    => 20,),
        array(
            'name'        => 'mopt_payone__fin_klarna',
            'description' => 'PAYONE Klarna',
            'template'    => 'mopt_paymentmean_klarna.tpl',
            'position'    => 21,),
        array(
            'name'        => 'mopt_payone__ibt_p24',
            'description' => 'PAYONE P24',
            'template'    => null,
            'position'    => 22,),
        array(
            'name'        => 'mopt_payone__creditcard_iframe',
            'description' => 'PAYONE Kreditkarte',
            'template'    => null,
            'position'    => 23,),
        array(
                'name' => 'mopt_payone__csh_barzahlen',
                'description' => 'PAYONE Barzahlen',
                'template' => null,
                'position' => 24,
                'additionalDescription' => '<label for="payment_barzahlen" style="float: initial;">'
                    . '  <img style="height: 3em; vertical-align: -1em;" src="https://cdn.barzahlen.de/images/barzahlen_logo.png" alt="Barzahlen">'
                    . '</label>'
                    . '<img style="float: right; margin-left: 10px; max-width: 180px; max-height: 180px;" src="https://cdn.barzahlen.de/images/barzahlen_special.png">'
                    . 'Mit Abschluss der Bestellung bekommen Sie einen Zahlschein angezeigt, den Sie sich ausdrucken oder auf Ihr Handy schicken lassen können. Bezahlen Sie den Online-Einkauf mit Hilfe des Zahlscheins an der Kasse einer Barzahlen-Partnerfiliale.<br/><br/>'
                    . '<strong>Bezahlen Sie bei:</strong>'
                    . ' '
                    . '<img src="https://cdn.barzahlen.de/images/barzahlen_partner_01.png" style="height: 1em; vertical-align: -0.1em;">'
                    . '<img src="https://cdn.barzahlen.de/images/barzahlen_partner_02.png" style="height: 1em; vertical-align: -0.1em;">'
                    . '<img src="https://cdn.barzahlen.de/images/barzahlen_partner_03.png" style="height: 1em; vertical-align: -0.1em;">'
                    . '<img src="https://cdn.barzahlen.de/images/barzahlen_partner_04.png" style="height: 1em; vertical-align: -0.1em;">'
                    . '<img src="https://cdn.barzahlen.de/images/barzahlen_partner_05.png" style="height: 1em; vertical-align: -0.1em;">'
                    . '<img src="https://cdn.barzahlen.de/images/barzahlen_partner_06.png" style="height: 1em; vertical-align: -0.1em;">'
                    . '<img src="https://cdn.barzahlen.de/images/barzahlen_partner_07.png" style="height: 1em; vertical-align: -0.1em;">'
                    . '<img src="https://cdn.barzahlen.de/images/barzahlen_partner_08.png" style="height: 1em; vertical-align: -0.1em;">'
                    . '<img src="https://cdn.barzahlen.de/images/barzahlen_partner_09.png" style="height: 1em; vertical-align: -0.1em;">'
                    . '<img src="https://cdn.barzahlen.de/images/barzahlen_partner_10.png" style="height: 1em; vertical-align: -0.1em;">'),
        
        array(
            'name'        => 'mopt_payone__ewallet_paydirekt',
            'description' => 'PAYONE Paydirekt',
            'template'    => null,
            'position'    => 25,),
        array(
            'name' => 'mopt_payone__fin_payolution_invoice',
            'description' => 'PAYONE Payolution Rechnung',
            'template' => 'mopt_paymentmean_payolution_invoice.tpl',
            'position' => 26,),
        array(
            'name' => 'mopt_payone__fin_payolution_debitnote',
            'description' => 'PAYONE Payolution Lastschrift',
            'template' => 'mopt_paymentmean_payolution_debitnote.tpl',
            'position' => 27,),
        );


    }

  /**
   * add payment data table
   */
    public function moptCreatePaymentDataTable()
    {
        $sql = "CREATE TABLE IF NOT EXISTS `s_plugin_mopt_payone_payment_data` (`userId` int(11) NOT NULL,`moptPaymentData`"
            . " text NOT NULL, PRIMARY KEY (`userId`))";
        Shopware()->Db()->exec($sql);
    }

  /**
   * extend config data table with sepa config coloumns
   */
    public function moptExtendConfigDataTable()
    {
        $sql = "ALTER TABLE `s_plugin_mopt_payone_config` "
            . "ADD COLUMN show_accountnumber TINYINT(1) NOT NULL DEFAULT 0,"
            . "ADD COLUMN mandate_active TINYINT(1) NOT NULL DEFAULT 0,"
            . "ADD COLUMN mandate_download_enabled TINYINT(1) NOT NULL DEFAULT 0;";
        Shopware()->Db()->exec($sql);
    }

  /**
   * extend config data table with klarna config coloumn
   */
    public function moptExtendConfigKlarnaDataTable()
    {
        $sql = "ALTER TABLE `s_plugin_mopt_payone_config` "
            . "ADD COLUMN klarna_store_id VARCHAR(255) DEFAULT 0;";
        Shopware()->Db()->exec($sql);
    }

  /**
   * extend config data table with showBic config coloumn
   */
    public function fcExtendConfigShowBicDataTable()
    {
        $sql = "ALTER TABLE `s_plugin_mopt_payone_config` "
              . "ADD COLUMN show_bic TINYINT(1) NOT NULL DEFAULT 0;";
        Shopware()->Db()->exec($sql);
    }

  /**
   * extend config data table with showSofortIbanBic config column
   */
    public function fcExtendConfigShowSofortIbanBicDataTable()
    {
        $sql = "ALTER TABLE `s_plugin_mopt_payone_config` "
              . "ADD COLUMN show_sofort_iban_bic TINYINT(1) NOT NULL DEFAULT 0;";
        Shopware()->Db()->exec($sql);
    }

  /**
   * extend config data table with save terms coloumn
   */
    public function moptExtendConfigSaveTermsDataTable()
    {
        $sql = "ALTER TABLE `s_plugin_mopt_payone_config` "
            . "ADD COLUMN save_terms VARCHAR(255) DEFAULT 0;";
        Shopware()->Db()->exec($sql);
    }

  /**
   * extend config data table with save terms coloumn
   */
    public function moptExtendConfigPaypalEcsDataTable()
    {
        $sql = "ALTER TABLE `s_plugin_mopt_payone_config` "
            . "ADD COLUMN paypal_ecs_active TINYINT(1) NOT NULL DEFAULT 0;";
        Shopware()->Db()->exec($sql);
    }

  /**
   * extend config data table with payolution config coloumn
   */
    public function moptExtendConfigPayolutionDataTable()
    {
        $sql = "SELECT value FROM s_core_config_values "
              . "WHERE element_id = '893';";
        $result = Shopware()->Db()->query($sql);
        $serializedCompanyName = $result->fetchColumn(0);
        $companyName = unserialize((string) $serializedCompanyName);
        if ($companyName) {
            $sql = "ALTER TABLE `s_plugin_mopt_payone_config` "
                  . "ADD COLUMN payolution_company_name VARCHAR(255) DEFAULT '" . $companyName . "' ,"
                  . "ADD COLUMN payolution_b2bmode TINYINT(1) NOT NULL DEFAULT 1;";
        } else {
            $sql = "ALTER TABLE `s_plugin_mopt_payone_config` "
                  . "ADD COLUMN payolution_company_name VARCHAR(255) DEFAULT 'Ihr Firmenname' ,"
                  . "ADD COLUMN payolution_b2bmode TINYINT(1) NOT NULL DEFAULT 1;";
        }
        Shopware()->Db()->exec($sql);
    }
  
  /**
   * extend config data table with save terms coloumn
   */
    public function moptExtendConfigCreditcardMinValidDays()
    {
        $sql = "ALTER TABLE `s_plugin_mopt_payone_config` "
              . "ADD COLUMN creditcard_min_valid INT(11) NOT NULL DEFAULT 0;";
        Shopware()->Db()->exec($sql);
    }
  
  /**
   * extend config table for addresscheck country configuration
   */
    public function moptExtendConfigAddressCheckCountries()
    {
        $sql = "ALTER TABLE `s_plugin_mopt_payone_config` "
              . "ADD COLUMN adresscheck_billing_countries VARCHAR(255);";
        $sql1 = "ALTER TABLE `s_plugin_mopt_payone_config` "
              . "ADD COLUMN adresscheck_shipping_countries VARCHAR(255);";
        Shopware()->Db()->exec($sql);
        Shopware()->Db()->exec($sql1);
    }
  
  /**
   * fix order_ne coloumn from transaction log model
   */
    public function updateTransactionLogModel()
    {
        $sql = 'alter table s_plugin_mopt_payone_transaction_log change order_nr order_nr varchar(100)';
        Shopware()->Db()->query($sql);
    }

    /**
   * insert document extensions (used to display additional PAYONE data) if not already stored in DB
   */
    public function moptInsertDocumentsExtensionIntoDatabaseIfNotExist()
    {
        $sql    = 'SELECT * FROM s_core_documents_box WHERE name = ? OR name = ?';
        $result = Shopware()->Db()->query($sql, array('PAYONE_Footer', 'PAYONE_Content_Info'));

        if ($result->rowCount() < 2) {
        // add PAYONE block for documents
            $sql = "INSERT INTO `s_core_documents_box` (`documentID`, `name`, `style`, `value`) VALUES
	(1, 'PAYONE_Footer', 'width: 170mm;\r\nposition:fixed;\r\nbottom:-20mm;\r\nheight: 15mm;', ?),
	(1, 'PAYONE_Content_Info', ?, ?);";
            Shopware()->Db()->query($sql, array(
            '<table style="height: 90px;" border="0" width="100%">'
            . '<tbody>'
            . '<tr valign="top">'
            . '<td style="width: 33%;">'
            . '<p><span style="font-size: xx-small;">Demo GmbH</span></p>'
            . '<p><span style="font-size: xx-small;">Steuer-Nr <br />UST-ID: <br />Finanzamt '
            . '</span><span style="font-size: xx-small;">Musterstadt</span></p>'
            . '</td>'
            . '<td style="width: 33%;">'
            . '<p><span style="font-size: xx-small;">AGB<br /></span></p>'
            . '<p><span style="font-size: xx-small;">Gerichtsstand ist Musterstadt<br />'
            . 'Erf&uuml;llungsort Musterstadt</span></p>'
            . '</td>'
            . '<td style="width: 33%;">'
            . '<p><span style="font-size: xx-small;">Gesch&auml;ftsf&uuml;hrer</span></p>'
            . '<p><span style="font-size: xx-small;">Max Mustermann</span></p>'
            . '</td>'
            . '</tr>'
            . '</tbody>'
            . '</table>',
            '.payment_instruction, .payment_instruction td, .payment_instruction tr {'
            . '	margin: 0;'
            . '	padding: 0;'
            . '	border: 0;'
            . '	font-size:8px;'
            . '	font: inherit;'
            . '	vertical-align: baseline;'
            . '}'
            . '.payment_note {'
            . '	font-size: 10px;'
            . '	color: #333;'
            . '}',
            '<div class="payment_note">'
            . '<br/>'
            . '{$instruction.clearing_instructionnote}<br/>'
            . '{$instruction.clearing_legalnote}}<br/><br/>'
            . '</div>'
            . '<table class="payment_instruction">'
            . '<tr>'
            . '	<td>Empfänger:</td>'
            . '	<td>{$instruction.clearing_bankaccountholder}</td>'
            . '</tr>'
            . '<tr>'
            . '	<td>Kontonr.:</td>'
            . '	<td>{$instruction.clearing_bankaccount}</td>'
            . '</tr>'
            . '<tr>'
            . '	<td>BLZ:</td>'
            . '	<td>{$instruction.clearing_bankcode}</td>'
            . '</tr>'
            . '<tr>'
            . '	<td>IBAN:</td>'
            . '	<td>{$instruction.clearing_bankiban}</td>'
            . '</tr>'
            . '<tr>'
            . '	<td>BIC:</td>'
            . '	<td>{$instruction.clearing_bankbic}</td>'
            . '</tr>'
            . '<tr>'
            . '	<td>Bank:</td>'
            . '	<td>{$instruction.clearing_bankname}</td>'
            . '</tr>'
            . '<tr>'
            . '	<td>Betrag:</td>'
            . '	<td>{$instruction.amount|currency}</td>'
            . '</tr>'
            . '<tr>'
            . '	<td>Verwendungszweck:</td>'
            . '	<td>{$instruction.clearing_reference}</td>'
            . '</tr>'
            . '</table>'
            ));
        }
    }

  /**
   * insert empty config on installations to keep the shop stable
   * without any config and activated plugin exceptions will occur during checkout
   */
    public function moptInsertEmptyConfigIfNotExists()
    {
        $sql    = 'SELECT id FROM s_plugin_mopt_payone_config WHERE payment_id = 0;';
        $result = Shopware()->Db()->query($sql);

        if ($result->rowCount() === 0) {
            $sql = "INSERT INTO `s_plugin_mopt_payone_config` (`payment_id`, `merchant_id`, `portal_id`, `subaccount_id`, `api_key`, `live_mode`, `authorisation_method`, `submit_basket`, `adresscheck_active`, `adresscheck_live_mode`, `adresscheck_billing_adress`, `adresscheck_shipping_adress`, `adresscheck_automatic_correction`, `adresscheck_failure_handling`, `adresscheck_min_basket`, `adresscheck_max_basket`, `adresscheck_lifetime`, `adresscheck_failure_message`, `map_person_check`, `map_know_pre_lastname`, `map_know_lastname`, `map_not_known_pre_lastname`, `map_multi_name_to_adress`, `map_undeliverable`, `map_person_dead`, `map_wrong_adress`, `consumerscore_active`, `consumerscore_live_mode`, `consumerscore_check_moment`, `consumerscore_check_mode`, `consumerscore_default`, `consumerscore_lifetime`, `consumerscore_min_basket`, `consumerscore_max_basket`, `consumerscore_failure_handling`, `consumerscore_note_message`, `consumerscore_note_active`, `consumerscore_agreement_message`, `consumerscore_agreement_active`, `consumerscore_abtest_value`, `consumerscore_abtest_active`, `payment_specific_data`, `state_appointed`, `state_capture`, `state_paid`, `state_underpaid`, `state_cancelation`, `state_refund`, `state_debit`, `state_reminder`, `state_vauthorization`, `state_vsettlement`, `state_transfer`, `state_invoice`, `check_cc`, `check_account`, `trans_appointed`, `trans_capture`, `trans_paid`, `trans_underpaid`, `trans_cancelation`, `trans_refund`, `trans_debit`, `trans_reminder`, `trans_vauthorization`, `trans_vsettlement`, `trans_transfer`, `trans_invoice`) VALUES
      (0, 0, 0, 0, '0', 0, 'Vorautorisierung', 0, 0, 0, 1, 0, 0, 0, 0, 0, 0, 'Es ist ein Fehler aufgetreten', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 'IH', 0, 0, 0, 0, 0, '', 0, '', 0, 0, 0, 'N;', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, NULL, '', '', '', '', '', '', '', '', '', '', '', '');
      ";
            Shopware()->Db()->query($sql);
        }
    
      // insert default values for creditcard config
        $sql = 'SELECT id FROM s_plugin_mopt_payone_creditcard_config';
        $result = Shopware()->Db()->query($sql);

        if ($result->rowCount() === 0) {
            $sql = "INSERT INTO `s_plugin_mopt_payone_creditcard_config` (`error_locale_id`, `shop_id`, `show_errors`, `integration_type`, `standard_input_css`, `standard_input_css_selected`, `standard_iframe_height`, `standard_iframe_width`, `cardno_input_chars`, `cardno_input_chars_max`, `cardno_input_css`, `cardno_custom_iframe`, `cardno_iframe_height`, `cardno_iframe_width`, `cardno_custom_style`, `cardno_field_type`, `cardcvc_input_chars`, `cardcvc_input_chars_max`, `cardcvc_input_css`, `cardcvc_custom_iframe`, `cardcvc_iframe_height`, `cardcvc_iframe_width`, `cardcvc_custom_style`, `cardcvc_field_type`, `cardmonth_input_chars`, `cardmonth_input_chars_max`, `cardmonth_input_css`, `cardmonth_custom_iframe`, `cardmonth_iframe_height`, `cardmonth_iframe_width`, `cardmonth_custom_style`, `cardmonth_field_type`, `cardyear_input_chars`, `cardyear_input_chars_max`, `cardyear_input_css`, `cardyear_custom_iframe`, `cardyear_iframe_height`, `cardyear_iframe_width`, `cardyear_custom_style`, `cardyear_field_type`, `merchant_id`, `portal_id`, `subaccount_id`, `api_key`, `live_mode`, `check_cc`, `creditcard_min_valid`) VALUES
      (1, 1, 1, 0, 'box-shadow:inset 0 1px 1px #dadae5;background:#f8f8fa;border:1px solid #dadae5;border-top-color:#cbcbdb;line-height:19px;font-size:.875rem;width:85%;padding:.625rem .625rem .5625rem .625rem;color:#8798a9;','box-shadow:inset 0 1px 1px #dadae5;background:#f8f8fa;border:1px solid #dadae5;border-top-color:#cbcbdb;line-height:19px;font-size:.875rem;width:85%;padding:.625rem .625rem .5625rem .625rem;color:#8798a9;', '20px', '80px', 20, 20, '', 0, '20px', '100px', 1, 'tel', 4, 4, '', 0, '20px', '100px', 1, 'tel', 4, 4, '', 0, '20px', '40px', 1, 'select', 4, 4, '', 0, '20px', '40px', 1, 'select', 0, 0, 0, '', 0, 1, 0);
      ";
            Shopware()->Db()->query($sql);
        }
    }

  /**
   * check if user attributes are already extended
   *
   * @return boolean
   */
    public function moptUserAttributesExist()
    {
        $DBConfig = Shopware()->Db()->getConfig();
    
        $sql    = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='s_user_attributes'
                AND TABLE_SCHEMA='". $DBConfig['dbname'] ."'
                AND COLUMN_NAME ='mopt_payone_consumerscore_result'";
        $result = Shopware()->Db()->query($sql);

        if ($result->rowCount() === 0) {
            return false;
        }

        return true;
    }

  /**
   * check if billing address attributes are already extended
   *
   * @return boolean
   */
    public function moptBillingAddressAttributesExist()
    {
        $DBConfig = Shopware()->Db()->getConfig();
    
        $sql    = "SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='s_user_billingaddress_attributes'
               AND TABLE_SCHEMA='". $DBConfig['dbname'] ."'
                AND COLUMN_NAME ='mopt_payone_addresscheck_result'";
        $result = Shopware()->Db()->query($sql);

        if ($result->rowCount() === 0) {
            return false;
        }

        return true;
    }

  /**
   * check if shipping address attributes are already extended
   *
   * @return boolean
   */
    public function moptShippingAddressAttributesExist()
    {
        $DBConfig = Shopware()->Db()->getConfig();
    
        $sql    = "SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='s_user_shippingaddress_attributes'
                AND TABLE_SCHEMA='". $DBConfig['dbname'] ."'
                AND COLUMN_NAME ='mopt_payone_addresscheck_result'";
        $result = Shopware()->Db()->query($sql);

        if ($result->rowCount() === 0) {
            return false;
        }

        return true;
    }

  /**
   * check if order attributes are already extended
   *
   * @return boolean
   */
    public function moptOrderAttributesExist()
    {
        $DBConfig = Shopware()->Db()->getConfig();
    
        $sql    = "SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='s_order_attributes'
                AND TABLE_SCHEMA='". $DBConfig['dbname'] ."'
                AND COLUMN_NAME ='mopt_payone_txid'";
        $result = Shopware()->Db()->query($sql);

        if ($result->rowCount() === 0) {
            return false;
        }

        return true;
    }

    /**
     * check if order attributes for Payolution Payments are already extended
     *
     * @return boolean
     */
    public function moptPayolutionWorkOrderIdAttributeExist()
    {
        $DBConfig = Shopware()->Db()->getConfig();

        $sql = "SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='s_order_attributes'
                AND TABLE_SCHEMA='" . $DBConfig['dbname'] . "'
                AND COLUMN_NAME ='mopt_payone_payolution_workorder_id'";
        $result = Shopware()->Db()->query($sql);

        if ($result->rowCount() === 0) {
            return false;
        }
        return true;
    }

    /**
     * check if order attributes for Payolution Payments are already extended
     *
     * @return boolean
     */
    public function moptPayolutionClearingReferenceAttributeExist()
    {
        $DBConfig = Shopware()->Db()->getConfig();

        $sql = "SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='s_order_attributes'
                AND TABLE_SCHEMA='" . $DBConfig['dbname'] . "'
                AND COLUMN_NAME ='mopt_payone_payolution_clearing_reference'";
        $result = Shopware()->Db()->query($sql);

        if ($result->rowCount() === 0) {
            return false;
        }
        return true;
    }

  /**
   * check if order details attributes are already extended
   *
   * @return boolean
   */
    public function moptOrderDetailsAttributesExist()
    {
        $DBConfig = Shopware()->Db()->getConfig();
    
        $sql    = "SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='s_order_details_attributes'
                AND TABLE_SCHEMA='". $DBConfig['dbname'] ."'
                AND COLUMN_NAME ='mopt_payone_payment_status'";
        $result = Shopware()->Db()->query($sql);

        if ($result->rowCount() === 0) {
            return false;
        }

        return true;
    }

  /**
   * check if 2nd order details attributes extension is already made
   * needed to for new shipping costs handling
   *
   * @return boolean
   */
    public function moptOrderAttributesShippingCostsExist()
    {
        $DBConfig = Shopware()->Db()->getConfig();
    
        $sql    = "SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='s_order_attributes'
                AND TABLE_SCHEMA='". $DBConfig['dbname'] ."'
                AND COLUMN_NAME ='mopt_payone_ship_captured'";
        $result = Shopware()->Db()->query($sql);

        if ($result->rowCount() === 0) {
            return false;
        }

        return true;
    }

  /**
   * check if 3rd order attributes extension is already made
   * needed to for abo commerce support
   *
   * @return boolean
   */
    public function moptOrderAttributesPaymentDataExist()
    {
        $DBConfig = Shopware()->Db()->getConfig();
    
        $sql    = "SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='s_order_attributes'
                AND TABLE_SCHEMA='". $DBConfig['dbname'] ."'
                AND COLUMN_NAME ='mopt_payone_payment_data'";
        $result = Shopware()->Db()->query($sql);

        if ($result->rowCount() === 0) {
            return false;
        }

        return true;
    }
  
  /**
   * check if 4th order attributes extension is already made
   * needed to save orderhash
   *
   * @return boolean
   */
    public function moptOrderAttributesOrderHashExist()
    {
        $DBConfig = Shopware()->Db()->getConfig();
    
        $sql    = "SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='s_order_attributes'
                AND TABLE_SCHEMA='". $DBConfig['dbname'] ."'
                AND COLUMN_NAME ='mopt_payone_order_hash'";
        $result = Shopware()->Db()->query($sql);

        if ($result->rowCount() === 0) {
            return false;
        }

        return true;
    }

  /**
   * check if payone configuration is already extended for sepa options
   *
   * @return boolean
   */
    public function moptPayoneConfigExtensionExist()
    {
        $DBConfig = Shopware()->Db()->getConfig();
    
        $sql    = "SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='s_plugin_mopt_payone_config'
                AND TABLE_SCHEMA='". $DBConfig['dbname'] ."'
                AND COLUMN_NAME ='show_accountnumber'";
        $result = Shopware()->Db()->query($sql);

        if ($result->rowCount() === 0) {
            return false;
        }

        return true;
    }

  /**
   * check if payone configuration is already extended for klarna options
   *
   * @return boolean
   */
    public function moptPayoneConfigKlarnaExtensionExist()
    {
        $DBConfig = Shopware()->Db()->getConfig();
    
        $sql    = "SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='s_plugin_mopt_payone_config'
                AND TABLE_SCHEMA='". $DBConfig['dbname'] ."'
                AND COLUMN_NAME ='klarna_store_id'";
        $result = Shopware()->Db()->query($sql);

        if ($result->rowCount() === 0) {
            return false;
        }

        return true;
    }

  /**
   * check if payone configuration is already extended for showBic config option
   *
   * @return boolean
   */
    public function fcPayoneConfigShowBicExtensionExist()
    {
        $DBConfig = Shopware()->Db()->getConfig();
        $sql = "SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='s_plugin_mopt_payone_config'
              AND TABLE_SCHEMA='" . $DBConfig['dbname'] . "'
              AND COLUMN_NAME ='show_bic';";
        $result = Shopware()->Db()->query($sql);

        if ($result->rowCount() === 0) {
            return false;
        }

        return true;
    }

  /**
   * check if payone configuration is already extended for show_sofort_iban_bic config option
   *
   * @return boolean
   */
    public function fcPayoneConfigShowSofortIbanBicExtensionExist()
    {
        $DBConfig = Shopware()->Db()->getConfig();

        $sql = "SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='s_plugin_mopt_payone_config'
              AND TABLE_SCHEMA='" . $DBConfig['dbname'] . "'
              AND COLUMN_NAME ='show_sofort_iban_bic';";
        $result = Shopware()->Db()->query($sql);

        if ($result->rowCount() === 0) {
            return false;
        }

        return true;
    }

  /**
   * check if payone configuration is already extended for payolution payment
   *
   * @return boolean
   */
    public function moptPayoneConfigPayolutionExtensionExist()
    {
        $DBConfig = Shopware()->Db()->getConfig();

        $sql = "SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='s_plugin_mopt_payone_config'
              AND TABLE_SCHEMA='" . $DBConfig['dbname'] . "'
              AND COLUMN_NAME ='payolution_company_name';";
        $result = Shopware()->Db()->query($sql);

        if ($result->rowCount() === 0) {
            return false;
        }

        return true;
    }
  
  /**
   * check if payone configuration is already extended with save terms option
   *
   * @return boolean
   */
    public function moptPayoneConfigSaveTermsExtensionExist()
    {
        $DBConfig = Shopware()->Db()->getConfig();
    
        $sql    = "SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='s_plugin_mopt_payone_config'
                AND TABLE_SCHEMA='". $DBConfig['dbname'] ."'
                AND COLUMN_NAME ='save_terms'";
        $result = Shopware()->Db()->query($sql);

        if ($result->rowCount() === 0) {
            return false;
        }

        return true;
    }
  
  /**
   * check if payone configuration is already extended with paypal ecs option
   *
   * @return boolean
   */
    public function moptPayoneConfigPaypalEcsExtensionExist()
    {
        $DBConfig = Shopware()->Db()->getConfig();
    
        $sql    = "SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='s_plugin_mopt_payone_config'
                AND TABLE_SCHEMA='". $DBConfig['dbname'] ."'
                AND COLUMN_NAME ='paypal_ecs_active'";
        $result = Shopware()->Db()->query($sql);

        if ($result->rowCount() === 0) {
            return false;
        }

        return true;
    }
  
  /**
   * check if payone configuration is already extended country config for risk checks
   *
   * @return boolean
   */
    public function moptPayoneConfigRiskCountryExtensionExist()
    {
        $DBConfig = Shopware()->Db()->getConfig();
    
        $sql    = "SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='s_plugin_mopt_payone_config'
                AND TABLE_SCHEMA='". $DBConfig['dbname'] ."'
                AND COLUMN_NAME ='creditcard_min_valid'";
        $result = Shopware()->Db()->query($sql);

        if ($result->rowCount() === 0) {
            return false;
        }

        return true;
    }
  
  /**
   * check if ordernr column has correct type
   *
   * @return boolean
   */
    public function isTransactionLogModelUpdated()
    {
        $DBConfig = Shopware()->Db()->getConfig();
    
        $sql    = "SELECT DATA_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='s_plugin_mopt_payone_transaction_log'
                AND TABLE_SCHEMA='". $DBConfig['dbname'] ."'
                AND COLUMN_NAME ='order_nr'";
        $result = Shopware()->Db()->query($sql);
      //$sql1    = "SHOW FIELDS FROM s_plugin_mopt_payone_transaction_log WHERE Field='order_nr'";
      //$sql2    = "describe ". $DBConfig['dbname'] .".s_plugin_mopt_payone_transaction_log order_nr";

        $type = $result->fetch();
    
        if ($type['DATA_TYPE'] === 'int') {
            return false;
        }

        return true;
    }
  
  /**
   * check if the old PAYONE plugin is active and rename label to prevent errors
   *
   * @return string
   */
    public function moptGetConfigurationLabelName()
    {
        $sql    = "SELECT id FROM s_core_menu
                WHERE name = 'Konfiguration'
                AND controller <> 'MoptConfigPayone'";
        $result = Shopware()->Db()->query($sql);

        if ($result->rowCount() === 0) {
            return 'Konfiguration';
        }

        return 'PAYONE Konfiguration';
    }
  
    /**
     * check if later added coloumns already exist
     *
     * @return boolean
     */
    public function isCreditCardConfigModelUpdated()
    {
        $DBConfig = Shopware()->Db()->getConfig();

        $sql = "SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='s_plugin_mopt_payone_creditcard_config'
                AND TABLE_SCHEMA='" . $DBConfig['dbname'] . "'
                AND COLUMN_NAME ='merchant_id'";
        $result = Shopware()->Db()->query($sql);

        if ($result->rowCount() === 0) {
            return false;
        }

        return true;
    }

    /**
     * extend config table for addresscheck country configuration
     */
    public function updateCreditCardConfigModel()
    {
        $sql = "ALTER TABLE `s_plugin_mopt_payone_creditcard_config` "
        . "ADD COLUMN merchant_id INT(11) NOT NULL DEFAULT 0,"
        . "ADD COLUMN portal_id INT(11) NOT NULL DEFAULT 0,"
        . "ADD COLUMN subaccount_id INT(11) NOT NULL DEFAULT 0,"
        . "ADD COLUMN api_key VARCHAR(100) NOT NULL,"
        . "ADD COLUMN live_mode TINYINT(1) NOT NULL DEFAULT 0,"
        . "ADD COLUMN check_cc TINYINT(1) NOT NULL DEFAULT 1,"
        . "ADD COLUMN creditcard_min_valid INT(11) DEFAULT 0;";
        
        Shopware()->Db()->exec($sql);
    }
}
