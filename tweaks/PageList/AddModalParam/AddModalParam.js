// wait for document to be ready
$(document).ready(() => {
  // detect if we are in a modal
  const isModal = document.body.classList.contains("modal");
  // console.log("isModal", isModal);
  if (!isModal) return;

  // get the modal parameter of the current url and exit if it doesn't exist
  const params = new URLSearchParams(window.location.search);
  const modal = params.get("modal");

  // on every ajax request update all pagelist links
  $(document).on("ajaxComplete", () => {
    const links = document.querySelectorAll(".PageListRoot a[href]");

    links.forEach((link) => {
      // get href attribute
      const href = link.getAttribute("href");

      // don't update # links
      if (href === "#") return;

      // don't update links that do not point to the backend
      // which means they do not start with ProcessWire.config.urls.admin
      if (!href.startsWith(ProcessWire.config.urls.admin)) return;

      try {
        // get the base path and search params
        const [path, search] = href.split("?");
        const params = new URLSearchParams(search);

        // if it already has a modal parameter, exit
        if (params.get("modal")) return;

        // add the modal parameter
        params.set("modal", modal);

        // construct the new href
        const queryString = params.toString() ? "?" + params.toString() : "";
        const newHref = path + queryString;

        // replace the href
        link.setAttribute("href", newHref);
      } catch (error) {
        console.error("failed to build url", href);
      }
    });
  });
});
