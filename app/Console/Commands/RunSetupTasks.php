<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class RunSetupTasks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:setup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Runs all necessary commands for application setup';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Starting application setup...');

        // $this->call('key:generate');
        // $this->call('migrate'); // Run it manually
        // $this->call('db:seed'); // Run it manually
        // $this->call('storage:link'); // Do in dockerfile

        $this->createMenus();

        $this->info('✅ All setup tasks completed successfully!');

        return 0;
    }

    protected function createMenus()
    {
        // Register all necessary menus and submenus here
        $menus = [
            'Home',
            'Activities',
            'Congregants',
            'Service Types',
            'Schedule Management' => [
                'Congregant Services',
                'Schedules',
            ],
            'Settings' => [
                'Users',
            ],
        ];

        foreach ($menus as $menu => $submenu) {
            if (is_array($submenu)) {
                $this->call('app:create-menu', ['menu' => $menu]);
                foreach ($submenu as $sub) {
                    $this->call('app:create-menu', ['menu' => $sub, '--parent' => $menu]);
                }
            } else {
                $this->call('app:create-menu', ['menu' => $submenu]);
            }
        }
    }
}
