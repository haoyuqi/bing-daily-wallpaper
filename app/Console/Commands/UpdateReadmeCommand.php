<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class UpdateReadmeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:readme';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update README.md file';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $filesystem = new Filesystem();
        $readme_template = $filesystem->get(storage_path('readme-template.dat'));

        $now = now();
        $content = str_replace('%today-wallpaper%', "![{$now->toDateString()}](./storage/bing-wallpaper/{$now->year}/{$now->format('m')}/{$now->toDateString()}.png)" . PHP_EOL . $now->toDateString(), $readme_template);

        $res = $filesystem->put(base_path('README.md'), $content);

        $this->info('Write README.md file ' . ($res ? 'success' : 'error') . '.');
    }
}
