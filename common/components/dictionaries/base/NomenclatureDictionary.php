<?php

namespace common\components\dictionaries\base;
class NomenclatureDictionary extends BaseDictionary
{
    const ORDER_INIT = 0;
    const ORDER_ENROLL = 1;
    const ORDER_DEDUCT = 2;
    const ORDER_TRANSFER = 3;
    public const ADMIN_ORDER = '02-02';
    public const COD_ADD = '13-01';
    public const COD_DEL = '13-02';
    public const TECHNOPARK_ADD = '09-01';
    public const TECHNOPARK_DEL = '09-02';
    public const TECHNOPARK_ADD_BUDGET = '09-22';
    public const TECHNOPARK_DEL_BUDGET = '09-23';
    public const QUANTORIUM_ADD = '10-01';
    public const QUANTORIUM_DEL = '10-02';
    public const QUANTORIUM_ADD_BUDGET = '10-26';
    public const QUANTORIUM_DEL_BUDGET = '10-27';
    public const CDNTT_ADD = '11-01';
    public const CDNTT_DEL = '11-02';
    public const CDNTT_ADD_BUDGET = '11-26';
    public const CDNTT_DEL_BUDGET = '11-27';
    public const CDNTT_TRANSFER = '11-31';
    public const MOB_QUANT_ADD = '12-01';
    public const MOB_QUANT_DEL = '12-02';
    public function __construct()
    {
        parent::__construct();
        $this->list = [
            self::ADMIN_ORDER => '02-02 Приказы директора по основной деятельности',
            self::COD_ADD => '13-01 Приказы о зачислении обучающихся',
            self::COD_DEL => '13-02 Приказы об отчислении обучающихся',
            self::TECHNOPARK_ADD => '09-01 Приказы о зачислении обучающихся',
            self::TECHNOPARK_DEL => '09-02 Приказы об отчислении обучающихся',
            self::TECHNOPARK_ADD_BUDGET => '09-22 Приказы о зачислении обучающихся (по внебюджетной деятельности)',
            self::TECHNOPARK_DEL_BUDGET => '09-23 Приказы об отчислении обучающихся (по внебюджетной деятельности)',
            self::QUANTORIUM_ADD => '10-01 Приказы о зачислении обучающихся',
            self::QUANTORIUM_DEL => '10-02 Приказы об отчислении обучающихся',
            self::QUANTORIUM_ADD_BUDGET => '10-26 Приказы об зачислении обучающихся (по внебюджетной деятельности)',
            self::QUANTORIUM_DEL_BUDGET => '10-27 Приказы об отчислении обучающихся (по внебюджетной деятельности)',
            self::CDNTT_ADD => '11-01 Приказы о зачислении обучающихся',
            self::CDNTT_DEL => '11-02 Приказы об отчислении обучающихся',
            self::CDNTT_ADD_BUDGET => '11-26 Приказы о зачислении обучающихся (по внебюджетной деятельности)',
            self::CDNTT_DEL_BUDGET => '11-27 Приказы об отчислении обучающихся (по внебюджетной деятельности)',
            self::CDNTT_TRANSFER => '11-31 Приказы о переводе',
            self::MOB_QUANT_ADD => '12-01 Приказы о зачислении обучающихся',
            self::MOB_QUANT_DEL => '12-02 Приказы об отчислении обучающихся',
        ];
    }
    public function customSort()
    {
        return [
            $this->list[self::ADMIN_ORDER],
            $this->list[self::COD_ADD],
            $this->list[self::COD_DEL],
            $this->list[self::TECHNOPARK_ADD],
            $this->list[self::TECHNOPARK_DEL],
            $this->list[self::TECHNOPARK_ADD_BUDGET],
            $this->list[self::TECHNOPARK_DEL_BUDGET],
            $this->list[self::QUANTORIUM_ADD],
            $this->list[self::QUANTORIUM_DEL],
            $this->list[self::QUANTORIUM_ADD_BUDGET],
            $this->list[self::QUANTORIUM_DEL_BUDGET],
            $this->list[self::CDNTT_ADD],
            $this->list[self::CDNTT_DEL],
            $this->list[self::CDNTT_ADD_BUDGET],
            $this->list[self::CDNTT_DEL_BUDGET],
            $this->list[self::CDNTT_TRANSFER],
            $this->list[self::MOB_QUANT_ADD],
            $this->list[self::MOB_QUANT_DEL],
        ];
    }
    public static function getBranchByNomenclature($nomenclature)
    {
        switch ($nomenclature) {
            case '09-01':
                return BranchDictionary::TECHNOPARK;
            case '09-02':
                return BranchDictionary::TECHNOPARK;
            case '09-22':
                return BranchDictionary::TECHNOPARK;
            case '09-23':
                return BranchDictionary::TECHNOPARK;
            case '10-01':
                return BranchDictionary::QUANTORIUM;
            case '10-02':
                return BranchDictionary::QUANTORIUM;
            case '10-26':
                return BranchDictionary::QUANTORIUM;
            case '10-27':
                return BranchDictionary::QUANTORIUM;
            case '11-01':
                return BranchDictionary::CDNTT;
            case '11-02':
                return BranchDictionary::CDNTT;
            case '11-26':
                return BranchDictionary::CDNTT;
            case '11-27':
                return BranchDictionary::CDNTT;
            case '11-31':
                return BranchDictionary::CDNTT;
            case '13-01':
                return BranchDictionary::COD;
            case '13-02':
                return BranchDictionary::COD;
            case '02-02':
                return BranchDictionary::ADMINISTRATION;
            default:
                return null;
        }
    }
    public function getListByBranch($branch){
        switch ($branch){
            case BranchDictionary::TECHNOPARK:
                return $this->list = [
                self::TECHNOPARK_ADD => '09-01 Приказы о зачислении обучающихся',
                self::TECHNOPARK_DEL => '09-02 Приказы об отчислении обучающихся',
                self::TECHNOPARK_ADD_BUDGET => '09-22 Приказы о зачислении обучающихся (по внебюджетной деятельности)',
                self::TECHNOPARK_DEL_BUDGET => '09-23 Приказы об отчислении обучающихся (по внебюджетной деятельности)',
            ];
            case BranchDictionary::QUANTORIUM:
                return $this->list = [
                    self::QUANTORIUM_ADD => '10-01 Приказы о зачислении обучающихся',
                    self::QUANTORIUM_DEL => '10-02 Приказы об отчислении обучающихся',
                    self::QUANTORIUM_ADD_BUDGET => '10-26 Приказы об зачислении обучающихся',
                    self::QUANTORIUM_DEL_BUDGET => '10-27 Приказы об отчислении обучающихся',
                ];
            case BranchDictionary::CDNTT:
                return $this->list = [
                    self::CDNTT_ADD => '11-01 Приказы о зачислении обучающихся',
                    self::CDNTT_DEL => '11-02 Приказы об отчислении обучающихся',
                    self::CDNTT_ADD_BUDGET => '11-26 Приказы о зачислении обучающихся (по внебюджетной деятельности)',
                    self::CDNTT_DEL_BUDGET => '11-27 Приказы об отчислении обучающихся (по внебюджетной деятельности)',
                    self::CDNTT_TRANSFER => '11-31 Приказы о переводе',
                ];
            case BranchDictionary::COD:
                return $this->list = [
                    self::COD_ADD => '13-01 Приказы о зачислении обучающихся',
                    self::COD_DEL => '13-02 Приказы об отчислении обучающихся',
                ];
            case BranchDictionary::MOBILE_QUANTUM:
                return $this->list = [
                    self::MOB_QUANT_ADD => '10-01 Приказы о зачислении обучающихся',
                    self::MOB_QUANT_DEL => '10-02 Приказы об отчислении обучающихся',
                ];
            case BranchDictionary::PLANETARIUM:
                return $this->list = [];
            case BranchDictionary::ADMINISTRATION:
                return $this->list = [
                    self::ADMIN_ORDER => '02-02 Приказы директора по основной деятельности',
                ];
            default:
                return $this->list = [];
        }
    }
    public static function getStatus($nomenclature)
    {
        // зачисление
        if($nomenclature == NomenclatureDictionary::COD_ADD || $nomenclature == NomenclatureDictionary::TECHNOPARK_ADD
            || $nomenclature == NomenclatureDictionary::TECHNOPARK_ADD_BUDGET || $nomenclature == NomenclatureDictionary::QUANTORIUM_ADD
            || $nomenclature == NomenclatureDictionary::CDNTT_ADD || $nomenclature == NomenclatureDictionary::CDNTT_ADD_BUDGET
            || $nomenclature == NomenclatureDictionary::MOB_QUANT_ADD || $nomenclature == NomenclatureDictionary::QUANTORIUM_ADD_BUDGET){
            return self::ORDER_ENROLL;
        }
        // отчисление
        if ($nomenclature == NomenclatureDictionary::COD_DEL || $nomenclature == NomenclatureDictionary::TECHNOPARK_DEL
            || $nomenclature == NomenclatureDictionary::TECHNOPARK_DEL_BUDGET || $nomenclature == NomenclatureDictionary::QUANTORIUM_DEL
            || $nomenclature == NomenclatureDictionary::CDNTT_DEL || $nomenclature == NomenclatureDictionary::CDNTT_DEL_BUDGET
            || $nomenclature == NomenclatureDictionary::MOB_QUANT_DEL || $nomenclature == NomenclatureDictionary::QUANTORIUM_DEL_BUDGET) {
            return self::ORDER_DEDUCT;
        }
        // перевод
        if($nomenclature == NomenclatureDictionary::CDNTT_TRANSFER){
            return self::ORDER_TRANSFER;
        }
        return self::ORDER_INIT;
    }
    public static function getOrderName($status){
        if($status == self::ORDER_ENROLL){
            return 'О зачислении';
        }
        else if($status == self::ORDER_DEDUCT){
            return 'Об отчислении';
        }
        else if($status == self::ORDER_TRANSFER){
            return 'О переводе';
        }
        else {
            return 'ERROR ORDER NAME';
        }
    }
}