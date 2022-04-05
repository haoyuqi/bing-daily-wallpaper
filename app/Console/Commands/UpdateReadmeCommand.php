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

        $today = today();
        $content = str_replace('%today-wallpaper%', "![{$today->toDateString()}](./storage/bing-wallpaper/{$today->year}/{$today->format('m')}/{$today->toDateString()}.png)" . PHP_EOL . $today->toDateTimeString(), $readme_template);

        $filesystem->put(base_path('README.md'), $content);
    }
}
