<?php
namespace Plugins\Areanet_Push\Command;

use Areanet\PIM\Classes\Command\CustomCommand;
use Areanet\PIM\Classes\Exceptions\ContentflyException;
use Areanet\PIM\Entity\Group;
use Doctrine\ORM\EntityManager;
use Plugins\Areanet_Push\Classes\Android;
use Plugins\Areanet_Push\Classes\Ios;
use Plugins\Areanet_Push\Entity\BaseMessage;
use Plugins\Areanet_Push\Entity\Message;
use Plugins\Areanet_Push\Entity\Token;
use Plugins\Areanet_Push\PushPlugin;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SendCommand extends CustomCommand
{
    /** @var EntityManager */
    protected $em;
    
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('push:send')
            ->addOption(
                'resendError',
                'e'
            )
            ->setDescription('Versenden von Push-Nachrichten.')
        ;


    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $status     = $input->getOption('resendError') ? Message::ERROR :Message::WAITING;

        $app        = $this->getSilexApplication();
        $this->em   = $app['orm.em'];

        $qb = $this->em->createQueryBuilder();

        $entityName = PushPlugin::$OPTIONS->custom_entity ? PushPlugin::$OPTIONS->custom_entity : 'Plugins\\Areanet_Push\\Entity\\Message';
        $entity = new $entityName;

        if(!($entity instanceof BaseMessage)){
            throw new \Exception("Invalid Push--Entity $entityName");
        }

        $qb->select('m')
            ->from($entityName, 'm')
            ->where('m.statusAndroid = :status')
            ->orWhere('m.statusIos = :status')
            ->orderBy('m.created', 'DESC')
            ->setMaxResults(1);
        $qb->setParameter('status', $status);

        /** @var Message $message */
        try{
            $message  = $qb->getQuery()->getSingleResult();
        }catch(\Exception $e){
            $output->writeln("No Push-Notifications to send.");
            return;
        }
        
        //Send iOS
        if ($message->getStatusIos() == $status) {

            $iosTokensObj = $this->getTokens(Token::IOS, $message->getGroup());

            $output->writeln("Try to send to " . count($iosTokensObj) . " iOS-Tokens...");
            $message->setStatusIos(Message::SENDING);
            $this->em->flush();

            try {
                $iosPush = new Ios($message->getTitle(), $message->getSubtitle(), $message->getExtendedData());

                $iosTokensToDelete = array();
                /** @var Token $iosTokenObj */
                foreach ($iosTokensObj as $iosTokenObj) {
                    if (!$iosPush->send($iosTokenObj->getToken())) {
                        $iosTokensToDelete[] = $iosTokenObj;
                    }
                }

                $iosPush->close();

                $iosSended  = count($iosTokensObj) - count($iosTokensToDelete);
                $iosDeleted = count($iosTokensToDelete);

                foreach ($iosTokensToDelete as $iosTokenToDelete) {
                    $this->em->remove($iosTokenToDelete);
                }

                $message->setSendediOS($iosSended);
                $message->setStatusIos(Message::SENDED);


                $output->writeln("<info>$iosSended iOS-Notifications sended.</info>");
                if ($iosDeleted) $output->writeln("<error>$iosDeleted iOS-Tokens deleted.</error>");

                $message->appendLogs(Token::IOS, "$iosSended iOS-Notifications sended.");

                $this->em->flush();
            } catch (\Exception $e) {
                $message->setStatusIos(Message::ERROR);
                $this->em->flush();

                $output->writeln("<error>" . $e->getMessage() . "</error>");
                $message->appendLogs(Token::IOS, "$e->getMessage()");
            }


        }


        //Send Android
        if ($message->getStatusAndroid() == $status) {

            $androidTokensObj = $this->getTokens(Token::ANDROID, $message->getGroup());

            $androidTokens      = array();

            /** @var Token $androidTokenObj */
            foreach($androidTokensObj as $androidTokenObj){
                $androidTokens[] = $androidTokenObj->getToken();
            }

            $output->writeln("Try to send to " . count($androidTokens) . " Android-Tokens...");
            $message->setStatusAndroid(Message::SENDING);
            $this->em->flush();

            try {
                $androidPush            = new Android($message->getTitle(), $message->getSubtitle(), $message->getExtendedData());
                $androidTokensSended    = $androidPush->send($androidTokens);

                $message->setSendedAndroid(count($androidTokensSended));
                $message->setStatusAndroid(Message::SENDED);

                $androidDeleted = 0;
                for ($i = 0; $i < count($androidTokensSended); $i++) {
                    if ($androidTokensSended[$i]['error']) {
                        $this->em->remove($androidTokensObj[$i]);
                        $androidDeleted++;
                    }
                }
                $androidSended = count($androidTokensSended) - $androidDeleted;

                $output->writeln("<info>$androidSended Android-Notifications sended.</info>");
                if ($androidDeleted) $output->writeln("<error>$androidDeleted Android-Tokens deleted.</error>");

                $message->appendLogs(Token::ANDROID, "$androidSended Android-Notifications sended.");
            } catch (\Exception $e) {
                $message->setStatusAndroid(Message::ERROR);
                $output->writeln("<error>" . $e->getMessage() . "</error>");
                $message->appendLogs(Token::ANDROID, $e->getMessage());
            }

            $this->em->flush();
        }
    }
    
    protected function getTokens($platform, Group $group = null){
        $qb  = $this->em->createQueryBuilder();
        $qb->select('t')
            ->from('Plugins\\Areanet_Push\\Entity\\Token', 't')
            ->where('t.platform = :platform')
            ->setParameter('platform', $platform);

        if($group){
            $qb->join('t.userCreated', 'u')
                ->andWhere('u.group = :group')
                ->setParameter('group', $group);
        }

        return $qb->getQuery()->getResult();
    }
}