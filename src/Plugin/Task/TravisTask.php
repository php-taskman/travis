<?php

declare(strict_types = 1);

namespace PhpTaskman\Travis\Plugin\Task;

use PhpTaskman\CoreTasks\Plugin\BaseTask;
use Robo\Common\BuilderAwareTrait;
use Robo\Common\ResourceExistenceChecker;
use Robo\Exception\TaskException;
use Robo\Robo;
use Robo\Task\Base\loadTasks;

final class TravisTask extends BaseTask
{
    use BuilderAwareTrait;
    use loadTasks;
    use ResourceExistenceChecker;

    public const ARGUMENTS = [
        'file',
        'section',
    ];
    public const NAME = 'travis';

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $arguments = $this->getTaskArguments();

        if (!$this->checkResource($arguments['file'], 'file')) {
            throw new TaskException(__CLASS__, 'Travis file is unreachable.');
        }

        $travisConfig = Robo::createConfiguration([$arguments['file']]);

        $tasks = [];
        foreach ($travisConfig->get($arguments['section'], []) as $task) {
            $tasks[] = $this->taskExec($task);
        }

        return $this
            ->collectionBuilder()
            ->addTaskList($tasks)
            ->run();
    }
}
