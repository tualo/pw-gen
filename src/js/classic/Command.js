Ext.define('Tualo.PWGen.commands.Command', {
    statics: {
        glyph: 'cogs',
        title: 'Daten generieren',
        tooltip: 'Daten generieren'
    },
    extend: 'Ext.panel.Panel',
    alias: ['widget.pw_gen_command'],
    requires: [
        // 'Ext.exporter.excel.Xlsx',
        'Ext.grid.plugin.Exporter'
    ],
    plugins: {
        gridexporter: true
    },
    layout: 'fit',
    items: [
        {
            xtype: 'form',
            itemId: 'form',
            bodyPadding: '25px',
            items: [
                {
                    xtype: 'label',
                    text: 'Unique-Daten ermitteln.',
                }, {
                    xtype: 'progressbar',
                    itemId: 'progressbar_unique',
                    disabled: true,
                    text: ' '
                },
                {
                    xtype: 'label',
                    text: 'Daten erzeugen.',
                }, {
                    xtype: 'progressbar',
                    itemId: 'progressbar_data',
                    disabled: true,
                    text: ' '
                },
                {
                    xtype: 'label',
                    text: 'Hashes erzeugen.',
                }, {
                    xtype: 'progressbar',
                    itemId: 'progressbar_save',
                    disabled: true,
                    text: ' '
                }
            ]
        }
    ],
    loadRecord: function (record, records, selectedrecords, parent) {
        this.record = record;
        this.table_name = record.get('table_name');
        this.records = records;

        this.list = Ext.getCmp(this.calleeId).getComponent('list')
        this.store = this.list.getStore();
        this.selectedrecords = selectedrecords;
        let me = this;

    },


    run: async function (list) {
        let me = this,
            progressbar_unique = me.getComponent('form').getComponent('progressbar_unique'),
            progressbar_data = me.getComponent('form').getComponent('progressbar_data'),
            progressbar_save = me.getComponent('form').getComponent('progressbar_save'),
            range = me.records;

        progressbar_unique.wait({
            interval: 500, //bar will move fast!
            duration: 150000,
            increment: 15,
            text: 'Updating...',
            scope: this,
            fn: function () {
            }
        });

        let o = await (await fetch('./pw-gen/' + this.table_name + '/new_unique')).json();
        progressbar_unique.reset();
        progressbar_unique.updateProgress(1);
        progressbar_unique.updateText(' ');
        me.recordid = o.recordid;
        me.username = o.username;
        me.password = o.password;
        me.current = 0;
        me.blocksize = 2000;
        console.log(me.current, range.length);
        while ((await me.loopPWRange()) == false) {

        };

        me.saveExcel();

        return true;

    },


    loopPWRange: async function () {
        let me = this,
            range = me.records,
            i = 0,
            progressbar_data = me.getComponent('form').getComponent('progressbar_data');

        if (me.current < range.length) {
            range[0].store.suspendEvents() // true);
            while (i < me.blocksize && me.current < range.length) {
                range[me.current].set('pwgen_pass', me.password[me.current].val);
                range[me.current].set('pwgen_id', me.recordid[me.current].val);
                range[me.current].set('pwgen_user', me.username[me.current].val);
                me.current++;
                i++;
            }

            range[0].store.resumeEvents();
            progressbar_data.updateProgress((me.current + 1) / range.length);

            await me.loopPWHashRange();
            return false;
        } else {
            return true;
        }
    },

    loopPWHashRange: async function () {
        let me = this,
            progressbar_save = me.getComponent('form').getComponent('progressbar_save')
            ;
        try {
            // while (me.current < me.records.length) {
            let pw_list = me.store.getModifiedRecords();
            let pws_list = pw_list.map((item) => {
                return {
                    id: item.get('__id'),
                    password: item.get('pwgen_pass')
                }
            });

            let r = await (await fetch('./pw-gen/bcrypt', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    passwords: pws_list
                })
            })).json();

            me.store.suspendEvents();
            r.data.forEach((item) => {
                let rec = me.store.findRecord('__id', item.id);
                rec.set('pwgen_hash', item.pwhash);

            });
            me.store.resumeEvents();

            await me.set();
            pw_list.forEach((item) => {
                item.commit();
            });
            progressbar_save.updateProgress((me.current + 1) / me.records.length);
            // }
        } catch (e) {
            console.log(e);
            Ext.toast({
                html: "Es ist ein Fehler aufgetreten: ",
                title: 'Fehler',
                width: 400,
                align: 't',
                iconCls: 'fa fa-warning'
            });
        }

        //progressbar_save.updateProgress(1);
    },

    saveExcel: function () {
        let me = this;


        let btncfg = {
            type: 'excel07',
            ext: 'xlsx',
            includeGroups: true,
            includeSummary: true
        };

        var cfg = Ext.merge({
            title: 'PWGen Datenexport',
            fileName: 'Datenexport' + '.' + (btncfg.ext || btncfg.type)
        }, btncfg);
        me.list.saveDocumentAs(cfg);



    },

    set: async function () {
        let me = this;
        let pw_list = me.store.getModifiedRecords(),
            data = pw_list.map((item) => {
                return {
                    id: item.get('id'),
                    pwgen_user: item.get('pwgen_user'),
                    pwgen_hash: item.get('pwgen_hash')
                }
            });

        let r = await (await fetch('./pw-gen/set', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        })).json();

    },

    singleSync: function () {
        let me = this;
        return new Promise((resolve) => {
            me.store.sync({
                callback: resolve
            });
        })
    },

    getNextText: function () {
        return 'Erzeugen';
    },


});