/* Originally by tpr in the AdminOnSteroids module */
/* Ported to RockAdminTweaks by netcarver */
$(document).on("hover", ".PageListItem", function () {
  var $extrasToggle = $(this).find(".clickExtras"),
    $templateEditAction = $(this).find(
      ".PageListActionEdit ~ .PageListActionEdit"
    );
  if ($extrasToggle.length) {
    $extrasToggle.trigger("click").remove();
    if ($(this).find(".PageListActionExtras").length) {
      $(this).find(".PageListActionExtras").remove();
    }
    // move template edit link to the end
    if ($templateEditAction.length) {
      $templateEditAction.parent().append($templateEditAction);
    }
  }
});
