document.addEventListener("DOMContentLoaded", function () {
  var PrevNextLinks = ProcessWire.config.AOS_prevnextlinks;
  if (PrevNextLinks) {
    var targetElement = $("h1, li.title span, li.title").first();
    if (targetElement.length) {
      var icon;
      if (PrevNextLinks.prev) {
        icon = "fa fa-angle-left";
        targetElement.append(
          '<a href="' +
            PrevNextLinks.prev.url +
            '" title="' +
            PrevNextLinks.prev.title +
            '"' +
            ' class="aos-edit-prev"><i class="' +
            icon +
            '"></i></a>'
        );
      }
      if (PrevNextLinks.next) {
        icon = "fa fa-angle-right";
        targetElement.append(
          '<a href="' +
            PrevNextLinks.next.url +
            '" title="' +
            PrevNextLinks.next.title +
            '"' +
            ' class="aos-edit-next"><i class="' +
            icon +
            '"></i></a>'
        );
      }
    }
  }
});
