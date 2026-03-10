<?php

/** @var yii\web\View $this */

use yii\helpers\Url;

$this->title = 'Сервис коротких ссылок';
?>
<div class="site-index">
    <div class="jumbotron text-center bg-transparent mt-5 mb-4">
        <h1 class="display-4 mb-3">Сервис коротких ссылок + QR</h1>
        <p class="lead mb-0">Вставьте любой URL и получите короткую ссылку и QR-код без перезагрузки страницы.</p>
    </div>

    <div class="body-content">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="url-input" class="form-label">Введите URL</label>
                            <div class="input-group input-group-lg">
                                <input type="text" id="url-input" class="form-control" placeholder="https://example.com/page" />
                                <button class="btn btn-primary" id="shorten-btn">OK</button>
                            </div>
                            <div class="form-text">Поддерживаются только ссылки с протоколами http и https.</div>
                        </div>
                        <div id="message" class="mt-2"></div>
                    </div>
                </div>

                <div id="result-container" class="card shadow-sm d-none">
                    <div class="card-body d-flex flex-column flex-md-row align-items-center">
                        <div class="me-md-4 mb-3 mb-md-0 text-center">
                            <img id="qr-image" src="" alt="QR код" class="img-fluid border rounded" style="max-width: 260px;">
                        </div>
                        <div class="flex-fill">
                            <h5>Короткая ссылка</h5>
                            <p class="mb-2">
                                <a href="#" id="short-url" target="_blank" rel="noopener noreferrer"></a>
                            </p>
                            <p class="text-muted mb-0">
                                Наведите камеру телефона на QR-код, чтобы открыть короткую ссылку.
                            </p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<?php
$shortenUrl = Url::to(['site/shorten']);
$js = <<<JS
$(function () {
    $('#shorten-btn').on('click', function (e) {
        e.preventDefault();

        var url = $('#url-input').val();
        var \$btn = $(this);
        var \$msg = $('#message');
        var \$result = $('#result-container');

        \$msg.removeClass().empty();
        \$result.addClass('d-none');

        if (!url) {
            \$msg.addClass('text-danger').text('Введите URL.');
            return;
        }

        \$btn.prop('disabled', true).text('Обработка...');

        $.ajax({
            url: '$shortenUrl',
            type: 'POST',
            data: {url: url},
            dataType: 'json'
        }).done(function (data) {
            if (data.success) {
                $('#short-url').attr('href', data.shortUrl).text(data.shortUrl);
                $('#qr-image').attr('src', data.qrUrl + '&_=' + new Date().getTime());
                \$result.removeClass('d-none');
                \$msg.addClass('text-success').text('Ссылка успешно сокращена.');
            } else {
                \$msg.addClass('text-danger').text(data.error || 'Произошла ошибка.');
            }
        }).fail(function () {
            \$msg.addClass('text-danger').text('Ошибка при запросе к серверу.');
        }).always(function () {
            \$btn.prop('disabled', false).text('OK');
        });
    });

    $('#url-input').on('keypress', function (e) {
        if (e.which === 13) {
            $('#shorten-btn').click();
        }
    });
});
JS;

$this->registerJs($js);
