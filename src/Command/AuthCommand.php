<?php
/**
 * Created by PhpStorm.
 * User: edd
 * Date: 2/9/19
 * Time: 8:29 PM
 */

namespace Eddmash\PermissionBundle\Command;


use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\Reader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class AuthCommand extends Command
{
    protected static $defaultName = 'eddmash:permission';
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var AnnotationReader
     */
    private $annotationReader;
    /**
     * @var string
     */
    private $userEntity;
    /**
     * @var string
     */
    private $fetchAdminCallback;

    public function __construct(EntityManagerInterface $entityManager,
                                Reader $annotationReader,
                                string $userEntity,
                                string $fetchAdminCallback)
    {
        parent::__construct(null);
        $this->entityManager = $entityManager;
        $this->annotationReader = $annotationReader;
        $this->userEntity = $userEntity;
        $this->fetchAdminCallback = $fetchAdminCallback;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $repo = $this->entityManager->getRepository($this->userEntity);
        $adminuser = null;
        $fetchAdminCallback = $this->fetchAdminCallback;

        $username = '';
        if (!method_exists($repo, $fetchAdminCallback)) {
            $username = $this->getUsername($input, $output);
        }
        $permission = new Permissions(
            $this->userEntity,
            $this->fetchAdminCallback,
            $username,
            $input,
            $output
        );
        $permission->load($this->entityManager, $this->annotationReader);

        return 0;
    }


    private function getUsername(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');

        $question = new Question(' > <info>Admin Username</info>: ');
        $question->setValidator(
            function ($answer) {
                if (empty($answer)) {
                    throw new \RuntimeException('The username cannot be empty');
                }

                return $answer;
            }
        );
        return $helper->ask($input, $output, $question);


    }

}