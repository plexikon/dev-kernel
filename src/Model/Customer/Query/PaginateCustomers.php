<?php
declare(strict_types=1);

namespace Plexikon\Kernel\Model\Customer\Query;

final class PaginateCustomers
{
    private int $limit;

    public function __construct(int $limit = 10)
    {
        $this->limit = $limit;
    }

    public function getLimit(): int
    {
        return $this->limit;
    }
}
