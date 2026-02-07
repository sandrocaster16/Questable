<?php
use yii\helpers\Url;

$this->context->layout = false;
$this->title = 'Вход в Questable';
$botUsername = Yii::$app->params['telegramBotUsername'];
$redirectUrl = Url::to(['auth/tg-callback'], true);
?>

<div class="container" style="height: 80vh; display: flex; align-items: center; justify-content: center;">
    <div class="quest-form-container" style="text-align: center; width: 100%; max-width: 400px; padding: 40px;">

        <h2 class="section-title" style="margin-bottom: 10px;">Вход</h2>
        <p style="color: var(--text-secondary); margin-bottom: 30px;">
            Войдите, чтобы создавать квесты и сохранять прогресс.
        </p>

        <!-- Telegram Widget Script -->
        <script async src="https://telegram.org/js/telegram-widget.js?22"
                data-telegram-login="<?= $botUsername ?>"
                data-size="large"
                data-radius="12"
                data-auth-url="<?= $redirectUrl ?>"
                data-request-access="write">
        </script>

        <div style="margin-top: 20px; font-size: 0.9em; color: #999;">
            Мы используем Telegram для безопасной авторизации. <br>Пароли не требуются.
        </div>

    </div>
</div>