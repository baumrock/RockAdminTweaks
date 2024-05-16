$(document).ready(function () {

    if (window.Split && $('.aos_col_left ~ .aos_col_right').length) {

        var storageName = $('.aos_col_right').attr('data-splitter-storagekey'),
            defaultLeft = parseFloat($('.aos_col_left').attr('data-splitter-default')),
            defaultRight = parseFloat($('.aos_col_right').attr('data-splitter-default')),
            sizes = localStorage.getItem(storageName) || [defaultLeft, defaultRight];

        var aos_splitter = Split(['.aos_col_left', '.aos_col_right'], {
            sizes: typeof sizes === 'string' ? JSON.parse(sizes) : sizes,
            gutterSize: 20,
            minSize: 250,
            onDragEnd: function () {
                localStorage.setItem(storageName, JSON.stringify(aos_splitter.getSizes()));
                setSplitterHeight();
            }
        });
    }

    function setSplitterHeight() {
        // set height to 0 before checking parent height
        !$('.gutter').length || $('.gutter').css('height', 0).css('height', $('.gutter').parent().outerHeight());
    }

    $(window).on('load', function () {
        setTimeout(function () {
            setSplitterHeight();
        }, 2000);
    });

    // restore default splitter position on double-click on splitter
    $(document).on('dblclick', '.aos_col_left + .gutter', function (e) {
        if (!aos_splitter) return true;
        aos_splitter.setSizes([defaultLeft, defaultRight]);
        localStorage.removeItem(storageName);
    });

    // recalculate splitter height on window resize
    $(window).on('resize', function () {
        setSplitterHeight();
    });


    // check for AdminColumns in tabs
    if ($('#ProcessPageEdit li[data-column-break]').length) {

        $(document).on('wiretabclick', function (e, elem) {

            var tabName = elem.attr('id').replace('Inputfield_', ''),
                tabSelector = '#Inputfield_' + tabName,
                tabColumnBreaks = $('#ProcessPageEdit li[data-column-break]').attr('data-column-break');

            if ($(tabSelector).hasClass('aos-columns-ready')) return false;

            if (tabColumnBreaks) tabColumnBreaks = JSON.parse(tabColumnBreaks);

            if (tabColumnBreaks[tabName]) {

                if (!tabColumnBreaks[tabName][0]) return false;

                var breakField = $('#wrap_Inputfield_' + tabColumnBreaks[tabName][0]),
                    colWidth = tabColumnBreaks[tabName][1] ? tabColumnBreaks[tabName][1] : 67;

                if (!breakField.length) return false;

                var aosColBreakIndex = breakField.index() + 1;

                $(tabSelector + ' > .Inputfields > li:lt(' + aosColBreakIndex + ')').wrapAll('<li class="InputfieldFieldsetOpen aos_col_left" style="width: ' + colWidth + '%;"><div class="InputfieldContent"><ul class="Inputfields">');
                $(tabSelector + ' > .Inputfields > .aos_col_left ~ li').wrapAll('<li class="InputfieldFieldsetOpen aos_col_right" style="width: ' + (100 - colWidth) + '%;"><div class="InputfieldContent"><ul class="Inputfields">');

                $(tabSelector).addClass('aos-columns-ready');
            }
        });
    }
});
