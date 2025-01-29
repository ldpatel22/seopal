const ReportsPageControl = function () {
    let $table = $('#table_Reports');

    $table.DataTable({
        paging: true,
        searching: true,
        info: false
    });
};
