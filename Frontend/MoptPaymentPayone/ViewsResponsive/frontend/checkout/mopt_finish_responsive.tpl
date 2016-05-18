{extends file="frontend/checkout/finish.tpl"}

{block name="frontend_checkout_finish_teaser" append}
  {if $moptPaymentConfigParams.moptMandateDownloadEnabled}
      <div class="row hidden-print">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-body">
                  <a href="{url controller=moptPaymentPayone action=downloadMandate forceSecure}" 
                    target="_blank">
                    {s name='mandateDownload' namespace='frontend/MoptPaymentPayone/payment'}Download Mandat{/s}
                  </a>
                </div>
            </div>
        </div>
    </div>
  {/if}
  {if $moptBarzahlenCode}
    {$moptBarzahlenCode}
  {/if}
{/block}