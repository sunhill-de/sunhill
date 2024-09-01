<?php

namespace Sunhill\Console;

use Illuminate\Console\Command;
use Sunhill\Basic\Facades\Checks;

class Check extends Command
{
    protected $signature = 'sunhill:check {--repair} {--group=}';
    
    protected $description = 'Checks the consistency of sunhill databases and structures';
    
    public function handle()
    {
        $this->info(__('Performing checks...',[]));
        $repair = $this->option('repair');
        $row = 0;
        $output = $this->output;
        $result = Checks::Check($repair, $this->option('group'),function($checker, $checks) use (&$output, &$row) {
            switch ($checker->getLastResult()) {
                case 'passed':
                    $output->write(".",false);
                    break;
                case 'failed':
                    $output->write("F",false);
                    break;
                case 'repaired':
                    $output->write("R",false);
                    break;
                case 'unrepairable':
                    $output->write("U",false);
                    break;
            }
/*            if (!($row++ % 63)) {
                $output->writeln($checks->getTestsPerformed().' / '.$checks->getTotalTests()); 
            } */
        });
        $params = [
            'run'=>Checks::getTestsPerformed(),
            'passed'=>Checks::getTestsPassed(),
            'failed'=>Checks::getTestsFailed(),
            'repaired'=>Checks::getTestsRepaired(),
            'unrepairable'=>Checks::getTestsUnrepairable()
        ];
        $this->newLine();
        if ($repair) {
            $this->info(__('Checks finished (:run checks run: :passed passed, :failed failed, :repaired repaired, :unrepairable unrepairable)',$params));
        } else {
            $this->info(__('Checks finished (:run checks run: :passed passed, :failed failed, repair not set)',$params));            
        }
    }
}
