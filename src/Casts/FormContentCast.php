<?php

namespace FormEntries\Casts;

use JsonFieldCast\Casts\AbstractMeta;

class FormContentCast extends AbstractMeta
{
    protected function metaClass(): string
    {
        return \FormEntries\Forms\FormContent::class;
    }
}
