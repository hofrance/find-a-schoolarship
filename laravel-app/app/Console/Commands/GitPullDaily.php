<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GitPullDaily extends Command
{
    protected $signature = 'repo:pull';
    protected $description = 'Pull latest changes from the git remote';

    public function handle(): int
    {
        $base = base_path();
        $this->info("Running git pull in $base ...");
        $cmd = sprintf('bash -lc %s', escapeshellarg('git --no-pager pull --rebase --autostash 2>&1'));
        $out = [];
        $code = 0;
        exec($cmd, $out, $code);
        foreach ($out as $line) {
            $this->line($line);
        }
        if ($code !== 0) {
            $this->error('git pull failed');
        }
        return $code;
    }
}
