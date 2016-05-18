Ext.define('Shopware.apps.MoptPayonePaypalOld.model.Button', {
    extend: 'Shopware.data.Model',
    configure: function () {
        return {
            controller: 'MoptPayonePaypalOld',
            detail: 'Shopware.apps.MoptPayonePaypalOld.view.detail.Button'
        };
    },
 
    fields: [
        { name: 'id', type: 'int', useNull: true},
        { name: 'localeId', type: 'int' },
        { name: 'image', type: 'string'},
        { name: 'isDefault', type: 'boolean'}
    ],
    
    associations: [
        {
            relation: 'ManyToOne',
            field: 'localeId',
            
            type: 'hasMany',
            model: 'Shopware.apps.Base.model.Locale',
            name: 'getLocale',
            associationKey: 'locale'
        }]
});
 
