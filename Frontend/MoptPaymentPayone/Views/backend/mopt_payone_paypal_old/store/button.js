Ext.define('Shopware.apps.MoptPayonePaypalOld.store.Button', {
    extend:'Shopware.store.Listing',
 
    configure: function() {
        return {
            controller: 'MoptPayonePaypalOld'
        };
    },
    model: 'Shopware.apps.MoptPayonePaypalOld.model.Button'
});