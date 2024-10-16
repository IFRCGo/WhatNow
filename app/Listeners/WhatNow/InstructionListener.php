<?php

namespace App\Listeners\WhatNow;

use App\Events\WhatNow\InstructionCreated;
use App\Events\WhatNow\InstructionCreatedViaImport;
use App\Events\WhatNow\InstructionDeleted;
use App\Events\WhatNow\InstructionTranslationCreated;
use App\Events\WhatNow\InstructionTranslationPublished;
use App\Events\WhatNow\InstructionTranslationUnpublished;
use App\Events\WhatNow\InstructionUpdated;
use App\Events\WhatNow\InstructionUpdatedViaImport;
use App\Repositories\HistoryRepository;

class InstructionListener
{
    
    public static function onCreate(InstructionCreated $event)
    {
        HistoryRepository::create(
            "history.whatnow.instruction.created",
            $event->instruction->getEventType(),
            $event->instruction->getId(),
            $event->instruction->getCountryCode()
        );
    }

    public static function onImportCreate(InstructionCreatedViaImport $event)
    {
        HistoryRepository::create(
            "history.whatnow.instruction.import.created",
            $event->instruction->getEventType(),
            $event->instruction->getId(),
            $event->instruction->getCountryCode()
        );
    }

    
    public static function onDelete(InstructionDeleted $event)
    {
        HistoryRepository::create(
            "history.whatnow.instruction.deleted",
            $event->instruction->getEventType(),
            $event->instruction->getId(),
            $event->instruction->getCountryCode()
        );
    }

    
    public static function onUpdate(InstructionUpdated $event)
    {
        HistoryRepository::create(
            "history.whatnow.instruction.updated",
            $event->instruction->getEventType(),
            $event->instruction->getId(),
            $event->instruction->getCountryCode()
        );
    }

    
    public static function onImportUpdate(InstructionUpdatedViaImport $event)
    {
        $action = $event->wasForced() ? "history.whatnow.instruction.import.force_updated" : "history.whatnow.instruction.import.updated";

        HistoryRepository::create(
            $action,
            $event->instruction->getEventType(),
            $event->instruction->getId(),
            $event->instruction->getCountryCode()
        );
    }

    
    public static function onTranslationCreate(InstructionTranslationCreated $event)
    {
        HistoryRepository::create(
            "history.whatnow.instruction.translation_created",
            $event->instruction->getEventType(),
            $event->instruction->getId(),
            $event->instruction->getCountryCode()
        );
    }

    
    public static function onPublish(InstructionTranslationPublished $event)
    {
        HistoryRepository::create(
            "history.whatnow.instruction.translation_published",
            $event->instruction->getEventType(),
            $event->instruction->getId(),
            $event->instruction->getCountryCode(),
            $event->languageCode
        );
    }


    
    public static function onUnpublish(InstructionTranslationUnpublished $event)
    {
        HistoryRepository::create(
            "history.whatnow.instruction.translation_unpublished",
            $event->instruction->getEventType(),
            $event->instruction->getId(),
            $event->instruction->getCountryCode(),
            $event->languageCode
        );
    }

    
    public function subscribe($events)
    {
        $events->listen(
            InstructionCreated::class,
            'App\Listeners\WhatNow\InstructionListener@onCreate'
        );

        $events->listen(
            InstructionCreatedViaImport::class,
            'App\Listeners\WhatNow\InstructionListener@onImportCreate'
        );

        $events->listen(
            InstructionDeleted::class,
            'App\Listeners\WhatNow\InstructionListener@onDelete'
        );

        $events->listen(
            InstructionUpdated::class,
            'App\Listeners\WhatNow\InstructionListener@onUpdate'
        );

        $events->listen(
            InstructionUpdatedViaImport::class,
            'App\Listeners\WhatNow\InstructionListener@onImportUpdate'
        );

        $events->listen(
            InstructionTranslationCreated::class,
            'App\Listeners\WhatNow\InstructionListener@onTranslationCreate'
        );

        $events->listen(
            InstructionTranslationPublished::class,
            'App\Listeners\WhatNow\InstructionListener@onPublish'
        );

        $events->listen(
            InstructionTranslationUnpublished::class,
            'App\Listeners\WhatNow\InstructionListener@onUnpublish'
        );
    }
}
