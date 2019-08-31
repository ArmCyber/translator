<?php

namespace Zakhayko\Translator\Commands;

use Illuminate\Console\Command;
use Zakhayko\Translator\Facades\Translator;

class TranslatorDelete extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'translator:delete {key}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deletes Translator Attribute From JSON';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $argument = $this->argument('key');
        $result = Translator::deleteTranslation($argument);
        if ($result === false) $this->error('Key "'.$argument.'" does not exist.');
        else if ($result===null) $this->error('Key "'.$argument.'" is array.');
        else $this->info('Key "'.$result.'" deleted.');
        return ;
    }
}
