// === ЛОГИКА РЕДАКТИРОВАНИЯ СТАНЦИЙ (Без изменений) ===
const stationModalEl = new bootstrap.Modal(document.getElementById('station-modal'));
const stationBodyEl = $('#station-modal-body');

$(document).on('click', '#add-station-btn, .edit-station-btn', function(e) {
    e.preventDefault();
    let url = $(this).data('url');
    stationBodyEl.html('<div class="text-center py-5"><div class="spinner-border text-dark" role="status"></div><p class="mt-2 text-muted">Загрузка...</p></div>');
    stationModalEl.show();
    stationBodyEl.load(url, function(response, status, xhr) {
        if (status === "error") {
            stationBodyEl.html('<div class="alert alert-danger">Ошибка: ' + xhr.status + '</div>');
        } else {
            toggleQuizBlock();
        }
    });
});

$(document).on('change', '#station-type-select', function() { toggleQuizBlock(); });

function toggleQuizBlock() {
    const type = $('#station-type-select').val();
    const quizBlock = $('#quiz-block');
    if(quizBlock.length) {
        type === 'quiz' ? quizBlock.slideDown() : quizBlock.slideUp();
    }
}

$(document).on('submit', '#station-active-form', function(e) {
    e.preventDefault();
    var form = $(this);
    var formData = new FormData(form[0]);
    $.ajax({
        url: form.attr('action'),
        type: 'post',
        data: formData,
        processData: false, 
        contentType: false,
        success: function(response) {
            if (response.success) {
                stationModalEl.hide();
                location.reload(); 
            } else {
                stationBodyEl.html(response);
                toggleQuizBlock();
            }
        },
        error: function() { alert('Ошибка сохранения'); }
    });
    return false;
});