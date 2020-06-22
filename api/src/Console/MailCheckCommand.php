<?php
declare(strict_types=1);

namespace App\Console;

use App\Auth\Entity\User\Email;
use App\Auth\Entity\User\Token;
use App\Auth\Service\SignUpConfirmationSender;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MailCheckCommand extends Command
{
    /**
     * @var SignUpConfirmationSender
     */
    private SignUpConfirmationSender $sender;

    protected function configure(): void
    {
        $this->setName('mailer:check');
    }

    public function __construct(SignUpConfirmationSender $sender)
    {
        parent::__construct();
        $this->sender = $sender;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('<comment>Sending</comment>');

//        $transport = (new \Swift_SmtpTransport('mailer', 1025))
//            ->setUsername('app')
//            ->setPassword('secret')
//            ->setEncryption('tcp');
//
//        $mailer = new \Swift_Mailer($transport);
//
//        $message = (new \Swift_Message('Test'))
//            ->setFrom('mail@app.com')->setTo('user@app.com')
//            ->setBody('Test Success!!');
//
//        if($mailer->send($message) === 0){
//            throw new \RuntimeException('Unable to send email.');
//        }

        $this->sender->send(
            new Email('user@app.com'),
            new Token(Uuid::uuid4()->toString(), new \DateTimeImmutable())
        );

        $output->writeln('<info>Done!</info>');

        return 0;
    }
}