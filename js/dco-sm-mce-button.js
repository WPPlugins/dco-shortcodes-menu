(function ($) {
    if (!$.isEmptyObject(shortcodes)) {
        tinymce.PluginManager.add('dco_sm_mce_button', function (editor, url) {
            var shortcodeValues = [];
            $.each(shortcodes, function () {
                var code = this.name;
                var fields = this.fields;
                if (!fields) {
                    shortcodeValues.push({
                        text: code,
                        onselect: function (e) {
                            var text = editor.selection.getContent();
                            editor.selection.setContent('[' + code + ']' + text + '[/' + code + ']');
                        },
                    });
                } else {
                    shortcodeValues.push({
                        text: code,
                        onclick: function () {
                            editor.windowManager.open({
                                title: code,
                                body: fields,
                                onsubmit: function (e) {
                                    var text = editor.selection.getContent();
                                    var attr = '';
                                    $.each(e.data, function (i, el) {
                                        if ($.trim(i)) {
                                            attr += ' ' + i + '="' + el.replace(/\r?\n/g, '<br>') + '"';
                                        }
                                    });
                                    editor.selection.setContent('[' + code + ' ' + attr + ']' + text + '[/' + code + ']');
                                }
                            });
                        }
                    });
                }
            });
            editor.addButton('dco_sm_mce_button', {
                text: dco_sm_shortcodes_title,
                icon: false,
                type: 'menubutton',
                menu: shortcodeValues,
            });
        });
    }
})(jQuery);