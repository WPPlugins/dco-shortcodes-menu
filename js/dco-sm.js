(function ($) {
    function form_clear(el) {
        $(el).find(':input').each(function () {
            switch (this.type) {
                case 'password':
                case 'select-multiple':
                case 'select-one':
                case 'text':
                case 'textarea':
                    if ($(this).data('default')) {
                        $(this).val($(this).data('default'));
                    } else {
                        $(this).val('');
                    }
                    break;
                case 'checkbox':
                case 'radio':
                    $(this).prop('checked', false);
                    break;
            }
        });
    }

    $(document).ready(function () {
        //change field type
        $('.dco-sm-field-types select').change(function () {
            var val = $(this).val();
            $('.dco-sm-field-type').removeClass('show');
            $('.dco-sm-field-type' + val).addClass('show');

            if ($('.dco-sm-field-types').hasClass('edit')) {
                $('.dco-sm-field-types h3').text($('.dco-sm-field-types h3').data('edit'));
                $('.dco-sm-field-type .dco-sm-add-field').text($('.dco-sm-field-type .dco-sm-add-field').data('edit'));
            }
        });
        //add new field
        $('.dco-sm-add-field').click(function (e) {
            var $field_type = $('.dco-sm-field-type.show');

            //check required fields
            var error = false;
            $field_type.find('.required').each(function () {
                if (!$(this).val()) {
                    $(this).addClass('error');
                    error = true;
                } else {
                    $(this).removeClass('error');
                }
            });
            if (error) {
                return false;
            }

            var attr = $field_type.find('[name="name"]').val();
            var label = $field_type.find('[name="label"]').val();
            var type = $field_type.data('type');
            var type_title = $field_type.data('type-title');
            var form_data = {};
            $.each($field_type.find(':input').serializeArray(), function () {
                form_data[this.name] = this.value;
            });
            form_data['type'] = type;
            form_data['type_title'] = type_title;
            form_data = JSON.stringify(form_data);
            var row = '<tr><td class="name">' + attr + '</td><td class="label">' + label + '</td><td class="type" data-type="' + type + '">' + type_title + '</td><td><a href="#" class="button button-edit">' + dco_sm.edit + '</a> <a href="#" class="button button-delete">' + dco_sm.delete + '</a><textarea name="dco_sm_field[]" style="display: none;">' + form_data + '</textarea></td></tr>';
            if ($('.dco-sm-field-types').hasClass('edit')) {
                $('.dco-sm-fields-list tr.active').replaceWith(row);
                $('.dco-sm-field-types').removeClass('edit');
            } else {
                $('.dco-sm-fields-list tbody').append(row);
            }

            form_clear('.dco-sm-field-types');
            $('.dco-sm-field-type.show').removeClass('show');

            $('.dco-sm-field-types h3').text($('.dco-sm-field-types h3').data('add'));
            $('.dco-sm-field-type .dco-sm-add-field').text($('.dco-sm-field-type .dco-sm-add-field').data('add'));

            e.preventDefault();
        });
        //initialize attributes sortable
        $('.dco-sm-fields-list tbody').sortable({
            items: 'tr',
            axis: 'y'
        });
        //delete attribute
        $('.dco-sm-fields-list').on('click', '.button-delete', function (e) {
            var $tr = $(this).closest('tr');
            var attr = $tr.find('.name').text();
            var question = dco_sm.delete_attr.replace('%attr%', attr);
            if (confirm(question)) {
                if ($tr.hasClass('active')) {
                    form_clear('.dco-sm-field-types');
                    $('.dco-sm-field-type.show').removeClass('show');
                    $('.dco-sm-field-types h3').text($('.dco-sm-field-types h3').data('add'));
                    $('.dco-sm-field-type .dco-sm-add-field').text($('.dco-sm-field-type .dco-sm-add-field').data('add'));
                    $('.dco-sm-field-types').removeClass('edit');
                }
                $tr.remove();
            }

            e.preventDefault();
        });
        //edit attribute
        $('.dco-sm-fields-list').on('click', '.button-edit', function (e) {
            $('.dco-sm-fields-list tr').removeClass('active');
            var $tr = $(this).closest('tr');
            var $ft = $('.dco-sm-field-types');
            $tr.addClass('active');
            var type = $tr.find('.type').data('type');
            $ft.addClass('edit');
            $ft.find('option[data-type="' + type + '"]').prop('selected', true).trigger('change');
            var form_data = JSON.parse($tr.find('textarea').text());
            $.each(form_data, function (i, el) {
                var $field = $ft.find('[name="' + i + '"]');
                if ($field.length) {
                    switch ($field[0].type) {
                        case 'password':
                        case 'select-multiple':
                        case 'select-one':
                        case 'text':
                        case 'textarea':
                            $field.val(el);
                            break;
                        case 'checkbox':
                        case 'radio':
                            $field.prop('checked', el);
                    }
                }

            });
            e.preventDefault();
        });

        $('form').submit(function () {
            $('.dco-sm-field-types').find(':input').prop('disabled', true);
        });
    });
})(jQuery);