Ext.provide('Phlexible.teasers.ElementLayoutPanel');

Ext.require('Phlexible.elements.Teaser');
Ext.require('Phlexible.teasers.ElementLayoutTree');

Phlexible.teasers.ElementLayoutPanel = Ext.extend(Ext.Panel, {
    title: Phlexible.teasers.Strings.layout,
    strings: Phlexible.teasers.Strings,
    iconCls: 'p-element-tab_layout-icon',
    layout: 'border',
    border: false,

    initComponent: function () {

        this.element.on({
            load: this.onLoadElement,
            getlock: this.onGetLock,
            islocked: this.onRemoveLock,
            removelock: this.onRemoveLock,
            scope: this
        });

        this.teaserElement = new Phlexible.elements.Teaser({
            siteroot_id: this.element.siteroot_id,
            language: this.element.language
        });

        this.teaserElement.on({
            load: this.onLoadTeaser,
            scope: this
        });

        this.items = [
            {
                xtype: 'teasers-layout-tree',
                region: 'west',
                width: 200,
                split: true,
                element: this.element,
                listeners: {
                    teaserselect: function (eid) {
//                        this.dataPanel.disable();
                        this.getTopToolbar().show();
                        this.getComponent(1).getLayout().setActiveItem(0);
                        this.teaserElement.load(eid);
                    },
                    scope: this
                }
            },
            {
                region: 'center',
                layout: 'card',
                border: false,
                activeItem: 0,
                items: [{
                    xtype: 'elements-data-panel',
                    element: this.teaserElement,
                    lockElement: this.element
                }]
            }
        ];

        this.tbar = [
            {
                xtype: 'tbtext',
                text: 'Sorting of teaser elements:'
            },
            {
                text: 'Publish',
                iconCls: 'p-element-sort-publish-icon',
                handler: function () {
                    this.onPublishSort();
                },
                scope: this,
                disabled: true
            },
            '-',
            {
                text: 'Reset',
                iconCls: 'p-element-sort-reset-icon',
                handler: function () {
                    this.store.reload();
                    var tb = this.getTopToolbar();
                    tb.items.items[1].disable();
                    tb.items.items[3].disable();
                },
                scope: this,
                disabled: true
            }
        ];

        Phlexible.teasers.ElementLayoutPanel.superclass.initComponent.call(this);
    },

    onLoadElement: function () {
//        this.dataPanel.disable();
        this.getComponent(1).getLayout().setActiveItem(0);
    },

    onLoadTeaser: function (element) {
//        this.dataPanel.enable();
    },

    onPublishSort: function () {
        var records = this.store.getRange();
        //Phlexible.console.log(records);
        var data = [];
        for (var i = 0; i < records.length; i++) {
            if ((records[i].get('_type') == 'teaser' || records[i].get('_type') == 'inherit') && records[i].get('_level') == 2) {
                data.push({
                    type: records[i].get('_type'),
                    eid: records[i].id,
                    layout_id: records[i].get('_parent')
                });
            }
        }

        Ext.Ajax.request({
            url: Phlexible.Router.generate('teasers_layout_sort'),
            params: {
                eid: this.element.eid,
                data: Ext.encode(data)
            }
        });
    },

    onGetLock: function () {
        if (0 === this.getComponent(1).getLayout().activeItem) {
            this.getTopToolbar().enable();
        }
    },

    onRemoveLock: function () {
        this.getTopToolbar().disable();
    }
});

Ext.reg('teasers-layout-panel', Phlexible.teasers.ElementLayoutPanel);
