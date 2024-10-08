<?php

namespace chemezov\luya\tilda\frontend\blockgroups;

use luya\cms\base\BlockGroup;

class TildaGroup extends BlockGroup
{
    public function identifier()
    {
        return 'tilda-group';
    }

    public function label()
    {
        return 'Tilda';
    }

    public function getPosition()
    {
        return 100;
    }
}
