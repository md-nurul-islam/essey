$(document).ready(function () {

    $(document).on('click', '#btn-print', function () {
        var divContents = $(".print").html();
        var printWindow = window.open('', '', 'height=550, width=600');
        printWindow.document.write('<html><head><title>Print</title>');
        printWindow.document.write('</head><body >');
        printWindow.document.write(divContents);
        printWindow.document.write('</body></html>');
        printWindow.document.close();
        printWindow.print();
    });
});