<?php

use common\helpers\html\HtmlBuilder;
use frontend\models\work\order\DocumentOrderWork;
use frontend\models\work\order\OrderEventWork;
use common\helpers\DateFormatter;
use common\helpers\files\FilesHelper;
use frontend\models\work\general\PeopleWork;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model \frontend\models\work\order\OrderEventWork */
/* @var $modelResponsiblePeople */
/* @var $actTable */
$this->title = $model->order_name;
$this->params['breadcrumbs'][] = ['label' => 'Приказы о мероприятиях'];
\yii\web\YiiAsset::register($this);
?>
<div class="order-main-view">
    <h1><?= Html::encode($this->title) ?></h1>
    <p>
        <?= Html::a('Редактировать', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= HtmlBuilder::createErrorsBlock(DocumentOrderWork::tableName(), $model->id) ?>

    <?=
    DetailView::widget([
        'model' => $model,
        'attributes' => [
            ['label' => 'Регистрационный номер приказа', 'value' => function (OrderEventWork $model) {
                return $model->getNumberPostfix();
            }],
            ['label' => 'Наименование приказа', 'attribute' => 'order_name'],
            ['label' => 'Дата приказа', 'attribute' => 'order_date', 'value' => function (OrderEventWork $model) {
                return DateFormatter::format($model->order_date, DateFormatter::Ymd_dash, DateFormatter::dmY_dot);
            }],

            ['label' => 'Проект вносит', 'attribute' => 'bring_id', 'value' => function (OrderEventWork $model) {
                return $model->bringWork ? $model->bringWork->getFIO(PeopleWork::FIO_SURNAME_INITIALS) : '';
            }],
            ['label' => 'Исполнитель', 'attribute' => 'executor_id', 'value' => function (OrderEventWork $model) {
                return $model->executorWork ? $model->executorWork->getFullFio() : '';
            }],
            ['label' => 'Ответственные', 'value' => $modelResponsiblePeople, 'format' => 'raw'],
            ['label' => 'Скан документа', 'attribute' => 'scan', 'value' => function (OrderEventWork $model) {
                return implode('<br>', ArrayHelper::getColumn($model->getFileLinks(FilesHelper::TYPE_SCAN), 'link'));
            }, 'format' => 'raw'],
            ['label' => 'Редактируемые документы', 'attribute' => 'doc', 'value' => function (OrderEventWork $model) {
                return implode('<br>', ArrayHelper::getColumn($model->getFileLinks(FilesHelper::TYPE_DOC), 'link'));
            }, 'format' => 'raw'],
            ['label' => 'Создатель карточки', 'attribute' => 'creator_id', 'value' => function (OrderEventWork $model) {
                return $model->creatorWork ? $model->creatorWork->getFIO(PeopleWork::FIO_SURNAME_INITIALS) : '';
            }],
            ['label' => 'Последний редактор', 'attribute' => 'last_update_id', 'value' => function (OrderEventWork $model) {
                return $model->lastUpdateWork ? $model->lastUpdateWork->getFIO(PeopleWork::FIO_SURNAME_INITIALS) : '';
            }],
        ],
    ]) ?>
    <?php if (!is_null($actTable)): ?>
        <?= $actTable; ?>
    <?php endif; ?>
</div>
