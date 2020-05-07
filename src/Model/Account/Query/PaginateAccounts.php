<?php
declare(strict_types=1);

namespace Plexikon\Kernel\Model\Account\Query;

final class PaginateAccounts
{
    private int $limit;

    public function __construct(int $limit = 10)
    {
        $this->limit = $limit;
    }

    public function limit(): int
    {
        return $this->limit;
    }
}
