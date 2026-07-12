// SIWES Client Utilities

document.addEventListener('DOMContentLoaded', () => {
    // Initialization code
    console.log('SIWES Client Initialized');
});

/**
 * Client-side HTML Table to CSV Downloader
 * @param {string} tableId - ID of the table element
 * @param {string} filename - Output name of the CSV file
 */
function exportTableToCSV(tableId, filename = 'siwes_report.csv') {
    const table = document.getElementById(tableId);
    if (!table) return;

    let csv = [];
    const rows = table.querySelectorAll('tr');
    
    for (let i = 0; i < rows.length; i++) {
        const row = [], cols = rows[i].querySelectorAll('td, th');
        
        for (let j = 0; j < cols.length; j++) {
            // Clean text, escape double quotes
            let data = cols[j].innerText.replace(/(\r\n|\n|\r)/gm, ' ').replace(/^\s+|\s+$/g, '');
            data = data.replace(/"/g, '""');
            row.push('"' + data + '"');
        }
        csv.push(row.join(','));
    }

    const csvFile = new Blob([csv.join('\n')], { type: 'text/csv;charset=utf-8;' });
    const downloadLink = document.createElement('a');
    
    downloadLink.download = filename;
    downloadLink.href = window.URL.createObjectURL(csvFile);
    downloadLink.style.display = 'none';
    document.body.appendChild(downloadLink);
    downloadLink.click();
    document.body.removeChild(downloadLink);
}

/**
 * Print specific UI section
 * @param {string} divId - ID of the section container
 */
function printReportSection(divId) {
    const printContents = document.getElementById(divId).innerHTML;
    const originalContents = document.body.innerHTML;

    // Temporal swap for clean printing
    document.body.innerHTML = `
        <div class="p-8 bg-white text-black font-sans">
            ${printContents}
        </div>
    `;
    window.print();
    
    // Restore
    document.body.innerHTML = originalContents;
    window.location.reload(); // Reload to restore event listeners
}

/**
 * Inline validation helpers
 */
function validatePasswordMatch(p1Id, p2Id, buttonId) {
    const p1 = document.getElementById(p1Id);
    const p2 = document.getElementById(p2Id);
    const btn = document.getElementById(buttonId);
    if (!p1 || !p2 || !btn) return;

    const check = () => {
        if (p1.value === p2.value && p1.value.length >= 6) {
            p2.classList.remove('border-danger');
            p2.classList.add('border-success');
            btn.removeAttribute('disabled');
        } else {
            p2.classList.remove('border-success');
            p2.classList.add('border-danger');
            btn.setAttribute('disabled', 'true');
        }
    };

    p1.addEventListener('input', check);
    p2.addEventListener('input', check);
}
