<?php

namespace App\Console\Commands\OneDrive;

use App\Helpers\OneDrive;
use App\Helpers\Tool;
use Illuminate\Console\Command;

class Direct extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'od:direct
                            {remote : 文件地址}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'DirectDownloadLink For File';

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
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function handle()
    {
        $this->info('请稍等...');
        $this->call('od:refresh');
        $target = $this->argument('remote');
        $target_path = trim(Tool::handleUrl($target), '/');
        $id_request = OneDrive::responseToArray(OneDrive::pathToItemId(empty($target_path) ? '/' : ":/{$target_path}:/"));
        if ($id_request['code'] === 200)
            $_id = $id_request['data']['id'];
        else {
            $this->warn('路径异常!');
            exit;
        }
        /* @var $result \Illuminate\Http\JsonResponse */
        $result = OneDrive::createShareLink($_id);
        $response = OneDrive::responseToArray($result);
        $response['code'] === 200 ? $this->info("创建成功!\n永久直链地址： {$response['data']['redirect']}") : $this->warn("创建失败!\n{$response['msg']} ");
    }
}
