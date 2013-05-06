$(document).ready(function() {
    $('select').each(function() {
        $(this).select2({ minimumResultsForSearch: 6 });
    });
    $.tablesorter.themes.oskedge = {icons: '',sortNone: 'bootstrap-icon-unsorted',sortAsc: 'icon-chevron-up',sortDesc   : 'icon-chevron-down'};
});

