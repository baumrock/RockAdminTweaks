document.addEventListener("DOMContentLoaded", function() {
    var upgrades_url = ProcessWire.config.urls.admin + 'setup/upgrades/';
    $('a[href="' + upgrades_url + '"]').attr('href', upgrades_url + 'refresh');
});
