<?php
//use yii\widgets\ActiveForm;
//use yii\helpers\Html;
/** @var yii\web\View $this */
/** @var yii\bootstrap5\ActiveForm $form */

/** @var app\models\LoginForm $model */

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;
?>

    <div class="container">
        <?php $form = ActiveForm::begin(['id' => 'link-form']); ?>

        <?= $form->field($model, 'url')->textInput(['placeholder' => 'Введите URL']) ?>

        <?= Html::button('OK', ['class' => 'btn btn-primary', 'id' => 'generate-btn']) ?>

        <?php ActiveForm::end(); ?>

        <div id="result" class="mt-4"></div>
    </div>

<?php
$js = <<<JS
$('#generate-btn').on('click', function() {
    $.ajax({
        url: '/site/generate',
        type: 'POST',
        data: $('#link-form').serialize(),
        success: function(response) {
            if (response.error) {
                $('#result').html('<div class="alert alert-danger">' + response.error + '</div>');
            } else {
                var html = '<div class="row">' +
                    '<div class="col-md-6"><img src="' + response.qrCode + '" class="img-fluid"></div>' +
                    '<div class="col-md-6"><p>Ваша короткая ссылка:</p>' +
                     '<a href="' + response.shortUrl + '" target="_blank">' + response.shortUrl + '</a></div>' +
                    '</div>';
                
                $('#result').html(html);
            }
        }
    });
});
JS;
$this->registerJs($js);
?>