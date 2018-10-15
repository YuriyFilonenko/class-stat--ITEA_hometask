<?php

namespace App;

use ReflectionClass;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\{InputArgument, InputInterface};
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;

/**
 * Implements logic for class statistic
 *
 * @author Yuriy Filonenko <mail@gmail.com>
 */
class ClassStatisticCommand extends Command
{
    private $srcDir;
    private $rootNamespace;
    
    public function __construct(string $srcDir, string $rootNamespace, mixed $name = null)
    {
        parent::__construct($name);
        
        $this->srcDir = $srcDir;
        $this->rootNamespace = $rootNamespace;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('class-stat')
            ->setDescription('Show class statistic')
            ->addArgument(
                'class',
                InputArgument::REQUIRED,
                'Class for statistic'
            )
        ;
    }
    
    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $class = $input->getArgument('class');
        
        $finder = new Finder();
        $finder
            ->files()
            ->in($this->srcDir)
            ->name('/^[A-Z].+\.php$/')
        ;
        
        foreach ($finder as $file) {
            $path = $file->getRelativePathname();
            $className = \rtrim($path, '.php');
            $fullClassName = $this->rootNamespace . '\\' . \rtrim($path, '.php');
            $flag = false;

            if ($class == $className) {
                $reflector = new ReflectionClass($fullClassName);
                $flag = true;
                break;
            }
        }
        
        if (!$flag) {
            throw new \RuntimeException("Class $class not found!");
        }
        
        $isFinal = $reflector->isFinal();
        $isAbstarct = $reflector->isAbstract();
        
        $outputClassName = '';

        if ($isFinal) {
            $outputClassName = "$class (Final class)";
        } elseif ($isAbstarct) {
            $outputClassName = "$class (Abstract class)";
        } else {
            $outputClassName = "$class";
        }

        $countOfPublicProperties = \count(
            $reflector->getProperties(\ReflectionProperty::IS_PUBLIC)
        );
        
        $countOfProtectedProperties = \count(
            $reflector->getProperties(\ReflectionProperty::IS_PROTECTED)
        );
        
        $countOfPrivateProperties = \count(
            $reflector->getProperties(\ReflectionProperty::IS_PRIVATE)
        );
        
        $countOfPublicMethods = \count(
            $reflector->getMethods(\ReflectionMethod::IS_PUBLIC)
        );
        
        $countOfProtectedMethods = \count(
            $reflector->getMethods(\ReflectionMethod::IS_PROTECTED)
        );
        
        $countOfPrivateMethods = \count(
            $reflector->getMethods(\ReflectionMethod::IS_PRIVATE)
        );
        
        $output->writeln(
            "Class: $outputClassName" . \PHP_EOL
            . 'Propertyes:' . \PHP_EOL
            . "    public: $countOfPublicProperties" . \PHP_EOL
            . "    protected: $countOfProtectedProperties" . \PHP_EOL
            . "    private: $countOfPrivateProperties" . \PHP_EOL
            . 'Methods:' . \PHP_EOL
            . "    public: $countOfPublicMethods" . \PHP_EOL
            . "    protected: $countOfProtectedMethods" . \PHP_EOL
            . "    private: $countOfPrivateMethods" . \PHP_EOL
        );
    }
}
