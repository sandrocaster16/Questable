<?php
use yii\helpers\Html;
use yii\helpers\Url;

/** @var $quest app\models\Quests */
/** @var $participant app\models\QuestParticipants */
/** @var $progress array */

$this->title = 'Квест завершен: ' . Html::encode($quest->name);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= Html::encode($this->title) ?> - Questable</title>
    <link rel="stylesheet" href="../app/web/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .completion-container {
            background: white;
            border-radius: 24px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            max-width: 700px;
            width: 100%;
            padding: 50px 40px;
            text-align: center;
            animation: fadeInUp 0.6s ease-out;
        }
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .trophy-icon {
            font-size: 120px;
            color: #ffd700;
            margin-bottom: 30px;
            animation: bounce 1s ease-in-out infinite;
            filter: drop-shadow(0 4px 8px rgba(0,0,0,0.2));
        }
        @keyframes bounce {
            0%, 100% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-10px);
            }
        }
        .completion-title {
            font-size: 42px;
            font-weight: bold;
            color: #333;
            margin-bottom: 15px;
        }
        .completion-subtitle {
            font-size: 20px;
            color: #666;
            margin-bottom: 40px;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 20px;
            margin: 40px 0;
        }
        .stat-card {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            border-radius: 16px;
            padding: 25px 20px;
            transition: transform 0.3s;
        }
        .stat-card:hover {
            transform: translateY(-5px);
        }
        .stat-value {
            font-size: 36px;
            font-weight: bold;
            color: #667eea;
            margin-bottom: 5px;
        }
        .stat-label {
            font-size: 14px;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .action-buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-top: 40px;
            flex-wrap: wrap;
        }
        .btn-completion {
            padding: 15px 30px;
            font-size: 16px;
            font-weight: 600;
            border-radius: 12px;
            border: none;
            transition: all 0.3s;
            min-width: 180px;
        }
        .btn-completion:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.2);
        }
        .confetti {
            position: fixed;
            width: 10px;
            height: 10px;
            background: #ffd700;
            position: absolute;
            animation: confetti-fall 3s linear infinite;
        }
        @keyframes confetti-fall {
            to {
                transform: translateY(100vh) rotate(360deg);
                opacity: 0;
            }
        }
    </style>
</head>
<body>
    <div class="completion-container">
        <div class="trophy-icon">
            <i class="fas fa-trophy"></i>
        </div>
        
        <h1 class="completion-title">Поздравляем!</h1>
        <p class="completion-subtitle">Вы успешно завершили квест<br><strong><?= Html::encode($quest->name) ?></strong></p>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-value"><?= $progress['completed_stations'] ?></div>
                <div class="stat-label">Станций пройдено</div>
            </div>
            <div class="stat-card">
                <div class="stat-value"><?= $participant->points ?></div>
                <div class="stat-label">Набрано очков</div>
            </div>
            <div class="stat-card">
                <div class="stat-value"><?= $progress['progress_percentage'] ?>%</div>
                <div class="stat-label">Прогресс</div>
            </div>
        </div>

        <div class="alert alert-success" style="border-radius: 12px; margin-top: 30px;">
            <i class="fas fa-check-circle me-2"></i>
            <strong>Отличная работа!</strong> Вы прошли все станции квеста и набрали <?= $participant->points ?> очков.
        </div>

        <div class="action-buttons">
            <a href="<?= Url::to(['game/progress', 'quest_id' => $quest->id]) ?>" 
               class="btn btn-primary btn-completion">
                <i class="fas fa-chart-line me-2"></i> Подробная статистика
            </a>
            <a href="<?= Url::to(['site/index']) ?>" 
               class="btn btn-outline-primary btn-completion">
                <i class="fas fa-home me-2"></i> На главную
            </a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
