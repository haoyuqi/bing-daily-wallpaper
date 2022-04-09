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
        $readme_template = str_replace('%today-wallpaper%', "![{$now->toDateString()}](./storage/bing-wallpaper/{$now->year}/{$now->format('m')}/{$now->toDateString()}.png)" . PHP_EOL . $now->toDateString(), $readme_template);

        $all_wallpaper = '';
        $directories = collect($filesystem->directories(storage_path('bing-wallpaper')))->sortByDesc(function ($item) {
            return $item;
        });
        foreach ($directories as $directory) {
            $year = basename($directory);
            $all_wallpaper .= PHP_EOL . '## ' . $year . PHP_EOL;

            $month_directories = collect($filesystem->directories($directory))->sortByDesc(function ($item) {
                return $item;
            });
            foreach ($month_directories as $item) {
                $month = basename($item);
                $all_wallpaper .= PHP_EOL . '### ' . $month . PHP_EOL;

                $files = collect($filesystem->allFiles($item))->sortByDesc(function ($file) {
                    return $file->getFilename();
                })->values();
                $all_wallpaper .= '| | | |' . PHP_EOL;
                $all_wallpaper .= '|:---:|:---:|:---:|' . PHP_EOL;

                foreach ($files as $key => $file) {
                    $all_wallpaper .= '|';
                    $string = "![{$file->getFilenameWithoutExtension()}](./storage/bing-wallpaper/{$year}/{$month}/{$file->getFilename()}) {$file->getFilenameWithoutExtension()}";
                    $all_wallpaper .= (($key + 1) % 3 === 0) ? $string . '|' . PHP_EOL : $string;
                }

                $remainder = count($files) % 3;
                if ($remainder !== 0) {
                    $all_wallpaper .= str_pad('', (3 - $remainder) * 2, '| ') . '|';
                }
            }
        }
        $readme_template = str_replace('%all-wallpaper%', $all_wallpaper, $readme_template);

        $res = $filesystem->put(base_path('README.md'), $readme_template);

        $this->info('Write README.md file ' . ($res ? 'success' : 'error') . '.');
    }
}
