<?php

namespace StudySauce\Bundle\DependencyInjection;

use Composer\Script\Event;
use Sensio\Bundle\DistributionBundle\Composer\ScriptHandler;
use Symfony\Component\Process\Process;


class SauceHandler extends ScriptHandler
{

    protected static function getOptions(Event $event)
    {
        $options = array_merge(array(
            'symfony-app-dir' => 'app',
            'symfony-web-dir' => 'web',
            'symfony-assets-install' => 'hard',
            'assetic-dump-asset-root' => null,
            'assetic-dump-force' => true
        ), $event->getComposer()->getPackage()->getExtra());
        $options['symfony-assets-install'] = getenv('SYMFONY_ASSETS_INSTALL') ?: $options['symfony-assets-install'];
        $options['process-timeout'] = $event->getComposer()->getConfig()->get('process-timeout');
        return $options;
    }

    public static function dumpAssets($event)
    {
        $options = self::getOptions($event);
        $webDir = $options['symfony-web-dir'];
        $appDir = $options['symfony-app-dir'];
        $arguments = array();

        if ($options['assetic-dump-force']) {
            $arguments[] = '--force';
        }

        if ($options['assetic-dump-asset-root'] !== null) {
            $arguments = escapeshellarg($options['assetic-dump-asset-root']);
        }
        if (!is_dir($webDir)) {
            echo 'The symfony-app-dir (' . $webDir . ') specified in composer.json was not found in ' . getcwd() . ', can not install assets.' . PHP_EOL;
            return;
        }

        static::executeCommand($event, $appDir, 'assetic:dump' . implode(' ', $arguments));
    }

    protected static function executeCommand(Event $event, $consoleDir, $cmd, $timeout = 300)
    {
        $php = escapeshellarg(static::getPhp(false));
        $phpArgs = implode(' ', array_map('escapeshellarg', static::getPhpArguments()));
        $console = escapeshellarg($consoleDir.'/console');
        if ($event->getIO()->isDecorated()) {
            $console .= ' --ansi';
        }

        $process = new Process($php.($phpArgs ? ' '.$phpArgs : '').' '.$console.' '.$cmd, null, null, null, $timeout);
        $process->run(function ($type, $buffer) use ($event) { $event->getIO()->write($buffer, false); });
        if (!$process->isSuccessful()) {
            throw new \RuntimeException(sprintf("An error occurred when executing the \"%s\" command:\n\n%s\n\n%s.", escapeshellarg($cmd), $process->getOutput(), $process->getErrorOutput()));
        }
    }

}