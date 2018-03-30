<?php

namespace Shippinno\Job\Infrastructure\Persistence\Doctrine;

use Doctrine\Common\Persistence\ManagerRegistry;

trait ManagerRegistryAwareTrait
{
    /**
     * @var ManagerRegistry
     */
    protected $managerRegistry;

    /**
     * @param ManagerRegistry $managerRegistry
     */
    public function setManagerRegistry(ManagerRegistry $managerRegistry)
    {
        $this->managerRegistry = $managerRegistry;
    }

    /**
     * @return void
     */
    public function flush(): void
    {
        if ($this->hasEntityManager()) {
            $this->managerRegistry->getManager()->flush();
        }
    }

    /**
     * @return void
     */
    public function clear(): void
    {
        if ($this->hasEntityManager()) {
            $this->managerRegistry->getManager()->clear();
        }
    }

    /**
     * @return bool
     */
    protected function hasEntityManager(): bool
    {
        return null !== $this->managerRegistry;
    }
}
