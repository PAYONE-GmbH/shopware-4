<div class="register">
  <div class="heading">
    <h2>
      {s namespace='frontend/MoptPaymentPayone/payment' name='confirmAddressTitle'}Adresse Bestätigen{/s}
    </h2>
  </div>
  <div style="padding: 25px;">
    <p class="none">
    <h3>
      {s namespace='frontend/MoptPaymentPayone/payment' name='originalAddressTitle'}Eingegebene Adresse:{/s}
    </h3>
    {$moptAddressCheckOriginalAddress.street} {$moptAddressCheckOriginalAddress.streetnumber}<br>
    {$moptAddressCheckOriginalAddress.zipcode}<br>
    {$moptAddressCheckOriginalAddress.city}
    </p>
    <p class="none" style="margin-top: 25px;">
    <h3>
      {s namespace='frontend/MoptPaymentPayone/payment' name='correctedAddressTitle'}Korrigierte Adresse:{/s}
    </h3>
    {$moptAddressCheckCorrectedAddress.streetname} {$moptAddressCheckCorrectedAddress.streetnumber}<br>
    {$moptAddressCheckCorrectedAddress.zip}<br>
    {$moptAddressCheckCorrectedAddress.city}
    </p>

    <p class="none" style="margin-top: 25px;">
      <input class="button-middle large left" type="submit" onclick="saveCorrectedAddress();" 
             value="{s namespace='frontend/MoptPaymentPayone/payment' name='saveDataButtonLabel'}Daten übernehmen{/s}"/>
      <input style="margin-bottom: 25px;" class="button-middle large left" type="submit" onclick="saveOriginalAddress();" 
             value="{s namespace='frontend/MoptPaymentPayone/payment' name='dontSaveDataButtonLabel'}Daten nicht übernehmen{/s}"/>
    </p>
  </div>
</div>

<script type="text/javascript">
  <!--
  function saveOriginalAddress()
  {
    jQuery.post('{url controller="moptPaymentPayone" action="saveOriginalAddress" forceSecure}', function()
    {
      window.location = "{url controller=account action=saveBilling sTarget=$moptAddressCheckTarget forceSecure}";
    });
  }

  function saveCorrectedAddress()
  {
    jQuery.post('{url controller="moptPaymentPayone" action="saveCorrectedAddress" forceSecure}', function()
    {
      window.location = "{url controller=account action=saveBilling sTarget=$moptAddressCheckTarget forceSecure}";
    });
  }
  // -->
</script>