document.addEventListener('DOMContentLoaded', function() {
    const barcodes = document.querySelectorAll('.barcode');
    barcodes.forEach(svg => {
        const code = svg.getAttribute('data-code');
        try {
            JsBarcode(svg, code, {
                format: "CODE128",
                width: 2,
                height: 50,
                displayValue: true,
                fontSize: 14,
                margin: 10
            });
        } catch(e) {
            console.error("Error generating barcode for", code, e);
        }
    });

    // Wait a little bit for rendering then print
    setTimeout(() => {
        window.print();
    }, 500);
});
