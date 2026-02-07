<?php

use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\Quests */

$this->title = 'Отредактировать квест: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Квесты', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';

// Подключаем Bootstrap CSS для модального окна
$this->registerCssFile('https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css');

// Формируем URL для QR кода
$questUrl = Url::to(['site/view', 'id' => $model->id], true);
?>
<div class="quests-update">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1><?= Html::encode($this->title) ?></h1>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#qrCodeModal">
            <i class="fas fa-qrcode"></i> Сгенерировать QR код
        </button>
    </div>

    <?= $this->render('_form', [
            'model' => $model,
    ]) ?>

</div>

<!-- Модальное окно для QR кода -->
<div class="modal fade" id="qrCodeModal" tabindex="-1" aria-labelledby="qrCodeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="qrCodeModalLabel">QR код для квеста</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <p class="text-muted mb-3">Отсканируйте QR код, чтобы открыть квест</p>
                <div id="qrcode" class="mb-3" style="display: flex; justify-content: center;"></div>
                <div class="input-group mb-3">
                    <input type="text" class="form-control" id="questUrlInput" value="<?= Html::encode($questUrl) ?>" readonly>
                    <button class="btn btn-outline-secondary" type="button" onclick="copyUrl()">
                        <i class="fas fa-copy"></i> Копировать
                    </button>
                </div>
                <button class="btn btn-success" onclick="downloadQR()">
                    <i class="fas fa-download"></i> Скачать QR код
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Подключаем библиотеку для генерации QR кода -->
<script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.4/qrcode.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    let qrCodeCanvas = null;
    const questUrl = <?= json_encode($questUrl) ?>;

    // Генерируем QR код при открытии модального окна
    document.getElementById('qrCodeModal').addEventListener('shown.bs.modal', function () {
        const qrcodeDiv = document.getElementById('qrcode');
        qrcodeDiv.innerHTML = ''; // Очищаем предыдущий QR код
        
        // Создаем canvas для QR кода
        const canvas = document.createElement('canvas');
        qrcodeDiv.appendChild(canvas);
        
        // Генерируем QR код
        QRCode.toCanvas(canvas, questUrl, {
            width: 300,
            margin: 2,
            color: {
                dark: '#000000',
                light: '#FFFFFF'
            },
            errorCorrectionLevel: 'M'
        }, function (error) {
            if (error) {
                console.error('Ошибка генерации QR кода:', error);
                qrcodeDiv.innerHTML = '<div class="alert alert-danger">Ошибка генерации QR кода</div>';
            } else {
                qrCodeCanvas = canvas;
            }
        });
    });

    // Функция копирования URL
    function copyUrl() {
        const input = document.getElementById('questUrlInput');
        input.select();
        input.setSelectionRange(0, 99999); // Для мобильных устройств
        document.execCommand('copy');
        
        // Показываем уведомление
        const btn = event.target.closest('button');
        const originalText = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-check"></i> Скопировано!';
        btn.classList.add('btn-success');
        btn.classList.remove('btn-outline-secondary');
        
        setTimeout(function() {
            btn.innerHTML = originalText;
            btn.classList.remove('btn-success');
            btn.classList.add('btn-outline-secondary');
        }, 2000);
    }

    // Функция скачивания QR кода
    function downloadQR() {
        if (!qrCodeCanvas) {
            alert('QR код еще не сгенерирован');
            return;
        }
        
        // Создаем ссылку для скачивания
        const link = document.createElement('a');
        link.download = 'quest-qr-code-<?= $model->id ?>.png';
        link.href = qrCodeCanvas.toDataURL('image/png');
        link.click();
    }
</script>