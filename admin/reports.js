$(() => {
    $('#saveReport').on('click', () => {
        const table = $('#reportTable');
        table.table2csv({ filename: table.data('report') });
    });
});
