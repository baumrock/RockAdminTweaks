$(document).ready(function () {

    var $checkAllCheckboxes = $('<li id="checkAllCheckboxes"><i class="fa fa-check"></i></li>');
    function updateCheckCheckboxState($ul) {
        $checkAllCheckboxes.attr('data-checked-all', $ul.find('input:not(:checked)').length === 0 ? '1' : '');
    }
    function checkCheckboxes(e, $ul) {
        if (e.which !== 1) return true; // fire on left click only
        var isAllChecked = $ul.find('input:not(:checked)').length > 0,
                inputSelector = isAllChecked ? 'input:not(:checked)' : 'input:checked';
        // need to trigger change, eg. for showIf fields
        $ul.find(inputSelector).attr('checked', isAllChecked).trigger('change');
        return isAllChecked;
    }
    $(document).on('change', 'ul[class*="InputfieldCheckboxes"] input', function () {
        updateCheckCheckboxState($(this).parents('ul').first());
    });
    $checkAllCheckboxes.on('click', function (e) {
        $(this).attr('data-checked-all', checkCheckboxes(e, $(this).parent()) > 0 ? '1' : '');
    });
    $(document).on('hover', '.InputfieldCheckboxes ul[class*="InputfieldCheckboxes"]', function () {
        updateCheckCheckboxState($(this));
        $(this).append($checkAllCheckboxes);
    });

});
