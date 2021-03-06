Ext.provide('Phlexible.elements.accordion.Configuration');

Phlexible.elements.accordion.Configuration = Ext.extend(Ext.form.FormPanel, {
    strings: Phlexible.elements.Strings.configuration,
    title: Phlexible.elements.Strings.configuration.configuration,
    cls: 'p-elements-page-accordion',
    iconCls: 'p-element-action-icon',
    border: false,
    autoHeight: true,
    labelWidth: 100,
    bodyStyle: 'padding: 5px',
    labelAlign: 'top',

    key: 'configuration',

    initComponent: function () {
        this.populateItems();

        Phlexible.elements.accordion.Configuration.superclass.initComponent.call(this);
    },

    populateItems: function () {
        this.items = [
            {
                // 0
                xtype: 'checkbox',
                name: 'navigation',
                hideLabel: true,
                boxLabel: this.strings.in_navigation
            },{
                // 1
                xtype: 'textfield',
                name: 'template',
                fieldLabel: this.strings.template
            },
            {
                // 2
                xtype: 'checkbox',
                name: 'robotsNoIndex',
                fieldLabel: this.strings.robots,
                boxLabel: this.strings.robots_no_index
            },
            {
                // 3
                xtype: 'checkbox',
                name: 'robotsNoFollow',
                hideLabel: true,
                boxLabel: this.strings.robots_no_follow
            },
            {
                // 4
                xtype: 'checkbox',
                name: 'searchNoIndex',
                fieldLabel: this.strings.internal_search,
                boxLabel: this.strings.search_no_index
            }
        ];
    },

    load: function (data) {
        if (data.properties.et_type !== 'full' && data.properties.et_type !== 'part') {
            this.hide();
            return;
        }

        this.getForm().setValues({
            navigation: data.configuration.navigation || false,
            template: data.configuration.template || '',
            robotsNoIndex: data.configuration.robotsNoIndex || false,
            robotsNoFollow: data.configuration.robotsNoFollow || false,
            searchNoIndex: data.configuration.searchNoIndex || false
        });

        if (data.properties.et_type === 'part') {
            this.getComponent(0).hide();
            this.getComponent(1).show();
            this.getComponent(2).hide();
            this.getComponent(3).hide();
            this.getComponent(4).hide();
        } else {
            this.getComponent(0).show();
            this.getComponent(1).show();
            this.getComponent(2).show();
            this.getComponent(3).show();
            this.getComponent(4).show();
        }

        this.show();
    },

    getData: function () {
        var form = this.getComponent(0).getForm();

        return form.getValues();
    }
});

Ext.reg('elements-configurationaccordion', Phlexible.elements.accordion.Configuration);
