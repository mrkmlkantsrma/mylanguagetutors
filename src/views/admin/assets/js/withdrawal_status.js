function markAsPaid(link) {
    const username = link.dataset.username;
    
    link.innerHTML = '<span class="spinner-border spinner-border-sm" role="status"></span> Processing...';

    fetch('../../controllers/update_withdrawal_status.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `ajax=update_withdrawal&username=${username}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            refreshTable();
        } else {
            // Handle any errors, maybe alert to the user
            alert("There was an error processing the request.");
            link.textContent = "Mark as Paid";
        }
    });
}

function refreshTable() {
    fetch('../../src/controllers/update_withdrawal_status.php')
    .then(response => response.text())
    .then(data => {
        document.getElementById('withdrawalTable').innerHTML = data;
    });
}
