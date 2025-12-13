// === ЛОГИКА QR КОДА (Адаптировано под qrcodejs) ===
const qrModalObj = new bootstrap.Modal(document.getElementById('qrCodeModal'));
let qrcodeObj = null; // Для хранения экземпляра

$(document).on('click', '.show-qr-btn', function() {
    const url = $(this).data('url');
    const title = $(this).data('title');
    const filename = $(this).data('filename');
    const qrcodeDiv = document.getElementById('qrcode');
    
    // 1. Обновляем тексты
    $('#qrCodeModalLabel').text(title);
    $('#questUrlInput').val(url);
    $('#qrFilename').val(filename);
    
    // 2. Очищаем предыдущий код
    qrcodeDiv.innerHTML = '';

    // 3. Генерируем новый (синтаксис qrcodejs)
    if (typeof QRCode === 'undefined') {
        qrcodeDiv.innerHTML = '<div class="alert alert-warning">Библиотека QR кода не загружена</div>';
    } else {
        // Создаем экземпляр. Библиотека сама добавит canvas/img внутрь qrcodeDiv
        qrcodeObj = new QRCode(qrcodeDiv, {
            text: url,
            width: 256,
            height: 256,
            colorDark : "#000000",
            colorLight : "#ffffff",
            correctLevel : QRCode.CorrectLevel.M
        });
    }

    // 4. Показываем модалку
    qrModalObj.show();
});

window.copyUrl = function(btnElement) {
    const input = document.getElementById('questUrlInput');
    input.select();
    input.setSelectionRange(0, 99999);
    document.execCommand('copy');
    
    const icon = btnElement.querySelector('i');
    const originalClass = icon.className;
    
    icon.className = 'fas fa-check';
    btnElement.classList.replace('btn-outline-secondary', 'btn-success');
    
    setTimeout(function() {
        icon.className = originalClass;
        btnElement.classList.replace('btn-success', 'btn-outline-secondary');
    }, 1500);
}

window.downloadQR = function() {
    const container = document.getElementById('qrcode');
    // Библиотека создает canvas или img. Ищем любой из них.
    const canvas = container.querySelector('canvas');
    const img = container.querySelector('img');
    
    let dataUrl = '';
    
    if (canvas) {
        dataUrl = canvas.toDataURL("image/png");
    } else if (img && img.src) {
        dataUrl = img.src;
    } else {
        alert('QR код еще не сгенерирован или произошла ошибка');
        return;
    }

    const filename = $('#qrFilename').val() || 'qrcode';
    const link = document.createElement('a');
    link.download = filename + '.png';
    link.href = dataUrl;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}