<?php
require_once dirname(__DIR__) . '/vendor/autoload.php';

use App\Kernel;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

(new Dotenv())->bootEnv(dirname(__DIR__) . '/.env');

$kernel = new Kernel($_SERVER['APP_ENV'], (bool) $_SERVER['APP_DEBUG']);
$application = new Application($kernel);
$application->setAutoExit(false);

$input = new ArrayInput([
    'command' => 'doctrine:schema:update',
    '--dump-sql' => true,
]);

$output = new BufferedOutput();
$application->run($input, $output);

$content = $output->fetch();
echo nl2br($content);
