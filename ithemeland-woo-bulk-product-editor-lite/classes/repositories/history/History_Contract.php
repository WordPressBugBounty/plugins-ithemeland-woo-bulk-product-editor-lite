<?php

namespace wcbel\classes\repositories\history;

interface History_Contract
{
    public function revert($history_id);

    public function reset($history_id);
}
