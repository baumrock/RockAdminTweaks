$(document).on('mousedown', '.Inputfield .aos_EditField', function (e) {
    var fieldID = $(this).attr('data-for-field'),
        // in repeaters field names are suffixed, eg. "excerpt_repeater1516"
        editFieldLink = $(this).parents('.Inputfield').eq(0).find('.aos_EditFieldLink[data-field-id="' + fieldID + '"]');

    // right click
    if (e.which === 3) return false;

    if (editFieldLink.length) {

        // if middle mouse button pressed, open a new page
        if (e.which === 2 || e.button === 4) {
            window.open(editFieldLink.attr('href').replace('&modal=1', ''));
        } else {
            editFieldLink[0].click();
        }

        return false;

    }
});

// workaround: add edit links to ajax-loaded fields
$('.Inputfield:not(.InputfieldPageListSelect)').on('reloaded', function () {
    var field = $(this),
        label = field.children('label');

    if (!label.length) return;

    if (label.find('span').length === 0) {
        field.addClass('aos_hasTooltip');
        var fieldName = label.parent().find('.InputfieldContent .aos_EditFieldLink').attr('data-field-name');

        if (!fieldName) return;

        label.contents().eq(0).wrap('<span class="title">');
        field.find('span.title').append('<em class="aos_EditField">' + fieldName + ' <i class="fa fa-pencil"></i></em>');
    }
});
