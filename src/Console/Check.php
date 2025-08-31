<?php

/**
 * @file Check.php
 * The command that executes the installed checks
 * Lang en
 * Reviewstatus: 2025-08-10
 * Create date: 2024-09-01
 * Localization: incomplete
 * Documentation: complete
 * Tests: @todo has no test
 * Coverage Unit: 0% (2025-06-06)
 */

namespace Sunhill\Console;

use Illuminate\Console\Command;
use Sunhill\Facades\Checks;

class Check extends Command
{
    protected $signature = 'sunhill:check {--repair} {--group=}';

    protected $description = 'Checks the consistency of sunhill databases and structures';

    public function handle()
    {
        $this->info(__('Performing checks...', []));
        $repair = $this->option('repair');
        $row = 0;
        $output = $this->output;
        $result = Checks::Check($repair, $this->option('group'), function ($checker, $checks) use (&$output, &$row) {
            switch ($checker->getLastResult()) {
                case 'passed':
                    $output->write('.', false);
                    break;
                case 'failed':
                    $output->write('F', false);
                    break;
                case 'repaired':
                    $output->write('R', false);
                    break;
                case 'unrepairable':
                    $output->write('U', false);
                    break;
            }
            /*            if (!($row++ % 63)) {
                            $output->writeln($checks->getTestsPerformed().' / '.$checks->getTotalTests());
                        } */
        });
        $params = [
            'run' => Checks::getTestsPerformed(),
            'passed' => Checks::getTestsPassed(),
            'failed' => Checks::getTestsFailed(),
            'repaired' => Checks::getTestsRepaired(),
            'unrepairable' => Checks::getTestsUnrepairable(),
        ];
        $this->newLine();
        if ($repair) {
            $this->info(__('Checks finished (:run checks run: :passed passed, :failed failed, :repaired repaired, :unrepairable unrepairable)', $params));
        } else {
            $this->info(__('Checks finished (:run checks run: :passed passed, :failed failed, repair not set)', $params));
        }
    }
}
