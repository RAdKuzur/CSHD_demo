<?php

namespace frontend\models\work\dictionaries;

use common\events\EventTrait;
use common\helpers\files\FilesHelper;
use common\models\scaffold\Auditorium;
use InvalidArgumentException;
use Yii;

class AuditoriumWork extends Auditorium
{
    use EventTrait;

    const NO_EDUCATION = 0;
    const IS_EDUCATION = 1;

    const NO_INCLUDE = 0;
    const IS_INCLUDE = 1;

    public $filesList;

    public function rules()
    {
        return array_merge(parent::rules(), [
            [['filesList'], 'file', 'skipOnEmpty' => true, 'maxFiles' => 10],
        ]);
    }

    /**
     * Возвращает массив
     * link => форматированная ссылка на документ
     * id => ID записи в таблице files
     * @param $filetype
     * @return array
     * @throws \yii\base\InvalidConfigException
     */
    public function getFileLinks($filetype)
    {
        if (!array_key_exists($filetype, FilesHelper::getFileTypes())) {
            throw new InvalidArgumentException('Неизвестный тип файла');
        }

        $addPath = '';
        switch ($filetype) {
            case FilesHelper::TYPE_OTHER:
                $addPath = FilesHelper::createAdditionalPath(AuditoriumWork::tableName(), FilesHelper::TYPE_OTHER);
                break;
        }

        return FilesHelper::createFileLinks($this, $filetype, $addPath);
    }

    /**
     * Вывод номера и названия аудитории
     * @return string
     */
    public function getFullName()
    {
        return "$this->name ($this->text)";
    }

    public function isEducation()
    {
        return $this->is_education;
    }

    public function isIncludeSquare()
    {
        return $this->include_square;
    }

    public function getEducationPretty()
    {
        return $this->isEducation() ? 'Да' : 'Нет';
    }

    public function getIncludeSquarePretty()
    {
        return $this->isIncludeSquare() ? 'Да' : 'Нет';
    }

    public function getAuditoriumTypePretty()
    {
        return Yii::$app->auditoriumType->get($this->auditorium_type);
    }
}