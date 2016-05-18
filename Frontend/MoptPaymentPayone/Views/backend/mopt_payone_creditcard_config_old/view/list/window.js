//{namespace name=backend/mopt_payone_creditccard_config/main}
Ext.define('Shopware.appsMoptPayoneCreditcardConfigOld.view.list.Window', {
    extend: 'Shopware.window.Listing',
    alias: 'widget.mopt-payone-creditcard-config-list-window',
    height: 450,
    width: 600,
    title : '{s name=window/title}Payone Kreditkartenkonfiguration{/s}',
 
    configure: function() {
        return {
            listingGrid: 'Shopware.appsMoptPayoneCreditcardConfigOld.view.list.Creditcardconfig',
            listingStore: 'Shopware.appsMoptPayoneCreditcardConfigOld.store.Creditcardconfig'
        };
    }
});