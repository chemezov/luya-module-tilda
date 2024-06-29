<?php

namespace chemezov\luya\tilda\frontend\blockgroups;

use luya\cms\base\BlockGroup;

class TildaBlockGroup extends BlockGroup
{
    public function identifier()
    {
        return 'tilda-block-group';
    }

    public function label()
    {
        return 'Tilda';
    }

    public function getPosition()
    {
        return 1;
    }
}
