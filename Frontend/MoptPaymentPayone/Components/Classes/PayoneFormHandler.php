<?php

/**
 * $Id: $
 */
class Mopt_PayoneFormHandler
{

  /**
   * process payment form
   *
   * @param string $paymentId
   * @param array $formData
   * @param Mopt_PayonePaymentHelper $paymentHelper
   * @return array payment data 
   */
  public function processPaymentForm($paymentId, $formData, $paymentHelper)
  {
    if ($paymentHelper->isPayoneCreditcard($paymentId))
    {
      return $this->proccessCreditCard($formData);
    }

    if ($paymentHelper->isPayoneSofortuerberweisung($paymentId))
    {
      return $this->proccessSofortueberweisung($formData);
    }

    if ($paymentHelper->isPayoneGiropay($paymentId))
    {
      return $this->proccessGiropay($formData);
    }

    if ($paymentHelper->isPayoneEPS($paymentId))
    {
      return $this->proccessEps($formData);
    }

    if ($paymentHelper->isPayoneIDeal($paymentId))
    {
      return $this->proccessIdeal($formData);
    }

    if ($paymentHelper->isPayoneDebitnote($paymentId))
    {
      return $this->proccessDebitNote($formData);
    }

    if ($paymentHelper->isPayoneKlarnaInstallment($paymentId))
    {
      return $this->proccessKlarnaInstallment($formData);
    }

    if ($paymentHelper->isPayoneKlarna($paymentId))
    {
      return $this->proccessKlarna($formData);
    }

    return array();
  }

  /**
   * process form data 
   *
   * @param array $formData
   * @return array 
   */
  protected function proccessSofortueberweisung($formData)
  {
    $paymentData = array();

    if ($formData["mopt_payone__sofort_bankcountry"] == 'not_choosen')
    {
      $paymentData['sErrorFlag']["mopt_payone__sofort_bankcountry"] = true;
    }
    else
    {
      $paymentData['formData']["mopt_payone__sofort_bankcountry"] = $formData["mopt_payone__sofort_bankcountry"];
    }
    
    if (!$formData["mopt_payone__sofort_bankaccount"])
    {
      $paymentData['sErrorFlag']["mopt_payone__sofort_bankaccount"] = true;
    }
    else
    {
      $paymentData['formData']["mopt_payone__sofort_bankaccount"] = $formData["mopt_payone__sofort_bankaccount"];
    }
    
    if (!$formData["mopt_payone__sofort_bankcode"])
    {
      $paymentData['sErrorFlag']["mopt_payone__sofort_bankcode"] = true;
    }
    else
    {
      $paymentData['formData']["mopt_payone__sofort_bankcode"] = $formData["mopt_payone__sofort_bankcode"];
    }
    
    if (!$formData["mopt_payone__sofort_iban"])
    {
      $paymentData['sErrorFlag']["mopt_payone__sofort_iban"] = true;
    }
    else
    {
      $paymentData['formData']["mopt_payone__sofort_iban"] = $formData["mopt_payone__sofort_iban"];
    }
    
    if (!$formData["mopt_payone__sofort_bic"])
    {
      $paymentData['sErrorFlag']["mopt_payone__sofort_bic"] = true;
    }
    else
    {
      $paymentData['formData']["mopt_payone__sofort_bic"] = $formData["mopt_payone__sofort_bic"];
    }

    if($paymentData['sErrorFlag']["mopt_payone__sofort_iban"] 
            && $paymentData['sErrorFlag']["mopt_payone__sofort_bic"] 
            && !$paymentData['sErrorFlag']["mopt_payone__sofort_bankaccount"] 
            && !$paymentData['sErrorFlag']["mopt_payone__sofort_bankcode"] 
            )
    {
      unset($paymentData['sErrorFlag']["mopt_payone__sofort_iban"]);
      unset($paymentData['sErrorFlag']["mopt_payone__sofort_bic"]);
    }

    if(!$paymentData['sErrorFlag']["mopt_payone__sofort_iban"] 
            && !$paymentData['sErrorFlag']["mopt_payone__sofort_bic"] 
            && $paymentData['sErrorFlag']["mopt_payone__sofort_bankaccount"] 
            && $paymentData['sErrorFlag']["mopt_payone__sofort_bankcode"] 
            )
    {
      unset($paymentData['sErrorFlag']["mopt_payone__sofort_bankaccount"]);
      unset($paymentData['sErrorFlag']["mopt_payone__sofort_bankcode"]);
    }
    
    if (count($paymentData['sErrorFlag']))
    {
      return $paymentData;
    }

    $paymentData['formData']['onlinebanktransfertype'] = Payone_Api_Enum_OnlinebanktransferType::INSTANT_MONEY_TRANSFER;

    return $paymentData;
  }

  /**
   * process form data 
   *
   * @param array $formData
   * @return array 
   */
  protected function proccessGiropay($formData)
  {
    $paymentData = array();

    if (!$formData["mopt_payone__giropay_iban"])
    {
      $paymentData['sErrorFlag']["mopt_payone__giropay_iban"] = true;
    }
    else
    {
      $paymentData['formData']["mopt_payone__giropay_iban"] = $formData["mopt_payone__giropay_iban"];
    }
    
    if (!$formData["mopt_payone__giropay_bic"])
    {
      $paymentData['sErrorFlag']["mopt_payone__giropay_bic"] = true;
    }
    else
    {
      $paymentData['formData']["mopt_payone__giropay_bic"] = $formData["mopt_payone__giropay_bic"];
    }

    if (count($paymentData['sErrorFlag']))
    {
      return $paymentData;
    }

    $paymentData['formData']['onlinebanktransfertype']           = Payone_Api_Enum_OnlinebanktransferType::GIROPAY;
    $paymentData['formData']['mopt_payone__giropay_bankcountry'] = 'DE';

    return $paymentData;
  }

  /**
   * process form data 
   *
   * @param array $formData
   * @return array 
   */
  protected function proccessEps($formData)
  {
    $paymentData = array();

    if ($formData["mopt_payone__eps_bankgrouptype"] == 'not_choosen')
    {
      $paymentData['sErrorFlag']["mopt_payone__eps_bankgrouptype"] = true;
    }
    else
    {
      $paymentData['formData']["mopt_payone__eps_bankgrouptype"] = $formData["mopt_payone__eps_bankgrouptype"];
      $paymentData['formData']['onlinebanktransfertype']         = Payone_Api_Enum_OnlinebanktransferType::EPS_ONLINE_BANK_TRANSFER;
      $paymentData['formData']['mopt_payone__eps_bankcountry']   = 'AT';
    }

    return $paymentData;
  }

  /**
   * process form data 
   *
   * @param array $formData
   * @return array 
   */
  protected function proccessIdeal($formData)
  {
    $paymentData = array();

    if ($formData["mopt_payone__ideal_bankgrouptype"] == 'not_choosen')
    {
      $paymentData['sErrorFlag']["mopt_payone__ideal_bankgrouptype"] = true;
    }
    else
    {
      $paymentData['formData']["mopt_payone__ideal_bankgrouptype"] = $formData["mopt_payone__ideal_bankgrouptype"];
      $paymentData['formData']['onlinebanktransfertype']           = Payone_Api_Enum_OnlinebanktransferType::IDEAL;
      $paymentData['formData']['mopt_payone__ideal_bankcountry']   = 'NL';
    }

    return $paymentData;
  }

  /**
   * process form data 
   *
   * @param array $formData
   * @return array 
   */
  protected function proccessDebitNote($formData)
  {
    $paymentData = array();

    //bankaccount/code or bic/iban
    
    if (!$formData["mopt_payone__debit_iban"])
    {
      $paymentData['sErrorFlag']["mopt_payone__debit_iban"] = true;
    }
    else
    {
      $paymentData['formData']["mopt_payone__debit_iban"] = $formData["mopt_payone__debit_iban"];
    }
    
    if (!$formData["mopt_payone__debit_bic"])
    {
      $paymentData['sErrorFlag']["mopt_payone__debit_bic"] = true;
    }
    else
    {
      $paymentData['formData']["mopt_payone__debit_bic"] = $formData["mopt_payone__debit_bic"];
    }
    
    if (!$formData["mopt_payone__debit_bankaccount"])
    {
      $paymentData['sErrorFlag']["mopt_payone__debit_bankaccount"] = true;
    }
    else
    {
      $paymentData['formData']["mopt_payone__debit_bankaccount"] = $formData["mopt_payone__debit_bankaccount"];
    }
    
    if (!$formData["mopt_payone__debit_bankcode"])
    {
      $paymentData['sErrorFlag']["mopt_payone__debit_bankcode"] = true;
    }
    else
    {
      $paymentData['formData']["mopt_payone__debit_bankcode"] = $formData["mopt_payone__debit_bankcode"];
    }
    
    if (!$formData["mopt_payone__debit_bankaccountholder"])
    {
      $paymentData['sErrorFlag']["mopt_payone__debit_bankaccountholder"] = true;
    }
    else
    {
      $paymentData['formData']["mopt_payone__debit_bankaccountholder"] = $formData["mopt_payone__debit_bankaccountholder"];
    }
    
    if ($formData["mopt_payone__debit_bankcountry"] == 'not_choosen')
    {
      $paymentData['sErrorFlag']["mopt_payone__debit_bankcountry"] = true;
    }
    else
    {
      $paymentData['formData']["mopt_payone__debit_bankcountry"] = $formData["mopt_payone__debit_bankcountry"];
    }

    if($paymentData['sErrorFlag']["mopt_payone__debit_iban"] 
            && $paymentData['sErrorFlag']["mopt_payone__debit_bic"] 
            && !$paymentData['sErrorFlag']["mopt_payone__debit_bankaccount"] 
            && !$paymentData['sErrorFlag']["mopt_payone__debit_bankcode"] 
            )
    {
      unset($paymentData['sErrorFlag']["mopt_payone__debit_iban"]);
      unset($paymentData['sErrorFlag']["mopt_payone__debit_bic"]);
    }

    if(!$paymentData['sErrorFlag']["mopt_payone__debit_iban"] 
            && !$paymentData['sErrorFlag']["mopt_payone__debit_bic"] 
            && $paymentData['sErrorFlag']["mopt_payone__debit_bankaccount"] 
            && $paymentData['sErrorFlag']["mopt_payone__debit_bankcode"] 
            )
    {
      unset($paymentData['sErrorFlag']["mopt_payone__debit_bankaccount"]);
      unset($paymentData['sErrorFlag']["mopt_payone__debit_bankcode"]);
    }
    
    return $paymentData;
  }

  /**
   * process form data 
   *
   * @param array $formData
   * @return array 
   */
  protected function proccessCreditCard($formData)
  {
    $paymentData = array();
    $paymentData['formData'] = $formData;
    return $paymentData;
  }

  /**
   * process form data 
   *
   * @param array $formData
   * @return array 
   */
  protected function proccessKlarna($formData)
  {
    $paymentData = array();

    if (!$formData["mopt_payone__klarna_telephone"])
    {
      $paymentData['sErrorFlag']["mopt_payone__klarna_telephone"] = true;
    }
    else
    {
      $paymentData['formData']["mopt_payone__klarna_telephone"] = $formData["mopt_payone__klarna_telephone"];
    }

    if (!$formData["mopt_payone__klarna_agreement"] || $formData["mopt_payone__klarna_agreement"] !== 'on')
    {
      $paymentData['sErrorFlag']["mopt_payone__klarna_agreement"] = true;
    }
    else
    {
      $paymentData['formData']["mopt_payone__klarna_agreement"] = $formData["mopt_payone__klarna_agreement"];
    }

    if (!$formData["mopt_payone__klarna_birthyear"])
    {
      $paymentData['sErrorFlag']["mopt_payone__klarna_birthyear"] = true;
    }
    else
    {
      $paymentData['formData']["mopt_payone__klarna_birthyear"] = $formData["mopt_payone__klarna_birthyear"];
    }

    if (!$formData["mopt_payone__klarna_birthmonth"])
    {
      $paymentData['sErrorFlag']["mopt_payone__klarna_birthmonth"] = true;
    }
    else
    {
      $paymentData['formData']["mopt_payone__klarna_birthmonth"] = $formData["mopt_payone__klarna_birthmonth"];
    }

    if (!$formData["mopt_payone__klarna_birthday"])
    {
      $paymentData['sErrorFlag']["mopt_payone__klarna_birthday"] = true;
    }
    else
    {
      $paymentData['formData']["mopt_payone__klarna_birthday"] = $formData["mopt_payone__klarna_birthday"];
    }
     $paymentData['formData']['mopt_save_birthday_and_phone'] = true;
    
     if($paymentData['sErrorFlag']["mopt_payone__klarna_telephone"] 
             || $paymentData['sErrorFlag']["mopt_payone__klarna_birthyear"] 
             || $paymentData['sErrorFlag']["mopt_payone__klarna_birthmonth"] 
             || $paymentData['sErrorFlag']["mopt_payone__klarna_birthday"])
     {
       $paymentData['formData']['mopt_save_birthday_and_phone'] = false;
     }
     
    return $paymentData;
  }

  /**
   * process form data 
   *
   * @param array $formData
   * @return array 
   */
  protected function proccessKlarnaInstallment($formData)
  {
    $paymentData = array();

    if (!$formData["mopt_payone__klarna_inst_telephone"])
    {
      $paymentData['sErrorFlag']["mopt_payone__klarna_inst_telephone"] = true;
    }
    else
    {
      $paymentData['formData']["mopt_payone__klarna_inst_telephone"] = $formData["mopt_payone__klarna_inst_telephone"];
    }

    if (!$formData["mopt_payone__klarna_inst_agreement"] || $formData["mopt_payone__klarna_inst_agreement"] !== 'on')
    {
      $paymentData['sErrorFlag']["mopt_payone__klarna_inst_agreement"] = true;
    }
    else
    {
      $paymentData['formData']["mopt_payone__klarna_inst_agreement"] = $formData["mopt_payone__klarna_inst_agreement"];
    }

    if (!$formData["mopt_payone__klarna_inst_birthyear"])
    {
      $paymentData['sErrorFlag']["mopt_payone__klarna_inst_birthyear"] = true;
    }
    else
    {
      $paymentData['formData']["mopt_payone__klarna_inst_birthyear"] = $formData["mopt_payone__klarna_inst_birthyear"];
    }

    if (!$formData["mopt_payone__klarna_inst_birthmonth"])
    {
      $paymentData['sErrorFlag']["mopt_payone__klarna_inst_birthmonth"] = true;
    }
    else
    {
      $paymentData['formData']["mopt_payone__klarna_inst_birthmonth"] = $formData["mopt_payone__klarna_inst_birthmonth"];
    }

    if (!$formData["mopt_payone__klarna_inst_birthday"])
    {
      $paymentData['sErrorFlag']["mopt_payone__klarna_inst_birthday"] = true;
    }
    else
    {
      $paymentData['formData']["mopt_payone__klarna_inst_birthday"] = $formData["mopt_payone__klarna_inst_birthday"];
    }
     $paymentData['formData']['mopt_save_birthday_and_phone'] = true;
    
     if($paymentData['sErrorFlag']["mopt_payone__klarna_inst_telephone"] 
             || $paymentData['sErrorFlag']["mopt_payone__klarna_inst_birthyear"] 
             || $paymentData['sErrorFlag']["mopt_payone__klarna_inst_birthmonth"] 
             || $paymentData['sErrorFlag']["mopt_payone__klarna_inst_birthday"])
     {
       $paymentData['formData']['mopt_save_birthday_and_phone'] = false;
     }
     
    return $paymentData;
  }

}