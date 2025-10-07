<?php
declare(strict_types=1);

namespace Schmid\IcsImporter\Task;

use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Scheduler\AdditionalFieldProviderInterface;
use TYPO3\CMS\Scheduler\Controller\SchedulerModuleController;
use TYPO3\CMS\Scheduler\Task\AbstractTask;

final class ImportWithCategoryAdditionalFieldProvider implements AdditionalFieldProviderInterface
{
    public function getAdditionalFields(array &$taskInfo, $task, SchedulerModuleController $parentObject): array
    {
        /** @var ImportWithCategoryTask|null $task */
        $fields = [];

        // ICS URL
        $valUrl = $task instanceof ImportWithCategoryTask ? (string)$task->icsUrl : ($taskInfo['icsUrl'] ?? '');
        $fields['icsUrl'] = [
            'code'  => '<input class="form-control" type="text" name="tx_scheduler[icsUrl]" value="' . htmlspecialchars($valUrl) . '" />',
            'label' => 'ICS URL (https://… oder t3://file?uid=…)',
        ];

        // PID
        $valPid = $task instanceof ImportWithCategoryTask ? (int)$task->pid : (int)($taskInfo['pid'] ?? 0);
        $fields['pid'] = [
            'code'  => '<input class="form-control" type="number" name="tx_scheduler[pid]" value="' . (int)$valPid . '" />',
            'label' => 'Ziel-PID',
        ];

        // Kategorie UID
        $valCat = $task instanceof ImportWithCategoryTask ? (int)$task->categoryUid : (int)($taskInfo['categoryUid'] ?? 0);
        $fields['categoryUid'] = [
            'code'  => '<input class="form-control" type="number" name="tx_scheduler[categoryUid]" value="' . (int)$valCat . '" />',
            'label' => 'sys_category UID',
        ];

        // Since (optional)
        $valSince = $task instanceof ImportWithCategoryTask ? (string)($task->since ?? '') : (string)($taskInfo['since'] ?? '');
        $fields['since'] = [
            'code'  => '<input class="form-control" type="text" name="tx_scheduler[since]" value="' . htmlspecialchars($valSince) . '" placeholder="z. B. -14 days oder 2024-01-01" />',
            'label' => 'Since (optional)',
        ];

        return $fields;
    }

    public function validateAdditionalFields(array &$submittedData, SchedulerModuleController $parentObject): bool
    {
        if (empty($submittedData['icsUrl'])) {
            $parentObject->addMessage('Bitte eine ICS-URL angeben.', \TYPO3\CMS\Core\Type\ContextualFeedbackSeverity::ERROR);
            return false;
        }
        if (empty($submittedData['pid']) || (int)$submittedData['pid'] <= 0) {
            $parentObject->addMessage('Bitte eine gültige PID angeben.', \TYPO3\CMS\Core\Type\ContextualFeedbackSeverity::ERROR);
            return false;
        }
        if (empty($submittedData['categoryUid']) || (int)$submittedData['categoryUid'] <= 0) {
            $parentObject->addMessage('Bitte eine gültige sys_category UID angeben.', \TYPO3\CMS\Core\Type\ContextualFeedbackSeverity::ERROR);
            return false;
        }
        // since ist optional; wenn gesetzt, nicht prüfen (DateTime wird im Task erstellt)
        return true;
    }

    public function saveAdditionalFields(array $submittedData, AbstractTask $task): void
    {
        /** @var ImportWithCategoryTask $task */
        $task->icsUrl      = (string)$submittedData['icsUrl'];
        $task->pid         = (int)$submittedData['pid'];
        $task->categoryUid = (int)$submittedData['categoryUid'];
        $task->since       = trim((string)($submittedData['since'] ?? '')) ?: null;
    }
}
