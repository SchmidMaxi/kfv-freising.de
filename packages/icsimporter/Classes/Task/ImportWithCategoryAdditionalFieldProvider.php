<?php

declare(strict_types=1);

namespace Schmid\IcsImporter\Task;

use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Type\ContextualFeedbackSeverity;
use TYPO3\CMS\Scheduler\AbstractAdditionalFieldProvider;
use TYPO3\CMS\Scheduler\Controller\SchedulerModuleController;
use TYPO3\CMS\Scheduler\Task\AbstractTask;

/**
 * Additional field provider for the ICS import scheduler task.
 */
final class ImportWithCategoryAdditionalFieldProvider extends AbstractAdditionalFieldProvider
{
    private const LANG_FILE = 'LLL:EXT:icsimporter/Resources/Private/Language/locallang.xlf:';

    public function getAdditionalFields(
        array &$taskInfo,
        $task,
        SchedulerModuleController $schedulerModule,
    ): array {
        $this->initializeTaskInfo($taskInfo, $task);

        return [
            'icsUrl' => $this->createTextField(
                'icsUrl',
                $taskInfo['icsUrl'],
                self::LANG_FILE . 'task.field.icsUrl'
            ),
            'pid' => $this->createNumberField(
                'pid',
                (int)$taskInfo['pid'],
                self::LANG_FILE . 'task.field.pid'
            ),
            'categoryUid' => $this->createNumberField(
                'categoryUid',
                (int)$taskInfo['categoryUid'],
                self::LANG_FILE . 'task.field.categoryUid'
            ),
            'since' => $this->createTextField(
                'since',
                $taskInfo['since'],
                self::LANG_FILE . 'task.field.since',
                $this->translate('task.field.since.placeholder')
            ),
        ];
    }

    public function validateAdditionalFields(
        array &$submittedData,
        SchedulerModuleController $schedulerModule,
    ): bool {
        $isValid = true;

        if (empty($submittedData['icsUrl'])) {
            $schedulerModule->addMessage(
                $this->translate('task.error.icsUrl'),
                ContextualFeedbackSeverity::ERROR
            );
            $isValid = false;
        }

        if (empty($submittedData['pid']) || (int)$submittedData['pid'] <= 0) {
            $schedulerModule->addMessage(
                $this->translate('task.error.pid'),
                ContextualFeedbackSeverity::ERROR
            );
            $isValid = false;
        }

        if (empty($submittedData['categoryUid']) || (int)$submittedData['categoryUid'] <= 0) {
            $schedulerModule->addMessage(
                $this->translate('task.error.categoryUid'),
                ContextualFeedbackSeverity::ERROR
            );
            $isValid = false;
        }

        return $isValid;
    }

    public function saveAdditionalFields(
        array $submittedData,
        AbstractTask $task,
    ): void {
        if (!$task instanceof ImportWithCategoryTask) {
            return;
        }

        $task->icsUrl = trim((string)$submittedData['icsUrl']);
        $task->pid = (int)$submittedData['pid'];
        $task->categoryUid = (int)$submittedData['categoryUid'];
        $task->since = trim((string)($submittedData['since'] ?? '')) ?: null;
    }

    private function initializeTaskInfo(array &$taskInfo, mixed $task): void
    {
        $defaults = [
            'icsUrl' => '',
            'pid' => 0,
            'categoryUid' => 0,
            'since' => '',
        ];

        if ($task instanceof ImportWithCategoryTask) {
            $defaults = [
                'icsUrl' => $task->icsUrl,
                'pid' => $task->pid,
                'categoryUid' => $task->categoryUid,
                'since' => $task->since ?? '',
            ];
        }

        foreach ($defaults as $key => $default) {
            $taskInfo[$key] ??= $default;
        }
    }

    /**
     * @return array{code: string, label: string}
     */
    private function createTextField(
        string $name,
        string $value,
        string $labelKey,
        string $placeholder = '',
    ): array {
        $placeholderAttr = $placeholder !== ''
            ? sprintf(' placeholder="%s"', htmlspecialchars($placeholder))
            : '';

        return [
            'code' => sprintf(
                '<input class="form-control" type="text" name="tx_scheduler[%s]" value="%s"%s />',
                htmlspecialchars($name),
                htmlspecialchars($value),
                $placeholderAttr
            ),
            'label' => $labelKey,
        ];
    }

    /**
     * @return array{code: string, label: string}
     */
    private function createNumberField(string $name, int $value, string $labelKey): array
    {
        return [
            'code' => sprintf(
                '<input class="form-control" type="number" name="tx_scheduler[%s]" value="%d" min="0" />',
                htmlspecialchars($name),
                $value
            ),
            'label' => $labelKey,
        ];
    }

    private function translate(string $key): string
    {
        return $this->getLanguageService()->sL(self::LANG_FILE . $key);
    }

    private function getLanguageService(): LanguageService
    {
        return $GLOBALS['LANG'];
    }
}
