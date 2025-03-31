<?php

namespace frontend\components\routes;

class Urls
{
    /**
     * Константы DocumentIn
     *
     * DOC_IN_VIEW - actionView
     * DOC_IN_CREATE - actionCreate
     * DOC_IN_RESERVE - actionReserve
     * DOC_IN_INDEX - actionIndex
    */
    const DOC_IN_VIEW = "document/document-in/view";
    const DOC_IN_CREATE = "document/document-in/create";
    const DOC_IN_RESERVE = "document/document-in/reserve";
    const DOC_IN_INDEX = "document/document-in/index";

    /**
     * Константы DocumentOut
     *
     * DOC_OUT_VIEW - actionView
     * DOC_OUT_CREATE - actionCreate
     * DOC_OUT_INDEX - actionIndex
     */
    const DOC_OUT_VIEW = "document/document-out/view";
    const DOC_OUT_CREATE = "document/document-out/create";
    const DOC_OUT_INDEX = "document/document-out/index";

    /**
     * Константы RegulationEvent
     *
     * REG_EVENT_VIEW - actionView
     * REG_EVENT_INDEX - actionIndex
     */
    const REG_EVENT_INDEX = "regulation/regulation-event/index";
    const REG_EVENT_VIEW = "regulation/regulation-event/view";

    /**
     * Константы Regulation
     *
     * REG_VIEW - actionView
     * REG_INDEX - actionIndex
     */
    const REG_INDEX = "regulation/regulation/index";
    const REG_VIEW = "regulation/regulation/view";

    /**
     * Константы OurEvent
     *
     * OUR_EVENT_VIEW - actionView
     * OUR_EVENT_INDEX - actionIndex
     */
    const OUR_EVENT_INDEX = "event/our-event/index";
    const OUR_EVENT_VIEW = "event/our-event/view";

    /**
     * Константы TrainingProgram
     *
     * PROGRAM_VIEW - actionView
     * PROGRAM_INDEX - actionIndex
     * PROGRAM_RELEVANCE - actionRelevance
     */
    CONST PROGRAM_INDEX = "educational/training-program/index";
    const PROGRAM_VIEW = "educational/training-program/view";
    const PROGRAM_RELEVANCE = "educational/training-program/relevance";

    /**
     * Константа для пост запроса изменения актуальности TrainingProgram и TrainingGroup
     */
    const ACTUAL_OBJECT = "@app/views/educational/relevance-post/relevance-post.php";

    /**
     * Константы TrainingGroup
     *
     * TRAINING_GROUP_VIEW - actionView
     * TRAINING_GROUP_UPDATE - actionView
     * TRAINING_GROUP_INDEX - actionIndex
     * TRAINING_GROUP_ARCHIVE - actionRelevance
     * LESSON_THEMES_CREATE - actionCreateLessonThemes
     * JOURNAL_DELETE - actionDeleteJournal
     */
    const TRAINING_GROUP_INDEX = "educational/training-group/index";
    const TRAINING_GROUP_UPDATE = "educational/training-group/base-form";
    const TRAINING_GROUP_VIEW = "educational/training-group/view";
    const TRAINING_GROUP_ARCHIVE = "educational/training-group/archive";
    const LESSON_THEMES_CREATE = "educational/training-group/create-lesson-themes";
    const JOURNAL_DELETE = "educational/training-group/delete-journal";

    /**
     * Константы Journal
     *
     * JOURNAL_VIEW - actionView
     * JOURNAL_UPDATE - actionUpdate
     * JOURNAL_UPDATE - actionEditPlan
     * JOURNAL_DELETE_PLAN - actionDeletePlan
     */
    const JOURNAL_VIEW = "educational/journal/view";
    const JOURNAL_UPDATE = "educational/journal/update";
    const JOURNAL_EDIT_PLAN = "educational/journal/edit-plan";
    const JOURNAL_DELETE_PLAN = "educational/journal/delete-plan";

    /**
     * Константы Certificate
     *
     * CERTIFICATE_VIEW - actionView
     */
    const CERTIFICATE_VIEW = "educational/certificate/view";

    /**
     * Константы ForeignEventParticipants
     *
     * PARTICIPANT_VIEW - actionView
     */
    const PARTICIPANT_VIEW = "dictionaries/foreign-event-participants/view";

    /**
     * Константы People
     *
     * PEOPLE_VIEW - actionView
     */
    const PEOPLE_VIEW = "dictionaries/people/view";

    /**
     * Константы OrderMain
     *
     * ORDER_MAIN_VIEW - actionView
     */
    const ORDER_MAIN_VIEW = "order/order-main/view";

    /**
     * Константы OrderTraining
     *
     * ORDER_TRAINING_VIEW - actionView
     */
    const ORDER_TRAINING_VIEW = "order/order-training/view";
}