<div class="debit">

    {if ($moptCreditCardCheckEnvironment.payolutionConfig.payolutionB2bmode == "0" && $sUserData.billingaddress.birthday == "0000-00-00") || ( $moptCreditCardCheckEnvironment.payolutionConfig.payolutionB2bmode == "1" && $sUserData.billingaddress.birthday == "0000-00-00" && !$sUserData.billingaddress.company  ) }      
        <p class ="none">
            <label for="mopt_payone__payolution_debitnote_birthday">
                {s name='birthdate'}Geburtsdatum{/s}
            </label>
        </p>

        <select name="moptPaymentData[mopt_payone__payolution_debitnote_birthday]" 
                id="mopt_payone__payolution_debitnote_birthday" onchange="payolutionDebitNoteDobInput()" 
                {if $payment_mean.id == $form_data.payment}required="required" aria-required="true"{/if}
                class="select--country is--required{if $error_flags.mopt_payone__payolution_debitnote_birthday} has--error{/if}">
            <option value="">--</option>
            {section name="birthdate" start=1 loop=32 step=1}
                <option value="{$smarty.section.birthdate.index}" 
                        {if $smarty.section.birthdate.index eq $moptCreditCardCheckEnvironment.mopt_payone__payolution_debitnote_birthday}
                            selected
                        {/if}>
                    {$smarty.section.birthdate.index}</option>
                {/section}
        </select>

        <select name="moptPaymentData[mopt_payone__payolution_debitnote_birthmonth]" 
                id="mopt_payone__payolution_debitnote_birthmonth" onchange="payolutionDebitNoteDobInput()" 
                {if $payment_mean.id == $form_data.payment}required="required" aria-required="true"{/if}
                class="select--country is--required{if $error_flags.mopt_payone__payolution_debitnote_birthmonth} has--error{/if}">
            <option value="">-</option>
            {section name="birthmonth" start=1 loop=13 step=1}
                <option value="{$smarty.section.birthmonth.index}" 
                        {if $smarty.section.birthmonth.index eq $moptCreditCardCheckEnvironment.mopt_payone__payolution_debitnote_birthmonth}
                            selected
                        {/if}>
                    {$smarty.section.birthmonth.index}</option>
                {/section}
        </select>

        <select name="moptPaymentData[mopt_payone__payolution_debitnote_birthyear]" 
                id="mopt_payone__payolution_debitnote_birthyear" onchange="payolutionDebitNoteDobInput()" 
                {if $payment_mean.id == $form_data.payment}required="required" aria-required="true"{/if}
                class="select--country is--required{if $error_flags.mopt_payone__payolution_debitnote_birthyear} has--error{/if}">
            <option value="">----</option>
            {section name="birthyear" loop=2016 max=100 step=-1}
                <option value="{$smarty.section.birthyear.index}" 
                        {if $smarty.section.birthyear.index eq $moptCreditCardCheckEnvironment.mopt_payone__payolution_debitnote_birthyear}
                            selected
                        {/if}>
                    {$smarty.section.birthyear.index}</option>
                {/section}
        </select>
    {/if} 

    <input class="is--hidden validate-18-years" type="hidden" name="moptPaymentData[mopt_payone__payolution_debitnote_birthdaydate]" id="mopt_payone__payolution_debitnote_birthdaydate" value="{$sUserData.billingaddress.birthday}">   
    <div id="debitnote-hint-18-years" class="is--hidden  instyle_error" style="display: none;">Sie müssen mindestens 18 Jahre alt sein, um diese Zahlart verwenden zu können.</div>    


    {if $moptCreditCardCheckEnvironment.payolutionConfig.payolutionB2bmode == "1" && $sUserData.billingaddress.company}
        <p class="none">
            <label for="mopt_payone__debitnote_company_trade_registry_number">
                Handelsregisternummer
            </label>
            <input name="moptPaymentData[mopt_payone__debitnote_company_trade_registry_number]" type="text" id="mopt_payone__debitnote_company_trade_registry_number" 
                   value="{$form_data.mopt_payone__debitnote_company_trade_registry_number}" 
                   class="text {if $error_flags.mopt_payone__debitnote_company_trade_registry_number}instyle_error{/if}" />
        </p>
        <input class="is--hidden" type="hidden" name="moptPaymentData[mopt_payone__payolution_b2bmode]" id="moptPaymentData[mopt_payone__payolution_b2bmode]" value="1">   
    {/if}        


    <p class="none">
        <label for="mopt_payone__payolution_debitnote_iban">
            {s namespace='frontend/MoptPaymentPayone/payment' name='bankIBAN'}IBAN{/s}
        </label>
        <input name="moptPaymentData[mopt_payone__payolution_debitnote_iban]" type="text" id="mopt_payone__payolution_debitnote_iban" 
               value="{$form_data.mopt_payone__payolution_debitnote_iban|escape}" 
               class="text {if $error_flags.mopt_payone__payolution_debitnote_iban}instyle_error{/if}" />
    </p>
    <p class="none">
        <label for="mopt_payone__payolution_debitnote_bic">
            {s namespace='frontend/MoptPaymentPayone/payment' name='bankBIC'}BIC{/s}
        </label>
        <input name="moptPaymentData[mopt_payone__payolution_debitnote_bic]" type="text" id="mopt_payone__payolution_debitnote_bic" 
               value="{$form_data.mopt_payone__payolution_debitnote_bic|escape}" 
               class="text {if $error_flags.mopt_payone__debit_bic}instyle_error{/if}" />
    </p>    

    <p class="none clearfix">
        <input name="moptPaymentData[mopt_payone__payolution_debitnote_agreement]" type="checkbox" id="mopt_payone__payolution_debitnote_agreement" value="true"
               {if $form_data.mopt_payone__payolution_debitnote_agreement eq "on" || $form_data.mopt_payone__payolution_debitnote_agreement eq "true" }
                   checked="checked"
               {/if}
               class="checkbox{if $error_flags.mopt_payone__payolution_debitnote_agreement} has--error{/if}"/>
        <label for="mopt_payone__payolution_debitnote_agreement"  style="float:none; width:100%; display:inline">{$moptCreditCardCheckEnvironment.moptPayolutionInformation.consentDebit}</label>
    </p>    
    <div class="register--required-info">{$moptCreditCardCheckEnvironment.moptPayolutionInformation.legalTerm}</div>    

    <div class="modal" id="payolution_overlay_debit" style="width: 800px; display: none; margin-left: -400px; position: fixed; top: 40px;">
        <p>
        <div class="ajax_modal_custom">
            <div class="heading"  style="margin-left: 10px;">
                <h2>Einwilligung</h2>
                <a title="Fenster schliessen" class="modal_close" href="#">Fenster schliessen</a>
            </div>
            <div class="inner_container" style="height: 600px;">
                {$moptCreditCardCheckEnvironment.moptPayolutionInformation.overlaycontent}   
            </div>
        </div>
        </p>
    </div>  
            
    <p class="none clearfix">
        <input name="moptPaymentData[mopt_payone__payolution_debitnote_agreement2]" type="checkbox" id="mopt_payone__payolution_debitnote_agreement2" value="true"
               {if $form_data.mopt_payone__payolution_debitnote_agreement2 eq "on" || $form_data.mopt_payone__payolution_debitnote_agreement2 eq "true"}
                   checked="checked"
               {/if}
               class="checkbox{if $error_flags.mopt_payone__payolution_debitnote_agreement2} has--error{/if}"/>
        <label for="mopt_payone__payolution_debitnote_agreement2"  style="float:none; width:100%; display:inline">{$moptCreditCardCheckEnvironment.moptPayolutionInformation.sepaagreement}</label>
    </p>  

</div>

<script type="text/javascript">
    $('#mopt_payone__payolution_debitnote_iban').focus(function () {
        $('#payment_mean{$payment_mean.id}').attr('checked', true);
        $('#moptSavePayment{$payment_mean.id}').slideDown();
        $('input[type="radio"]:not(:checked)').trigger('change');
    });

    $('#mmopt_payone__payolution_debitnote_bic').focus(function () {
        $('#payment_mean{$payment_mean.id}').attr('checked', true);
        $('#moptSavePayment{$payment_mean.id}').slideDown();
        $('input[type="radio"]:not(:checked)').trigger('change');
    });

    $(document).ready(function () {
        $('#moptSavePaymentButton{$payment_mean.id}').attr('onClick', "");
        $("#moptSavePaymentButton{$payment_mean.id}").click(function (event) {
            var valid = moptValidateAndSaveDebit{$payment_mean.id}();
            if (!valid)
            {
                event.preventDefault();
            } else
            {
                MoptSubmitPaymentForm();
            }
        });
    });

    function moptValidateAndSaveDebit{$payment_mean.id}() {
    {literal}
            var ibanbicReg = /^[A-Z0-9 ]+$/;
            var numberReg = /^[0-9 ]+$/;
            var bankCodeReg = /^(?:\s*[0-9]\s*){8}$/;
            var formNotValid = false;
    {/literal}

            $(".moptFormError").remove();

            $('#mopt_payone__payolution_debitnote_iban').removeClass('instyle_error');
            $('#mopt_payone__payolution_debitnote_bic').removeClass('instyle_error');

            if ($('#mopt_payone__payolution_debitnote_iban').val() && !ibanbicReg.test($('#mopt_payone__payolution_debitnote_iban').val())) {
                $('#mopt_payone__payolution_debitnote_iban').addClass('instyle_error');
                $('#mopt_payone__payolution_debitnote_iban').parent().after('<div class="error moptFormError">{s namespace="frontend/MoptPaymentPayone/errorMessages" name="ibanbicFormField"}Dieses Feld darf nur Großbuchstaben und Ziffern enthalten{/s}</div>');
                formNotValid = true;
            }

            if ($('#mopt_payone__payolution_debitnote_bic').val() && !ibanbicReg.test($('#mopt_payone__payolution_debitnote_bic').val())) {
                $('#mopt_payone__payolution_debitnote_bic').addClass('instyle_error');
                $('#mopt_payone__payolution_debitnote_bic').parent().after('<div class="error moptFormError">{s namespace="frontend/MoptPaymentPayone/errorMessages" name="ibanbicFormField"}Dieses Feld darf nur Großbuchstaben und Ziffern enthalten{/s}</div>');
                formNotValid = true;
            }

            if (formNotValid)
            {
                return false;
            }

            return true;
        }
        ;

        function displayOverlayDebit() {
            document.getElementById('payolution_overlay_debit').style.display = "block";
        }
        function removeOverlayDebit() {
            document.getElementById('payolution_overlay_debit').style.display = "none";
        }

        function payolutionDebitNoteDobInput()
        {
            var daySelect = document.getElementById("mopt_payone__payolution_debitnote_birthday");
            var monthSelect = document.getElementById("mopt_payone__payolution_debitnote_birthmonth");
            var yearSelect = document.getElementById('mopt_payone__payolution_debitnote_birthyear');
            var hiddenDobFull = document.getElementById("mopt_payone__payolution_debitnote_birthdaydate");
            var hiddenDobHint = document.getElementById("debitnote-hint-18-years");

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