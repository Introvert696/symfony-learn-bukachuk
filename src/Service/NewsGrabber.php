<?php

namespace App\Service;

use App\Entity\Blog;
use App\Repository\BlogRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\DomCrawler\Crawler;

class NewsGrabber{
    private LoggerInterface $logger;
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly EntityManagerInterface $em,
        private  readonly  BlogRepository $blogRepository,
        private  readonly  ParameterBagInterface $parameterBag,

    )
    {
    }
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
        return $this;
    }

    /**
     * @param int|null $count
     * @param bool $dryRun
     * @return void
     * @throws GuzzleException
     */
    public function importNews(?int $count,bool $dryRun): void
    {
        $this->logger->info('Start getting news');
        $client = new Client([
            'timeout'  => 7.0,
        ]);

        $response = $client->get('https://www.engadget.com/news', ['verify' => false]);

        $texts = [];

        $crawler = new Crawler($response->getBody()->getContents());
        $crawler->filter('h4.My\(0\) > a')->each(function(Crawler $crawler) use(&$texts,$count){
            if(count($texts) >= $count){
                return;
            }
            $texts[] = [
                'title' => $crawler->text(),
                'href' => $crawler->attr('href')
            ];
        });
        unset($crawler);
        $this->logger->info(sprintf('get %d news', count($texts)));
        foreach ($texts as &$text){
            $response = $client->get('https://www.engadget.com'.$text['href'], ['verify' => false]);
            $crawler = new Crawler($response->getBody()->getContents());
            $crowblerBody = $crawler->filter('div.caas-body')->first();
            $text['text'] = $crowblerBody->text();

            $this->logger->info(sprintf('Parsing news %s',$text['title']));

        }
        unset($text);

        $this->saveNews($texts, $dryRun);
    }
    private function saveNews(array $texts,bool $dryRun): void
    {
        $this->logger->info('Save news');

        $blogUser = $this->userRepository->find($this->parameterBag->get('autoblog'));
        if(!$blogUser)
        {
            $this->logger->error(sprintf('User is not found %s ',$this->parameterBag->get('autoblog')));
            return ;
        }

        foreach($texts as $text) {
            if($this->blogRepository->findByTitle($text['title'])){
                $this->logger->info(sprintf('News already exists %s',$text['title']));
                continue;
            }
            if(!$dryRun){
                continue;
            }
            $this->logger->info(sprintf('Save blog %s',$text['title']));
            $blog = new Blog($blogUser);
            $blog
                ->setTitle($text['title'])
                ->setDescription(mb_substr($text['text'],0,200))
                ->setText($text['text'])
                ->setStatus('pending');
            $this->em->persist($blog);
        }
        $this->em->flush();
    }
}
