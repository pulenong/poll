<?php

declare(strict_types=1);

namespace User\View\Helper;

use Laminas\View\Helper\AbstractHelper;
use User\Plugin\AccountPlugin;

class AccountHelper extends AbstractHelper
{
    /**
     * @var AccountPlugin|null
     */
    protected ?AccountPlugin $accountPlugin;

    /**
     * @return AccountPlugin
     */
    public function getAccountPlugin(): AccountPlugin
    {
        return $this->accountPlugin;
    }

    /**
     * @param AccountPlugin|null $accountPlugin
     * @return $this
     */
    public function setAccountPlugin(?AccountPlugin $accountPlugin): AccountHelper
    {
        $this->accountPlugin = $accountPlugin;
        return $this;
    }
}
