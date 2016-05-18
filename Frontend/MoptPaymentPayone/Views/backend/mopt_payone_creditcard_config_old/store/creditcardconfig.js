Ext.define('Shopware.appsMoptPayoneCreditcardConfigOld.store.Creditcardconfig', {
    extend:'Shopware.store.Listing',
 
    configure: function() {
        return {
            controller: 'MoptPayoneCreditcardConfigOld'
        };
    },
    model: 'Shopware.appsMoptPayoneCreditcardConfigOld.model.Creditcardconfig'
});