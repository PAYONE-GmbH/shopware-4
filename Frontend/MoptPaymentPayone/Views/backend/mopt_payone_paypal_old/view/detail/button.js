//{namespace name=backend/mopt_payone_paypal/main}
Ext.define('Shopware.apps.MoptPayonePaypalOld.view.detail.Button', {
    extend: 'Shopware.model.Container',
    padding: 20,
    configure: function () {

        return {
            controller: 'MoptPayonePaypalOld',
            fieldSets: [{
                    title: '{s name=details/title}Payone PayPal Button-Details{/s}',
                    layout: 'fit',
                    fields: {
                        localeId: {
                            fieldLabel: '{s name=language}Sprache{/s}',
                            name: 'localeId',
                            allowBlank: false
                        },
                        image: {
                            fieldLabel: '{s name=button}PayPal Button{/s}',
                            xtype: 'mediaselectionfield',
                            allowBlank: false
                        },
                        isDefault: '{s name=default}Default{/s}'
                    }
                }]
        };
    }
});
             