function initAsmSelectBox(inputfield_id) {
    var $asmSelect = $('#wrap_' + inputfield_id + ' select.asmSelect'),
        placeholder;

    if (!$asmSelect.length) {
        window.requestAnimationFrame(function () {
            initAsmSelectBox(inputfield_id);
        });
        return false;
    }

    // add data-asm-placeholder for existing placeholder
    var asmSelect2Config = {},
        $placeholderOption = $asmSelect.find('option[selected]:not([value])');

    if ($placeholderOption.length) {
        placeholder = $placeholderOption.text();
        asmSelect2Config.placeholder = placeholder;
        $asmSelect.attr('data-asm-placeholder', placeholder);
        $placeholderOption.empty(); // placeholder in select2.js needs an empty option
    }

    $asmSelect.select2(asmSelect2Config);
}

$(document).ready(function () {

    var select2Config = {}/*,
    keepAsmSearchTerm = AsmTweaksSettings.indexOf('asmSearchBoxKeepTerm') !== -1;*/

    $(document).on('change', '.asmSelect ~ select', function () {

        var src = event && (event.target || event.srcElement);

        // asmSelect remove icon click
        if (src && src.tagName === 'I') {
            var $asmSelect = $(this).parents('.asmContainer').first().find('.asmSelect');
            $asmSelect.select2('destroy');
            restoreAsmSelectBoxPlaceholder($asmSelect, select2Config);
            $asmSelect.select2(select2Config);
        }
    });


    // save scroll position
    $(document).on('select2:selecting', '.asmSelect', function () {
        $(this).attr('data-scroll-position', $('.select2-results__options').scrollTop());
    });


    $(document).on('select2:select', '.asmSelect', function (event) {

        var $asmSelect = $(this),
            src = event.target || event.srcElement,
            inputSelector = '.select2-search__field',
            searchTermAttr = 'data-select2-search-term',
            searchTerm = $(inputSelector).val()/*,
            keepListOpen = AsmTweaksSettings.indexOf('asmSearchBoxKeepListOpen') !== -1*/;

        // select an item in select2 dropdown
        if (src.tagName === 'SELECT') {

            // save search term in parent's data attr
            if (keepAsmSearchTerm) {
                $asmSelect.parent().attr(searchTermAttr, searchTerm);
            }

            $asmSelect.select2('destroy');
            restoreAsmSelectBoxPlaceholder($asmSelect, select2Config);
            $asmSelect.select2(select2Config);

            if (!keepListOpen) {
                return false;
            }

            $asmSelect.select2('open');

            // restore previous search term
            if (keepAsmSearchTerm) {
                var $input = $(inputSelector);
                $input.val($asmSelect.parent().attr(searchTermAttr));
                $input.trigger('keyup');
                if ($input.val && $input.setSelectionRange) {
                    var len = $input.value.length * 2;
                    $input.setSelectionRange(len, len);
                }
            }

            // restore scroll position
            $(".select2-results__options").scrollTop($asmSelect.attr('data-scroll-position'));
        }
    });
});
