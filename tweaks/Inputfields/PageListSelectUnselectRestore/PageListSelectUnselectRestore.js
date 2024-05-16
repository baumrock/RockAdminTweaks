$(document).on('pageSelected', function (e, obj) {

    var clearButton = obj.a.parents('.InputfieldPageListSelect').first().find('button.clear'),
        restoreButton = obj.a.parents('.InputfieldPageListSelect').first().find('button.restore');

    if (obj.id !== 0) {
        clearButton.removeClass('empty');
    } else {
        clearButton.addClass('empty');
    }

    restoreButton.removeClass('empty').removeClass('initial');
});

$(document).on('click', '.aos_pagelist_unselect', function () {

    var button = $(this),
        parentEl = button.parent(),
        input = button.parent().find('input'),
        //titleElem = button.parent().find('.PageListSelectName .label_title');
        titleElem = button.parent().find('.PageListSelectName');

    // try without .label_title (on pageSelected the span disappears)
    //if (!titleElem.length) {
    //    titleElem = button.parent().find('.PageListSelectName');
    //}

    if (button.hasClass('clear')) {
        // clear
        input.removeAttr('value');
        titleElem.html('');
        button.addClass('empty');

        parentEl.find('button.restore[data-value-original!=""]').removeClass('empty');
        parentEl.find('button.restore').removeClass('initial');
    } else {
        // restore
        input.val(button.attr('data-value-original'));
        titleElem.html(button.attr('data-title-original'));
        button.addClass('empty');
        parentEl.find('button.clear').removeClass('empty');
    }

    // if pagelist is open, close it
    if (parentEl.find('.PageListItemOpen').length) {
        parentEl.find('a.PageListSelectActionToggle').trigger('click');
    }

    // allow dependent fields to update
    input.trigger('change');

    return false;
});
