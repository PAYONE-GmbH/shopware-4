{namespace name='frontend/MoptPaymentPayone/payment'}

<div class="payment--form-group">

    {if ($moptCreditCardCheckEnvironment.payolutionConfig.payolutionB2bmode == "0" && $sUserData.billingaddress.birthday == "0000-00-00") || ( $moptCreditCardCheckEnvironment.payolutionConfig.payolutionB2bmode == "1" && $sUserData.billingaddress.birthday == "0000-00-00" && !$sUserData.billingaddress.company  ) }

        <p class ="none">
            <label for="mopt_payone__payolution_invoice_birthday">
                {s name='birthdate'}Geburtsdatum{/s}
            </label>
        </p>

        <select name="moptPaymentData[mopt_payone__payolution_invoice_birthday]" 
                id="mopt_payone__payolution_invoice_birthday" onchange="payolutionInvoiceDobInput()" 
                {if $payment_mean.id == $form_data.payment}required="required" aria-required="true"{/if}
                class="is--required{if $error_flags.mopt_payone__payolution_invoice_birthday} has--error{/if}">
            <option value="">--</option>
            {section name="birthdate" start=1 loop=32 step=1}
                <option value="{$smarty.section.birthdate.index}" 
                        {if $smarty.section.birthdate.index eq $moptCreditCardCheckEnvironment.mopt_payone__payolution_invoice_birthday}
                            selected
                        {/if}>
                    {$smarty.section.birthdate.index}</option>
                {/section}
        </select>

        <select name="moptPaymentData[mopt_payone__payolution_invoice_birthmonth]" 
                id="mopt_payone__payolution_invoice_birthmonth" onchange="payolutionInvoiceDobInput()" 
                {if $payment_mean.id == $form_data.payment}required="required" aria-required="true"{/if}
                class="is--required {if $error_flags.mopt_payone__payolution_invoice_birthmonth} has--error{/if}">
            <option value="">--</option>
            {section name="birthmonth" start=1 loop=13 step=1}
                <option value="{$smarty.section.birthmonth.index}" 
                        {if $smarty.section.birthmonth.index eq $moptCreditCardCheckEnvironment.mopt_payone__payolution_invoice_birthmonth}
                            selected
                        {/if}>
                    {$smarty.section.birthmonth.index}</option>
                {/section}
        </select>

        <select name="moptPaymentData[mopt_payone__payolution_invoice_birthyear]" 
                id="mopt_payone__payolution_invoice_birthyear" onchange="payolutionInvoiceDobInput()" 
                {if $payment_mean.id == $form_data.payment}required="required" aria-required="true"{/if}
                class="select--country is--required{if $error_flags.mopt_payone__payolution_invoice_birthyear} register--error-msg {/if}">
            <option value="">----</option>
            {section name="birthyear" loop=2016 max=100 step=-1}
                <option value="{$smarty.section.birthyear.index}" 
                        {if $smarty.section.birthyear.index eq $moptCreditCardCheckEnvironment.mopt_payone__payolution_invoice_birthyear}
                            selected
                        {/if}>
                    {$smarty.section.birthyear.index}</option>
                {/section}
        </select>
    {/if}  

    <input class="is--hidden validate-18-years" type="hidden" name="moptPaymentData[mopt_payone__payolution_invoice_birthdaydate]" id="mopt_payone__payolution_invoice_birthdaydate" value="{$sUserData.billingaddress.birthday}">   
    <div id="invoice-hint-18-years" class="is--hidden instyle_error" style="display: none;">Sie müssen mindestens 18 Jahre alt sein, um diese Zahlart verwenden zu können.</div>        

    {if $moptCreditCardCheckEnvironment.payolutionConfig.payolutionB2bmode == "1" && $sUserData.billingaddress.company}
        <p class="none">
            <label for="mopt_payone__invoice_company_trade_registry_number">
                Handelsregisternummer
            </label>
            <input name="moptPaymentData[mopt_payone__invoice_company_trade_registry_number]" type="text" id="mopt_payone__invoice_company_trade_registry_number" 
                   value="{$form_data.mopt_payone__invoice_company_trade_registry_number}" 
                   class="text {if $error_flags.mopt_payone__invoice_company_trade_registry_number}instyle_error{/if}" />
        </p>
        <input class="is--hidden" type="hidden" name="moptPaymentData[mopt_payone__payolution_b2bmode]" id="moptPaymentData[mopt_payone__payolution_b2bmode]" value="1">   
    {/if}       

    <p class="none">
        <input name="moptPaymentData[mopt_payone__payolution_invoice_agreement]" type="checkbox" id="mopt_payone__payolution_invoice_agreement" value="true"
               {if $form_data.mopt_payone__payolution_invoice_agreement eq "on" || $form_data.mopt_payone__payolution_invoice_agreement eq "true" }
                   checked="checked"
               {/if}
               class="checkbox{if $error_flags.mopt_payone__payolution_invoice_agreement} has--error{/if}"/>
        <label for="mopt_payone__payolution_invoice_agreement"  style="float:none; width:100%; display:inline">{$moptCreditCardCheckEnvironment.moptPayolutionInformation.consentInvoice}</label>
    </p>   
    
    <div class="modal" id="payolution_overlay_invoice" style="width: 800px; display: none; margin-left: -400px; position: fixed; top: 40px;">
        <p>
        <div class="ajax_modal_custom">
            <div class="heading" style="margin-left: 10px;">
                <h2>Einwilligung</h2>
                <a title="#LoginActionClose#" class="modal_close" href="#">Fenster schliessen</a>
            </div>
            <div class="inner_container" style="height: 600px;">
                {$moptCreditCardCheckEnvironment.moptPayolutionInformation.overlaycontent}   
            </div>
        </div>
        </p>
    </div>      
            
</div>    

<script type="text/javascript">
    function displayOverlayInvoice() {
        document.getElementById('payolution_overlay_invoice').style.display = "block";
    }
    function removeOverlayInvoice() {
        document.getElementById('payolution_overlay_invoice').style.display = "none";
    }

    function payolutionInvoiceDobInput()
    {
        var daySelect = document.getElementById("mopt_payone__payolution_invoice_birthday");
        var monthSelect = document.getElementById("mopt_payone__payolution_invoice_birthmonth");
        var yearSelect = document.getElementById('mopt_payone__payolution_invoice_birthyear');
        var hiddenDobFull = document.getElementById("mopt_payone__payolution_invoice_birthdaydate");
        var hiddenDobHint = document.getElementById("invoice-hint-18-years");

        if (daySelect.value == "" || monthSelect.value == "" || yearSelect.value == ""
                || hiddenDobFull.value == "" || daySelect == undefined) {
            return;
        }
        hiddenDobFull.value = yearSelect.value + "-" + monthSelect.value + "-" + daySelect.value;
        var oBirthDate = new Date(hiddenDobFull.value);
        var oMinDate = new Date(new Date().setYear(new Date().getFullYear() - 18));
        if (oBirthDate > oMinDate) {
            hiddenDobHint.style.display = "block";
        } else {
            hiddenDobHint.style.display = "none";
            return;
        }
    }

</script>        
