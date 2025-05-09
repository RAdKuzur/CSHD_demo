<?php

namespace frontend\services\order;

use app\events\act_participant\ActParticipantBranchDeleteEvent;
use app\events\act_participant\ActParticipantDeleteEvent;
use app\events\act_participant\SquadParticipantDeleteByIdEvent;
use app\events\document_order\DocumentOrderDeleteEvent;
use app\events\document_order\OrderEventGenerateDeleteEvent;
use app\events\educational\training_group\OrderTrainingGroupParticipantByIdDeleteEvent;
use app\events\educational\training_group\UpdateStatusTrainingGroupParticipantEvent;
use app\events\expire\ExpireDeleteEvent;
use app\events\foreign_event\ForeignEventDeleteEvent;
use app\events\general\OrderPeopleDeleteByIdEvent;
use app\events\team\TeamNameDeleteEvent;
use common\components\dictionaries\base\NomenclatureDictionary;
use common\repositories\act_participant\ActParticipantRepository;
use common\repositories\educational\OrderTrainingGroupParticipantRepository;
use common\repositories\event\ForeignEventRepository;
use common\repositories\expire\ExpireRepository;
use common\repositories\general\FilesRepository;
use common\repositories\general\OrderPeopleRepository;
use common\repositories\general\PeopleStampRepository;
use common\repositories\order\OrderMainRepository;
use common\repositories\team\TeamRepository;
use common\services\general\PeopleStampService;
use frontend\events\educational\training_group\DeleteTrainingGroupParticipantEvent;
use frontend\events\general\FileDeleteEvent;
use frontend\models\work\educational\training_group\OrderTrainingGroupParticipantWork;
use frontend\models\work\general\FilesWork;
use frontend\models\work\general\OrderPeopleWork;
use frontend\models\work\order\DocumentOrderWork;
use common\helpers\files\filenames\DocumentOrderFileNameGenerator;
use common\helpers\files\FilesHelper;
use common\helpers\html\HtmlBuilder;
use common\helpers\OrderNumberHelper;
use common\helpers\DateFormatter;
use common\services\general\files\FileService;
use frontend\events\general\FileCreateEvent;
use frontend\models\work\order\OrderTrainingWork;
use frontend\models\work\team\ActParticipantWork;
use phpseclib3\Crypt\EC\Curves\brainpoolP160r1;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\UploadedFile;

class DocumentOrderService
{

    private FileService $fileService;
    private DocumentOrderFileNameGenerator $filenameGenerator;
    private PeopleStampService $peopleStampService;
    private PeopleStampRepository $peopleStampRepository;
    private FilesRepository $filesRepository;
    private ExpireRepository $expireRepository;
    private ForeignEventRepository $foreignEventRepository;
    private TeamRepository $teamRepository;
    private ActParticipantRepository $actParticipantRepository;
    private OrderPeopleRepository $orderPeopleRepository;
    private OrderTrainingGroupParticipantRepository $orderTrainingGroupParticipantRepository;

    public function __construct(
        FileService $fileService,
        DocumentOrderFileNameGenerator $filenameGenerator,
        PeopleStampService $peopleStampService,
        PeopleStampRepository $peopleStampRepository,
        FilesRepository $filesRepository,
        ExpireRepository $expireRepository,
        ForeignEventRepository $foreignEventRepository,
        TeamRepository $teamRepository,
        ActParticipantRepository $actParticipantRepository,
        OrderPeopleRepository $orderPeopleRepository,
        OrderTrainingGroupParticipantRepository $orderTrainingGroupParticipantRepository
    )
    {
        $this->fileService = $fileService;
        $this->filenameGenerator = $filenameGenerator;
        $this->peopleStampService = $peopleStampService;
        $this->peopleStampRepository = $peopleStampRepository;
        $this->filesRepository = $filesRepository;
        $this->expireRepository = $expireRepository;
        $this->foreignEventRepository = $foreignEventRepository;
        $this->teamRepository = $teamRepository;
        $this->actParticipantRepository = $actParticipantRepository;
        $this->orderPeopleRepository = $orderPeopleRepository;
        $this->orderTrainingGroupParticipantRepository = $orderTrainingGroupParticipantRepository;
    }
    public function createOrderPeopleArray(array $data)
    {
        $result = [];
        foreach ($data as $item) {
            /** @var OrderPeopleWork $item */
            $result[] = ($this->peopleStampRepository->get($item->people_id))->getFullFio();
        }
        return $result;
    }
    public function getFilesInstances($model)
    {
        $model->scanFile = UploadedFile::getInstance($model, 'scanFile');
        $model->docFiles = UploadedFile::getInstances($model, 'docFiles');
        $model->appFiles = UploadedFile::getInstances($model, 'appFiles');
    }
    public function saveFilesFromModel($model)
    {
        if ($model->scanFile !== null) {
            $filename = $this->filenameGenerator->generateFileName($model, FilesHelper::TYPE_SCAN);
            $this->fileService->uploadFile(
                $model->scanFile,
                $filename,
                [
                    'tableName' => DocumentOrderWork::tableName(),
                    'fileType' => FilesHelper::TYPE_SCAN
                ]
            );

            $model->recordEvent(
                new FileCreateEvent(
                    $model::tableName(),
                    $model->id,
                    FilesHelper::TYPE_SCAN,
                    $filename,
                    FilesHelper::LOAD_TYPE_SINGLE
                ),
                get_class($model)
            );
        }
        if ($model->docFiles != NULL) {
            for ($i = 1; $i < count($model->docFiles) + 1; $i++) {
                $filename = $this->filenameGenerator->generateFileName($model, FilesHelper::TYPE_DOC, ['counter' => $i]);

                $this->fileService->uploadFile(
                    $model->docFiles[$i - 1],
                    $filename,
                    [
                        'tableName' => DocumentOrderWork::tableName(),
                        'fileType' => FilesHelper::TYPE_DOC
                    ]
                );

                $model->recordEvent(
                    new FileCreateEvent(
                        $model::tableName(),
                        $model->id,
                        FilesHelper::TYPE_DOC,
                        $filename,
                        FilesHelper::LOAD_TYPE_SINGLE
                    ),
                    get_class($model)
                );
            }
        }
    }
    public function getUploadedFilesTables($model)
    {
        $scanLinks = $model->getFileLinks(FilesHelper::TYPE_SCAN);
        $scanFile = HtmlBuilder::createTableWithActionButtons(
            [
                array_merge(['Название файла'], ArrayHelper::getColumn($scanLinks, 'link'))
            ],
            [
                HtmlBuilder::createButtonsArray(
                    'Удалить',
                    Url::to('delete-file'),
                    ['modelId' => array_fill(0, count($scanLinks), $model->id), 'fileId' => ArrayHelper::getColumn($scanLinks, 'id')])
            ]
        );

        $docLinks = $model->getFileLinks(FilesHelper::TYPE_DOC);
        $docFiles = HtmlBuilder::createTableWithActionButtons(
            [
                array_merge(['Название файла'], ArrayHelper::getColumn($docLinks, 'link'))
            ],
            [
                HtmlBuilder::createButtonsArray(
                    'Удалить',
                    Url::to('delete-file'),
                    ['modelId' => array_fill(0, count($docLinks), $model->id), 'fileId' => ArrayHelper::getColumn($docLinks, 'id')])
            ]
        );

        $appLinks = $model->getFileLinks(FilesHelper::TYPE_APP);
        $appFiles = HtmlBuilder::createTableWithActionButtons(
            [
                array_merge(['Название файла'], ArrayHelper::getColumn($appLinks, 'link'))
            ],
            [
                HtmlBuilder::createButtonsArray(
                    'Удалить',
                    Url::to('delete-file'),
                    ['modelId' => array_fill(0, count($appLinks), $model->id), 'fileId' => ArrayHelper::getColumn($appLinks, 'id')])
            ]
        );

        return ['scan' => $scanFile, 'docs' => $docFiles, 'app' => $appFiles];
    }
    public function getPeopleStamps($model)
    {
        if ($model->executor_id != "") {
            $peopleStampId = $this->peopleStampService->createStampFromPeople($model->executor_id);
            $model->executor_id = $peopleStampId;
        }
        if ($model->signed_id != "") {
            $peopleStampId = $this->peopleStampService->createStampFromPeople($model->signed_id);
            $model->signed_id = $peopleStampId;
        }
        if ($model->bring_id != "") {
            $peopleStampId = $this->peopleStampService->createStampFromPeople($model->bring_id);
            $model->bring_id = $peopleStampId;
        }
    }
    public function setResponsiblePeople($responsiblePeople, $model)
    {
        foreach ($responsiblePeople as $index => $person) {
            $person = $this->peopleStampRepository->get($person);
            $responsiblePeople[$index] = $person->people_id;
        }
        $model->responsible_id = $responsiblePeople;
    }
    public function documentOrderDelete($model)
    {
        switch ($model->type) {
            case DocumentOrderWork::ORDER_MAIN:
                $this->orderMainDelete($model);
            case DocumentOrderWork::ORDER_EVENT:
                $this->orderEventDelete($model);
            case DocumentOrderWork::ORDER_TRAINING:
                $this->orderTrainingDelete($model);
        }
    }
    public function orderMainDelete(DocumentOrderWork $model)
    {
        /* @var FilesWork $file */
        $responsiblePeople = $this->orderPeopleRepository->getResponsiblePeople($model->id);
        foreach ($responsiblePeople as $person) {
            $model->recordEvent(new OrderPeopleDeleteByIdEvent($person->id), DocumentOrderWork::class);
        }
        $expires = $this->expireRepository->getExpireByActiveRegulationId($model->id);
        foreach ($expires as $expire) {
            $model->recordEvent(new ExpireDeleteEvent($expire->id), DocumentOrderWork::class);
        }
        $files = $this->filesRepository->getByDocument(DocumentOrderWork::tableName(), $model->id);
        foreach ($files as $file) {
            $model->recordEvent(new FileDeleteEvent($file->id), DocumentOrderWork::class);
        }
        $model->recordEvent(new DocumentOrderDeleteEvent($model->id), DocumentOrderWork::class);
    }

    public function orderEventDelete(DocumentOrderWork $model)
    {
        $responsiblePeople = $this->orderPeopleRepository->getResponsiblePeople($model->id);
        foreach ($responsiblePeople as $person) {
            $model->recordEvent(new OrderPeopleDeleteByIdEvent($person->id), DocumentOrderWork::class);
        }
        //files
        $files = $this->filesRepository->getByDocument(DocumentOrderWork::tableName(), $model->id);
        foreach ($files as $file) {
            $model->recordEvent(new FileDeleteEvent($file->id), DocumentOrderWork::class);
        }
        //order_event_generate
        $model->recordEvent(new OrderEventGenerateDeleteEvent($model->id), DocumentOrderWork::class);
        $event = $this->foreignEventRepository->getByDocOrderId($model->id);
        $acts = $this->actParticipantRepository->getByForeignEventIds([$event->id]);
        foreach ($acts as $act) {
            //files(act_participant)
            $files = $this->filesRepository->getByDocument(ActParticipantWork::tableName(), $act->id);
            foreach ($files as $file) {
                $model->recordEvent(new FileDeleteEvent($file->id), DocumentOrderWork::class);
            }
            //act_participant_branch
            $model->recordEvent(new ActParticipantBranchDeleteEvent($act->id), DocumentOrderWork::class);
            //squad_participant
            $model->recordEvent(new SquadParticipantDeleteByIdEvent($act->id), DocumentOrderWork::class);
            //act_participant
            $model->recordEvent(new ActParticipantDeleteEvent($act->id), DocumentOrderWork::class);
        }
        //team_name
        $teams = $this->teamRepository->getNamesByForeignEventId($event->id);
        foreach ($teams as $team) {
            $model->recordEvent(new TeamNameDeleteEvent($team->id), DocumentOrderWork::class);
        }
        //foreign_event
        $model->recordEvent(new ForeignEventDeleteEvent($event->id), DocumentOrderWork::class);
        $model->recordEvent(new DocumentOrderDeleteEvent($model->id), DocumentOrderWork::class);
    }
    public function orderTrainingDelete(DocumentOrderWork $model)
    {
        /* @var $orderParticipant OrderTrainingGroupParticipantWork */
        $responsiblePeople = $this->orderPeopleRepository->getResponsiblePeople($model->id);
        foreach ($responsiblePeople as $person) {
            $model->recordEvent(new OrderPeopleDeleteByIdEvent($person->id), DocumentOrderWork::class);
        }
        $status = NomenclatureDictionary::getStatus((explode("/",$model->order_number))[0]);
        $orderParticipants = $this->orderTrainingGroupParticipantRepository->getByOrderIds($model->id);
        //update & delete TrainingGroupParticipant
        foreach ($orderParticipants as $orderParticipant) {
            switch ($status) {
                case $status == NomenclatureDictionary::ORDER_ENROLL:
                    $model->recordEvent(new UpdateStatusTrainingGroupParticipantEvent($orderParticipant->training_group_participant_in_id, $status - 1), DocumentOrderWork::class);
                    $model->recordEvent(new OrderTrainingGroupParticipantByIdDeleteEvent($orderParticipant->id), DocumentOrderWork::class);
                    break;
                case $status == NomenclatureDictionary::ORDER_DEDUCT:
                    $model->recordEvent(new UpdateStatusTrainingGroupParticipantEvent($orderParticipant->training_group_participant_out_id, $status - 1), DocumentOrderWork::class);
                    $model->recordEvent(new OrderTrainingGroupParticipantByIdDeleteEvent($orderParticipant->id), DocumentOrderWork::class);
                    break;
                case $status == NomenclatureDictionary::ORDER_TRANSFER:
                    $model->recordEvent(new UpdateStatusTrainingGroupParticipantEvent($orderParticipant->training_group_participant_out_id, $status - 2), DocumentOrderWork::class);
                    $model->recordEvent(new OrderTrainingGroupParticipantByIdDeleteEvent($orderParticipant->id), DocumentOrderWork::class);
                    $model->recordEvent(new DeleteTrainingGroupParticipantEvent($orderParticipant->training_group_participant_in_id), DocumentOrderWork::class);
                    break;
            }
        }
        $files = $this->filesRepository->getByDocument(DocumentOrderWork::tableName(), $model->id);
        foreach ($files as $file) {
            $model->recordEvent(new FileDeleteEvent($file->id), DocumentOrderWork::class);
        }
        $model->recordEvent(new DocumentOrderDeleteEvent($model->id), DocumentOrderWork::class);
    }

    public function generateNumber($model) {
        // Взято по аналогии из OrderTrainingWork.php/OrderMainWork.php/OrderEventWork.php
        $formNumber = $model->order_number;
        $model_date = DateFormatter::format($model->order_date, DateFormatter::dmY_dot, DateFormatter::Ymd_dash);
        $array_number = [];
        $records = Yii::createObject(OrderMainRepository::class)->getEqualPrefix($formNumber);
        $array_number = Yii::createObject(OrderMainService::class)->createArrayNumber($records, $array_number);
        
        // Наши реализациии
        $result = $this->createOrderNumberVarRufat($array_number, $formNumber, $model_date);
        // ИЛИ
        $result = $this->createOrderNumberVarTimur($array_number, $formNumber, $model_date);
        
        $model->setNumber($result['number'], $result['order_copy_id'], $result['postfix']);
    }

    private function postfixDetect($itemSplit) {
        $order_number = $itemSplit[0];
        $order_copy_id = $itemSplit[1];
        if (count($itemSplit) > 2) {
            $order_postfix = implode('/', array_slice($itemSplit, 2));
        } else {
            $order_postfix = NULL;
        }
        return ['number'=> $order_number,'order_copy_id' => $order_copy_id, 'postfix'=> $order_postfix];
    }
    
    private function incrementLastIndex($itemSplit) {
        // Увеличиваем последнее значение нижней границы на 1
        $lastPos = count($itemSplit) - 1;
        $itemSplit[$lastPos] = (int)$itemSplit[$lastPos] + 1;
        return $itemSplit;
    }

    private function createOrderNumberVarRufat($array_number, $formNumber, $model_date): array
    {
        // определяем с границы
        foreach ($array_number as $item) {
            if ($item[0] < $model_date) {
                $downItem = $item;
            }elseif ($item[0] == $model_date) {
                $equalItem[] = $item;
            }else {
                $upItem = $item;
                break;
            }
        }
        // переопределяет нижнюю границу если есть записи в ту же дату
        if($equalItem != NULL) {
            OrderNumberHelper::sortArrayByOrderNumber($equalItem);
            $downItem = $equalItem[count($equalItem) - 1];
        }

        // если нет нижней границы
        if (!$downItem) {
            return [
                'number'      => $formNumber,
                'order_copy_id' => null,
                'postfix'     => null
            ];
        }

        $downItemSplit = OrderNumberHelper::splitString($downItem[2]);
        // если нет верхней границы то увеличь индекс у downItem на 1 
        if ($upItem === null) {
            $itemSplit = $this->incrementLastIndex($downItemSplit);
            return $this->postfixDetect($itemSplit);
        }

        $upItemSplit = OrderNumberHelper::splitString($upItem[2]);
        // Сравниваем глубину индексации
        if (count($downItemSplit) !== count($upItemSplit)) {
            // увеличивам последний индекс на 1
            $itemSplit = $this->incrementLastIndex($downItemSplit);
            $posibleItem = implode('/', $itemSplit);
            if (!OrderNumberHelper::findByNumberPostfix($array_number, $posibleItem)) {
                return self::postfixDetect($itemSplit);
            }

        } 

        // увеличение глубины до тех пор пока не будет найден свободный вариант
        $posibleItem = $downItem[2] . '/1';
        while(OrderNumberHelper::findByNumberPostfix($array_number, $posibleItem)) {
            $posibleItem .= '/1';
        }
        
        $itemSplit = OrderNumberHelper::splitString( $posibleItem);
        return $this->postfixDetect($itemSplit);
    }

    public function createOrderNumberVarTimur($array_number, $formNumber, $model_date) {
        // Инициализация переменных
        //$downItem = null;
        //$equalItem = [];
        //$upItem = null;
    
        // Сортируем по датам
        foreach ($array_number as $item) {
            if ($item[0] < $model_date) {
                $downItem = $item;
            } elseif ($item[0] == $model_date) {
                $equalItem[] = $item;
            } elseif ($upItem === null) {
                $upItem = $item;
                break;
            }
        }
    
        // Заказы с одинаковой датой
        if (!empty($equalItem)) {
            OrderNumberHelper::sortArrayByOrderNumber($equalItem);
            $downItem = end($equalItem);
        }
    
        // Определяем базовый номер
        $newNumber = $downItem !== null ? $downItem[2] : $formNumber;
    
        // Генерация номера
        if ($downItem !== null && OrderNumberHelper::findByNumberPostfix($array_number, $newNumber)) {
            
            do {
                $generated = false; // флаг выхода
                $parts = OrderNumberHelper::splitString($newNumber);
                $lastIndex = count($parts) - 1;
                
                // Случай 1: Увеличиваем последнюю часть
                $newLast = (int)$parts[$lastIndex] + 1;
                $candidate1 = implode('/', array_slice($parts, 0, $lastIndex)) . '/' . $newLast;
                
                // Случай 2: Нужно увеличить вложенность
                $candidate2 = $newNumber . '/1';
    
                // Проверяем варианты
                if (($upItem === null || $candidate1 < $upItem[2]) && 
                    !OrderNumberHelper::findByNumberPostfix($array_number, $candidate1)) {
                    $newNumber = $candidate1;
                    $generated = true;
                } else {
                    $newNumber = $candidate2;
                }
    
                
    
            } while (!$generated && OrderNumberHelper::findByNumberPostfix($array_number, $newNumber));
        }
    
        // Разделяем номер на части
        $parts = OrderNumberHelper::splitString($newNumber);
        return [
            //'full_number' => $newNumber, для дебага
            'number' => $parts[0] ?? $formNumber,
            'order_copy_id' => $parts[1] ?? null,
            'postfix' => count($parts) > 2 ? implode('/', array_slice($parts, 2)) : null
        ];
        }
}