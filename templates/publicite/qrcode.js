// Initialize QRious for QR code generation
let qr = new QRious({
    element: document.getElementById('qrcode'),
    size: 200,
    value: 'Initial QR code value'
});

function updateQRCode() {
    const form = document.getElementById('paymentForm');

    const offre = form.querySelector('select[name="offre_pub"]').value;
    const numero_carte = form.querySelector('input[name="numero_carte"]').value;
    const nom = form.querySelector('input[name="nom"]').value;
    const prenom = form.querySelector('input[name="prenom"]').value;

    const qrContent = `${offre}-${numero_carte}-${nom}-${prenom}`;

    qr.value = qrContent; // Met à jour le QR code avec le nouveau contenu
}

