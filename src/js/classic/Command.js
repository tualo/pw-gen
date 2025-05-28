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

        this.records = records;

        this.list = Ext.getCmp(this.calleeId).getComponent('list')
        this.store = this.list.getStore();

        let fields = this.store.getModel().getFieldsMap();

        let has_pwgen_pass = fields['pwgen_pass'] ? true : false;
        let has_pwgen_id = fields['pwgen_id'] ? true : false;
        let has_pwgen_user = fields['pwgen_user'] ? true : false;
        let has_pwgen_hash = fields['pwgen_hash'] ? true : false;


        if (has_pwgen_pass == false) {
            Ext.toast({
                html: "Bitte fügen Sie das Feld pwgen_pass (nur leerer String) in die <b>Lese-Tabelle</b> ein.",
                title: 'Fehler',
                width: 400,
                align: 't',
                iconCls: 'fa fa-warning'
            });
        }

        if (has_pwgen_id == false) {
            Ext.toast({
                html: "Bitte fügen Sie das Feld pwgen_id in die Tabelle ein.",
                title: 'Fehler',
                width: 400,
                align: 't',
                iconCls: 'fa fa-warning'
            });
        }
        if (has_pwgen_user == false) {
            Ext.toast({
                html: "Bitte fügen Sie das Feld pwgen_user in die Tabelle ein.",
                title: 'Fehler',
                width: 400,
                align: 't',
                iconCls: 'fa fa-warning'
            });
        }
        if (has_pwgen_hash == false) {
            Ext.toast({
                html: "Bitte fügen Sie das Feld pwgen_hash in die Tabelle ein.",
                title: 'Fehler',
                width: 400,
                align: 't',
                iconCls: 'fa fa-warning'
            });
        }


        /*
                range[me.current].set('pwgen_pass', me.password[me.current].val);
                range[me.current].set('pwgen_id', me.recordid[me.current].val);
                range[me.current].set('pwgen_user', me.username[me.current].val);
                */

        this.table_name = this.store.getProxy().tablename;
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

        /*
        let o = await (await fetch('./pw-gen/' + this.table_name + '/new_unique')).json();
        if (o.success == false) {
            Ext.toast({
                html: "Es ist ein Fehler aufgetreten: " + o.msg,
                title: 'Fehler',
                width: 400,
                align: 't',
                iconCls: 'fa fa-warning'
            });
            return false;
        }
        */
        progressbar_unique.reset();
        progressbar_unique.updateProgress(1);
        progressbar_unique.updateText(' ');

        /*
        me.recordid = o.recordid;
        me.username = o.username;
        me.password = o.password;
        */
        me.current = 0;
        me.blocksize = 2000000;
        console.log('>>>>>', me.current, range.length);
        while ((await me.loopPWRange()) == false) {
            console.log('>>>>>***', me.current, range.length);
        };

        me.saveExcel();

        return true;

    },




    generateRandomPassword: function (length, includeUppercase, includeLowercase, includeNumbers, includeSpecialChars) {
        const uppercaseChars = 'ABCDEFGHJKLMNPQRSTUVWXYZ';
        const lowercaseChars = 'abcdefghijklmnopqrstuvwxyz';
        const numberChars = '123456789';
        const specialChars = '!@#$%^&*()-=_+[]{}|;:,.<>?/';

        let allChars = '';
        let password = '';

        if (includeUppercase) allChars += uppercaseChars;
        if (includeLowercase) allChars += lowercaseChars;
        if (includeNumbers) allChars += numberChars;
        if (includeSpecialChars) allChars += specialChars;

        const allCharsLength = allChars.length;

        for (let i = 0; i < length; i++) {
            const randomIndex = Math.floor(window.crypto.getRandomValues(new Uint32Array(1))[0] / (0xFFFFFFFF + 1) * allCharsLength);
            password += allChars.charAt(randomIndex);
        }

        return password;
    },

    // 

    loopPWRange: async function () {
        let me = this,
            range = me.records,
            i = 0,
            progressbar_data = me.getComponent('form').getComponent('progressbar_data');

        if (me.current < range.length) {
            range[0].store.suspendEvents() // true);

            console.log('*****', me.blocksize, me.current, range.length);
            while (i < me.blocksize && me.current < range.length) {


                range[me.current].set('pwgen_pass', me.generateRandomPassword(5, true, false, true, false));
                // range[me.current].set('pwgen_id', me.recordid[me.current].val);
                // range[me.current].set('pwgen_user', me.username[me.current].val);
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

            let pw_list = me.store.getModifiedRecords();
            for (let i = 0; i < pw_list.length; i++) {
                const salt = await bcrypt.genSalt(10);
                const hash = await bcrypt.hash(pw_list[i].get('pwgen_pass'), salt);
                progressbar_save.updateProgress((i + 1) / pw_list.length);
                pw_list[i].set('pwgen_hash', hash);

                if (i % 50 == 0) {
                    await me.set();
                }
            }

            me.store.resumeEvents();


            /*
            pw_list.forEach((item) => {
                item.commit();
            });
            */
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
        let pw_list = me.store.getModifiedRecords();
        let critical = {};
        let fields = me.store.getModel().getFieldsMap();
        for (key in fields) {
            if (
                fields.hasOwnProperty(key) && (
                    (fields[key] != null) ||
                    (me.store.getModel().getField(key).critical)
                )
            ) {
                critical[key] = true;
            }
        }


        let data = pw_list.map((item) => {
            let o = {
                pwgen_user: item.get('pwgen_user'),
                pwgen_hash: item.get('pwgen_hash'),
                pwgen_id: item.get('pwgen_id')
            }
            for (let key in critical) {
                if (item.get(key) != null) {
                    o[key] = item.get(key);
                }
            }
            return o;
        });










        let r = await (await fetch('./pw-gen/' + this.table_name + '/set', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        })).json();

        pw_list.forEach((item) => {
            item.commit();
        });

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