<?php

namespace Shippinno\Job\Infrastructure\Ui\Console\Laravel\Command;

use Doctrine\Common\Persistence\ManagerRegistry;
use Illuminate\Console\Command;
use LogicException;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Shippinno\Job\Application\Messaging\ConsumeStoredJobService;
use Shippinno\Job\Domain\Model\JobRunnerNotRegisteredException;

class JobConsume extends Command
{
    use LoggerAwareTrait;

    /**
     * {@inheritdoc}
     */
    protected $signature = 'job:consume';

    /**
     * @var ConsumeStoredJobService
     */
    private $consumeStoredJobService;

    /**
     * @var ManagerRegistry|null
     */
    private $managerRegistry;

    /**
     * @param ConsumeStoredJobService $consumeStoredJobService
     * @param ManagerRegistry|null $managerRegistry
     * @param LoggerInterface|null $logger
     */
    public function __construct(
        ConsumeStoredJobService $consumeStoredJobService,
        ManagerRegistry $managerRegistry = null,
        LoggerInterface $logger = null
    ) {
        parent::__construct();
        $this->consumeStoredJobService = $consumeStoredJobService;
        $this->managerRegistry = $managerRegistry;
        $this->setLogger(null !== $logger ? $logger : new NullLogger);
    }

    public function handle()
    {
        $queue = env('JOB_CONSUME_QUEUE');
        if (!$queue) {
            throw new LogicException('The env JOB_CONSUME_QUEUE is not defined');
        }
        while (true) {
            if (null !== $this->managerRegistry) {
                $this->managerRegistry->getManager()->clear();
            }
            try {
                $this->consumeStoredJobService->execute($queue);
                if (null !== $this->managerRegistry) {
                    $this->managerRegistry->getManager()->flush();
                }
            } catch (JobRunnerNotRegisteredException $e) {
                if (null !== $this->managerRegistry) {
                    $this->managerRegistry->getManager()->flush();
                }
            }
        }
    }
}
