<?php

declare(strict_types=1);

namespace PoP\Notifications\TypeResolvers;

use PoP\Translation\Facades\TranslationAPIFacade;
use PoP\ComponentModel\TypeResolvers\AbstractTypeResolver;
use PoP\Notifications\TypeDataLoaders\NotificationTypeDataLoader;

class NotificationTypeResolver extends AbstractTypeResolver
{
    public const NAME = 'Notification';

    public function getTypeName(): string
    {
        return self::NAME;
    }

    public function getSchemaTypeDescription(): ?string
    {
        $translationAPI = TranslationAPIFacade::getInstance();
        return $translationAPI->__('Notifications for the user', 'notifications');
    }

    public function getID($resultItem)
    {
        $notification = $resultItem;
        return $notification->histid;
    }

    public function getTypeDataLoaderClass(): string
    {
        return NotificationTypeDataLoader::class;
    }
}
