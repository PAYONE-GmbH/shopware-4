{extends file="frontend/checkout/finish.tpl"}

{block name="frontend_checkout_finish_teaser" append}
 {if $moptPaymentConfigParams.moptMandateDownloadEnabled}
<div class="doublespace">&nbsp;</div>
<div class="trustedshops_form">
  <a href="{url controller=moptPaymentPayone action=downloadMandate forceSecure}" target="_blank">
    {s name='mandateDownload' namespace='frontend/MoptPaymentPayone/payment'}Download Mandat{/s}
  </a>
</div>
{/if}
{if $moptBarzahlenCode}
    {$moptBarzahlenCode}
{/if}
{/block}