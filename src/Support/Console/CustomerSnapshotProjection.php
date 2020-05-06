<?php
declare(strict_types=1);

namespace Plexikon\Kernel\Support\Console;

use Illuminate\Console\Command;
use Plexikon\Chronicle\Provider\SnapshotModelProjectionManager;
use Plexikon\Chronicle\Support\Snapshot\SnapshotStreamProjection;
use Plexikon\Kernel\Model\Customer\Customer;

class CustomerSnapshotProjection extends Command
{
    protected $signature = 'kernel:snapshot-customer';

    public function handle(): void
    {
        $projection = $this->createCustomerStreamProjection();

        $projection->run(true);
    }

    protected function createCustomerStreamProjection(): SnapshotStreamProjection
    {
        return $this->getLaravel()
            ->get(SnapshotModelProjectionManager::class)
            ->createSnapshotProjection('user-stream', [Customer::class]);
    }
}
