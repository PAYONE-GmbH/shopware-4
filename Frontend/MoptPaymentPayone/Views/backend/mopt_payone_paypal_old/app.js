//Shopware backend application components
{include file='backend/mopt_payone_paypal_old/application/Shopware.model.Helper.js'}
{include file='backend/mopt_payone_paypal_old/application/Shopware.grid.Controller.js'}
{include file='backend/mopt_payone_paypal_old/application/Shopware.grid.Panel.js'}
{include file='backend/mopt_payone_paypal_old/application/Shopware.data.Model.js'}
{include file='backend/mopt_payone_paypal_old/application/Shopware.store.Listing.js'}
{include file='backend/mopt_payone_paypal_old/application/Shopware.window.Detail.js'}
{include file='backend/mopt_payone_paypal_old/application/Shopware.window.Listing.js'}
{include file='backend/mopt_payone_paypal_old/application/Shopware.window.Progress.js'}
{include file='backend/mopt_payone_paypal_old/application/Shopware.model.DataOperation.js'}
{include file='backend/mopt_payone_paypal_old/application/Shopware.grid.Association.js'}
{include file='backend/mopt_payone_paypal_old/application/Shopware.model.Container.js'}
{include file='backend/mopt_payone_paypal_old/application/Shopware.form.field.Search.js'}
{include file='backend/mopt_payone_paypal_old/application/Shopware.detail.Controller.js'}
{include file='backend/mopt_payone_paypal_old/application/Shopware.listing.InfoPanel.js'}
{include file='backend/mopt_payone_paypal_old/application/Shopware.listing.FilterPanel.js'}
{include file='backend/mopt_payone_paypal_old/application/Shopware.filter.Field.js'}

{include file='backend/mopt_payone_paypal_old/application/Shopware.store.Association.js'}
{include file='backend/mopt_payone_paypal_old/application/Shopware.form.field.Media.js'}

Ext.define('Shopware.apps.MoptPayonePaypalOld', {
    extend: 'Enlight.app.SubApplication',
 
    name:'Shopware.apps.MoptPayonePaypalOld',
 
    loadPath: '{url action=load}',
    bulkLoad: true,
 
    controllers: [ 'Main' ],
 
    views: [
        'list.Window',
        'list.Button',
        'detail.Window',
        'detail.Button'
    ],
 
    models: [ 'Button' ],
    stores: [ 'Button' ],
 
    launch: function() {
        return this.getController('Main').mainWindow;
    }
});