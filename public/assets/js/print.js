document.addEventListener('DOMContentLoaded', function() {
    var printBtn = document.getElementById('btn-print-report');
    if (printBtn) {
        printBtn.addEventListener('click', function() {
            window.print();
        });
    }
});
